@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')
@section('page-subtitle', 'Tambahkan produk baru ke sistem')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">Form Tambah Produk</h3>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
            @csrf

            <!-- Product Name -->
            <div>
                <label for="product_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="product_name"
                    name="product_name"
                    value="{{ old('product_name') }}"
                    required
                    placeholder="Contoh: Beras Organik Premium"
                    class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all
                    {{ $errors->has('product_name') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}"
                >
                @error('product_name')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price & Stock -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        value="{{ old('price') }}"
                        required
                        min="0"
                        step="100"
                        placeholder="0"
                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all
                        {{ $errors->has('price') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}"
                    >
                    @error('price')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        value="{{ old('stock', 0) }}"
                        required
                        min="0"
                        placeholder="0"
                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all
                        {{ $errors->has('stock') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}"
                    >
                    @error('stock')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Deskripsi produk..."
                    class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none
                    {{ $errors->has('description') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div>
                <label for="images" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Gambar Produk <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div class="relative">
                    <input
                        type="file"
                        id="images"
                        name="images"
                        accept="image/jpg,image/jpeg,image/png,image/webp"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer transition-all"
                    >
                </div>
                <p class="mt-1.5 text-xs text-gray-400">Format: JPG, JPEG, PNG, WEBP. Maks 2MB.</p>
                @error('images')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm cursor-pointer">
                    Simpan Produk
                </button>
                <a href="{{ route('products.index') }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
