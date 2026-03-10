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
        $forecasts = Forecast::select('id', 'product_id', 'type', 'month', 'weekly', 'year', 'total', 'st', 'sst', 'at', 'bt', 'forecast', 'pe', 'selisih', 'evaluasi', 'alpha')
            ->with(['product' => function ($query) {
                $query->select('id', 'product_name');
            }])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $products = Product::select('id', 'product_name')->orderBy('product_name')->get();
        return view('forecasts.index', compact('forecasts', 'products'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'product_id' => 'required', 
            'alpha'      => 'required|numeric|min:0.01|max:0.99',
            'type'       => 'required|in:monthly,weekly',
        ]);

        $productIdInput = $request->product_id;
        $alpha = $request->alpha;
        $type = $request->type;

        // Agregasi di level database agar hemat memori
        $isMonthly = ($type === 'monthly');
        
        $forecastData = Transaction::query()
            ->when($productIdInput !== 'all', function ($q) use ($productIdInput) {
                return $q->where('product_id', $productIdInput);
            })
            ->select([
                DB::raw($isMonthly ? "DATE_FORMAT(date_sale, '%Y-%m') as period_key" : "DATE_FORMAT(date_sale, '%x-%v') as period_key"),
                DB::raw($isMonthly ? "MONTH(date_sale) as period_num" : "WEEK(date_sale, 3) as period_num"),
                DB::raw("YEAR(date_sale) as period_year"),
                DB::raw("SUM(total_buy) as total_actual"),
                // Ambil satu transaction_id sebagai referensi jika dibutuhkan relasi
                DB::raw("MIN(id) as ref_transaction_id")
            ])
            ->groupBy('period_key', 'period_num', 'period_year')
            ->orderBy('period_key', 'asc')
            ->get();

        if ($forecastData->isEmpty()) {
            return back()->with('error', 'Tidak ada data transaksi untuk kriteria ini.');
        }

        $s1_prev = null;
        $s2_prev = null;
        $a_prev = null;
        $b_prev = null;
        
        // Bersihkan data lama
        Forecast::where('type', $type)
            ->when($productIdInput === 'all', function($q) {
                return $q->whereNull('product_id');
            }, function($q) use ($productIdInput) {
                return $q->where('product_id', $productIdInput);
            })
            ->delete();

        foreach ($forecastData as $iteration => $row) {
            $current_actual = $row->total_actual;
            $transaction_id = $row->ref_transaction_id;

            if ($iteration === 0) {
                $s1 = $current_actual;
                $s2 = $current_actual;
                $at = $s1; 
                $bt = 0;   
                $forecast_current = $current_actual; 
            } else {
                $s1 = ($alpha * $current_actual) + ((1 - $alpha) * $s1_prev);
                $s2 = ($alpha * $s1) + ((1 - $alpha) * $s2_prev);
                $at = (2 * $s1) - $s2;
                $bt = ($alpha / (1 - $alpha)) * ($s1 - $s2);
                $forecast_current = $a_prev + $b_prev;
            }

            $error = $current_actual - $forecast_current;
            $pe = $current_actual != 0 ? abs($error / $current_actual) * 100 : 0;
            $eval = '';
            if ($pe < 10) $eval = 'Sangat Baik';
            elseif ($pe < 20) $eval = 'Baik';
            elseif ($pe < 50) $eval = 'Cukup';
            else $eval = 'Buruk';

            Forecast::create([
                'product_id'     => ($productIdInput === 'all') ? null : $productIdInput,
                'transaction_id' => $transaction_id, 
                'type'           => $type,
                'month'          => $isMonthly ? $row->period_num : null,
                'weekly'         => !$isMonthly ? $row->period_num : null,
                'year'           => $row->period_year,
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
