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
            echo "<P class=\"alert alert-error\">$message_error</P>\n";
            }
        elseif ($message!="") {
            echo "<P class=\"alert alert-success\">$message</P>\n";
            }
?>

<FORM method="POST" action="ShowPreviousSessions.php" class="well form-inline">
  <fieldset>
    <P>Use this page to search for session records from previous cons to import to the current list of sessions.</P>
      <DIV class="row-fluid">
        <DIV class="span2">
            <LABEL for="currenttrack" class="control-label">Current Track: </LABEL>
                <SELECT name="currenttrack" class="xspan2">
                    <?php populate_select_from_table("Tracks", $SessionSearchParameters['currenttrack'], "Any", TRUE); //$table_name, $default_value, $option_0_text, $default_flag ?>
                </SELECT>
        </DIV>
        <DIV class="span2">
            <LABEL for="previoustrack" class="control-label">Obsolete Track: </LABEL>
                <SELECT name="previoustrack" class="xspan2">
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
                </SELECT>
        </DIV>
        <div class="span2">
            <LABEL class="control-label" for="previouscon">Previous Con: </LABEL>
                <SELECT name="previouscon" class="xspan2">
                    <?php populate_select_from_table("PreviousCons", $SessionSearchParameters['previouscon'], "Any", TRUE); //$table_name, $default_value, $option_0_text, $default_flag ?>
                </SELECT>
        </div>
        <div class="span2">
            <LABEL class="control-label" for="type">Type: </LABEL>
                <SELECT name="type" class="xspan2">
                    <?php populate_select_from_table("Types", $SessionSearchParameters['type'], "Any", TRUE); //$table_name, $default_value, $option_0_text, $default_flag ?>
                </SELECT>
        </div>
        <div class="span2">
            <LABEL class="control-label" for="status">Status: </LABEL>
                <SELECT name="status" class="xspan2">
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
                </SELECT>
        </div>
    </div><br />
    <DIV class="row-fluid">
            <LABEL class="control-label" for="title">Title: </LABEL>
                <INPUT type="text" name="title" size="40" value="<?php echo $SessionSearchParameters['title'];?>">
                <span class="help-inline">Enter a word or phrase for which to search. Leave blank for any.</span>
    </div><br />
    <DIV class="row-fluid">
            <label class="checkbox">
                    <INPUT type="checkbox" name="showimported" <?php echo $SessionSearchParameters['showimported']?'checked':''; ?>>
                    Include in results sessions which have been imported already.
                </label>
    </div><br />
    <DIV class="row-fluid">
        <BUTTON type="submit" class="btn btn-primary" value="search">Search</BUTTON>
    </div>
  </fieldset>
</FORM>
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
        $message_error=$query."Error querying database.";
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
    echo "<div class=\"row-fluid\"><FORM method=POST action=\"SubmitImportSessions.php\" class=\"form-horizontal\">\n";
    echo "<div class=\"clearfix\"><BUTTON type=submit class=\"btn btn-primary pull-right\" value=\"submitimport\">Import</BUTTON></div>\n";
    echo "<TABLE class=\"table-condensed\">\n";
    foreach ($result_array as $resultrowindex => $resultrow) {
        echo "<TR><TD colspan=6><hr style='margin: 0;'/></TD></TR>\n";
        echo "<TR><TD rowspan=3>&nbsp;</TD>";
        echo "<TD colspan=5><strong>".htmlspecialchars($resultrow['title'],ENT_NOQUOTES)."<strong></TD></TR>\n";
        echo "<TR><TD><LABEL class=\"checkbox\"><INPUT type=\"checkbox\" name=\"import$resultrowindex\"";
        if ($resultrow['importedsessionid']!='') {
            echo " disabled checked";
            }
        echo ">Import</LABEL>";
        echo "<INPUT type=\"hidden\" name=\"previousconid$resultrowindex\" value=\"{$resultrow['previousconid']}\">";
        echo "<INPUT type=\"hidden\" name=\"previoussessionid$resultrowindex\" value=\"{$resultrow['previoussessionid']}\"></TD>";
        echo "<TD><span class=\"label\">{$resultrow['trackname']}</span></TD>";
        echo "<TD><span class=\"label\">{$resultrow['typename']}</span></TD>";
        echo "<TD><span class=\"label\">{$resultrow['statusname']}</span></TD>";
        echo "<TD><span class=\"label label-info\">{$resultrow['previousconname']}</span></TD></TR>\n";
        echo "<TD colspan=5 class=\"padding2000\">".htmlspecialchars($resultrow['progguiddesc'],ENT_NOQUOTES)."</TD></TR>\n";
        }
    echo "<INPUT type=\"hidden\" name=\"lastrownum\" value=\"$resultrowindex\">\n";
    echo "</TABLE><hr /><div class=\"clearfix\"><BUTTON type=submit class=\"btn btn-primary pull-right \" value=\"submitimport\">Import</BUTTON></div></FORM>\n";
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
                $message_error=$query2."There was a problem because 1 row was expected to ";
                $message_error.="be inserted, but $x rows were actually inserted. ";
                rollback();
                Return(FALSE);
                }
            $sessionid=mysql_insert_id($link);
            if ($sessionid==0 || !$sessionid) { 
                $message_error=$query2."Insert id not returned as expected from previous query. ";
                rollback();
                Return(FALSE);
                }
            $query3="UPDATE PreviousSessions\n";
            $query3.="    SET importedsessionid=$sessionid WHERE\n";
        	$query3.="        previousconid=$previousconid AND\n";
        	$query3.="        previoussessionid=$previoussessionid\n";
        	$result=mysql_query($query3,$link);
            if (!$result) {
                $message_error=$query3."Error querying database.";
                rollback();
                Return(FALSE);
                }
            if (($x=mysql_affected_rows($link))!=1) { 
                $message_error=$query3."There was a problem because 1 row was expected to ";
                $message_error.="be inserted, but $x rows were actually inserted. ";
                rollback();
                Return(FALSE);
                }
           $query4="INSERT INTO SessionEditHistory\n";
           $query4.="    (sessionid, badgeid, name, email_address, timestamp, sessioneditcode, statusid, editdescription)\n";
           $query4.="    Values($sessionid, \"$badgeid\", \"$name\", \"$email\", NULL, 6, 6, NULL)\n";
           $result=mysql_query($query4,$link);
           if (!$result) {
               $message_error=$query4."Error querying database.";
               rollback();
               Return(FALSE);
               }
           if (($x=mysql_affected_rows($link))!=1) { 
               $message_error=$query4."There was a problem because 1 row was expected to ";
               $message_error.="be inserted, but $x rows were actually inserted. ";
               rollback();
               Return(FALSE);
               }
           $result=mysql_query("COMMIT",$link);
           if (!$result) {
               $message_error="COMMIT: Error querying database.";
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
