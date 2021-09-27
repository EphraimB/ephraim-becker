var offsetDiv = document.getElementById("timeOffset");

var offsetInfo = new Date().getTimezoneOffset() * 60;

offsetDiv.value = offsetInfo;
