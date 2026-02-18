@extends('layouts.app')

@section('title', $product->product_name)
@section('page-title', 'Detail Produk')
@section('page-subtitle', $product->product_name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Image -->
        @if($product->images)
        <div class="w-full h-56 bg-gray-100">
            <img src="{{ asset('storage/' . $product->images) }}" alt="{{ $product->product_name }}"
                 class="w-full h-full object-cover">
        </div>
        @else
        <div class="w-full h-40 bg-gradient-to-br from-emerald-50 to-green-100 flex items-center justify-center">
            <svg class="w-16 h-16 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        @endif

        <div class="px-6 py-6 space-y-5">
            <!-- Name & Slug -->
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $product->product_name }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">Slug: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">{{ $product->slug }}</code></p>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 rounded-xl p-4">
                    <p class="text-xs text-green-600 font-semibold uppercase tracking-wider mb-1">Harga</p>
                    <p class="text-xl font-bold text-green-800">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl p-4 {{ $product->stock > 10 ? 'bg-emerald-50' : ($product->stock > 0 ? 'bg-yellow-50' : 'bg-red-50') }}">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1 {{ $product->stock > 10 ? 'text-emerald-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">Stok</p>
                    <p class="text-xl font-bold {{ $product->stock > 10 ? 'text-emerald-800' : ($product->stock > 0 ? 'text-yellow-800' : 'text-red-800') }}">{{ $product->stock }} unit</p>
                </div>
            </div>

            <!-- Description -->
            @if($product->description)
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $product->description }}</p>
            </div>
            @endif

            <!-- Created At -->
            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-400">Ditambahkan pada {{ \Carbon\Carbon::parse($product->created_at)->format('d F Y') }}</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('products.edit', $product) }}"
                   class="flex items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('products.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
