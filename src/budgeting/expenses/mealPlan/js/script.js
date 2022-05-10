var foodItem = document.getElementsByClassName("foodItem");

for(var i = 0; i < foodItem.length; i++) {
  for(var j = 0; j < foodItem[i].getElementsByTagName("a").length; j++) {
    foodItem[i].getElementsByTagName("a")[j].classList.add("hide-action-buttons");
  }
}

function showActionButtons(e) {
  for(var j = 0; j < e.getElementsByTagName("a").length; j++) {
    e.getElementsByTagName("a")[j].classList.remove("hide-action-buttons");
  }
}
