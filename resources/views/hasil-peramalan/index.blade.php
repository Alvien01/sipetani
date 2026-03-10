@extends('layouts.app')

@section('title', 'Hasil Peramalan')
@section('page-title', 'Hasil Analisis Peramalan')
@section('page-subtitle', 'Double Exponential Smoothing — Tabel hasil_peramalan')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
            <div class="w-9 h-9 bg-emerald-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-800">Generate Hasil Peramalan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Hitung dan simpan hasil peramalan ke tabel <code class="bg-gray-100 px-1 rounded text-emerald-700">hasil_peramalan</code></p>
            </div>
        </div>

        <form action="{{ route('hasil-peramalan.generate') }}" method="POST" class="px-6 py-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Produk --}}
                <div>
                    <label for="gen_product_id" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Produk</label>
                    <select id="gen_product_id" name="product_id" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition-all">
                        <option value="all" {{ old('product_id') == 'all' ? 'selected' : '' }}>Semua Produk (Gabungan)</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->product_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Alpha --}}
                <div>
                    <label for="gen_alpha" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Alpha (α) <span class="normal-case font-normal text-gray-400">0.01–0.99</span>
                    </label>
                    <input type="number" id="gen_alpha" name="alpha" step="0.01" min="0.01" max="0.99"
                        value="{{ old('alpha', 0.3) }}" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition-all">
                    @error('alpha')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Beta --}}
                <div>
                    <label for="gen_beta" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Beta (β) <span class="normal-case font-normal text-gray-400">0.00–0.99</span>
                    </label>
                    <input type="number" id="gen_beta" name="beta" step="0.01" min="0.00" max="0.99"
                        value="{{ old('beta', 0.3) }}" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition-all">
                    @error('beta')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Tipe Periode --}}
                <div>
                    <label for="gen_tipe" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Tipe Periode</label>
                    <select id="gen_tipe" name="tipe_periode" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition-all">
                        <option value="bulanan" {{ old('tipe_periode') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="mingguan" {{ old('tipe_periode') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    </select>
                    @error('tipe_periode')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Generate & Simpan
                </button>
            </div>
        </form>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
        <form method="GET" action="{{ route('hasil-peramalan.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Filter Produk</label>
                <select name="product_id"
                    class="px-3.5 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 outline-none transition-all">
                    <option value="">-- Semua Data --</option>
                    <option value="all" {{ request('product_id') == 'all' ? 'selected' : '' }}>Hasil Gabungan (Semua Produk)</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tipe Periode</label>
                <select name="tipe_periode"
                    class="px-3.5 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 outline-none transition-all">
                    <option value="">Semua Tipe</option>
                    <option value="bulanan"  {{ request('tipe_periode') == 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                    <option value="mingguan" {{ request('tipe_periode') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                </select>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Terapkan Filter
            </button>
            @if(request()->hasAny(['product_id','tipe_periode']))
            <a href="{{ route('hasil-peramalan.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- ===== STATS CARDS (jika filter produk aktif) ===== --}}
    @if($stats)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Produk</p>
            <p class="text-base font-bold text-gray-800 mt-1 truncate">{{ $stats['product_name'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">α={{ $stats['alpha'] }} β={{ $stats['beta'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Periode</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">data tersimpan</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">MAPE</p>
            @php $mape = $stats['avg_mape']; @endphp
            <p class="text-2xl font-bold mt-1 {{ $mape < 10 ? 'text-green-600' : ($mape < 20 ? 'text-blue-600' : ($mape < 50 ? 'text-yellow-600' : 'text-red-600')) }}">
                {{ number_format($mape, 2) }}%
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                @if($mape < 10) Sangat Baik
                @elseif($mape < 20) Baik
                @elseif($mape < 50) Cukup
                @else Buruk
                @endif
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Forecast Terakhir</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['last_forecast'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Periode {{ $stats['last_periode'] }}</p>
        </div>
    </div>

    {{-- ===== GRAFIK ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Grafik Kurva Peramalan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Aktual vs Forecast — Double Exponential Smoothing (Holt's Method)</p>
            </div>
            <div class="flex items-center gap-5 text-xs">
                <span class="flex items-center gap-1.5">
                    <span class="w-8 border-t-2 border-emerald-500 inline-block"></span>
                    <span class="text-gray-600">Aktual</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-8 border-t-2 border-dashed border-blue-500 inline-block"></span>
                    <span class="text-gray-600">Forecast</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-8 border-t-2 border-dotted border-orange-400 inline-block"></span>
                    <span class="text-gray-600">St (Level)</span>
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="relative" style="height: 300px;">
                <canvas id="hasilChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== TABEL HASIL PERAMALAN ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Tabel Hasil Peramalan</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $results->total() }} data ditemukan
                    @if(request('product_id') || request('tipe_periode'))
                        <span class="text-emerald-600">(filter aktif)</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Search --}}
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="hasilSearch" placeholder="Cari data..."
                        class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition-all w-48">
                </div>
                {{-- Hapus (jika filter produk aktif) --}}
                @if(request('product_id') && request('tipe_periode'))
                <form action="{{ route('hasil-peramalan.destroy-filter') }}" method="POST"
                    onsubmit="return confirm('Hapus semua data peramalan untuk filter ini?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                    <input type="hidden" name="tipe_periode" value="{{ request('tipe_periode') }}">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm text-red-600 border border-red-200 hover:bg-red-50 rounded-xl transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Data
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="hasilTable">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        @php
                            $cols = ['#', 'Produk', 'Periode', 'Tipe', 'Aktual', 'St', 'bt', 'Forecast', 'Alpha', 'Beta', 'PE (%)', 'MAPE (%)', 'Evaluasi', 'Dibuat'];
                        @endphp
                        @foreach($cols as $ci => $col)
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap
                            {{ in_array($ci, [0,3,12,13]) ? '' : 'cursor-pointer select-none' }}"
                            {{ !in_array($ci, [0,3,12,13]) ? 'data-col="'.$ci.'"' : '' }}>
                            <div class="flex items-center gap-1">
                                {{ $col }}
                                @if(!in_array($ci, [0,3,12,13]))
                                    <span class="sort-icon text-gray-300">↕</span>
                                @endif
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="hasilTbody">
                    @forelse($results as $r)
                    @php
                        $mapeVal  = $r->mape;
                        $peVal    = $r->pe;
                        $evalText = '-';
                        $evalColor = 'bg-gray-100 text-gray-500';
                        if ($mapeVal !== null) {
                            if ($mapeVal < 10)      { $evalText = 'Sangat Baik'; $evalColor = 'bg-green-100 text-green-700'; }
                            elseif ($mapeVal < 20)  { $evalText = 'Baik';        $evalColor = 'bg-blue-100 text-blue-700'; }
                            elseif ($mapeVal < 50)  { $evalText = 'Cukup';       $evalColor = 'bg-yellow-100 text-yellow-700'; }
                            else                    { $evalText = 'Buruk';        $evalColor = 'bg-red-100 text-red-700'; }
                        }
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors hasil-row">
                        <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">{{ $results->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5">
                            <span class="font-medium text-gray-800">{{ $r->product?->product_name ?? 'Hasil Gabungan' }}</span>
                        </td>
                        <td class="px-4 py-3.5 font-medium text-gray-700 whitespace-nowrap">{{ $r->periode }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold
                                {{ $r->tipe_periode === 'bulanan' ? 'bg-teal-100 text-teal-700' : 'bg-violet-100 text-violet-700' }}">
                                {{ ucfirst($r->tipe_periode) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800">
                            {{ $r->aktual !== null ? number_format($r->aktual, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-600">{{ number_format($r->st, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ number_format($r->bt, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 font-semibold text-blue-700">{{ number_format($r->forecast, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-gray-500 text-center">{{ $r->alpha }}</td>
                        <td class="px-4 py-3.5 text-gray-500 text-center">{{ $r->beta }}</td>
                        <td class="px-4 py-3.5">
                            @if($peVal !== null)
                            <div class="flex items-center gap-2">
                                <div class="w-14 bg-gray-100 rounded-full h-1.5 flex-shrink-0">
                                    <div class="h-1.5 rounded-full {{ $peVal < 10 ? 'bg-green-500' : ($peVal < 20 ? 'bg-blue-500' : ($peVal < 50 ? 'bg-yellow-500' : 'bg-red-500')) }}"
                                        style="width: {{ min($peVal, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold {{ $peVal < 10 ? 'text-green-600' : ($peVal < 20 ? 'text-blue-600' : ($peVal < 50 ? 'text-yellow-600' : 'text-red-600')) }}">
                                    {{ number_format($peVal, 2) }}%
                                </span>
                            </div>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($mapeVal !== null)
                            <span class="font-semibold {{ $mapeVal < 10 ? 'text-green-600' : ($mapeVal < 20 ? 'text-blue-600' : ($mapeVal < 50 ? 'text-yellow-600' : 'text-red-600')) }}">
                                {{ number_format($mapeVal, 2) }}%
                            </span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $evalColor }}">
                                {{ $evalText }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Belum ada data hasil peramalan</p>
                                    <p class="text-xs text-gray-400 mt-1">Gunakan form <strong>Generate & Simpan</strong> di atas untuk memulai</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($results->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500">
                Menampilkan {{ $results->firstItem() }}–{{ $results->lastItem() }} dari {{ $results->total() }} data
            </p>
            {{ $results->links() }}
        </div>
        @endif
    </div>

    {{-- ===== KETERANGAN RUMUS ===== --}}
    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-100 px-6 py-5">
        <h4 class="text-sm font-semibold text-emerald-800 mb-3">📘 Rumus Holt's Double Exponential Smoothing</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-emerald-900">
            <div class="space-y-1.5">
                <p><span class="font-semibold">Sₜ</span> = α·Yₜ + (1−α)·(Sₜ₋₁ + bₜ₋₁) &nbsp;→&nbsp; <em>Level (St)</em></p>
                <p><span class="font-semibold">bₜ</span> = β·(Sₜ − Sₜ₋₁) + (1−β)·bₜ₋₁ &nbsp;→&nbsp; <em>Trend (bt)</em></p>
            </div>
            <div class="space-y-1.5">
                <p><span class="font-semibold">F̂ₜ₊₁</span> = Sₜ + bₜ &nbsp;→&nbsp; <em>Forecast 1 periode ke depan</em></p>
                <p><span class="font-semibold">MAPE</span> = (1/n) Σ |Yₜ − F̂ₜ| / Yₜ × 100%</p>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-3 text-xs">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-lg font-semibold">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>Sangat Baik: MAPE &lt; 10%
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-lg font-semibold">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>Baik: 10% ≤ MAPE &lt; 20%
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-lg font-semibold">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>Cukup: 20% ≤ MAPE &lt; 50%
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 rounded-lg font-semibold">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>Buruk: MAPE ≥ 50%
            </span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== CHART =====
    @if($stats && $results->count())
    const chartLabels   = @json($results->pluck('periode')->values());
    const chartAktual   = @json($results->pluck('aktual')->values());
    const chartForecast = @json($results->pluck('forecast')->values());
    const chartSt       = @json($results->pluck('st')->values());

    const ctx = document.getElementById('hasilChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Aktual',
                        data: chartAktual,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5,150,105,0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#059669',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true,
                    },
                    {
                        label: 'Forecast',
                        data: chartForecast,
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        borderWidth: 2.5,
                        borderDash: [6, 3],
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false,
                    },
                    {
                        label: 'St (Level)',
                        data: chartSt,
                        borderColor: '#f97316',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        borderDash: [3, 3],
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        tension: 0.35,
                        fill: false,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,0.92)',
                        titleColor: '#f9fafb',
                        bodyColor: '#d1d5db',
                        padding: 12,
                        callbacks: {
                            label: ctx => ' ' + ctx.dataset.label + ': ' +
                                Number(ctx.parsed.y).toLocaleString('id-ID', { maximumFractionDigits: 2 })
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 11 }, color: '#9ca3af' },
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af',
                            callback: v => Number(v).toLocaleString('id-ID')
                        },
                        beginAtZero: false,
                    }
                }
            }
        });
    }
    @endif

    // ===== SEARCH =====
    const searchInput = document.getElementById('hasilSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.hasil-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // ===== SORT =====
    const table = document.getElementById('hasilTable');
    if (table) {
        let sortDir = {};
        table.querySelectorAll('th[data-col]').forEach(th => {
            th.addEventListener('click', function () {
                const col = parseInt(this.dataset.col);
                sortDir[col] = !sortDir[col];
                const tbody = document.getElementById('hasilTbody');
                const rows  = Array.from(tbody.querySelectorAll('.hasil-row'));
                rows.sort((a, b) => {
                    const aVal = a.cells[col]?.textContent.trim().replace(/[.,\s%]/g, '') || '';
                    const bVal = b.cells[col]?.textContent.trim().replace(/[.,\s%]/g, '') || '';
                    const aNum = parseFloat(aVal.replace(',', '.'));
                    const bNum = parseFloat(bVal.replace(',', '.'));
                    if (!isNaN(aNum) && !isNaN(bNum)) return sortDir[col] ? aNum - bNum : bNum - aNum;
                    return sortDir[col] ? aVal.localeCompare(bVal, 'id') : bVal.localeCompare(aVal, 'id');
                });
                table.querySelectorAll('.sort-icon').forEach(ic => ic.textContent = '↕');
                this.querySelector('.sort-icon').textContent = sortDir[col] ? '↑' : '↓';
                rows.forEach(r => tbody.appendChild(r));
            });
        });
    }

});
</script>
@endpush
