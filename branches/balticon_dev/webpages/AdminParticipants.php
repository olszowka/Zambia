<?php
$title="Administer Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
$fbadgeid = getInt("badgeid");
//error_log("Reached AdminParticpants.");
staff_header($title);

if ($fbadgeid)
	echo"<script type=\"text/javascript\">fbadgeid = $fbadgeid;</script>\n";
	
	$queryArray["reg_types"]="SELECT regtype FROM regtypes WHERE 1;";
	
	if(($resultXML=mysql_query_XML($queryArray))===false) {
		RenderError($title, $message_error);
		exit();
		}
	
	if(($resultRegTypes = mysql_query_with_error_handling($queryArray["reg_types"]))===false) {
		RenderError($title, $message_error);
		exit();
		}
	
?>
<div style="display:none" id="searchPartsDIV">
	<div class="dialog">Enter all or part of first name, last name, badge name, <span style="font-weight:bold">or</span> published name.  If you enter numbers, it will be interpreted as a complete badgeid.
	</div>
	<div style="margin-top: 0.5em">
		<input type="text" id="searchPartsINPUT" onkeypress = "if (event.keyCode == 13) doSearchPartsBUTN();" />
		<button type="button" id="doSearchPartsBUTN">Search</button>
		<button type="button" id="cancelSearchPartsBUTN">Cancel</button>
	</div>
	<div style="margin-top: 1em; height:250px; overflow:auto; border: 1px solid grey" id="searchResultsDIV">
		&nbsp;
	</div>
</div>
<div style="display:none" id="createPartsDIV">
	<div class="dialog" id="createPartsDIVheader">Email Address and Publication Name are both required</div>
	<div style="margin-top: 0.5em">
<?php
	$optionsNode = $resultXML->createElement("options");
	$docNode = $resultXML->getElementsByTagName("doc")->item(0);
	$optionsNode = $docNode->appendChild($optionsNode);
	
	$optionsNode->setAttribute("enableParticipant",may_I('create_participant'));
	$optionsNode->setAttribute("enableAdministration",may_I('Administrator'));
	$optionsNode->setAttribute("conName", CON_NAME . " " . CON_ID);
	
	//echo($resultXML->saveXML()); // for debugging only
	
	$xsl = new DomDocument;
	$xsl->load('xsl/editParticipant.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));

?>
		<input id="partStatus" type="hidden" name="partStatus" value="on" />
		<br/>
		
		<button type="button" id="doCreatePartsBUTN">Create Participant</button>
		<button type="button" id="cancelCreatePartsBUTN">Cancel</button>
	</div>
	<div style="margin-top: 1em; height:250px; overflow:auto; border: 1px solid grey" id="createResultsDIV">
		&nbsp;
	</div>
</div>
<div style="display:none" id="unsavedWarningDIV">
	<div class="dialog">You have unsaved changes.</div>
	<button type="button" id="cancelOpenSearchBUTN" onclick="openSearchPartsBUTN('cancel');">Cancel</button>
	<button type="button" id="overrideOpenSearchBUTN" onclick="openSearchPartsBUTN('override');">Discard Changes</button>
</div>
<div class="resultBox" id="resultBoxDIV"><span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span></div>
<div style="margin-left: 1em; margin-right: 1em">
	<button type="button" id="openSearchPartsBUTN">Search for participants</button>
	<button type="button" id="createPartsBUTN">Create Participant</button>
</div>

<div class="newformdiv">
	<div class="newformlabel"><label for="badgeid" class="newformlabel">Badgeid:</label></div>
	<div class="newforminput"><input id="badgeid" type="text" readonly="readonly" size="8"/></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="lname_fname" class="newformlabel">Last name, first name:</label></div>
	<div class="newforminput"><input id="lname_fname" type="text" readonly="readonly" size="35" /></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="bname" class="newformlabel">Badge name:</label></div>
	<div class="newforminput"><input id="bname" type="text" readonly="readonly" size="12" /></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="pname" class="newformlabel">Name for publications:</label></div>
	<div class="newforminput"><input id="pname" type="text" readonly="readonly" size="35" onchange="textChange('pname');" onkeyup="textChange('pname');" /></div>
	</div><br/>

<div class="newformdiv">
	<div class="newformlabel"><label for="regtype" class="newformlabel">Reg Status:</label></div>
	<div class="newforminput"><select id="regtype" readonly="readonly" onchange="textChange('regtype');" >
		<?php
		while($row=mysql_fetch_assoc($resultRegTypes)) {
			?>
			<option><?php echo($row["regtype"]); ?></option>
			<?php
		}
		?>
	</select></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="regdepartment" class="newformlabel">Department:</label></div>
	<div class="newforminput"><input id="regdepartment" type="text" readonly="readonly" size="35" onchange="textChange('regdepartment');" onkeyup="textChange('regdepartment');" /></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="adminStatus" class="newformlabel">Admin:</label></div>
	<div class="newforminput"><input id="adminStatus" type="checkbox" <?php
	if(!may_I('Administrator')) { echo("disabled=\"true\""); }
	?> onchange="textChange('adminStatus');"/></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="staffStatus" class="newformlabel">Staff:</label></div>
	<div class="newforminput"><input id="staffStatus" type="checkbox" <?php
	if(!may_I('Administrator')) { echo("disabled=\"true\""); }
	?> onchange="textChange('staffStatus');"/>

	</div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="partStatus" class="newformlabel">Participant:</label></div>
	<div class="newforminput"><input id="partStatus" type="checkbox" disabled="true" onchange="textChange('partStatus');"/></div>
	</div><br/>
	
<div class="newformdiv" style="padding: 4px; border: 1px solid gray; width: 25em">
	<div class="newformlabel"><label for="interested" class="newformlabel">Participant is interested and available to participate in <?php echo CON_NAME; ?> programming:</label></div>
	<div class="newforminput">
		<select id="interested" class="yesno" disabled="disabled" onchange="anyChange();">
			<option value="0" selected="selected">&nbsp</option>
			<option value="1" >Yes</option>
			<option value="2" >No</option>
			</select>
		</div>
	<div class="newformnote">Changing this to no will remove the particpant from all sessions.</div>
	</div>
<div class="newformdiv" style="width: 10em">
	<div class="password2">Change Participant's Password&nbsp;</div>
	<div class="value"><INPUT type="password" size="10" id="password" readonly="readonly" onchange="anyChange();" onkeyup="anyChange();"></div>
	</div>
<div class="newformdiv" style="width: 10em">
	<div class="password2">Confirm New Password&nbsp;</div>
	<div class="value"><INPUT type="password" size="10" id="cpassword" readonly="readonly" onchange="anyChange();" onkeyup="anyChange();"></div>
	</div>
<div class="newformdiv" id="passwordsDontMatch" style="width: 10em; color: red; font-weight: bold; display:none;">Passwords don't match.
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="bio" class="newformlabel">Participant biography:</label></div>
	<div class="newforminput"><textarea id="bio" rows="4" cols="80" readonly="readonly" onchange="textChange('bio');" onkeyup="textChange('bio');"></textarea></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><label for="staffnotes" class="newformlabel">Staff notes re. participant:</label></div>
	<div class="newforminput"><textarea id="staffnotes" rows="4" cols="80" readonly="readonly" onchange="textChange('snotes');" onkeyup="textChange('snotes');"></textarea></div>
	</div>
<div class="newformdiv">
	<div class="newformlabel"><span class="newformspan">Participant roles:</span></div>
	<div class="newforminput"><div id="partRoles" class="divbox" style="height: 4em; width: 25em; overflow: auto"></div></div>
	</div>
<div style="margin-left: 1em; margin-right: 1em">
	<button class="ActionButton" type="button" id="updateBUTN" onclick="updateBUTN();" disabled="disabled">Update</button>
</div>

<?php
staff_footer();
?>