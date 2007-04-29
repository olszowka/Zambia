<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Update Session Interests";
    require ('db_functions.php'); //define database functions
    require ('data_functions.php'); //define non database functions
    require_once('ParticipantFooter.php');
    require_once('renderMySessions2.php');
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    $max_si_row=get_session_interests_from_post();
    $status=validate_session_interests($max_si_row); // $messages populated with error message.
    if ($status==false) {
        participant_header($title);
        echo "<P class=\"errmsg\">The data you entered was incorrect.  Database not updated.<BR>".$messages."</P>"; // error message
?>        
    <FORM name="addform" method=POST action="my_sessions2.php">
        <DIV class="bigbox">
            <H3>Add Session to my List</H3>
            <SPAN>Session ID</SPAN>
            <SPAN><Input type="text" name="sessionid" size=10></SPAN>
            <DIV><BUTTON class="SubmitButton" type="submit" name="add" id="add">Add</BUTTON></DIV>
            </DIV>
        </FORM>
    <HR>
    <H3>List of Sessions in Which I'm Interested in Participating</H3>
    <FORM name="sessionform" method=POST action="SubmitMySessions2.php">
        <TABLE>
            <TR>
                <TH rowspan=2 class="border2122">Session<BR>ID</TH>
                <TH class="border2111">Title</TH>
                <TH class="border2111">Rank<BR>Preference</TH>
                <TH rowspan=2 class="border2221">Delete<BR>From<BR>List</TH>
                </TR>
            <TR>    
                <TH class="border1121">Notes to Program Committee and Other Participants</TH>
                <TH class="border1121">Would Moderate</TH>
                </TR>
<?php
        for ($i=0;$i<=$max_si_row;$i++) {
            echo "        <TR>\n";
            echo "            <TD rowspan=2 id=\"sessidtcell\">".$sessInts[$i]["sessionid"]."";
                echo "<INPUT type=\"hidden\" name=\"sessionid".$i."\" value=\"".$sessInts[$i]["sessionid"]."\"></TD>\n";
            echo "            <TD>".$title."</TD>\n";
            echo "            <TD>Rank: <INPUT type=\"text\" size=5 name=\"rank".$i."\" value=\"".$sessInts[$i]["rank"]."\"></TD>\n";
            echo "            <TD rowspan=2>Delete<BR><INPUT type=\"checkbox\" value=1 name=\"delete".$i."\"></TD>\n";
            echo "            </TR>\n";
            echo "            <TD><TEXTAREA cols=50 name=\"comments".$i."\" id=\"intCmnt\">".htmlspecialchars($sessInts[$i]["comments"],ENT_COMPAT)."</TEXTAREA></TD>\n";
            echo "            <TD>Mod.:<INPUT type=\"checkbox\" value=1 name=\"mod".$i."\" ".($sessInts[$i]["mod"]?"checked":"")."></TD>\n";
            echo "            </TR>\n";
            }
?>
            </TABLE>    
        <BUTTON type="submit" name="submit" id="submit">Save</BUTTON>
        </FORM>
<?php        
        participant_footer();
        exit(0);
        }
    $query="DELETE FROM ParticipantSessionInterest WHERE badgeid=\"".$badgeid."\"";
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        RenderError($title,$message);
        exit();
        }
    for ($i=0;$i<=$max_si_row;$i++) {
        if ($sessInts[$i]["delete"]) { continue; }

        $query="INSERT INTO ParticipantSessionInterest set badgeid=\"".$badgeid."\", sessionid=".$sessInts[$i]["sessionid"].", rank=".(($sessInts[$i]["rank"]!="")?$sessInts[$i]["rank"]:"null").", willmoderate=".(($sessInts[$i]["mod"])?1:0).", comments=\"".mysql_real_escape_string($sessInts[$i]["comments"],$link)."\"";

        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.  Database not updated.";
            RenderError($title,$message);
            exit();
            }
        }
    $error=false;
    $message="Database updated successfully.";
    renderMySessions2($title, $error, $message, $badgeid);
    participant_footer();
?>

