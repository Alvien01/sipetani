@extends('layouts.dashboard')
@section('title', 'Dashboard Siaga')
@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900" data-stat="total">{{ $totalPersonel }}</p>
                <p class="text-sm font-medium text-slate-500">Total Personel</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-3 bg-orange-50 rounded-xl text-orange-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900" data-stat="siaga">{{ $dalamSiaga }}</p>
                <p class="text-sm font-medium text-slate-500">Dalam Siaga</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-3 bg-green-50 rounded-xl text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900" data-stat="terkonfirmasi">{{ $terkonfirmasi }}</p>
                <p class="text-sm font-medium text-slate-500">Terkonfirmasi</p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900" data-stat="tersedia">{{ $tersedia }}</p>
                <p class="text-sm font-medium text-slate-500">Personel Tersedia</p>
            </div>
        </div>
    </div>

    <!-- Alert / Status Section -->
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
        <div class="bg-gradient-to-br from-green-700 to-green-800 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Status Siaga</h3>
                    <p class="text-green-100 text-sm mt-1">Sistem Peringatan Dini</p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                </div>
            </div>

            @if ($activeAlert)
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-bold text-lg">{{ $activeAlert->title }}</p>
                            <p class="text-green-100 text-sm mt-1">{{ $activeAlert->message }}</p>
                            @if ($activeAlert->level)
                                <span
                                    class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-xs font-semibold">SIAGA TK {{ $activeAlert->level }}</span>
                            @endif
                            <p class="text-green-100 text-xs mt-2">Dimulai:
                                {{ $activeAlert->started_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                        </span>
                    </div>
                </div>
                <form action="{{ route('alarm.stop') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="cursor-pointer w-full bg-white text-green-700 font-semibold py-3 rounded-xl hover:bg-green-50 transition-all shadow-md">
                        Tutup Siaga
                    </button>
                </form>
            @else
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-4">
                    <p class="text-center text-green-100">Tidak ada siaga aktif</p>
                </div>
                <button type="button" onclick="openAlarmModal()"
                    class="cursor-pointer w-full bg-white text-green-700 font-semibold py-3 rounded-xl hover:bg-green-50 transition-all shadow-md">
                    Kirim Alarm
                </button>
            @endif
        </div>

        @if ($activeAlert)
            <div class="w-full bg-slate-100 rounded-full h-2.5 mb-2 mt-6">
                @php
                    $percentage = $totalPersonelnoKomandan > 0 ? ($terkonfirmasi / $totalPersonelnoKomandan) * 100 : 0;
                @endphp
                <div class="bg-rose-500 h-2.5 rounded-full transition-all duration-500"
                    style="width: {{ $percentage }}%"></div>
            </div>
            <p class="text-sm text-slate-500 font-medium progress-text">{{ $terkonfirmasi }}/{{ $totalPersonelnoKomandan }} Personel Terkonfirmasi
                ({{ round($percentage) }}%)</p>
        @endif
    </div>

    <!-- Bottom Section: Table & Map -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
        <!-- Table Section -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-indigo-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pangkat</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">NRP</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jabatan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($personels as $personel)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $personel->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $personel->rank }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $personel->nrp }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $personel->position }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-bold rounded-full
                                    {{ $personel->status === 'Terkonfirmasi'
                                        ? 'text-emerald-600 bg-emerald-100'
                                        : ($personel->status === 'Siaga'
                                            ? 'text-orange-600 bg-orange-100'
                                            : 'text-blue-600 bg-blue-100') }}">
                                        {{ $personel->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada data personel.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alarm Modal -->
    <div id="alarmModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">Kirim Alarm Siaga</h3>
                <button onclick="closeAlarmModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('alarm.trigger') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat Kesiagaan</label>
                        <select name="level" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent">
                            <option value="">Pilih Tingkat Siaga</option>
                            <option value="I">SIAGA TK I</option>
                            <option value="II">SIAGA TK II</option>
                            <option value="III">SIAGA TK III</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Alarm</label>
                        <input type="text" name="title" required placeholder="Contoh: Panggilan Luar Biasa"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pesan</label>
                        <textarea name="message" required rows="4" placeholder="Masukkan pesan alarm..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent resize-none"></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeAlarmModal()"
                            class="cursor-pointer flex-1 px-4 py-3 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="cursor-pointer flex-1 px-4 py-3 bg-gradient-to-r from-green-700 to-green-800 text-white font-semibold rounded-xl hover:from-green-800 hover:to-green-900 transition-all shadow-lg">
                            Kirim Alarm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAlarmModal() {
            document.getElementById('alarmModal').classList.remove('hidden');
        }

        function closeAlarmModal() {
            document.getElementById('alarmModal').classList.add('hidden');
        }

        document.getElementById('alarmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAlarmModal();
            }
        });

        function updateDashboardStats() {
            fetch('{{ route('dashboard.stats') }}')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('[data-stat="total"]').textContent = data.totalPersonel;
                    document.querySelector('[data-stat="siaga"]').textContent = data.dalamSiaga;
                    document.querySelector('[data-stat="terkonfirmasi"]').textContent = data.terkonfirmasi;
                    document.querySelector('[data-stat="tersedia"]').textContent = data.tersedia;

                    const progressBar = document.querySelector('.bg-rose-500');
                    const progressText = document.querySelector('.text-sm.text-slate-500.font-medium.progress-text');

                    if (progressBar && progressText && data.totalPersonelnoKomandan > 0) {
                        const percentage = (data.terkonfirmasi / data.totalPersonelnoKomandan) * 100;
                        progressBar.style.width = percentage + '%';
                        progressText.textContent = `${data.terkonfirmasi}/${data.totalPersonelnoKomandan} Personel Terkonfirmasi (${Math.round(percentage)}%)`;
                    }

                    const tbody = document.querySelector('tbody.divide-y');
                    if (tbody && data.personels && data.personels.length > 0) {
                        tbody.innerHTML = data.personels.map(personel => {
                            const statusClass = personel.status === 'Terkonfirmasi'
                                ? 'text-emerald-600 bg-emerald-100'
                                : personel.status === 'Siaga'
                                ? 'text-orange-600 bg-orange-100'
                                : 'text-blue-600 bg-blue-100';

                            return `
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900">${personel.name}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">${personel.rank}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">${personel.nrp}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">${personel.position}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full ${statusClass}">
                                            ${personel.status}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    }

                    console.log('Dashboard updated:', data);
                })
                .catch(error => console.error('Error updating stats:', error));
        }

        setInterval(updateDashboardStats, 10000);

        // Listen for real-time updates from Firebase
        window.addEventListener('siaga-notification', (event) => {
            console.log('Real-time alert received, updating stats...', event.detail);
            updateDashboardStats();
             if (event.detail.notification) {
             }
        });
    </script>
@endsection
