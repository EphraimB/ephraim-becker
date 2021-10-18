var allDay = document.getElementById("allDay");
var endEventDateExist = document.getElementById("endEventDateExist");

var eventTime = document.getElementById("eventTime");
var timezone = document.getElementById("timezone");
var timezoneOffset = document.getElementById("timezoneOffset");

var endEventDate = document.getElementById("endEventDate");
var submit = document.getElementById("submit");


allDay.onchange = function() {
  eventTime.disabled = this.checked;
}

endEventDateExist.onchange = function() {
  endEventDate.disabled = !this.checked;
}

eventDate.onchange = function() {
  timezone.value = new Date().toLocaleTimeString('en-us',{timeZoneName:'short'}).split(' ')[2];
  timezoneOffset.value = new Date().getTimezoneOffset()*60;

  submit.disabled = false;
}
