// function toggleNavMenu() {
//   var x = document.getElementById("links");
//   if (x.style.display === "block") {
//     x.style.display = "none";
//   } else {
//     x.style.display = "block";
//   }
// }
//
// function toggleNavSubmenu() {
//   var x = document.getElementById("dropdown-content");
//   if (x.style.display === "block") {
//     x.style.display = "none";
//   } else {
//     x.style.display = "block";
//   }
// }
//
// document.addEventListener('touchstart', onTouchStart, {passive: true});
//
// function onTouchStart() {
//
// }
//
// function highlightNavItem() {
//   var links = document.getElementById("links").getElementsByTagName("li");
//
//   for(var i = 0; i < links.length; i++) {
//     if(window.location.pathname == "/") {
//       links[0].className = 'focus';
//     } else if(links[i].children[0].pathname == window.location.pathname.match(new RegExp("^" + links[i].children[0].pathname,"g"))) {
//       console.log(links[i].children[0].pathname);
//       console.log(window.location.pathname.match(new RegExp("^" + links[i].children[0].pathname,"g")));
//         links[i].className = 'focus';
//
//         if(i == 3 || i == 4) {
//           links[2].className = 'focus';
//         }
//     }
//   }
// }
//
// highlightNavItem();

var piemenu = new wheelnav("piemenu");
piemenu.wheelRadius = piemenu.wheelRadius * 0.83;
piemenu.spreaderEnable = true;
piemenu.spreaderInTitle = "imgsrc:/img/ephraim-becker-round-list.png";
piemenu.spreaderOutTitle = "imgsrc:/img/ephraim-becker-round.png";
piemenu.spreaderOutTitleHeight = 125;
piemenu.spreaderInTitleHeight = 125;
piemenu.spreaderRadius = 0;
piemenu.createWheel();
piemenu.setTooltips(["Home","Timeline","Daily Life","checked","star"]);
