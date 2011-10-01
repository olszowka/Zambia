<?php
function SetSessionSearchParameterDefaults () {
    global $SessionSearchParameters;
    $SessionSearchParameters['currenttrack']=0;
    $SessionSearchParameters['previouscontrack']=0;
    $SessionSearchParameters['previouscon']=0;
    $SessionSearchParameters['type']=0;
    $SessionSearchParameters['status']=0;
    $SessionSearchParameters['title']='';
    $SessionSearchParameters['showimported']=FALSE;
    }

function RenderSearchPreviousSessions() {
    global $SessionSearchParameters, $message_error,$message;
    if ($message_error) {
            echo "<P class=\"errmsg\">$message_error</P>\n";
            }
        elseif ($message!="") {
            echo "<P class=\"regmsg\">$message</P>\n";
            }
?>

<P>Use this page to search for session records from previous cons to import to the current list of sessions.</P>
<FORM method=POST action="ShowPreviousSessions.php">
<SPAN class="controlwithlabel">
    <SPAN class="newlabel"><LABEL for="currenttrack">Current Track</LABEL></SPAN>
    <SPAN class="control"><SELECT name="currenttrack">
        <?php populate_select_from_table("Tracks", $SessionSearchParameters['currenttrack'], "Any", TRUE); //$table_name, $default_value, $option_0_text, $default_flag ?>
        </SELECT></SPAN>
    </SPAN>
<SPAN class="controlwithlabel">
    <SPAN class="newlabel"><LABEL for="previoustrack">Obsolete Track</LABEL></SPAN>
    <SPAN class="control"><SELECT name="previoustrack">
        <?php $query=<<<EOD
SELECT
        CONCAT(PC.previousconid,"a",PCT.previoustrackid), CONCAT(PC.previousconname,": ",PCT.trackname)
    FROM
        PreviousCons PC JOIN
	PreviousConTracks PCT USING (previousconid) LEFT JOIN
	TrackCompatibility TC USING (previousconid, previoustrackid)
    WHERE
        TC.currenttrackid IS NULL
    ORDER BY
        PC.display_order, PCT.previoustrackid
EOD;
        populate_select_from_query($query, $SessionSearchParameters['previouscontrack'], "ANY", TRUE); ?>
        </SELECT></SPAN>
    </SPAN>
<SPAN class="controlwithlabel">
    <SPAN class="newlabel"><LABEL for="previouscon">Previous Con</LABEL></SPAN>
    <SPAN class="control"><SELECT name="previouscon">
        <?php populate_select_from_table("PreviousCons", $SessionSearchParameters['previouscon'], "Any", TRUE); //$table_name, $default_value, $option_0_text, $default_flag ?>
        </SELECT></SPAN>
    </SPAN>
<SPAN class="controlwithlabel">
    <SPAN class="newlabel"><LABEL for="type">Type</LABEL></SPAN>
    <SPAN class="control"><SELECT name="type">
        <?php populate_select_from_table("Types", $SessionSearchParameters['type'], "Any", TRUE); //$table_name, $default_value, $option_0_text, $default_flag ?>
        </SELECT></SPAN>
    </SPAN>
<SPAN class="controlwithlabel">
    <SPAN class="newlabel"><LABEL for="status">Status</LABEL></SPAN>
    <SPAN class="control"><SELECT name="status">
        <?php $query=<<<EOD
SELECT
        ST.statusid, ST.statusname
    FROM
        SessionStatuses ST
    WHERE
        ST.statusid in (SELECT distinct previousstatusid from PreviousSessions)
    ORDER BY
        ST.display_order
EOD;
        populate_select_from_query($query, $SessionSearchParameters['status'], "ANY", TRUE); ?>
        </SELECT></SPAN>
    </SPAN>
<SPAN class="controlwithlabel">
    <SPAN class="newlabel"><LABEL for="title">Title</LABEL></SPAN>
    <SPAN class="control"><INPUT type="text" name="title" size="40" value="<?php echo $SessionSearchParameters['title'];?>"></SPAN>
    <SPAN class="caption"><LABEL for="title">Enter a word or phrase for which to search.  Leave blank for any.</LABEL></SPAN>
    </SPAN>
<SPAN class="controlwithlabel">
    <SPAN><INPUT type="checkbox" name="showimported" <?php echo $SessionSearchParameters['showimported']?'checked':''; ?>></SPAN>
    <SPAN><LABEL for="showimported">Include in results sessions which have been imported already.</LABEL></SPAN>
    </SPAN>
    <DIV><BUTTON type=submit value="search">&nbsp;Search&nbsp;</BUTTON></DIV>
  </FORM>
<BR>
<?php } // End of RenderSearchPreviousSessions()

function HandleSearchParameters() {
    // parse parameters for Search of previous sessions and validate them
    // return TRUE if successful, FALSE otherwise
    global $SessionSearchParameters, $message_error,$message;
    $message_error="This page is intended to be reached from a form.  One or more required Post parameters were not provided. No further processing is possible.";
    if (isset($_POST['currenttrack'])) {
            $SessionSearchParameters['currenttrack']=$_POST['currenttrack'];
            }
        else {
            Return (FALSE);
            }
    if (isset($_POST['previoustrack'])) {
            $SessionSearchParameters['previouscontrack']=$_POST['previoustrack'];
            }
        else {
            Return (FALSE);
            }
    if (isset($_POST['previouscon'])) {
            $SessionSearchParameters['previouscon']=$_POST['previouscon'];
            }
        else {
            Return (FALSE);
            }
    if (isset($_POST['type'])) {
            $SessionSearchParameters['type']=$_POST['type'];
            }
        else {
            Return (FALSE);
            }
    if (isset($_POST['status'])) {
            $SessionSearchParameters['status']=$_POST['status'];
            }
        else {
            Return (FALSE);
            }
    if (isset($_POST['title'])) {
            $SessionSearchParameters['title']=$_POST['title'];
            }
        else {
            Return (FALSE);
            }
    $SessionSearchParameters['showimported']=(isset($_POST['showimported']))?TRUE:FALSE;
    if ($SessionSearchParameters['previouscontrack']!=0) {
        sscanf($SessionSearchParameters['previouscontrack'],"%da%d",$SessionSearchParameters['previouscon2'],
            $SessionSearchParameters['previoustrack']);
        if ($SessionSearchParameters['previouscon']!=0 && 
            $SessionSearchParameters['previouscon']!=$SessionSearchParameters['previouscon2']) {
            $message_error="<I>Previous Track</I> is not from the con indicated by <I>Previous Con</I> so no results can be returned.";
            Return (FALSE);
            }
        }
    if ($SessionSearchParameters['previoustrack']!=0 && $SessionSearchParameters['currenttrack']!=0) {
        $message_error="<I>Previous Track</I> and <I>Current Track</I> are both specified so no results can be returned.";
        Return (FALSE);
        }
    $message_error='';
    Return (TRUE);
    } // End of HandleSearchParameters()
    
function PerformPrevSessionSearch () {
    global $SessionSearchParameters, $message_error,$message,$result,$link;
    $query="SELECT PS.title, PS.progguiddesc, PS.previousconid, PS.previoussessionid, PS.importedsessionid, TY.typename,";
    $query.=" PC.previousconname, SS.statusname, PCT.trackname";
    $query.=" FROM PreviousSessions PS JOIN PreviousCons PC USING (previousconid)";
    $query.=" JOIN PreviousConTracks PCT USING (previousconid, previoustrackid)";
    $query.=" JOIN Types TY USING (typeid) JOIN SessionStatuses SS ON PS.previousstatusid=SS.statusid";
    $query.=" LEFT JOIN TrackCompatibility TC USING (previousconid, previoustrackid)";
    if ($SessionSearchParameters['currenttrack']!=0 || $SessionSearchParameters['previouscontrack']!=0 ||
        $SessionSearchParameters['previouscon']!=0 || $SessionSearchParameters['type']!=0 ||
        $SessionSearchParameters['status']!=0 || $SessionSearchParameters['searchtitle']!='' ||
        !$SessionSearchParameters['showimported']) {
        $query.=" WHERE";
        }
    if ($SessionSearchParameters['currenttrack']!=0) {
        $query.=" TC.currenttrackid={$SessionSearchParameters['currenttrack']} AND";
        }
    if ($SessionSearchParameters['previouscontrack']!=0) {
            $query.=" PS.previoustrackid={$SessionSearchParameters['previoustrack']} AND";
            $query.=" PS.previousconid={$SessionSearchParameters['previouscon2']} AND";
            }
        else {
            if ($SessionSearchParameters['previouscon']!=0) {
                $query.=" PS.previousconid={$SessionSearchParameters['previouscon']} AND";
                }
            }
    if ($SessionSearchParameters['type']!=0) {
        $query.=" PS.typeid={$SessionSearchParameters['type']} AND";
        }
    if ($SessionSearchParameters['status']!=0) {
        $query.=" PS.previousstatusid={$SessionSearchParameters['status']} AND";
        }
    if ($SessionSearchParameters['title']!='') {
        $query.=" PS.title LIKE \"%".mysql_real_escape_string($SessionSearchParameters['title'])."%\" AND";
        }
    if (!$SessionSearchParameters['showimported']) {
        $query.=" PS.importedsessionid IS NULL AND";
        }
    if (substr($query,-4)==' AND') {     //take last 4 characters
        $query=substr($query,0,-4);     //drop last 4 characters
        }
    $query.=" ORDER BY PC.display_order, PS.previoustrackid";
    $result=mysql_query($query,$link);
    if (!$result) {
        $message_error=$query."<BR>Error querying database.";
        Return(FALSE);
        }
    if(mysql_num_rows($result)==0) {
        $message_error="No matching sessions found.";
        Return(FALSE);
        }
    Return(TRUE);
    } // End of PerformPrevSessionSearch()
    
function RenderSearchPrevSessionResults() {
    global $result;
    while ($result_array[]=mysql_fetch_array($result,MYSQL_ASSOC));
    array_pop($result_array);
    echo "<FORM method=POST action=\"SubmitImportSessions.php\">\n";
    echo "<BUTTON type=submit value=\"submitimport\">&nbsp;Import&nbsp;</BUTTON>\n";
    echo "<TABLE><COL><COL><COL><COL><COL><COL>\n";
    echo "<TR><TD colspan=6 class=\"border0020\">&nbsp;</TD></TR>\n";
    foreach ($result_array as $resultrowindex => $resultrow) {
        echo "<TR><TD colspan=6 class=\"border0000\">&nbsp;</TD></TR>\n";
        echo "<TR><TD rowspan=3>&nbsp;</TD>";
        echo "<TD colspan=5 class=\"emphasis\">".htmlspecialchars($resultrow['title'],ENT_NOQUOTES)."</TD></TR>\n";
        echo "<TR><TD><INPUT type=\"checkbox\" name=\"import$resultrowindex\"";
        if ($resultrow['importedsessionid']!='') {
            echo " disabled checked";
            }
        echo "><LABEL for=\"import$resultrowindex\">&nbsp;Import</LABEL>";
        echo "<INPUT type=\"hidden\" name=\"previousconid$resultrowindex\" value=\"{$resultrow['previousconid']}\">";
        echo "<INPUT type=\"hidden\" name=\"previoussessionid$resultrowindex\" value=\"{$resultrow['previoussessionid']}\"></TD>";
        echo "<TD>{$resultrow['trackname']}</TD>";
        echo "<TD>{$resultrow['typename']}</TD>";
        echo "<TD>{$resultrow['statusname']}</TD>";
        echo "<TD>{$resultrow['previousconname']}</TD></TR>\n";
        echo "<TD colspan=5 class=\"padding2000\">".htmlspecialchars($resultrow['progguiddesc'],ENT_NOQUOTES)."</TD></TR>\n";
        echo "<TR><TD colspan=6 class=\"border0020\">&nbsp;</TD></TR>\n";
        }
    echo "<INPUT type=\"hidden\" name=\"lastrownum\" value=\"$resultrowindex\">\n";
    echo "</TABLE><BUTTON type=submit value=\"submitimport\">&nbsp;Import&nbsp;</BUTTON></FORM>\n";
    }  // End of RenderSearchPrevSessionResults()
    
function ProcessImportSessions() {
    global $message, $message_error, $link;
    if (!isset($_POST['lastrownum'])) { 
        $message_error="This page is intended to be reached from a form.  One or more required ";
        $message_error.="Post parameters were not provided. No further processing is possible.";
        Return(FALSE);
        }
    get_name_and_email($name, $email); // populates them from session data or db as necessary
    $name=mysql_real_escape_string($name,$link);
    $email=mysql_real_escape_string($email,$link);
    $badgeid=mysql_real_escape_string($_SESSION['badgeid'],$link);
    $query1="START TRANSACTION";
    $success_rows=0;
    for ($i=0;$i<=$_POST['lastrownum'];$i++) { 
        if (isset($_POST["import$i"])) {
            $previousconid=mysql_real_escape_string($_POST["previousconid$i"],$link);
            $previoussessionid=mysql_real_escape_string($_POST["previoussessionid$i"],$link);
            $query2="INSERT INTO Sessions\n";
            $query2.="        (sessionid, trackid, typeid, divisionid, pubstatusid, \n"; 
        	$query2.="        languagestatusid, pubsno, title, secondtitle, pocketprogtext, \n"; 
        	$query2.="        progguiddesc, persppartinfo, duration, estatten, kidscatid, \n"; 
        	$query2.="        signupreq, roomsetid, notesforpart, servicenotes, statusid, \n";
        	$query2.="        notesforprog, warnings, invitedguest, ts) \n";
        	$query2.="    SELECT\n";
        	$query2.="            NULL sessionid, COALESCE(TC.currenttrackid, 99), PS.typeid, PS.divisionid, 2 pubstatusid, \n";
        	$query2.="            PS.languagestatusid, NULL pubsno, PS.title, PS.secondtitle, PS.pocketprogtext, \n";
        	$query2.="            PS.progguiddesc, PS.persppartinfo, PS.duration, PS.estatten, PS.kidscatid, \n"; 
        	$query2.="            PS.signupreq, 99 roomsetid, NULL notesforpart, NULL servicenotes, 6 statusid, \n"; 
        	$query2.="            PS.notesforprog, NULL warnings, PS.invitedguest, NULL ts \n";
        	$query2.="        FROM\n";
        	$query2.="            PreviousSessions PS LEFT JOIN\n";
        	$query2.="            TrackCompatibility TC USING (previousconid, previoustrackid)\n";
        	$query2.="        WHERE\n";
        	$query2.="            previousconid=$previousconid AND\n";
        	$query2.="            previoussessionid=$previoussessionid\n";
			//echo $query2;
        	$result = mysql_query_with_error_handling($query1);
            if (!$result) {
                rollback();
                Return(FALSE);
                }
	        	$result = mysql_query_with_error_handling($query2);
            if (!$result) {
                rollback();
                Return(FALSE);
                }
            if (($x=mysql_affected_rows($link))!=1) { 
                $message_error=$query2."<BR>There was a problem because 1 row was expected to ";
                $message_error.="be inserted, but $x rows were actually inserted. ";
                rollback();
                Return(FALSE);
                }
            $sessionid=mysql_insert_id($link);
            if ($sessionid==0 || !$sessionid) { 
                $message_error=$query2."<BR>Insert id not returned as expected from previous query. ";
                rollback();
                Return(FALSE);
                }
            $query3="UPDATE PreviousSessions\n";
            $query3.="    SET importedsessionid=$sessionid WHERE\n";
        	$query3.="        previousconid=$previousconid AND\n";
        	$query3.="        previoussessionid=$previoussessionid\n";
        	$result=mysql_query($query3,$link);
            if (!$result) {
                $message_error=$query3."<BR>Error querying database.";
                rollback();
                Return(FALSE);
                }
            if (($x=mysql_affected_rows($link))!=1) { 
                $message_error=$query3."<BR>There was a problem because 1 row was expected to ";
                $message_error.="be inserted, but $x rows were actually inserted. ";
                rollback();
                Return(FALSE);
                }
           $query4="INSERT INTO SessionEditHistory\n";
           $query4.="    (sessionid, badgeid, name, email_address, timestamp, sessioneditcode, statusid, editdescription)\n";
           $query4.="    Values($sessionid, \"$badgeid\", \"$name\", \"$email\", NULL, 6, 6, NULL)\n";
           $result=mysql_query($query4,$link);
           if (!$result) {
               $message_error=$query4."<BR>Error querying database.";
               rollback();
               Return(FALSE);
               }
           if (($x=mysql_affected_rows($link))!=1) { 
               $message_error=$query4."<BR>There was a problem because 1 row was expected to ";
               $message_error.="be inserted, but $x rows were actually inserted. ";
               rollback();
               Return(FALSE);
               }
           $result=mysql_query("COMMIT",$link);
           if (!$result) {
               $message_error="COMMIT<BR>Error querying database.";
               Return(FALSE);
               }
            $success_rows++;
            }
        }
    $message="$success_rows sessions(s) imported.";
    $message_error="";
    Return(TRUE);
    } // End of ProcessImportSessions()
?>
