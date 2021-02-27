<?php
/**
 * Generate the SQL to insert participants into Zambia for testing purposes.
 * Assumed schema:
 * 
 * TABLE CongoDump (
 *   badgeid varchar(15) NOT NULL DEFAULT '',
 *   firstname varchar(30) DEFAULT NULL,
 *   lastname varchar(40) DEFAULT NULL,
 *   badgename varchar(51) DEFAULT NULL,
 *   phone varchar(100) DEFAULT NULL,
 *   email varchar(100) DEFAULT NULL,
 *   postaddress1 varchar(100) DEFAULT NULL,
 *   postaddress2 varchar(100) DEFAULT NULL,
 *   postcity varchar(50) DEFAULT NULL,
 *   poststate varchar(25) DEFAULT NULL,
 *   postzip varchar(10) DEFAULT NULL,
 *   postcountry varchar(25) DEFAULT NULL,
 *   regtype varchar(40) DEFAULT NULL,
 *   PRIMARY KEY (badgeid)
 *
 * TABLE Participants (
 *   badgeid varchar(15) NOT NULL DEFAULT '',
 *   password varchar(32) DEFAULT NULL,
 *   bestway varchar(12) DEFAULT NULL,
 *   interested tinyint(1) DEFAULT NULL,
 *   bio text,
 *   pubsname varchar(50) DEFAULT NULL,
 *   share_email tinyint(11) DEFAULT '1',
 *   staff_notes text,
 *   use_photo tinyint(4) DEFAULT NULL,
 *   PRIMARY KEY (badgeid)
 *
 * TABLE UserHasPermissionRole (
 *   badgeid varchar(15) NOT NULL DEFAULT '',
 *   permroleid int(11) NOT NULL DEFAULT '0',
 *   PRIMARY KEY (badgeid,permroleid),
 *   KEY FK_UserHasPermissionRole (permroleid),
 *   CONSTRAINT UserHasPermissionRole_ibfk_1 FOREIGN KEY (badgeid) REFERENCES Participants (badgeid),
 *   CONSTRAINT UserHasPermissionRole_ibfk_2 FOREIGN KEY (permroleid) REFERENCES PermissionRoles (permroleid)
 *
 * For StaffSendEmailCompose.php we need these fields:
 *
 * CD.badgeid, CD.firstname, CD.lastname, CD.email
 * P.badgeid, P.pubsname, CD.badgename, P.bio
 * UserHasPermissionRole.badgeid, .permroleid
 * permroleid = 3 for Program Participant
 */
$n_users = 200;
$start_id = 200001;
$roles = [ 'Admin' => 1, 'Staff' => 2, 'Participant' => 3, 'Test' => 12];
$roleid = $roles['Test'];
$interested = 1; /* YES */
for ($i = 0; $i < $n_users; $i++) {
    $badgeid = $start_id + $i;
    $firstname = "John-$badgeid";
    $lastname = "Smith";
    $email = "john.smith.$badgeid@carterhaugh.net";
    $pubsname = "$firstname $lastname";
    $badgename = $pubsname;
    $bio = "Test Participant - Can Delete";
    echo "\n";
    echo "BEGIN;\n";
    echo "INSERT INTO CongoDump(badgeid, badgename, firstname, lastname, email) VALUES($badgeid, '$badgename', '$firstname', '$lastname', '$email');\n";
    echo "INSERT INTO Participants(badgeid, pubsname, bio, interested) VALUES($badgeid, '$pubsname', '$bio', $interested);\n";
    echo "INSERT INTO UserHasPermissionRole(badgeid, permroleid) VALUES($badgeid, $roleid);\n";
    echo "COMMIT;\n";
}
