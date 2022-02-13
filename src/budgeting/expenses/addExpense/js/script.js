var startDate = document.getElementById("startDate");
var endDate = document.getElementById("endDate");
var endDateExist = document.getElementById("endDateExist");
var submit = document.getElementById("submit");

startDate.onchange = function() {
  timezone.value = new Date().toLocaleTimeString('en-us',{timeZoneName:'short'}).split(' ')[2];
  timezoneOffset.value = new Date().getTimezoneOffset()*60;

  submit.disabled = false;
}

endDateExist.onchange = function() {
  endDate.disabled = !this.checked;
}
