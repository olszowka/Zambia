function makeEverythingRed() {
    const requiredElements = Array("name", "email", "track", "title", "progguiddesc");
    requiredElements.forEach((elem) => {
        const domElem = document.getElementsByName(elem)[0];
        if (!domElem) {
            return;
        }
        domElem.style.color = 'red';
    })
}

function checkSubmitButton() {
    const requiredElements = Array("name", "email", "track", "title", "progguiddesc");
    requiredElements.forEach((elem) => {
        const domElem = document.getElementsByName(elem)[0];
        if (!domElem) {
            return;
        }
        switch (domElem.tagName) {
            case "LABEL":
                break;
            case "SELECT":
                if (o.options[o.selectedIndex].value === 0) {
                    enable = false;
                    relatedO.style.color = unhappyColor;
                } else {
                    relatedO.style.color = happyColor;
                }
                break;
            case "TEXTAREA":
                if (o.value === "") {
                    enable = false;
                    relatedO.style.color = unhappyColor;
                } else {
                    relatedO.style.color = happyColor;
                }
                break;
            case "INPUT":
                if (o.value === "") {
                    enable = false;
                    relatedO.style.color = unhappyColor;
                } else {
                    relatedO.style.color = happyColor;
                }
                break;
        }
            }
        }
    }
    var saveButton = document.getElementById("sButtonTop");
    if (saveButton != null) {
        saveButton.disabled = !enable;
    }
    var saveButton2 = document.getElementById("sButtonBottom");
    if (saveButton2 != null) {
        saveButton2.disabled = !enable;
    }
}

function initializeBrainstormCreateSession() {
    makeEverythingRed();
    checkSubmitButton();
}

document.addEventListener('DOMContentLoaded', initializeBrainstormCreateSession);
