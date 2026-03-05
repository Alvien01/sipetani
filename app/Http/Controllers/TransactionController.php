<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('product')->latest()->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::all();
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|exists:products,id',
            'date_sale'     => 'required|date',
            'total_buy'     => 'required|integer|min:1',
            'total_payment' => 'required|numeric|min:0',
        ]);

        Transaction::create($request->all());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        $products = Product::all();
        return view('transactions.edit', compact('transaction', 'products'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'product_id'    => 'required|exists:products,id',
            'date_sale'     => 'required|date',
            'total_buy'     => 'required|integer|min:1',
            'total_payment' => 'required|numeric|min:0',
        ]);

        $transaction->update($request->all());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    public function exportCsv()
    {
        $transactions = Transaction::with('product')->orderBy('date_sale', 'asc')->get();
        $filename = 'transaksi_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['id', 'product_id', 'product_name', 'date_sale', 'total_buy', 'total_payment']);
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->id,
                    $t->product_id,
                    $t->product?->product_name ?? '',
                    $t->date_sale,
                    $t->total_buy,
                    $t->total_payment,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        set_time_limit(300); // 5 menit untuk proses import besar

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:65536', // max 64MB
        ]);

        $file   = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }

        $productMap = Product::pluck('id', 'product_name')->toArray();
        $productIds = Product::pluck('id')->toArray();
        $header   = null;
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 0;

        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            $row++;

            if ($header === null) {
                $header = array_map('strtolower', array_map('trim', $line));
                continue;
            }

            if (empty(array_filter($line))) continue;

            if (count($line) < count($header)) {
                $line = array_pad($line, count($header), '');
            }

            $data = array_combine($header, array_slice($line, 0, count($header)));
            $productId = null;

            if (!empty($data['product_id'] ?? '')) {
                $pid = (int) $data['product_id'];
                if (in_array($pid, $productIds)) {
                    $productId = $pid;
                }
            }

            if (!$productId && !empty($data['product_name'] ?? '')) {
                $productId = $productMap[trim($data['product_name'])] ?? null;
            }

            if (!$productId) {
                $errors[] = "Baris {$row}: produk tidak ditemukan, dilewati.";
                $skipped++;
                continue;
            }

            $dateSale = trim($data['date_sale'] ?? '');
            if (empty($dateSale)) {
                $errors[] = "Baris {$row}: date_sale kosong, dilewati.";
                $skipped++;
                continue;
            }

            try {
                $dateSale = \Carbon\Carbon::parse($dateSale)->format('Y-m-d');
            } catch (\Exception $e) {
                $errors[] = "Baris {$row}: format tanggal tidak valid, dilewati.";
                $skipped++;
                continue;
            }

            $totalBuy     = max(1, (int) ($data['total_buy'] ?? 1));
            $totalPayment = (float) str_replace(',', '.', $data['total_payment'] ?? 0);

            Transaction::create([
                'product_id'    => $productId,
                'date_sale'     => $dateSale,
                'total_buy'     => $totalBuy,
                'total_payment' => $totalPayment,
            ]);

            $inserted++;
        }

        fclose($handle);

        $msg = "{$inserted} transaksi berhasil diimport.";
        if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";
        if (!empty($errors)) $msg .= ' Catatan: ' . implode(' | ', array_slice($errors, 0, 5));

        return back()->with('success', $msg);
    }

    public function templateCsv()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_transaksi.csv"',
        ];

        $products = Product::select('id', 'product_name')->limit(3)->get();

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['product_id', 'product_name', 'date_sale', 'total_buy', 'total_payment']);
            foreach ($products as $p) {
                fputcsv($handle, [$p->id, $p->product_name, now()->format('Y-m-d'), 10, 150000]);
            }
            if ($products->isEmpty()) {
                fputcsv($handle, [1, 'Nama Produk', now()->format('Y-m-d'), 10, 150000]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
