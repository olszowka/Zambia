<?php
require_once('StaffCommonCode.php');
global $link;
$_SESSION['return_to_page']="genindex.php";
$title="All Grids";
$description="<P>All the grids are listed below, in the grid.</P>\n";
$additionalinfo="<P>The type of grid is listed as the headers and the area of interest it pertains to is listed down the side.\n";
$additionalinfo.="The choice of color or not is inside each grid element.</P>\n";
$additionalinfo.="<P>Also useful is the <A HREF=StaffBios.php>Bios for Presenters</A> (<A HREF=Bios.php>public version</A>),\n";
$additionalinfo.="the <A HREF=StaffDescriptions.php>Descriptions of scheduled Precis</A> (<A HREF=Descriptions.php>public version</A>),\n";
$additionalinfo.="the <A HREF=StaffPostgrid.php>Grid</A>, (<A HREF=StaffPostvolgrid.php>with volunteer</A> or <A HREF=Postgrid.php>public version</A>),\n";
$additionalinfo.="and the <A HREF=StaffSchedule.php>Scheduled Precis</A> (<A HREF=Schedule.php>public version</A>) in time order.</P>\n";

## Replacement for the query
$how_array['Description']="";
$how_array['Start Time']="starttime=y";
$how_array['Start Time<br>Unabridged']="starttime=y&unpublished=y";
$how_array['Start Time<br>Staff Only']="starttime=y&staffonly=y";
$how_array['Time Filled']="timefilled=y";
$how_array['Time Filled<br>Unabridged']="timefilled=y&unpublished=y";
$how_array['Time Filled<br>Staff Only']="timefilled=y&staffonly=y";
$how_array['Time Semi-filled']="standard=y";
$how_array['Time Semi-filled<br>Unabridged']="unpublished=y";
$how_array['Time Semi-filled<br>Staff Only']="staffonly=y";

$type_array['Complete']="standard=y&";
$type_array['Fast Track']="fasttrack=y&";
$type_array['Event']="events=y&";
$type_array['GoH']="goh=y&";
$type_array['Programming']="programming=y&";

//build the returned array
$header_array=array_keys($how_array);
$body_array=array_keys($type_array);
$rows=0;
foreach ($body_array as $y_element) {
  $rows++;
  foreach ($header_array as $x_element) {
    if ($x_element == "Description") {
      $grid_array[$rows]["$x_element"]="<B>".$y_element."</B>";
    } else {
      $grid_array[$rows]["$x_element"]="<A HREF=grid.php?".$type_array["$y_element"].$how_array["$x_element"].">Color</A> / \n";
      $grid_array[$rows]["$x_element"].="<A HREF=grid.php?".$type_array["$y_element"].$how_array["$x_element"]."&nocolor=y>No Color</A>\n";
    }
  }
}

## Page Rendering
topofpagereport($title,$description,$additionalinfo);
renderhtmlreport($rows,$header_array,$grid_array);

?>