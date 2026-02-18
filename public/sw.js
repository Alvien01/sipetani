"use strict";

// Import Firebase scripts for push notifications
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Firebase Configuration
const firebaseConfig = {
    apiKey: "AIzaSyD3-_HRKwkyedciqw3gSQgU-h7dEbXasx8",
    authDomain: "poltekadsiaga.firebaseapp.com",
    projectId: "poltekadsiaga",
    storageBucket: "poltekadsiaga.firebasestorage.app",
    messagingSenderId: "971088041031",
    appId: "1:971088041031:web:9268e5176456d0b08a4d62"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Cache configuration
const CACHE_NAME = "offline-cache-v1";
const OFFLINE_URL = '/offline.html';

const filesToCache = [
    OFFLINE_URL
];

// Install event
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(filesToCache))
    );
    self.skipWaiting();
});

// Fetch event
self.addEventListener("fetch", (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match(OFFLINE_URL);
                })
        );
    } else {
        event.respondWith(
            caches.match(event.request)
                .then((response) => {
                    return response || fetch(event.request);
                })
        );
    }
});

// Activate event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Handle background push notifications
messaging.onBackgroundMessage((payload) => {
    console.log('[SW] Received background message:', payload);

    const notificationTitle = payload.notification?.title || 'SIAGA ALERT';
    const notificationOptions = {
        body: payload.notification?.body || 'Ada panggilan siaga baru',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-192x192.png',
        vibrate: [200, 100, 200, 100, 200, 100, 200],
        tag: 'siaga-notification',
        requireInteraction: true,
        sound: '/sounds/alarm_sound.mp3',
        data: {
            url: '/alert',
            ...payload.data
        },
        actions: [
            {
                action: 'open',
                title: 'Buka'
            },
            {
                action: 'close',
                title: 'Tutup'
            }
        ]
    };

    console.log('[SW] Attempting to play custom sound...');
    try {
        const audio = new Audio('/sounds/alarm_sound.mp3');
        audio.volume = 1.0;
        console.log('[SW] Audio object created, attempting play...');
        audio.play()
            .then(() => console.log('[SW] ✅ Sound played successfully!'))
            .catch(e => console.error('[SW] ❌ Sound play failed:', e));
    } catch (e) {
        console.error('[SW] ❌ Audio creation failed:', e);
    }

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event);

    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    // Open or focus the alert page
    const urlToOpen = event.notification.data?.url || '/alert';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then((windowClients) => {
            // Check if there's already a window open
            for (let client of windowClients) {
                if (client.url.includes(urlToOpen) && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, open a new window
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
