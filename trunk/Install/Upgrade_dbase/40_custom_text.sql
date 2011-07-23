## This script adds a new table to support the custom text mechanism
CREATE TABLE `CustomText` (
	`customtextid` int(11) NOT NULL auto_increment,
	`page` varchar(100),
	`tag` varchar(25),
	`textcontents` text,
	PRIMARY KEY  (`customtextid`),
	UNIQUE INDEX(page, tag)
) ENGINE=InnoDB;
INSERT INTO `CustomText` (`customtextid`, `page`, `tag`, `textcontents`) VALUES
(1, 'My Profile', 'biography_note', 'Note: Your biography will appear immediately following your name in the program. If you have previously been a participant, the stored bio for you will be displayed below. Please verify the text for publication.<BR>\n'),
(2, 'My Availability', 'note_after_times', 'Please note: the Masquerade is generally scheduled for Sat 8-10pm.');
INSERT INTO PatchLog (patchname) VALUES ('40_custom_text.sql');
