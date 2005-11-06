<?php

    function participant_header($title) {
    global $badgeid;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html xmlns="http://www.w3.org/TR/xhtml1/transitional">
<head>
  <title>Zambia -- <?php echo $title ?></title>
  <link rel="stylesheet" href="ParticipantSection.css" type="text/css">
  <meta name="keywords" content="Questionnaire">
  <meta name="description" content="Form to request information from potential program participants">
  <script language="JavaScript" type="text/JavaScript">
<!--
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
//-->
  </script>
</head>
<body onload="MM_preloadImages('images/my_profile-active.png','images/my_availability-active.png',
    'images/search_panels-active.png','images/staff_pages-active.png','images/welcome-active.png',
    'images/my_general_interests-active.png','images/my_suggestions-active.png)">
<H1 class="head">Zambia&ndash;The Arisia Scheduling Tool</H1> 
<hr>

<?php if (isset($_SESSION["badgeid"])) { ?>
<!--    <td background="images/grey-bg.gif"> -->
  <table class="header">
    <tr>
      <td class="head"><a href="welcome.php" onmouseout="MM_swapImgRestore()"
          onmouseover="MM_swapImage('welcome','','images/welcome-active.png',1)">
          <img src="images/welcome.png" name="welcome" alt="Welcome"></a></td>
      <td class="head"><a href="my_contact.php" onmouseout="MM_swapImgRestore()"
          onmouseover="MM_swapImage('my_profile','','images/my_profile-active.png',1)">
          <img src="images/my_profile.png" name="my_profile" alt="My Profile"></a></td>
      <td class="head"><a href="my_sched_constr.php" onmouseout="MM_swapImgRestore()"
          onmouseover="MM_swapImage('my_availability','','images/my_availability-active.png',1)">
          <img src="images/my_availability.png" name="my_availability" alt="My Availability"></a></td>
      <td class="head"><a href="my_sessions1.php" onmouseout="MM_swapImgRestore()"
          onmouseover="MM_swapImage('search_panels','','images/search_panels-active.png',1)">
          <img src="images/search_panels.png" name="search_panels" alt="Search Panels"></a></td>
      <td class="head"><a href="my_sessions2.php" onmouseout="MM_swapImgRestore()"
          onmouseover="MM_swapImage('my_panel_interests','','images/my_panel_interests-active.png',1)">
          <img src="images/my_panel_interests.png" name="my_panel_interests" alt="My Panel Interests"></a></td>
      <td class="head"><a href="my_suggestions.php" onmouseout="MM_swapImgRestore()"
          onmouseover="MM_swapImage('my_suggestions','','images/my_suggestions-active.png',1)">
          <img src="images/my_suggestions.png" name="my_suggestions" alt="My Suggestions"></a></td>
      <td class="head"><a href="my_interests.php" onmouseout="MM_swapImgRestore()"
          onmouseover="MM_swapImage('my_general_interests','','images/my_general_interests-active.png',1)">
          <img src="images/my_general_interests.png" name="my_general_interests" alt="My General Interests"></a></td>
<?php if (isStaff($badgeid)) { 
      echo "<td id=\"head\"><a href=\"StaffPage.php\" onmouseout=\"MM_swapImgRestore()\" onmouseover=\"MM_swapImage('staff_pages','','images/staff_pages-active.png',1)\"><img src=\"images/staff_pages.png\" name=\"staff_pages\" alt=\"Staff Pages\"></a></td>";
} ?>
          </tr>
        </table>
      </td>
    </tr>
  <tr>
    <td style="height:5px">
      </td>
    </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <td width="425">&nbsp;
            </td>
          <td class="Welcome">Welcome <?php echo $_SESSION["badgename"]; ?>
            </td>
          <td><A class="logout" HREF="logout.php">&nbsp;Logout&nbsp;</A>
            </td>
          <td width="25">&nbsp;
            </td>
          </tr>
        </table>
      </td>
    </tr>
<?php } ?>
  </table>

<H2 class="head"><?php echo $title ?></H2>
<?php } ?>
