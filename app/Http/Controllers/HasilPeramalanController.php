<?php

namespace App\Http\Controllers;

use App\Models\HasilPeramalan;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HasilPeramalanController extends Controller
{
    /**
     * Tampilkan daftar hasil peramalan dengan filter & pagination.
     */
    public function index(Request $request)
    {
        $products = Product::orderBy('product_name')->get();

        $query = HasilPeramalan::with('product')->latest();

        if ($request->filled('product_id')) {
            $query->where('id_produk', $request->product_id);
        }

        if ($request->filled('tipe_periode')) {
            $query->where('tipe_periode', $request->tipe_periode);
        }

        $results = $query->paginate(20)->withQueryString();

        // Statistik ringkasan untuk produk yang dipilih
        $stats = null;
        if ($request->filled('product_id')) {
            $all = HasilPeramalan::where('id_produk', $request->product_id)
                ->when($request->filled('tipe_periode'), fn($q) => $q->where('tipe_periode', $request->tipe_periode))
                ->get();

            if ($all->isNotEmpty()) {
                $stats = [
                    'total'       => $all->count(),
                    'avg_mape'    => $all->whereNotNull('mape')->avg('mape'),
                    'avg_pe'      => $all->whereNotNull('pe')->avg('pe'),
                    'last_forecast' => $all->last()?->forecast,
                    'last_periode'  => $all->last()?->periode,
                    'product_name'  => $all->first()?->product?->product_name,
                    'alpha'         => $all->first()?->alpha,
                    'beta'          => $all->first()?->beta,
                ];
            }
        }

        return view('hasil-peramalan.index', compact('results', 'products', 'stats'));
    }

    /**
     * Form generate hasil peramalan baru dari data transaksi.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'alpha'        => 'required|numeric|min:0.01|max:0.99',
            'beta'         => 'required|numeric|min:0.00|max:0.99',
            'tipe_periode' => 'required|in:bulanan,mingguan',
        ]);

        $productId   = $request->product_id;
        $alpha       = (float) $request->alpha;
        $beta        = (float) $request->beta;
        $tipePeriode = $request->tipe_periode;

        // Ambil transaksi
        $transactions = Transaction::where('product_id', $productId)
            ->orderBy('date_sale', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            return back()->with('error', 'Tidak ada data transaksi untuk produk ini.');
        }

        // Agregasi per periode
        $data = [];
        foreach ($transactions as $trx) {
            $date = Carbon::parse($trx->date_sale);

            if ($tipePeriode === 'bulanan') {
                $key    = $date->format('Y-m');
                $label  = $date->format('m-Y'); // e.g. 01-2025
            } else {
                $key   = $date->format('o-W');
                $label = $date->format('W') . '-' . $date->year; // e.g. 01-2025
            }

            if (!isset($data[$key])) {
                $data[$key] = ['total' => 0, 'label' => $label];
            }
            $data[$key]['total'] += $trx->total_buy;
        }

        ksort($data);

        // Hapus data lama untuk produk & tipe yang sama
        HasilPeramalan::where('id_produk', $productId)
            ->where('tipe_periode', $tipePeriode)
            ->delete();

        // Hitung Double Exponential Smoothing
        $st_prev = null;
        $bt_prev = null;
        $iteration = 0;
        $mapeSum   = 0;
        $mapeCount = 0;
        $rows      = [];

        foreach ($data as $key => $row) {
            $aktual = $row['total'];
            $label  = $row['label'];

            if ($iteration === 0) {
                // Inisialisasi
                $st       = $aktual;
                $bt       = 0;
                $forecast = $aktual;
            } else {
                // S't = α * Yt + (1-α) * (S't-1 + bt-1)
                $st = $alpha * $aktual + (1 - $alpha) * ($st_prev + $bt_prev);
                // bt = β * (S't - S't-1) + (1-β) * bt-1
                $bt = $beta * ($st - $st_prev) + (1 - $beta) * $bt_prev;
                // Forecast = S't-1 + bt-1
                $forecast = $st_prev + $bt_prev;
            }

            // Error
            $pe   = ($aktual != 0) ? abs($aktual - $forecast) / $aktual * 100 : null;
            $mape = $pe; // per-period MAPE = PE; cumulative MAPE dihitung di akhir

            if ($pe !== null) {
                $mapeSum += $pe;
                $mapeCount++;
            }

            $rows[] = [
                'id_produk'    => $productId,
                'periode'      => $label,
                'tipe_periode' => $tipePeriode,
                'aktual'       => $aktual,
                'st'           => round($st, 2),
                'bt'           => round($bt, 2),
                'forecast'     => round($forecast, 2),
                'alpha'        => $alpha,
                'beta'         => $beta,
                'pe'           => $pe !== null ? round($pe, 2) : null,
                'mape'         => null, // akan diisi setelah loop
            ];

            $st_prev = $st;
            $bt_prev = $bt;
            $iteration++;
        }

        // Hitung MAPE kumulatif (rata-rata PE seluruh periode)
        $mapeKumulatif = $mapeCount > 0 ? round($mapeSum / $mapeCount, 2) : null;

        // Simpan ke database
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
                'product_id'   => $productId,
                'tipe_periode' => $tipePeriode,
            ])
            ->with('success', 'Hasil peramalan berhasil digenerate! MAPE: ' . ($mapeKumulatif ?? '-') . '%');
    }

    /**
     * Hapus semua hasil peramalan untuk produk tertentu.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'tipe_periode' => 'required|in:bulanan,mingguan',
        ]);

        HasilPeramalan::where('id_produk', $request->product_id)
            ->where('tipe_periode', $request->tipe_periode)
            ->delete();

        return back()->with('success', 'Data hasil peramalan berhasil dihapus.');
    }
}
