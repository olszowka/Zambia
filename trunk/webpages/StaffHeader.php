<?php
    $_SESSION['role'] = "Staff";

    function staff_header($title) {
      require_once ("javascript_functions.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html xmlns="http://www.w3.org/TR/xhtml1/transitional">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=latin-1">
  <title>Zambia -- <?php echo $title ?></title>
  <link rel="stylesheet" href="StaffSection.css" type="text/css">
  <meta name="keywords" content="Questionnaire">
  <meta name="description" content="Form to request information from potential program participants">
  <?php javascript_for_edit_session(); javascript_pretty_buttons(); ?>

</head>
<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0"><!--onload="MM_preloadImages('images/my_contact-active.png','images/my_constraint-active.png','images/search_sessions-active.png','images/my_session_interests-active.png')"-->
<H1 class="head">Zambia&ndash;The <?php echo CON_NAME; ?> Scheduling Tool</H1>
<hr>

<?php if (isset($_SESSION['badgeid'])) { ?>
  <table class="header">
    <tr>
      <!--<td id="head"><a href="my_contact.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('my_contact','','images/my_contact-active.png',1)"><img src="images/my_contact.png" name="my_contact" border="0" height="14" width="122" alt="My Contact Info"></a></td> -->
      <td class="head"><a href="StaffAvailableReports.php">Available Reports</a></td>
      <td class="head"><a href="StaffManageSessions.php">Manage Sessions</a></td>
      <td class="head"><a href="StaffManageParticipants.php">Manage Participants &amp; Schedule</a></td>
      <td class="head"><a href="welcome.php">Participant View</a></td>
    </tr>
  </table>
<table class="header">
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
          <td class="Welcome">Welcome <?php echo $_SESSION['badgename']; ?>
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
