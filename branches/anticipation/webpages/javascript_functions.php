<?php
function javascript_for_import_participant() { //
?>
<link rel="stylesheet" type="text/css" media="screen" href="themes/green/grid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="themes/jqModal.css" />
<script src="jquery.js" type="text/javascript"></script>
<script src="jquery.jqGrid.js" type="text/javascript"></script>
<script src="js/jqModal.js" type="text/javascript"></script>
<script src="js/jqDnR.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function(){ 
  jQuery("#list").jqGrid({
    url:'getParticpantFormData.php',
    datatype: 'xml',
    mtype: 'GET',
    colNames:['Submission Date','Name', 'Lang', 'Email', 'Badge Id' ],
    colModel :[ 
      {name:'mail_date', index:'mail_date', width:250	}, 
      {name:'name', index:'name', width:300}, 
      {name:'lang', index:'lang', width:50, align:'left'},
      {name:'lang', index:'email', width:300},
      {name:'lang', index:'badgeid', width:100}
	   ],
    pager: jQuery('#pager'),
    rowNum:20,
    rowList:[20,50,100],
    sortname: 'name',
    sortorder: "asc",
    viewrecords: true,
    imgpath: 'themes/green/images',
	height: "100%",
	multiselect: true,
	subGrid : true,
	subGridUrl: 'getParticpantDetails.php',
    subGridModel: [{ 
		name  : ['Address','FR','Alt(FR)','Language','EN','Alt(EN)','Language'], 
        width : [300, 30, 60, 200, 30, 60, 200],
		params: ['name']
	}],
    caption: 'Participants from Email Form',
	toolbar: [true,"top"],
	loadError : function(xhr,st,err) {
    	jQuery("#rsperror").html("Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText);
    }
  }); 

$("#t_list").append("<input type='button' value='Add to Zambia' style='height:20px;font-size:-3'/>");
$("input","#t_list").click(function(){
	$('#rsperror').text("");
	var gsr = jQuery("#list").getGridParam('selarrrow');
	var msg = $("#load_list").text();
	$.get('addParticipant.php',{'ids[]':gsr}, function(data) { 
		var error = $(data).find('#error').html();
		if ( error != null) { $('#rsperror').text(error); };
		$("#load_list").html("Importing " + data);
		$("#load_list").fadeIn("normal", function() {
			$("#load_list").fadeOut("normal", function() {
				$("#load_list").text(msg);
				$("#list").trigger("reloadGrid");
				});
			});
		});
});
}); 
</script>
<?php } ?>
<?php
function javascript_for_edit_session() { //now also for edit participant
?>
    <SCRIPT LANGUAGE="JavaScript">

<!-- Begin

function fpopdefaults() {
    var a, b, c;
    a = document.partform.firstname.value;
    b = document.partform.lastname.value;
    c = a + " " + b;
    document.partform.pubsname.value = c;
    document.partform.badgename.value = c;
    }

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
<?php } 
// This function writes out to the browser the javascript functions for highlighting the tabs.
    function mousescripts() { ?>

<script language="javascript">
  <!--

  // function called when the mouse is over a tab

  function mouseovertab(x)
  {
    x.className="mousedovertab";
  }

  // function called when the mouse leaves a tab

  function mouseouttab(x)
  {
    x.className="usabletab";
  }
  -->
</script><?php } ?>
