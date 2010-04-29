<?php

  // Render Error reporting
function StaffRenderError ($title, $message) {
    global $debug;
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);
    if (isset($debug)) echo $debug."<BR>\n";
    echo "<P id=\"errmsg\">".$message."</P>\n";
    staff_footer();
    }

function PartRenderError ($title, $message) {
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    participant_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
    participant_footer();
    }

function BrainstormRenderError ($title, $message) {
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    brainstorm_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
    brainstorm_footer();
    }

function PostingRenderError ($title, $message) {
    require_once('PostingHeader.php');
    require_once('PostingFooter.php');
    posting_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
    posting_footer();
    }

function RenderError($title,$message) {
  if ($_SESSION['role'] == "Brainstorm") {
    BrainstormRenderError($title,$message);
  }
  elseif ($_SESSION['role'] == "Participant") {
    PartRenderError($title,$message);
  }
  elseif ($_SESSION['role'] == "Staff") {
    StaffRenderError($title,$message);
  }
  elseif ($_SESSION['role'] == "Posting") {
    PostingRenderError($title,$message);
  }
  else {
    // do something generic here (though this might be way too generic)
    // better to output some error message reliably than none at all
    echo "<html>";
    echo "<head>";
    echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
    echo "<title>Zambia -".$title."</title>";
    echo "</head>";
    echo "<body>";
    echo "<H1>Zambia&ndash;The".CON_NAME."Scheduling Tool</H1>";
    echo "<hr>";
    echo "<p> An error occurred: </p>";
    echo $message;
    echo "</body>";
    echo "</html>";
  }
}

// Top of page reporting
function topofpagereport($title,$description,$info) {
  if ($_SESSION['role'] == "Brainstorm") {
    brainstorm_header($title);
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

function topofpagecsv($filename) {
  header("Expires: 0");
  header("Cache-control: private");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Description: File Transfer");
  header("Content-Type: text/csv");
  header("Content-disposition: attachment; filename=$filename");
}

function renderhtmlreport($headers,$rows,$header_array,$element_array) {
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

function rendercsvreport($headers,$rows,$header_array,$element_array) {
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

function queryhtmlreport($query,$link,$title,$description) {
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
  if (0==($rows=mysql_num_rows($result))) {
    $message="$description\n<P>This report retrieved no results matching the criteria.<P>\n";
    RenderError($title,$message);
    exit();
  }
  for ($i=1; $i<=$rows; $i++) {
    $element_array[$i]=mysql_fetch_assoc($result);
  }
  $header_array=array_keys($element_array[1]);
  $columns=count($header_array);
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="<TH>";
    $headers.=$header_name;
    $headers.="</TH>\n";
  }
  return array ($headers,$rows,$header_array,$element_array);
}

function querycsvreport($query,$link) {
  if (($result=mysql_query($query,$link))===false) {
    $message="Error retrieving data from database.<BR>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
  if (0==($rows=mysql_num_rows($result))) {
    $message="This report retrieved no results matching the criteria.";
    RenderError($title,$message);
    exit();
  }
  for ($i=1; $i<=$rows; $i++) {
    $element_array[$i]=mysql_fetch_assoc($result);
  }
  $header_array=array_keys($element_array[1]);
  $columns=count($header_array);
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="\"";
    $headers.=$header_name;
    $headers.="\",";
  }
  $headers = substr($headers, 0, -1);
  return array ($headers,$rows,$header_array,$element_array);
}

?>
