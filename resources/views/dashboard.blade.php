@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan data & analisis SiPetani')

@section('content')
<div class="space-y-6">

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Users --}}
        <a href="{{ route('users.index') }}"
            class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md hover:border-green-200 transition-all duration-200">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors flex-shrink-0">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Users</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalUsers) }}</p>
                <p class="text-xs text-green-600 mt-0.5 font-medium">pengguna aktif</p>
            </div>
        </a>

        {{-- Total Produk --}}
        <a href="{{ route('products.index') }}"
            class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md hover:border-emerald-200 transition-all duration-200">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-200 transition-colors flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Produk</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalProducts) }}</p>
                <p class="text-xs text-emerald-600 mt-0.5 font-medium">jenis produk</p>
            </div>
        </a>

        {{-- Total Transaksi --}}
        <a href="{{ route('transactions.index') }}"
            class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md hover:border-blue-200 transition-all duration-200">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors flex-shrink-0">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalTransactions) }}</p>
                <div class="flex items-center gap-1 mt-0.5">
                    @if($trxGrowth >= 0)
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                        </svg>
                        <span class="text-xs text-green-600 font-medium">+{{ $trxGrowth }}% bulan ini</span>
                    @else
                        <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span class="text-xs text-red-500 font-medium">{{ $trxGrowth }}% bulan ini</span>
                    @endif
                </div>
            </div>
        </a>

        {{-- Total Omzet --}}
        <div class="bg-gradient-to-br from-green-700 to-emerald-800 rounded-2xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-green-200 uppercase tracking-wide">Total Omzet</p>
                <p class="text-xl font-bold text-white mt-0.5 truncate">
                    Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                </p>
                <p class="text-xs text-green-300 mt-0.5">
                    Bulan ini: Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}
                </p>
            </div>
        </div>

    </div>

    {{-- ===== GRAFIK TRANSAKSI PER BULAN ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Grafik Transaksi Bulanan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Jumlah transaksi & omzet 12 bulan terakhir</p>
            </div>
            {{-- Toggle chart type --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                <button id="btnTrx" onclick="switchChart('trx')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-green-700 shadow-sm transition-all">
                    Transaksi
                </button>
                <button id="btnOmzet" onclick="switchChart('omzet')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-500 hover:text-gray-700 transition-all">
                    Omzet
                </button>
                <button id="btnQty" onclick="switchChart('qty')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-500 hover:text-gray-700 transition-all">
                    Qty Terjual
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="relative" style="height: 280px;">
                <canvas id="trxChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ===== ROW: TOP PRODUK + TRANSAKSI TERBARU ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Top 5 Produk (Donut + List) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Top 5 Produk</h3>
                <p class="text-xs text-gray-500 mt-0.5">Berdasarkan jumlah transaksi</p>
            </div>
            <div class="p-5">
                {{-- Donut chart --}}
                <div class="relative mx-auto" style="height: 180px; max-width: 180px;">
                    <canvas id="donutChart"></canvas>
                </div>
                {{-- List --}}
                <div class="mt-4 space-y-2.5">
                    @php
                        $donutColors = ['#16a34a','#3b82f6','#f97316','#8b5cf6','#ec4899'];
                        $maxTrx = $topProducts->max('total_trx') ?: 1;
                    @endphp
                    @forelse($topProducts as $i => $tp)
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                            style="background:{{ $donutColors[$i] ?? '#9ca3af' }}"></span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-gray-700 truncate">
                                    {{ $tp->product?->product_name ?? 'Produk #'.$tp->product_id }}
                                </p>
                                <span class="text-xs font-semibold text-gray-600 ml-2 flex-shrink-0">
                                    {{ number_format($tp->total_trx) }}x
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1">
                                <div class="h-1 rounded-full transition-all duration-500"
                                    style="width: {{ ($tp->total_trx / $maxTrx) * 100 }}%; background: {{ $donutColors[$i] ?? '#9ca3af' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada data transaksi</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Transaksi Terbaru</h3>
                    <p class="text-xs text-gray-500 mt-0.5">8 transaksi paling baru</p>
                </div>
                <a href="{{ route('transactions.index') }}"
                    class="text-xs text-green-700 font-semibold hover:underline flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTransactions as $trx)
                <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50/60 transition-colors">
                    <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">
                            {{ $trx->product?->product_name ?? 'Produk #'.$trx->product_id }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ \Carbon\Carbon::parse($trx->date_sale)->translatedFormat('d M Y') }}
                            &nbsp;·&nbsp; {{ number_format($trx->total_buy) }} unit
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-semibold text-gray-800">
                            Rp {{ number_format($trx->total_payment, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-400">Belum ada transaksi</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ===== QUICK ACTIONS ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">⚡ Aksi Cepat</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
            </a>
            <a href="{{ route('products.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Produk
            </a>
            <a href="{{ route('transactions.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Transaksi
            </a>
            <a href="{{ route('forecasts.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Peramalan
            </a>
            <a href="{{ route('hasil-peramalan.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Hasil Analisis
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels  = @json($chartLabels);
    const dataTrx   = @json($chartTrx);
    const dataOmzet = @json($chartOmzet);
    const dataQty   = @json($chartQty);

    // ── Grafik Transaksi Bulanan (Bar + Line) ──────────────────────
    const trxCtx = document.getElementById('trxChart');
    let trxChart;

    function buildTrxChart(mode) {
        if (trxChart) trxChart.destroy();

        let dataset, label, color, yLabel;
        if (mode === 'omzet') {
            dataset = dataOmzet;
            label   = 'Omzet (Rp)';
            color   = '#3b82f6';
            yLabel  = 'Rp';
        } else if (mode === 'qty') {
            dataset = dataQty;
            label   = 'Qty Terjual';
            color   = '#f97316';
            yLabel  = 'unit';
        } else {
            dataset = dataTrx;
            label   = 'Jumlah Transaksi';
            color   = '#16a34a';
            yLabel  = 'trx';
        }

        trxChart = new Chart(trxCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label,
                        data: dataset,
                        backgroundColor: color + '22',
                        borderColor: color,
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                        order: 2,
                    },
                    {
                        label: label + ' (tren)',
                        data: dataset,
                        type: 'line',
                        borderColor: color,
                        backgroundColor: 'transparent',
                        borderWidth: 2.5,
                        pointBackgroundColor: color,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.4,
                        order: 1,
                    }
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
                            label: ctx => {
                                const v = ctx.parsed.y;
                                if (mode === 'omzet') return ' Rp ' + Number(v).toLocaleString('id-ID');
                                return ' ' + Number(v).toLocaleString('id-ID') + ' ' + yLabel;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af' },
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af',
                            callback: v => mode === 'omzet'
                                ? 'Rp ' + Number(v).toLocaleString('id-ID')
                                : Number(v).toLocaleString('id-ID')
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    buildTrxChart('trx');

    window.switchChart = function(mode) {
        buildTrxChart(mode);
        ['trx','omzet','qty'].forEach(m => {
            const btn = document.getElementById('btn' + m.charAt(0).toUpperCase() + m.slice(1));
            if (btn) {
                if (m === mode) {
                    btn.classList.add('bg-white','text-green-700','shadow-sm');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('bg-white','text-green-700','shadow-sm');
                    btn.classList.add('text-gray-500');
                }
            }
        });
    };

    // ── Donut Chart Top Produk ─────────────────────────────────────
    const donutCtx = document.getElementById('donutChart');
    @if($topProducts->isNotEmpty())
    const donutLabels = @json($topProducts->map(fn($t) => $t->product?->product_name ?? 'Produk #'.$t->product_id)->values());
    const donutData   = @json($topProducts->pluck('total_trx')->values());
    const donutColors = ['#16a34a','#3b82f6','#f97316','#8b5cf6','#ec4899'];

    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutData,
                backgroundColor: donutColors,
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.92)',
                    titleColor: '#f9fafb',
                    bodyColor: '#d1d5db',
                    padding: 10,
                    callbacks: {
                        label: ctx => ' ' + Number(ctx.parsed).toLocaleString('id-ID') + ' transaksi'
                    }
                }
            }
        }
    });
    @endif

});
</script>
@endpush
