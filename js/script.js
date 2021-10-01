function toggleNavMenu() {
  var x = document.getElementById("links");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}

function toggleNavSubmenu() {
  var x = document.getElementById("dropdown-content");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}

document.addEventListener('touchstart', onTouchStart, {passive: true});

function onTouchStart() {

}

function highlightNavItem() {
  var links = document.getElementById("links").getElementsByTagName("li");
  var current = 0;

  for(var i = 0; i < links.length; i++) {
      if (links[i].children[0].href === document.URL) {
          current = i;
      }
  }
  links[current].className = 'focus';
}

highlightNavItem();
