var allDay = document.getElementById("allDay");
var endEventDateExist = document.getElementById("endEventDateExist");

var eventDate = document.getElementById("eventDate");
var eventTime = document.getElementById("eventTime");
var endEventDate = document.getElementById("endEventDate");

var timezone = document.getElementById("timezone");
var timezoneOffset = document.getElementById("timezoneOffset");


document.body.onload = function formsScript() {
  if(allDay.checked) {
    eventTime.disabled = true;
  }

  if(!endEventDateExist.checked) {
    endEventDate.disabled = true;
  }
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
