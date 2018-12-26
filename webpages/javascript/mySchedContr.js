function onSubmitSchedConstr() {
    Array.prototype.slice.call(document.querySelectorAll(".conflict-block-checkbox"))
        .forEach(function(item) {
        if (item.defaultChecked === item.checked) {
            item.disabled = true;
        } else {
            if (item.checked) {
                item.value = "1";
            } else {
                item.value = "0";
                item.checked = true;
            }
        }
    });
}