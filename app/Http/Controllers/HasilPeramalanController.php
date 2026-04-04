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
        $query = HasilPeramalan::select('id', 'id_produk', 'periode', 'tipe_periode', 'aktual', 'st', 'bt', 'it', 'forecast', 'alpha', 'beta', 'gamma', 'seasonal_length', 'pe', 'mape', 'created_at')
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
                    'gamma'         => $all->first()?->gamma,
                    'seasonal_length' => $all->first()?->seasonal_length,
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
            'gamma'        => 'required|numeric|min:0.00|max:0.99',
            'seasonal_length' => 'required|integer|min:2',
            'tipe_periode' => 'required|in:bulanan,mingguan',
        ]);

        $productIdInput = $request->product_id;
        $alpha          = (float) $request->alpha;
        $beta           = (float) $request->beta;
        $gamma          = (float) $request->gamma;
        $seasonalLength = (int) $request->seasonal_length;
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

        // --- FILL MISSING PERIODS DENGAN 0 AGAR MUSIMAN TERJAGA ---
        $firstRow = $forecastData->first();
        $lastRow = $forecastData->last();
        $contiguousData = [];
        
        if ($isMonthly) {
            $curr = Carbon::createFromFormat('Y-m', $firstRow->sort_key)->startOfMonth();
            $end = Carbon::createFromFormat('Y-m', $lastRow->sort_key)->startOfMonth();
            while ($curr->lte($end)) {
                $sortKey = $curr->format('Y-m');
                $contiguousData[$sortKey] = (object) [
                    'sort_key' => $sortKey,
                    'label' => $curr->format('m-Y'),
                    'total_actual' => 0
                ];
                $curr->addMonth();
            }
        } else {
            $startYear = (int) substr($firstRow->sort_key, 0, 4);
            $startWeek = (int) substr($firstRow->sort_key, 5, 2);
            $endYear = (int) substr($lastRow->sort_key, 0, 4);
            $endWeek = (int) substr($lastRow->sort_key, 5, 2);
            
            $curr = Carbon::now()->setISODate($startYear, $startWeek)->startOfWeek();
            $end = Carbon::now()->setISODate($endYear, $endWeek)->startOfWeek();

            while ($curr->lte($end)) {
                $sortKey = $curr->format('o-W');
                $contiguousData[$sortKey] = (object) [
                    'sort_key' => $sortKey,
                    'label' => $curr->format('W-o'),
                    'total_actual' => 0
                ];
                $curr->addWeek();
            }
        }

        foreach ($forecastData as $row) {
            if (isset($contiguousData[$row->sort_key])) {
                $contiguousData[$row->sort_key]->total_actual = (int) $row->total_actual;
            } else {
                $contiguousData[$row->sort_key] = clone $row;
            }
        }
        ksort($contiguousData);
        $forecastData = array_values($contiguousData);

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

        $tempData = $forecastData;
        $actualL = min($seasonalLength, count($tempData));
        $sumL = 0;
        for($i=0; $i<$actualL; $i++) {
            $sumL += $tempData[$i]->total_actual;
        }
        $avgL = $actualL > 0 ? $sumL / $actualL : 0;

        $seasonalIndices = array_fill(0, $seasonalLength, 0);
        for($i=0; $i<$actualL; $i++) {
            $seasonalIndices[$i] = $tempData[$i]->total_actual - $avgL;
        }

        // Stabilize Trend Initialization
        $bt_init = 0;
        if (count($tempData) >= 2 * $seasonalLength) {
            $sumTrend = 0;
            for ($i = 0; $i < $seasonalLength; $i++) {
                $sumTrend += ($tempData[$i + $seasonalLength]->total_actual - $tempData[$i]->total_actual) / $seasonalLength;
            }
            $bt_init = $sumTrend / $seasonalLength;
        } elseif (count($tempData) > 1) {
            $sumTrend = 0;
            $len = count($tempData) - 1;
            for ($i = 0; $i < $len; $i++) {
                $sumTrend += ($tempData[$i+1]->total_actual - $tempData[$i]->total_actual);
            }
            $bt_init = $sumTrend / $len;
        }

        foreach ($forecastData as $row) {
            $aktual = (int) $row->total_actual;
            $label  = $row->label;
            
            $seasonIndex = $iteration % $seasonalLength;
            $prevSeason = $seasonalIndices[$seasonIndex];

            if ($iteration === 0) {
                // S0 HARUS sama dengan base rata-rata yang digunakan untuk menghitung I0.
                // Jika S0 = aktual, maka akan terjadi lompatan nilai pada forecast berikutnya.
                $st       = $avgL;
                $bt       = $bt_init;
                $forecast = max(0, $aktual);
                $it       = $prevSeason;
            } else {
                $st = $alpha * ($aktual - $prevSeason) + (1 - $alpha) * ($st_prev + $bt_prev);
                $bt = $beta * ($st - $st_prev) + (1 - $beta) * $bt_prev;
                $forecast = max(0, $st_prev + $bt_prev + $prevSeason);
                $it = $gamma * ($aktual - $st) + (1 - $gamma) * $prevSeason;
            }

            $seasonalIndices[$seasonIndex] = $it;

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
                'it'           => round($it, 2),
                'forecast'     => round($forecast, 2),
                'alpha'        => $alpha,
                'beta'         => $beta,
                'gamma'        => $gamma,
                'seasonal_length' => $seasonalLength,
                'pe'           => $pe !== null ? round($pe, 2) : null,
                'mape'         => null, 
            ];

            $st_prev = $st;
            $bt_prev = $bt;
            $iteration++;
        }

        // --- Recommendation Generation ---
        $nextPeriodLabel = $isMonthly 
            ? Carbon::createFromFormat('m-Y', $label)->addMonth()->format('m-Y') 
            : Carbon::now()->addWeek()->format('v-Y'); // simplified naming
        $nextSeasonIndex = $iteration % $seasonalLength;
        $nextForecast = round($st_prev + $bt_prev + $seasonalIndices[$nextSeasonIndex], 2);

        // Save recommendation
        if ($productIdInput !== 'all') {
            $productModel = Product::find($productIdInput);
            if ($productModel) {
                $safetyStock = $productModel->safety_stock ?? 0;
                $currentStock = $productModel->stock ?? 0;
                $recommendedQty = max(0, ($nextForecast + $safetyStock) - $currentStock);

                \App\Models\StockRecommendation::updateOrCreate(
                    [
                        'id_produk' => $productIdInput,
                        'periode' => $nextPeriodLabel,
                    ],
                    [
                        'tipe_periode' => $tipePeriode,
                        'current_stock' => $currentStock,
                        'forecast_qty' => $nextForecast,
                        'safety_stock' => $safetyStock,
                        'recommended_qty' => round($recommendedQty),
                    ]
                );
            }
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
