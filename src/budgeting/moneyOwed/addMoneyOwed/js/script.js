var submitButton = document.getElementById("submitButton");
var timezone = document.getElementById("timezone");
var timezoneOffset = document.getElementById("timezoneOffset");

window.onload = function() {
  timezone.value = new Date().toLocaleTimeString('en-us',{timeZoneName:'short'}).split(' ')[2];
  timezoneOffset.value = new Date().getTimezoneOffset()*60;

  submitButton.disabled = false;
}
