<?php
	function staffHeaderOnePage($title) {
		require_once ("javascript_functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Zambia -- <?php echo $title ?></title>
	<link rel="stylesheet" href="StaffSection.css" type="text/css" />
	<link rel="stylesheet" href="jquery/jquery-ui-1.8.16.custom.css" type="text/css" />
	<script type="text/javascript">
		var thisPage="<?php echo $title; ?>";
	</script>
<?php
	load_javascript();
	load_jquery();
?>
</head>
<body class="onepage">
<div id="fullPageContainer">
	<div class="topBand" id="fullPageHeader">
		<h1 class="appTitle">Zambia&ndash;The <?php echo CON_NAME; ?> Scheduling Tool</h1>
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
					<?php maketab("Brainstorm View",may_I('public_login'),"BrainstormWelcome.php"); ?></td>
		    </tr>
		</table>
		<table style="width: 100%">
			<tr>
				<td colspan="3" style="height:5px"></td>
			</tr>
			<tr>
				<td style="width: 35%">&nbsp;</td>
				<td>
					<span class="Welcome">Welcome <?php echo $_SESSION['badgename']; ?></span>
					<span class="logout"><a class="logout" HREF="logout.php">&nbsp;Logout&nbsp;</a></span>
				</td>
				<td style="width: 35%">&nbsp;</td>
			</tr>
		</table>
<?php } ?>
		<h2 class="pageTitle"><?php echo $title; ?></h2>
	</div>
	<div id="mainContentContainer" class="secondaryFullWidthContainer"><!-- is closed in footer function -->
<?php } ?>
