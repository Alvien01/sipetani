@extends('layouts.app')

@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')
@section('page-subtitle', 'Perbarui data penjualan')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">Form Edit Transaksi</h3>
        </div>

        <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="px-6 py-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Product -->
            <div>
                <label for="product_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Produk <span class="text-red-500">*</span>
                </label>
                <select id="product_id" name="product_id" required
                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all cursor-pointer
                        {{ $errors->has('product_id') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            data-price="{{ $product->price }}"
                            {{ old('product_id', $transaction->product_id) == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }} (Stok: {{ $product->stock }}) - Rp {{ number_format($product->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date -->
            <div>
                <label for="date_sale" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Tanggal Penjualan <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    id="date_sale"
                    name="date_sale"
                    value="{{ old('date_sale', $transaction->date_sale) }}"
                    required
                    class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all
                    {{ $errors->has('date_sale') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}"
                >
                @error('date_sale')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Qty & Total -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="total_buy" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Jumlah (Qty) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="total_buy"
                        name="total_buy"
                        value="{{ old('total_buy', $transaction->total_buy) }}"
                        required
                        min="1"
                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all
                        {{ $errors->has('total_buy') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}"
                    >
                    @error('total_buy')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_payment" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Total Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="total_payment"
                        name="total_payment"
                        value="{{ old('total_payment', $transaction->total_payment) }}"
                        required
                        min="0"
                        step="100"
                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all
                        {{ $errors->has('total_payment') ? 'border-red-300 bg-red-50' : 'border-gray-200' }}"
                    >
                    @error('total_payment')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm cursor-pointer">
                    Perbarui Transaksi
                </button>
                <a href="{{ route('transactions.index') }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('total_buy');
    const totalInput = document.getElementById('total_payment');

    // Kalkulasi otomatis (opsional saat edit, user mungkin mau manual)
    // Kita biarkan user mengedit manual, tapi jika mereka mengganti produk atau jumlah, kita update total
    function calculateTotal() {
        const option = productSelect.options[productSelect.selectedIndex];
        const price = option.getAttribute('data-price');
        const qty = qtyInput.value;

        if (price && qty) {
            totalInput.value = price * qty;
        }
    }

    productSelect.addEventListener('change', calculateTotal);
    qtyInput.addEventListener('input', calculateTotal);
</script>
@endpush
@endsection
