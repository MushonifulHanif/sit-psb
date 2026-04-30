// SW Version: 1.0.2 (Force update)
self.addEventListener('push', function(event) {
    let payload = {
        title: 'SIT-PSB PPRTQ',
        body: 'Anda memiliki pemberitahuan baru.',
        icon: '/assets/img/logo.png', 
        url: '/'
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            payload.title = parsed.title || payload.title;
            payload.body = parsed.body || payload.body;
            // Baca URL dari objek data yang dikirim PHP
            if (parsed.data && parsed.data.url) {
                payload.url = parsed.data.url;
            }
            if (parsed.icon) {
                payload.icon = parsed.icon;
            }
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const options = {
        body: payload.body,
        icon: payload.icon,
        data: { url: payload.url },
        vibrate: [100, 50, 100],
        requireInteraction: true
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
