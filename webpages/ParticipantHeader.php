<?php
    function participant_header($title) {
    require_once ("javascript_functions.php");
    global $badgeid;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Zambia -- <?php echo $title ?></title>
  <link rel="stylesheet" href="ParticipantSection.css" type="text/css" />

</head>
<body>
<?php load_javascript(); ?>
<h1 class="head">Zambia&ndash;The <?php echo CON_NAME; ?> Scheduling Tool</h1> 
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
         <?php maketab("My Panel Interests",may_I('my_panel_interests'),"PartPanelInterests.php"); ?></td>
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
          <td><a class="logout" HREF="logout.php">&nbsp;Logout&nbsp;</a>
            </td>
          <td width="25">&nbsp;
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
<?php } ?>

<h2 class="head"><?php echo $title ?></h2>
<?php } ?>
