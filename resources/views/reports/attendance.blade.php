@extends('layouts.dashboard')

@section('title', 'Laporan Kehadiran')

@section('content')
<div class="space-y-6">
    @if($alerts->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">Belum Ada Data Alarm</h3>
            <p class="text-sm text-slate-500">Belum ada alarm yang dibuat. Silakan buat alarm terlebih dahulu.</p>
        </div>
    @else
        <!-- Alerts List -->
        <div class="space-y-4">
            @foreach($alerts as $alert)
                @php
                    $logs = $alert->attendanceLogs;
                    $totalPersonel = $logs->count();
                    $hadir = $logs->where('status', 'hadir')->count();
                    $tidakHadir = $logs->where('status', 'tidak_hadir')->count();
                    $percentage = $totalPersonel > 0 ? round(($hadir / $totalPersonel) * 100, 2) : 0;
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Alert Header (Clickable) -->
                    <div class="bg-gradient-to-r from-green-700 to-green-800 p-5 cursor-pointer hover:from-green-800 hover:to-green-900 transition-all duration-200"
                        onclick="toggleAccordion('alert-{{ $alert->id }}')">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-white">{{ $alert->title }}</h3>
                                    @if($alert->status === 'active')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            AKTIF
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            SELESAI
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-4 text-green-100 text-sm">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $alert->started_at?->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $alert->triggered_by }}
                                    </span>
                                    <span class="bg-white/20 px-2.5 py-1 rounded-lg text-xs font-semibold">
                                        Tingkat {{ strtoupper($alert->level) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <!-- Quick Stats -->
                                <div
                                    class="hidden md:flex items-center gap-4 bg-white/10 rounded-lg px-4 py-2 backdrop-blur-sm">
                                    <div class="text-center">
                                        <div class="text-xl font-bold text-white">{{ $hadir }}/{{ $totalPersonel }}
                                        </div>
                                        <div class="text-xs text-green-200">Hadir</div>
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="text-xl font-bold {{ $percentage >= 80 ? 'text-green-200' : ($percentage >= 60 ? 'text-yellow-200' : 'text-red-200') }}">
                                            {{ $percentage }}%</div>
                                        <div class="text-xs text-green-200">Kehadiran</div>
                                    </div>
                                </div>
                                <!-- Chevron Icon -->
                                <svg class="w-5 h-5 text-white transform transition-transform duration-200 chevron-icon"
                                    id="chevron-alert-{{ $alert->id }}" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Content (Collapsible) -->
                    <div id="alert-{{ $alert->id }}" class="hidden">
                        <!-- Summary Cards -->
                        <div class="p-6 bg-slate-50 border-b border-slate-200">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- Total Personel -->
                                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="bg-blue-50 rounded-lg p-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-600 uppercase">Total</div>
                                    <div class="text-2xl font-bold text-slate-900">{{ $totalPersonel }}</div>
                                </div>

                                <!-- Hadir -->
                                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="bg-green-50 rounded-lg p-2">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-600 uppercase">Hadir</div>
                                    <div class="text-2xl font-bold text-green-600">{{ $hadir }}</div>
                                </div>

                                <!-- Tidak Hadir -->
                                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="bg-red-50 rounded-lg p-2">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-600 uppercase">Tidak Hadir</div>
                                    <div class="text-2xl font-bold text-red-600">{{ $tidakHadir }}</div>
                                </div>

                                <!-- Persentase -->
                                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="bg-yellow-50 rounded-lg p-2">
                                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-600 uppercase">Persentase</div>
                                    <div
                                        class="text-2xl font-bold {{ $percentage >= 80 ? 'text-green-600' : ($percentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $percentage }}%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Table -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-base font-semibold text-slate-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Detail Kehadiran Personel ({{ $totalPersonel }} orang)
                                </h4>
                                <div class="flex gap-2">
                                    <a href="/reports/export/{{ $alert->id }}" target="_blank"
                                        class="inline-flex items-center px-3 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Cetak
                                    </a>
                                </div>
                            </div>

                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                                Nama</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                                NRP</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                                Jabatan</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                                Waktu Hadir</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                                Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        @foreach($logs as $index => $log)
                                            <tr
                                                class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-green-50 transition duration-150">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-900">
                                                    {{ $log->personel->name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                                    {{ $log->personel->nrp }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                                    {{ ucfirst($log->role) }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if($log->status === 'hadir')
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Hadir
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Tidak Hadir
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                                    {{ $log->attended_at?->format('d/m/Y H:i:s') ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-slate-600">{{ $log->keterangan }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function toggleAccordion(id) {
        const content = document.getElementById(id);
        const chevron = document.getElementById('chevron-' + id);

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }
</script>
@endsection