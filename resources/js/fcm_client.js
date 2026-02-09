import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

// Your web app's Firebase configuration (from backend .env or Vite env)
function getFirebaseConfig() {
    const injected = typeof window !== 'undefined' && window.__FIREBASE_CONFIG__;
    if (injected) {
        return {
            apiKey: window.__FIREBASE_CONFIG__.apiKey,
            authDomain: window.__FIREBASE_CONFIG__.authDomain || '',
            projectId: window.__FIREBASE_CONFIG__.projectId,
            storageBucket: window.__FIREBASE_CONFIG__.storageBucket || '',
            messagingSenderId: window.__FIREBASE_CONFIG__.messagingSenderId,
            appId: window.__FIREBASE_CONFIG__.appId,
        };
    }
    return {
        apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
        authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
        projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
        storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
        messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
        appId: import.meta.env.VITE_FIREBASE_APP_ID,
    };
}

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
    const res = await fetch('/fcm/token', {
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
    return res;
}

function getDeviceData() {
    return {
        permission: 'granted',
        fcm_token: null,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || null,
        language: navigator.language || navigator.userLanguage || null,
        screen_resolution: `${window.screen.width}x${window.screen.height}`,
        viewport_size: `${window.innerWidth}x${window.innerHeight}`,
        referrer: document.referrer || null,
    };
}

export async function postSavePermission(token, permission = 'granted') {
    const data = { ...getDeviceData(), permission, fcm_token: token };
    await fetch('/save-notification-permission', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(data),
    });
}

export async function enablePushNotifications() {
    if (!('Notification' in window)) throw new Error('Notifications not supported');
    if (!('serviceWorker' in navigator)) throw new Error('Service worker not supported');
    if (!isSecureContextForPush()) throw new Error('Push needs HTTPS (or localhost)');

    const firebaseConfig = getFirebaseConfig();
    const vapidKey =
        (typeof window !== 'undefined' && window.__FIREBASE_CONFIG__?.vapidKey) ||
        import.meta.env.VITE_FIREBASE_VAPID_KEY;

    if (!firebaseConfig.apiKey || !firebaseConfig.projectId || !firebaseConfig.messagingSenderId || !firebaseConfig.appId) {
        throw new Error('Firebase config missing (set FIREBASE_* or FCM_* in .env; layout injects from backend)');
    }
    if (!vapidKey) throw new Error('VAPID key missing (set FIREBASE_VAPID_KEY or FCM_VAPID_KEY in .env)');

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') throw new Error('Permission denied');

    const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

    // Initialize Firebase (same shape as Firebase console snippet)
    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    const token = await getToken(messaging, { vapidKey, serviceWorkerRegistration: reg });
    if (!token) throw new Error('No token received');

    const tokenRes = await postToken(token);
    if (tokenRes.status === 401) {
        await postSavePermission(token, 'granted');
    } else {
        await postSavePermission(token, 'granted');
    }

    // Foreground messages (optional)
    onMessage(messaging, (payload) => {
        try {
            const title = payload?.notification?.title || 'Notification';
            const body = payload?.notification?.body || '';
            const opts = { body };
            if (payload?.notification?.image) opts.image = payload.notification.image;
            if (payload?.data?.url) opts.data = { url: payload.data.url };
            // eslint-disable-next-line no-new
            const n = new Notification(title, opts);
            if (payload?.data?.url) n.onclick = () => window.open(payload.data.url, '_blank');
        } catch (e) {
            // no-op
        }
    });

    return { ok: true, token };
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

