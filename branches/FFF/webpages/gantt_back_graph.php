<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;

// Localizations
$title="Picture of background for gantt chart";
$description="<P>If you are seeing this, as opposed to the picture, something went horribly wrong.</P>";

$height = $_GET['height'];
$width = $_GET['width'];
$reverse = $_GET['reverse'];

// Create the background image
$image = ImageCreate($width, $height);

// We are making 3 colors, white, black, and grey
$white = ImageColorAllocate($image, 255, 255, 255);
$grey = ImageColorAllocate($image, 200, 200, 200);
$black = ImageColorAllocate($image, 0, 0, 0);

// Create the image background
if ($reverse == "R") {
  ImageFill($image,0,0,$grey);
 } else {
  ImageFill($image,0,0,$white);
 }

//Draw dividing line
ImageLine($image, $width-1, 0, $width-1, $height-1, $black);

//Tell the browser what kind of file is come in
header("Content-Type: image/jpeg");

//Output the newly created image in jpeg format
ImageJpeg($image);

//Free up resources
ImageDestroy($image);

