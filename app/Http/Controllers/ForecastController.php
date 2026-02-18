<?php

namespace App\Http\Controllers;

use App\Models\Forecast;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForecastController extends Controller
{
    public function index()
    {
        $forecasts = Forecast::with('product')->latest()->paginate(20);
        $products = Product::all();
        return view('forecasts.index', compact('forecasts', 'products'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'alpha'      => 'required|numeric|min:0.01|max:0.99',
            'type'       => 'required|in:monthly,weekly',
        ]);

        $productId = $request->product_id;
        $alpha = $request->alpha;
        $type = $request->type;

        // Fetch transactions
        $query = Transaction::where('product_id', $productId)->orderBy('date_sale', 'asc');
        $transactions = $query->get();

        if ($transactions->isEmpty()) {
            return back()->with('error', 'Tidak ada data transaksi untuk produk ini.');
        }

        // Aggregate Data
        $data = [];
        foreach ($transactions as $trx) {
            $date = Carbon::parse($trx->date_sale);
            
            if ($type === 'monthly') {
                $key = $date->format('Y-m');
                $label = $date->format('F Y');
                $year = $date->year;
                $period = $date->month; // Month number
            } else {
                $key = $date->format('o-W'); // ISO Year-Week
                $label = 'Week ' . $date->weekOfYear . ' ' . $date->year;
                $year = $date->year;
                $period = $date->weekOfYear;
            }

            if (!isset($data[$key])) {
                $data[$key] = [
                    'total' => 0,
                    'year' => $year,
                    'period' => $period,
                    'transaction_ids' => [] // track IDs if needed, though user schema has single ID. We'll pick one or null.
                ];
            }
            $data[$key]['total'] += $trx->total_buy;
            $data[$key]['transaction_ids'][] = $trx->id;
        }

        // Sort by key (date)
        ksort($data);
        $s1_prev = null; // S'
        $s2_prev = null; // S''
        $a_prev = null;  // Level
        $b_prev = null;  // Trend
        $forecast_val = null;
        Forecast::where('product_id', $productId)->where('type', $type)->delete();
        $iteration = 0;
        foreach ($data as $key => $row) {
            $current_actual = $row['total'];
            $transaction_id = !empty($row['transaction_ids']) ? $row['transaction_ids'][0] : null; // Link one ID

            if ($iteration === 0) {
                $s1 = $current_actual;
                $s2 = $current_actual;
                $at = $s1; 
                $bt = 0;   
                $forecast_current = $current_actual; 
            } else {
                $s1 = ($alpha * $current_actual) + ((1 - $alpha) * $s1_prev);
                // S''_t = alpha * S'_t + (1-alpha) * S''_{t-1}
                $s2 = ($alpha * $s1) + ((1 - $alpha) * $s2_prev);
                // at = 2*S'_t - S''_t
                $at = (2 * $s1) - $s2;
                // bt = (alpha / (1-alpha)) * (S'_t - S''_t)
                $bt = ($alpha / (1 - $alpha)) * ($s1 - $s2);
                $forecast_current = $a_prev + $b_prev;
            }

            // Error metrics
            $error = $current_actual - $forecast_current;
            $pe = $current_actual != 0 ? abs($error / $current_actual) * 100 : 0;
            
            // Evaluation text
            $eval = '';
            if ($pe < 10) $eval = 'Sangat Baik';
            elseif ($pe < 20) $eval = 'Baik';
            elseif ($pe < 50) $eval = 'Cukup';
            else $eval = 'Buruk';

            Forecast::create([
                'product_id'     => $productId,
                'transaction_id' => $transaction_id, 
                'type'           => $type,
                'month'          => ($type == 'monthly') ? $row['period'] : null,
                'weekly'         => ($type == 'weekly') ? $row['period'] : null,
                'year'           => $row['year'],
                'total'          => $current_actual,
                'st'             => $s1,
                'sst'            => $s2,
                'at'             => $at,
                'bt'             => $bt,
                'forecast'       => $forecast_current,
                'pe'             => $pe,
                'selisih'        => $error,
                'evaluasi'       => $eval,
                'actual_prev'    => $iteration > 0 ? $s1_prev : 0,
                'alpha'          => $alpha,
            ]);

            // Prepare for next iteration
            $s1_prev = $s1;
            $s2_prev = $s2;
            $a_prev = $at;
            $b_prev = $bt;
            $iteration++;
        }

        return redirect()->route('forecasts.index')
            ->with('success', 'Peramalan berhasil dibuat!');
    }
}
