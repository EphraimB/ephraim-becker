function filterTimeline(year) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.body.innerHTML = this.responseText;
    }
  };

  xmlhttp.open("GET", "filterTimeline.php?year="+year, true);
  xmlhttp.send();
}
