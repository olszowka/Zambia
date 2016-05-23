function fadditems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    for ( i = 0 ; i < source.length ; i++ ) {
        if (source.options[i].selected==true) {
            itemtext=source.options[i].text;
            itemvalue=source.options[i].value;
            dest.options[dest.options.length] = new Option(text=itemtext, value=itemvalue);
            source.options[i] = null;
            i--
            }
        }
    }

function fdropitems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    for ( i = 0 ; i < dest.length ; i++ ) {
        if (dest.options[i].selected==true) {
            itemtext=dest.options[i].text;
            itemvalue=dest.options[i].value;
            source.options[source.options.length] = new Option(text=itemtext, value=itemvalue);
            dest.options[i] = null;
            i--
            }
        }
    }

function mysubmit() {
	$("#sessionid").prop("disabled",false);
    var i;
    for ( i = 0 ; i < document.sessform.featdest.length ; i++ ) {
        document.sessform.featdest.options[i].selected=true;
        }
    for ( i = 0 ; i < document.sessform.servdest.length ; i++ ) {
        document.sessform.servdest.options[i].selected=true;
        }
    }
