var allDay = document.getElementById("allDay");
var endEventDateExist = document.getElementById("endEventDateExist");

var eventDate = document.getElementById("eventDate");
var eventTime = document.getElementById("eventTime");
var endEventDate = document.getElementById("endEventDate");

var timezone = document.getElementById("timezone");
var timezoneOffset = document.getElementById("timezoneOffset");


function msToTime(duration) {
  var milliseconds = Math.floor(duration % 1000),
    seconds = Math.floor((duration / 1000) % 60),
    minutes = Math.floor((duration / (1000 * 60)) % 60),
    hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

  hours = (hours < 10) ? "0" + hours : hours;
  minutes = (minutes < 10) ? "0" + minutes : minutes;
  seconds = (seconds < 10) ? "0" + seconds : seconds;

  return hours + ":" + minutes + ":" + seconds;
}

function msToDate(duration) {
  var date = new Date(duration);

  return date.toISOString().split('T')[0];
}

document.body.onload = function formsScript() {
  if(allDay.checked) {
    eventTime.disabled = true;
  } else {
    var localDate = msToDate(Date.parse(eventDate.value + " " + eventTime.value) + -((timezoneOffset.value*1000)*2));
    eventDate.value = localDate;
  }

  if(!endEventDateExist.checked) {
    endEventDate.disabled = true;
  }

  var localTime = msToTime(Date.parse(eventDate.value + " " + eventTime.value) + -((timezoneOffset.value*1000)*2));
  eventTime.value = localTime;
}

allDay.onchange = function() {
  eventTime.disabled = this.checked;
}

endEventDateExist.onchange = function() {
  endEventDate.disabled = !this.checked;
}

eventTime.onchange = function() {
  timezone.value = new Date().toLocaleTimeString('en-us',{timeZoneName:'short'}).split(' ')[2];
  timezoneOffset.value = new Date().getTimezoneOffset()*60;
}
