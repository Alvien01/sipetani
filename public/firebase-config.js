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
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// Export for use in other scripts
self.firebaseMessaging = messaging;
