<?php

namespace App\Http\Controllers;

use App\Models\HasilPeramalan;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Carbon\Carbon;

class HasilPeramalanController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::select('id', 'product_name')->orderBy('product_name')->get();
        $query = HasilPeramalan::select('id', 'id_produk', 'periode', 'tipe_periode', 'aktual', 'st', 'bt', 'forecast', 'alpha', 'beta', 'pe', 'mape', 'created_at')
            ->with(['product' => function ($q) {
                $q->select('id', 'product_name');
            }])
            ->latest();

        if ($request->filled('product_id')) {
            if ($request->product_id === 'all') {
                $query->whereNull('id_produk');
            } else {
                $query->where('id_produk', $request->product_id);
            }
        }

        if ($request->filled('tipe_periode')) {
            $query->where('tipe_periode', $request->tipe_periode);
        }

        $results = $query->paginate(20)->withQueryString();
        $stats = null;
        if ($request->filled('product_id')) {
            $statsQuery = HasilPeramalan::where('tipe_periode', $request->filled('tipe_periode') ? $request->tipe_periode : 'bulanan');
            
            if ($request->product_id === 'all') {
                $statsQuery->whereNull('id_produk');
            } else {
                $statsQuery->where('id_produk', $request->product_id);
            }

            $all = $statsQuery->get();

            if ($all->isNotEmpty()) {
                $stats = [
                    'total'       => $all->count(),
                    'avg_mape'    => $all->whereNotNull('mape')->avg('mape'),
                    'avg_pe'      => $all->whereNotNull('pe')->avg('pe'),
                    'last_forecast' => $all->last()?->forecast,
                    'last_periode'  => $all->last()?->periode,
                    'product_name'  => $request->product_id === 'all' ? 'Semua Produk' : $all->first()?->product?->product_name,
                    'alpha'         => $all->first()?->alpha,
                    'beta'          => $all->first()?->beta,
                ];
            }
        }

        return view('hasil-peramalan.index', compact('results', 'products', 'stats'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'product_id'   => 'required', 
            'alpha'        => 'required|numeric|min:0.01|max:0.99',
            'beta'         => 'required|numeric|min:0.00|max:0.99',
            'tipe_periode' => 'required|in:bulanan,mingguan',
        ]);

        $productIdInput = $request->product_id;
        $alpha          = (float) $request->alpha;
        $beta           = (float) $request->beta;
        $tipePeriode    = $request->tipe_periode;

        // Optimasi: Agregasi di level database (GROUP BY) untuk menghemat memori
        $isMonthly = ($tipePeriode === 'bulanan');
        
        $forecastData = Transaction::query()
            ->when($productIdInput !== 'all', function ($q) use ($productIdInput) {
                return $q->where('product_id', $productIdInput);
            })
            ->select([
                DB::raw($isMonthly ? "DATE_FORMAT(date_sale, '%m-%Y') as label" : "DATE_FORMAT(date_sale, '%v-%x') as label"),
                DB::raw($isMonthly ? "DATE_FORMAT(date_sale, '%Y-%m') as sort_key" : "DATE_FORMAT(date_sale, '%x-%v') as sort_key"),
                DB::raw("SUM(total_buy) as total_actual")
            ])
            ->groupBy('label', 'sort_key')
            ->orderBy('sort_key', 'asc')
            ->get();

        if ($forecastData->isEmpty()) {
            return back()->with('error', 'Tidak ada data transaksi untuk kriteria ini.');
        }

        // Hapus data lama berdasarkan filter
        HasilPeramalan::where('tipe_periode', $tipePeriode)
            ->when($productIdInput === 'all', function($q) {
                return $q->whereNull('id_produk');
            }, function($q) use ($productIdInput) {
                return $q->where('id_produk', $productIdInput);
            })
            ->delete();
        $st_prev = null;
        $bt_prev = null;
        $iteration = 0;
        $mapeSum   = 0;
        $mapeCount = 0;
        $rows      = [];

        foreach ($forecastData as $row) {
            $aktual = (int) $row->total_actual;
            $label  = $row->label;

            if ($iteration === 0) {
                $st       = $aktual;
                $bt       = 0;
                $forecast = $aktual;
            } else {
                $st = $alpha * $aktual + (1 - $alpha) * ($st_prev + $bt_prev);
                $bt = $beta * ($st - $st_prev) + (1 - $beta) * $bt_prev;
                $forecast = $st_prev + $bt_prev;
            }

            $pe   = ($aktual != 0) ? abs($aktual - $forecast) / $aktual * 100 : null;
            $mape = $pe;

            if ($pe !== null) {
                $mapeSum += $pe;
                $mapeCount++;
            }

            $rows[] = [
                'id_produk'    => ($productIdInput === 'all') ? null : $productIdInput,
                'periode'      => $label,
                'tipe_periode' => $tipePeriode,
                'aktual'       => $aktual,
                'st'           => round($st, 2),
                'bt'           => round($bt, 2),
                'forecast'     => round($forecast, 2),
                'alpha'        => $alpha,
                'beta'         => $beta,
                'pe'           => $pe !== null ? round($pe, 2) : null,
                'mape'         => null, 
            ];

            $st_prev = $st;
            $bt_prev = $bt;
            $iteration++;
        }

        $mapeKumulatif = $mapeCount > 0 ? round($mapeSum / $mapeCount, 2) : null;

        foreach ($rows as &$r) {
            $r['mape'] = $mapeKumulatif;
        }

        HasilPeramalan::insert(array_map(function ($r) {
            return array_merge($r, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $rows));

        return redirect()
            ->route('hasil-peramalan.index', [
                'product_id'   => $productIdInput,
                'tipe_periode' => $tipePeriode,
            ])
            ->with('success', 'Hasil peramalan berhasil digenerate! MAPE: ' . ($mapeKumulatif ?? '-') . '%');
    }

    public function destroyFilter(Request $request)
    {
        $request->validate([
            'tipe_periode' => 'required',
        ]);

        $query = HasilPeramalan::where('tipe_periode', $request->tipe_periode);

        if ($request->filled('product_id')) {
            if ($request->product_id === 'all') {
                $query->whereNull('id_produk');
            } else {
                $query->where('id_produk', $request->product_id);
            }
        }

        $query->delete();

        return back()->with('success', 'Data hasil peramalan berhasil dihapus.');
    }
}
