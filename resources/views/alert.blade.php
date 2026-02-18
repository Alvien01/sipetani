@extends('layouts.pwa')

@section('content')
    <div id="alert-container"
        class="min-h-screen bg-gradient-to-br from-green-700 via-green-800 to-green-900 flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <!-- Alert Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all hover:scale-105">
                <!-- Header with Animation -->
                <div class="bg-gradient-to-r from-green-700 to-green-800 p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>

                    <div class="relative z-10 flex items-center justify-center mb-4">
                        <div class="bg-white/20 p-4 rounded-full animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                            </svg>
                        </div>
                    </div>

                    @if ($alert)
                        <h1 class="text-3xl font-bold text-white text-center mb-2">SIAGA TK {{ $alert->level }} AKTIF</h1>
                        <p class="text-green-100 text-center text-sm">Dimulai:
                            {{ $alert->started_at->format('d M Y, H:i') }}</p>
                    @else
                        <h1 class="text-3xl font-bold text-white text-center mb-2">TIDAK ADA SIAGA</h1>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-8">
                    @if ($alert)
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-slate-800 mb-3">{{ $alert->title }}</h2>
                            <p class="text-slate-600 leading-relaxed">{{ $alert->message }}</p>
                        </div>

                        <!-- Hadir Button -->
                        <form action="{{ route('alert.attend') }}" method="POST" id="attendForm">
                            @csrf
                            @php
                                $isConfirmed = isset($alreadyAttended) ? $alreadyAttended : (Auth::guard('personel')->user()->status === 'Terkonfirmasi');
                            @endphp
                            <button type="submit"
                                @if($isConfirmed) disabled @endif
                                class="w-full font-bold py-4 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2
                                    {{ $isConfirmed
                                        ? 'bg-slate-400 text-white cursor-not-allowed opacity-60'
                                        : 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white hover:from-emerald-600 hover:to-emerald-700 transform hover:scale-105 active:scale-95' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $isConfirmed ? 'SUDAH TERKONFIRMASI' : 'HADIR' }}</span>
                            </button>
                        </form>

                        <p class="text-center text-slate-400 text-sm mt-4">
                            {{ $isConfirmed ? 'Kehadiran Anda sudah tercatat' : 'Klik tombol untuk konfirmasi kehadiran' }}
                        </p>
                    @else
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-slate-300 mx-auto mb-4"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-slate-500 text-lg">Tidak ada siaga aktif saat ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Footer -->
            <div class="mt-6 text-center">
                <p class="text-white/80 text-sm">SIAGA APP - Sistem Peringatan Dini</p>
            </div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>

    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyD3-_HRKwkyedciqw3gSQgU-h7dEbXasx8",
            authDomain: "poltekadsiaga.firebaseapp.com",
            projectId: "poltekadsiaga",
            storageBucket: "poltekadsiaga.firebasestorage.app",
            messagingSenderId: "971088041031",
            appId: "1:971088041031:web:9268e5176456d0b08a4d62"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        async function requestNotificationPermission() {
            try {
                const permission = await Notification.requestPermission();

                if (permission === 'granted') {
                    console.log('Notification permission granted');

                    const token = await messaging.getToken({
                        vapidKey: 'BCiIqerZjZ4WnuFWD4pXwMc4RxUlpu0n-RKqrX0eZzMZDWQl8WXd0jeakA9-A4kcxki5jZ9CU0DgjlqmMiLPMQE' // Get from Firebase Console
                    });

                    if (token) {
                        console.log('FCM Token:', token);

                        await fetch('{{ route('fcm.subscribe') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                token: token
                            })
                        });

                        console.log('Token sent to server');
                    }
                } else {
                    console.log('Notification permission denied');
                }
            } catch (error) {
                console.error('Error getting notification permission:', error);
            }
        }

        messaging.onMessage((payload) => {
            console.log('Foreground message received:', payload);

            const notificationTitle = payload.notification?.title || 'SIAGA ALERT';
            const notificationOptions = {
                body: payload.notification?.body || 'Ada panggilan siaga baru',
                icon: '/icons/icon-192x192.svg',
                badge: '/icons/icon-192x192.svg',
                vibrate: [200, 100, 200, 100, 200, 100, 200],
                tag: 'siaga-notification',
                requireInteraction: true
            };

            // Show notification
            if (Notification.permission === 'granted') {
                new Notification(notificationTitle, notificationOptions);
            }

            // Play sound
            try {
                const audio = new Audio('/sounds/alarm_sound.mp3');
                audio.volume = 1.0;
                audio.play()
                    .then(() => console.log('Sound played'))
                    .catch(e => console.log('Sound error:', e));
            } catch (e) {
                console.log('Audio error:', e);
            }

            // Refresh content via AJAX to avoid full page reload
            console.log('Refreshing alert content...');
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('alert-container');
                    const currentContent = document.getElementById('alert-container');

                    if (newContent && currentContent) {
                        currentContent.innerHTML = newContent.innerHTML;
                        // Re-attach event listeners if needed (e.g., for the form)
                        attachFormListener();
                        console.log('Alert content updated successfully.');
                    } else {
                         // Fallback if containers mismatch
                         window.location.reload();
                    }
                })
                .catch(err => {
                    console.error('Failed to update content:', err);
                    window.location.reload(); // Fallback
                });
        });

        if ('Notification' in window && Notification.permission === 'default') {
            requestNotificationPermission();
        } else if (Notification.permission === 'granted') {
            requestNotificationPermission();
        }

        function attachFormListener() {
            document.getElementById('attendForm')?.addEventListener('submit', function(e) {
                const button = this.querySelector('button');
                button.innerHTML =
                    '<svg class="animate-spin h-6 w-6 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                button.disabled = true;
            });
        }

        // Attach initial listener
        attachFormListener();
    </script>
@endsection
