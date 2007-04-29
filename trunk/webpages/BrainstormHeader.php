<?php
    $_SESSION['role'] = "Brainstorm";

    function brainstorm_header($title) {
      require_once ("javascript_functions.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html xmlns="http://www.w3.org/TR/xhtml1/transitional">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=latin-1">
  <title>Zambia -- <?php echo $title ?></title>
  <link rel="stylesheet" href="BrainstormSection.css" type="text/css">
  <meta name="keywords" content="Questionnaire">
  <?php javascript_for_edit_session(); javascript_pretty_buttons(); ?>

</head>
<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">
<H1 class="head">Zambia&ndash;The <?php echo CON_NAME; ?> Scheduling Tool</H1>
<hr>

<?php if (isset($_SESSION['badgeid'])) { ?>
  <table class="header">
    <tr>
      <td class="head"><a href="BrainstormWelcome.php">Welcome</a> </td>
      <td class="head"><a href="BrainstormReportUnseen.php">New Suggestions Report</a></td>
      <td class="head"><a href="BrainstormReportVetted.php">In Progress Suggestions Report</a></td>
      <td class="head"><a href="BrainstormReportAll.php">All Suggestions Report</a></td>
      <?php if(may_I('Participant')) { ?>
         <td class="head"><a href="welcome.php">Return To Participant View</a></td>
      <?php } ?>
      <?php if(may_I('Participant')) { ?>
         <td class="head"><a href="welcome.php">Return To Staff View</a></td>
      <?php } ?>
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
