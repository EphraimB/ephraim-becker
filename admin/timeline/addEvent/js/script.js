var allDay = document.getElementById("allDay");
var endEventDateExist = document.getElementById("endEventDateExist");

var eventTime = document.getElementById("eventTime");
var endEventDate = document.getElementById("endEventDate");



allDay.onchange = function() {
  eventTime.disabled = this.checked;
}

endEventDateExist.onchange = function() {
  endEventDate.disabled = !this.checked;
}
