<?php
function javascript_for_edit_session() {
?>
    <SCRIPT LANGUAGE="JavaScript">

<!-- Begin

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
    var i;
    for ( i = 0 ; i < document.sessform.featdest.length ; i++ ) {
        document.sessform.featdest.options[i].selected=true;
        }
    for ( i = 0 ; i < document.sessform.servdest.length ; i++ ) {
        document.sessform.servdest.options[i].selected=true;
        }
    }

//  End -->

  </script>
<?php } ?>

<?php function javascript_pretty_buttons() { ?>
  <script language="JavaScript" type="text/JavaScript">

<!-- Begin

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
// End -->
  </script>
<?php } ?>
