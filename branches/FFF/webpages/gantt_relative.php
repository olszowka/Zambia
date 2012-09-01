<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid'];
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// LOCALIZATIONS
$_SESSION['return_to_page']="gantt_absolute.php";
$title="Gantt Chart";
$description="<P>Gantt Chart of the <A HREF=\"genreport.php?reportname=tasklistdisplay\">Task List</A></P>\n";

// The next three values might need tinkering for ease of viewing
// how thick the bars are
$task_height = 8;

// How wide each day element should be.
$day_width_in_px = 40;

// Discovered constant to fix for the offset between the chart and the table.
$table_offset=7;

// Large values of start and end, so that they can be adjusted.
// They might be able to simply be set to null, but I'm not sure of the math then.
$chart_date_begin=9999999999;
$chart_date_end=0000000000;

// gantt image used to create bar and spacing
$gantt_bar = 'images/gantt.gif';

// Creation of the background images, bg for the standard bgr for the header/reversed
$image_bg = "gantt_back_graph.php?width=$day_width_in_px&height=$task_height";
$image_bgr = "gantt_back_graph.php?width=$day_width_in_px&height=$task_height&reverse=R";

// Get this from the database
$query = <<<EOD
SELECT
    CONCAT("<A HREF=TaskListUpdate.php?activityid=",activityid,">",activity,"</A>") as task,
    UNIX_TIMESTAMP(activitystart) as date_start,
    UNIX_TIMESTAMP(targettime) as date_end,
    donestate
  FROM
    $ReportDB.TaskList
  WHERE
    conid=$conid
  ORDER BY
    activitystart

EOD;

list($rows,$nonheader_array,$events_array)=queryreport($query,$link,$title,$description,0);

// Sets the chart start date, end date, and colors of the tasks
for ($i=1; $i<=$rows; $i++) {
  $this_date_start=$events_array[$i]['date_start'];
  $this_date_end=$events_array[$i]['date_end'];
  if ($this_date_start < $chart_date_begin) {$chart_date_begin=$this_date_start;}
  if ($this_date_end > $chart_date_end) {$chart_date_end=$this_date_end;}
  if ($events_array[$i]['donestate']=="Y") {$events_array[$i]['color']='B2D0B0';} else {$events_array[$i]['color']='8589BB';}
}

// Divide by the number of seconds in a day.
// Rounded because there might be a daylight savings time in there.
// Incremented by one because things don't stop at midnight on that day, but midnight on the next one
$chart_width_in_days = round(($chart_date_end - $chart_date_begin)/86400) + 1;

// Width of each day * number of days within the table
$chart_width_in_px = $chart_width_in_days * $day_width_in_px;

// Width of internal chart, plus offset to make the table work
$chart_table_width_px = $chart_width_in_px + $table_offset;

$event_display = <<<EOD
<DIV class="scrolling_div" >
  <TABLE border="1">
    <TR>
      <TD>
        <TABLE border="0" cellspacing="0" cellpadding"0" width="{$chart_table_width_px}">
          <TR>
	    <TD style="border-bottom: solid 1px #999999;" nowrap>

EOD;

// Gantt chart Header
for ($i=0; $i<$chart_width_in_days;$i++) {
  // n/j is the month number / day number format
  $chart_header_date = date ( 'n/j' , $chart_date_begin + ($i * 86400) );
  $day_left = $i * $day_width_in_px;
  $event_display.= <<<EOD
              <DIV class="event_text" style="display:block; float:left; width:{$day_width_in_px}px; overflow:hidden; top:0px; left:{$day_left}px; background-image: url($image_bgr);">$chart_header_date</DIV>

EOD;
}
$event_display.= "            </TD>\n          </TR>\n";

// Key Header
$event_list = <<<EOD
<TABLE border="1" cellspacing="0" cellpadding"0" >
  <TR class="event_text" style="background: #CCCCCC;">
    <TH align="center">Task Name</TH>
    <TH align="center">Start Date</TH>
    <TH align="center">End Date</TH>
    <TH align="center">Done?</TH>
  </TR>

EOD;

// Build the key and the chart
foreach ( $events_array as $key => $value ) {
  // From the database, colours need to be figured somehow.
  $task_name = $value['task'];
  $background = $value['color'];
	
  // Start and end position of the element in the chart, so the width can be determined.
  $position_left = round(($value['date_start']-$chart_date_begin)/86400) * $day_width_in_px;
  $position_right = (round(($value['date_end']-$chart_date_begin)/86400) + 1 ) * $day_width_in_px;
  $width = $position_right - $position_left;
	
  // length the rest of the table for the grid as background
  $postwidth = $chart_width_in_px - $position_right;
	
  // Body of the key
  $event_list.="  <TR class=\"event_text\">\n    <TD>$task_name</td>\n    <TD align=\"right\">".date('n/j',$value['date_start'])."</TD>\n";
  $event_list.="    <TD align=\"right\">".date('n/j',$value['date_end'])."</TD>\n";
  $event_list.="    <TD align=\"center\" style=\"background: #{$background};\">".$value['donestate']."</TD>\n  </TR>\n";
	
  // Body of the Gantt chart
  // add to the display, 
  //   First two to fill to the task position, name and image.
  //   Second two the name of the task and the gantt line.
  //   Third two the fill to the end of the graph for both name and image
  $event_display .= <<<EOD
          <TR>
            <TD>
              <DIV class="event_text" style="display:inline; float:left; width:{$position_left}px; overflow:hidden; top:0px; left:0px; background-image: url($image_bg);">&nbsp;<br>
                <img style="display:inline; height:{$task_height}px; width:1px; background: #FFFFFF;  top-margin:4px; left:{$position_left}px; z-index: 90;" src="{$gantt_bar}" />
              </DIV>
              <DIV class="event_text" style="display:inline; float:left; overflow:hidden; top:0px; left:{$position_left}px; background-image: url($image_bg);">$task_name<br>
                <img style="display:inline; height:{$task_height}px; width:{$width}px; background: #{$background};  top-margin:4px; left:{$position_left}px; z-index: 100;" src="{$gantt_bar}" />
              </DIV>
              <DIV class="event_text" style="display:inline; float:left; width:{$postwidth}px; overflow:hidden; top:0px; left:{$position_right}px; background-image: url($image_bg);">&nbsp;<br>
                <img style="display:inline; height:{$task_height}px; width:1px; background: #FFFFFF;  top-margin:4px; left:{$position_left}px; z-index: 90;" src="{$gantt_bar}" />
              </DIV>
            </TD>
          </TR>

EOD;
}
$event_list.="</TABLE>\n";
$event_display.="        </TABLE>\n      </TD>\n    </TR>\n  </TABLE>\n</DIV>\n";

// Page Rendering
topofpagereport($title,$description,$additionalinfo);
echo "<P>$event_display<br>\n";
echo "$event_list</P>\n";
correct_footer();
?>
