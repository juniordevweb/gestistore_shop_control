console.log("SW FILE CHARGE");

self.addEventListener('install', event => {
    console.log('SW installe');
});

self.addEventListener('activate', event => {
    console.log('SW active');
});

self.addEventListener('fetch', event => {
});