<?php
    global $participant,$message_error,$message2,$congoinfo;
    require ('StaffCommonCode.php');
    require ('StaffEditBios_FNC.php');
    $title='Select Biographies to Edit';
    $postkeys=array_keys($_POST);
    if (isset($_POST['badgeid'])) { // then coming here from edit individual biography
            $badgeid=$_POST['badgeid'];
            $edited_bio=mysql_real_escape_string(stripslashes($_POST['edited_bio']),$link);
            $scndlangbio=mysql_real_escape_string(stripslashes($_POST['scndlangbio']),$link);
            $bioeditstatus=stripslashes($_POST['bioeditstatus']);
            $query = <<<EOD
UPDATE Participants
    SET
        editedbio = '$edited_bio',
        scndlangbio = '$scndlangbio',
        bioeditstatusid = $bioeditstatus
    WHERE
        badgeid='$badgeid';
EOD;
            if (($result=mysql_query($query,$link))===false) {
                $message=$query."<BR>\nError updating database.\n";
                RenderError($title,$message);
                exit();
                }
            unlock_participant($badgeid);
            if (isset($_SESSION['bioid_list'])) {
                    $bioid_list=$_SESSION['bioid_list'];
                    }
                else {
                    $bioid_list='-1';
                    }
            }
        else {
            $c=count($postkeys);
            if ($c<2) {
                $message="Internal problem parsing post variables.<BR>\n";
                RenderError($title,$message);
                exit();
                }
            $bioid_list="";
            for ($i=0; $i<$c; $i++) {
                if ((substr($postkeys[$i],0,5)=="bioid") and ($_POST[$postkeys[$i]])) {
                    $bioid_list.=substr($postkeys[$i],5,999).",";
                    }
                }
            $bioid_list=substr($bioid_list,0,-1); // drop trailing comma
            $_SESSION['bioid_list']=$bioid_list;
            }
    $query = <<<EOD
SELECT IF(P.pubsname!="",P.pubsname,CD.badgename) name, BES.bioeditstatusname, ST.pubsname lockedby, P.badgeid
    FROM
            Participants P
        join
            BioEditStatuses BES
        using (bioeditstatusid)
        join
            CongoDump CD
        using (badgeid)
        left join
            Participants ST
        on P.biolockedby = ST.badgeid
    WHERE
        P.interested = 1 and
        P.bioeditstatusid in ($bioid_list)
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message=$query."<BR>\nError retrieving data from database.\n";
        RenderError($title,$message);
        exit();
        }
    staff_header($title);
    $numrows=mysql_num_rows($result);
    if ($numrows==0) {
        echo "<P>There are no biographies to edit which match your selection.</P>\n";
        echo "<P><A HREF=\"StaffManageBios.php\">Back to Manage Biographies</A></P>\n";
        staff_footer();
        exit();
        }
    echo "<P>Click on the participant name in the table below to edit his biography.</P>\n";
    echo "<TABLE class=\"grid\">\n";
    echo "    <TR>\n";
    echo "        <TH class=\"border1111\">Participant</TH>\n";
    echo "        <TH class=\"border1111\">Status of Biography</TH>\n";
    echo "        <TH class=\"border1111\">Currently being edited by</TH>\n";
    echo "        </TR>\n";
    for ($i=1; $i<=$numrows; $i++) {
        $result_array=mysql_fetch_assoc($result);
        echo "    <TR>\n";
        $p=htmlspecialchars($result_array['name']);
        $b=$result_array['badgeid'];
        echo "        <TD class=\"border1111\"><A HREF=\"StaffEditBios.php?badgeid=$b\">$p</A></TD>\n";
        echo "        <TD class=\"border1111\">".$result_array['bioeditstatusname']."</TD>\n";
        echo "        <TD class=\"border1111\">".htmlspecialchars($result_array['lockedby'])."</TD>\n";
        echo "        </TR>\n";
        }
    echo "    </TABLE>\n";
    staff_footer();
?>

