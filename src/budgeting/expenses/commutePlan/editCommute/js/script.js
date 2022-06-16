var priceElement = document.getElementById("price");
var zoneElement = document.getElementById("zoneId");
var peakCheckbox = document.getElementById("peakId");
var price = 0.00;

window.addEventListener("load", changePrice);
zoneElement.addEventListener("change", changePrice);
peakCheckbox.addEventListener("change", changePrice);

function changePrice() {
  if(parseInt(zoneElement.value) == 0) {
    price = 1.35;
  } else if(parseInt(zoneElement.value) == 1) {
    if(peakCheckbox.checked) {
      price = 9.00;
    } else {
      price = 4.50;
    }
  } else if(parseInt(zoneElement.value) == 2) {
    if(peakCheckbox.checked) {
      price = 9.75;
    } else {
      price = 4.75;
    }
  } else if(parseInt(zoneElement.value) == 3) {
    if(peakCheckbox.checked) {
      price = 10.75;
    } else {
      price = 5.25;
    }
  } else if(parseInt(zoneElement.value) == 4) {
     if(peakCheckbox.checked) {
       price = 12.50;
     } else {
       price = 6.25;
     }
   } else if(parseInt(zoneElement.value) == 5) {
      if(peakCheckbox.checked) {
        price = 12.75;
      } else {
        price = 6.25;
      }
    } else if(parseInt(zoneElement.value) == 7) {
       if(peakCheckbox.checked) {
         price = 14.00;
       } else {
         price = 7.00;
       }
     }
  priceElement.value = price;
}
