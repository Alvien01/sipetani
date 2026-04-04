@extends('layouts.app')

@section('title', 'Rekomendasi Stok')
@section('page-title', 'Rekomendasi Stok Produk')
@section('page-subtitle', 'Saran kebutuhan stok berdasarkan hasil prediksi Holt-Winters')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
        <form method="GET" action="{{ route('stock-recommendations.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tipe Periode</label>
                <select name="tipe_periode" class="px-3.5 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 outline-none transition-all">
                    <option value="">Semua Tipe</option>
                    <option value="bulanan" {{ request('tipe_periode') == 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                    <option value="mingguan" {{ request('tipe_periode') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Terapkan Filter
            </button>
            @if(request()->hasAny(['tipe_periode']))
            <a href="{{ route('stock-recommendations.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Daftar Rekomendasi</h3>
                <p class="text-xs text-gray-500 mt-0.5">Disarankan berdasarkan prediksi per minggu/bulan depan.</p>
            </div>
            @if(request('tipe_periode'))
            <form action="{{ route('stock-recommendations.destroy') }}" method="POST" onsubmit="return confirm('Hapus semua riwayat rekomendasi filter ini?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tipe_periode" value="{{ request('tipe_periode') }}">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors cursor-pointer">
                    Hapus Riwayat Filter Ini
                </button>
            </form>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase sortable">Produk</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase sortable">Periode Depan</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase sortable">Prediksi Kebutuhan</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase sortable">Stok Aman</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase sortable">Stok Gudang</th>
                        <th class="px-4 py-4 text-xs font-semibold text-emerald-700 uppercase bg-emerald-50 sortable">Rekomendasi Restock</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase sortable">Terakhir Update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recommendations as $r)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-4">
                            <span class="font-bold text-gray-800">{{ $r->product->product_name ?? 'Produk Dihapus' }}</span>
                        </td>
                        <td class="px-4 py-4 text-gray-700 font-medium">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $r->tipe_periode === 'bulanan' ? 'bg-teal-100 text-teal-700' : 'bg-violet-100 text-violet-700' }}">
                                {{ $r->periode }} ({{ ucfirst($r->tipe_periode) }})
                            </span>
                        </td>
                        <td class="px-4 py-4 text-blue-600 font-semibold text-base">{{ $r->forecast_qty }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $r->safety_stock }}</td>
                        <td class="px-4 py-4 text-gray-600"><span class="px-2 py-1 bg-gray-100 rounded font-mono">{{ $r->current_stock }}</span></td>
                        <td class="px-4 py-4 bg-emerald-50/50">
                            @if($r->recommended_qty > 0)
                                <span class="text-emerald-700 font-black text-lg gap-1 flex items-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    {{ $r->recommended_qty }}
                                </span>
                            @else
                                <span class="text-gray-400 font-medium text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Aman (0)
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-500">{{ $r->updated_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Belum ada rincian rekomendasi</p>
                                    <p class="text-xs text-gray-400 mt-1">Lakukan "Generate" di menu Hasil Analisis Peramalan terlebih dahulu.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($recommendations->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $recommendations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
