var offsetInfo = new Date().getTimezoneOffset() * 60
url = new URL(window.location.href);

var newUrl = new URL(window.location);
newUrl.searchParams.set('offset', offsetInfo);
window.history.pushState({}, '', newUrl);

window.onload = function() {
  if(!window.location.hash) {
    window.location = window.location + '#loaded';
    window.location.reload();
  }
}
