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
    $_SESSION['return_to_page']="SaturdayFeedback.php";

    /* Function to start the page correctly. */    
    function topofpage() {
        posting_header("Saturday Feedback");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
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
    $query.=" Time_TO_SEC(SCH.starttime) > 100000 AND";
    $query.=" Time_TO_SEC(SCH.starttime) < 200000";
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
    echo "Questions go here:";
    posting_footer();
