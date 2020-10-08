addEventListener('install', event => {
  caches.open('v1').then(function(cache) {
      return cache.addAll([
        '/',
		'/sw.js',
        '/index.php',
        '/css/gwn.css',
        '/js/liste.js',
		'/liste/',
        '/liste/index.php',
		'/add/',
        '/liste/index.php',
        '/images/background.jpg',
        '/images/neu.png',
        '/images/fussball-nottuln-logo.png',
		'/images/select-arrow.svg'		
      ]);
    })
});

addEventListener('notificationclick', function(event) {
  event.notification.close();
});

/*self.addEventListener('fetch', function(event) {
  // event.respondWith(fetch(event.request));
  // or simply don't call event.respondWith, which
  // will result in default browser behaviour
});*/

addEventListener('fetch', event => {
  // Let the browser do its default thing
  // for non-GET requests.
  if (event.request.method != 'GET') return;

  // Prevent the default, and handle the request ourselves.
  event.respondWith(async function() {
    // Try to get the response from a cache.
    const cache = await caches.open('dynamic-v1');
    const cachedResponse = await cache.match(event.request);

    if (cachedResponse) {
      // If we found a match in the cache, return it, but also
      // update the entry in the cache in the background.
      event.waitUntil(cache.add(event.request));
      return cachedResponse;
    }

    // If we didn't find a match in the cache, use the network.
    return fetch(event.request);
  }());
});

self.addEventListener('activate', function(event) {
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.filter(function(cacheName) {
          // Return true if you want to remove this cache,
          // but remember that caches are shared across
          // the whole origin
        }).map(function(cacheName) {
          return caches.delete(cacheName);
        })
      );
    })
  );
});