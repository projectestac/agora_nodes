if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('serviceworker.js')
            .then(function(registration) { registration.update(); })
            .catch(function(error) { });
    });
}

