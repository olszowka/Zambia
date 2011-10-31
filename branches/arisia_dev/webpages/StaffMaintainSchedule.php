<?php
global $onePage;
$title="Edit Schedule";
$onePage = true; // changes body class to support all content restricted to screen size
require_once('db_functions.php');
require_once('StaffHeaderOnePage.php');
require_once('StaffFooterOnePage.php');
require_once('StaffCommonCode.php');
require_once('StaffMaintainSchedule_FNC.php');
//require_once('SubmitAdminParticipants.php');

staffHeaderOnePage($title);
?>
	<!-- start off contained in main content div which is full width and most of the height (and is constrained in height and width) -->
	<div style="position: absolute; top:0; bottom: 0; left:0; height: auto; width: 350px; margin: 2px 1px 2px 2px; border: 1px solid black"><!-- ### LEFT COLUMN ###-->
		<div id="tabs" style="position: absolute; top:0; left:0; right:0; height: 40%; width: auto; border-bottom: 1px solid black">
			<div id="tabsBar" style="position: absolute; top:0; left:0; right:0; width: auto; ">
				<ul>
					<li><a href="#tabs-1">Rooms</a></li>
					<li><a href="#tabs-2">Sessions</a></li>
					<li><a href="#tabs-3">Messages</a></li>
				</ul>
			</div>
			<div id="tabsContent" style="position: absolute; right:0; left:0; bottom:0; height: auto; width:auto">
				<div id="tabs-1" style="position: absolute; top:0; right:0; bottom: 0; left: 0; height: auto; width: auto; overflow:auto;">
					<?php getRoomsForScheduler(); ?>	
				</div>
				<div id="tabs-2">
					<p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut Mauris consectetur tortor et purus.</p>
				</div>
				<div id="tabs-3">
					<p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel , lacus.</p>
					<p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla</p> 
				</div>
			</div>
		</div>
		<div style="position: absolute; left:0; right:0; bottom:0; top: 40%; width:auto; height: auto;margin: 2px">
			<div style="position: absolute; left:0; right:0; top:0; width:auto;">
				<div class="onepage-titlebar">
					<span class="onepage-title">Sessions to be scheduled</span>
				</div>
				<div style="height: 32px; padding: 3px; text-align:center">
					<span style="padding-left:10px;padding-right:5px"><span id="clearAllButton">Clear All</span></span>
					<span style="padding-left:5px;padding-right:10px"><input type="checkbox" id="swapModeCheck"/><label for="swapModeCheck">Swap Mode</label></span>
				</div>
			</div>
			<div style="position: absolute; left:0; right:0; bottom:0; top: 150px; height: auto; width:auto; ">
				<div id="sessionsToBeScheduled" style="height: 100%; overflow:auto">&nbsp;</div>
			</div>
		</div>
	</div>
	<div style="position: absolute; top:0; bottom: 0; left:352px; right: 0; height: auto; width: auto; margin: 2px 2px 2px 1px; border: 1px solid black">
		<div class = "fullBlockContainer" style="background-color: #e0c0c0; margin: 2px">&nbsp;</div>
	</div>
<?php
staffFooterOnePage();
?>
