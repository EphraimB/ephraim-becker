var commuteItem = document.getElementsByClassName("commuteItem");

for(var i = 0; i < commuteItem.length; i++) {
  for(var j = 0; j < commuteItem[i].getElementsByTagName("a").length; j++) {
    commuteItem[i].getElementsByTagName("a")[j].classList.add("hide-action-buttons");
  }
}

function showActionButtons(e) {
  for(var j = 0; j < e.getElementsByTagName("a").length; j++) {
    e.getElementsByTagName("a")[j].classList.remove("hide-action-buttons");
  }
}
