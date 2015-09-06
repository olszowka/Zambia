<?php
/*
 * Populate tables of prior con data to be used by the "Import Session" command.
 * 
 * Zambia allows staff users to search Session data from prior cons to
 * import into this year's con.  This might include particularly popular
 * sessions which they think should be run again or, perhaps, sessions
 * which had to be cancelled at the last minute.  One of the nuances of
 * this process is that the list of tracks changes from year to year, so
 * they must be mapped as part of the search and import process.
 * 
 * The tables involved are
 *  PreviousCons,
 *  PreviousConTracks,
 *  PreviousSessions, and
 *  TrackCompatibility.
 * I think you can take the contents of those tables from the arisia12_prod,
 * append the data from Tracks and Sessions from arisia12_prod, and load it into
 * arisia13_prod.  Look at the schema and contents of these tables from
 * arisia12_prod and let me know if you have any questions.
 * 
 *   --PeterO (August 2012)
 *
 * Later email indicated that PreviousParticipants should contain last year's participants (only).
 * This script now incorporates that as well.
 *
 * This script assumes that wipe-con-data.sql has already been run on a copy of the previous year's database.
 * Hence it fetches some data from the previous year's database itself.
 * It would be simpler to incorporate wipe-con-data into this script at an appropriate point after this script
 * has used the previous year's info; then this script would only need to consult the current database.
 */
require_once 'XPDO.inc';

error_reporting(E_ALL);

$test = false;

$host = 'localhost';
$lastYear = 14;
$thisYear = 15;

$lastDBname = "hosting_zambia_{$lastYear}";
$thisDBname = "hosting_zambia_{$thisYear}";
if ($test) {
    $thisDBname .= '_test';
    print "Using test database {$thisDBname}\n";
} else {
    print "Using LIVE database {$thisDBname}\n";
}

$password = getPassword();
if ($password === '') {
    print "Assuming your MySQL account needs no password\n";
}
    
$thisDB = new XPDO("mysql:host=$host;dbname=$thisDBname", $_ENV['LOGNAME'], $password);

// $lastDB = new XPDO("mysql:host=$host;dbname=$lastDBname", 'dfranklin', '');
$lastDB = $thisDB;

$thisDB->beginTransaction();

/************************ Truncate Last Year's Data ********************/

/**
 * Data from these tables are not needed by subsequent code - truncate now
 */
$truncateList = array(
    'UserHasPermissionRole',
    'ParticipantAvailability',
    'ParticipantAvailabilityTimes',
    'ParticipantAvailabilityDays',
    'ParticipantHasCredential',
    'ParticipantHasRole',
    'ParticipantInterests',
    'ParticipantOnSession',
    'ParticipantSessionInterest',
    'ParticipantSuggestions',
    'CongoDump',
    'Schedule',
    'SessionHasFeature',
    'SessionHasService',
    'SessionEditHistory',
    'SessionHasPubChar',
    'Sessions',
);

foreach ($truncateList as $tableName) {
    $thisDB->queryParams("truncate $tableName");
}

/************************ PreviousCons ****************************/

$previousConsCols = array('previousconid', 'previousconname', 'display_order');

/* Arisia 2013 DB had truncated PreviousCons, but 2014 did not; I expect future DBs will not */
if (false) {
    /* Copy last year's PreviousCons table to this year's */
    $lastPreviousCons = $lastDB->loadTable('PreviousCons', $previousConsCols, PDO::FETCH_NUM);
    $thisDB->insertRows('PreviousCons', $previousConsCols, $lastPreviousCons);
}

/* Add a row for last year's con */
print "Adding PreviousCons row for previous year\n";
$maxConId = $thisDB->selectOne('select max(previousconid) from PreviousCons', array(), PDO::FETCH_COLUMN);
if (empty($maxConId)) {
    $maxConId = 0;
    print "Using 0 for maxConId\n";
}
$lastConId = $maxConId + 1;
$thisDB->insertRow(
    'PreviousCons',
    array(
        'previousconid' => $lastConId,
        'previousconname' => "Arisia '$lastYear",
        'display_order' => $lastConId
    )
);

/************************ PreviousConTracks ***************************/

$previousConTracksCols = array('previoustrackid', 'previousconid', 'trackname');

/* Arisia 2013 DB had truncated PreviousConTracks, but 2014 did not; I expect future DBs will not */
if (false) {
    /* Copy last year's PreviousConTracks table to this year's */
    $lastPreviousConTracks = $lastDB->loadTable('PreviousConTracks', $previousConTracksCols, PDO::FETCH_NUM);
    $thisDB->insertRows('PreviousConTracks', $previousConTracksCols, $lastPreviousConTracks);
}

/* Add last year's tracks to this year's PreviousConTracks */
print "Adding last year's tracks to PreviousConTracks\n";
$tracksCols = array('trackid', 'trackname', 'display_order', 'selfselect');
$lastTracks = $lastDB->loadTable('Tracks', $tracksCols, PDO::FETCH_ASSOC);
foreach ($lastTracks as $lastTrack) {
    $previousConTrack = array(
        'previoustrackid' => $lastTrack['trackid'],
        'previousconid' => $lastConId,
        'trackname' => $lastTrack['trackname'],
    );
    $thisDB->insertRow('PreviousConTracks', $previousConTrack);
}

/************************ PreviousSessions ****************************/

$previousSessionsCols = array(
    'previousconid',
    'previoussessionid',
    'previoustrackid',
    'previousstatusid',
    'typeid',
    'divisionid',
    'languagestatusid',
    'title',
    'secondtitle',
    'pocketprogtext',
    'progguiddesc',
    'persppartinfo',
    'duration',
    'estatten',
    'kidscatid',
    'signupreq',
    'notesforpart',
    'notesforprog',
    'invitedguest',
    /* 'importedsessionid' -- do not copy, default to NULL */
);

/* Arisia 2013 DB had truncated PreviousSessions, but 2014 did not; I expect future DBs will not */
if (false) {
    /* Copy last year's PreviousSessions table to this year's */
    $lastPreviousSessions = $lastDB->loadTable('PreviousSessions', $previousSessionsCols, PDO::FETCH_NUM);
    $thisDB->insertRows('PreviousSessions', $previousSessionsCols, $lastPreviousSessions);
}

/* Add last year's sessions to this year's PreviousSessions */
print "Adding last year's sessions to PreviousSessions\n";
$sessionsCols = array(
    'sessionid', 
    'trackid',
    'statusid',
    'typeid',
    'divisionid',
    'languagestatusid',
    'title',
    'secondtitle',
    'pocketprogtext',
    'progguiddesc',
    'persppartinfo',
    'duration',
    'estatten',
    'kidscatid',
    'signupreq',
    'notesforpart',
    'notesforprog',
    'invitedguest',
);
/* These Sessions columns are not used by PreviousSessions so we don't bother loading them */
$sessionsColsNotUsed = array(
    'pubstatusid',
    'pubsno',
    'roomsetid',
    'servicenotes',
    'warnings',
    'ts',
);

$lastSessions = $lastDB->loadTable('Sessions', $sessionsCols, PDO::FETCH_ASSOC);
foreach ($lastSessions as $lastSession) {
    $previousSession = array(
        'previousconid'    => $lastConId,
        'previoussessionid' => $lastSession['sessionid'],
        'previoustrackid'  => $lastSession['trackid'],
        'previousstatusid' => $lastSession['statusid'],
    );
    $copyCols = array_intersect($sessionsCols, $previousSessionsCols);
    foreach ($copyCols as $col) {
        $previousSession[$col] = $lastSession[$col];
    }
    $thisDB->insertRow('PreviousSessions', $previousSession);
}

/********************* TrackCompatibility ***********************/

$trackCompatibilityCols = array('previousconid', 'previoustrackid', 'currenttrackid');

/* Arisia 2013 DB had truncated TrackCompatibility, but 2014 did not; I expect future DBs will not */
if (false) {
    /* Copy last year's TrackCompatibility table to this year's */
    $lastTrackCompatibility = $lastDB->loadTable('TrackCompatibility', $trackCompatibilityCols, PDO::FETCH_NUM);
    $thisDB->insertRows('TrackCompatibility', $trackCompatibilityCols, $lastTrackCompatibility);
}

print "Adding TrackCompatibility entries for Arisia {$thisYear}\n";
/**
 * In 2013 the Crafting track (15) was renamed to Maker, and a new ConComm track (100) was added,
 * but no other changes were made from 2012 to 2013.
 * Right now it appears no one has modified 2014's Tracks.
 * So we can just add identity mapping for last year's tracks.
 */
$lastTracks = $lastDB->loadTable('Tracks', $tracksCols, PDO::FETCH_ASSOC);
foreach ($lastTracks as $lastTrack) {
    $trackCompatibilityRow = array(
        'previousconid' => $lastConId,
        'previoustrackid' => $lastTrack['trackid'],
        'currenttrackid' => $lastTrack['trackid'],
    );
    $thisDB->insertRow('TrackCompatibility', $trackCompatibilityRow);
}

/******************** PreviousParticipants *************************/

$previousParticipantsCols = array('badgeid', 'bio', 'staff_notes');

print "Replacing PreviousParticipants from previous year Participants table\n";
/* This table is replaced each year with the previous year's participants (only) */
$thisDB->queryParams('truncate PreviousParticipants', array());
$lastParticipants = $lastDB->loadTable('Participants', $previousParticipantsCols, PDO::FETCH_NUM);
$thisDB->insertRows('PreviousParticipants', $previousParticipantsCols, $lastParticipants);
$thisDB->queryParams('truncate Participants');

/******************** Brainstorm Account ******************************/

$participantsCols = array('badgeid', 'pubsname', 'password', 'bestway', 'interested', 'bio');
$thisDB->insertRows(
    'Participants',
    $participantsCols,
    array(array('brainstorm',null,'ecf65a5d41056d7dd4d548e3ef200476',null,null,null))
);
$thisDB->insertRows(
    'UserHasPermissionRole', array('badgeid','permroleid'), array(array('brainstorm',5))
);

$thisDB->commit();

/************************ Functions ******************************/

function getPassword()
{
    $iniFile = $_ENV['HOME'] . '/.my.cnf';

    if (!file_exists($iniFile)) {
        return '';
    }

    $ini = parse_ini_file($iniFile);
    if (!isset($ini['password'])) {
        return '';
    }
    return $ini['password'];
}

