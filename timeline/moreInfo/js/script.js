var date = document.getElementsByClassName("date");
var offset = document.getElementsByClassName("offset");

var offsetInfo = new Date().getTimezoneOffset() * 60;

var xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function() {
  if (this.readyState == 4 && this.status == 200) {
    for(var i = 0; i < date.length; i++) {
      date[i].innerHTML = this.responseText;
    }
  }
};

xmlhttp.open("GET", "convertToLocalTime.php?offset="+offsetInfo+"&date="+date, true);
xmlhttp.send();
