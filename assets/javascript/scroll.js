// Je déclare les variables

var headerNav = document.getElementById("nav");
var prevScrollpos = window.pageYOffset;

// Au scroll vers le bas, le header disparaît, au scroll vers  le haut, il réapparaît instantanement 

window.onscroll = function() {

  var currentScrollPos = window.pageYOffset;
  if (prevScrollpos > currentScrollPos) {
    headerNav.style.top = "0";
    headerNav.style.transitionDuration = "0.4s";
  } else {
    headerNav.style.top = "-12vh";
    headerNav.style.transitionDuration = "0.4s";
  }
  prevScrollpos = currentScrollPos;
}