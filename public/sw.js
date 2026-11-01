/**
 * Apttitudee Service Worker - PWA & Offline Engine
 * Strictly caches static assets (CSS, JS, fonts, images).
 * NEVER caches dynamic PHP pages, per-user state, auth endpoints, or api_* actions.
 */

const CACHE_NAME = 'apttitudee-static-v3-5-3';
const OFFLINE_URL = 'offline.html';

const STATIC_PRECACHE = [
  'offline.html',
  'css/theme.css',
  'css/style.css',
  'js/app.js',
  'js/quiz_shell.js',
  'manifest.json',
  'assets/icons/icon-192.png',
  'assets/icons/icon-512.png',
  'assets/og-preview.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(STATIC_PRECACHE);
    }).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  const req = event.request;
  const url = new URL(req.url);

  // 1. Strictly ignore non-GET requests
  if (req.method !== 'GET') {
    return;
  }

  // 2. Strictly bypass service worker for all dynamic endpoints, APIs, auth, and user routes
  const isApiRequest = url.search.includes('action=api_') || url.pathname.includes('/api_') || url.pathname.includes('/api/');
  const isAuthRequest = url.search.includes('action=login') || url.search.includes('action=register') || url.search.includes('action=logout') || url.search.includes('dev_login');
  const isPerUserPage = url.search.includes('action=dashboard') || url.search.includes('action=profile') || url.search.includes('action=store') || url.search.includes('action=admin') || url.search.includes('action=social') || url.search.includes('action=duel') || url.search.includes('action=trophy_room');

  if (isApiRequest || isAuthRequest || isPerUserPage) {
    // Direct network pass-through; never touch cache
    return;
  }

  // 3. Navigation (HTML pages): Network-First, fallback to offline.html ONLY if network fails
  // Crucially: never store dynamic HTML in cache so logged-out users never see cached logged-in pages
  if (req.mode === 'navigate') {
    event.respondWith(
      fetch(req).catch(() => {
        return caches.match(OFFLINE_URL).then(res => {
          return res || new Response(
            `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Offline | Apttitudee</title></head><body><h1>You are offline</h1><p>Please check your connection.</p></body></html>`,
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
          );
        });
      })
    );
    return;
  }

  // 4. Static Assets ONLY (CSS, JS, Fonts, Images): Cache-First with Network Revalidation
  const isStaticAsset = (
    url.pathname.endsWith('.css') ||
    url.pathname.endsWith('.js') ||
    url.pathname.endsWith('.png') ||
    url.pathname.endsWith('.jpg') ||
    url.pathname.endsWith('.svg') ||
    url.pathname.endsWith('.ico') ||
    url.pathname.endsWith('.woff2') ||
    url.hostname.includes('fonts.googleapis.com') ||
    url.hostname.includes('fonts.gstatic.com') ||
    url.hostname.includes('cdn.jsdelivr.net')
  );

  if (isStaticAsset) {
    event.respondWith(
      caches.open(CACHE_NAME).then(cache => {
        return cache.match(req).then(cached => {
          const fetchPromise = fetch(req).then(networkRes => {
            if (networkRes && networkRes.status === 200 && networkRes.type !== 'opaque') {
              cache.put(req, networkRes.clone());
            }
            return networkRes;
          }).catch(() => cached);

          return cached || fetchPromise;
        });
      })
    );
    return;
  }

  // 5. Default: Network pass-through
  event.respondWith(fetch(req));
});
