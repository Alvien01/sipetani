@extends('layouts.app')

@section('title', 'Peramalan')
@section('page-title', 'Peramalan Penjualan')
@section('page-subtitle', 'Double Exponential Smoothing — Analisis tren penjualan produk')

@section('content')
<div class="space-y-6">

    {{-- ===== FORM GENERATE ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
            <div class="w-9 h-9 bg-green-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-800">Generate Peramalan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pilih produk, nilai alpha, dan tipe periode untuk menghitung peramalan</p>
            </div>
        </div>

        <form action="{{ route('forecasts.generate') }}" method="POST" class="px-6 py-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Produk --}}
                <div>
                    <label for="product_id" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Produk
                    </label>
                    <select id="product_id" name="product_id" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition-all">
                        <option value="">— Pilih Produk —</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->product_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alpha --}}
                <div>
                    <label for="alpha" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Nilai Alpha (α)
                        <span class="normal-case font-normal text-gray-400 ml-1">0.01 – 0.99</span>
                    </label>
                    <input type="number" id="alpha" name="alpha" step="0.01" min="0.01" max="0.99"
                        value="{{ old('alpha', 0.3) }}" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition-all">
                    @error('alpha')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipe --}}
                <div>
                    <label for="type" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Tipe Periode
                    </label>
                    <select id="type" name="type" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition-all">
                        <option value="monthly" {{ old('type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="weekly"  {{ old('type') == 'weekly'  ? 'selected' : '' }}>Mingguan</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 active:scale-95 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Generate Peramalan
                </button>
            </div>
        </form>
    </div>

    @if($forecasts->count())
    {{-- ===== INFO CARDS ===== --}}
    @php
        $firstForecast = $forecasts->first();
        $avgPe = $forecasts->avg('pe');
        $totalActual = $forecasts->sum('total');
        $lastForecast = $forecasts->last();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Produk --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Produk</p>
            <p class="text-lg font-bold text-gray-800 mt-1 truncate">{{ $firstForecast->product->product_name ?? '-' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">α = {{ $firstForecast->alpha }}</p>
        </div>
        {{-- Total Data --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Data</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $forecasts->total() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">periode {{ $firstForecast->type == 'monthly' ? 'bulanan' : 'mingguan' }}</p>
        </div>
        {{-- Rata-rata MAPE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Rata-rata MAPE</p>
            <p class="text-2xl font-bold mt-1
                {{ $avgPe < 10 ? 'text-green-600' : ($avgPe < 20 ? 'text-blue-600' : ($avgPe < 50 ? 'text-yellow-600' : 'text-red-600')) }}">
                {{ number_format($avgPe, 2) }}%
            </p>
            <p class="text-xs text-gray-400 mt-0.5">Mean Absolute Percentage Error</p>
        </div>
        {{-- Forecast Berikutnya --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Forecast Berikutnya</p>
            @php $nextForecast = $lastForecast->at + $lastForecast->bt; @endphp
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($nextForecast, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">estimasi periode selanjutnya</p>
        </div>
    </div>

    {{-- ===== GRAFIK ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Grafik Peramalan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbandingan data aktual vs hasil peramalan Double Exponential Smoothing</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                    <span class="text-gray-600">Aktual</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    <span class="text-gray-600">Forecast</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>
                    <span class="text-gray-600">S' (Smoothing 1)</span>
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="relative" style="height: 320px;">
                <canvas id="forecastChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ===== DATATABLE ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Tabel Hasil Peramalan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Detail perhitungan Double Exponential Smoothing per periode</p>
            </div>
            {{-- Search --}}
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="tableSearch" placeholder="Cari data..."
                    class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition-all w-52">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="forecastTable">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="0">
                            <div class="flex items-center gap-1">#<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="1">
                            <div class="flex items-center gap-1">Periode<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="2">
                            <div class="flex items-center gap-1">Aktual (Yt)<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="3">
                            <div class="flex items-center gap-1">S' (St)<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="4">
                            <div class="flex items-center gap-1">S'' (SSt)<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="5">
                            <div class="flex items-center gap-1">at<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="6">
                            <div class="flex items-center gap-1">bt<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="7">
                            <div class="flex items-center gap-1">Forecast<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="8">
                            <div class="flex items-center gap-1">Selisih<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap cursor-pointer select-none" data-col="9">
                            <div class="flex items-center gap-1">MAPE (%)<span class="sort-icon text-gray-300">↕</span></div>
                        </th>
                        <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            Evaluasi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="forecastTbody">
                    @forelse($forecasts as $i => $f)
                    @php
                        $periode = $f->type === 'monthly'
                            ? \Carbon\Carbon::createFromDate($f->year, $f->month, 1)->translatedFormat('F Y')
                            : 'Minggu ' . $f->weekly . ' ' . $f->year;
                        $evalColor = match($f->evaluasi) {
                            'Sangat Baik' => 'bg-green-100 text-green-700',
                            'Baik'        => 'bg-blue-100 text-blue-700',
                            'Cukup'       => 'bg-yellow-100 text-yellow-700',
                            default       => 'bg-red-100 text-red-700',
                        };
                        $selisihColor = $f->selisih >= 0 ? 'text-green-600' : 'text-red-500';
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors forecast-row">
                        <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">{{ $forecasts->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5 font-medium text-gray-700 whitespace-nowrap">{{ $periode }}</td>
                        <td class="px-4 py-3.5 text-gray-800 font-semibold">{{ number_format($f->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ number_format($f->st, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ number_format($f->sst, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ number_format($f->at, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ number_format($f->bt, 4, ',', '.') }}</td>
                        <td class="px-4 py-3.5 font-semibold text-blue-700">{{ number_format($f->forecast, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 font-medium {{ $selisihColor }}">
                            {{ $f->selisih >= 0 ? '+' : '' }}{{ number_format($f->selisih, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5 w-16">
                                    <div class="h-1.5 rounded-full {{ $f->pe < 10 ? 'bg-green-500' : ($f->pe < 20 ? 'bg-blue-500' : ($f->pe < 50 ? 'bg-yellow-500' : 'bg-red-500')) }}"
                                        style="width: {{ min($f->pe, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold {{ $f->pe < 10 ? 'text-green-600' : ($f->pe < 20 ? 'text-blue-600' : ($f->pe < 50 ? 'text-yellow-600' : 'text-red-600')) }}">
                                    {{ number_format($f->pe, 2) }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $evalColor }}">
                                {{ $f->evaluasi }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">Belum ada data peramalan</p>
                                <p class="text-xs text-gray-400">Gunakan form di atas untuk generate peramalan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($forecasts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-500">
                Menampilkan {{ $forecasts->firstItem() }}–{{ $forecasts->lastItem() }} dari {{ $forecasts->total() }} data
            </p>
            {{ $forecasts->links() }}
        </div>
        @endif
    </div>

    {{-- ===== LEGEND / KETERANGAN ===== --}}
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border border-green-100 px-6 py-5">
        <h4 class="text-sm font-semibold text-green-800 mb-3">📘 Keterangan Rumus Double Exponential Smoothing</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-green-900">
            <div class="space-y-1.5">
                <p><span class="font-semibold">S'ₜ</span> = α·Yₜ + (1−α)·S'ₜ₋₁ &nbsp;→&nbsp; <em>Single Smoothing</em></p>
                <p><span class="font-semibold">S''ₜ</span> = α·S'ₜ + (1−α)·S''ₜ₋₁ &nbsp;→&nbsp; <em>Double Smoothing</em></p>
                <p><span class="font-semibold">aₜ</span> = 2·S'ₜ − S''ₜ &nbsp;→&nbsp; <em>Level</em></p>
            </div>
            <div class="space-y-1.5">
                <p><span class="font-semibold">bₜ</span> = (α/(1−α))·(S'ₜ − S''ₜ) &nbsp;→&nbsp; <em>Trend</em></p>
                <p><span class="font-semibold">F̂ₜ₊₁</span> = aₜ + bₜ &nbsp;→&nbsp; <em>Nilai Forecast</em></p>
                <p><span class="font-semibold">MAPE</span> = |Yₜ − F̂ₜ| / Yₜ × 100%</p>
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
    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-20 text-center">
        <div class="flex flex-col items-center gap-4">
            <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
            </div>
            <div>
                <p class="text-base font-semibold text-gray-700">Belum Ada Data Peramalan</p>
                <p class="text-sm text-gray-400 mt-1">Isi form di atas dan klik <strong>Generate Peramalan</strong> untuk memulai analisis.</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== CHART =====
    @if($forecasts->count())
    const labels  = @json($forecasts->map(function($f) {
        if ($f->type === 'monthly') {
            return \Carbon\Carbon::createFromDate($f->year, $f->month, 1)->translatedFormat('M Y');
        }
        return 'Mg ' . $f->weekly . '/' . $f->year;
    })->values());

    const actuals   = @json($forecasts->pluck('total')->values());
    const forecasts = @json($forecasts->pluck('forecast')->values());
    const st        = @json($forecasts->pluck('st')->values());

    const ctx = document.getElementById('forecastChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Aktual (Yt)',
                        data: actuals,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22,163,74,0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#16a34a',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true,
                    },
                    {
                        label: 'Forecast (F̂t)',
                        data: forecasts,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.06)',
                        borderWidth: 2.5,
                        borderDash: [6, 3],
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false,
                    },
                    {
                        label: "S' (Single Smoothing)",
                        data: st,
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
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,0.92)',
                        titleColor: '#f9fafb',
                        bodyColor: '#d1d5db',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': ' +
                                    Number(ctx.parsed.y).toLocaleString('id-ID', {maximumFractionDigits: 2});
                            }
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

    // ===== TABLE SEARCH =====
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.forecast-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // ===== TABLE SORT =====
    const table = document.getElementById('forecastTable');
    if (table) {
        let sortDir = {};
        table.querySelectorAll('th[data-col]').forEach(th => {
            th.addEventListener('click', function () {
                const col = parseInt(this.dataset.col);
                sortDir[col] = !sortDir[col];
                const tbody = document.getElementById('forecastTbody');
                const rows = Array.from(tbody.querySelectorAll('.forecast-row'));
                rows.sort((a, b) => {
                    const aVal = a.cells[col]?.textContent.trim().replace(/[.,\s%+]/g, '') || '';
                    const bVal = b.cells[col]?.textContent.trim().replace(/[.,\s%+]/g, '') || '';
                    const aNum = parseFloat(aVal.replace(',', '.'));
                    const bNum = parseFloat(bVal.replace(',', '.'));
                    if (!isNaN(aNum) && !isNaN(bNum)) {
                        return sortDir[col] ? aNum - bNum : bNum - aNum;
                    }
                    return sortDir[col]
                        ? aVal.localeCompare(bVal, 'id')
                        : bVal.localeCompare(aVal, 'id');
                });
                // Update sort icons
                table.querySelectorAll('.sort-icon').forEach(ic => ic.textContent = '↕');
                this.querySelector('.sort-icon').textContent = sortDir[col] ? '↑' : '↓';
                rows.forEach(r => tbody.appendChild(r));
            });
        });
    }

});
</script>
@endpush
