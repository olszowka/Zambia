// Onload function called when page loaded. Load initial values from local storage. Set up events.
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
  if (localStorage.horgap) {
    document.getElementById("horgap").value = localStorage.horgap;
  }
  if (localStorage.vergap) {
    document.getElementById("vergap").value = localStorage.vergap;
  }
  if (localStorage.fontsize) {
    document.getElementById("fontsize").value = localStorage.fontsize;
  }
  document.getElementById("units").oninput = resizeStickers;
  document.getElementById("width").oninput = resizeStickers;
  document.getElementById("height").oninput = resizeStickers;
  document.getElementById("horgap").oninput = resizeStickers;
  document.getElementById("vergap").oninput = resizeStickers;
  document.getElementById("fontsize").oninput = resizeStickers;
  document.getElementById("badgenumbers").oninput = filterBadges;
  document.getElementById("skip").oninput = insertBlankLabels;
  document.getElementById("borders").oninput = showBorders;
  resizeStickers();
}

// Set label size and spacing.
function resizeStickers() {
  // Get the values of the form fields.
  var units = document.getElementById("units").value;
  var width = document.getElementById("width").value;
  var height = document.getElementById("height").value;
  var horgap = document.getElementById("horgap").value;
  var vergap = document.getElementById("vergap").value;
  var fontsize = document.getElementById("fontsize").value;
  // Set appropriate step size for selected units.
  var step = (units == "in" ? "0.01" : (units == "cm" ? "0.1" : "1"));
  document.getElementById("width").step = step;
  document.getElementById("height").step = step;
  document.getElementById("horgap").step = step;
  document.getElementById("vergap").step = step;
  // Apply styles to each badge label.
  applyToBadgeStickers(function(element, index, array) {
    element.style.width = width + units;
    element.style.height = height + units;
    element.style.marginRight = horgap + units;
    element.style.marginBottom = vergap + units;
    element.style.fontSize = fontsize + 'pt';
  });
  // Save parameters to local storage.
  localStorage.setItem("units", units);
  localStorage.setItem("width", width);
  localStorage.setItem("height", height);
  localStorage.setItem("horgap", horgap);
  localStorage.setItem("vergap", vergap);
  localStorage.setItem("fontsize", fontsize);
}

// Apply filtering to badges if specified in 
function filterBadges() {
  // Get the badge numbers field.
  var badgenumbers = document.getElementById("badgenumbers").value;
  // If no badge numbers specified, display all labels.
  if (badgenumbers.trim() == "") {
    applyToBadgeStickers(function(element) {
      element.style.display = "inline-block";
    });
  }
  else {
    // First hide all badge labels.
    applyToBadgeStickers(function(element) {
      element.style.display = "none";
    });
    // First split selection by commas, and loop through.
    badgenumbers.split(",").forEach( function(badgeId) {
      // Check if part contains a hyphen.
      if (badgeId.includes("-")) {
        // Split by hyphen. Show all labels between the lower and upper range.
        var range = badgeId.split("-");
        applyToBadgeStickers(function(element) {
          if (element.id.toLowerCase().trim() >= range[0].toLowerCase().trim() && element.id.toLowerCase().trim() <= range[1].toLowerCase().trim()) {
            element.style.display = "inline-block";
          }
        });
      }
      else {
        // Single label. Show the label matching the badge ID.
        applyToBadgeStickers(function(element) {
          if (element.id.toLowerCase().trim() == badgeId.toLowerCase().trim()) {
            element.style.display = "inline-block";
          }
        });
      }
    });
    // Finally show any blank labels.
    Array.from(document.getElementsByClassName('blank-sticker')).forEach( function(element, index, array) {
      element.style.display = "inline-block";
    });
  }
}

// Check if borders checkbox checked and apply borders to labels if required.
function showBorders() {
  var borders = document.getElementById("borders").checked;
  if (borders) {
    var borderStyle = "1px solid black";
  }
  else {
    var borderStyle = "none";
  }
  applyToBadgeStickers(function(element) {
    element.style.border = borderStyle;
  });
}

// Add blank labels to start of output.
function insertBlankLabels() {
  var skip = document.getElementById("skip").value;
  var stickers = document.getElementById("stickers");
  // Delete any previously added blank labels.
  Array.from(document.getElementsByClassName('blank-sticker')).forEach( function(element, index, array) {
    element.remove();
  });
  // Add the specified number of blank labels at the start of the stickers element.
  for (var i=0; i < skip; i++) {
    var blank = document.createElement("div");
    blank.classList.add("blank-sticker");
    blank.classList.add("badge-sticker");
    stickers.insertBefore(blank, stickers.firstChild);
  }
  // Apply size and borders to new labels.
  resizeStickers();
  showBorders();
}

// Function to apply a function to all badge stickers.
function applyToBadgeStickers(filter) {
  Array.from(document.getElementsByClassName('badge-sticker')).forEach( function(element, index, array) {
    filter(element);
  });
}
