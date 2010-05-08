<?php
    require_once('Constants.php');
    require_once('data_functions.php');
    require_once('db_functions.php');
    require_once('render_functions.php');
    require_once('validation_functions.php');
    require_once('php_functions.php');

    //set_session_timeout();
    session_start();
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($title,$message_error);
        exit();
        };
    if (isLoggedIn()==false and !isset($logging_in)) {
	    $message="Session expired. Please log in again.";
	    require ('login.php');
	    exit();
    };

    // function to generate a clickable tab.
    // 'text' contains the text that should appear in the tab.
    // 'usable' indicates whether the tab is usable.
    //
    // if the tab is usable, its background and foreground color will
    // be determined by the 'usabletab' class.  when the mouse is over the tab
    // the background and foreground colors of the tab will be determined
    // by the 'mousedovertab' class.
    //
    // if the tab is not usable, the tab will use class 'unusabletab'
    
    Function maketab($text,$usable,$url) {
	if ($usable) {
		echo '<SPAN class="usabletab" onmouseover="mouseovertab(this)" onmouseout="mouseouttab(this)">';
		echo '<IMG class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
		echo '<A HREF="' . $url . '">' ;// XXX link needs to be quoted
		echo $text;                     // XXX needs to be quoted
		echo '<IMG class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
		echo '</SPAN>';
	    }
	else {
		echo '<SPAN class="unusabletab">';
		echo '<IMG class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
		echo $text;                     // XXX needs to be quoted
		echo '<IMG class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
		echo '</SPAN>';
	    }
    }

/* functions to put the headers in place.  Probably should be generalized more,
   than specifically pre-scripting it, the way we do. */

function posting_header($title) {
  $ConName=CON_NAME; // make it a variable so it can be substituted
  $HeaderTemplateFile="../HeaderTemplate.html";

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  if ((file_exists($HeaderTemplateFile)) and ($title != "Sessions Grid")) {
    readfile($HeaderTemplateFile);
    } else {
    echo "  <link rel=\"stylesheet\" href=\"Common.css\" type=\"text/css\">\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<H1 class=\"head\">The information for $ConName</H1>\n";
    echo "<hr>\n\n";
    echo "<H2 class=\"head\">$title</H2>\n";
    }
  }


function staff_header($title) {
  require_once ("javascript_functions.php");
  $ConName=CON_NAME; // make it a variable so it can be substituted

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"StaffSection.css\" type=\"text/css\">\n";
  javascript_for_edit_session();
  mousescripts();
  echo "</head>\n";
  echo "<body>\n";
  echo "<H1 class=\"head\">Zambia&ndash;The $ConName Scheduling Tool</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "  <table class=\"tabhead\">\n    <tr class=\"tabrow\">\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Staff Overview",1,"StaffPage.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Available Reports",1,"StaffAvailableReports.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Manage Sessions",1,"StaffManageSessions.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Manage Participants &amp; Schedule",1,"StaffManageParticipants.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Participant View",1,"welcome.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Brainstorm View",may_I('public_login'),"BrainstormWelcome.php");
    echo "</td>\n    </tr>\n  </table>\n";
    echo "<table class=\"header\">\n  <tr>\n    <td style=\"height:5px\">\n      </td>\n    </tr>\n";
    echo "  <tr>\n    <td>\n      <table width=\"100%\">\n";
    echo "        <tr>\n          <td width=\"425\">&nbsp;\n            </td>\n";
    echo "          <td class=\"Welcome\">Welcome ";
    echo $_SESSION['badgename'];
    echo "            </td>\n";
    echo "          <td><A class=\"logout\" HREF=\"logout.php\">&nbsp;Logout&nbsp;</A>\n            </td>\n";
    echo "          <td width=\"25\">&nbsp;\n            </td>\n          </tr>\n        </table>\n";
    echo "      </td>\n    </tr>\n";
    }
  echo "  </table>\n\n<H2 class=\"head\">$title</H2>\n";
  }


function participant_header($title) {
  require_once ("javascript_functions.php");
  global $badgeid;
  $ConName=CON_NAME; // make it a variable so it can be substituted

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"ParticipantSection.css\" type=\"text/css\">\n";
  mousescripts();
  echo "</head>\n";
  echo "<body>\n";
  echo "<H1 class=\"head\">Zambia&ndash;The $ConName Scheduling Tool</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "<table class=\"tabhead\">\n";
    echo "  <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>\n";
    echo "  <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>\n";
    echo "  <tr class=\"tabrow\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Welcome", 1, "welcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("My Availability",may_I('my_availability'),"my_sched_constr.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("My Panel Interests",may_I('my_panel_interests'),"PartPanelInterests.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    echo "<!-- XXX this should have a may_I -->\n       ";
    maketab("My General Interests",1,"my_interests.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if (may_I('Staff')) { 
      maketab("Staff View",may_I('Staff'),"StaffPage.php"); 
      }
    echo "</td>\n  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020 smallspacer\">&nbsp;";
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    echo "<!-- XXX this should have a may_I -->\n       ";
    maketab("My Profile",1,"my_contact.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Search Panels",may_I('search_panels'),"my_sessions1.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("My Schedule",may_I('my_schedule'),"MySchedule.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormWelcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020 smallspacer\">&nbsp;";
    echo "</td>\n  </tr>\n</table>\n";
    echo "<table class=\"header\">\n  <tr>\n    <td style=\"height:5px\"></td>\n  </tr>\n";
    echo "  <tr>\n    <td>\n      <table width=\"100%\">\n";
    echo "        <tr>\n          <td width=\"425\">&nbsp;</td>\n";
    echo "          <td class=\"Welcome\">Welcome ";
    echo $_SESSION['badgename'];
    echo "            </td>\n";
    echo "          <td><A class=\"logout\" HREF=\"logout.php\">&nbsp;Logout&nbsp;</A></td>\n";
    echo "          <td width=\"25\">&nbsp;</td>\n        </tr>\n      </table>\n";
    echo "    </td>\n  </tr>\n";
    }
  echo "</table>\n\n<H2 class=\"head\">$title</H2>\n";
  }

function brainstorm_header($title) {
  require_once ("javascript_functions.php");
  $ConName=CON_NAME; // make it a variable so it can be substituted

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"BrainstormSection.css\" type=\"text/css\">\n";
  echo "  <meta name=\"keywords\" content=\"Questionnaire\">\n";
  javascript_for_edit_session();
  javascript_pretty_buttons();
  mousescripts();
  echo "</head>\n";
  echo "<body leftmargin=\"0\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"0\">\n";
  echo "<H1 class=\"head\">Zambia&ndash;The $ConName Scheduling Tool</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "<table class=\"tabhead\">\n";
    echo "  <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>\n";
    echo "  <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>\n";
    echo "  <tr class=\"tabrow\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Welcome",1,"BrainstormWelcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormCreateSession.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Search Sessions",1,"BrainstormSearchSession.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if(may_I('Participant')) { 
      maketab("Participants View",may_I('Participant'),"welcome.php"); 
      }
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if(may_I('Staff')) { 
      maketab("Staff View",may_I('Staff'),"StaffPage.php");
      }
    echo"  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020\" colspan=10>\n         View sessions proposed to date:</td>\n  </tr>";
    echo "</td>\n  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("All Proposals",1,"BrainstormReportAll.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("New (Unseen)",1,"BrainstormReportUnseen.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Reviewed",1,"BrainstormReportReviewed.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Likely to Occur",1,"BrainstormReportLikely.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Scheduled",1,"BrainstormReportScheduled.php");
    echo "</td>\n  </tr>\n</table>\n";
    echo "<table class=\"header\">\n  <tr>\n    <td style=\"height:5px\"></td>\n  </tr>\n";
    echo "  <tr>\n    <td>\n      <table width=\"100%\">\n";
    echo "        <tr>\n          <td width=\"425\">&nbsp;</td>\n";
    echo "          <td class=\"Welcome\">Welcome ";
    echo $_SESSION['badgename'];
    echo "            </td>\n";
    echo "          <td><A class=\"logout\" HREF=\"logout.php\">&nbsp;Logout&nbsp;</A></td>\n";
    echo "          <td width=\"25\">&nbsp;</td>\n        </tr>\n      </table>\n";
    echo "    </td>\n  </tr>\n";
    }
  echo "</table>\n\n<H2 class=\"head\">$title</H2>\n";
  }

function posting_footer() {
  $ProgramEmail=PROGRAM_EMAIL; // make it a variable so it can be substituted
  $FooterTemplateFile="../FooterTemplate.html";

  if (file_exists($FooterTemplateFile)) {
    readfile($FooterTemplateFile);
    } else {
    echo "<hr>\n<P>If you have questions or wish to communicate an idea, please contact ";
    echo "<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.\n</P>";
    }
  include ('google_analytics.php');
  echo "\n\n</body>\n</html>\n";
  }

function staff_footer() {
  $ProgramEmail=PROGRAM_EMAIL; // make it a variable so it can be substituted

  echo "<hr>\n<P>If you would like assistance using this tool or you would like to communicate an";
  echo " idea that you cannot fit into this form, please contact ";
  echo "<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.\n</P>";
  include ('google_analytics.php');
  echo "\n\n</body>\n</html>\n";
  }

function participant_footer() {
  $ProgramEmail=PROGRAM_EMAIL; // make it a variable so it can be substituted

  echo "<hr>\n<P>If you need help or to tell us something that doesn't fit here, please email ";
  echo "<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.\n</P>"; 
  include('google_analytics.php');
  echo "\n\n</body>\n</html>\n";
  }

function brainstorm_footer() {
  $ProgramEmail=PROGRAM_EMAIL; // make it a variable so it can be substituted
  $BrainstormEmail=BRAINSTORM_EMAIL;

  echo "<hr>\n<P>If you would like assistance using this tool, please contact ";
  echo "<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.  ";
  echo "If you would like to communicate an idea that you cannot fit into this form, please contact ";
  echo "<A HREF=\"mailto:$BrainstormEmail\">$BrainstormEmail</A>.</P>";
  include('google_analytics.php');
  echo "\n\n</body>\n</html>\n";
  }

//Top of page reporting, simplified by the pieces above for HTML pages
function topofpagereport($title,$description,$info) {
  if ($_SESSION['role'] == "Brainstorm") {
    require_once('BrainstormCommonCode.php');
    brainstorm_header($title);
  }
  elseif ($_SESSION['role'] == "Participant") {
    require_once('PartCommonCode.php');
    participant_header($title);
  }
  elseif ($_SESSION['role'] == "Staff") {
    require_once('StaffCommonCode.php');
    staff_header($title);
  }
  elseif ($_SESSION['role'] == "Posting") {
    require_once('PostingCommonCode.php');
    posting_header($title);
  }
  date_default_timezone_set('US/Eastern');
  echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
  echo $description;
  echo $info;
  
}

//Top of page reporting, simplified by the pieces above for CSV pages
function topofpagecsv($filename) {
  header("Expires: 0");
  header("Cache-control: private");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Description: File Transfer");
  header("Content-Type: text/csv");
  header("Content-disposition: attachment; filename=$filename");
}

// Produce the HTML body version of the information gathered in tables.
function renderhtmlreport($rows,$header_array,$element_array) {
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="<TH>";
    $headers.=$header_name;
    $headers.="</TH>\n";
  }
  echo "<TABLE BORDER=1>";
  echo "<TR>" . $headers . "</TR>";
  for ($i=1; $i<=$rows; $i++) {
    echo "<TR>";
    foreach ($header_array as $header_name) {
      echo "<TD>";
      echo $element_array[$i][$header_name];
      echo "</TD>\n";
    }
    echo "</TR>\n";
  }
  echo "</TABLE>";
  if ($_SESSION['role'] == "Brainstorm") {
    brainstorm_footer();
  }
  elseif ($_SESSION['role'] == "Participant") {
    participant_footer();
  }
  elseif ($_SESSION['role'] == "Staff") {
    staff_footer();
  }
  elseif ($_SESSION['role'] == "Posting") {
    posting_footer();
  }
}

// Produce the CSV body version of the information gathered in tables.
function rendercsvreport($rows,$header_array,$element_array) {
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="\"";
    $headers.=$header_name;
    $headers.="\",";
  }
  $headers = substr($headers, 0, -1);
  echo "$headers\n";
  for ($i=1; $i<=$rows; $i++) {
    $rowinfo="";
    foreach ($header_array as $header_name) {
      $rowinfo.="\"";
      $rowinfo.=$element_array[$i][$header_name];
      $rowinfo.="\",";
    }
    $rowinfo=substr($rowinfo, 0, -1);
    echo "$rowinfo\n";
  }
}

// Pull the informaiton requested by the queries
function queryreport($query,$link,$title,$description) {
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
  if (0==($rows=mysql_num_rows($result))) {
    $message="$description\n<P>This report retrieved no results matching the criteria.</P>\n";
    RenderError($title,$message);
    exit();
  }
  for ($i=1; $i<=$rows; $i++) {
    $element_array[$i]=mysql_fetch_assoc($result);
  }
  $header_array=array_keys($element_array[1]);
  return array ($rows,$header_array,$element_array);
}

?>
