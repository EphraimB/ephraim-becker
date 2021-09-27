var offsetInfo = new Date().getTimezoneOffset() * 60;

var url = new URL(window.location);
url.searchParams.set('offset', offsetInfo);
window.history.pushState({}, '', url);
