var main = document.getElementById("main");

function filterTimeline(year, month, day) {
  month = month || 0;
  day = day || 0;

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      main.innerHTML = this.responseText;
      window.history.pushState("Timeline - "+year, "Ephraim Becker - Timeline", "/timeline/index.php#"+year+"-"+month+"-"+day);
    }
  };

  xmlhttp.open("GET", "filterTimeline.php?year="+year+"&month="+month+"&day="+day, true);
  xmlhttp.send();
}

if(window.location.hash) {
  var hash = window.location.hash.substring(1);
  var hashArr = hash.split("-");
  var year = hashArr[0];
  var month = hashArr[1];
  var day = hashArr[2];
  filterTimeline(year, month, day);
}
