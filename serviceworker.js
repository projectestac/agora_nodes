'use strict';

/**
 * Service Worker for Àgora Nodes
 * */

const cacheName = 'Àgora cache';
const startPage = self.location.href;
const offlinePage = self.location.href;
const filesToCache = [startPage, offlinePage];
const neverCacheUrls = ['/wp-admin/(.*)'];
// Install
self.addEventListener('install', function(e) {
    e.waitUntil(
        caches.open(cacheName).then(function(cache) {
            filesToCache.map(function(url) {
                return cache.add(url).catch(function (reason) {
                });
            });
        })
    );
});

// Activate
self.addEventListener('activate', function(e) {
    e.waitUntil(
        caches.keys().then(function(keyList) {
            return Promise.all(keyList.map(function(key) {
                if ( key !== cacheName ) {
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

// Fetch


self.addEventListener('fetch', function(event) {

    if ( ! neverCacheUrls.every(checkNeverCacheList, event.request.url) ) {
        return;
    }

    if ( event.request.method !== 'GET' ) {
        event.respondWith(
            fetch(event.request).catch( function() {
                return caches.match(offlinePage);
            })
        );
        return;
    }


    event.respondWith(
        caches.match(event.request)
            .then(function(response) {

                if (event.request.url.match(/wp-login/) || event.request.url.match(/wp-admin/) || event.request.url.match(/preview=true/)) {
                    return fetch(event.request);
                }

                if (response) {
                    return response;
                } else {
                    return fetch(event.request)
                        .then(function(res) {
                            return caches.open(cacheName)
                                .then(function(cache) {
                                    cache.put(event.request.url, res.clone());
                                    return res;
                                })
                        })
                        .catch(function(err) {
                            return caches.open(cacheName)
                                .then(function(cache) {
                                    return cache.match(offlinePage);
                                });
                        });
                }
            })
    );
});

// Check if current url is in the neverCacheUrls list
function checkNeverCacheList(url) {
    if ( this.match(url) ) {
        return false;
    }
    return true;
}
