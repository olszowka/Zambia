<?php
require_once('Constants.php');
require_once('data_functions.php');
require_once('db_functions.php');
require_once('render_functions.php');
require_once('validation_functions.php');
require_once('php_functions.php');
require_once('error_functions.php');

global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$ConKey=CON_KEY; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

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


// Establish the con info
$query= <<<EOF
SELECT
    conname,
    constartdate,
    connumdays,
    conurl,
    conlogo,
    condefaultduration,
    condurationminutes,
    congridspacer
  FROM
      $ReportDB.ConInfo
  WHERE
      conid=$ConKey
EOF;

// Retrieve query fail if database can't be found, and if there isn't just one result
if (($result=mysql_query($query,$link))===false) {
  $message_error="Error retrieving data from database<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
if (0==($rows=mysql_num_rows($result))) {
  $message_error.="Database query did not return any rows.<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
if (1<($rows=mysql_num_rows($result))) {
  $message_error.="Too many results found.<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}

$ConInfo_array=mysql_fetch_assoc($result);
$ConInfo_array_keys=array_keys($ConInfo_array);
foreach ($ConInfo_array_keys as $element) {
  if ($_SESSION["$element"]=="") {$_SESSION["$element"]=$ConInfo_array["$element"];}
}

define("CON_NAME",$ConInfo_array['conname']);
define("CON_START_DATIM",$ConInfo_array['constartdate']);
define("CON_NUM_DAYS",$ConInfo_array['connumdays']);
define("CON_URL",$ConInfo_array['conurl']);
define("CON_LOGO",$ConInfo_array['conlogo']);
define("DEFAULT_DURATION",$ConInfo_array['condefaultduration']);
define("DURATION_IN_MINUTES",$ConInfo_array['condurationminutes']);
define("GRID_SPACER",$ConInfo_array['congridspacer']);
global $daymap;
for ($i=0;$i<CON_NUM_DAYS;$i++) {
  $today=strtotime(CON_START_DATIM)+(86400 * $i); // 86400 seconds in a day
  $daymap['long'][$i+1]=strftime("%A",$today);
  $daymap['short'][$i+1]=strftime("%a",$today);
 }

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
  $ConUrl=CON_URL; // make it a variable so it can be substituted
  $HeaderTemplateFile="../Local/HeaderTemplate.html";

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  if (file_exists($HeaderTemplateFile)) {
    readfile($HeaderTemplateFile);
    echo "<H1 class=\"head\" align=\"center\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
    echo "<hr>\n\n";
    echo "<H2 class=\"head\" align=\"center\">$title</H2>\n";
  } else {
    echo "  <link rel=\"stylesheet\" href=\"Common.css\" type=\"text/css\">\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<H1 class=\"head\">The information for $ConName</H1>\n";
    echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
    echo "<hr>\n\n";
    echo "<H2 class=\"head\">$title</H2>\n";
  }
}

function staff_header($title) {
  require_once ("javascript_functions.php");
  $ConName=CON_NAME; // make it a variable so it can be substituted
  $ConUrl=CON_URL; // make it a variable so it can be substituted

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
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "  <table class=\"tabhead\">\n    <tr class=\"tabrow\">\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Staff Overview",1,"StaffPage.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Available Reports",1,"genindex.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Manage Sessions",1,"StaffManageSessions.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Manage Participants &amp; Schedule",1,"StaffManageParticipants.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Printing",1,"PreconPrinting.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("TimeCards",1,"VolunteerCheckIn.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Participant View",1,"welcome.php");
    if (may_I('Vendor')) {
      echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
      maketab("Vendor View",1,"VendorWelcome.php");
      }
    if (may_I('public_login')) {
      echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
      maketab("Brainstorm View",1,"BrainstormWelcome.php");
    }
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
  //  echo "Permissions: ".print_r($_SESSION['permission_set'])."\n";
}

function participant_header($title) {
  require_once ("javascript_functions.php");
  global $badgeid;
  $ConName=CON_NAME; // make it a variable so it can be substituted
  $ConUrl=CON_URL; // make it a variable so it can be substituted

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
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
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
    maketab("My General Interests",may_I('my_gen_int_write'),"my_interests.php");
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
    maketab("Submit a Proposal",may_I('my_suggestions_write'),"MyProposals.php");
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
  $ConUrl=CON_URL; // make it a variable so it can be substituted

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
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "<table class=\"tabhead\">\n";
    echo "  <col width=8%><col width=8%><col width=8%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=10%><col width=10%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=8%>\n";
    echo "  <tr class=\"tabrow\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Welcome",1,"BrainstormWelcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormCreateSession.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Search Sessions",1,"BrainstormSearchSession.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Suggest a Presenter",may_I('BrainstormSubmit'),"BrainstormSuggestPresenter.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if(may_I('Participant')) { 
      maketab("Participants View",may_I('Participant'),"welcome.php"); 
    }
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if(may_I('Staff')) { 
      maketab("Staff View",may_I('Staff'),"StaffPage.php");
    }
    echo"  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020\" colspan=12>\n         View sessions proposed to date:</td>\n  </tr>";
    echo "</td>\n  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("All Proposals",1,"BrainstormReport.php?status=all");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("New (Unseen)",1,"BrainstormReport.php?status=unseen");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Reviewed",1,"BrainstormReport.php?status=reviewed");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Likely to Occur",1,"BrainstormReport.php?status=likely");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=4>\n       ";
    maketab("Scheduled",1,"BrainstormReport.php?status=scheduled");
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

function vendor_header($title) {
  require_once ("javascript_functions.php");
  $ConName=CON_NAME; // make it a variable so it can be substituted
  $ConUrl=CON_URL; // make it a variable so it can be substituted

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
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "<table class=\"tabhead\">\n";
    echo "  <col width=8%><col width=8%><col width=8%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=10%><col width=10%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=8%>\n";
    echo "  <tr class=\"tabrow\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Welcome",1,"VendorWelcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("List",1,"VendorSearch.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if (may_I('Vendor')) { 
      maketab("Update",may_I('Vendor'),"VendorSubmitVendor.php"); 
    } else {
      maketab("New Vendor",may_I('BrainstormSubmit'),"VendorSubmitVendor.php");
    }
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Apply",may_I('vendor_apply'),"VendorApply.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if (may_I('Staff')) { 
      maketab("Staff View",may_I('Staff'),"StaffPage.php");
    }
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
  $FooterTemplateFile="../Local/FooterTemplate.html";

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

function vendor_footer() {
  $VendorEmail=VENDOR_EMAIL; // make it a variable so it can be substituted

  echo "<hr>\n<P>If you would like assistance using this tool, please contact ";
  echo "<A HREF=\"mailto:$VendorEmail\">$VendorEmail</A>.  ";
  include('google_analytics.php');
  echo "\n\n</body>\n</html>\n";
}

/* Top of page reporting, simplified by the foo_header functions
 for HTML pages.  It takes the title, description and any
 additional information, and puts it all in the right place
 depending on the SESSION variable.*/
function topofpagereport($title,$description,$info) {
  if ($_SESSION['role'] == "Brainstorm") {
    brainstorm_header($title);
  }
  elseif ($_SESSION['role'] == "Vendor") {
    vendor_header($title);
  }
  elseif ($_SESSION['role'] == "Participant") {
    participant_header($title);
  }
  elseif ($_SESSION['role'] == "Staff") {
    staff_header($title);
  }
  elseif ($_SESSION['role'] == "Posting") {
    posting_header($title);
  }
  date_default_timezone_set('US/Eastern');
  echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
  echo $description;
  echo $info;
}

/* Top of page reporting, for CSV pages.  It takes only the filename
 as an input, and spits out the CSV headers. */
function topofpagecsv($filename) {
  header("Expires: 0");
  header("Cache-control: private");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Description: File Transfer");
  header("Content-Type: text/csv");
  header("Content-disposition: attachment; filename=$filename");
}

/* Footer choice, for html pages.  Select the correct footer,
 dependant on role.  This could probably just have the above footer
 functions, rolled into this, for simplicity sake. */
function correct_footer() {
  if ($_SESSION['role'] == "Brainstorm") {
    brainstorm_footer();
  }
  elseif ($_SESSION['role'] == "Vendor") {
    vendor_footer();
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

/* Produce the HTML body version of the information gathered in tables.
 It takes 4 inputs, the number of rows, the header array, the elements
 that go into the table, and if this table is the last thing on a page.
 the switch for the close on how it is called doesn't quite work yet,
 and might want to be simplified out, depending on the calling page to
 do the right thing, dropping it to 3 variables. */
function renderhtmlreport($startrows,$endrows,$header_array,$element_array) {
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="<TH>";
    $headers.=$header_name;
    $headers.="</TH>\n";
  }
  $htmlstring ="<TABLE BORDER=1>";
  $htmlstring.="<TR>" . $headers . "</TR>";
  for ($i=$startrows; $i<=$endrows; $i++) {
    $htmlstring.="<TR>";
    foreach ($header_array as $header_name) {
      $htmlstring.="<TD>";
      $htmlstring.=$element_array[$i][$header_name];
      $htmlstring.="</TD>\n";
    }
    $htmlstring.="</TR>\n";
  }
  $htmlstring.="</TABLE>";
  return($htmlstring);
}

/* Produce the CSV body version of the information gathered in tables.
 It takes in three variables, the number of rows, the header array,
 and the elements that go in the table.  It then strips out all of the
 unwanted characters (html tags, extraneous returns, and other bits)
 and outputs the comma seperated information.*/
function rendercsvreport($startrows,$endrows,$header_array,$element_array) {
  $headers="";
  $spacestr=array('\\n','\n','\\r','\r','&nbsp;');
  $newstr=array(' ',' ',' ',' ',' ');
  foreach ($header_array as $header_name) {
    $headers.="\"";
    $headers.=strip_tags(trim(str_replace($spacestr,$newstr,$header_name)));
    $headers.="\",";
  }
  $headers = substr($headers, 0, -1);
  $csvstring ="$headers\n";
  for ($i=$startrows; $i<=$endrows; $i++) {
    $rowinfo="";
    foreach ($header_array as $header_name) {
      $rowinfo.="\"";
      $rowinfo.=strip_tags(trim(str_replace($spacestr,$newstr,$element_array[$i][$header_name])));
      $rowinfo.="\",";
    }
    $rowinfo=substr($rowinfo, 0, -1);
    $csvstring.="$rowinfo\n";
  }
  return($csvstring);
}

/* This function presumes multiple calls on the same array informaition.
 It takes in 4 elements, the start and end row for a table, of the series
 of tables, the headers which go in every table, and the full array, from
 which the subset is used.  It then prints them nicely. */
function rendergridreport($startrows,$endrows,$header_array,$element_array) {
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="<TH class=\"border2222\">";
    $headers.=$header_name;
    $headers.="</TH>\n";
  }
  $gridstring="<P><TABLE cellspacing=0 border=1 class=\"border1111\">";
  $gridstring.="<TR>" . $headers . "</TR>";
  for ($i=$startrows; $i<=$endrows; $i++) {
    $gridstring.="<TR>";
    foreach ($header_array as $header_name) {
      $gridstring.=$element_array[$i][$header_name];
    }
    $gridstring.="</TR>\n";
  }
  $gridstring.="</TABLE></P>";
  return($gridstring);
}


/* Pull the information from the databas for a report.  This should be
 checked with, and possibly unified with other functions in db_functions
 file.  It takes the query and link to do the pull, title and description
 in case there is an error, or just no information, and a reportid, so
 the report can be edited if there is a query error in the report. */
function queryreport($query,$link,$title,$description,$reportid) {
  mysql_query("SET group_concat_max_len = 9216;",$link);
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    if ($reportid !=0) {
      $message.="Edit Report <A HREF=EditReport.php?selreport=$reportid>$reportid</A></P>\n<P>";
    }
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
  if (0==($rows=mysql_num_rows($result))) {
    $header_array[0]="This report retrieved no results matching the criteria.";
    $element_array[$header_array[0]]='';
    $rows=0;
    return array ($rows,$header_array,$element_array);
    /* $message="$description\n<P>This report retrieved no results matching the criteria.</P>\n";
    RenderError($title,$message);
    exit(); */
  }
  for ($i=1; $i<=$rows; $i++) {
    $element_array[$i]=mysql_fetch_assoc($result);
  }
  $header_array=array_keys($element_array[1]);
  return array ($rows,$header_array,$element_array);
}

/* Show a list of participants to select from, generated from all participants.
 Each list is ordered by the sorting key, for html-based and visual-based
 searching. */
function select_participant ($selpartid, $limit, $returnto) {
  $conid=$_SESSION['conid'];
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  global $link;

/* Get all the Permission Roles */
  $query = <<<EOD
SELECT
    permrolename,
    notes
  FROM
      $ReportDB.PermissionRoles
  WHERE
    permroleid > 1
EOD;

  list($permrole_rows,$permrole_header_array,$permrole_array)=queryreport($query,$link,"Broken Query - select_participant - PermissionRoles",$query,0);

  // Empty Title Switch to begin with.
  $TitleSwitch="";

  /* Attempt to establish default graph based on permissions */
  for ($i=1; $i<=$permrole_rows; $i++) {
    if (may_I($permrole_array[$i]['permrolename'])) {
      $permrolecheck_array[]="'".$permrole_array[$i]['permrolename']."'";
    }
  }

  $additional_permission_array=array('SuperProgramming', 'SuperLiaison', 'Liaison');

  foreach ($additional_permission_array as $perm) {
    if (may_I($perm)) {
      $permrolecheck_array[]="'Participant'";
    }
  }
  $permrolecheck_string=implode(",",$permrolecheck_array);

  if ($limit!='') {
    $query=<<<EOD
SELECT
    interestedtypeid
  FROM
      $ReportDB.InterestedTypes
  WHERE
    interestedtypename=$limit
EOD;

    list($interested_rows,$interested_header_array,$interested_array)=queryreport($query,$link,"Broken Query - select_participant - InterestedTypes",$query,0);
  
    // should only return one thing
    $interestedtypeid=$interested_array[1]['interestedtypeid'];
    if ($interestedtypeid=='') {
      $message.=$query." Returned an empty array";
      RenderError("Broken Query - select_participant - InterestedTypes", $message);
      exit;
    }
    $limittables="    JOIN $ReportDB.Interested I USING (badgeid)\n";
    $limitwhere="    interestedtypeid=$interestedtypeid AND\n    I.conid=$conid AND\n";
  } else {
    $limittables='';
    $limitwhere='';
  }

  // lastname, firstname (badgename/pubsname) - partid
  $query0=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(lastname,', ',firstname,' (',badgename,'/',pubsname,') - ',badgeid) AS pname
  FROM 
      $ReportDB.Participants 
    JOIN $ReportDB.CongoDump USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
    $limittables
  WHERE
    $limitwhere
    permrolename in ($permrolecheck_string) AND
    UHPR.conid=$conid
  ORDER BY
    lastname
EOD;

  // firstname lastname (badgename/pubsname) - partid
  $query1=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(firstname,' ',lastname,' (',badgename,'/',pubsname,') - ',badgeid) AS pname
  FROM
      $ReportDB.Participants
    JOIN $ReportDB.CongoDump USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
    $limittables
  WHERE
    $limitwhere
    permrolename in ($permrolecheck_string) AND
    UHPR.conid=$conid
  ORDER BY
    firstname
EOD;

  // pubsname/badgename (lastname, firstname) - partid
  $query2=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(pubsname,'/',badgename,' (',lastname,', ',firstname,') - ',badgeid) AS pname
  FROM
      $ReportDB.Participants
    JOIN $ReportDB.CongoDump USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
    $limittables
  WHERE
    $limitwhere
    permrolename in ($permrolecheck_string) AND
    UHPR.conid=$conid
  ORDER BY
    pubsname
EOD;

  // Now give the choices
  echo "<FORM name=\"selpartform\" method=POST action=\"".$returnto."\">\n";
  echo "<DIV><LABEL for=\"partidl\">Select Participant (Lastname)</LABEL>\n";
  echo "<SELECT name=\"partidl\">\n";
  populate_select_from_query($query0, $selpartid, "Select Participant (Lastname)", true);
  echo "</SELECT></DIV>\n";
  echo "<DIV><LABEL for=\"partidf\">Select Participant (Firstname)</LABEL>\n";
  echo "<SELECT name=\"partidf\">\n";
  populate_select_from_query($query1, $selpartid, "Select Participant (Firstname)", true);
  echo "</SELECT></DIV>\n";
  echo "<DIV><LABEL for=\"partidp\">Select Participant (Pubsname) </LABEL>\n";
  echo "<SELECT name=\"partidp\">\n";
  populate_select_from_query($query2, $selpartid, "Select Participant (Pubsname)", true);
  echo "</SELECT></DIV>\n";
  echo "<P>&nbsp;\n";
  echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
  echo "</FORM>\n";
}

/* Generic insert takes five variables: link, title, Table, array of elements, array of values */
function submit_table_element ($link, $title, $table, $element_array, $value_array) {
  foreach ($element_array as $element) {$element_string.=mysql_real_escape_string(stripslashes($element)).",";}
  foreach ($value_array as $value) {$value_string.="'".mysql_real_escape_string(stripslashes($value))."',";}
  $element_string=substr($element_string,0,-1);
  $value_string=substr($value_string,0,-1);
  $query= "INSERT INTO $table ($element_string) VALUES ($value_string)";
  if (!mysql_query($query,$link)) {
    $message_error=$query."<BR>Error updating $table.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }
  $message="Table $table updated successfully.<BR>";
  return($message);
}

/* Generic update takes six variables: link, title, Table, paired array of updates,
 which field to match on, and value of the match. */
function update_table_element ($link, $title, $table, $pairedvalue_array, $match_field, $match_value) {
  foreach ($pairedvalue_array as $pairedvalue) {$pairedvalue_string.=$pairedvalue.",";}
  $pairedvalue_string=substr($pairedvalue_string,0,-1);
  $query="UPDATE $table set $pairedvalue_string where $match_field = '$match_value'";
  if (!mysql_query($query,$link)) {
    $message_error=$query."<BR>Error updating $table.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }
  $message="Table $table updated successfully.<BR>";
  return($message);
}

/* unfrom/refrom fix so that queries can be set as values in the various database entries */
function unfrom ($transstring) {
   $badfrom = array("FROM", "From", "from");
   $goodfrom = array("UMFRAY", "Umfray", "umfray");
   return str_replace ($badfrom, $goodfrom, $transstring);
   }

function refrom ($transstring) {
   $badfrom = array("FROM", "From", "from");
   $goodfrom = array("UMFRAY", "Umfray", "umfray");
   return str_replace ($goodfrom, $badfrom, $transstring);
   }

/* Used to add a note on a participant as part of flow, and allowing for participant change. */
function submit_participant_note ($note, $partid) {
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  global $link;

  $query = "INSERT INTO $ReportDB.NotesOnParticipants (badgeid,rbadgeid,note,conid) VALUES ('";
  $query.=$partid."','";
  $query.=$_SESSION['badgeid']."','";
  $query.=mysql_real_escape_string($note)."','";
  $query.=$_SESSION['conid']."')";
  if (!mysql_query($query,$link)) {
    $message=$query."<BR>Error updating database with note.  Database not updated.";
    echo "<P class=\"errmsg\">".$message."\n";
    return;
  }
  $message="Database updated successfully with note.<BR>";
  echo "<P class=\"regmsg\">".$message."\n";
}

/* Pull the notes for a participant, in reverse order. */
// I'm no longer sure why the below is here ...
//"SELECT PR.pubsname, PB.pubsname, N.timestamp, N.note FROM NotesOnParticipants N, $ReportDB.Participants PR, $ReportDB.Participants PB WHERE N.rbadgeid=PR.badgeid AND N.badgeid=PB.badgeid;
function show_participant_notes ($partid) {
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  global $link;

  $query = <<<EOD
SELECT
    timestamp as 'When',
    pubsname as 'Who',
    note as 'What Was Done',
    conid as "Con"
  FROM
      $ReportDB.NotesOnParticipants N
      JOIN $ReportDB.Participants P ON N.rbadgeid=P.badgeid
  WHERE
    N.badgeid=$partid
  ORDER BY
    timestamp DESC
EOD;
  list($rows,$header_array,$notes_array)=queryreport($query,$link,"Notes on Participant","","");
  echo renderhtmlreport(1,$rows,$header_array,$notes_array);
  correct_footer();
}

/* create_participant and edit_participant functions.  Need more doc. */
function create_participant ($participant_arr,$permrole_arr) {
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  global $link;

  // Get the various length limits
  $limit_array=getLimitArray();

  // Get a set of bioinfo, not for the info, but for the arrays.
  $bioinfo=getBioData($_SESSION['badgeid']);

  // Test constraints.

  // Bios test moved into the add bios loop.

  // Too short/long name.
  $error_status=false;
  if (isset($limit_array['min']['web']['name'])) {
    $namemin=$limit_array['min']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < $namemin) OR
	(strlen($participant_arr['badgename']) < $namemin) OR
	(strlen($participant_arr['pubsname']) < $namemin)) {
      $message_error="All name fields are required and minimum length is $namemin characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }
  if (isset($limit_array['max']['web']['name'])) {
    $namemax=$limit_array['max']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) > $namemax) OR
	(strlen($participant_arr['badgename']) > $namemax) OR
	(strlen($participant_arr['pubsname']) > $namemax)) {
      $message_error="All name fields are required and maximum length is $namemax characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }

  // Invalid email address.
  if (!is_email($participant_arr['email'])) {
    $message_error="Email address: ".$participant_arr['email']." is not valid.  <BR>\n";
    return array ($message,$message_error);
  }

  // Get next possible badgeid.
  // WAS: "SELECT MAX(badgeid) FROM $ReportDB.Participants WHERE badgeid>='1'";
  $query = "SELECT badgeid FROM $ReportDB.Participants ORDER BY ABS(badgeid) DESC LIMIT 1";
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error="Unrecoverable error updating database.  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  if (mysql_num_rows($result)!=1) {
    $message_error="Database query returned unexpected number of rows(1 expected).  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  $maxbadgeid=mysql_result($result,0);
  //error_log("Zambia: SubmitEditCreateParticipant.php: maxbadgeid: $maxbadgeid");
  sscanf($maxbadgeid,"%d",$x);
  $newbadgeid=sprintf("%d",$x+1); // convert to num; add 1; convert back to string

  // Create Participants entry.
  $element_array = array('badgeid', 'password', 'bestway', 'altcontact', 'prognotes', 'pubsname');
  $value_array=array($newbadgeid,
                     $participant_arr['password'],
                     $participant_arr['bestway'],
                     htmlspecialchars_decode($participant_arr['altcontact']),
                     htmlspecialchars_decode($participant_arr['prognotes']),
		     htmlspecialchars_decode($participant_arr['pubsname']));
  $message.=submit_table_element($link, $title, "$ReportDB.Participants", $element_array, $value_array);

  // Add "Interested" if exists
  if (isset($participant_arr['interested']) AND ($participant_arr['interested']!='')) {
    $query ="UPDATE $ReportDB.Interested SET ";
    $query.="interestedtypeid=".$participant_arr['interested']." ";
    $query.="WHERE badgeid=\"".$newbadgeid."\" AND conid=".$_SESSION['conid'];
    if (!mysql_query($query,$link)) {
      $message.=$query."<BR>Error updating Interested table.  Database not update.";
      echo "<P class=\"errmsg\">".$message."</P>\n";
      return;
    }
    ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
    if ($r_matched[1]==0) {
      $element_array=array('conid','badgeid','interestedtypeid');
      $value_array=array($_SESSION['conid'], $newbadgeid, mysql_real_escape_string(stripslashes($participant_arr['interested'])));
      $message.=submit_table_element($link,$title,"$ReportDB.Interested", $element_array, $value_array);
    } elseif ($r_matched[1]>1) {
      $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
    }
  }

  // Add Bios.
  /* We are only updating the raw bios here, so only a 2-depth
   search happens on biolang and biotypename. */
  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {

      // Setup for keyname, to collapse all three variables into one passed name.
      $biotype=$bioinfo['biotype_array'][$i];
      $biolang=$bioinfo['biolang_array'][$j];
      // $biostate=$bioinfo['biostate_array'][$k];
      $keyname=$biotype."_".$biolang."_".$biostate."_bio";

      // Length-check the values.
      $biotext=stripslashes(htmlspecialchars_decode($participant_arr[$keyname]));
      if ((isset($limit_array['max'][$biotype]['bio'])) and (strlen($biotext)>$limit_array['max'][$biotype]['bio'])) {
	$message.=ucfirst($biostate)." ".ucfirst($biotype)." (".$biolang.") Biography";
	$message.=" too long (".strlen($biotext)." characters), the limit is ".$limit_array['max'][$biotype]['bio']." characters.";
       } elseif ((isset($limit_array['min'][$biotype]['bio'])) and (strlen($biotext)<$limit_array['min'][$biotype]['bio'])) {
	$message.=ucfirst($biostate)." ".ucfirst($biotype)." (".$biolang.") Biography";
	$message.=" too short (".strlen($biotext)." characters), the limit is ".$limit_array['min'][$biotype]['bio']." characters.";
       } else { 
	$message.=update_bio_element($link,$title,$biotext,$newbadgeid,$biotype,$biolang,$biostate);
      }
    }
  }

  // Create CongoDump entry.
  $element_array = array('badgeid', 'firstname', 'lastname', 'badgename', 'phone', 'email', 'postaddress1', 'postaddress2', 'postcity', 'poststate', 'postzip', 'regtype');
  $value_array=array($newbadgeid,
		     htmlspecialchars_decode($participant_arr['firstname']),
		     htmlspecialchars_decode($participant_arr['lastname']),
		     htmlspecialchars_decode($participant_arr['badgename']),
		     htmlspecialchars_decode($participant_arr['phone']),
		     htmlspecialchars_decode($participant_arr['email']),
		     htmlspecialchars_decode($participant_arr['postaddress1']),
		     htmlspecialchars_decode($participant_arr['postaddress2']),
		     htmlspecialchars_decode($participant_arr['postcity']),
		     htmlspecialchars_decode($participant_arr['poststate']),
		     htmlspecialchars_decode($participant_arr['postzip']),
		     htmlspecialchars_decode($participant_arr['regtype']));
  $message.=submit_table_element($link, $title, "$ReportDB.CongoDump", $element_array, $value_array);

  // Assign permissions.
  $query = "INSERT INTO $ReportDB.UserHasPermissionRole (badgeid, permroleid, conid) VALUES ";
  for ($i=2; $i<=count($permrole_arr); $i++) {
    $perm="permroleid".$i;
    if ($participant_arr[$perm]=="checked") {
      $query.="('".$newbadgeid."','".$i."','".$_SESSION['conid']."'),";
    }
  }

  $query=rtrim($query,',');
  if (!mysql_query($query,$link)) {
    $message_error=$query."<BR>Error updating $ReportDB.UserHasPermissionRole database.  Database not updated.";
    RenderError($title,$message_error);
    exit();
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($newbadgeid,
                     $_SESSION['badgeid'],
                     mysql_real_escape_string(htmlspecialchars_decode($participant_arr['note'])),
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "$ReportDB.NotesOnParticipants", $element_array, $value_array);

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully with ".$participant_arr["badgename"].".<BR>";
  return array ($message,$message_error);
}

function edit_participant ($participant_arr,$permrole_arr) {
  $conid=$_SESSION['conid'];
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  global $link;

  // Get the various length limits
  $limit_array=getLimitArray();

  // Get a set of bioinfo, and compare below.
  $bioinfo=getBioData($participant_arr['partid']);

  // Test constraints.

  // Too short/long name.
  if (isset($limit_array['min']['web']['name'])) {
    $namemin=$limit_array['min']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < $namemin) OR
	(strlen($participant_arr['badgename']) < $namemin) OR
	(strlen($participant_arr['pubsname']) < $namemin)) {
      $message_error="All name fields are required and minimum length is $namemin characters.  <BR>\n";
      echo "<P class=\"errmsg\">".$message_error."\n";
      return;
    }
  }
  if (isset($limit_array['max']['web']['name'])) {
    $namemax=$limit_array['max']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) > $namemax) OR
	(strlen($participant_arr['badgename']) > $namemax) OR
	(strlen($participant_arr['pubsname']) > $namemax)) {
      $message_error="All name fields are required and maximum length is $namemax characters.  <BR>\n";
      echo "<P class=\"errmsg\">".$message_error."\n";
      return;
    }
  }

  // Invalid email.
  if (!is_email($participant_arr['email'])) {
    $message_error="Email address: ".$participant_arr['email']." is not valid.  <BR>\n";
    echo "<P class=\"errmsg\">".$message_error."\n";
    return;
  }

  // Update Participants entry.
  $pairedvalue_array=array("bestway='".mysql_real_escape_string($participant_arr['bestway'])."'",
			   "altcontact='".mysql_real_escape_string($participant_arr['altcontact'])."'",
			   "prognotes='".mysql_real_escape_string(stripslashes($participant_arr['prognotes']))."'",
			   "pubsname='".mysql_real_escape_string(stripslashes($participant_arr['pubsname']))."'");
  $message.=update_table_element($link, $title, "$ReportDB.Participants", $pairedvalue_array, "badgeid", $participant_arr['partid']);

  // Update CongoDump entry.
  $pairedvalue_array=array("firstname='".mysql_real_escape_string(stripslashes($participant_arr['firstname']))."'",
			   "lastname='".mysql_real_escape_string(stripslashes($participant_arr['lastname']))."'",
			   "badgename='".mysql_real_escape_string(stripslashes($participant_arr['badgename']))."'",
			   "phone='".mysql_real_escape_string($participant_arr['phone'])."'",
			   "email='".mysql_real_escape_string($participant_arr['email'])."'",
			   "postaddress1='".mysql_real_escape_string(stripslashes($participant_arr['postaddress1']))."'",
			   "postaddress2='".mysql_real_escape_string(stripslashes($participant_arr['postaddress2']))."'",
			   "postcity='".mysql_real_escape_string(stripslashes($participant_arr['postcity']))."'",
			   "poststate='".mysql_real_escape_string($participant_arr['poststate'])."'",
			   "postzip='".mysql_real_escape_string($participant_arr['postzip'])."'",
			   "regtype='".mysql_real_escape_string(stripslashes($participant_arr['regtype']))."'");
  $message.=update_table_element($link, $title, "$ReportDB.CongoDump", $pairedvalue_array, "badgeid", $participant_arr['partid']);

  // Update Interested entry.
  if (isset($participant_arr['interested']) AND ($participant_arr['interested']!='') AND ($participant_arr['interested']!=0)) {
    $query ="UPDATE $ReportDB.Interested SET ";
    $query.="interestedtypeid=".$participant_arr['interested']." ";
    $query.="WHERE badgeid=\"".$participant_arr['partid']."\" AND conid=".$_SESSION['conid'];
    if (!mysql_query($query,$link)) {
      $message.=$query."<BR>Error updating Interested table.  Database not update.";
    }
    ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
    if ($r_matched[1]==0) {
      $element_array=array('conid','badgeid','interestedtypeid');
      $value_array=array($_SESSION['conid'], $participant_arr['partid'], mysql_real_escape_string(stripslashes($participant_arr['interested'])));
      $message.=submit_table_element($link,"Admin Participants","$ReportDB.Interested", $element_array, $value_array);
    } elseif ($r_matched[1]>1) {
      $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
    }
  }

  // Update/add Bios.
  /* We are only updating the raw bios here, so only a 2-depth
   search happens on biolang and biotypename. */
  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {

      // Setup for keyname, to collapse all three variables into one passed name.
      $biotype=$bioinfo['biotype_array'][$i];
      $biolang=$bioinfo['biolang_array'][$j];
      // $biostate=$bioinfo['biostate_array'][$k];
      $keyname=$biotype."_".$biolang."_".$biostate."_bio";

      // Clean up the posted string
      $teststring=stripslashes(htmlspecialchars_decode($participant_arr[$keyname]));
      $biostring=stripslashes(htmlspecialchars_decode($bioinfo[$keyname]));
      
      if ($teststring != $biostring) {
	if ((isset($limit_array['max'][$biotype]['bio'])) and (strlen($teststring)>$limit_array['max'][$biotype]['bio'])) {
	  $message.=ucfirst($biostate)." ".ucfirst($biotype)." (".$biolang.") Biography";
	  $message.=" too long (".strlen($teststring)." characters), the limit is ".$limit_array['max'][$biotype]['bio']." characters.";
	} elseif ((isset($limit_array['min'][$biotype]['bio'])) and (strlen($teststring)<$limit_array['min'][$biotype]['bio'])) {
	  $message.=ucfirst($biostate)." ".ucfirst($biotype)." (".$biolang.") Biography";
	  $message.=" too short (".strlen($teststring)." characters), the limit is ".$limit_array['min'][$biotype]['bio']." characters.";
	} else { 
	  $message.=update_bio_element($link,$title,$teststring,$participant_arr['partid'],$biotype,$biolang,$biostate);
	}
      }
    }
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($participant_arr['partid'],
                     $_SESSION['badgeid'],
                     mysql_real_escape_string(htmlspecialchars_decode($participant_arr['note'])),
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "$ReportDB.NotesOnParticipants", $element_array, $value_array);

  // Update permissions
  for ($i=2; $i<=count($permrole_arr); $i++) {
    $perm="permroleid".$i;
    $wperm="waspermroleid".$i;
    if (isset ($participant_arr[$perm])) {
      if ($participant_arr[$wperm] == "not") {
	$queryl ="INSERT INTO $ReportDB.UserHasPermissionRole (badgeid, permroleid, conid) VALUES ";
        $queryl.="('".$participant_arr['partid']."','".$i."','".$conid."');";
        if (!mysql_query($queryl,$link)) {
	  $message.=$queryl."<BR>Error updating $ReportDB.UserHasPermissionRole database.  Database not updated.";
	  echo "<P class=\"errmsg\">".$message."\n";
	  return;
	}
      }
    } elseif ($participant_arr[$wperm] == "indeed") {
      $queryl ="DELETE FROM $ReportDB.UserHasPermissionRole where ";
      $queryl.="badgeid=".$participant_arr['partid']." AND permroleid=".$i." AND conid=".$conid.";";
      if (!mysql_query($queryl,$link)) {
	$message.=$queryl."<BR>Error updating $ReportDB.UserHasPermissionRole database.  Database not updated.";
	echo "<P class=\"errmsg\">".$message."\n";
	return;
      }
    }
  }

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully.<BR>";
  echo "<P class=\"regmsg\">".$message."\n";
}    

function get_emailto_from_permrole($permrolename,$link,$title,$description) {
  /* Takes the permrolename and link, (and title and description, in
     case of failure) and returns a valid email address */

  // Get the email-to from the permrole
  $query=<<<EOD
SELECT
    emailtoquery
  FROM
    EmailTo
  WHERE
    emailtodescription='$permrolename'
EOD;

  // presume there is only one match and return that, with the error report, if necessary
  list($rows,$header_array,$emailtoquery_array)=queryreport($query,$link,$title,$description,0);
  list($rows,$header_array,$emailto_array)=queryreport($emailtoquery_array[1]['emailtoquery'],$link,$title,$description,0);
  return($emailto_array[1]['email']);
}

function send_fixed_email_info($emailto,$subject,$body,$link,$title,$description) {
  /* Takes the emailto (which might be a permrolename), subject, body,
     link (and title, and description in case of failure), resolve the
     emailto, if it is a permrolename, use the default from, and no
     cc, and add an entry to the email queue. */ 

  // Check to see if it is just an email address, or the permrole to be expanded
  if (!strpos($emailto,"@")) {
    $newemailaddress=get_emailto_from_permrole($emailto,$link,$title,$description);
    $newemailto="$emailto <$newemailaddress>";
    $emailto=$newemailto;
  }

  // Insert into queue, the ADMIN_EMAIL is the from address, no cc address, status 1 to send
  $element_array=array('emailto','emailfrom','emailcc','emailsubject','body','status');
  $value_array=array($emailto, ADMIN_EMAIL, '',
		     htmlspecialchars_decode($subject),
		     htmlspecialchars_decode($body),
		     1);
  $message.=submit_table_element($link, $title, "EmailQueue", $element_array, $value_array);
}

/* Three flow report functions.  They are remove, add, and delta rank.
 Need more header doc */
function remove_flow_report ($flowid,$table,$title,$description) {
  global $link;

  // Establish the table name
  $tablename=$table."Flow";

  // Establish the table element or fail
  if (strpos($table,"Group")) {
    $tableelement="gflowid";
  } elseif (strpos($table,"Personal")) {
    $tableelement="pflowid";
  } else {
    $message="<P>Error finding table $tablename.  Database not updated.</P>\n<P>";
    RenderError($title,$message);
    exit ();
  }

  // Set up the query
  $query="DELETE FROM $tablename where $tableelement=$flowid";

  // Execute the query and test the results
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error updating $tablename table.  Database not updated.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
}

function add_flow_report ($addreport,$addphase,$table,$group,$title,$description) {
  global $link;
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  $mybadgeid=$_SESSION['badgeid'];

  // Get phasetypeid list
  $query="SELECT phasetypeid FROM $ReportDB.PhaseTypes ORDER BY phasetypeid";

  // Retrieve query
  list($phasecount,$unneeded_array_a,$phase_array)=queryreport($query,$link,$title,$description,0);

  // Build the limits
  $firstphase=$phase_array[1]['phasetypeid'];
  $lastphase=$phase_array[$phasecount]['phasetypeid'];

  // Set the phase, if it is within the phasetypeid list
  $phasecheck="";
  if (($addphase<=$lastphase) AND ($addphase>=$firstphase)) {
    $phasecheck="phasetypeid='$addphase'";
  } else {
    $phasecheck="phasetypeid is NULL";
  }

  // Establish the table name
  $tablename=$table."Flow";

  // Establish the table element or fail
  if (strpos($table,"Group")) {
    $torder="gfloworder";
    $tname="gflowname";
    $cname=$group;
    $tid="gflowid";
  } elseif (strpos($table,"Personal")) {
    $torder="pfloworder";
    $tname="badgeid";
    $cname=$mybadgeid;
    $tid="pflowid";
  } else {
    $message="<P>Error finding table $tablename.  Database not updated.</P>\n<P>";
    RenderError($title,$message);
    exit ();
  }

  // Get the last element number, to increment
  $query="SELECT $torder AS floworder FROM $tablename where $tname='$cname' AND $phasecheck ORDER BY $torder DESC LIMIT 0,1";

  // Execute the query, test the results and assign the array values
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
  $floworder_array[1]=mysql_fetch_assoc($result);

  // Increment so we don't have redundant keys
  $nextfloworder=$floworder_array[1]['floworder']+1;

  // Insert query
  if ($phasecheck!="phasetypeid is NULL") {
    $query="INSERT INTO $tablename (reportid,$tname,$torder,phasetypeid) VALUES ($addreport,'$cname',$nextfloworder,$addphase)";
  } else {
    $query="INSERT INTO $tablename (reportid,$tname,$torder) VALUES ($addreport,'$cname',$nextfloworder)";
  }

  // Execute query
  if (!mysql_query($query,$link)) {
    $message=$query."<BR>Error updating $tablename database.  Database not updated.";
    RenderError($title,$message);
    exit ();
  }
}

function deltarank_flow_report ($flowid,$table,$direction,$title,$description) {
  global $link;
 
  // Establish the table name;
  $tablename=$table."Flow";

  // Estabilsh the table elements, or fail;
  if (strpos($table,"Group")) {
    $torder="gfloworder";
    $tname="gflowname";
    $tid="gflowid";
  } elseif (strpos($table,"Personal")) {
    $torder="pfloworder";
    $tname="badgeid";
    $tid="pflowid";
  } else {
    $message="<P>Error finding table $tablename.  Database not updated.</P>\n<P>";
    RenderError($title,$message);
    exit ();
  }

  // Get element from table;
  $query="SELECT $torder,$tname,phasetypeid FROM $tablename WHERE $tid=$flowid";
  list($phaserows,$phaseheader_array,$phasereport_array)=queryreport($query,$link,$title,$description,0);

  // Set the current flow order number;
  $corder=$phasereport_array[1][$torder];
  $cname=$phasereport_array[1][$tname];

  // Determine the next flow order number, depending on $direction;
  if ($direction=="Up") {
    $norder=$corder-1;
    if ($norder<1) {
      $message="<P>You cannot have an order number less than 1.</P>\n";
      RenderError($title,$message);
      exit ();
    }
  } elseif ($direction="Down") {
    $norder=$corder+1;
  } else {
    $message="<P>You have chosen an inappropriate direction: $direction.</P>\n";
    RenderError($title,$message);
    exit ();
  }

  // Determine if there is a phasetypeid attached to this particular flow element;
  if (isset($phasereport_array[1]['phasetypeid'])) {
    $phase=$phasereport_array[1]['phasetypeid'];
    $phasecheck="phasetypeid='$phase'";
  } else {
    $phasecheck="phasetypeid is NULL";
  }

  // Get element to be swapped with from table, based on current element floworder and $norder
  $query="SELECT $tid FROM $tablename WHERE $torder=$norder AND $tname='$cname' AND $phasecheck LIMIT 0,1";
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }

  // Swap the elements, checking for errors each time
  $query1="UPDATE $tablename set $torder=$norder where $tid=$flowid";

  // Execute the query and test the results
  if (($result1=mysql_query($query1,$link))===false) {
    $message="<P>Error updating $tablename table.  Database not updated.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }

  // If there is nothing to swap with, simply stop here.
  if (1==($row=mysql_num_rows($result))) {
    $replace_array[1]=mysql_fetch_assoc($result);
    $rtid=$replace_array[1][$tid];
    $query="UPDATE $tablename set $torder=$corder where $tid=$rtid";

    // Execute the query and test the results;
    if (($result=mysql_query($query,$link))===false) {
      $message="<P>Error updating $tablename table.  Database not updated.</P>\n<P>";
      $message.=$query;
      RenderError($title,$message);
      exit ();
    }
  }
}

/* These functions deal with the outside bios tables */

/* Function getBioData($badgeid)
 Reads Bios tables from db to populate returned array $bioinfo with the
 key of biotypename_biolang_biostatename_bio and the value of biotext eg:
 $bioinfo['web_en-us_raw_bio']='This bio is short and meaningless.'
 Returns bioinfo;
*/ 
function getBioData($badgeid) {
  global $message_error,$message2,$link;
  $BioDB=BIODB; // make it a variable so it can be substituted
  $LanguageList=LANGUAGE_LIST; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($BioDB=="BIODB") {unset($BioDB);}
  if ($LanguageList=="LANGUAGE_LIST") {unset($LanguageList);}

  $query= <<<EOD
SELECT
    concat(biotypename,"_",biolang,"_",biostatename,"_bio") AS biokey,
    biotext
  FROM
      $BioDB.Bios
    JOIN $BioDB.BioTypes USING (biotypeid)
    JOIN $BioDB.BioStates USING (biostateid)
  WHERE
        badgeid="$badgeid"
EOD;
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error.=mysql_error($link)."\n<BR>Database Error.<BR>No further execution possible.";
    RenderError($title,$message_error);
    exit;
  };
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $bioinfo[$row['biokey']]=$row['biotext'];
  }

  // Get all current possible biolang
  $query="SELECT DISTINCT(biolang) FROM $BioDB.Bios";
  if (isset($LanguageList)) {$query.=" WHERE biolang in $LanguageList";}
  if (($result=mysql_query($query,$link))===false) {
    $message_error.=$query."<BR>\nError retrieving biolang data from database.\n";
    RenderError($title,$message_error);
    exit();
  }
  while ($row=mysql_fetch_assoc($result)) {
    $biolang_array[]=$row['biolang'];
  }
  $bioinfo['biolang_array']=$biolang_array;

  // Get all current possible biotypenames
  $query="SELECT DISTINCT(biotypename) FROM $BioDB.BioTypes";
  if (($result=mysql_query($query,$link))===false) {
    $message_error.=$query."<BR>\nError retrieving biotypename data from database.\n";
    RenderError($title,$message_error);
    exit();
  }
  while ($row=mysql_fetch_assoc($result)) {
    $biotype_array[]=$row['biotypename'];
  }
  $bioinfo['biotype_array']=$biotype_array;

  // Get all current possible biostatenames
  $query="SELECT DISTINCT(biostatename) FROM $BioDB.BioStates";
  if (($result=mysql_query($query,$link))===false) {
    $message_error.=$query."<BR>\nError retrieving biotypename data from database.\n";
    RenderError($title,$message_error);
    exit();
  }
  while ($row=mysql_fetch_assoc($result)) {
    $biostate_array[]=$row['biostatename'];
  }
  $bioinfo['biostate_array']=$biostate_array;

  return($bioinfo);
}

/* Specific bio update takes seven variables: link, title, biotext,
 badgeid, biotypename, biostatename, and biolang, and returns the
 success message */
function update_bio_element ($link, $title, $newbio, $badgeid, $biotypename, $biolang, $biostatename) {
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($BioDB=="BIODB") {unset($BioDB);}

  // make sure it's clean
  $biotext=mysql_real_escape_string($newbio,$link);

  $query=<<<EOD
UPDATE 
    $BioDB.Bios
    INNER JOIN $BioDB.BioTypes using (biotypeid)
    INNER JOIN $BioDB.BioStates using (biostateid)
  SET
    biotext='$biotext'
  WHERE
    badgeid='$badgeid' AND
    biotypename in ('$biotypename') AND
    biolang in ('$biolang') AND
    biostatename in ('$biostatename')
EOD;

  if (!mysql_query($query,$link)) {
    $message_error.=$query."<BR>Error updating the $biotypename $biolang $biostatename bio for $badgeid.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }

  if (mysql_affected_rows($link) == 0) {
$query=<<<EOD
INSERT INTO 
    $BioDB.Bios (badgeid, biotypeid, biostateid, biolang, biotext) 
  VALUES 
    ('$badgeid',
     (SELECT biotypeid FROM $BioDB.BioTypes WHERE biotypename IN ('$biotypename')),
     (SELECT biostateid FROM $BioDB.BioStates WHERE biostatename IN ('$biostatename')),
     '$biolang',
     '$biotext');
EOD;

    if (!mysql_query($query,$link)) {
      $message_error.=$query."<BR>Error inserting the $biotypename $biolang $biostatename bio for $badgeid.  Database not updated.";
      RenderError($title,$message_error);
      exit;
    }
  }

  $message.="Database updated successfully with bio.<BR>";
  return ($message);
}

function generateSvgString($sessionid) {
  /* Global Variables */
  global $message_error,$message,$link;
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted
  $conid=$_SESSION['conid'];  // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  /* Local Variables */
  // Number of values offered
  $possible_value_count=5;

  // Number of offset and blank lines for the key
  //  1 for "Out of", 1 for "Q#", 1 for the space above the title
  //  1 for the title, 1 for the space below the title,
  //  1 for the space between the two sets of information
  //  1 for the bottom space
  $key_blank_lines=7;

  //should match $possible_values
  $value_title[1]="Totally Disagree";
  $value_title[2]="Somewhat Disagree";
  $value_title[3]="Neutral";
  $value_title[4]="Somewhat Agree";
  $value_title[5]="Totally Agree";

  $value_color[1]="red";
  $value_color[2]="orange";
  $value_color[3]="yellow";
  $value_color[4]="green";
  $value_color[5]="blue";

  //Some fixed and calculated values
  $fontsize=9;
  $textyoffset=$fontsize;
  $left_offset=$fontsize*6;
  $top_offset=$fontsize;
  $spacer=$fontsize;
  $bar_width=($fontsize*9);
  $short_bar_width=($bar_width/$possible_value_count);
  $grid_percent=20;
  $grid_count=100/$grid_percent;
  $height=($bar_width*$grid_count);

  // Get the count of each questionid mapped to questionvalue
  $query=<<<EOD
SELECT
    concat(questionid,":",questionvalue) AS QQ,
    count(*) AS tot,
    questionid,
    questionvalue
  FROM
      Feedback
  WHERE
    sessionid=$sessionid
  GROUP BY
    1
EOD;

  // Retrieve query
  list($questidvalnos,$questidvalheader_array,$questidval_array)=queryreport($query,$link,$title,$description,0);

  /* Create the graph_return array indexed by questionid and questionvalue.
     This array holds the count of each of the values of the question answers per question.*/
  for ($i=1; $i<=$questidvalnos; $i++) {
    $graph_return[$questidval_array[$i]['questionid']][$questidval_array[$i]['questionvalue']]=$questidval_array[$i]['tot'];
  }

  // Get the total count of each questionid
  $query=<<<EOD
SELECT
    questionid,
    count(*) AS tot,
    questiontext
  FROM
      Feedback
    JOIN $ReportDB.QuestionsForSurvey USING (questionid)
  WHERE
    sessionid=$sessionid
  GROUP BY
    questionid

EOD;

  // Retrieve query
  list($questvalnos,$questvalheader_array,$questval_array)=queryreport($query,$link,$title,$description,0);

  /* Create the questid_array of the list of questions,
     the graph_count array which is the total number answers for each question
     and the qdesc for the key which is the actual question, mapped to the number.*/
  for ($i=1; $i<=$questvalnos; $i++) {
    $questid_array[]=$questval_array[$i]['questionid'];
    $graph_count[$questval_array[$i]['questionid']]=$questval_array[$i]['tot'];
    $qdesc[$questval_array[$i]['questionid']]=$questval_array[$i]['questiontext'];
  }

  $number_of_questions=count($questid_array);
  $width=($number_of_questions*($bar_width+$spacer))+($left_offset*2);
  $fullheight=($top_offset+$height+(($textyoffset+$fontsize)*($key_blank_lines+$possible_value_count+$number_of_questions)));

  $svgstring="<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" width=\"".$width."px\" height=\"".$fullheight."px\" version=\"1.1\">\n";

  // Get the precis name as graph_title
  $query=<<<EOD
SELECT
    concat(title, if(secondtitle,concat(": ",secondtitle),"")) as Title
  FROM
      Sessions
  WHERE
    sessionid=$sessionid
    
EOD;

  // Retrieve query
  list($titlenos,$titleheader_array,$title_array)=queryreport($query,$link,$title,$description,0);

  $graph_title=htmlspecialchars($title_array[1]['Title']);

  // Begin grouping
  $svgstring.='<g font-size="'.$fontsize.'px" font-family="helvetica" fill="#000">'."\n";

  // Left side percentage counts
  for ($i=0; $i<=$grid_count; $i++) {
    $svgstring.='<text x="'.($left_offset-2).'" ';
    $svgstring.='y="'.($top_offset+$textyoffset+$height-((($i*$grid_percent)/100)*$height)).'" ';
    $svgstring.='text-anchor="end">'.$i*$grid_percent.'%-</text>'."\n";
  }

  // The bars
  $k=0;
  foreach ($questid_array as $i) {
    for ($j=$possible_value_count; $j>=1; $j--) {
      if ((isset($graph_return[$i][$j])) and ($graph_return[$i][$j] > 0)) {
	$graph_percent=(($graph_return[$i][$j]/$graph_count[$i])*100);
	$svgstring.='<rect height="'.(($graph_percent/$grid_percent)*$bar_width).'" ';
	$svgstring.='x="'.((($bar_width+$spacer)*$k)+$left_offset).'" ';
	$svgstring.='y="'.($top_offset+$height-(($graph_percent/$grid_percent)*$bar_width)).'" ';
	$svgstring.='width="'.($short_bar_width*$j).'" ';
	$svgstring.='style="stroke:#000;stroke-width:1ps;fill:'.$value_color[$j].';"/>'."\n";
      }
    }
    $svgstring.='<text x="'.((($bar_width+$spacer)*$k)+$left_offset+($bar_width/2)).'" ';
    $svgstring.='y="'.($top_offset+$height+$textyoffset+$fontsize).'" ';
    $svgstring.='text-anchor="middle">Q'.$i.'</text>'."\n";
    $svgstring.='<text x="'.((($bar_width+$spacer)*$k)+$left_offset+($bar_width/2)).'" ';
    $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*2)).'" ';
    $svgstring.='text-anchor="middle">Out of '.$graph_count[$i].'</text>'."\n";
    $k++;			      
  }

  // Right-side percentage counts
  for ($i=0; $i<=$grid_count; $i++) {
    $svgstring.='<text x="'.((($bar_width+$spacer)*$k)+$left_offset+2).'" ';
    $svgstring.='y="'.($top_offset+$textyoffset+$height-((($i*$grid_percent)/100)*$height)).'" ';
    $svgstring.='text-anchor="start">-'.$i*$grid_percent.'%</text>'."\n";
  }

  // Key
  $l=4;
  $svgstring.='<text x="'.(((($bar_width+$spacer)*$k)+($left_offset*2)+2)/2).'" ';
  $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*$l)).'" ';
  $svgstring.='text-anchor="middle">Feedback results for '.$graph_title.'</text>'."\n";
  $l++;
  for ($i=$possible_value_count; $i>=1; $i--) {
    $l++;
    $svgstring.='<text x="'.$left_offset.'" ';
    $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*$l)).'" ';
    $svgstring.='fill="'.$value_color[$i].'" ';
    $svgstring.='text-anchor="start">'.$value_title[$i].'='.$value_color[$i].'</text>'."\n";
  }
  $l++;
  foreach ($questid_array as $i) {
    $l++;
    $svgstring.='<text x="'.$left_offset.'" ';
    $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*$l)).'" ';
    $svgstring.='text-anchor="start">Q '.$i.': '.$qdesc[$i].'</text>'."\n";
  } 

  // Close the group, and the SVG
  $svgstring.="</g>\n";
  $svgstring.="</svg>\n";

  return($svgstring);
}

/* These three selects build session_array, list of comments associated with each class into
 session_array['sessionid'] if there should be a graph for that sessionid into
 session_array['graph']['sessionid'] and the key in session_array['key']
 Returns session_array*/
function getFeedbackData($badgeid) {
  global $message_error,$message2,$link;
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  $query = <<<EOD
SELECT
    sessionid,
    comment
  FROM
      CommentsOnSessions
EOD;

  if ($badgeid!="") {
    $query.=<<<EOD
  WHERE
    sessionid in (SELECT
                      sessionid 
                    FROM
                        ParticipantOnSession
                    WHERE badgeid='$badgeid')
EOD;
  }

  if (!$result=mysql_query($query,$link)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }

  while ($row=mysql_fetch_assoc($result)) {
    $session_array[$row['sessionid']].="    <br>\n    --\n    <br>\n    <PRE>".fix_slashes($row['comment'])."</PRE>";
  }

  // Check the existance of feedback in Feedback, and mark it in session_array['graph']['sessionid']
  $query = <<<EOD
SELECT
    DISTINCT(sessionid)
  FROM
      Feedback
EOD;

  if ($badgeid!="") {
    $query.=<<<EOD
  WHERE
    sessionid in (SELECT
                      sessionid 
                    FROM
                        ParticipantOnSession
                    WHERE badgeid='$badgeid')
EOD;
  }

  if (!$result=mysql_query($query,$link)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }

  while ($row=mysql_fetch_assoc($result)) {
    $session_array['graph'][$row['sessionid']]++;
  }

  return($session_array);
}

/* This function should populate the various limits from the LIMITDB
 database, once that is implimented, but in the meantime it pulls all
 the limits from the db_name.php file. */
/* Tentatively the LIMITDB database should be something like:
 limitid INT,
 conid varchar,
 minmax (0/1?),
 biotypeid INT (reference BioTypes),
 limitfield varchar, 
 limitval INT
 */
function getLimitArray() {
  $limit_array['min']['web']['bio']=MIN_WEB_BIO_LEN;
  $limit_array['max']['web']['bio']=MAX_WEB_BIO_LEN;
  $limit_array['min']['book']['bio']=MIN_BOOK_BIO_LEN;
  $limit_array['max']['book']['bio']=MAX_BOOK_BIO_LEN;
  $limit_array['min']['uri']['bio']=MIN_URI_BIO_LEN;
  $limit_array['max']['uri']['bio']=MAX_URI_BIO_LEN;
  $limit_array['min']['picture']['bio']=MIN_PICTURE_BIO_LEN;
  $limit_array['max']['picture']['bio']=MAX_PICTURE_BIO_LEN;
  $limit_array['min']['web']['desc']=MIN_WEB_DESC_LEN;
  $limit_array['max']['web']['desc']=MAX_WEB_DESC_LEN;
  $limit_array['min']['book']['desc']=MIN_BOOK_DESC_LEN;
  $limit_array['max']['book']['desc']=MAX_BOOK_DESC_LEN;
  $limit_array['min']['web']['name']=MIN_NAME_LEN;
  $limit_array['max']['web']['name']=MAX_NAME_LEN;
  $limit_array['min']['book']['name']=MIN_NAME_LEN;
  $limit_array['max']['book']['name']=MAX_NAME_LEN;
  $limit_array['min']['web']['title']=MIN_TITLE_LEN;
  $limit_array['max']['web']['title']=MAX_TITLE_LEN;
  $limit_array['min']['book']['title']=MIN_TITLE_LEN;
  $limit_array['max']['book']['title']=MAX_TITLE_LEN;

  // Tests for the substituted variables
  $minmax_array=array_keys($limit_array);
  foreach ($minmax_array as $minmax) {
    $type_array=array_keys($limit_array[$minmax]);
    foreach ($type_array as $type) {
      $limiting_array=array_keys($limit_array[$minmax][$type]);
      foreach ($limiting_array as $limiting) {
	if (!is_numeric($limit_array[$minmax][$type][$limiting])) {unset($limit_array[$minmax][$type][$limiting]);}
      }
    }
  }
  return($limit_array);
}
?>
