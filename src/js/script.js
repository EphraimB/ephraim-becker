function highlightNavItem() {
  var links = document.getElementById("piemenu");

  for(var i = 0; i < links.children.length; i++) {
    if(window.location.pathname == "/") {
      return 0;
    } else if(window.location.pathname == "/everydayLife/" || window.location.pathname == "/college/") {
      return 2;
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
var pieadminsubmenu = new wheelnav('pieadminmenu', piemenu.raphael);

piemenu.spreaderEnable = true;
piemenu.spreaderInTitle = "imgsrc:/img/ephraim-becker-round-list.png";
piemenu.spreaderOutTitle = "imgsrc:/img/ephraim-becker-round.png";
piemenu.spreaderOutTitleHeight = 120;
piemenu.spreaderInTitleHeight = 120;
piemenu.spreaderRadius = 0;

//Customize slicePaths for proper size
piemenu.slicePathFunction = slicePath().DonutSlice;
piemenu.slicePathCustom = slicePath().DonutSliceCustomization();
piemenu.maxPercent = 1.0;

piemenu.slicePathCustom.minRadiusPercent = 0.3;
piemenu.slicePathCustom.maxRadiusPercent = 0.6;
piemenu.sliceSelectedPathCustom = piemenu.slicePathCustom;
piemenu.sliceInitPathCustom = piemenu.slicePathCustom;

pieadminsubmenu.maxPercent = 1.0;
piesubmenu.slicePathFunction = slicePath().DonutSlice;
piesubmenu.slicePathCustom = slicePath().DonutSliceCustomization();
piesubmenu.maxPercent = 1.0;
piesubmenu.slicePathCustom.minRadiusPercent = 0.6;
piesubmenu.slicePathCustom.maxRadiusPercent = 0.9;
piesubmenu.sliceSelectedPathCustom = piesubmenu.slicePathCustom;
piesubmenu.sliceInitPathCustom = piesubmenu.slicePathCustom;
piemenu.createWheel();

piemenu.sliceSelectedAttr = { stroke: '#111111', 'stroke-width': 4 };
piemenu.refreshWheel()

piesubmenu.createWheel();

piesubmenu.sliceSelectedAttr = { stroke: '#111111', 'stroke-width': 4 };
piesubmenu.refreshWheel()

pieadminsubmenu.slicePathFunction = slicePath().DonutSlice;
pieadminsubmenu.slicePathCustom = slicePath().DonutSliceCustomization();
pieadminsubmenu.maxPercent = 1.0;

pieadminsubmenu.slicePathCustom.minRadiusPercent = 0.6;
pieadminsubmenu.slicePathCustom.maxRadiusPercent = 0.9;
pieadminsubmenu.sliceSelectedPathCustom = pieadminsubmenu.slicePathCustom;
pieadminsubmenu.sliceInitPathCustom = pieadminsubmenu.slicePathCustom;
pieadminsubmenu.createWheel();

piemenu.navigateWheel(highlighttedNav);
piesubmenu.navigateWheel(highlighttedSubNav);

piemenu.setTooltips(["Home", "Timeline", "Daily Life", "Projects", "Resources", "About", "Login/Logout"]);
piesubmenu.setTooltips(["Everyday Life", "College Life"]);
pieadminsubmenu.setTooltips(["Budgeting"]);

pieadminsubmenu.navItems[0].navItem.hide();

if(isAdmin) {
  //Add function to each main menu for show/hide sub menus
  var meSelected = true;
  piemenu.navItems[6].navigateFunction = function() {
    if (meSelected) {
      pieadminsubmenu.navItems[0].navItem.show();
    }
    else {
      pieadminsubmenu.navItems[0].navItem.hide();
    }
    meSelected = !meSelected;
  }
}

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
