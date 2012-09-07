<?php
    function participant_header($title) {
    require_once ("javascript_functions.php");
    global $badgeid, $message;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Zambia &ndash; <?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zambia, the Arisia scheduling tool">
    <meta name="author" content="PeterO, DDA, others">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link rel="stylesheet" href="css/zambia.css" type="text/css" media="screen" />
<!--   <link rel="stylesheet" href="ParticipantSection.css" type="text/css" /> -->

    <!-- Scripts -->
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
  </head>
<body>
<script type="text/javascript">
	var thisPage="<?php echo $title; ?>";
</script>
<?php
load_javascript();
load_jquery();
?>
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
