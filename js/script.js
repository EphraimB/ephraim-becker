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

  links[2].classList.remove('focus');

  for(var i = 0; i < links.length; i++) {
    if(window.location.pathname == "/") {
      links[0].className = 'focus';
    } else if(links[i].children[0].pathname == window.location.pathname) {
        links[i].className = 'focus';
    }
  }
}

highlightNavItem();
