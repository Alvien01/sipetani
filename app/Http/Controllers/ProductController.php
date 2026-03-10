<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::when($search, function ($query, $search) {
                $query->where('product_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

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

    public function importCsv(Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:51200', // support xlsx & max 50MB
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ProductImport, $request->file('file'));
            return back()->with('success', 'Produk berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
 
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
