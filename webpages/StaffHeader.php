<?php
    function staff_header($title) {
      require_once ("javascript_functions.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html xmlns="http://www.w3.org/TR/xhtml1/transitional">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=latin-1">
  <title>Zambia -- <?php echo $title ?></title>
  <link rel="stylesheet" href="StaffSection.css" type="text/css">
  <?php javascript_for_edit_session();
        mousescripts(); ?>
</head>
<body>
<H1 class="head">Zambia&ndash;The <?php echo CON_NAME; ?> Scheduling Tool</H1>
<hr>

<?php if (isset($_SESSION['badgeid'])) { ?>
  <table class="tabhead">
    <tr class="tabrow">
      <td class="tabblocks border0020">
        <?php maketab("Staff Overview",1,"StaffPage.php"); ?></td>
      <td class="tabblocks border0020">
        <?php maketab("Available Reports",1,"StaffAvailableReports.php"); ?></td>
      <td class="tabblocks border0020">
        <?php maketab("Manage Sessions",1,"StaffManageSessions.php"); ?></td>
      <td class="tabblocks border0020">
        <?php maketab("Manage Participants &amp; Schedule",1,"StaffManageParticipants.php"); ?></td>
      <td class="tabblocks border0020">
        <?php maketab("Participant View",1,"welcome.php"); ?></td>
      <td class="tabblocks border0020">
        <?php maketab("Brainstorm View",1,"BrainstormWelcome.php"); ?></td>
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
