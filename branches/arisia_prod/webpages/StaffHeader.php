<?php
    function staff_header($title) {
    require_once ("javascript_functions.php");
    global $badgeid, $message;
?>
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
    <link rel="stylesheet" href="css/zambia.less" type="text/less" media="screen" />
    <!--   <link rel="stylesheet" href="ParticipantSection.css" type="text/css" /> -->
    <link rel="stylesheet" href="jquery/jquery-ui-1.8.16.custom.css" type="text/css">

    <!-- Scripts -->
	<script src="javascript/less-1.1.3.min.js" type="text/javascript"></script>
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
		<header class="row-fluid staff" id="top">
		  <div class="span9">
  			<img src="/images/logo.gif" title="Arisia logo" class="pull-left" />
  			<h1 class="pull-left wide-medium-only">Zambia<br/><span class="wide-only">The <?php echo CON_NAME; ?> Scheduling Tool</span></h1>
  		</div>
<?php if (isset($_SESSION['badgeid'])) {
	require_once('db_functions.php');
  	$queryArray["categories"] = "SELECT reportcategoryid, description FROM ReportCategories ORDER BY display_order;";
  	if (($resultXML=mysql_query_XML($queryArray))===false) {
  	    RenderError($title,$message_error);
          exit();
    }
?>
			<div class="span3" id="welcome">
				<p>Welcome, <?php echo $_SESSION['badgename']; ?></p>
				<a id="logoutButton" class="btn btn-primary pull-right" href="logout.php" title="Click to log out">Log out</a>
			</div>
		</header>
    <nav id="participantNav" class="navbar">
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
              <li class="dropdown"><a href="#sessions" class="dropdown-toggle" data-toggle="dropdown">Sessions<b class="caret"></b></a>
              	<ul class="dropdown-menu">
              		<li><a href="StaffSearchSessions.php">Search Sessions</a></li>
              		<li><a href="CreateSession.php">Create New Session</a></li>
              		<li><a href="ViewSessionCountReport.php">View Session Counts</a></li>
              		<li><a href="ViewAllSessions.php">View All Sessions</a></li>
              		<li><a href="ViewPrecis.php?showlinks=0">View Precis</a></li>
              		<li><a href="ViewPrecis.php?showlinks=1">View Precis with Links</a></li>
              		<li><a href="StaffSearchPreviousSessions.php">Import Sessions</a></li>
              	</ul>
              </li>
      		    <li class="divider-vertial"></li>
              <li class="dropdown"><a href="#sessions" class="dropdown-toggle" data-toggle="dropdown">Participants<b class="caret"></b></a>
              	<ul class="dropdown-menu">
              		<li><a href="AdminParticipants.php">Administer</a></li>
              		<li><a href="InviteParticipants.php">Invite to a Session</a></li>
              		<li><a href="StaffAssignParticipants.php">Assign to a Session</a></li>
                  <?php makeMenuItem("Send email",may_I('SendEmail'),"StaffSendEmailCompose.php",false); ?></td>
              	</ul>
              </li>
              <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports<b class="caret"></b></a>
              	<ul class="dropdown-menu">
                	<?php
                    // Generate the links to all the reports
                  	$xmlstr = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
		<xsl:output omit-xml-declaration="yes" />
		<xsl:template match="/">
			<xsl:apply-templates match="doc/query[@queryName='categories']/row" />
			<li class="divider"></li>
			<li>
				<a href="staffReportsInCategory.php?reportcategoryid=0">All reports</a>
			</li>
		</xsl:template>
		<xsl:template match="/doc/query[@queryName='categories']/row">
			<li>
				<a href="staffReportsInCategory.php?reportcategoryid={@reportcategoryid}"><xsl:value-of select="@description" /></a>
			</li>
		</xsl:template>
	</xsl:stylesheet>
EOD;
                  	$xsl = new DomDocument;
                  	$xsl->loadXML($xmlstr);
                  	$xslt = new XsltProcessor();
                  	$xslt->importStylesheet($xsl);
                  	$html = $xslt->transformToXML($resultXML);
                  	echo $html
                	?>
              	</ul>
              </li>
      		  <li class="divider-vertical"></li>
            <li><a href="MaintainRoomSched.php">Room Schedules</a></li>
      		  <li class="divider-vertical"></li>
            <li><a href="StaffPage.php">Overview</a></li>
         	  <?php makeMenuItem("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormWelcome.php",may_I('BrainstormSubmit')); ?></td>
      		  <li class="divider-vertical"></li>
             <li>
              <form method=POST action="ShowSessions.php" class="navbar-search pull-left">
                <input type="text" name="searchtitle" class="search-query" placeholder="Search for sessions by title">
                <input type="hidden" value="ANY" name="track">
                <input type="hidden" value="ANY" name="status">
                <input type="hidden" value="ANY" name="type">
                <input type="hidden" value="" name="sessionid">
                <input type="hidden" value="ANY" name="divisionid">
              </form>
            </li>
           </ul>
  				    <ul class="nav pull-right">
        		  <li class="divider-vertical"></li>
				      <li><a id="ParticipantView" href="welcome.php">Participant View</a></li>
				    </ul>
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
