var isPushEnabled = false;
window.addEventListener('load', async () => {
    function getPWADisplayMode() {
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
        if (document.referrer.startsWith('android-app://')) {
              return 'twa';
        } else if (navigator.standalone || isStandalone) {
              return 'standalone';
        }
        return 'browser';
    }

    var mode = getPWADisplayMode();
    document.cookie = "theme=" + mode;
    // document.getElementById("stat").src = document.getElementById("stattemp").value + "&theme=" + mode;
    $(document).ready(function(){
        var params = document.getElementById("stattemp").value + "&theme=" + mode;
        // console.log(params);
        $.ajax({
            url: "pwa/pwa_info",
            type: 'POST',
            data: {
              'params': params,
            },
            dataType: 'json',
            success : function(data) {
                // console.log(data);
            },
        });
    });
});
//     let serviceWorker = localStorage.getItem('serviceWorker');
//     let permission = localStorage.getItem('permission');
//     const urlB64ToUint8Array = (base64String) => {
//       const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
//       const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/')
//       const rawData = window.atob(base64)
//       const outputArray = new Uint8Array(rawData.length)
//       for (let i = 0; i < rawData.length; ++i) {
//         outputArray[i] = rawData.charCodeAt(i)
//       }
//       return outputArray
//     }

//     const saveSubscription = async (subscription) => {
//       const SERVER_URL = 'http://true-pills.com/save-subscription'
//       const response = await fetch(SERVER_URL, {
//         method: 'post',
//         headers: {
//           'Content-Type': 'application/json',
//         },
//         body: JSON.stringify(subscription),
//       })
//       return response.json()
//     }


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

//   if(permission === 'granted') {
//     try {
//       const applicationServerKey = urlB64ToUint8Array(
//         'BF2l1EXb1uc7B6lot1tQSQDlpptSc39yzE1lckz2i6IsunhRQt1RKqA4Lw6cKlaCTr1xhJtFhZ0zLgzZI3rvfJo'
//       )
//       const options = {applicationServerKey, userVisibleOnly: true }
//       const subscription = await serviceWorker.registration.pushManager.subscribe(options)
//       const response = await saveSubscription(subscription)
//       console.log(JSON.stringify(subscription))
//       console.log(response)
//     } catch (err) {
//       console.log('Error', err)
//     }
//   }


// self.addEventListener('push', function (event) {
//   navigator.serviceWorker.ready.then((registeration) => {
//     showLocalNotification('NOTIFICATION', 'Our first notification!', 'test', registeration);
//   });
//   if (event.data) {
//     console.log('Push event!! ', event.data.text())

//   } else {
//     console.log('Push event but no data')
//   }
// })



    // var subscriptionButton = document.getElementById('subscriptionButton');

    // navigator.serviceWorker.ready
    //   .then(register => {
    //     console.log('ready');
    //     subscriptionButton.removeAttribute('disabled');
    //     return register.pushManager.getSubscription();
    //   }).then(function(subscription) {
    //     if (subscription) {
    //       console.log('Already subscribed', subscription.endpoint);
    //       setUnsubscribeButton();
    //     } else {
    //       setSubscribeButton();
    //     }
    //   });

    // Get the `registration` from service worker and create a new
// subscription using `registration.pushManager.subscribe`. Then
// register received new subscription by sending a POST request with
// the subscription to the server.
// function subscribe() {
//   navigator.serviceWorker.ready
//   .then(async function(register) {
//     // Get the server's public key
//     const response = await fetch('./vapidPublicKey');
//     const vapidPublicKey = await response.text();
//     // Chrome doesn't accept the base64-encoded (string) vapidPublicKey yet
//     // urlBase64ToUint8Array() is defined in /tools.js
//     const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);
//     // Subscribe the user
//     return register.pushManager.subscribe({
//       userVisibleOnly: true,
//       applicationServerKey: convertedVapidKey
//     });
//   }).then(function(subscription) {
//     console.log('Subscribed', subscription.endpoint);
//     return fetch('register', {
//       method: 'post',
//       headers: {
//         'Content-type': 'application/json'
//       },
//       body: JSON.stringify({
//         subscription: subscription
//       })
//     });
//   }).then(setUnsubscribeButton);
// }

// Get existing subscription from service worker, unsubscribe
// (`subscription.unsubscribe()`) and unregister it in the server with
// a POST request to stop sending push messages to
// unexisting endpoint.
// function unsubscribe() {
//   navigator.serviceWorker.ready
//   .then(function(register) {
//     return register.pushManager.getSubscription();
//   }).then(function(subscription) {
//     return subscription.unsubscribe()
//       .then(function() {
//         console.log('Unsubscribed', subscription.endpoint);
//         return fetch('unregister', {
//           method: 'post',
//           headers: {
//             'Content-type': 'application/json'
//           },
//           body: JSON.stringify({
//             subscription: subscription
//           })
//         });
//       });
//   }).then(setSubscribeButton);
// }

// Change the subscription button's text and action.
// function setSubscribeButton() {
//   subscriptionButton.onclick = subscribe;
//   subscriptionButton.textContent = 'Subscribe!';
// }

// function setUnsubscribeButton() {
//   subscriptionButton.onclick = unsubscribe;
//   subscriptionButton.textContent = 'Unsubscribe!';
// }


    // navigator.serviceWorker.ready.then((serviceWorker) => {
    //   // Do we already have a push message subscription?
    //   console.log("ready");
    //   serviceWorker.pushManager.getSubscription().then(subscription => {
    //     serviceWorker.pushManager.subscribe({
    //       userVisibleOnly: true //Set user to see every notification
    //     }).then(function (subscription) {
    //       toast('Subscribed successfully.');
    //       console.info('Push notification subscribed.');
    //       console.log(subscription);
    //     }).catch(function (error) {
    //       console.error('Push notification subscription error: ', error);
    //     });

    //   })
    //       if (subscription) {
    //         isPushEnabled = true;
    //         return subscription;
    //       }
    //       return serviceWorkerRegistration.pushManager.subscribe({
    //         userVisibleOnly: true
    //       });

    //       // // Keep your server in sync with the latest subscriptionId
    //       // sendSubscriptionToServer(subscription);

    //       // showCurlCommand(subscription);

    //       // isPushEnabled = true;
    //     })
    //     .catch((err) => {
    //       console.error(`Error during getSubscription(): ${err}`);
    //     });





    // self.addEventListener('push', function(event) {
    //   event.waitUntil(self.registration.showNotification('ServiceWorker Cookbook', {
    //     body: 'Push Notification Subscription Management'
    //   }));
    // });

    // self.addEventListener('pushsubscriptionchange', function(event) {
    //   console.log('Subscription expired');
    //   event.waitUntil(
    //     self.registration.pushManager.subscribe({ userVisibleOnly: true })
    //     .then(function(subscription) {
    //       console.log('Subscribed after expiration', subscription.endpoint);
    //       return fetch('register', {
    //         method: 'post',
    //         headers: {
    //           'Content-type': 'application/json'
    //         },
    //         body: JSON.stringify({
    //           endpoint: subscription.endpoint
    //         })
    //       });
    //     })
    //   );
    // });




  //   function isPushSupported() {
  //     //checks if user has granted permission to Push notifications
  //     console.log(Notification.permission);
  //     if (Notification.permission === 'denied') {
  //       console.log('User has blocked push notification.');
  //       return;
  //     }

  //     //Checks if current browser supports Push notification
  //     if (!('PushManager' in window)) {
  //       console.log('Sorry, Push notification isn\'t supported in your browser.');
  //       return;
  //     }

  //     //Get `push notification` subscription id

  //     //If `serviceWorker` is registered and ready
  //     navigator.serviceWorker.ready
  //     .then(function (registration) {
  //       registration.pushManager.getSubscription().then(function() {
  //         subscribePush(registration);
  //         unsubscribePush(registration);
  //       })
  //       .catch(function (error) {
  //         console.error('Error occurred while enabling push ', error);
  //       });
  //     });
  //   }

  //   function subscribePush(registration) {
  //     //Subscribes user to Push notifications
  //     registration.pushManager.subscribe({
  //       userVisibleOnly: true //Set user to see every notification
  //     })
  //     .then(function (subscription) {
  //       toast('Subscribed successfully.');
  //       console.info('Push notification subscribed.');
  //       console.log(subscription);
  //     })
  //     .catch(function (error) {
  //       console.error('Push notification subscription error: ', error);
  //     });
  //   }

  //   function unsubscribePush(registration) {
  //     navigator.serviceWorker.ready
  //     .then(function(registration) {
  //       //Get subscription
  //       registration.pushManager.getSubscription()
  //       .then(function (subscription) {
  //         //If no `push subscription`, then return
  //         if(!subscription) {
  //           alert('Unable to unregister push notification.');
  //           return;
  //         }

  //         //Unsubscribes user
  //         subscription.unsubscribe()
  //           .then(function () {
  //             toast('Unsubscribed successfully.');
  //             console.info('Push notification unsubscribed.');
  //           })
  //           .catch(function (error) {
  //             console.error(error);
  //           });
  //       })
  //       .catch(function (error) {
  //         console.error('Failed to unsubscribe push notification.');
  //       });
  //     })
  //   }

  // isPushSupported();
// });


//server
// Use the web-push library to hide the implementation details of the communication
// between the application server and the push service.
// For details, see https://tools.ietf.org/html/draft-ietf-webpush-protocol and
// https://tools.ietf.org/html/draft-ietf-webpush-encryption.
// const webPush = require("web-push");

// if (!process.env.VAPID_PUBLIC_KEY || !process.env.VAPID_PRIVATE_KEY) {
//   console.log(
//     "You must set the VAPID_PUBLIC_KEY and VAPID_PRIVATE_KEY " +
//       "environment variables. You can use the following ones:"
//   );
//   console.log(webPush.generateVAPIDKeys());
//   return;
// }
// // Set the keys used for encrypting the push messages.
// webPush.setVapidDetails(
//   "https://example.com/",
//   process.env.VAPID_PUBLIC_KEY,
//   process.env.VAPID_PRIVATE_KEY
// );

// // Global array collecting all active endpoints. In real world
// // application one would use a database here.
// const subscriptions = {};

// // How often (in seconds) should the server send a notification to the
// // user.
// const pushInterval = 10;

// // Send notification to the push service. Remove the subscription from the
// // `subscriptions` array if the  push service responds with an error.
// // Subscription has been cancelled or expired.
// function sendNotification(subscription) {
//   webPush
//     .sendNotification(subscription)
//     .then(function () {
//       console.log(
//         "Push Application Server - Notification sent to " +
//           subscription.endpoint
//       );
//     })
//     .catch(function () {
//       console.log(
//         "ERROR in sending Notification, endpoint removed " +
//           subscription.endpoint
//       );
//       delete subscriptions[subscription.endpoint];
//     });
// }

// // In real world application is sent only if an event occured.
// // To simulate it, server is sending a notification every `pushInterval` seconds
// // to each registered endpoint.
// setInterval(function () {
//   Object.values(subscriptions).forEach(sendNotification);
// }, pushInterval * 1000);

// module.exports = function (app, route) {
//   app.get(route + "vapidPublicKey", function (req, res) {
//     res.send(process.env.VAPID_PUBLIC_KEY);
//   });

//   // Register a subscription by adding it to the `subscriptions` array.
//   app.post(route + "register", function (req, res) {
//     var subscription = req.body.subscription;
//     if (!subscriptions[subscription.endpoint]) {
//       console.log("Subscription registered " + subscription.endpoint);
//       subscriptions[subscription.endpoint] = subscription;
//     }
//     res.sendStatus(201);
//   });

//   // Unregister a subscription by removing it from the `subscriptions` array
//   app.post(route + "unregister", function (req, res) {
//     var subscription = req.body.subscription;
//     if (subscriptions[subscription.endpoint]) {
//       console.log("Subscription unregistered " + subscription.endpoint);
//       delete subscriptions[subscription.endpoint];
//     }
//     res.sendStatus(201);
//   });
// };