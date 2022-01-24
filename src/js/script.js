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

function highlightSubNavItem() {
  var links = document.getElementById("piesubmenu");

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
var highlighttedSubNav = highlightSubNavItem();

var piemenu = new wheelnav("piemenu", null, 600, 600);
var piesubmenu = new wheelnav('piesubmenu', piemenu.raphael);

// piemenu.wheelRadius = piemenu.wheelRadius * 0.83;
piemenu.spreaderEnable = true;
piemenu.spreaderInTitle = "imgsrc:/img/ephraim-becker-round-list.png";
piemenu.spreaderOutTitle = "imgsrc:/img/ephraim-becker-round.png";
piemenu.spreaderOutTitleHeight = 120;
piemenu.spreaderInTitleHeight = 120;
piemenu.spreaderRadius = 0;

//Customize slicePaths for proper size
piemenu.slicePathFunction = slicePath().DonutSlice;
piemenu.slicePathCustom = slicePath().DonutSliceCustomization();
piemenu.slicePathCustom.minRadiusPercent = 0.3;
piemenu.slicePathCustom.maxRadiusPercent = 0.6;
piemenu.sliceSelectedPathCustom = piemenu.slicePathCustom;
piemenu.sliceInitPathCustom = piemenu.slicePathCustom;
piesubmenu.slicePathFunction = slicePath().DonutSlice;
piesubmenu.slicePathCustom = slicePath().DonutSliceCustomization();
piesubmenu.slicePathCustom.minRadiusPercent = 0.6;
piesubmenu.slicePathCustom.maxRadiusPercent = 0.9;
piesubmenu.sliceSelectedPathCustom = piesubmenu.slicePathCustom;
piesubmenu.sliceInitPathCustom = piesubmenu.slicePathCustom;
piemenu.createWheel();

piesubmenu.createWheel();

piemenu.navigateWheel(highlighttedNav);

piemenu.setTooltips(["Home", "Timeline", "Daily Life", "Projects", "Resources", "About", "Login/Logout"]);
piesubmenu.setTooltips(["Everyday Life", "College Life"]);

piesubmenu.navItems[0].navItem.hide();
piesubmenu.navItems[1].navItem.hide();

//Add function to each main menu for show/hide sub menus
var dailyLifeSelected = true;
piemenu.navItems[2].navigateFunction = function() {
  if (dailyLifeSelected) {
    piesubmenu.navItems[0].navItem.show();
    piesubmenu.navItems[1].navItem.show();
  }
  else {
    piesubmenu.navItems[0].navItem.hide();
    piesubmenu.navItems[1].navItem.hide();
  }
  dailyLifeSelected = !dailyLifeSelected;
}
