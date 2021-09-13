<?php
use CommonMark\Node;
//	Copyright (c) 2021 Syd Weinstein. All rights reserved. See copyright document for more details.
//  This page is NOT part of Zambia, but piggybacks on its verification system for convenience
global $title;
global $rankballots, $rankfinalists, $debuglevel, $exactwinner, $rankwinner, $catvotes, $winningvotes, $total_votes, $total_rank1, $finalists, $roundwinners;
$title = "Hugo Voting";
$bootstrap4 = true;
require_once('StaffCommonCode.php');
$fbadgeid = getInt("badgeid");
$debuglevel = getInt("showwork");
staff_header($title, $bootstrap4);
if (!isLoggedIn() || !may_I('Staff') || !may_I('d3_hugovoting')) {
    staff_footer();
    exit();
}
if ($fbadgeid) {
    echo "<script type=\"text/javascript\">fbadgeid = $fbadgeid;</script>\n";
}
$pgconn = null;
if ($pgconn == null) {
    //error_log("making new Postgress connection");
    $pgconn = pg_connect(WELLINGTONPROD);
    if (!$pgconn) {
        echo("Unable to connect to Wellington\n");
        exit();
    }
}
// perform the run-off test vs no-award
function runofftest($winners, $place) {
    global $rankballots, $rankfinalists, $debuglevel, $exactwinner, $rankwinner, $catvotes, $winningvotes, $total_votes, $total_rank1, $roundwinners;

    if ($debuglevel & 1) {
        echo "In runofftest(winners, $place)<br>";
        echo print_r($winners);
        echo "<br>";
    }
    $runoffwinners = array();
    foreach ($winners as $leader) {
            if ($leader != 'No Award') {
                // loop over original ballots
                // If the PW ranks higher than No Award (or the PW is ranked and NA isn’t mentioned), count this as a YES vote for the PW.
                // If No Award ranks higher than the PW (or NA is ranked and the PW isn’t mentioned), count this as a NO vote against the PW.

                $noawardrank = array();
                $reservations = array();
                $leaderrank = array();
                foreach ($catvotes as $pos => $ballot) {
                    $reservations[$ballot['reservation_id']] = true;
                    if ($ballot['short_name'] == $leader)
                        $leaderrank[$ballot['reservation_id']] = $ballot['position'];
                    if ($ballot['short_name'] == 'No Award')
                        $noawardrank[$ballot['reservation_id']] = $ballot['position'];
                }
                // now count the votes
                $leaderfirst = 0;
                $noawardfirst = 0;
                foreach ($reservations as $reservation_id => $value) {
                    if (array_key_exists($reservation_id, $noawardrank) && array_key_exists($reservation_id, $leaderrank)) {
                        if ($noawardrank[$reservation_id] < $leaderrank[$reservation_id])
                            $noawardfirst++;
                        else
                            $leaderfirst++;
                    } else if (array_key_exists($reservation_id, $noawardrank))
                        $noawardfirst++;
                    else if (array_key_exists($reservation_id, $leaderrank))
                        $leaderfirst++;
                }
                if ($debuglevel & 32) {
                    echo "$leader: noawardfirst: $noawardfirst, leaderfirst: $leaderfirst<br>";
                }
                if ($noawardfirst > $leaderfirst)
                    $leader = 'No Award';
            }
            array_push($runoffwinners, $leader);
        }
    $runoffwinners = array_unique($runoffwinners);
    if (count($runoffwinners) == 1) {
        echo "<i><strong>$place Winner: " . current($runoffwinners) . "</strong></i><br>";
        $roundwinners = $runoffwinners;
        return true;
    }
    $winnercount = 0;
    echo "<i><strong>$place Winner: ";
    $delim = "";
    $roundwinners = array();
    foreach ($runoffwinners as $name) {
        if ($name != 'No Award') {
            echo "$delim$name";
            $delim = ", ";
            $winnercount++;
            array_push($roundwinners, $name);
        }
    }
    if ($winnercount > 1)
        echo " (tie)";
    echo "</strong></i><br>";
}

function run_rank($rankpass, $place) {
    global $rankballots, $rankfinalists, $debuglevel, $exactwinner, $rankwinner, $winningvotes, $total_votes, $total_rank1;

    if ($debuglevel & 1) {
        echo "In run_rank($rankpass, $place)<br>";
    }
    if ($debuglevel & 2) {
            echo "starting rank pass $rankpass<br>";
            echo "votes before elimination " . count($rankballots) . ", in " . count($rankfinalists) . " categories<br>";
    }
    if ($rankpass > 1) {
        // find the lowest rank person and delete that one, look for ties
        $minvotes = $total_votes;
        $eliminate = array();
        foreach ($rankwinner as $name => $count) {
            if ($count < $minvotes) {
                $minvotes = $count;
                $eliminate = array($name);
            } else if ($minvotes == $count) {
                array_push($eliminate, $name);
            }
        }
        if (count($eliminate) > 1) {
            // break ties as who has the minimim number of first place votes
            if ($debuglevel & 32) {
                echo "prior to tiebreak eliminating ($minvotes): ";
                echo print_r($eliminate);
                echo "&nbsp;<br>";
            }
            $new_eliminate = array();
            $minvotes = $total_votes;
            foreach ($eliminate as $name) {
                if ($exactwinner[$name] < $minvotes) {
                    $minvotes = $exactwinner[$name];
                    $new_eliminate = array($name);
                } else if ($minvotes == $exactwinner[$name]) {
                    array_push($new_eliminate, $name);
                }
            }
            if ($debuglevel & 16) {
                echo "after tie test, count(eliminate) = " . count($eliminate) . ", count(new_eliminate) = " . count($new_eliminate) . "<br>";
            }
            $eliminate = $new_eliminate;
        }
        if ($debuglevel & 32) {
            echo "eliminating ($minvotes): ";
            echo print_r($eliminate);
            echo "&nbsp;<br>";
        }

        // if the number to eliminate == the numnber of ballots number left in ranking we have a tie, all in eliminate are winners
        if (count($eliminate) == count($rankfinalists)) {
            runofftest($rankfinalists, $place);
            return true;
        }

        if ($debuglevel & 128) {
            echo "rank ballots before elimination:<br>";
            echo print_r($rankballots);
            echo "&nbsp;<br>";
        }

        // do the eliminations - remove all votes for the items in the eliminate array
        foreach ($eliminate as $name) {
            if ($debuglevel & 32) {
                echo "eliminating '$name'<br>";
            }

            foreach ($rankfinalists as $pos => $final) {
                if ($final == $name)
                    unset($rankfinalists[$pos]);
            }

            foreach ($rankballots as $pos => $ballot) {
                if ($ballot["short_name"] == $name)
                    unset($rankballots[$pos]);
            }
        }

        if ($debuglevel & 1)
            echo "votes after elimination " . count($rankballots) . ", in " . count($rankfinalists) . " categories<br>";

        if ($debuglevel & 32) {
            echo "rankfinalists after elimination:<br>";
            echo print_r($rankfinalists);
            echo "&nbsp;<br>";
        }
        if ($debuglevel & 128) {
            echo "rank ballots after elimination:<br>";
            echo print_r($rankballots);
            echo "&nbsp;<br>";
        }
    }

    // now count the highest (lowest position) rank for each ballot
    $votes = array();
    foreach ($rankballots as $pos => $ballot) {
        if (array_key_exists($ballot['short_name'], $votes)) {
            if ($ballot['position'] < $votes[$ballot['short_name']])
                $votes[$ballot['reservation_id']] = $ballot['position'];
        } else {
            $votes[$ballot['reservation_id']] = $ballot['position'];
        }
    }

    if ($debuglevel & 128) {
        echo "votes aray<br>";
        echo print_r($votes);
        echo "&nbsp;";
    }

    // and then count the actual votes for that positions
    $rankwinner = array();
    foreach ($rankfinalists as $id => $name) {
        $rankwinner[$name] = 0;
    }
    $total_toprank = 0;
    foreach ($rankballots as $pos => $ballot) {
        if ($ballot['position'] == $votes[$ballot['reservation_id']]) {
            $rankwinner[$ballot['short_name']]++;
            $total_toprank++;
        }
    }
    arsort($rankwinner, 1);
    $winningvotes = floor($total_toprank/ 2) + 1;

    if ($debuglevel & 16) {
        echo "&nbsp;<br>Rank $rankpass votes to win: $winningvotes<br>";
        foreach ($rankwinner as $name => $count) {
            $percent = round( 100 * ($count / $total_rank1), 1);
            echo "$name: $count ($percent)";
            if ($count > $winningvotes)
                echo "&nbsp; <i><strong>Potential Winner</strong></i>";
            echo "<br>";
        }
    }

    // see if the rank vote gets a winner
    $leader = array_key_first($rankwinner);
    $votes = $rankwinner[$leader];
    if ($debuglevel & 16) {
        echo "testing $leader($votes) for $winningvotes<br>";
    }

    if ($votes >= $winningvotes) {
    // special check for no award if winner is not noaward
        runofftest(array($leader), $place);
        return true;
     }
}

function process_cat($place) {
    global $rankballots, $rankfinalists, $debuglevel, $exactwinner, $rankwinner, $catvotes, $winningvotes, $total_votes, $total_rank1, $finalists, $runoffwinners;

    if ($debuglevel & 1) {
        echo "In process_cat($place)<br>";
    }
    arsort($exactwinner, 1);

    if ($debuglevel & 32) {
        echo "&nbsp;<br>Raw votes (before rank voting)<br>total votes: $total_votes, total ranked 1 = $total_rank1, to win: $winningvotes<br>";
        foreach ($exactwinner as $name => $count) {
            $percent = round( 100 * ($count / $total_rank1), 1);
            echo "$name: $count ($percent)";
            if ($count > $winningvotes)
                echo "&nbsp; <i><strong> Raw Winner</strong></i>";
            echo "<br>";
        }
    }

    // see if the raw vote gets a winner
    $leader = array_key_first($exactwinner);
    $votes = $exactwinner[$leader];
    if ($debuglevel & 16) {
        echo "testing $leader($votes) for $winningvotes<br>";
    }

    if ($votes >= $winningvotes) {
        echo "<i><strong>$place Winner: $leader</strong></i><br>";
        $runoffwinners = array($leader);
        return;
    }

    // ok, no direct winner, loop over the ballots
    $rankfinalists = $finalists;
    $rankballots = $catvotes;
    $rankwinner = $exactwinner;
    $rankpass = 1;
    while (count($rankfinalists) > 1) {
        $rankpass++;
        if ($rankpass > 10)  // emergency exit for now, find bug
            break;
        if (run_rank($rankpass, $place))
            break;
    }
}

//error_log("postgress connection good");
echo "<h1>Hugo Vote Counting Testing</h1>\n";

$sql = 'select count(distinct reservation_id) as valid_ballots from ranks;';
$result = pg_query($pgconn, $sql);
if (!$result) {
    echo("Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING) . '\n');
    exit();
}
$valid_ballots = -1;
while ($row = pg_fetch_assoc($result)) {
    $valid_ballots = $row['valid_ballots'];
}
pg_free_result($result);

echo "<h3>Total ballots cast: $valid_ballots</h3>\n";

$sql = 'select id, name from categories order by id;';
$result = pg_query($pgconn, $sql);
if (!$result) {
    echo("Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING) . '\n');
    exit();
}
$categories = array();
while ($row = pg_fetch_assoc($result)) {
    $categories[$row['id']] = $row['name'];
}
pg_free_result($result);

foreach ($categories as $category_id => $name) {
    echo "<h4>Category: $name</h4>\n";
    $sql = <<<EOD
select count(*) as cast_ballots
from ranks r
join finalists f ON (r.finalist_id = f.id)
where f.category_id = $category_id and r.position = 1 and mod(r.finalist_id, 7) <> 0;
EOD;

    $result = pg_query($pgconn, $sql);
    if (!$result) {
        echo("Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING) . '\n');
        exit();
    }

    $cast_ballots = -1;
    while ($row = pg_fetch_assoc($result)) {
        $cast_ballots = $row['cast_ballots'];
    }
    pg_free_result($result);

    $percentcast = round( 100 * ($cast_ballots / $valid_ballots), 1);

    echo "Cast Ballots: $cast_ballots ($percentcast%)<br>\n";

    //3.12.2: “No Award” shall be given whenever the total number of valid ballots cast for a specific category
    //(excluding those cast for “No Award” in first place) is less than twenty-five percent (25%)
    //of the total number of final Award ballots received.
    if ($percentcast < 25) {
        echo "<i>Too few ballots cast: No Award</i><br>\n";
        continue;
    }

    $place = "1st";
    // load the finalists for this category
    $finalists = array();

    $sql = <<<EOD
SELECT short_name
from finalists
where category_id = $category_id
order by id
EOD;
    $result = pg_query($pgconn, $sql);
    if (!$result) {
        echo("Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING) . '\n');
        exit();
    }
    while ($row = pg_fetch_assoc($result)) {
        array_push($finalists, $row['short_name']);
    }
    pg_free_result($result);

    if ($debuglevel & 16) {
        echo "&nbsp;<br>Finalists: <br>";
        echo print_r($finalists);
        echo "&nbsp;<br>";
    }

    // load the votes for this category
    $catvotes = array();
    $exactwinner = array();
    foreach ($finalists as $id => $name) {
        $exactwinner[$name] = 0;
    }

    $sql = <<<EOD
SELECT r.id,r.reservation_id, f.short_name, r.position
from ranks r
join finalists f ON (r.finalist_id = f.id)
where f.category_id = $category_id
order by r.position;
EOD;

    $result = pg_query($pgconn, $sql);
    if (!$result) {
        echo("Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING) . '\n');
        exit();
    }
    $total_votes = 0;
    $total_rank1 = 0;
    while ($row = pg_fetch_assoc($result)) {
        if ($row['position'] == 1) {
            $total_rank1++;
            $exactwinner[$row['short_name']]++;
        }
        $total_votes++;
        array_push($catvotes, $row);
    }
    pg_free_result($result);

    $winningvotes = floor($total_rank1/ 2) + 1;

    process_cat($place);
    $places = array("2nd", "3rd", "4th", "5th");
    // now for 2nd through 5th place
    foreach ($places as $place) {
        if ($debuglevel & 16) {
            echo "Starting $place place<br>";
            echo "Starting count ballots = " . count($catvotes) . "<br>";
        }

        // first delete all the votes for the prior round winner
        foreach ($roundwinners as $winner) {
            if ($debuglevel & 16)
                echo "Clearing $winner<br>";

            foreach ($catvotes as $id => $ballot) {
                if ($ballot['short_name'] == $winner)
                    unset($catvotes[$id]);
            }
            foreach ($finalists as $id => $final) {
                if ($final == $winner)
                    unset($finalists[$id]);
            }
        }

        // ok, no direct winner, loop over the ballots
        $rankfinalists = $finalists;
        $rankballots = $catvotes;
        $rankwinner = array();
        $rankpass = 1;
        run_rank($rankpass, $place);
        $exactwinner = $rankwinner;

        process_cat($place);
    }
}

staff_footer();
?>
