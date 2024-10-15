const DEBUG = true;

// Меняем версию файла, когда меняем service worker
const serviceWorkerVer = "/sw.js?v=1";

var url     = window.location.origin,
    fullUrl = url + serviceWorkerVer;
var flag    = false;

+function installServiceWorker() {
    if ("serviceWorker" in navigator) {
        window.addEventListener('load', () => {
            if (navigator.serviceWorker.controller !== null) {
                DEBUG && console.log("[SW] Текущая версия в браузере " + navigator.serviceWorker.controller.scriptURL);
                DEBUG && console.log("[SW] Новая версия " + fullUrl);
            }

            if (navigator.serviceWorker.controller === null) {
                registrationServiceWorker(navigator.serviceWorker);

            } else if (
                navigator.serviceWorker.controller.scriptURL !== fullUrl) {

                removeServiceWorker(navigator.serviceWorker);

                removeCache('workbox-precache-v2-' + url + '/');
                removeCache('workbox-precache-' + url + '/');
                removeCache('workbox-precache-' + url + '/-temp');
                removeCache('workbox-precache');
                removeCache('images_cache');
                removeCache('descriptions_cache');
                removeCache('design_cache');
                removeCache('app_cache');
                removeCache('assets');
                removeCache('main');
                removeCache('other');

            } else {
                DEBUG && console.log("[SW] Активный service worker последней версии найден, повторно не регистрируем");
            }
        });
    }

}();

function enableNotif() {
    if (atob($('#vapid_pub').val()) != '') {
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                // get service worker
                navigator.serviceWorker.ready.then((sw) => {
                    //subscribe
                    sw.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: atob($('#vapid_pub').val())
                    }).then((subscription) => {
                        let cur_date = new Date();
                        let time_zone = -cur_date.getTimezoneOffset() / 60;
                        let shop_url = location.host;
                        let push_info = JSON.stringify(subscription);

                        let info = JSON.parse(push_info);
                        let date_coockie = new Date;
                        date_coockie.setDate(date_coockie.getDate() + 900);
                        date_coockie = date_coockie.toUTCString();
                        document.cookie = 'user_push=' + info['keys']['auth'] + '; path=/; expires=' + date_coockie;

                        let lang_lang = $('#lang_select :selected').attr('data-code').trim();
                        let curr_curr = $('#curr_select :selected').text().trim();

                        let user_agent = window.navigator.userAgent;

                        let format_date, year, month, day, hours, minutes, seconds = '';

                        day = cur_date.getDate() < 10 ? '0' + cur_date.getDate() : cur_date.getDate();
                        month = cur_date.getMonth() < 10 ? '0' + cur_date.getMonth() : cur_date.getMonth();
                        year = cur_date.getFullYear();
                        hours = cur_date.getHours() < 10 ? '0' + cur_date.getHours() : cur_date.getHours();
                        minutes = cur_date.getMinutes() < 10 ? '0' + cur_date.getMinutes() : cur_date.getMinutes();
                        seconds = cur_date.getSeconds() < 10 ? '0' + cur_date.getSeconds() : cur_date.getSeconds();

                        format_date = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

                        $.ajax({
                            url: '/push/save_push',
                            type: "POST",
                            data: {
                                'shop_url': shop_url,
                                'lang': $('#lang_session').val() ? $('#lang_session').val() : lang_lang,
                                'curr': curr_curr,
                                'push_info': push_info,
                                'date': format_date,
                                'time_zone': time_zone,
                                'customer_id': 0,
                            },
                            dataType: "json",
                            success: function (res) {
                                if (res['status'] == 'error') {
                                    alert(res['text']);
                                }
                            }
                        });
                    });
                });
            }
        });
    }
}


// const inactive = async () => {
//     try {
//         const applicationServerKey = urlB64ToUint8Array(
//           'BF2l1EXb1uc7B6lot1tQSQDlpptSc39yzE1lckz2i6IsunhRQt1RKqA4Lw6cKlaCTr1xhJtFhZ0zLgzZI3rvfJo'
//         )
//         const options = {applicationServerKey, userVisibleOnly: true }
//         console.log(reg.pushManager);
//       const subscription = await reg.pushManager.subscribe();
//         const response = await saveSubscription(subscription)
//         console.log(JSON.stringify(subscription))
//         console.log(response)
//       } catch (err) {
//         console.log('Error', err)
//       }
// }
/*
const requestNotificationPermission = async () => {
    const permission = await window.Notification.requestPermission();
        if(permission !== 'granted')
            throw new Error('Permission not granted for Notification');
        if(permission === 'granted') {
    // localStorage.setItem('permission', 'granted');
    // console.log('Permission was granted for Notification');
            isPushEnabled = true;
        }
}
*/
// const urlB64ToUint8Array = (base64String) => {
//     const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
//     const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/')
//     const rawData = atob(base64)
//     const outputArray = new Uint8Array(rawData.length)
//     for (let i = 0; i < rawData.length; ++i) {
//       outputArray[i] = rawData.charCodeAt(i)
//     }
//     return outputArray
//   }

//   const saveSubscription = async (subscription) => {
//     const SERVER_URL = '//true-pills.com/save-subscription'
//     const response = await fetch(SERVER_URL, {
//       method: 'post',
//       headers: {
//         'Content-Type': 'application/json',
//       },
//       body: JSON.stringify(subscription),
//     })
//     return response.json()
//   }

//   self.addEventListener('activate', async e => {
//     console.log(e.type)
//     try {
//       const applicationServerKey = urlB64ToUint8Array(
//         'BF2l1EXb1uc7B6lot1tQSQDlpptSc39yzE1lckz2i6IsunhRQt1RKqA4Lw6cKlaCTr1xhJtFhZ0zLgzZI3rvfJo'
//       )
//       const options = {applicationServerKey, userVisibleOnly: true }
//       console.log(registrationServiceWorker.pushManager);
//     const subscription = await reg.pushManager.subscribe();
//     //   const subscription = await registrationServiceWorker.pushManager.subscribe(options)
//       const response = await saveSubscription(subscription)
//       console.log(JSON.stringify(subscription))
//       console.log(response)
//     } catch (err) {
//       console.log('Error', err)
//     }
//   })

//   self.addEventListener('push', e => {
//     navigator.serviceWorker.ready.then((registeration) => {
//         showLocalNotification('NOTIFICATION', 'Our first notification!', 'test', registeration);
//       });
//     if (e.data) {
//       console.log('Push event!! ', e.data.text())
//     } else {
//       console.log('Push event but no data')
//     }
//   })

//   const showLocalNotification = (title, body, tag, registeration) => {
//     const options = {
//         body,
//         tag,
//         icon: '../android-chrome-512x512.png',
//         vibrate: [200, 100, 200],

//         // here you can add more properties like icon, image, vibrate, etc.
//     };
//     navigator.serviceWorker.ready.then(() => {
//       registeration.showNotification(title, options);
//     });
//   }

//   window.addEventListener('load', async () => {
      async function registerPeriodicSync() {
        const reg = await navigator.serviceWorker.ready;
        await reg.periodicSync.serviceWorker('get-daily-news', {
            minInterval: 24 * 60 * 60 * 1000
        });
    }
    async function periodicReg() {
    const statu = await navigator.permissions.query({name: 'periodic-background-sync'});
    if (statu.state === 'granted' && !flag) {
      console.log("periodic-background-sync was register")
      flag = true;
      registerPeriodicSync();
    }
}


    //   self.addEventListener('periodicsync', event => {
    //     if (event.tag === 'get-daily-news') {
    //         event.waitUntil(getDailyNewsInCache());
    //     }
    //   });

    //   self.addEventListener('fetch', async function (event) {

    //     event.respondWith(
    //       caches.match(event.request).then(function (response) {
    //         return response || fetch(event.request);
    //       }),
    //     );
    //   });
    async function installed() {
        var pwa = false;
        if("getInstalledRelatedApps" in navigator) {
            var relatedApps = await navigator.getInstalledRelatedApps();
            if (relatedApps) {
                for (const app of relatedApps) {
                    document.cookie = "app_platform=" + app.platform + "app_url=" + app.url;
                    var app_info = "&app_platform=" + app.platform + "&app_url=" + app.url;
                    document.getElementById("stat").src += app_info;
                    pwa = true;
                }
            }
        }
        return pwa;
    }

    async function beforeInstall() {

      btnInstall = document.querySelector('#install__button');
      install = document.querySelector('#install');

      window.addEventListener('beforeinstallprompt', event => {
        // console.log('beforeinstallprompt event was fired');
        installPromptEvent = event;
      });

      if (btnInstall) {
        btnInstall.addEventListener('click', () => {
          installPromptEvent.prompt();
          installPromptEvent.userChoice.then(choice => {
            if (choice.outcome === 'accepted') {
              // console.log('User accepted the A2HS prompt');
              install.style.display = "none";
            } else {
              // console.log('User dismissed the A2HS prompt');
            }
            const result = installPromptEvent.userChoice;
            installPromptEvent = null;
          });

        });
      }

      // var inst = false;
      window.addEventListener('appinstalled', (event) => {
        installPromptEvent = null;
        console.log('PWA was installed');
        document.cookie = "pwa_installed=true; max-age=31536000";

      });
    }



    // });


// Регистрация Service Worker
function registrationServiceWorker(navigatorServiceWorker) {
    navigatorServiceWorker
        .register(serviceWorkerVer)
        .then(reg => {
            flag = false;
            DEBUG && console.log(`[SW] Зарегистрирован service worker для адреса (scope):  ${reg.scope}`);
            // console.log(reg.pushManager.subscribe);
            // return reg;
        })
        .catch(err => {
            DEBUG && console.log(`[SW] При регистрации service worker произошла ошибка: ${err}`);
            // return err;
        });
}

// Удаления service worker
function removeServiceWorker(navigatorServiceWorker) {
    navigatorServiceWorker.getRegistrations()
        .then(function (registrations) {
                for (var registration of registrations) {
                    registration.unregister();
                    DEBUG && console.log("[SW] Предыдущая версия service worker была успешно удалена");
                    DEBUG && console.log("[SW] ! Для установки новой версии перезагрузите страницу");
                }
            }
        );
}

// Очистка кэша
function removeCache(cache) {
    caches.delete(cache).then(function (boolean) {
        DEBUG && console.log("[SW] Кэш " + cache + " очищен");
    });
}

const main = async () => {
    const permission =  await requestNotificationPermission();
    periodicReg();
    const pwa = await installed();
    if (!pwa)
        beforeInstall();
}

//main();