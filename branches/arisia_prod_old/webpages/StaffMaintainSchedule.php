<?php
global $fullPage;
$title="Grid Scheduler";
$fullPage = true; // changes body class to support all content restricted to screen size
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
//require_once('StaffHeaderOnePage.php');
//require_once('StaffFooterOnePage.php');
require_once('StaffCommonCode.php');
require_once('StaffMaintainSchedule_FNC.php');

staff_header($title);
//staffHeaderOnePage($title);
?>
	<!-- start off contained in #fullPageContainer div which is full width and full height -->
<div id="mainContentContainer" class="secondaryFullWidthContainer">
	<div style="position: absolute; top:0; bottom: 0; left:0; height: auto; width: 280px; margin: 2px 1px 2px 2px; border: 1px solid black"><!-- ### LEFT COLUMN ###-->
		<div id="tabs" style="position: absolute; top:0; left:0; right:0; height: 40%; width: auto; border-bottom: 1px solid black">
			<div id="tabsBar" style="position: absolute; top:0; left:0; right:0; width: auto; ">
				<ul>
					<li><a id="tabs-rooms-link" href="#tabs-rooms">Rooms</a></li>
					<li><a id="tabs-sessions-link" href="#tabs-sessions">Sessions</a></li>
					<li><a id="tabs-warnings-link" href="#tabs-warnings">Warnings</a></li>
					<li><a id="tabs-info-link" href="#tabs-info">Info</a></li>
				</ul>
			</div>
			<div id="tabsContent" style="position: absolute; right:0; left:0; bottom:0; height: auto; width:auto">
				<div id="tabs-rooms" style="position: absolute; top:0; right:0; bottom: 0; left: 0; height: auto; width: auto; overflow:auto;">
					<?php getRoomsForScheduler(); ?>	
				</div>
				<div id="tabs-sessions" style="position: absolute; top:0; right:0; bottom: 0; left: 0; height: auto; width: auto; overflow:auto; margin: 2px">
					<div style="height: 28px">
						<div style="display:inline-block; width:75px"><label for="track">Track:</label></div>
						<span class="newformselectspan">
							<select id="trackSEL" name="track" class="newformselect"><?php populate_select_from_table("Tracks",0,"ANY",true); ?></select>
						</span>
					</div>
					<div style="height: 28px">
						<div style="display:inline-block; width:75px"><label for="type">Type:</label></div>
						<span class="newformselectspan">
							<select id="typeSEL" name="type" class="newformselect"><?php populate_select_from_table("Types",0,"ANY",true); ?></select>
						</span>
					</div>
					<div style="height: 28px">
						<div style="display:inline-block; width:75px"><label for="division">Division:</label></div>
						<span class="newformselectspan">
							<select id="divisionSEL" name="division" class="newformselect"><?php populate_select_from_table("Divisions",0,"ANY",true); ?></select>
						</span>
					</div>
					<div style="height: 28px">
						<div style="display:inline-block; width:75px"><label for="sessionid">Session ID:</label></div>
						<span class="newforminputspan">
							<input id="sessionIdINP" name="sessionid" class="newforminputtight" />
						</span>
					</div>
					<div style="height: 28px">
						<div style="display:inline-block; width:75px"><label for="title">Title:</label></div>
						<span class="newforminputspan">
							<input id="titleINP" name="title" class="newforminputtight" style="width:160px; margin-left:2px" />
						</span>
					</div>
					<div style="height: 28px">
						<div style="display:inline-block; width:75px">&nbsp;</div>
						<span class="newformnotetight">Leave blank for "any".</span>
					</div>
					<div style="text-align: center">
						<button id="retrieveSessionsBUT" type="button" class="btn btn-primary" >Retrieve</button>
						<!--<div id="retrieveSessionsBUT">Retrieve</div>-->
						<button id="resetSessionsSearchBUT" type="button" class="btn" >Reset Search</button>
						<!--<div id="resetSessionsSearchBUT">Reset Search</div>-->
					</div>
					<div id="noSessionsFoundMSG" style="text-align: center; font-weight:bold; color:red; display:none; margin-top:3px">
						No new sessions matched.
					</div>
				</div>
				<div id="tabs-warnings" style="position: absolute; top:0; right:0; bottom: 0; left: 0; height: auto; width: auto; overflow:auto; margin: 2px">
				</div>
				<div id="tabs-info" style="position: absolute; top:0; right:0; bottom: 0; left: 0; height: auto; width: auto; overflow:auto;">
				</div>
			</div>
		</div>
		<div style="position: absolute; left:0; right:0; bottom:0; top: 40%; width:auto; height: auto;margin: 2px">
			<div style="position: absolute; left:0; right:0; top:0; width:auto;">
				<div style="position: absolute; left:0; right:0; width: auto; top:0;" class="onepage-titlebar">
					<span class="onepage-title">Sessions to be scheduled</span>
				</div>
				<div style="position: absolute; left:0; right:0; width: auto; top:19px; padding: 3px; text-align:center;">
					<span style="padding:0;"><button id="clearAllButton" class="btn">Clear All</button></span>
					<span style="padding:0;"><button id="swapModeCheck" class="btn" mychecked="false">Swap Mode</button></span>
					<img id="fileCabinetIMG" style="display:inline; vertical-align:middle" height="65" width="49" src="images/FileCabinetClosed.png" onmouseover="staffMaintainSchedule.fileCabinetSwap(true);"
						onmouseout="staffMaintainSchedule.fileCabinetSwap(false);" />
				</div>
			</div>
			<div id = "sessionsToBeSchedContainer" style="position: absolute; left:0; right:0; bottom:0; top: 90px; height: auto; width:auto; border: 3px solid white">
				<div id="sessionsToBeScheduled" style="height: 100%; overflow-y:auto; overflow-x: hidden;">&nbsp;</div>
			</div>
		</div>
	</div>
	<div style="position: absolute; top:0; bottom: 0; left:282px; right: 0; height: auto; width: auto; margin: 2px 2px 2px 1px; border: 1px solid black">
		<div id="scheduleGridContainer" class = "fullBlockContainer" style="margin: 2px; overflow:auto">&nbsp;</div>
	</div>
</div>
</div><!-- closes #fullPageContainer -->
</body>
</html>

