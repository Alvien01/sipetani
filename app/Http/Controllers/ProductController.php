<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest('created_at')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
            'stock'        => 'required|integer|min:0',
            'images'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('images')) {
            $imagePath = $request->file('images')->store('products', 'public');
        }

        $slug = Str::slug($request->product_name);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        Product::create([
            'product_name' => $request->product_name,
            'slug'         => $slug,
            'price'        => $request->price,
            'description'  => $request->description,
            'stock'        => $request->stock,
            'images'       => $imagePath,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
            'stock'        => 'required|integer|min:0',
            'images'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'product_name' => $request->product_name,
            'price'        => $request->price,
            'description'  => $request->description,
            'stock'        => $request->stock,
        ];

        if ($product->product_name !== $request->product_name) {
            $slug = Str::slug($request->product_name);
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    // ─── EXPORT CSV ──────────────────────────────────────────────────
    public function exportCsv()
    {
        $products = Product::orderBy('id')->get();

        $filename = 'produk_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar UTF-8 terbaca
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, ['id', 'product_name', 'slug', 'price', 'description', 'stock']);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->product_name,
                    $p->slug,
                    $p->price,
                    $p->description,
                    $p->stock,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── IMPORT CSV ──────────────────────────────────────────────────
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file   = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        // Hapus BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }

        $header  = null;
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 0;

        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            $row++;

            // Baris pertama = header
            if ($header === null) {
                $header = array_map('strtolower', array_map('trim', $line));
                continue;
            }

            // Lewati baris kosong
            if (empty(array_filter($line))) continue;

            $data = array_combine($header, $line);

            // Validasi kolom wajib
            if (empty($data['product_name'] ?? '')) {
                $errors[] = "Baris {$row}: product_name kosong, dilewati.";
                $skipped++;
                continue;
            }

            $price = isset($data['price']) ? (float) str_replace([',', '.'], ['', '.'], $data['price']) : 0;
            $stock = isset($data['stock']) ? (int) $data['stock'] : 0;

            // Buat slug unik
            $slug = Str::slug($data['product_name']);
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            Product::create([
                'product_name' => trim($data['product_name']),
                'slug'         => $slug,
                'price'        => $price,
                'description'  => trim($data['description'] ?? ''),
                'stock'        => $stock,
            ]);

            $inserted++;
        }

        fclose($handle);

        $msg = "{$inserted} produk berhasil diimport.";
        if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";
        if (!empty($errors)) $msg .= ' ' . implode(' ', array_slice($errors, 0, 3));

        return back()->with('success', $msg);
    }

    // ─── TEMPLATE CSV ────────────────────────────────────────────────
    public function templateCsv()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_produk.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['product_name', 'price', 'description', 'stock']);
            fputcsv($handle, ['Contoh Produk', '15000', 'Deskripsi produk', '100']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
