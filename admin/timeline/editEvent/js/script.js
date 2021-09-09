var allDay = document.getElementById("allDay");
var endEventDateExist = document.getElementById("endEventDateExist");

var eventTime = document.getElementById("eventTime");
var endEventDate = document.getElementById("endEventDate");

document.body.onload = function formsScript() {
  if(allDay.checked) {
    eventTime.disabled = true;
  }

  if(endEventDateExist.checked) {
    endEventDate.disabled = true;
  }
}

allDay.onchange = function() {
  eventTime.disabled = this.checked;
}

endEventDateExist.onchange = function() {
  endEventDate.disabled = this.checked;
}
