<?php
    function participant_header($title) {
    require_once ("javascript_functions.php");
    global $badgeid, $message;
?>
<!DOCTYPE html>
<html lang="en">
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
load_jquery();
load_javascript();
?>
  <div class="container-fluid">
	<!-- Header -->
		<header class="row-fluid participant" id="top">
		  <div id="regHeader" class="span12">
  		  <div class="span9">
    			<img src="images/logo.gif" title="Arisia logo" class="pull-left" />
    			<h1 class="pull-left wide-medium-only">Zambia<br/><span class="wide-only">The <?php echo CON_NAME; ?> Scheduling Tool</span></h1>
    		</div>
  <?php if (isset($_SESSION['badgeid'])) { ?>
  			<div class="span3" id="welcome">
  				<p>Welcome, <?php echo $_SESSION['badgename']; ?></p>
          <img id="hideHeader" class="imgButton pull-right" src="images/blue-up.png"/>
  				<a id="logoutButton" href="logout.php" class="btn btn-primary pull-right" title="Click to log out">Log out</a>
  			</div>
      </div>
  		<div id="altHeader" class="row-fluid">
  		  <div id="welcomeSmall">
  				<img src="images/blue-down.png" id="showHeader" class="pull-right"/>
  				<a id="logoutButton" class="btn btn-primary btn-mini pull-right" href="logout.php" title="Click to log out">Log out</a>
  				<p class="pull-right">Welcome, <?php echo $_SESSION['badgename']; ?></p>
        </div>
  		</div>
		</header>
    <nav id="participantNav" class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container" style="width: auto;">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<? echo $_SERVER['PATH_INFO'] ?>"><? echo $title ?></a>
          <div class="nav-collapse">
            <ul class="nav">
              <li><a href="my_contact.php">Profile</a></li>
        	  <?php makeMenuItem("Availability",may_I('my_availability'),"my_sched_constr.php",false); ?>
         	  <?php makeMenuItem("Session Interests",may_I('my_panel_interests'),"PartPanelInterests.php",false); ?>
		 	 <!-- XXX this should have a may_I -->
         	  <?php makeMenuItem("General Interests",1,"my_interests.php",false); ?>
         	  <?php makeMenuItem("My Schedule",may_I('my_schedule'),"MySchedule.php",false); ?>
         	  <?php makeMenuItem("Search Sessions",may_I('search_panels'),"my_sessions1.php",may_I('search_panels')); ?>
         	  <?php makeMenuItem("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormWelcome.php",may_I('BrainstormSubmit')); ?>
      		  <li class="divider-vertical"></li>
            <li><a href="welcome.php">Overview</a></li>
      		  <li class="divider-vertical"></li>
            </ul>
			<?php if (may_I('Staff')) {
				echo '<ul class="nav pull-right"><li class="divider-vertical"></li><li><a id="staffView" href="StaffPage.php">Staff View</a></li></ul>';
			}?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </nav>
<?php }
	  else {
	   require_once("loginForm.php");
?>
		</header>
<?php } ?>
<?php } ?>
