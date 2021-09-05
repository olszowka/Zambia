<?php

global $title;

error_reporting(E_ALL); ini_set('display_errors', true);

//require_once('db_functions.php');
//require_once('error_functions.php');
require_once('CommonCode.php');
require_once('GridHtmlHeader.php');
require_once('grid_functions.php');
require_once('GridHeader.php');
require_once('GridFooter.php');

$title="Public - Display Programming Grid";

$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted


if (!build_location_arrays($linki)) {
    $message_error = "Failed to retrieve location information. " . $message_error;
    RenderError($message_error);
    exit();
}

//build additional css for all of the locations
$cssoutput = output_grid_css($locationscss);


//call the page header and pass along the additional css so that it will be included on the page header
grid_header($title, $cssoutput);


//build a public grid of locations for every 15 minutes (900 seconds)
$locationGrid = build_location_grid($linki, $locations, 'public', 900);
$content = output_locationgrid($linki, $locationGrid, $locations, 'public');

?>

<body>
<div class="container-fluid">


            <div class="container-fluid">
                <!-- Header -->
                <header class="header-wrapper" id="top">
                    <div id="reg-header-container" class="collapsible-wrapper">
                        <div id="reg-header">
                            <div class="header-contents">
                                <img src="images/phandemoniumlogo.gif" alt="Capricon logo" class="d-none d-lg-block" />
                                <h1 class="d-none d-md-block"><span class="d-none d-lg-inline"> <?php echo CON_NAME; ?> Grid of Scheduled Events</span></h1>
                            </div>
                        </div>
                    </div>
                </header>
            </div>

<?php
    //Use the tags below to debug the arrays.
    //echo "<pre>";
    //print_r($locations);
    //print_r($locationscss);
    //print_r($locationGrid);
    //echo "</pre>";
?>

<div class="container-fluid">
<div class="container">

<p>(NOTE: All events are subject to last minute changes.  Please check back often.)<br />
Generated on: <?php echo date('l jS \of F Y h:i:s A'); ?><br />
Hover over a panel to get a description of the item.</p>
<br />
<h2 class="text-center">All times are Central timezone.</h2>

<br />


<?php
//Notes for Cap 40 in 2020.
//Look for [0] and [1] in the content below and fix them by moving them to the appropriate dummy room and then deleting the appropriate extra cells in the rows after the item so that the rowspan variable works.
//Also make sure that the main room item exists when there is an event in the dummy room - current code doesn't handle it well.
//Stretch the Blinkie Open Time event to be in the adjoining dummy rooms to make the grid look nicer.
?>

<div class="row">
<div class="col">
<a href="#grid-Thursday">Thursday</a>
</div>
<div class="col">
<a href="#grid-Friday">Friday</a>
</div>
<div class="col">
<a href="#grid-Saturday">Saturday</a>
</div>
<div class="col">
<a href="#grid-Sunday">Sunday</a> 
</div>
</div>

<br />
<br />

</div>      <!-- end of container -->
</div>      <!-- end of container-fluid -->


<?=$content?>

<br />
<br />
<br />


<?php grid_footer(); ?>
