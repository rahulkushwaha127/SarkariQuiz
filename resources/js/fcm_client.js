import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function getOrCreateDeviceId() {
    const key = 'device_id_v1';
    const existing = window.localStorage.getItem(key);
    if (existing && existing.length > 10) return existing;
    const id = `web_${crypto.randomUUID()}`;
    window.localStorage.setItem(key, id);
    return id;
}

function isSecureContextForPush() {
    if (window.isSecureContext) return true;
    const host = window.location.hostname;
    return host === 'localhost' || host === '127.0.0.1';
}

async function postToken(token) {
    await fetch('/fcm/token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            token,
            platform: 'web',
            device_id: getOrCreateDeviceId(),
        }),
    });
}

export async function enablePushNotifications() {
    if (!('Notification' in window)) throw new Error('Notifications not supported');
    if (!('serviceWorker' in navigator)) throw new Error('Service worker not supported');
    if (!isSecureContextForPush()) throw new Error('Push needs HTTPS (or localhost)');

    const firebaseConfig = {
        apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
        authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
        projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
        storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
        messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
        appId: import.meta.env.VITE_FIREBASE_APP_ID,
    };
    const vapidKey = import.meta.env.VITE_FIREBASE_VAPID_KEY;

    if (!firebaseConfig.apiKey || !firebaseConfig.projectId || !firebaseConfig.messagingSenderId || !firebaseConfig.appId) {
        throw new Error('Firebase env missing');
    }
    if (!vapidKey) throw new Error('VAPID key missing');

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') throw new Error('Permission denied');

    const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    const token = await getToken(messaging, { vapidKey, serviceWorkerRegistration: reg });
    if (!token) throw new Error('No token received');

    await postToken(token);

    // Foreground messages (optional)
    onMessage(messaging, (payload) => {
        // We keep it minimal: show browser notification only if permission granted.
        try {
            const title = payload?.notification?.title || 'Notification';
            const body = payload?.notification?.body || '';
            // eslint-disable-next-line no-new
            new Notification(title, { body });
        } catch (e) {
            // no-op
        }
    });

    return { ok: true };
}

export function wirePushEnableButtons() {
    document.addEventListener('click', async (e) => {
        const target = e.target instanceof HTMLElement ? e.target : null;
        if (!target) return;

        const btn = target.closest('[data-enable-push="true"]');
        if (!btn) return;
        e.preventDefault();

        try {
            await enablePushNotifications();
            window.show_toaster?.('Success', 'Push notifications enabled.', 'success');
            window.location.reload();
        } catch (err) {
            window.show_toaster?.('Error', err?.message || 'Failed to enable notifications', 'error');
        }
    });
}

