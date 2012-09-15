-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 15, 2012 at 09:05 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `balticon_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `categoryhasreport`
--

CREATE TABLE IF NOT EXISTS `categoryhasreport` (
  `reportcategoryid` int(11) NOT NULL,
  `reporttypeid` int(11) NOT NULL,
  PRIMARY KEY (`reportcategoryid`,`reporttypeid`),
  KEY `FK_CategoryHasReport2` (`reporttypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `congodump`
--

CREATE TABLE IF NOT EXISTS `congodump` (
  `badgeid` int(15) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(30) DEFAULT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `middleInit` varchar(1) DEFAULT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `badgename` varchar(51) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `postaddress1` varchar(100) DEFAULT NULL,
  `postaddress2` varchar(100) DEFAULT NULL,
  `postcity` varchar(50) DEFAULT NULL,
  `poststate` varchar(25) DEFAULT NULL,
  `postzip` varchar(10) DEFAULT NULL,
  `postcountry` varchar(25) DEFAULT NULL,
  `regtype` varchar(40) DEFAULT NULL,
  `regdepartment` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `congodump`
--

INSERT INTO `congodump` (`badgeid`, `firstname`, `lastname`, `middleInit`, `suffix`, `badgename`, `phone`, `email`, `postaddress1`, `postaddress2`, `postcity`, `poststate`, `postzip`, `postcountry`, `regtype`, `regdepartment`) VALUES
(1, 'Admin', 'User', 'A', NULL, 'Admin User', '867-5309', 'program@balticon.org', '3310 E Baltimore St', 'Baltimore Science Fiction Society', NULL, NULL, NULL, 'Admin User', 'BSFS', 'program'),
(2, 'Staff', 'User', 'b', NULL, 'Staff User', '1-234-567-8901', 'program@balticon.org', '3310 E Baltimore St', 'Baltimore Science Fiction Society', 'Baltimore', 'MD', NULL, 'USA', 'ConfirmedParticipant', 'Program');

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE IF NOT EXISTS `credentials` (
  `credentialid` int(11) NOT NULL AUTO_INCREMENT,
  `credentialname` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`credentialid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`credentialid`, `credentialname`, `display_order`) VALUES
(1, 'Professional Artist', 1),
(2, 'Professional Editor', 2),
(3, 'Professional Musician', 3),
(4, 'Published Author', 4),
(5, 'Web Comics Creator', 5);

-- --------------------------------------------------------

--
-- Table structure for table `customtext`
--

CREATE TABLE IF NOT EXISTS `customtext` (
  `customtextid` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(100) DEFAULT NULL,
  `tag` varchar(25) DEFAULT NULL,
  `textcontents` text,
  PRIMARY KEY (`customtextid`),
  UNIQUE KEY `page` (`page`,`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `customtext`
--

INSERT INTO `customtext` (`customtextid`, `page`, `tag`, `textcontents`) VALUES
(1, 'My Profile', 'biography_note', 'Note: Your biography will appear immediately following your name in the program. If you have previously been a participant, the stored bio for you will be displayed below. Please verify the text for publication.<BR>\n'),
(2, 'My Availability', 'note_after_times', 'Please note: the Masquerade is generally scheduled for Sat 8-10pm.');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE IF NOT EXISTS `divisions` (
  `divisionid` int(11) NOT NULL AUTO_INCREMENT,
  `divisionname` varchar(30) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`divisionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`divisionid`, `divisionname`, `display_order`) VALUES
(1, 'Programing', 1),
(2, 'Hotel', 2),
(3, 'Other', 3);

-- --------------------------------------------------------

--
-- Table structure for table `emailcc`
--

CREATE TABLE IF NOT EXISTS `emailcc` (
  `emailccid` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  `emailaddress` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`emailccid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Dumping data for table `emailcc`
--

INSERT INTO `emailcc` (`emailccid`, `description`, `display_order`, `emailaddress`) VALUES
(1, 'None', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `emailfrom`
--

CREATE TABLE IF NOT EXISTS `emailfrom` (
  `emailfromid` int(11) NOT NULL AUTO_INCREMENT,
  `emailfromdescription` varchar(30) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  `emailfromaddress` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`emailfromid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

--
-- Dumping data for table `emailfrom`
--

INSERT INTO `emailfrom` (`emailfromid`, `emailfromdescription`, `display_order`, `emailfromaddress`) VALUES
(1, 'Balticon Programming', 2, 'programming@balticon.org'),
(3, 'Balticon Con Chair', 4, 'chair@balticon.org');

-- --------------------------------------------------------

--
-- Table structure for table `emailhistory`
--

CREATE TABLE IF NOT EXISTS `emailhistory` (
  `emailid` int(11) NOT NULL,
  `badgeid` varchar(15) NOT NULL,
  PRIMARY KEY (`emailid`,`badgeid`),
  KEY `FK_EmailHistory2` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `emailqueue`
--

CREATE TABLE IF NOT EXISTS `emailqueue` (
  `emailqueueid` int(11) NOT NULL AUTO_INCREMENT,
  `emailto` varchar(255) DEFAULT NULL,
  `emailfrom` varchar(255) DEFAULT NULL,
  `emailcc` varchar(255) DEFAULT NULL,
  `emailsubject` varchar(255) DEFAULT NULL,
  `body` text,
  `status` int(11) NOT NULL,
  `emailtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`emailqueueid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emailto`
--

CREATE TABLE IF NOT EXISTS `emailto` (
  `emailtoid` int(11) NOT NULL AUTO_INCREMENT,
  `emailtodescription` varchar(75) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  `emailtoquery` text,
  PRIMARY KEY (`emailtoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emailtracking`
--

CREATE TABLE IF NOT EXISTS `emailtracking` (
  `emailid` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `senddttm` datetime DEFAULT NULL,
  PRIMARY KEY (`emailid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Dumping data for table `emailtracking`
--

INSERT INTO `emailtracking` (`emailid`, `description`, `senddttm`) VALUES
(1, 'Balticon 47 Program Invitation--development', '2012-06-16 12:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE IF NOT EXISTS `features` (
  `featureid` int(11) NOT NULL AUTO_INCREMENT,
  `featurename` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`featureid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`featureid`, `featurename`, `display_order`) VALUES
(1, 'Power (110)', 1),
(2, 'Cable TV', 2),
(3, 'Running Water', 3),
(4, 'Toilet', 4);

-- --------------------------------------------------------

--
-- Table structure for table `kidscategories`
--

CREATE TABLE IF NOT EXISTS `kidscategories` (
  `kidscatid` int(11) NOT NULL AUTO_INCREMENT,
  `kidscatname` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`kidscatid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `kidscategories`
--

INSERT INTO `kidscategories` (`kidscatid`, `kidscatname`, `display_order`) VALUES
(1, 'Targeted', 1),
(2, 'Welcome', 2),
(3, 'Only w/ Parent', 3),
(4, 'Not Allowed', 4);

-- --------------------------------------------------------

--
-- Table structure for table `languagestatuses`
--

CREATE TABLE IF NOT EXISTS `languagestatuses` (
  `languagestatusid` int(11) NOT NULL AUTO_INCREMENT,
  `languagestatusname` varchar(30) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`languagestatusid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `languagestatuses`
--

INSERT INTO `languagestatuses` (`languagestatusid`, `languagestatusname`, `display_order`) VALUES
(1, 'English', 1),
(2, 'French', 2),
(3, 'Bilingual', 3);

-- --------------------------------------------------------

--
-- Table structure for table `participantavailability`
--

CREATE TABLE IF NOT EXISTS `participantavailability` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `maxprog` int(11) DEFAULT NULL,
  `preventconflict` varchar(255) DEFAULT NULL,
  `otherconstraints` varchar(255) DEFAULT NULL,
  `numkidsfasttrack` int(11) DEFAULT NULL,
  PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participantavailabilitydays`
--

CREATE TABLE IF NOT EXISTS `participantavailabilitydays` (
  `badgeid` varchar(15) NOT NULL,
  `day` smallint(6) NOT NULL,
  `maxprog` int(11) DEFAULT NULL,
  PRIMARY KEY (`badgeid`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participantavailabilitytimes`
--

CREATE TABLE IF NOT EXISTS `participantavailabilitytimes` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `availabilitynum` int(11) NOT NULL DEFAULT '0',
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  PRIMARY KEY (`badgeid`,`availabilitynum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participanthascredential`
--

CREATE TABLE IF NOT EXISTS `participanthascredential` (
  `badgeid` varchar(15) NOT NULL,
  `credentialid` int(11) NOT NULL,
  PRIMARY KEY (`badgeid`,`credentialid`),
  KEY `phcfk2` (`credentialid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participanthasrole`
--

CREATE TABLE IF NOT EXISTS `participanthasrole` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `roleid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`badgeid`,`roleid`),
  KEY `roleid` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `participanthasrole`
--

INSERT INTO `participanthasrole` (`badgeid`, `roleid`) VALUES
('2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `participantinterests`
--

CREATE TABLE IF NOT EXISTS `participantinterests` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `yespanels` text,
  `nopanels` text,
  `yespeople` text,
  `nopeople` text,
  `otherroles` text,
  PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participantonsession`
--

CREATE TABLE IF NOT EXISTS `participantonsession` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `sessionid` int(11) NOT NULL DEFAULT '0',
  `moderator` tinyint(4) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`badgeid`,`sessionid`),
  KEY `sessionid` (`sessionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE IF NOT EXISTS `participants` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `password` varchar(32) DEFAULT NULL,
  `bestway` varchar(12) DEFAULT NULL,
  `interested` tinyint(1) DEFAULT NULL,
  `bio` text,
  `pubsname` varchar(50) DEFAULT NULL,
  `share_email` tinyint(11) DEFAULT '1',
  `staff_notes` text,
  PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`badgeid`, `password`, `bestway`, `interested`, `bio`, `pubsname`, `share_email`, `staff_notes`) VALUES
('1', '4cb9c8a8048fd02294477fcb1a41191a', NULL, 1, NULL, 'Admin User', NULL, 'Thhis is a test admin user'),
('2', '4cb9c8a8048fd02294477fcb1a41191a', NULL, 1, NULL, 'Program Manager', NULL, 'Program Deputy!');

-- --------------------------------------------------------

--
-- Table structure for table `participantsessioninterest`
--

CREATE TABLE IF NOT EXISTS `participantsessioninterest` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `sessionid` int(11) NOT NULL DEFAULT '0',
  `rank` int(11) DEFAULT NULL,
  `willmoderate` tinyint(1) DEFAULT NULL,
  `comments` text,
  PRIMARY KEY (`badgeid`,`sessionid`),
  KEY `sessionid` (`sessionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `participantsessioninterest`
--

INSERT INTO `participantsessioninterest` (`badgeid`, `sessionid`, `rank`, `willmoderate`, `comments`) VALUES
('2', 1, 2, 1, ''),
('2', 3, 5, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `participantsuggestions`
--

CREATE TABLE IF NOT EXISTS `participantsuggestions` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `paneltopics` text,
  `otherideas` text,
  `suggestedguests` text,
  PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `patchlog`
--

CREATE TABLE IF NOT EXISTS `patchlog` (
  `patchname` varchar(40) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `patchlog`
--

INSERT INTO `patchlog` (`patchname`, `timestamp`) VALUES
('2012-06-16 start balticon developmen', '2012-06-16 12:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `permissionatoms`
--

CREATE TABLE IF NOT EXISTS `permissionatoms` (
  `permatomid` int(11) NOT NULL AUTO_INCREMENT,
  `permatomtag` varchar(20) NOT NULL DEFAULT '',
  `elementid` int(11) DEFAULT NULL,
  `page` varchar(30) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`permatomid`),
  UNIQUE KEY `taginx` (`permatomtag`,`elementid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `permissionatoms`
--

INSERT INTO `permissionatoms` (`permatomid`, `permatomtag`, `elementid`, `page`, `notes`) VALUES
(1, 'Staff', NULL, 'renderWelcome', 'Enables staff menu link'),
(2, 'Administrator', NULL, 'many', 'Use to be determined'),
(3, 'Participant', NULL, 'many', 'Use to be determined'),
(4, 'EditBio', NULL, 'renderMyContact', 'Allow write to biography on my contact page'),
(5, 'my_availability', NULL, 'ParticipantHeader', 'Enables menu option throughout participant section and enables page.'),
(6, 'search_panels', NULL, 'ParticipantHeader', 'Enables menu option throughout participant section and enables page.'),
(7, 'my_panel_interests', NULL, 'ParticipantHeader', 'Enables menu option throughout participant section and enables page.'),
(8, 'my_schedule', NULL, 'ParticipantHeader', 'Enables menu option throughout participant section and enables page.'),
(9, 'my_suggestions_write', NULL, 'MySuggestions', 'Enables write access to the form elements on the page MySuggestions.'),
(10, 'my_gen_int_write', NULL, 'MyGeneralInterests', 'Enables write access to the form elements on the page My General Interests'),
(11, 'BrainstormSubmit', NULL, 'EditCreateSession', 'Brainstorm user can create session'),
(12, 'BS_sear_sess', NULL, 'SearchSessions', 'Brainstorm user can view sessions'),
(13, 'public_login', NULL, 'Login', 'Brainstorm user can log in'),
(14, 'SendEmail', NULL, 'StaffManageParticipants', 'Access to Send email set of pages'),
(15, 'postcon', NULL, 'renderWelcome', 'Forces participant welcome page to display only post con message.'),
(16, 'EditReg', NULL, 'renderMyContact', 'Allow write to registration information on my contact page'),
(17, 'create_participant', NULL, 'AdminParticipants', 'Allows someone to create new participants');

-- --------------------------------------------------------

--
-- Table structure for table `permissionroles`
--

CREATE TABLE IF NOT EXISTS `permissionroles` (
  `permroleid` int(11) NOT NULL AUTO_INCREMENT,
  `permrolename` varchar(100) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`permroleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=8 ;

--
-- Dumping data for table `permissionroles`
--

INSERT INTO `permissionroles` (`permroleid`, `permrolename`, `notes`) VALUES
(1, 'Administrator', 'Use to be determined'),
(2, 'Staff', 'Can access staff pages'),
(3, 'Program Participant', 'Use to be determined'),
(4, 'Brainstorm', 'Use for Brainstorm pages'),
(5, 'Event Participant', 'Can''t even log in.  Just for bulk email.'),
(6, 'Event Organizer', 'Can''t even log in.  Just for bulk email.'),
(7, 'Liaison', 'Can only login to get reports.');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `permissionid` int(11) NOT NULL AUTO_INCREMENT,
  `permatomid` int(11) NOT NULL DEFAULT '0',
  `phaseid` int(11) DEFAULT '0' COMMENT 'null indicates all phases',
  `permroleid` int(11) DEFAULT '0' COMMENT 'null indicates not applicable',
  `badgeid` int(11) DEFAULT '0' COMMENT 'null indicates not applicable',
  PRIMARY KEY (`permissionid`),
  UNIQUE KEY `unique1` (`permatomid`,`phaseid`,`permroleid`,`badgeid`),
  KEY `FK_Permissions` (`phaseid`),
  KEY `FK_PRoles` (`permroleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB' AUTO_INCREMENT=51 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permissionid`, `permatomid`, `phaseid`, `permroleid`, `badgeid`) VALUES
(1, 1, NULL, 1, NULL),
(4, 1, NULL, 2, NULL),
(2, 2, NULL, 1, NULL),
(13, 2, NULL, 7, NULL),
(3, 3, NULL, 1, NULL),
(5, 3, NULL, 2, NULL),
(6, 3, NULL, 3, NULL),
(9, 4, 1, 2, NULL),
(11, 4, 1, 3, NULL),
(10, 4, 2, 2, NULL),
(12, 4, 2, 3, NULL),
(14, 5, 1, 3, NULL),
(15, 5, 2, 3, NULL),
(16, 5, 3, 3, NULL),
(17, 6, 1, 3, NULL),
(18, 6, 2, 3, NULL),
(19, 6, 3, 3, NULL),
(20, 7, 1, 3, NULL),
(21, 7, 2, 3, NULL),
(22, 7, 3, 3, NULL),
(23, 8, 2, 3, NULL),
(24, 8, 3, 3, NULL),
(25, 8, 4, 3, NULL),
(26, 8, 5, 3, NULL),
(27, 9, NULL, 2, NULL),
(28, 9, NULL, 3, NULL),
(43, 9, 1, 2, NULL),
(41, 9, 1, 3, NULL),
(42, 9, 1, 4, NULL),
(29, 10, 1, 3, NULL),
(30, 10, 2, 3, NULL),
(31, 11, NULL, 2, NULL),
(32, 11, NULL, 3, NULL),
(33, 11, NULL, 4, NULL),
(50, 11, 1, 4, NULL),
(34, 13, NULL, 4, NULL),
(49, 13, 1, 4, NULL),
(35, 14, NULL, 2, NULL),
(40, 15, 5, 3, 0),
(36, 16, 1, 2, NULL),
(38, 16, 1, 3, NULL),
(37, 16, 2, 2, NULL),
(39, 16, 2, 3, NULL),
(46, 17, NULL, 1, NULL),
(47, 17, 1, 2, NULL),
(48, 17, 2, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phases`
--

CREATE TABLE IF NOT EXISTS `phases` (
  `phaseid` int(11) NOT NULL AUTO_INCREMENT,
  `phasename` varchar(100) DEFAULT NULL,
  `current` tinyint(1) DEFAULT '0',
  `notes` text,
  PRIMARY KEY (`phaseid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `phases`
--

INSERT INTO `phases` (`phaseid`, `phasename`, `current`, `notes`) VALUES
(1, 'Participant Registration', 1, 'Create Participants and panels, allow participants to sign up for panels'),
(2, 'Schedule Building', 0, 'Begin open scheduling of all panels and events'),
(3, 'Final Scheduling', 0, 'Registration fixed, start public reporting, brainstorming closed'),
(4, 'Convention Operations', 0, 'Schedule viewable, not changable'),
(5, 'Post Convention', 0, 'wrap up functions');

-- --------------------------------------------------------

--
-- Table structure for table `previouscons`
--

CREATE TABLE IF NOT EXISTS `previouscons` (
  `previousconid` int(11) NOT NULL AUTO_INCREMENT,
  `previousconname` varchar(128) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`previousconid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `previouscontracks`
--

CREATE TABLE IF NOT EXISTS `previouscontracks` (
  `previousconid` int(11) NOT NULL,
  `previoustrackid` int(11) NOT NULL,
  `trackname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`previousconid`,`previoustrackid`),
  KEY `previousconid` (`previousconid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `previousparticipants`
--

CREATE TABLE IF NOT EXISTS `previousparticipants` (
  `badgeid` varchar(15) NOT NULL,
  `bio` text,
  PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `previoussessions`
--

CREATE TABLE IF NOT EXISTS `previoussessions` (
  `previousconid` int(11) NOT NULL,
  `previoussessionid` int(11) NOT NULL,
  `previoustrackid` int(11) NOT NULL,
  `previousstatusid` int(11) NOT NULL,
  `typeid` int(11) NOT NULL,
  `divisionid` int(11) NOT NULL,
  `languagestatusid` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `secondtitle` varchar(100) DEFAULT NULL,
  `pocketprogtext` text,
  `progguiddesc` text,
  `persppartinfo` text,
  `duration` time DEFAULT NULL,
  `estatten` int(11) DEFAULT NULL,
  `kidscatid` int(11) NOT NULL,
  `signupreq` tinyint(1) DEFAULT NULL,
  `notesforpart` text,
  `notesforprog` text,
  `invitedguest` tinyint(1) DEFAULT NULL,
  `importedsessionid` int(11) DEFAULT NULL,
  PRIMARY KEY (`previousconid`,`previoussessionid`),
  KEY `previousconid` (`previousconid`),
  KEY `previoustrackid` (`previousconid`,`previoustrackid`),
  KEY `previousstatusid` (`previousstatusid`),
  KEY `typeid` (`typeid`),
  KEY `divisionid` (`divisionid`),
  KEY `languagestatusid` (`languagestatusid`),
  KEY `kidscatid` (`kidscatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pubcharacteristics`
--

CREATE TABLE IF NOT EXISTS `pubcharacteristics` (
  `pubcharid` int(11) NOT NULL AUTO_INCREMENT,
  `pubcharname` varchar(30) DEFAULT NULL,
  `pubchartag` varchar(10) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pubcharid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pubstatuses`
--

CREATE TABLE IF NOT EXISTS `pubstatuses` (
  `pubstatusid` int(11) NOT NULL AUTO_INCREMENT,
  `pubstatusname` varchar(12) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pubstatusid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pubstatuses`
--

INSERT INTO `pubstatuses` (`pubstatusid`, `pubstatusname`, `display_order`) VALUES
(2, 'Public', 2),
(3, 'Do not print', 3);

-- --------------------------------------------------------

--
-- Table structure for table `regtypes`
--

CREATE TABLE IF NOT EXISTS `regtypes` (
  `regtype` varchar(40) NOT NULL DEFAULT '',
  `message` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`regtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `regtypes`
--

INSERT INTO `regtypes` (`regtype`, `message`) VALUES
('BSFS', 'Registered'),
('Comp', 'Registered and Comp''ed'),
('ConfirmedParticipant', 'Registered and Comp''ed'),
('Dealer', 'Registered'),
('Guest of Honor', 'Registered and Comp''ed'),
('None', 'NOT registered'),
('Paid', 'Registered'),
('Participant', 'Comp Requested'),
('Staff', 'Registered and Comp''ed'),
('Volunteer', 'Registered');

-- --------------------------------------------------------

--
-- Table structure for table `reportcategories`
--

CREATE TABLE IF NOT EXISTS `reportcategories` (
  `reportcategoryid` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`reportcategoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reportqueries`
--

CREATE TABLE IF NOT EXISTS `reportqueries` (
  `reportqueryid` int(11) NOT NULL AUTO_INCREMENT,
  `reporttypeid` int(11) NOT NULL,
  `queryname` varchar(25) NOT NULL,
  `query` text,
  PRIMARY KEY (`reportqueryid`),
  KEY `FK_ReportQueries` (`reporttypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reporttypes`
--

CREATE TABLE IF NOT EXISTS `reporttypes` (
  `reporttypeid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text,
  `technology` varchar(200) DEFAULT NULL COMMENT 'notes for tracking migration only',
  `oldmechanism` tinyint(4) NOT NULL DEFAULT '0',
  `ondemand` tinyint(4) DEFAULT NULL COMMENT '=1 for all new tech and some old',
  `filename` varchar(35) DEFAULT NULL COMMENT 'populate only if oldmechanism = 1',
  `xsl` text COMMENT 'populate only if oldmechanism = 0',
  `download` tinyint(4) DEFAULT NULL COMMENT 'disregard if oldmechanism = 1',
  `downloadfilename` varchar(25) DEFAULT NULL COMMENT 'populate only if download = 1',
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`reporttypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `roleid` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`roleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roleid`, `rolename`, `display_order`) VALUES
(1, 'Other', -1),
(2, 'Reading my own works', 3),
(3, 'Autographing', 4),
(5, 'Performing', 6),
(6, 'Running workshops', 7),
(7, 'Leading discussions', 8),
(8, 'Running games', 9),
(9, 'Working with children', 10),
(10, 'Panel Moderator', 2),
(11, 'Panel Participant', 1),
(13, 'Join Balticon Volunteer Staff', 13);

-- --------------------------------------------------------

--
-- Table structure for table `roomhasset`
--

CREATE TABLE IF NOT EXISTS `roomhasset` (
  `roomid` int(11) NOT NULL DEFAULT '0',
  `roomsetid` int(11) NOT NULL DEFAULT '0',
  `capacity` int(11) DEFAULT NULL,
  PRIMARY KEY (`roomid`,`roomsetid`),
  KEY `roomsetid` (`roomsetid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roomhasset`
--

INSERT INTO `roomhasset` (`roomid`, `roomsetid`, `capacity`) VALUES
(1, 1, NULL),
(1, 4, NULL),
(1, 5, NULL),
(2, 2, NULL),
(2, 4, NULL),
(2, 5, NULL),
(3, 2, NULL),
(3, 3, NULL),
(4, 2, NULL),
(5, 2, NULL),
(6, 2, NULL),
(7, 2, NULL),
(8, 2, NULL),
(9, 2, NULL),
(9, 4, NULL),
(10, 2, NULL),
(10, 7, NULL),
(11, 2, NULL),
(11, 7, NULL),
(12, 2, NULL),
(12, 5, NULL),
(13, 2, NULL),
(13, 5, NULL),
(14, 5, NULL),
(15, 5, NULL),
(16, 5, NULL),
(17, 5, NULL),
(18, 1, NULL),
(19, 1, NULL),
(20, 5, NULL),
(21, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE IF NOT EXISTS `rooms` (
  `roomid` int(11) NOT NULL AUTO_INCREMENT,
  `roomname` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `height` varchar(100) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `function` varchar(100) DEFAULT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `notes` text,
  `opentime1` time DEFAULT NULL,
  `closetime1` time DEFAULT NULL,
  `opentime2` time DEFAULT NULL,
  `closetime2` time DEFAULT NULL,
  `opentime3` time DEFAULT NULL,
  `closetime3` time DEFAULT NULL,
  `is_scheduled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roomid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`roomid`, `roomname`, `display_order`, `height`, `dimensions`, `area`, `function`, `floor`, `notes`, `opentime1`, `closetime1`, `opentime2`, `closetime2`, `opentime3`, `closetime3`, `is_scheduled`) VALUES
(1, 'Valley Ballroom', 1, NULL, NULL, NULL, 'Main Tent', 'Lower Level', NULL, '19:00:00', '03:00:00', '07:00:00', '02:00:00', '07:00:00', '02:00:00', 1),
(2, 'Garden Room', 2, NULL, NULL, NULL, 'Small Tent', 'Lobby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(3, 'Belmont', 3, NULL, NULL, NULL, 'Program', 'Lobby', 'has TV', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(4, 'Chase', 4, NULL, NULL, NULL, 'Childrens (daytime)', 'Lobby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(5, 'Derby', 5, NULL, NULL, NULL, 'New Media', 'Lobby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(6, 'Chesapeake', 6, NULL, NULL, NULL, 'New Media', 'Lobby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(7, 'Pimlico', 7, NULL, NULL, NULL, 'Reading', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(8, 'Salon A', 8, NULL, NULL, NULL, 'Science', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(9, 'Salon B', 9, NULL, NULL, NULL, 'Program', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(10, 'Salon C', 10, NULL, NULL, NULL, 'Program', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(11, 'Salon D', 11, NULL, NULL, NULL, 'Program', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(12, 'Parlor 1041', 12, NULL, NULL, NULL, 'Program', 'Lobby', 'Among Guest Rooms', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(13, 'Parlor 3041', 13, NULL, NULL, NULL, 'Program', '3rd Floor', 'Among Guest Rooms', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(14, 'Maryland Foyer', 14, NULL, NULL, NULL, 'Autograph Sessions', 'Lobby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(15, 'Con-Suite', 15, NULL, NULL, NULL, 'Con Suite Concerts and Parties', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(16, 'Valley Foyer', 16, NULL, NULL, NULL, 'Prefunction', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(17, 'McCormick Suite', 17, NULL, NULL, NULL, 'Participant Green Room', '2nd Floor', 'Among Guest Rooms', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(18, 'Salon E', 18, NULL, NULL, NULL, 'Anime', 'Lower Level', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19, 'Salon F', 19, NULL, NULL, NULL, 'Video', 'Lower Level', 'Masquerade Overflow Saturday Night', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(20, 'Tack', 20, NULL, NULL, NULL, 'Gaming', 'Lobby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(21, 'Hotel Bar', 21, NULL, NULL, NULL, 'informal', 'Lobby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roomsets`
--

CREATE TABLE IF NOT EXISTS `roomsets` (
  `roomsetid` int(11) NOT NULL AUTO_INCREMENT,
  `roomsetname` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`roomsetid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `roomsets`
--

INSERT INTO `roomsets` (`roomsetid`, `roomsetname`, `display_order`) VALUES
(1, 'Theater', 2),
(2, 'Panel', 1),
(3, 'Workshop', 3),
(4, 'No Chairs', 4),
(5, 'Other (describe)', 98),
(6, 'I do not know', 99),
(7, 'Merged', 5);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `scheduleid` int(11) NOT NULL AUTO_INCREMENT,
  `sessionid` int(11) NOT NULL DEFAULT '0',
  `roomid` int(11) NOT NULL DEFAULT '0',
  `starttime` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`scheduleid`),
  KEY `sessionid` (`sessionid`),
  KEY `roomid` (`roomid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `serviceid` int(11) NOT NULL AUTO_INCREMENT,
  `servicename` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`serviceid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`serviceid`, `servicename`, `display_order`) VALUES
(1, 'Projector & Screen', 10),
(2, 'DVD Player', 20),
(3, 'Elmo', 30),
(4, 'Sound', 40);

-- --------------------------------------------------------

--
-- Table structure for table `sessioneditcodes`
--

CREATE TABLE IF NOT EXISTS `sessioneditcodes` (
  `sessioneditcode` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`sessioneditcode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7 ;

--
-- Dumping data for table `sessioneditcodes`
--

INSERT INTO `sessioneditcodes` (`sessioneditcode`, `description`, `display_order`) VALUES
(1, 'Created in brainstorm', 1),
(2, 'Created in staff create session', 2),
(3, 'Unknown edit', 4),
(4, 'Add to schedule', 5),
(5, 'Remove from schedule', 6),
(6, 'Created by import', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sessionedithistory`
--

CREATE TABLE IF NOT EXISTS `sessionedithistory` (
  `sessionid` int(11) NOT NULL DEFAULT '0',
  `badgeid` varchar(15) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `email_address` varchar(75) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sessioneditcode` int(11) NOT NULL DEFAULT '0',
  `statusid` int(11) NOT NULL DEFAULT '0',
  `editdescription` text,
  PRIMARY KEY (`sessionid`,`timestamp`),
  KEY `FK_SessionEditHistory` (`badgeid`),
  KEY `FK_SessionEditCodes` (`sessioneditcode`),
  KEY `FK_SessionEditHistory4` (`statusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 8192 kB';

--
-- Dumping data for table `sessionedithistory`
--

INSERT INTO `sessionedithistory` (`sessionid`, `badgeid`, `name`, `email_address`, `timestamp`, `sessioneditcode`, `statusid`, `editdescription`) VALUES
(1, '2', 'Program Manager', 'program@balticon.org', '2012-06-24 20:55:33', 1, 2, NULL),
(1, '2', 'Program Manager', 'program@balticon.org', '2012-06-24 21:36:00', 3, 2, NULL),
(1, '2', 'Program Manager', 'program@balticon.org', '2012-06-24 21:37:01', 3, 2, NULL),
(2, '2', 'Program Manager', 'program@balticon.org', '2012-06-24 21:34:23', 1, 2, NULL),
(2, '2', 'Program Manager', 'program@balticon.org', '2012-06-24 21:37:13', 3, 2, NULL),
(3, '2', 'Program Manager', 'program@balticon.org', '2012-06-24 21:34:55', 1, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessionhasfeature`
--

CREATE TABLE IF NOT EXISTS `sessionhasfeature` (
  `sessionid` int(11) NOT NULL DEFAULT '0',
  `featureid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionid`,`featureid`),
  KEY `featureid` (`featureid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessionhasfeature`
--

INSERT INTO `sessionhasfeature` (`sessionid`, `featureid`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessionhaspubchar`
--

CREATE TABLE IF NOT EXISTS `sessionhaspubchar` (
  `sessionid` int(11) NOT NULL DEFAULT '0',
  `pubcharid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionid`,`pubcharid`),
  KEY `Fkey2` (`pubcharid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessionhasservice`
--

CREATE TABLE IF NOT EXISTS `sessionhasservice` (
  `sessionid` int(11) NOT NULL DEFAULT '0',
  `serviceid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionid`,`serviceid`),
  KEY `serviceid` (`serviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessionhasservice`
--

INSERT INTO `sessionhasservice` (`sessionid`, `serviceid`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `sessionid` int(11) NOT NULL AUTO_INCREMENT,
  `trackid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) NOT NULL DEFAULT '0',
  `divisionid` int(11) NOT NULL DEFAULT '0',
  `pubstatusid` int(11) DEFAULT '0',
  `languagestatusid` int(11) DEFAULT '1',
  `pubsno` varchar(50) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `secondtitle` varchar(100) DEFAULT NULL,
  `pocketprogtext` text,
  `progguiddesc` text,
  `persppartinfo` text,
  `duration` time DEFAULT NULL,
  `estatten` int(11) DEFAULT NULL,
  `kidscatid` int(11) NOT NULL DEFAULT '0',
  `signupreq` tinyint(1) DEFAULT NULL,
  `roomsetid` int(11) NOT NULL DEFAULT '0',
  `notesforpart` text,
  `servicenotes` text,
  `statusid` int(11) NOT NULL DEFAULT '0',
  `notesforprog` text,
  `warnings` tinyint(1) DEFAULT NULL,
  `invitedguest` tinyint(1) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sessionid`),
  KEY `trackid` (`trackid`),
  KEY `typeid` (`typeid`),
  KEY `kidscatid` (`kidscatid`),
  KEY `roomsetid` (`roomsetid`),
  KEY `statusid` (`statusid`),
  KEY `Sessions_ibfk_7` (`divisionid`),
  KEY `Sessions_ibfk_6` (`pubstatusid`),
  KEY `languagestatusid` (`languagestatusid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`sessionid`, `trackid`, `typeid`, `divisionid`, `pubstatusid`, `languagestatusid`, `pubsno`, `title`, `secondtitle`, `pocketprogtext`, `progguiddesc`, `persppartinfo`, `duration`, `estatten`, `kidscatid`, `signupreq`, `roomsetid`, `notesforpart`, `servicenotes`, `statusid`, `notesforprog`, `warnings`, `invitedguest`, `ts`) VALUES
(1, 1, 1, 1, 2, 1, '', 'anime flick', '', '', 'this is a flick', '', '01:15:00', NULL, 2, 0, 2, '', '', 2, 'test 1', 0, 1, '2012-06-24 21:37:01'),
(2, 6, 1, 1, 2, 1, '', 'dead dog party', 'a party', '', 'This is the dead dog party!', '', '01:15:00', NULL, 2, 0, 4, '', '', 2, '', 0, 1, '2012-06-24 21:37:13'),
(3, 2, 1, 1, 2, 1, '', 'Real Art Panel', '', '', 'They''re just farting around', '', '01:15:00', NULL, 2, 0, 2, '', '', 2, '', 0, 0, '2012-06-24 21:34:55');

-- --------------------------------------------------------

--
-- Table structure for table `sessionstatuses`
--

CREATE TABLE IF NOT EXISTS `sessionstatuses` (
  `statusid` int(11) NOT NULL AUTO_INCREMENT,
  `statusname` varchar(50) DEFAULT NULL,
  `validate` tinyint(4) NOT NULL DEFAULT '0',
  `may_be_scheduled` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`statusid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `sessionstatuses`
--

INSERT INTO `sessionstatuses` (`statusid`, `statusname`, `validate`, `may_be_scheduled`, `display_order`) VALUES
(1, 'Brainstorm', 0, 0, 4),
(2, 'Vetted', 1, 1, 6),
(3, 'Scheduled', 1, 1, 10),
(4, 'Dropped', 0, 0, 18),
(5, 'Cancelled', 0, 0, 20),
(6, 'Edit Me', 0, 0, 1),
(7, 'Assigned', 1, 1, 13),
(10, 'duplicate', 0, 0, 90);

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

CREATE TABLE IF NOT EXISTS `times` (
  `timeid` int(11) NOT NULL,
  `timedisplay` char(14) DEFAULT NULL,
  `timevalue` time DEFAULT NULL,
  `next_day` tinyint(4) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `avail_start` tinyint(4) DEFAULT NULL,
  `avail_end` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`timeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `times`
--

INSERT INTO `times` (`timeid`, `timedisplay`, `timevalue`, `next_day`, `display_order`, `avail_start`, `avail_end`) VALUES
(1, '8:30a', '08:30:00', 0, 1, 1, 0),
(2, '10:00a', '10:00:00', 0, 2, 1, 1),
(3, '11:30a', '11:30:00', 0, 3, 1, 1),
(4, '1:00p', '13:00:00', 0, 4, 1, 1),
(5, '2:30p', '14:30:00', 0, 5, 1, 1),
(6, '4:00p', '16:00:00', 0, 6, 1, 1),
(7, '5:30p', '17:30:00', 0, 7, 1, 1),
(8, '7:00p', '19:00:00', 0, 8, 1, 1),
(9, '8:30p', '20:30:00', 0, 9, 1, 1),
(10, '10:00p', '22:00:00', 0, 10, 1, 1),
(11, '11:30p', '23:30:00', 0, 11, 1, 1),
(12, '1:00a (+1d)', '01:00:00', 1, 12, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `trackcompatibility`
--

CREATE TABLE IF NOT EXISTS `trackcompatibility` (
  `previousconid` int(11) NOT NULL,
  `previoustrackid` int(11) NOT NULL,
  `currenttrackid` int(11) NOT NULL,
  PRIMARY KEY (`previousconid`,`previoustrackid`),
  KEY `currenttrackid` (`currenttrackid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tracks`
--

CREATE TABLE IF NOT EXISTS `tracks` (
  `trackid` int(11) NOT NULL AUTO_INCREMENT,
  `trackname` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `selfselect` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`trackid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Dumping data for table `tracks`
--

INSERT INTO `tracks` (`trackid`, `trackname`, `display_order`, `selfselect`) VALUES
(1, 'Anime', 10, 1),
(2, 'Art', 20, 1),
(3, 'Autographings', 30, 1),
(4, 'Childrens', 40, 1),
(5, 'Costuming', 50, 1),
(6, 'Dances & Parties', 60, 1),
(7, 'Fan', 70, 1),
(8, 'Film, TV, & Film Making', 80, 1),
(9, 'Gaming', 90, 0),
(10, 'Kaffeeklatsches', 100, 1),
(11, 'LARP', 110, 1),
(12, 'Masquerade', 120, 1),
(13, 'Music', 130, 1),
(14, 'New Media', 140, 1),
(15, 'Teen', 150, 1),
(16, 'Publishers', 160, 0),
(17, 'Readers', 170, 0),
(18, 'Writers', 180, 0),
(19, 'Science', 190, 0),
(20, 'Skeptical Thinking', 200, 0),
(21, 'Teen', 210, 0),
(22, 'Video', 220, 0),
(23, 'Craft', 55, 1),
(24, 'Workshops', 185, 1),
(99, 'I do not know', 99, 0);

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `typeid` int(11) NOT NULL AUTO_INCREMENT,
  `typename` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `selfselect` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`typeid`, `typename`, `display_order`, `selfselect`) VALUES
(1, 'Panel', 1, 1),
(2, 'Workshop', 2, 0),
(3, 'Room Turn', 500, 0),
(4, 'Lecture', 6, 0),
(5, 'Meeting', 20, 0),
(6, 'Interview', 9, 0),
(7, 'Signing', 13, 0),
(8, 'Reading', 7, 0),
(9, 'Rehearsal', 100, 0),
(10, 'TV - Live Event', 10, 0),
(11, 'TV - Previous Event', 11, 0),
(12, 'Projected Media', 8, 0),
(14, 'Open Gaming', 18, 0),
(15, 'Scheduled Game', 19, 0),
(16, 'TV - Movie/Show', 12, 0),
(17, 'I do not know', 700, 0),
(18, 'Concert', 4, 0),
(19, 'Participatory Event', 3, 0),
(20, 'Drama', 5, 0),
(21, 'Setup', 200, 0),
(22, 'Refresh', 499, 0);

-- --------------------------------------------------------

--
-- Table structure for table `userhaspermissionrole`
--

CREATE TABLE IF NOT EXISTS `userhaspermissionrole` (
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `permroleid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`badgeid`,`permroleid`),
  KEY `FK_UserHasPermissionRole` (`permroleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `userhaspermissionrole`
--

INSERT INTO `userhaspermissionrole` (`badgeid`, `permroleid`) VALUES
('1', 1),
('1', 2),
('2', 2),
('2', 3);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categoryhasreport`
--
ALTER TABLE `categoryhasreport`
  ADD CONSTRAINT `FK_CategoryHasReport` FOREIGN KEY (`reportcategoryid`) REFERENCES `reportcategories` (`reportcategoryid`),
  ADD CONSTRAINT `FK_CategoryHasReport2` FOREIGN KEY (`reporttypeid`) REFERENCES `reporttypes` (`reporttypeid`);

--
-- Constraints for table `emailhistory`
--
ALTER TABLE `emailhistory`
  ADD CONSTRAINT `FK_EmailHistory` FOREIGN KEY (`emailid`) REFERENCES `emailtracking` (`emailid`),
  ADD CONSTRAINT `FK_EmailHistory2` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`);

--
-- Constraints for table `participantavailability`
--
ALTER TABLE `participantavailability`
  ADD CONSTRAINT `ParticipantAvailability_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`);

--
-- Constraints for table `participantavailabilitydays`
--
ALTER TABLE `participantavailabilitydays`
  ADD CONSTRAINT `ParticipantAvailabilityDays_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`);

--
-- Constraints for table `participantavailabilitytimes`
--
ALTER TABLE `participantavailabilitytimes`
  ADD CONSTRAINT `ParticipantAvailabilityTimes_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`);

--
-- Constraints for table `participanthascredential`
--
ALTER TABLE `participanthascredential`
  ADD CONSTRAINT `phcfk1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`),
  ADD CONSTRAINT `phcfk2` FOREIGN KEY (`credentialid`) REFERENCES `credentials` (`credentialid`);

--
-- Constraints for table `participanthasrole`
--
ALTER TABLE `participanthasrole`
  ADD CONSTRAINT `ParticipantHasRole_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`),
  ADD CONSTRAINT `ParticipantHasRole_ibfk_2` FOREIGN KEY (`roleid`) REFERENCES `roles` (`roleid`);

--
-- Constraints for table `participantinterests`
--
ALTER TABLE `participantinterests`
  ADD CONSTRAINT `ParticipantInterests_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`);

--
-- Constraints for table `participantonsession`
--
ALTER TABLE `participantonsession`
  ADD CONSTRAINT `ParticipantOnSession_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`),
  ADD CONSTRAINT `ParticipantOnSession_ibfk_2` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`);

--
-- Constraints for table `participantsessioninterest`
--
ALTER TABLE `participantsessioninterest`
  ADD CONSTRAINT `ParticipantSessionInterest_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`),
  ADD CONSTRAINT `ParticipantSessionInterest_ibfk_2` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`),
  ADD CONSTRAINT `ParticipantSessionInterest_ibfk_3` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`),
  ADD CONSTRAINT `ParticipantSessionInterest_ibfk_4` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`);

--
-- Constraints for table `participantsuggestions`
--
ALTER TABLE `participantsuggestions`
  ADD CONSTRAINT `ParticipantSuggestions_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `Permissions_ibfk_1` FOREIGN KEY (`permatomid`) REFERENCES `permissionatoms` (`permatomid`),
  ADD CONSTRAINT `Permissions_ibfk_2` FOREIGN KEY (`phaseid`) REFERENCES `phases` (`phaseid`),
  ADD CONSTRAINT `Permissions_ibfk_3` FOREIGN KEY (`permroleid`) REFERENCES `permissionroles` (`permroleid`);

--
-- Constraints for table `previouscontracks`
--
ALTER TABLE `previouscontracks`
  ADD CONSTRAINT `PreviousCons_ibfk_1` FOREIGN KEY (`previousconid`) REFERENCES `previouscons` (`previousconid`);

--
-- Constraints for table `previoussessions`
--
ALTER TABLE `previoussessions`
  ADD CONSTRAINT `PreviousSessions_ibfk_1` FOREIGN KEY (`previousconid`) REFERENCES `previouscons` (`previousconid`),
  ADD CONSTRAINT `PreviousSessions_ibfk_2` FOREIGN KEY (`previousconid`, `previoustrackid`) REFERENCES `previouscontracks` (`previousconid`, `previoustrackid`),
  ADD CONSTRAINT `PreviousSessions_ibfk_3` FOREIGN KEY (`previousstatusid`) REFERENCES `sessionstatuses` (`statusid`),
  ADD CONSTRAINT `PreviousSessions_ibfk_4` FOREIGN KEY (`typeid`) REFERENCES `types` (`typeid`),
  ADD CONSTRAINT `PreviousSessions_ibfk_5` FOREIGN KEY (`divisionid`) REFERENCES `divisions` (`divisionid`),
  ADD CONSTRAINT `PreviousSessions_ibfk_6` FOREIGN KEY (`languagestatusid`) REFERENCES `languagestatuses` (`languagestatusid`),
  ADD CONSTRAINT `PreviousSessions_ibfk_7` FOREIGN KEY (`kidscatid`) REFERENCES `kidscategories` (`kidscatid`);

--
-- Constraints for table `reportqueries`
--
ALTER TABLE `reportqueries`
  ADD CONSTRAINT `FK_ReportQueries` FOREIGN KEY (`reporttypeid`) REFERENCES `reporttypes` (`reporttypeid`);

--
-- Constraints for table `roomhasset`
--
ALTER TABLE `roomhasset`
  ADD CONSTRAINT `RoomHasSet_ibfk_1` FOREIGN KEY (`roomid`) REFERENCES `rooms` (`roomid`),
  ADD CONSTRAINT `RoomHasSet_ibfk_2` FOREIGN KEY (`roomsetid`) REFERENCES `roomsets` (`roomsetid`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `Schedule_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`),
  ADD CONSTRAINT `Schedule_ibfk_2` FOREIGN KEY (`roomid`) REFERENCES `rooms` (`roomid`);

--
-- Constraints for table `sessionedithistory`
--
ALTER TABLE `sessionedithistory`
  ADD CONSTRAINT `SessionEditHistory_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`),
  ADD CONSTRAINT `SessionEditHistory_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`),
  ADD CONSTRAINT `SessionEditHistory_ibfk_3` FOREIGN KEY (`sessioneditcode`) REFERENCES `sessioneditcodes` (`sessioneditcode`),
  ADD CONSTRAINT `SessionEditHistory_ibfk_4` FOREIGN KEY (`statusid`) REFERENCES `sessionstatuses` (`statusid`);

--
-- Constraints for table `sessionhasfeature`
--
ALTER TABLE `sessionhasfeature`
  ADD CONSTRAINT `SessionHasFeature_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`),
  ADD CONSTRAINT `SessionHasFeature_ibfk_2` FOREIGN KEY (`featureid`) REFERENCES `features` (`featureid`);

--
-- Constraints for table `sessionhaspubchar`
--
ALTER TABLE `sessionhaspubchar`
  ADD CONSTRAINT `Fkey1` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`),
  ADD CONSTRAINT `Fkey2` FOREIGN KEY (`pubcharid`) REFERENCES `pubcharacteristics` (`pubcharid`);

--
-- Constraints for table `sessionhasservice`
--
ALTER TABLE `sessionhasservice`
  ADD CONSTRAINT `SessionHasService_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `sessions` (`sessionid`),
  ADD CONSTRAINT `SessionHasService_ibfk_2` FOREIGN KEY (`serviceid`) REFERENCES `services` (`serviceid`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `Sessions_ibfk_1` FOREIGN KEY (`trackid`) REFERENCES `tracks` (`trackid`),
  ADD CONSTRAINT `Sessions_ibfk_2` FOREIGN KEY (`typeid`) REFERENCES `types` (`typeid`),
  ADD CONSTRAINT `Sessions_ibfk_3` FOREIGN KEY (`kidscatid`) REFERENCES `kidscategories` (`kidscatid`),
  ADD CONSTRAINT `Sessions_ibfk_4` FOREIGN KEY (`roomsetid`) REFERENCES `roomsets` (`roomsetid`),
  ADD CONSTRAINT `Sessions_ibfk_5` FOREIGN KEY (`statusid`) REFERENCES `sessionstatuses` (`statusid`),
  ADD CONSTRAINT `Sessions_ibfk_6` FOREIGN KEY (`pubstatusid`) REFERENCES `pubstatuses` (`pubstatusid`),
  ADD CONSTRAINT `Sessions_ibfk_7` FOREIGN KEY (`divisionid`) REFERENCES `divisions` (`divisionid`),
  ADD CONSTRAINT `Sessions_ibfk_8` FOREIGN KEY (`languagestatusid`) REFERENCES `languagestatuses` (`languagestatusid`);

--
-- Constraints for table `trackcompatibility`
--
ALTER TABLE `trackcompatibility`
  ADD CONSTRAINT `TrackCompatibility_ibfk_1` FOREIGN KEY (`previousconid`, `previoustrackid`) REFERENCES `previouscontracks` (`previousconid`, `previoustrackid`),
  ADD CONSTRAINT `TrackCompatibility_ibfk_2` FOREIGN KEY (`currenttrackid`) REFERENCES `tracks` (`trackid`);

--
-- Constraints for table `userhaspermissionrole`
--
ALTER TABLE `userhaspermissionrole`
  ADD CONSTRAINT `UserHasPermissionRole_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `participants` (`badgeid`),
  ADD CONSTRAINT `UserHasPermissionRole_ibfk_2` FOREIGN KEY (`permroleid`) REFERENCES `permissionroles` (`permroleid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
