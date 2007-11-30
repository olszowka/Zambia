<?php

    function participant_header($title) {
    global $badgeid;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html xmlns="http://www.w3.org/TR/xhtml1/transitional">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=latin-1">
  <title>Zambia -- <?php echo $title ?></title>
  <link rel="stylesheet" href="ParticipantSection.css" type="text/css">
</head>
<body>
<H1 class="head">Zambia&ndash;The <?php echo CON_NAME; ?> Scheduling Tool</H1> 
<hr>

<?php if (isset($_SESSION['badgeid'])) { ?>
  <table class="tabhead">
    <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>
    <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>
    <tr class="tabrow">
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("Welcome", 1, "welcome.php"); ?> </td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("My Availability",may_I('my_availability'),"my_sched_constr.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("My Panel Interests",may_I('my_panel_interests'),"my_sessions2.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
 	 <!-- XXX this should have a may_I -->
         <?php maketab("My General Interests",1,"my_interests.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php if (may_I('Staff')) { 
                     maketab("Staff View",may_I('Staff'),"StaffPage.php"); 
               }?></td>
      </tr><tr><td class="tabblocks border0020 smallspacer">&nbsp;</td>
      <td class="tabblocks border0020" colspan=2>
 	 <!-- XXX this should have a may_I -->
         <?php maketab("My Profile",1,"my_contact.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("Search Panels",may_I('search_panels'),"my_sessions1.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("My Schedule",may_I('my_schedule'),"MySchedule.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormWelcome.php"); ?></td>
      <td class="tabblocks border0020 smallspacer">&nbsp;</td>
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
