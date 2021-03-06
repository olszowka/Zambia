// JavaScript source code
function setColor(item, element, match) {
    var options = element.options;
    var sel = options.item(element.selectedIndex).defaultSelected;
    
    if (sel == false) {
        $(item).addClass("tdChangedHighlight");
    } else {
        $(item).removeClass("tdChangedHighlight");
    }
}

function FindDefaultOption(options) {
    for (var i = 0, option; option = options[i]; i++) {
        if (option.defaultSelected) {
            return (i);
        }
    }
    return (0);
}

function ChangePhase(num, element) {
    var item = '#phase_id_num_' + num;
    var match = 'phase_name_' + num;

    setColor(item, element, match);

    if (num == 8) {
        var table = document.getElementById("phase_table");
        for (var i = 1, row; row = table.rows[i]; i++) {
            var col0 = row.cells[0];
            var rid = col0.innerHTML
            if (rid != 8) {
                var sel = document.getElementById('phase_id_' + rid)
                if (element.selectedIndex == 1) {
                    sel.selectedIndex = 0;
                } else {
                    sel.selectedIndex = FindDefaultOption(sel.options)
                }
                col0.classList.remove("tdChangedHighlight");
            }
        }
    }
}

function ResetCol1() {
    var table = document.getElementById("phase_table");

    for (var i = 0, row; row = table.rows[i]; i++) {
        row.cells[0].classList.remove("tdChangedHighlight");
    }
}