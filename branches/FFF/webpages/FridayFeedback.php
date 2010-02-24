<?php
    require_once('db_functions.php');
    require_once('PostingHeader.php');
    require_once('PostingFooter.php');
    require_once('CommonCode.php');
    require_once('error_functions.php');

    /* Global Variables */
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $NumOfColumns=3; // Number of columns at the top of the page.
    $_SESSION['return_to_page']="FridayFeedback.php";

    /* Function to start the page correctly. */    
    function topofpage() {
        posting_header("FFF 34 Friday Feedback");
        }

    /* No matching retuned values. */
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        posting_footer();
        }

    /* This query grabs everything necessary for the schedule to be printed. */
    $query="select DISTINCT S.title, DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime), '%l:%i %p') as time ";
    $query.="FROM Sessions S JOIN Schedule SCH USING (sessionid)";
    $query.=" WHERE (typeid = 1 OR typeid = 2) AND";
    $query.=" Time_TO_SEC(SCH.starttime) < 87000";
    $query.=" ORDER BY S.title;";

    /* Standard test for failing to connect to the database. */
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	$message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }

    /* Standard test to make sure there was some information returned. */
    if (0==($elements=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }

    /* Associate the information with header_array. */
    for ($i=1; $i<=$elements; $i++) {
        $element_array[$i]=mysql_fetch_assoc($result);
        }

    /* Get the number of elements into 3 rows */
    $NumPerColumn=ceil($elements/$NumOfColumns);

    /* Printing body.  Uses the page-init from above adds informational line
       then creates the Descriptions. */
    topofpage();
    echo "<P><H3>Please, indicate the class you are offering feedback on.</H3></P>\n";
    echo "<TABLE>\n  <TR>\n    <TD>\n    <UL>\n      <UL>\n";
    for ($i=1; $i<=$elements; $i++) {
      echo sprintf("  <LI><B>%s</B> (%s)\n",$element_array[$i]['title'],$element_array[$i]['time']);
      if ($i % $NumPerColumn == 0) {
        echo "      </UL>\n    </UL>\n    </TD>\n";
        echo "    <TD>\n    <UL>\n      <UL>\n";
      }
    }
    echo "      </UL>\n    </UL>\n    </TD>\n  </TR>\n</TABLE>\n";
    echo "<hr>\n";
    echo "<P>&nbsp;&nbsp;Please answer the following questions where 1 = totally disagree, 5 = totally agree.";
    echo "<TABLE border=1>";
    echo "  <TR><TH>&nbsp;</TH><TH>Totally Disagree</TH><TH>Somewhat Disagree</TH><TH>Neutral</TH><TH>Somewhat Agree</TH><TH>Totally Agree</TH></TR>";
    echo "  <TR><TD><br>This class/panel matched the description:<br>&nbsp;</TD><TD align=center>1</TD><TD align=center>2</TD><TD align=center>3</TD><TD align=center>4</TD><TD align=center>5</TD></TR>";
    echo "  <TR><TD><br>The class/panel was fun AND educational:<br>&nbsp;</TD><TD align=center>1</TD><TD align=center>2</TD><TD align=center>3</TD><TD align=center>4</TD><TD align=center>5</TD></TR>";
    echo "  <TR><TD><br>I'd recommend the class/panel to a friend:<br>&nbsp;</TD><TD align=center>1</TD><TD align=center>2</TD><TD align=center>3</TD><TD align=center>4</TD><TD align=center>5</TD></TR>";
    echo "  <TR><TD><br>This class/panel has inspired me to try something new:<br>&nbsp;</TD><TD align=center>1</TD><TD align=center>2</TD><TD align=center>3</TD><TD align=center>4</TD><TD align=center>5</TD></TR>";
    echo "  <TR><TD><br>The presenter(s) really knew their stuff:<br>&nbsp;</TD><TD align=center>1</TD><TD align=center>2</TD><TD align=center>3</TD><TD align=center>4</TD><TD align=center>5</TD></TR>";
    echo "  <TR><TD><br>I'd recommend the presenter to a friend:<br>&nbsp;</TD><TD align=center>1</TD><TD align=center>2</TD><TD align=center>3</TD><TD align=center>4</TD><TD align=center>5</TD></TR>";
    echo "  <TR><TD><br>Bring this presenter back next year:<br>&nbsp;</TD><TD align=center>1</TD><TD align=center>2</TD><TD align=center>3</TD><TD align=center>4</TD><TD align=center>5</TD></TR>";
    echo "</TABLE></P>\n<hr>\n";
    echo "<P>Other Comments:</P>";
    ?>
</body>
</html>
