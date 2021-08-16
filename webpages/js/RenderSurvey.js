// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function lradditems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    var itemothertext;
    var option;
    for (i = 0; i < source.length; i++) {
        if (source.options[i].selected == true) {
            option = source.options[i];
            itemtext = option.text;
            itemvalue = option.value;
            itemothertext = option.getAttribute('data-othertext');
            option = new Option(text = itemtext, value = itemvalue);
            option.setAttribute('data-othertext', itemothertext);
            dest.options[dest.options.length] = option;
            source.options[i] = null;
            i--
        }
    }
    lrChangeOthertext(dest);
}

function lrdropitems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    var itemothertext;
    var option;

    for (i = 0; i < dest.length; i++) {
        if (dest.options[i].selected == true) {
            option = dest.options[i];
            itemtext = option.text;
            itemvalue = option.value;
            itemothertext = option.getAttribute('data-othertext');
            option = new Option(text = itemtext, value = itemvalue);
            option.setAttribute('data-othertext', itemothertext);
            source.options[source.options.length] = option;
            dest.options[i] = null;
            i--
        }
    }
    lrChangeOthertext(dest);
}

function lrChangeOthertext(dest) {
    var i;
    var othertext = 0;
    var name;
    var ot;
    for (i = 0; i < dest.length; i++) {
        othertext = othertext + Number(dest.options[i].getAttribute('data-othertext'));
    }
    id = dest.getAttribute("id");
    name = id.replace('-dest', '-othertext');
    //console.log("in fChangeOthertext, id= " + id + " and othertext = " + othertext + " othertext field = " + name);

    ot = document.getElementById(name);
    ot.disabled = (othertext == 0);
    if (othertext == 0) ot.value = '';
}

function RadioChangeOthertext(el) {
    var name = el.getAttribute("name")
    var othertext = el.getAttribute("data-othertext");
    var checked = el.getAttribute("value");
    //console.log("RadioChangeOthertext call on id: " + name + " with check=" + checked + " and othertext= " + othertext);

    name = name + '-othertext';
    var ot = document.getElementById(name);
    ot.disabled = (othertext == 0);
    if (othertext == 0) ot.value = '';
}

function CheckboxChangeOthertext(el) {
    var name = el.getAttribute("name")
    var checkboxes = document.getElementsByName(name);
    var othertext = 0;
    var i;

    //console.log("CheckboxChangeOthertext call on name: " + name);
    //console.log(checkboxes);

    for (i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked)
            othertext = othertext + Number(checkboxes[i].getAttribute('data-othertext'));
    }

    name = name.replace('[]', '-othertext');
    var ot = document.getElementById(name);
    ot.disabled = (othertext == 0);
    if (othertext == 0) ot.value = '';
}

function SelectChangeOthertext(el) {
    var othertext = 0;
    var name = el.getAttribute("name")
    //console.log("SelectChangeOthertext call on name: " + name);

    if (name.includes('[]')) {
        var options = el.options;
        var i;

        name = name.replace('[]', '');
        for (i = 0; i < options.length; i++) {
            if (options[i].selected)
                othertext = othertext + Number(options[i].getAttribute('data-othertext'));
        }
    } else {
        var index = el.selectedIndex;
        var opt = el.options[index];
        othertext = opt.getAttribute("data-othertext");
    }

    name = name + '-othertext';
    //console.log(name);
    var ot = document.getElementById(name);
    //console.log(othertext);
    ot.disabled = (othertext == 0);
    if (othertext == 0) ot.value = '';
}