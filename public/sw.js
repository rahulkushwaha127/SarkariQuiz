// Minimal PWA service worker (required for install prompt).
self.addEventListener('install', function (event) {
  self.skipWaiting();
});
self.addEventListener('activate', function (event) {
  event.waitUntil(self.clients.claim());
});
self.addEventListener('fetch', function () {
  // Pass-through; no caching required for installability.
});
