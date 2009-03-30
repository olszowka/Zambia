<?php
    global $participant,$message_error,$message2,$congoinfo;
    require ('StaffCommonCode.php');
    require ('StaffEditBios_FNC.php');
    $title='Edit Participant Biographies';
    //print_r($_GET);
    if (!isset($_GET['badgeid'])) {
        $message="Required argument 'badgeid' missing from URL.<BR>\n";
        RenderError($title,$message);
        exit ();
        }
    $badgeid=$_GET['badgeid'];
    $lockresult=lock_participant($badgeid); // returns true if succeeded, false if failed
    $query = <<<EOD
SELECT
        bio, editedbio, scndlangbio, bioeditstatusid, biolockedby,
        if(pubsname!="",pubsname,badgename) name 
    FROM 
        Participants P JOIN CongoDump CD USING (badgeid)
    WHERE
        P.badgeid='$badgeid'
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message=$query."<BR>\nError retrieving data from database.\n";
        RenderError($title,$message);
        exit();
        }
    $participant_info_array=mysql_fetch_assoc($result);
    $query = <<<EOD
SELECT
        bioeditstatusid, bioeditstatusname
    FROM
        BioEditStatuses
    ORDER BY
        display_order
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message=$query."<BR>\nError retrieving data from database.\n";
        RenderError($title,$message);
        exit();
        }
    $numeditstatuses=mysql_num_rows($result);
    for ($i=0; $i<$numeditstatuses; $i++) {
        $bioeditstatuses_array[$i]=mysql_fetch_assoc($result);
        }
    if ($lockresult==false) {
        $editor_badgeid=$participant_info_array['badgeid'];
        $query="SELECT if(pubsname!=\"\",pubsname,badgename) name FROM Participants P JOIN CongoDump CD USING ('badgeid')";
        $query.=" WHERE P.badgeid=\'$editor_badgeid\'";
        if (($result=mysql_query($query,$link))===false) {
            $message=$query."<BR>\nError retrieving data from database.\n";
            RenderError($title,$message);
            exit();
            }
        $editor_name=mysql_result($result,0);
        echo "<P class=\"warning\">This biography is currently being edited by $editor_name.</P>\n";
        }
    staff_header($title);
    echo "<FORM name=\"bioeditform\" method=POST action=\"StaffManageBios_POST.php\">\n";
    $participant_name=htmlspecialchars($participant_info_array['name']);
    $orig_bio=htmlspecialchars($participant_info_array['bio']);
    $edited_bio=htmlspecialchars($participant_info_array['editedbio']);
    $scndlangbio=htmlspecialchars($participant_info_array['scndlangbio']);
    echo "<INPUT type=hidden name=\"badgeid\" value=\"$badgeid\"\n";
    echo "<H3 style=\"text.align:center\">$participant_name</H3>\n";
    echo "<LABEL for=\"orig_bio\">Original Biography:</LABEL><BR>\n";
    echo "<TEXTAREA readonly name=\"orig_bio\" rows=8 cols=72 >$orig_bio</TEXTAREA><BR><BR>\n";
    echo "<LABEL for=\"edited_bio\">Edited Biography:</LABEL><BR>\n";
    echo "<TEXTAREA name=\"edited_bio\" rows=8 cols=72>$edited_bio</TEXTAREA><BR><BR>\n";
    echo "<LABEL for=\"scndlangbio\">".SECOND_BIOGRAPHY_CAPTION."</LABEL><BR>\n";
    echo "<TEXTAREA name=\"scndlangbio\" rows=8 cols=72>$scndlangbio</TEXTAREA><BR><BR>\n";
    echo "<LABEL for=\"bioeditstatus\">Status of this participant's biography:</LABEL><BR>\n";
    for ($i=0; $i<$numeditstatuses; $i++) {
        $statid=$bioeditstatuses_array[$i]['bioeditstatusid'];
        echo "<INPUT type=\"radio\" name=\"bioeditstatus\" id=\"bioeditstatus\" value=\"$statid\"";
        if ($statid==$participant_info_array['bioeditstatusid']) {echo " checked";}
        echo ">";
        echo "&nbsp;".$bioeditstatuses_array[$i]['bioeditstatusname']."<BR>\n";
        }
    echo "<DIV class=\"submit\">\n";
    echo "<DIV id=\"submit\"><BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save</BUTTON></DIV>\n";
    echo "</DIV>\n";
    echo "</FORM>\n";
    staff_footer();
?>

