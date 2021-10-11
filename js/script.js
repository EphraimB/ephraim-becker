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

  for(var i = 0; i < links.length; i++) {
    if(window.location.pathname == "/") {
      links[0].className = 'focus';
    } else if(links[i].children[0].pathname == window.location.pathname) {
        links[i].className = 'focus';
        links[2].classList.remove('focus');

        if(i == 3 || i == 4) {
          links[2].className = 'focus';
        }
    }
  }
}

highlightNavItem();
