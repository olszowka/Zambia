<?php
//	Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
function staff_header($title, $is_report = false, $reportColumns = false, $reportAdditionalOptions = false) {
    require_once ("PageHeader.php");
    global $fullPage, $header_used;
    $header_used = HEADER_STAFF;
    page_header($title);
?>
<body <?php if ($fullPage) echo "class =\"fullPage\""; ?>>
	<div <?php if ($fullPage) echo "id=\"fullPageContainer\""; ?> class="container-fluid">
	<div id="myhelper"></div><!-- used for drag-and-drop operations -->
	<?php if ($fullPage) echo "<div id=\"headerContainer\">"; ?>
	<!-- Header -->
		<header class="row-fluid staff" id="top">
		  <div id="regHeader" class="span12">
  			<div class="span9">
  				<h1 class="pageHeader pull-left">
  					<img id="zambiaLogo" src="images/Z_illuminated.jpg" alt="Illuminated Z" class="zambiaLogo zambiaImage wide-only" />
  					<div class="pageHeaderText span9"> Zambia<span class="wide-medium-only">: The <?php echo CON_NAME; ?> Scheduling Tool</span></div>
  				</h1>
  			</div>
  <?php if (isset($_SESSION['badgeid'])) { ?>
  			<div class="span3" id="welcome">
  				<p>Welcome, <?php echo $_SESSION['badgename']; ?></p>
          <img id="hideHeader" class="imgButton pull-right" src="images/green-up.png" alt="Shrink header to a thin strip" title="Shrink header to a thin strip"/>
  				<a id="logoutButton" class="btn btn-primary pull-right" href="logout.php" title="Click to log out">Log out</a>
  			</div>
      </div>
  		<div id="altHeader" class="row-fluid">
  		  <div id="welcomeSmall">
  				<img src="images/green-down.png" id="showHeader" class="pull-right" alt="Expand header to normal size" title="Expand header to normal size"/>
  				<a id="logoutButton" class="btn btn-primary btn-mini pull-right" href="logout.php" title="Click to log out">Log out</a>
  				<p class="pull-right">Welcome, <?php echo $_SESSION['badgename']; ?></p>
        </div>
  		</div>
		</header>

<?php
		if ($fullPage) echo "</div>"; //close headerContainer 
 		} // if badgeid was set
	else {
			require_once("loginForm.php");
			echo "<script type=\"text/javascript\">";
			echo "   var alwaysShowLargeHeader = true;";
			echo "</script>";
?>
		</header>
<?php
		if ($fullPage) echo "</div>"; //close headerContainer 
		}
	}
?>
