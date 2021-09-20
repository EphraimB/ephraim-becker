function filterTimeline(year, month, day) {
  month = month || 0;
  day = day || 0;

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.body.innerHTML = this.responseText;
    }
  };

  xmlhttp.open("GET", "filterTimeline.php?year="+year+"&month="+month+"&day="+day, true);
  xmlhttp.send();
}
