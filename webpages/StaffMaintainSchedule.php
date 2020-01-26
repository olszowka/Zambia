<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $fullPage, $title;
$title="Grid Scheduler";
$fullPage = true; // changes body class to support all content restricted to screen size
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
	<div class="flex-row-container" style="position: absolute; top:0; bottom: 0; left:0; height: auto; width: 280px; margin: 2px 1px 2px 2px; border: 1px solid black"><!-- ### LEFT COLUMN ###-->
		<div id="tabs" class="flex-row-fixed flex-row-container" style="border-bottom:1px solid black;height:40%;">
			<div id="tabs-bar" class="flex-row-fixed">
				<ul>
					<li><a id="tabs-rooms-link" href="#tabs-rooms">Rooms</a></li>
					<li><a id="tabs-sessions-link" href="#tabs-sessions">Sessions</a></li>
					<li><a id="tabs-warnings-link" href="#tabs-warnings">Warnings</a></li>
					<li><a id="tabs-info-link" href="#tabs-info">Info</a></li>
				</ul>
			</div>
            <div id="tabs-content-wrapper" class="flex-row-remainder-wrapper">
                <div id="tabs-rooms" class="tabs-content overflow-y-container">
                    <?php getRoomsForScheduler(); ?>	
                </div>
                <div id="tabs-sessions" class="tabs-content overflow-y-container">
                    <div style="height: 28px">
                        <div style="display:inline-block; width:75px"><label for="trackSEL">Track:</label></div>
                        <span class="newformselectspan">
                            <select id="trackSEL" name="track" class="newformselect"><?php populate_select_from_table("Tracks",0,"ANY",true); ?></select>
                        </span>
                    </div>
                    <div style="height: 28px">
                        <div style="display:inline-block; width:75px"><label for="typeSEL">Type:</label></div>
                        <span class="newformselectspan">
                            <select id="typeSEL" name="type" class="newformselect"><?php populate_select_from_table("Types",0,"ANY",true); ?></select>
                        </span>
                    </div>
                    <div style="height: 28px">
                        <div style="display:inline-block; width:75px"><label for="divisionSEL">Division:</label></div>
                        <span class="newformselectspan">
                            <select id="divisionSEL" name="division" class="newformselect"><?php populate_select_from_table("Divisions",0,"ANY",true); ?></select>
                        </span>
                    </div>
                    <div style="height: 28px">
                        <div style="display:inline-block; width:75px"><label for="sessionIdINP">Session ID:</label></div>
                        <span class="newforminputspan">
                            <input id="sessionIdINP" name="sessionid" class="newforminputtight" />
                        </span>
                    </div>
                    <div style="height: 28px">
                        <div style="display:inline-block; width:75px"><label for="titleINP">Title:</label></div>
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
                <div id="tabs-warnings" class="tabs-content overflow-y-container">
                </div>
                <div id="tabs-info" class="tabs-content overflow-y-container">
                </div>
            </div>
		</div>
		<div style="margin:2px;height:59.7%;" class="flex-row-fixed flex-row-container">
            <div class="flex-row-fixed onepage-titlebar">
                <div class="onepage-title">Sessions to be scheduled</div>
            </div>
            <div style="padding: 3px; text-align:center;" class="flex-row-fixed">
                <span style="padding:0;"><button id="clearAllButton" class="btn">Clear All</button></span>
                <span style="padding:0;"><button id="swapModeCheck" class="btn" mychecked="false">Swap Mode</button></span>
                <img id="fileCabinetIMG" style="display:inline; vertical-align:middle;" height="65" width="49" src="images/FileCabinetClosed.png" onmouseover="staffMaintainSchedule.fileCabinetSwap(true);"
                    onmouseout="staffMaintainSchedule.fileCabinetSwap(false);" alt="drop here to archive" />
            </div>
            <div id="sessions-to-be-scheduled-wrapper" class="flex-row-remainder">
                <div id = "sessions-to-be-scheduled-container" style="border: 3px solid white;" ></div>
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

