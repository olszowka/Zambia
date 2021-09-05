<?php


// --------------------------------------------------------------------------------------------
/**
 * Return an array of dates and times between 2 dates using a specific interval
 *
 * access public
 * param    $sdttm date/time in mySQL format
 * param    $edttm date/time in mySQL format
 * param    $int interval to use in seconds
 * return array
 */
function datetime_array($sdttm = '', $edttm = '', $int = 900) {
    // ensure that dates are sent
    if ($sdttm == '' OR $edttm == '') {
        return FALSE;
    }

    list($sdt, $stm) = explode(' ', $sdttm);
    list($edt, $etm) = explode(' ', $edttm);

    // convert time to number of seconds
    $stm = timeval($stm);
    $etm = timeval($etm);

    // initialize dates array and set first value to blank
    $dates=Array();
    $dates[] = '';

    // loop through dates and build array
    $i=0;
    while ( strtotime($edt) >= strtotime("+".$i." day",strtotime($sdt)) ) {
        $dt=date("Y-m-d",strtotime("+".$i++." day",strtotime($sdt)));
        $slptm = 0;
        $elptm = 86400;
        if ($dt == $sdt) {
            $slptm = $stm;  // override start loop time on start date
        }
        if ($dt == $edt) {
            $elptm = $etm;  // override end loop time on end date
        }

        // loop thru time starting at start time and ending at end time by interval
        $j = $slptm;
        for ($j = $slptm; $j <= ($elptm - $int); $j = $j + $int) {
            $h = floor($j / 3600);
            $m = ($j - ($h*3600)) / 60;
            $dates[]=$dt . " " . date("H:i:s", mktime($h, $m, 0));
        }
    }

    return $dates;
}


// --------------------------------------------------------------------------------------------
/**
 * Return an array times using a specific interval
 *
 * access public
 * param    $int interval to use in seconds
 * return array
 */
function time_array($int = 900) {
    // initialize times array and set first value to blank
    $times=Array();
    $times[] = '';

    // convert time to number of seconds
    $slptm = 0;
    $elptm = 86400;

    // loop thru time starting at start time and ending at end time by interval
    $j = $slptm;
    for ($j = $slptm; $j <= ($elptm - $int); $j = $j + $int) {
        $h = floor($j / 3600);
        $m = ($j - ($h*3600)) / 60;
        $times[]=date("H:i:s", mktime($h, $m, 0));
    }

    return $times;
}


// --------------------------------------------------------------------------------------------
/**
 * Convert hh:mm:ss time to seconds
 *
 * access public
 * param    $tm string of time in 00:00:00 format
 * return integer    time in seconds
 */
function timeval($tm) {
    $tm = explode(':', $tm);
    $tm = ($tm[0] * 60 * 60) + ($tm[1] * 60) + $tm[2];
    return $tm;
}

// --------------------------------------------------------------------------------------------
/**
 * Round the time up to make it evenly divisible by the interval
 *
 * access public
 * param    $tm time in seconds
 * param    $int interval to use in seconds
 * return integer    time in seconds
 */
function match_interval($tm, $int = 900) {
    while (($tm % $int) <> 0) {
        $tm = $tm + 900;  // add 15 minutes to time
    }
    return $tm;
}

// --------------------------------------------------------------------------------------------
/**
 * Return an array of panels between 2 dates using a specific interval and list (of locations or panelists)
 *
 * access public
 * param    $sdttm date/time in mySQL format
 * param    $edttm date/time in mySQL format
 * param    $int interval to use in seconds
 * param    $list list of locations or panelists
 * return array
 */
// grid could be a grid of locations or a grid of panelists
function build_empty_grid($sdttm, $edttm, $int, $list) {
    list($sdt, $stm) = explode(' ', $sdttm);
    list($edt, $etm) = explode(' ', $edttm);
    $stm = timeval($stm);
    $etm = timeval($etm);

    $grid = Array();
    $grid = addIt($grid,$list,$sdt,$stm,$edt,$etm,$int,array('id'=>'','type'=>'','slots'=>'','cols'=>''));

    return $grid;
}

// --------------------------------------------------------------------------------------------
/**
 * Adds existing event to location grid.
 * Assumes that conflicts don't exist, events meet "scheduled" criteria.
 *
 * access public
 * param    $grid array of events by locations
 * param    $locations list of locations
 * param    $eventInfo array of event info
 * param    $int interval to use in seconds
 * return array    array of events by location
 */
function assign_event_to_locationgrid($grid, $locations, $eventInfo, $int) {
    $sdttm = $eventInfo['sdttm'];
    $edttm = $eventInfo['edttm'];
    $eventlength = $eventInfo['eventlength'];
    $locationcnt = $eventInfo['locationcnt'];

    // get time and change to seconds and then round up to match interval
    list($sdt, $stm) = explode(' ', $sdttm);
    $stm = timeval($stm);
    $stm = match_interval($stm, $int);

    // get time and change to seconds and then round up to match interval
    list($edt, $etm) = explode(' ', $edttm);
    $etm = timeval($etm);
    $etm = match_interval($etm, $int);

    //if ($eventInfo['id'] == 363) {
    //    print_r("\n" . $eventlength);
    //}

    //if ($eventlength > 0)
    if (isset($eventlength)) {
        $slots = countSlots($sdt,$stm,$edt,$etm,$int);
        //if ($eventInfo['id'] == 363) {
        //    print_r("\n" . $slots);
        //}
        $grid = addIt($grid,$locations,$sdt,$stm,$edt,$etm,$int,array('id'=>$eventInfo['id'],'type'=>$eventInfo['type'],'slots'=>$slots,'cols'=>$locationcnt,'name'=>$eventInfo['name'],'sdttm'=>$sdttm,'progguiddesc'=>$eventInfo['progguiddesc']));
    }

    return $grid;
}

// --------------------------------------------------------------------------------------------
/**
 * Adds existing event to participant grid.
 * Assumes that conflicts don't exist, events meet "scheduled" criteria.
 *
 * access public
 * param    $grid array of events by locations
 * param    $locations list of locations
 * param    $eventInfo array of event info
 * param    $int interval to use in seconds
 * return array        array of events by location
 */
function assign_event_to_partgrid($grid, $participants, $eventInfo, $int) {
    $sdttm = $eventInfo['sdttm'];
    $edttm = $eventInfo['edttm'];
    $setupdttm = $eventInfo['setupdttm'];
    $teardowndttm = $eventInfo['teardowndttm'];
    $setup = $eventInfo['setup'];
    $teardown = $eventInfo['teardown'];
    $eventlength = $eventInfo['eventlength'];
    $eventtype = $eventInfo['type'];


    list($sdt, $stm) = explode(' ', $sdttm);
    $stm = timeval($stm);

    list($edt, $etm) = explode(' ', $edttm);
    $etm = timeval($etm);

    list($sudt, $sutm) = explode(' ', $setupdttm);
    $sutm = timeval($sutm);

    list($tddt, $tdtm) = explode(' ', $teardowndttm);
    $tdtm = timeval($tdtm);

    if ($eventlength > 0) {
        $slots = countSlots($sdt,$stm,$edt,$etm,$int);
        $grid = addIt($grid,$participants,$sdt,$stm,$edt,$etm,$int,array('id'=>$eventInfo['id'],'type'=>$eventtype,'slots'=>$slots,'cols'=>1));
    }

    return $grid;
}

// --------------------------------------------------------------------------------------------
// Add the event (item) to the (list) grid from start dttm to end dttm using int slots
function addIt($grid,$list,$sdt,$stm,$edt,$etm,$int,$item) {
    //loop thru dates
    $i=0;
    while ( strtotime($edt) >= strtotime("+".$i." day",strtotime($sdt)) ) {
        $dt=date("Y-m-d",strtotime("+".$i++." day",strtotime($sdt)));
        $slptm = 0;  //start loop time
        $elptm = 86400;  //end loop time
        if ($dt == $sdt) {
            $slptm = $stm;  // override start loop time
        }
        if ($dt == $edt) {
            $elptm = $etm;  // override end loop time
        }

        //loop thru times
        $j = $slptm;
        for ($j = $slptm; $j <= ($elptm - $int); $j = $j + $int) {
            $h = floor($j / 3600);
            $m = ($j - ($h*3600)) / 60;
            $tm = date("H:i", mktime($h, $m));

            // array is sortval as key and id as val
            foreach ($list as $key => $val) {

                // clunky code added to account for multiple sessions in a room. Added 20200113 lmv
                if (!empty($grid[$dt][$tm][$key]['id'])) {
                    $key = 0;
                    $item['name'] .= '[' . $key . ']';
                }
                if (!empty($grid[$dt][$tm][$key]['id'])) {
                    $key = 1;
                    $item['name'] .= '[' . $key . ']';
                }


                $item['listID'] = $val;
                $grid[$dt][$tm][$key] = $item;
            }
        }
    }
    return $grid;
}

// --------------------------------------------------------------------------------------------
// Count the number of slots from start dttm to end dttm using int length slots
function countSlots($sdt,$stm,$edt,$etm,$int) {
    //loop thru dates
    $i=0;
    $slots = 0;
    while ( strtotime($edt) >= strtotime("+".$i." day",strtotime($sdt)) ) {
        $dt=date("Y-m-d",strtotime("+".$i++." day",strtotime($sdt)));
        $slptm = 0;  //start loop time
        $elptm = 86400;  //end loop time
        if ($dt == $sdt) {
            $slptm = $stm;  // override start loop time
        }
        if ($dt == $edt) {
            $elptm = $etm;  // override end loop time
        }

        //loop thru times
        $j = $slptm;
        for ($j = $slptm; $j <= ($elptm - $int); $j = $j + $int) {
            $slots++;
        }
    }
    return $slots;
}


// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------
/**
 * Return an html string of the locationgrid
 *
 * @access public
 * @params array    $grid array of events by locations
 * @params array    $locations list of locations
 * @params string    $mode 'internal', 'public', 'publication'
 * @return string
 */
function output_locationgrid($link, $grid, $locations, $mode = 'internal') {
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted


    //get 2 arrays of the rooms using id as the key and display_order as the key
    $query = "SELECT roomid, display_order, roomname, function, floor, grid_column_span FROM Rooms";               //WHERE display_order = $locationkey
    $result=mysqli_query($link, $query);
    $room_disp_arr = array();
    $room_arr = array();
    while ($row=mysqli_fetch_assoc($result)) {
        $room_arr[$row['roomid']] = $row;
        $room_disp_arr[$row['display_order']] = $row;
    }
    mysqli_free_result($result);
    //test
    //echo "<pre>\n";           //test
    //print_r($room_arr);   //test
    //print_r($room_disp_arr);   //test
    //echo "</pre><br />\n";    //test
    //exit();      //test
    //test


    $output = '';
    $output .= '<table class=grid border=1>' . "\n";

    $locationHeader = '';
    $locationHeader .= '<tr>' . "\n" . '     <th>&nbsp;</th>' . "\n";
    foreach ($locations as $locationkey => $locationval) {
        set_time_limit(0);

        if ($room_disp_arr[$locationkey]['grid_column_span'] != 0) {

            // Build the location header line
            $locationHeader .= '     <th class=' . classname($room_disp_arr[$locationkey]['roomname']);
            if ($room_disp_arr[$locationkey]['grid_column_span'] != 1) {
                $locationHeader .= ' colspan=' . $room_disp_arr[$locationkey]['grid_column_span'];
            }
            $locationHeader .= '>' . $room_disp_arr[$locationkey]['roomname'];
            if ($room_disp_arr[$locationkey]['floor'] != '') {
                $locationHeader .= ' (' . $room_disp_arr[$locationkey]['floor'] . ')';
            }
            if ($room_disp_arr[$locationkey]['function'] != '') {
                $locationHeader .= '<hr />' . $room_disp_arr[$locationkey]['function'];
            }
            $locationHeader .= '</th>' . "\n";

        }

    }
    $locationHeader .= '</tr>' . "\n";
    $output .= $locationHeader;

    //test
    //$output .= '</table>' . "\n";   //test
    //return $output;   //test
    //exit();  //test
    //test

    // number of columns plus one for times column
    $locationcnt = count($locations) + 1;
    $flag = 0;
    $flag2 = 0;

    // loop thru grid dates
    foreach ($grid as $dt=>$dtarr) {
        $dtname = date('l',strtotime($dt));
        // use flag to indicate first row of grid and output date
        if ($flag == 0) {
            $output .= '<tr><td class=grid_date colspan=' . $locationcnt . '><a id="grid-' .$dtname . '">' . $dt . ' - ' . $dtname . '</a></td></tr>' . "\n";
            $flag += 1;
        }
        // loop thru grid times
        foreach ($dtarr as $tm=>$tmarr) {
            // output date on grid at 5am
            if ($tm == '05:00') {
                $output .= $locationHeader;
                $output .= '<tr><td class=grid_date colspan=' . $locationcnt . '><a id="grid-' .$dtname . '">' . $dt . ' - ' . $dtname . '</a></td></tr>' . "\n";
            }
            
            // output time
            $output .= '<tr>' . "\n" . '     <td>' . cnv_tm_from_military($tm) . '</td>' . "\n";
            
            // loop thru grid locations
            foreach ($tmarr as $location => $item) {
                // get event id
                $id = $item['id'];
                // get event details
                $type = $item['type'];      // event, setup
                $slots = $item['slots'];    // number of rows
                $cols = $item['cols'];      // number of columns
                $locationID = $item['listID'];  // location id
                $locationName=$room_arr[$locationID]['roomname'];
                $className = classname($locationName);
                $classNameOrig = $className;  // save original name before changing it
                if ($type != 'event' and $type != '') {
                    $className = $type;     // change classname to setup
                }

                $row['name'] = '';
                if (isset($item['name'])) {
                    $row['name'] = $item['name'];
                    //$flag2 += 1;
                }



                // if mode is public or publication and the item type is not event
                //   (meaning it is setup or teardown), then don't display it
                if ( ($mode == 'public' or $mode == 'publication') and $type != 'event') {
                    $output .= '     <td';
                    if ($type != 'setup' and $type != 'teardown') {
                        $output .= ' class=' . $className;
                    }
                    if ($type == 'setup' or $type == 'teardown') {
                        $output .= ' class=' . $classNameOrig;
                    }
                    $output .= '> &nbsp;</td>' . "\n";
                    continue;
                }

                // if mode is public or publication, there is an event, and it is hidden,
                //  then output blank cell
                if ( ($mode == 'public' or $mode == 'publication') and $id != '' and $row['name'] == '') {
                    $output .= '     <td';
                    $output .= ' class=' . $classNameOrig;
                    $output .= '> &nbsp;</td>' . "\n";
                    continue;
                }

                // check if this event is done already
                if ($id != '') {
                    if (isset($doneflag[$id][$type])) {
                        continue;
                    }
                    $doneflag[$id][$type] = 1;
                }


                // row name could be null because of hidden event, so make sure to output nothing here
                if ($row['name'] == '') {
                    $output .= '     <td class=' . $className . '> &nbsp;</td>' . "\n";
                } else {
                    // check if event starttime match grid time
                    $actual_start_time = '';
                    list($tempsdt, $tempstm) = explode(' ', $item['sdttm']);
                    $temptm = explode(':',$tempstm);
                    $temptm1 = $temptm[0] . ':' . $temptm[1];
                    if (($temptm1 != $tm) and ('0'.$temptm1 != $tm)) {
                        $actual_start_time = ' (' . cnv_tm_from_military($temptm1) . ')';
                    }

                    $output .= '     <td';
                    $output .= ' rowspan=' . $slots;
                    $output .= ' colspan=' . $cols;
                    if ($type != 'event') {
                        $output .= ' class=' . $className;
                    }
                    $output .= ' title="' . $item['progguiddesc'] . '"';
                    $output .= '>';
                    if ($mode == 'internal') {
                        $output .= '(<a href="StaffAssignParticipants.php?selsess=' . $id . '" title="Edit session participants">' . $id . '</a>) ';
                        $output .= '<a href="EditSession.php?id=' . $id . '" title="Edit session">' . $row['name'] . '</a>' ;
                    } else if ($mode == 'publication') {
                        $output .= '<div title="' . $item['progguiddesc'] . '">' . $row['name'] . '</div>';
                    } else {
                        $output .= '(' . $id . ') ';
                        $output .= $row['name'];
                    }
                    if ($actual_start_time != '') {
                        $output .= $actual_start_time;
                    }
                    if ($mode == 'internal' and $type != 'event') {
                        $output .= '<br />' . $type;
                    }
                    //$output .= '<br />' . $cols;
                    //$output .= '<br />' . $slots;
                    $output .= '</td>' . "\n";
                }
            }

            $output .= '</tr>' . "\n";


            //test 1 set
            //if ($flag2 > 5)    //test
            //  $output .= '<br />' . $flag2;
            //  break 2;   //test
            //test

        }
    //test 1 day
    //break;   //test
    //test
    
    }

    $output .= '</table>' . "\n";

    return $output;
}

// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------
/**
 * Return an html string of the grid css
 *
 * @access public
 * @params array    $locationarray list of locations
 * @return string
 */
function output_grid_css($locationarray)
{
    $output = '';
    $output .= '.grid{' . "\n";
    $output .= '    font-family:arial;' . "\n";
    $output .= '    font-size:12px;' . "\n";
    $output .= '    /* width:400px; */' . "\n";
    $output .= '    empty-cells: show;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= '.grid th{' . "\n";
    $output .= '    font-size:18px;' . "\n";
    $output .= '    margin:0px;' . "\n";
    $output .= '    padding:2px;' . "\n";
    $output .= '    empty-cells: show;' . "\n";
    $output .= '    border-bottom:1px solid black; /* Border bottom of table data cells */' . "\n";
    $output .= '    border-right:1px solid black;  /* Border bottom of table data cells */' . "\n";
    $output .= '    border-left:1px solid black;  /* Border bottom of table data cells */' . "\n";
    $output .= '    border-top:1px solid black;  /* Border bottom of table data cells */' . "\n";
    $output .= '    max-height:10px;' . "\n";
    $output .= '    background-color:#FFF;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= '.grid td{' . "\n";
    $output .= '    margin:0px;' . "\n";
    $output .= '    padding:2px;' . "\n";
    $output .= '    empty-cells: show;' . "\n";
    $output .= '    border-bottom:1px solid black; /* Border bottom of table data cells */' . "\n";
    $output .= '    border-right:1px solid black;  /* Border bottom of table data cells */' . "\n";
    $output .= '    border-left:1px solid black;  /* Border bottom of table data cells */' . "\n";
    $output .= '    border-top:1px solid black;  /* Border bottom of table data cells */' . "\n";
    $output .= '    max-height:10px;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= 'td.grid_date{' . "\n";
    $output .= '    font-size:18px;' . "\n";
    $output .= '    text-align: center;' . "\n";
    $output .= '    font-weight: bold;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= '.grid tbody{' . "\n";
    $output .= '    background-color:#FFF;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= '.grid thead{' . "\n";
    $output .= '    /*position:relative; */ ;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= '.grid thead tr{' . "\n";
    $output .= '    /*position:relative; */' . "\n";
    $output .= '    top:0px;' . "\n";
    $output .= '    bottom:0px;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= 'TH.setup, TD.setup{' . "\n";
    $output .= '    border-style: solid;' . "\n";
    $output .= '    border-width: 1px;' . "\n";
    $output .= '    border-color: black;' . "\n";
    $output .= '    padding: 2px;' . "\n";
    $output .= '    background: red;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= 'TH.teardown, TD.teardown{' . "\n";
    $output .= '    border-style: solid;' . "\n";
    $output .= '    border-width: 1px;' . "\n";
    $output .= '    border-color: black;' . "\n";
    $output .= '    padding: 2px;' . "\n";
    $output .= '    background: red;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    $output .= 'TH.white, TD.white{' . "\n";
    $output .= '    border-style: solid;' . "\n";
    $output .= '    border-width: 1px;' . "\n";
    $output .= '    border-color: black;' . "\n";
    $output .= '    padding: 2px;' . "\n";
    $output .= '    background: white;' . "\n";
    $output .= '}' . "\n";
    $output .= "\n";

    foreach ($locationarray as $roomname => $roomcolorcode) {
        $classname = classname($roomname);
        $output .= 'th.' . $classname . ', td.' . $classname . '{' . "\n";
        $output .= '    border-style: solid;' . "\n";
        $output .= '    border-width: 1px;' . "\n";
        $output .= '    border-color: black;' . "\n";
        $output .= '    padding: 2px;' . "\n";
        $output .= '}' . "\n";
        $output .= 'td.' . $classname . '{' . "\n";
        $output .= '    background: #' . $roomcolorcode . ';' . "\n";
        $output .= '}' . "\n";
        $output .= "\n";
    }

    return $output;
}


// --------------------------------------------------------------------------------------------
/*
 * Strip spaces from string and change to lowercase
 */
function classname($name) {
    return 'room_' . str_replace(array(' ','`','~','!','@','#','$','%','^','&','*','-','_','=','+',';',':',',','<','.','>','/','?','|'), '', strtolower($name));
}

// --------------------------------------------------------------------------------------------
/*
 * Convert time from military time to am/pm time
 */
function cnv_tm_from_military($tm) {
    list($t1, $t2) = explode(':', $tm);
    $t1 = ($t1 + 0);  // strip leading zero
    $ampm = ' am';
    if ($t1 > 12) {
        $t1 = ($t1 - 12);
        $ampm = ' pm';
    }

    if ($t1 == 12) {
        $ampm = ' pm';  //noon
    }
    if ($t1 == 0) {
        $t1 = 12;  //midnight
    }

    return ($t1 . ':' . $t2 . $ampm);
}



// --------------------------------------------------------------------------------------------
/**
 * Build a grid of scheduled events and the locations they are in
 *
 * @access public
 * @params array    $link : to database
 * @params array    $locations : list of locations
 * @params string    $mode : 'internal', 'public', 'publication'
 * @params integer    $grid_interval : 900, 3600
 * @return string
 */
function build_location_grid($link, $locations, $mode = 'internal', $grid_interval = 900) {
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $GridStartDatim=GRID_START_DATIM; // make it a variable so it can be substituted
    $GridEndDatim=GRID_END_DATIM; // make it a variable so it can be substituted

    // Build an empty location grid
    $locationGrid = build_empty_grid($GridStartDatim,
                                     $GridEndDatim,
                                     $grid_interval,
                                     $locations);

    //test
    //echo "<pre>\n";           //test
    //print_r($locationGrid);   //test
    //echo "</pre><br />\n";    //test
    //return $locationGrid;     //test
    //exit();      //test
    //test


    if ($mode == 'public') {
        // Select all scheduled events with a startdatetime and a location assignment that are public
        $query = <<<EOD
SELECT 
        SCH.sessionid AS id,
        R.display_order,
        ADDTIME("$ConStartDatim$",SCH.starttime) AS starttimeFMT,
        S.duration AS duration,
        SCH.roomid,
        S.title as name,
        S.pubstatusid,
        S.statusid,
        T.typeid AS type,
        S.progguiddesc as progguiddesc,
        S.participantlabel as participantlabel,
        GROUP_CONCAT(' ',P.pubsname, IF (POS.moderator=1,' (M)','') ORDER BY P.sortedpubsname) as 'participants' 
    FROM
                  Sessions S
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
             JOIN Types T USING (typeid)
        LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
        LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE
        S.statusid = 3                              #3 means scheduled
        AND S.pubstatusid = 2                       #2 means public only
        #AND R.on_public_grid = 1
    GROUP BY
        SCH.sessionid
    ORDER BY
        SCH.starttime;
EOD;
    } else if ($mode == 'publication') {
        // Select all scheduled events with a startdatetime and a location assignment that are public or wobegon
        $query = <<<EOD
SELECT 
        SCH.sessionid AS id,
        R.display_order,
        ADDTIME("$ConStartDatim$",SCH.starttime) AS starttimeFMT,
        S.duration AS duration,
        SCH.roomid,
        S.title as name,
        S.pubstatusid,
        S.statusid,
        T.typeid AS type,
        S.progguiddesc as progguiddesc,
        S.participantlabel as participantlabel,
        GROUP_CONCAT(' ',P.pubsname, IF (POS.moderator=1,' (M)','') ORDER BY P.sortedpubsname) as 'participants' 
    FROM
                  Sessions S
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
             JOIN Types T USING (typeid)
        LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
        LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE
        S.statusid = 3                              #3 means scheduled
        AND S.pubstatusid IN (2,4)                  #2-public,4-wobegon
    GROUP BY
        SCH.sessionid
    ORDER BY
        SCH.starttime;
EOD;
    } else {
        // Select all scheduled events with a startdatetime and a location assignment
        $query = <<<EOD
SELECT
        SCH.sessionid AS id,
        R.display_order,
        ADDTIME("$ConStartDatim$",SCH.starttime) AS starttimeFMT,
        S.duration AS duration,
        SCH.roomid,
        S.title as name,
        S.pubstatusid,
        S.statusid,
        T.typeid AS type,
        S.progguiddesc as progguiddesc,
        S.participantlabel as participantlabel,
        GROUP_CONCAT(' ',P.pubsname, IF (POS.moderator=1,' (M)','') ORDER BY P.sortedpubsname) as 'participants' 
    FROM
                  Sessions S
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
             JOIN Types T USING (typeid)
        LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
        LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE
        S.statusid = 3                        #3 means scheduled
    GROUP BY
        SCH.sessionid
    ORDER BY
        SCH.starttime;
EOD;
    }

    // test
    //echo "<pre>\n";           //test
    //print_r($query);          //test
    //echo "</pre><br />\n";    //test
    //exit();                   //test
    //return $locationGrid;     //test
    // test

    $result = mysqli_query($link, $query);

    //printf("Select returned %d rows.\n", mysqli_num_rows($result));   //test
    //exit();    //test

    if (!$result) {
        $message2 = mysqli_error($link);
        $message = $query . "<BR>" . message2 . "<BR>Error querying database. Unable to continue.<BR>";
        RenderError($message);
        exit();
    }

    //loop thru events and add to grid
    //    foreach ($result->result_array() as $row)
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $id = $row['id'];  //event id
        $locationsort = array($row['display_order'] => $row['roomid']);  //location display_order
        //$locationcnt = $this->CI->eventlocations_model->count_condition('eventLocations','eventID = '.$id);   //event may span locations
        $locationcnt = 1;    //event can no longer span locations
        $sdttm = strtotime($row['starttimeFMT']);
        sscanf($row['duration'], "%d:%d", $hours, $minutes);
        $time_seconds = $hours * 3600 + $minutes * 60;
        $edttm = strtotime("+".($time_seconds).' seconds', $sdttm);
        $type = 'event';
        if (($row['type'] == 3) or ($row['type'] == 21) or ($row['type'] == 22)) {
            $type = 'setup';
        }
        $panelistInfo = '';
        if (isset($row['participants'])) {
            $panelistInfo = "\n" . $row['participantlabel'] . ':' . $row['participants'];
        }
        $eventInfo = array('id' => $id,
                           'locationcnt' => $locationcnt,
                           'sdttm' => date('Y-m-d H:i:s',$sdttm),
                           'edttm' => date('Y-m-d H:i:s',$edttm),
                           'eventlength' => $row['duration'],
                           'type' => $type,
                           'name' => $row['name'],
                           'progguiddesc' => htmlspecialchars($row['progguiddesc']) . htmlspecialchars($panelistInfo)
                           );

        //if ($id == 363) {
        //    print_r($eventInfo);
        //    print_r($grid_interval);
        //}
        $locationGrid = assign_event_to_locationgrid($locationGrid, $locationsort, $eventInfo, $grid_interval);
    }
    mysqli_free_result($result);

    // test
    //echo "<pre>\n";    //test
    //print_r($locationGrid);
    //echo "</pre><br />\n";    //test
    //exit();    //test
    // test

    return $locationGrid;
}


// --------------------------------------------------------------------------------------------
/**
 * Build two arrays of locations and location css
 *
 * @access public
 * @params array    $link : to database
 * @params array    $locations : list of locations
 * @params string    $mode : 'internal', 'public', 'publication'
 * @params integer    $grid_interval : 900, 3600
 * @return string
 */
function build_location_arrays($link) {
    global $locations, $locationscss;

    //This query only grabs rooms that have public programming in them.
    $query = <<<EOD
SELECT
        R.roomid,
        R.display_order,
        R.roomname,
        RC.roomcolorcode
    FROM
        Rooms R
        JOIN RoomColors RC using (roomcolorid)
    WHERE
        ( R.roomid IN
            (SELECT
                    DISTINCT SCH.roomid
                FROM
                         Schedule SCH
                    JOIN Sessions S USING (sessionid)
                WHERE
                    S.pubstatusid != 3 # not Do Not Print
            )
        OR R.roomname LIKE '%dummy%' )
        #AND R.on_public_grid = 1
    ORDER BY
        R.display_order
EOD;
    $result=mysqli_query($link, $query);

    if (!$result) {
        $message2 = mysqli_error($link);
        $message = $query . "<BR>" . message2 . "<BR>Error querying database. Unable to continue.<BR>";
        RenderError($message);
        return false;
    }

    //Make an array of the rooms/locations that will be in the grid.
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $locations[$row['display_order']] = $row['roomid'];
        $locationscss[$row['roomname']] = $row['roomcolorcode'];
    }
    mysqli_free_result($result);

    return true;
}