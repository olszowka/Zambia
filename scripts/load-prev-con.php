<?php
/*
 * This script takes a database that is a complete duplicate of last year's Zambia database
 * and prepares it to be used for this year.  It does the following:
 *  1. Truncates the tables that should be truncated (formerly done by wipe_con_data.sql)
 *  2. Populates tables of prior con data to be used by the "Import Session" command (see below)
 *  3. Adds the "brainstorm" participant
 * 
 * "Import Session": Zambia allows staff users to search Session data from prior cons to
 * import into this year's con.  This might include particularly popular
 * sessions which they think should be run again or, perhaps, sessions
 * which had to be cancelled at the last minute.
 *
 * One of the nuances of this process is that the list of tracks may change from year to year, so
 * they must be mapped as part of the search and import process.  To handle this mapping, see the custom code
 * in this script.  If the track list does not change, no changes to this script are needed.
 * 
 * The tables involved are
 *  PreviousCons
 *  PreviousConTracks
 *  PreviousSessions
 *  TrackCompatibility
 *  PreviousParticipants (should contain last year's participants only).
 * 
 * (Parts of the above adapted from email sent by PeterO August 2012)
 *
 * This script truncates CongoDump; the separate import script needs to be run to populate it
 * and keep it in sync.
 */
require_once 'XPDO.inc';

error_reporting(E_ALL);

/**
 * These should be updated each year
 */
$test = false;
$lastYear = 18;
$thisYear = 19;
$truncate = true; /* True if the Zambia DB is not yet in use */
$brainstorm = true; /* Leave true if the Zambia Participants table does not have a brainstorm entry */
/**
 * End of section that needs updating each year
 */

$host = 'localhost';
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

/**
 * The next line allows you to separate cases where we need last year's database
 * from this year's.  Set last to this one if this year's DB is a complete copy of last year's
 * when this script is run.
 */
$lastDB = $thisDB;

/**
 * Badge IDs for special people.
 */
$badges = [ 'DanF' => 5225, 'PeterO' => 53159 ];

$thisDB->beginTransaction();

/******************** Participants to keep ************************/

$plist = $lastDB->getPlist(count($badges));
$badgeInfo = $lastDB->selectAll("select * from Participants where badgeid in ($plist)", array_values($badges));
$permInfo = $lastDB->selectAll("select * from UserHasPermissionRole where badgeid in ($plist)", array_values($badges));
$congoInfo = $lastDB->selectAll("select * from CongoDump where badgeid in ($plist)", array_values($badges));

/************************ Truncate Last Year's Data ********************/

if ($truncate) {
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
        'ParticipantOnSessionHistory',
        'ParticipantSessionInterest',
        'ParticipantSuggestions',
        'CongoDump',
        'Schedule',
        'SessionHasFeature',
        'SessionHasService',
        'SessionEditHistory',
        'SessionHasPubChar',
        'EmailHistory',
        // 'Sessions',  -- Can't truncate yet, needed to populate PreviousSessions
    );

    foreach ($truncateList as $tableName) {
        $thisDB->queryParams("truncate $tableName");
    }
    print "Truncated " . join(' ', $truncateList) . "\n";
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

if ($truncate) {
    // Now we can truncate Sessions
    print "Truncating Sessions\n";
    // Use delete; truncate fails because Sessions is the target of a foreign key
    $thisDB->queryParams("delete from Sessions");
    echo "Truncated Sessions\n";
}

/********************* TrackCompatibility ***********************/

$trackCompatibilityCols = array('previousconid', 'previoustrackid', 'currenttrackid');

/* Arisia 2013 DB had truncated TrackCompatibility, but 2014 did not; I expect future DBs will not */
if (false) {
    print "Copy last year's TrackCompatibility table to this year's\n";
    $lastTrackCompatibility = $lastDB->loadTable('TrackCompatibility', $trackCompatibilityCols, PDO::FETCH_NUM);
    $thisDB->insertRows('TrackCompatibility', $trackCompatibilityCols, $lastTrackCompatibility);
}

print "Adding TrackCompatibility entries for Arisia {$thisYear}\n";
/**
 * In 2013 the Crafting track (15) was renamed to Maker, and a new ConComm track (100) was added,
 * but no other changes were made from 2012 to 2013.
 * No further changes were made in 2014 - 2016.
 * So we can just add identity mapping for last year's tracks.
 */
$lastTracks = $lastDB->loadTable('Tracks', $tracksCols, PDO::FETCH_ASSOC);
$success = true;
foreach ($lastTracks as $lastTrack) {
    $trackCompatibilityRow = array(
        'previousconid' => $lastConId,
        'previoustrackid' => $lastTrack['trackid'],
        'currenttrackid' => $lastTrack['trackid'],
    );
    try {
        $thisDB->insertRow('TrackCompatibility', $trackCompatibilityRow);
        print "    Inserted track compatibility: last con's {$lastTrack['trackid']}\n";
    } catch (PDOexception $exc) {
        print $exc->getMessage();
        print "\n";
        print "    Could not insert row into TrackCompatibility:\n" . var_export($trackCompatibilityRow, true) . "\n";
        $success = false;
    }
}
if ($success === false) {
    print "\n***** One or more TrackCompatibility rows could not be inserted\n";
}

/******************** PreviousParticipants *************************/

print "Replacing PreviousParticipants with previous year Participants table contents\n";

$previousParticipantsCols = array('badgeid', 'bio', 'staff_notes');

/* This table is replaced each year with the previous year's participants (only) */
$thisDB->queryParams('truncate PreviousParticipants', array());
$lastParticipants = $lastDB->loadTable('Participants', $previousParticipantsCols, PDO::FETCH_NUM);
$thisDB->insertRows('PreviousParticipants', $previousParticipantsCols, $lastParticipants);
if ($truncate) {
    /* Use delete; truncate fails because Participants is the target of a foreign key */
    $thisDB->queryParams('delete from Participants');
}

/******************** Brainstorm Account ******************************/

if ($brainstorm) {

    print "Insert brainstorm account\n";

    $participantsCols = array('badgeid', 'pubsname', 'password', 'bestway', 'interested', 'bio');
    $thisDB->insertRows(
        'Participants',
        $participantsCols,
        array(array('brainstorm',null,'ecf65a5d41056d7dd4d548e3ef200476',null,null,null))
    );
     $thisDB->insertRows(
        'UserHasPermissionRole', array('badgeid','permroleid'), array(array('brainstorm',5))
    );
}
                               
/******************** Add administrators back ********************/

foreach ($badgeInfo as $row) {
    $id = $row['badgeid'];
    $name = $row[pubsname];
    try {
        $thisDB->insertRow('Participants', $row);
        print "Added back badge $id ($name)\n";
    } catch (Exception $exc) {
        fprintf(STDERR, "Could not insert Participant row for $id ($name): " . $exc->getMessage() . "\n");
    }
}
foreach ($permInfo as $row) {
    $id = $row['badgeid'];
    try {
        $thisDB->insertRow('UserHasPermissionRole', $row);
        print "Added back $id\n";
    } catch (Exception $exc) {
        fprintf(STDERR, "Could not insert UserHasPermissionRole row for $id: " . $exc->getMessage() . "\n");
    }
}    

foreach ($congoInfo as $row) {
    $id = $row['badgeid'];
    try {
        $thisDB->insertRow('CongoDump', $row);
        print "Added back $id\n";
    } catch (Exception $exc) {
        fprintf(STDERR, "Could not insert CongoDump row for $id: " . $exc->getMessage() . "\n");
    }
}    

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
        print "No password found in $iniFile\n";
        return '';
    }
    print "Password found in $iniFile\n";
    return $ini['password'];
}
