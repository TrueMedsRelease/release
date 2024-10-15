// Workbox from google
var workbox = function() {
  "use strict";
  try {
    self.workbox.v["workbox:sw:6.5.4"] = 1
  }
  catch(t) { }
  const t="https://storage.googleapis.com/workbox-cdn/releases/6.5.4",
  e = {
    backgroundSync: "background-sync",
    broadcastUpdate: "broadcast-cache-update",
    cacheableResponse: "cacheable-response",
    core: "core",
    expiration: "expiration",
    googleAnalytics: "google-analytics",
    navigationPreload: "navigation-preload",
    precaching: "precaching",
    rangeRequests: "range-requests",
    routing: "routing",
    strategies: "strategies",
    streams: "streams"
  };

  return new class {
    constructor() {
      return this.v = { },
      this.t = {
        debug: "localhost" === self.location.hostname,
        modulePathPrefix: null,
        modulePathCb: null
      },
      this.e = this.t.debug ? "dev" : "prod",
      this.s = !1,
      new Proxy(this, {
        get(t, s) {
          if(t[s])
            return t[s];
          const o = e[s];
          return o&&t.loadModule(`workbox-${o}`), t[s]
        }
      })
    }

    setConfig(t = { }) {
      if(this.s)
        throw new Error("Config must be set before accessing workbox.* modules");
      Object.assign(this.t,t), this.e = this.t.debug ? "dev" : "prod"
    }

    skipWaiting() {
      self.addEventListener("install", () => self.skipWaiting())
    }

    clientsClaim() {
      self.addEventListener("activate", () => self.clients.claim())
    }

    loadModule(t) {
      const e = this.o(t);
      try {
        importScripts(e), this.s = !0
      }
      catch(s){
        throw console.error(`Unable to import module '${t}' from '${e}'.`), s
      }
    }

    o(e) {
      if(this.t.modulePathCb)
        return this.t.modulePathCb(e, this.t.debug);
      let s = [t];
      const o = `${e}.${this.e}.js`, r = this.t.modulePathPrefix;
      return r && "" === (s = r.split("/"))[s.length-1] && s.splice(s.length-1, 1), s.push(o), s.join("/")
    }
  }
}();

if (workbox) {
  const versionPrecache = 1.0;
  workbox.precaching.precacheAndRoute([
    {
        "url": "/public/js/all_js.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_1/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_1/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_1/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_1/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_2/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_2/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_2/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_2/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_3/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_3/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_3/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_3/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_4/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_4/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_4/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_4/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_5/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_5/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_5/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_5/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_5/js/main.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_6/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_6/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_6/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_6/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_6/js/main.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_7/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_7/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_7/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_7/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_7/js/main.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_8/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_8/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_8/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_8/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_8/js/main.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_9/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_9/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_9/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_9/js/app.js",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_10/layouts/main.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/resources/views/design_10/index.blade.php",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_10/css/style.css",
        "revision": versionPrecache
    },
    {
        "url": "/public/design_10/js/app.js",
        "revision": versionPrecache
    },
  ]);
  workbox.routing.registerRoute(
    /(.*)\.(?:png|webp|svg|jpg)(.*)/,
    new workbox.strategies.CacheFirst({
      cacheName: 'images_cache',
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxEntries: 1000,
          maxAgeSeconds: 60 * 60 * 24 * 7,
          purgeOnQuotaError: true
        })
      ]
    })
  );
  workbox.routing.registerRoute(
    /(.*)\.(?:html)(.*)|common.conf/,
    new workbox.strategies.CacheFirst({
      cacheName: 'descriptions_cache',
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxAgeSeconds: 60 * 60 * 24 * 7,
          purgeOnQuotaError: true
        })
      ]
    })
  );
  workbox.routing.registerRoute(
    /\b(?:\S(?!(.*)cart|cart_content))+\.(?:blade.php)+\b/,
    new workbox.strategies.CacheFirst({
      cacheName: 'design_cache',
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxEntries: 300,
          maxAgeSeconds: 60 * 60 * 24 * 7,
          purgeOnQuotaError: true
        })
      ]
    })
  );
  workbox.routing.registerRoute(
    /\b(?:\S(?!(.*)))+\.(?:php)+\b/,
    new workbox.strategies.CacheFirst({
      cacheName: 'app_cache',
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxEntries: 300,
          maxAgeSeconds: 60 * 60 * 24 * 7,
          purgeOnQuotaError: true
        })
      ]
    })
  );
  workbox.routing.registerRoute(
    /(.*)(cart|cart_content|checkout|checkout_content|complete)\.(?:blade.php|php)/,
    new workbox.strategies.NetworkFirst()
  );
  workbox.routing.registerRoute(
    ({ request }) =>
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'worker',
    new workbox.strategies.StaleWhileRevalidate({
      // помещаем файлы в кеш с названием 'assets'
      cacheName: 'assets',
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxAgeSeconds: 60 * 60 * 24 * 7,
          purgeOnQuotaError: true
        })
      ]
    })
  );

  /*const urlB64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/')
    const rawData = atob(base64)
    const outputArray = new Uint8Array(rawData.length)
    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i)
    }
    return outputArray
  }

  const saveSubscription = async (subscription) => {
    // const SERVER_URL1 = 'https://pharm-discount.com/save-subscriptionconsole.warn(xhr.responseText)'
    // const response = await fetch(SERVER_URL1, {
      // method: 'post',
      // headers: {
        // 'Content-Type': 'application/json',
      // },
      // body: JSON.stringify(subscription),
    // })
    // return response.json()
  }

  const showLocalNotification = (title, body, tag, registeration) => {
    const options = {
        body,
        tag,
        icon: '../android-chrome-512x512.png',
        vibrate: [200, 100, 200],

        // here you can add more properties like icon, image, vibrate, etc.
    };
    navigator.serviceWorker.ready.then(() => {
      registeration.showNotification(title, options);
    });
  }

  self.addEventListener("activate", async e => {
    console.log(e.type)
    try {
      const applicationServerKey = urlB64ToUint8Array(
        'BF2l1EXb1uc7B6lot1tQSQDlpptSc39yzE1lckz2i6IsunhRQt1RKqA4Lw6cKlaCTr1xhJtFhZ0zLgzZI3rvfJo'
      )
      const options = { applicationServerKey, userVisibleOnly: true }
      const subscription = await self.registration.pushManager.subscribe(options)
      const response = await saveSubscription(subscription)
      console.log(JSON.stringify(response))
    } catch (err) {
      console.log('Error', err)
    }
  })

  self.addEventListener('push', e => {
    navigator.serviceWorker.ready.then((registeration) => {
        showLocalNotification('NOTIFICATION', 'Our first notification!', 'test', registeration);
      });
    if (e.data) {
      console.log('Push event!! ', e.data.text())
    } else {
      console.log('Push event but no data')
    }
  })

  self.addEventListener('periodicsync', event => {
    if (event.tag === 'get-daily-news') {
        event.waitUntil(getDailyNewsInCache());
    }
  });

  self.addEventListener('fetch', async function (event) {
    event.respondWith(
      caches.match(event.request).then(function (response) {
        return response || fetch(event.request);
      }),
    );
  });*/

  self.addEventListener("push", (event) => {
    const notification = event.data.json();
    event.waitUntil(self.registration.showNotification(notification.title, {
      body: notification.body,
      icon: notification.img, //'apple-touch-icon.png',
      data: {
        notifURL: notification.url
      }
    }));
  });

  self.addEventListener("notificationclick", (event) => {
    event.waitUntil(clients.openWindow(event.notification.data.notifURL));
  });
}