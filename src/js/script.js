// // function toggleNavMenu() {
// //   var x = document.getElementById("links");
// //   if (x.style.display === "block") {
// //     x.style.display = "none";
// //   } else {
// //     x.style.display = "block";
// //   }
// // }
// //
// // function toggleNavSubmenu() {
// //   var x = document.getElementById("dropdown-content");
// //   if (x.style.display === "block") {
// //     x.style.display = "none";
// //   } else {
// //     x.style.display = "block";
// //   }
// // }
// //
// // document.addEventListener('touchstart', onTouchStart, {passive: true});
// //
// // function onTouchStart() {
// //
// // }

function highlightNavItem() {
  var links = document.getElementById("piemenu");

  for(var i = 0; i < links.children.length; i++) {
    if(window.location.pathname == "/") {
      return 0;
    } else if(links.children[i].children[0].pathname == window.location.pathname.match(new RegExp("^" + links.children[i].children[0].pathname,"g"))) {
      console.log(links.children[i].children[0].pathname);
      console.log(window.location.pathname.match(new RegExp("^" + links.children[i].children[0].pathname,"g")));
      console.log(i);
      
      return i;
    }
  }

  return 0;
}

var highlighttedNav = highlightNavItem();

var piemenu = new wheelnav("piemenu");
piemenu.wheelRadius = piemenu.wheelRadius * 0.83;
piemenu.spreaderEnable = true;
piemenu.spreaderInTitle = "imgsrc:/img/ephraim-becker-round-list.png";
piemenu.spreaderOutTitle = "imgsrc:/img/ephraim-becker-round.png";
piemenu.spreaderOutTitleHeight = 125;
piemenu.spreaderInTitleHeight = 125;
piemenu.spreaderRadius = 0;
piemenu.createWheel();

piemenu.navigateWheel(highlighttedNav);

piemenu.setTooltips(["Home","Timeline","Daily Life","checked","star"]);
