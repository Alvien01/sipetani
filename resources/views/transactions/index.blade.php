@extends('layouts.app')

@section('title', 'Transaksi')
@section('page-title', 'Data Transaksi')
@section('page-subtitle', 'Kelola data penjualan produk')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="flex flex-col md:flex-row md:items-center justify-between px-6 py-5 border-b border-gray-100 gap-4">
        <div class="flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-800">Daftar Transaksi</h3>
            <p class="text-sm text-gray-500 mt-0.5">Total {{ $transactions->total() }} transaksi</p>
        </div>

        <div class="flex-1 max-w-md mx-0 md:mx-4">
            <form action="{{ route('transactions.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari transaksi (produk, tanggal)..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('transactions.export.csv') }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900 text-sm font-medium rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export
            </a>

            {{-- Import Button --}}
            <button onclick="document.getElementById('importModal').classList.remove('hidden')"
               class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900 text-sm font-medium rounded-xl transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Import
            </button>

            {{-- Create Button --}}
            <a href="{{ route('transactions.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah (Qty)</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pembayaran</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $transactions->firstItem() + $loop->index }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($trx->date_sale)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $trx->product->product_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $trx->total_buy }} unit</td>
                    <td class="px-6 py-4 text-sm font-medium text-green-700">Rp {{ number_format($trx->total_payment, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('transactions.edit', $trx) }}"
                               class="p-2 text-gray-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('transactions.destroy', $trx) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500">Belum ada transaksi</p>
                            <a href="{{ route('transactions.create') }}" class="text-sm text-green-700 font-medium hover:underline">Tambah transaksi pertama</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

{{-- Modal Import --}}
<div id="importModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-100">
        <div class="p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Import Transaksi</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('transactions.import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV/Excel</label>
                        <input type="file" name="file" required accept=".csv,.txt"
                            class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2.5 file:px-4
                            file:rounded-xl file:border-0
                            file:text-sm file:font-semibold
                            file:bg-green-50 file:text-green-700
                            hover:file:bg-green-100
                            transition-all cursor-pointer border border-gray-200 rounded-xl">
                        <p class="text-xs text-gray-400 mt-2">Mendukung format .csv</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Belum punya format?</p>
                            <a href="{{ route('transactions.template.csv') }}" class="text-xs text-blue-600 hover:text-blue-800 hover:underline">Download Template CSV</a>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 bg-gray-50 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-green-700 text-white text-sm font-medium rounded-xl hover:bg-green-800 transition-colors shadow-sm">
                        Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
