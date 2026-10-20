import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const wsHost = (import.meta.env.VITE_REVERB_HOST && import.meta.env.VITE_REVERB_HOST !== 'localhost' && import.meta.env.VITE_REVERB_HOST !== '127.0.0.1')
    ? import.meta.env.VITE_REVERB_HOST
    : (typeof window !== 'undefined' ? window.location.hostname : 'localhost');

const isHttps = typeof window !== 'undefined'
    ? (window.location.protocol === 'https:' || (import.meta.env.VITE_REVERB_SCHEME ?? '') === 'https')
    : false;

const isLocalHost = wsHost === 'localhost' || wsHost === '127.0.0.1';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || '2e6qbwql55vxgwre8lpd',
    wsHost: wsHost,
    wsPort: isLocalHost ? (Number(import.meta.env.VITE_REVERB_PORT) || 8080) : (window.location.port ? Number(window.location.port) : (isHttps ? 443 : 80)),
    wssPort: isLocalHost ? (Number(import.meta.env.VITE_REVERB_PORT) || 8080) : (window.location.port ? Number(window.location.port) : 443),
    forceTLS: isHttps,
    enabledTransports: ['ws', 'wss'],
});
