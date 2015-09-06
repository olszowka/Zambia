<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Staff - Manage Participant Biographies";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    $query= <<<EOD
SELECT 
        BIO.bioeditstatusid, BIO.bioeditstatusname, cnt
    FROM
        BioEditStatuses BIO LEFT JOIN
        (SELECT bioeditstatusid, count(*) cnt FROM Participants WHERE interested=1 GROUP BY bioeditstatusid) SUB
    USING (bioeditstatusid)
    ORDER BY display_order;
EOD;
// Participants.interested: 1=yes; 2=no; 0=left blank; null=never hit 'save'
    if (($result=mysql_query($query,$link))===false) {
        $message=$query."<BR>\nError retrieving data from database.\n";
        RenderError($title,$message);
        exit();
        }
    staff_header($title);
    echo "<P>Report of status of Participant Biographies run ".date('d-M-Y h:i A')."</P>\n";
    $numrows=mysql_num_rows($result);
    echo "<FORM name=\"manbioform\" method=POST action=\"StaffManageBios_POST.php\">\n";
    echo "<TABLE class=\"grid\">\n";
    echo "    <TR>\n";
    echo "        <TH class=\"border1111\" >Select</TH>\n";
    echo "        <TH class=\"border1111\">Status Description</TH>\n";
    echo "        <TH class=\"border1111\">Number of<BR>Records</TH>\n";
    echo "        </TR>\n";
    for ($i=1; $i<=$numrows; $i++) {
        $reslt_row=mysql_fetch_array($result,MYSQL_ASSOC);
        echo "    <TR>\n";
        $j = $reslt_row['bioeditstatusid'];
        echo "        <TD class=\"center border1111\"><INPUT type=\"checkbox\" value=\"1\" name=\"bioid$j\"></TD>\n";
        echo "        <TD class=\"border1111\">".$reslt_row['bioeditstatusname']."</TD>\n";
        if ($reslt_row['cnt']=='') $reslt_row['cnt']=0;
        echo "        <TD class=\"center border1111\">".$reslt_row['cnt']."</TD>\n";
        echo "        </TR>\n";
        }        
    echo "    </TABLE>\n";
    echo "<P>This report is limited to participants who are currently listed as attending and interested in particpating.</P>\n";
    echo "<P>Select with which category or categories you would like to work and hit \"Work\".</P>\n";
    echo "<DIV><BUTTON class=\"SubmitButton\" type=\"submit\" name=\"Work\" id=\"Work\">Work</BUTTON></DIV>\n";
    echo "</FORM>\n";
    staff_footer();     
