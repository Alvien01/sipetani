<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#4A5D23">
    <title>Siaga App</title>

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.svg') }}">
    <link rel="icon" href="{{ asset('icons/icon-192x192.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased">
    @yield('content')

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-app.js";
        import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyD3-_HRKwkyedciqw3gSQgU-h7dEbXasx8",
            authDomain: "poltekadsiaga.firebaseapp.com",
            projectId: "poltekadsiaga",
            storageBucket: "poltekadsiaga.firebasestorage.app",
            messagingSenderId: "971088041031",
            appId: "1:971088041031:web:9268e5176456d0b08a4d62"
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        async function requestPermission(reg) {
            console.log('Requesting permission...');
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    console.log('Notification permission granted.');

                    const token = await getToken(messaging, {
                        serviceWorkerRegistration: reg
                    });

                    if (token) {
                        console.log('FCM Token:', token);
                        await fetch('{{ route("fcm.subscribe") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ token: token })
                        });
                        console.log('Token sent to server.');
                    } else {
                        console.log('No token available. Using topic subscription instead.');
                    }
                }
            } catch (error) {
                console.log('An error occurred while retrieving token. ', error);
                console.log('Continuing without push token - will use topic subscription.');
            }
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => {
                        console.log('Service worker registered.', reg);
                        requestPermission(reg);
                    })
                    .catch((err) => console.log('Service worker registration failed:', err));
            });
        }
    </script>
</body>
</html>
