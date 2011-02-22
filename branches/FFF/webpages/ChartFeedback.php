<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;

// LOCALIZATIONS
$title="Picture of bar graph";
$description="<P>If you are seeing this, as opposed to the picture, something went horribly wrong.</P>";
$possible_values=5;
$sessionid=$_GET['sessionid'];

//should match $possible_values
$value_title[5]="Totally Agree";
$value_title[4]="Somewhat Agree";
$value_title[3]="Neutral";
$value_title[2]="Somewhat Disagree";
$value_title[1]="Totally Disagree";

// execute the SQL and return the title associated with the $sessionid
$query="select title from Sessions where sessionid=$sessionid";
// Retrieve query
list($titles,$header_array,$title_array)=queryreport($query,$link,$title,"<P>No session matching that sessionid.</P>",0);

$title=$title_array[1]['title'];

//execute the SQL query and return the feedback for $sessionid
$query="select questionid,questionvalue from Feedback where sessionid=$sessionid";

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,"<P>No feedback available on that session.</P>",0);

// Walk the query, making an array of the questionids each with their value and count
for ($i=1; $i<=$elements; $i++) {
  $question_array[$element_array[$i]['questionid']]['value']+=$element_array[$i]['questionvalue'];
  $question_array[$element_array[$i]['questionid']]['count']++;
 }

// How many questions
$numquestions=count($question_array);

// Walk each of the questions, and establish the mean value
for ($i=1; $i<=$numquestions; $i++) {
  $question_array[$i]['mean']=$question_array[$i]['value']/$question_array[$i]['count'];
}

//Tell gd2, where your fonts reside 
//putenv('GDFONTPATH=C:\WINDOWS\Fonts');
$font = '../Local/FreeMono.ttf';

//Specify constant values
$height = 500; //Image height in pixels
$bar_width = 80; //Bars width
$headerfontsize=5; // Font size for header fonts.
$labelfontsize=2; // Font size for the labels for the rows/columns

// Establish the longest title length from above, for indentation purposes.
$longest_title_length=0;
for ($i=1; $i<=$possible_values; $i++) {
  if ((imagefontwidth(2)*strlen($value_title[$i])) > $longest_title_length) {
      $longest_title_length=imagefontwidth(2)*strlen($value_title[$i]);
  }
 }

// Offset, so we aren't right up against the edge.
$longest_title_length+=10;

$legend_bar_length=10;

$top_margin = imagefontheight($headerfontsize)*3; // space at the top for the name
$top_notes = imagefontheight($labelfontsize)*3; // space at the top for notes (usually percentages)
$bottom_margin = imagefontheight($labelfontsize)*6; // space at the bottom for the x-values
$left_margin = $longest_title_length + $legend_bar_length; // space at the left for the y-values
$right_margin = 0; // space at the right for visual effect, is already half a bar width wide
$width = (($numquestions * $bar_width) * 1.5) + $left_margin + $right_margin; //Calculating image width
$bar_unit = ($height - $top_margin - $top_notes - $bottom_margin) / $possible_values; //Distance on the bar chart standing for 1 unit

//Set starting point for drawing 
$x = $left_margin;

//Create the image resource
$image = ImageCreate($width, $height);

//We are making four colors, white, black, blue and red
$white = ImageColorAllocate($image, 255, 255, 255);
$black = ImageColorAllocate($image, 0, 0, 0);
$red   = ImageColorAllocate($image, 255, 0, 0);
$blue  = imagecolorallocate($image,0,0,255);

//Create image background
//ImageFill($image,$height,$width,$white);
ImageFill($image,0,0,$white);
//Draw background shape
ImageRectangle($image, 0, 0, $width-1, $height-1, $black);
//Output header string.  Find the center of the page, subtract half the length of the fontwidth.
$headerstring="Feedback Results for $title";
$headerpoint=ceil($width / 2) - ceil((imagefontwidth($headerfontsize)*strlen($headerstring))/2);
ImageString($image, $headerfontsize, $headerpoint, $top_margin/3, $headerstring, $black);

// Side legend
// The verical line
ImageLine($image,$longest_title_length+ceil($legend_bar_length/2),$height-$bottom_margin,$longest_title_length+ceil($legend_bar_length/2),$height-$bottom_margin-($possible_values*$bar_unit),$black);

// The first horizontal tick
ImageLine($image,$longest_title_length,$height-$bottom_margin,$longest_title_length+ceil($legend_bar_length/2),$height-$bottom_margin,$black);

// The labels and the rest of the horizontal ticks
for ($i=1; $i<=$possible_values; $i++) {
  ImageString($image, $labelfontsize, $longest_title_length-(imagefontwidth(2)*strlen($value_title[$i])), $height-$bottom_margin-($i*$bar_unit)-ceil((imagefontheight($labelfontsize))/2), $value_title[$i], $black);
  ImageLine($image,$longest_title_length,$height-$bottom_margin-($i*$bar_unit),$longest_title_length+ceil($legend_bar_length/2),$height-$bottom_margin-($i*$bar_unit),$black);
 }


for ($i=1; $i<=$numquestions; $i++) {

  //Output question number
  $questionlabel="Q: ".$i;
  $questionpoint=ceil($bar_width/2) - ceil((imagefontwidth($labelfontsize)*strlen($questionlabel))/2);
  ImageString($image, $labelfontsize, $x + $questionpoint, $height-(($bottom_margin/3)*2), $questionlabel, $black);
  //Output mean for a particular question
  $mean='('.round($question_array[$i]['mean'],2).')';
  $meanpoint=ceil($bar_width/2) - ceil((imagefontwidth($labelfontsize)*strlen($mean))/2);
  ImageString($image, $labelfontsize, $x + $meanpoint, $top_margin+($top_notes/3), $mean, $red);
  //Output the number of votes for a particular question
  $numvotes='out of '.$question_array[$i]['count'];
  $votepoint=ceil($bar_width/2) - ceil((imagefontwidth($labelfontsize)*strlen($numvotes))/2);
  ImageString($image, $labelfontsize, $x + $votepoint, $height-($bottom_margin/3), $numvotes, $black);

  $bar_length = $question_array[$i]['mean'] * $bar_unit;

  //Draw a shape that corresponds to 100%
  ImageRectangle($image, $x, $height-$bottom_margin, $x+$bar_width, $height-$bottom_margin-($possible_values*$bar_unit), $black);
  //Output a bar for a particular value
  ImageFilledRectangle($image, $x, $height-$bottom_margin, $x+$bar_width, $height-$bottom_margin-$bar_length, $blue);

  //Going down to the next bar
  $x = $x + ($bar_width * 1.5);
  
 }

//Tell the browser what kind of file is come in
header("Content-Type: image/jpeg");

//Output the newly created image in jpeg format
ImageJpeg($image);

//Free up resources
ImageDestroy($image);
?> 
