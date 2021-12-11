window.onload = function() {
  if (localStorage.units) {
    document.getElementById("units").value = localStorage.units;
  }
  if (localStorage.width) {
    document.getElementById("width").value = localStorage.width;
  }
  if (localStorage.height) {
    document.getElementById("height").value = localStorage.height;
  }
  if (localStorage.fontsize) {
    document.getElementById("fontsize").value = localStorage.fontsize;
  }
  document.getElementById("units").oninput = resizeStickers;
  document.getElementById("width").oninput = resizeStickers;
  document.getElementById("height").oninput = resizeStickers;
  document.getElementById("fontsize").oninput = resizeStickers;
  document.getElementById("badgenumbers").oninput = filterBadges;
  resizeStickers();
}

function resizeStickers(e) {
  var units = document.getElementById("units").value;
  var width = document.getElementById("width").value;
  var height = document.getElementById("height").value;
  var fontsize = document.getElementById("fontsize").value;
  Array.from(document.getElementsByClassName('badge-sticker')).forEach( function(element, index, array) {
    element.style.width = width + units;
    element.style.height = height + units;
    element.style.fontSize = fontsize + 'pt';
  });
  localStorage.setItem("units", units);
  localStorage.setItem("width", width);
  localStorage.setItem("height", height);
  localStorage.setItem("fontsize", fontsize);
}

function filterBadges() {
  var badgenumbers = document.getElementById("badgenumbers").value;
  if (badgenumbers == "") {
    applyFilter(function(element) {
      element.style.display = "inline-block";
    });
  }
  else {
    applyFilter(function(element) {
      element.style.display = "none";
    });
    badgenumbers.split(",").forEach( function(badgeId) {
      if (badgeId.includes("-")) {
        var range = badgeId.split("-");
        applyFilter(function(element) {
          if (element.id.toLowerCase().trim() >= range[0].toLowerCase().trim() && element.id.toLowerCase().trim() <= range[1].toLowerCase().trim()) {
            element.style.display = "inline-block";
          }
        });
      }
      else {
        applyFilter(function(element) {
          if (element.id.toLowerCase().trim() == badgeId.toLowerCase().trim()) {
            element.style.display = "inline-block";
          }
        });
      }
    });
  }
}

function applyFilter(filter) {
  Array.from(document.getElementsByClassName('badge-sticker')).forEach( function(element, index, array) {
    filter(element);
  });
}
