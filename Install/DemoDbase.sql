-- Copyright (c) 2011-2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
INSERT INTO `CongoDump`
    (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype)
    VALUES
    ('1','Admin','User','Admin','781-555-1212','admin@zambiademo.org','123 First Street',NULL,'Arlington','MA','02474','USA',''),
    ('2','Staff','User','Admin','781-555-1111','staff@zambiademo.org','256 First Street',NULL,'Arlington','MA','02474','USA',''),
    ('3','Participant','User','Participant','617-555-1212','participant@zambiademo.org','123 Fifth Street',NULL,'Boston','MA','02100','USA',''),
    ('4','James','Kirk','Kirk','617-555-1213','jtkirk@zambiademo.org','1 Third Street',NULL,'Boston','MA','02100','USA',''),
    ('5','Mr.','Spock','Spock','617-555-1214','spock@zambiademo.org','1 Fourth Street',NULL,'Boston','MA','02100','USA',''),
    ('6','Leonard','McCoy','Bones','617-555-1215','mccoy@zambiademo.org','1 Fifth Street',NULL,'Boston','MA','02100','USA',''),
    ('7','Nikota','Uhura','Nikota','617-555-1216','uhura@zambiademo.org','1 Sixth Street',NULL,'Boston','MA','02100','USA',''),
    ('8','Christine','Chapel','Christine','617-555-1217','chapel@zambiademo.org','1 Seventh Street',NULL,'Boston','MA','02100','USA',''),
    ('9','Janice','Rand','Janice','617-555-1218','rand@zambiademo.org','1 Eighth Street',NULL,'Boston','MA','02100','USA','');

INSERT INTO `Participants`
    (badgeid, password, bestway, interested, bio, htmlbio, pubsname, uploadedphotofilename, approvedphotofilename,
        photodenialreasonothertext, photodenialreasonid, photouploadstatus, share_email, staff_notes, use_photo, data_retention)
    VALUES
    ('1','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL, 'Admin User has been program chair of Zambiademo for 5 years.',
        '<p>Admin User has been program chair of Zambiademo for 5 years.</p>','Admin User',NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 1),
    ('2','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL,
        'Staff User has been on the program committee of Zambiademo for 3 years.',
        '<p>Staff User has been on the program committee of Zambiademo for 3 years.</p>','Staff User',NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 1),
    ('3','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL, 'Participant User has been on program of Zambiademo for 5 years.',
        '<p>Participant User has been on program of Zambiademo for 5 years.</p>','Participant User',NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 1),
    ('4','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL, 'James T. Kirk has been captain of the starship Enterprise for 5 years.',
        '<p>James T. Kirk has been captain of the starship Enterprise for 5 years.</p>','James T. Kirk',NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 1),
    ('5','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL,
     'Commander Spock has been science officer of the starship Enterprise for 10 years.',
        '<p>Commander Spock has been science officer of the starship Enterprise for 10 years.</p>','Mr. Spock',NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 1),
    ('6','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL,
     'Leonard McCoy, MD, has been chief medical officer of the starship Enterprise for 7 years.',
        '<p>Leonard McCoy, MD, has been chief medical officer of the starship Enterprise for 7 years.</p>','Leonard McCoy, MD',NULL, NULL, NULL, NULL, NULL,
        1, NULL, 1, 1),
    ('7','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL,
     'Nikota Uhura has been communications officer of the starship Enterprise for 4 years.',
        '<p>Nikota Uhura has been communications officer of the starship Enterprise for 4 years.</p>','Nikota Uhura',NULL, NULL, NULL, NULL, NULL, 1, NULL,
        1, 1),
    ('8','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL,
     'Christine Chapel has been a nurse serving on the starship Enterprise for 5 years.',
        '<p>Christine Chapel has been a nurse serving on the starship Enterprise for 5 years.</p>','Christine Chapel',NULL, NULL, NULL, NULL, NULL, 1, NULL,
        1, 1),
    ('9','$2y$10$ynxaiOCmt3bOqeDFHPEjA.wuP2ptf5Lt563pqleZ4rro7t0ycTjGe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 0);

INSERT INTO `UserHasPermissionRole`
    (badgeid, permroleid)
    VALUES
    ('1', 1),
    ('1', 4),
    ('2', 3),
    ('3', 4),
    ('4', 4),
    ('5', 4),
    ('6', 4),
    ('7', 4),
    ('8', 4),
    ('9', 5);

INSERT INTO `Rooms`
    (roomid, roomname, display_order, height, dimensions, area, `function`, floor, notes, opentime1,
     closetime1, opentime2, closetime2, opentime3, closetime3, is_scheduled)
    VALUES
    (1,'Alcott', 10,'8 ft 5in','23 x 34','654','Panels','1 East',NULL,NULL,NULL,NULL,NULL,NULL,NULL, 1),
    (2,'Brandeis', 20,'8 ft 6in','13 x 35','446','Panels','1 East',NULL,NULL,NULL,NULL,NULL,NULL,NULL, 1),
    (3,'Cabot', 30,'8 ft 6in','19 x 29','515','Panels','1 East',NULL,NULL,NULL,NULL,NULL,NULL,NULL, 1),
    (4,'Delaware', 40,'8 ft 6in','19 x 29','515','Green Room','1 East',NULL,NULL,NULL,NULL,NULL,NULL,NULL, 0);

INSERT INTO `RoomHasSet`
    (roomhassetid, roomid, roomsetid, capacity)
    VALUES
    (1, 1, 1, 50),
    (2, 2, 1, 50),
    (3, 3, 1, 75),
    (4, 4, 1, 100),
    (5, 4, 3, 50);

INSERT INTO `Sessions`
    (sessionid, trackid, typeid, divisionid, pubstatusid, languagestatusid, pubsno, title, secondtitle,
        pocketprogtext, progguiddesc, progguidhtml, persppartinfo, meetinglink, panelistlink, captionlink,
        recordinglink, duration, estatten, kidscatid, signupreq, roomsetid, notesforpart, servicenotes,
        statusid, notesforprog, warnings, invitedguest, ts)
    VALUES
        (1, 1, 1, 1, 2, 1, '', 'Are Fans Interesting?', '', '', 'Who are these people at the Con and why do we care?',
            '<p>Who are these people at the Con and why do we care?</p>', 'Talk about people are cons and make fun of them.',
            '', '', '', '', '01:00:00', 20, 2, 0, 1, 'Don\'t come if you don\'t want to be made fun of.', '', 3, '', 0, 0,
            '2026-06-22 14:40:51'),
        (2, 1, 3, 1, 2, 1, '', 'Book Signing - Jacqueline Carey', NULL, '',
         'Guest of Honor Jacqueline Carey autographs her works including Kushiel\'s Avatar series and the Sundering duology. Books available for purchase at the signing session. No more than 3 autographs per person, please.',
            NULL, '', NULL, NULL, NULL, NULL, '02:00:00', 100, 2, 0, 1, 'Will JC attend a play party?',
            'Will need credit card set up for book-seller table.', 6, 'Will need gopher for duration of signing', 0, 0, '2006-10-04 22:38:14'),
        (3, 1, 1, 2, 2, 1, '', 'NaNoWriMo And You', '', '',
            'The National Novel Writing Month project is in it\'s Umpteenth Year. Learn about NaNoWriMo from those who have completed it, and see if it\'s the key to completing your first (or next) novel in 30 days.',
            NULL, 'This panel will be moderated by the new NaNoWriMo liason, and we\'re looking for panelists who have participated in NaNoWriMo before, whether they\'ve completed it or not.',
            NULL, NULL, NULL, NULL, '01:00:00', 30, 2, 0, 1, '', '', 3, '', 0, 0, '2026-06-25 13:51:36'),
        (4, 1, 1, 1, 2, 1, '', 'Coffee, Tea and Squee', '', '', '', '',
            'We\'ve got representatives from Dean\'s Beans and Cooks Shop Here (a NoHo tea shop) but are interested in other panelists who can provide similar non-alcoholic beverage samples. Please contact Jess Hartley at piconpanels@gmail.com if you think you can provide such.',
            '', NULL, NULL, '', '01:00:00', NULL, 3, 0, 1, '', '', 4, 'Rep from Dean\'s Beans had to cancel.  Will try again next year.',
            0, 0, '2026-06-22 13:09:00'),
        (5, 1, 2, 1, 2, 1, '', 'Midnight Filk', '', NULL, 'Open Mic Filk Session ', NULL,
            'We\'d like facilitators to keep things moving for this open mic, collaborative session.',
            NULL, NULL, NULL, NULL, '01:00:00', 0, 2, 0, 1, '', '', 6, '', 0, 0, '2006-10-04 22:38:14'),
        (6, 2, 3, 2, 2, 1, '', 'Concert: James Kirk', '', '',
            'Come hear James Kirk play some of your favorites and introduce a few new tracks from his upcoming album.', '<p>Come hear James Kirk play some of your favorites and introduce a few new tracks from his upcoming album.</p>',
            '', '', NULL, NULL, '', '01:15:00', NULL, 2, 0, 2, '', 'Full concert sound system', 3,
            '', NULL, 0, '2026-06-22 14:41:04'),
        (7, 1, 1, 1, 2, 1, '', 'Pricing your art', '', '', 'There are lots of factors to take into consideration when figuring out how much to sell art for.',
            '<p>There are lots of factors to take into consideration when figuring out how much to sell art for.</p>',
            'Seeking artists who earn their entire living by selling art.', '', NULL, NULL, '',
            '01:15:00', NULL, 2, 0, 1, '', '', 3, '', NULL, 0, '2026-06-22 14:49:32'),
        (8, 1, 1, 1, 2, 1, '', 'It’s Not Dead Yet', '', '',
            'Was the last time you posted to your blog or podcast... before the pandemic? It can be hard to determine whether an abandoned online presence should be laid to rest or resurrected. Panelists will discuss the importance of audience, platform, and end-goals when determining when to revive an account, and when to just walk away.',
            '<p>Was the last time you posted to your blog or podcast... before the pandemic? It can be hard to determine whether an abandoned online presence should be laid to rest or resurrected. Panelists will discuss the importance of audience, platform, and end-goals when determining when to revive an account, and when to just walk away.</p>',
            'If this looks familiar, it probably is. Last year it received significant interest, but we weren\'t able to fit it in the schedule. We hope to see it get another chance.',
            '', NULL, NULL, '', '01:00:00', NULL, 2, 0, 1, '', '', 3, '', NULL, 0, '2026-06-22 14:50:24'),
        (9, 1, 1, 1, 2, 1, '', '“Don’t Get Me Started!\"', '', '',
            'Our intelligent and witty panelists will pull a topic out of a hat. They will then have two minutes to rant on that topic. Audience will submit topics, the least rant-worthy topics imaginable. Moderator retains the right to reject topics that violate this standard. In the second round, our panelists will take it to the next level and create a conspiracy theory linking two topics from the hat.',
            '<p>Our intelligent and witty panelists will pull a topic out of a hat. They will then have two minutes to rant on that topic. Audience will submit topics, the least rant-worthy topics imaginable. Moderator retains the right to reject topics that violate this standard. In the second round, our panelists will take it to the next level and create a conspiracy theory linking two topics from the hat.</p>',
            'If this looks familiar, it probably is. Last year it received significant interest, but we weren\'t able to fit it in the schedule. We hope to see it get another chance.',
            'https://us06web.zoom.us/j/88961605030?pwd=IyWyBpCXEAUT18penXpHS1pwGK0qva.1', '', '', '',
            '01:00:00', NULL, 3, 0, 1, '', '', 2, 'Have notecards and pens available for this panel.',
            NULL, 0, '2026-06-22 14:40:18'),
        (10, 1, 1, 1, 2, 1, '', 'Indigenous Genre Fiction', '', '',
            'More indigenous genre fiction is available than ever before, offering viewpoints from groups often ignored or stereotyped. This panel will discuss current trends in indigenous genre fiction, emergent themes, and creators to watch.',
            '<p>More indigenous genre fiction is available than ever before, offering viewpoints from groups often ignored or stereotyped. This panel will discuss current trends in indigenous genre fiction, emergent themes, and creators to watch.</p>',
            'If this looks familiar, it probably is. Last year it received significant interest, but we weren\'t able to fit it in the schedule. We hope to see it get another chance.',
            '', NULL, NULL, '', '01:00:00', NULL, 2, 0, 1, '', '', 2, '', NULL, 0, '2026-06-20 12:46:05'),
        (11, 1, 1, 1, 2, 1, '', 'What Should I Read Next? Horror Version', '', '',
            'Who are some of the new voices in Horror? What are some underappreciated or undiscovered classics you should be reading? Our panelists will share their favorites, the ones that make them howl to the moon. Unless you have a fogged mirror handy, bring something to take notes with.',
            '<p>Who are some of the new voices in Horror? What are some underappreciated or undiscovered classics you should be reading? Our panelists will share their favorites, the ones that make them howl to the moon. Unless you have a fogged mirror handy, bring something to take notes with.</p>',
            'If this looks familiar, it probably is. Last year it received significant interest, but we weren\'t able to fit it in the schedule. We hope to see it get another chance.',
            '', '', '', '', '01:00:00', NULL, 2, 0, 1, '', '', 2, '-- borrowed from Mysticon 2019',
            NULL, 0, '2026-06-22 14:40:25'),
        (12, 4, 3, 1, 2, 1, '', 'You Spin Me Right Round:  80s Alternative Dance Party', '', '',
            'Come dance to your favorite \"Alternative\" music from the \'80\'s and a few contemporary favorites as well.',
            '<p>Come dance to your favorite \"Alternative\" music from the \'80\'s and a few contemporary favorites as well.</p>',
            '', '', NULL, NULL, '', '01:15:00', NULL, 2, 0, 1, '', 'Thump thump sound system', 2, '',
            NULL, 1, '2026-06-20 12:52:31');

INSERT INTO SessionEditHistory
    (sessionid, badgeid, name, email_address, timestamp, sessioneditcode, statusid, editdescription)
    VALUES
    (1, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 13:10:48', 3, 3, null),
    (1, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:40:01', 3, 2, null),
    (1, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:40:51', 4, 3, 'Tue 10:00 AM in 1'),
    (3, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-25 13:51:36', 4, 3, 'Tue 11:00 AM in 1'),
    (4, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 13:09:00', 3, 4, null),
    (6, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:41:04', 4, 3, 'Tue 10:00 PM in 1'),
    (7, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:49:32', 4, 3, 'Tue 10:00 AM in 2'),
    (8, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:49:44', 4, 3, 'Tue 10:00 AM in 2'),
    (8, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:50:03', 5, 2, null),
    (8, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:50:24', 4, 3, 'Tue 11:00 AM in 2'),
    (9, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:40:18', 3, 2, null),
    (11, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-22 14:40:25', 3, 2, null),
    (12, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-20 12:49:01', 2, 2, null),
    (12, '1', 'Admin User', 'admin@zambiademo.org', '2026-06-20 12:52:31', 3, 2, null);

INSERT INTO ParticipantAvailability
    (badgeid, maxprog, preventconflict, otherconstraints, numkidsfasttrack)
    VALUES
    ('3', 5, 'The masquerade.', 'I need to eat lunch between noon and 2 on Saturday.', null),
    ('5', 3, '', '', null);

INSERT INTO ParticipantAvailabilityDays
    (badgeid, day, maxprog)
    VALUES
    ('3', 1, 2),
    ('3', 2, 3),
    ('3', 3, 2),
    ('5', 1, 1),
    ('5', 2, 1),
    ('5', 3, 1);

INSERT INTO ParticipantAvailabilityTimes
    (badgeid, availabilitynum, starttime, endtime)
    VALUES
    ('3', 1, '19:00:00', '22:00:00'),
    ('3', 2, '32:30:00', '46:00:00'),
    ('3', 3, '58:00:00', '61:00:00'),
    ('5', 1, '16:00:00', '64:00:00');

INSERT INTO ParticipantHasCredential
    (badgeid, credentialid)
    VALUES
    ('3', 2),
    ('5', 3),
    ('3', 4),
    ('5', 5);

INSERT INTO ParticipantHasRole
    (badgeid, roleid)
    VALUES
    ('5', 1),
    ('3', 2),
    ('5', 2),
    ('3', 3);

INSERT INTO ParticipantHasTag
    (badgeid, participanttagid)
    VALUES
    ('4', 1),
    ('5', 1),
    ('5', 3),
    ('6', 3);

INSERT INTO ParticipantInterests
    (badgeid, yespanels, nopanels, yespeople, nopeople, otherroles)
    VALUES
    ('3', 'I can do a workshop on editing novels and other long forms.', 'I''ve done too many year in review panels--not another one.', 'I love being on panels with Mr. Spock.', 'Kolos', ''),
    ('5', '', '', '', '', 'Performing on Vulcan harp.');

INSERT INTO ParticipantOnSessionHistory
    (participantonsessionhistoryid, badgeid, sessionid, moderator, participantonsessionid, createdts, createdbybadgeid, inactivatedts, inactivatedbybadgeid)
    VALUES
    (1, '4', 6, 0, null, '2026-06-22 18:36:25', '1', null, null),
    (2, '3', 1, 1, null, '2026-06-22 18:37:06', '1', null, null),
    (3, '5', 1, 0, null, '2026-06-22 18:37:06', '1', null, null),
    (4, '4', 3, 0, null, '2026-06-25 13:50:45', '1', null, null),
    (5, '5', 3, 0, null, '2026-06-25 13:50:57', '1', null, null),
    (6, '3', 3, 0, null, '2026-06-25 13:51:12', '1', null, null);

UPDATE ParticipantOnSessionHistory
    SET inactivatedts = '2026-06-25 13:51:18', inactivatedbybadgeid = '1'
    WHERE participantonsessionhistoryid = 4;

INSERT INTO ParticipantOnSessionHistory
    (participantonsessionhistoryid, badgeid, sessionid, moderator, participantonsessionid, createdts, createdbybadgeid, inactivatedts, inactivatedbybadgeid)
    VALUES
    (7, '4', 3, 1, null, '2026-06-25 13:51:18', '1', null, null);

INSERT INTO ParticipantSessionInterest
    (badgeid, sessionid, `rank`, willmoderate, comments)
    VALUES
    ('3', 1, 1, 1, 'I''m interesting and I think all fans should be.'),
    ('3', 3, 3, 0, 'I''ve used various tools to help me sit down and write.'),
    ('3', 7, 5, 0, 'I''ve sold some art so I could fill in if needed.'),
    ('4', 3, null, null, null),
    ('4', 6, null, null, null),
    ('5', 1, 2, 0, 'Most people who think they are interesting aren''t.'),
    ('5', 3, null, null, null),
    ('5', 9, 4, 0, ''),
    ('5', 10, 1, 0, 'In particular, I''d like to discuss fiction indigenous to Vulcan.');

INSERT INTO ParticipantSuggestions
    (badgeid, paneltopics, otherideas, suggestedguests)
    VALUES
    ('4', 'Panel discussion on which Star Trek captain is the best leader.', '', '');

INSERT INTO Schedule
    (scheduleid, sessionid, roomid, starttime)
    VALUES
    (1, 1, 1, '34:00:00'),
    (2, 6, 1, '46:00:00'),
    (3, 7, 2, '34:00:00'),
    (5, 8, 2, '35:00:00'),
    (6, 3, 1, '35:30:00');

INSERT INTO SessionHasTag
    (sessionid, tagid)
    VALUES
    (7, 2),
    (12, 15),
    (6, 17),
    (12, 17);

INSERT INTO SurveyQuestionConfig
    (questionid, shortname, description, prompt, hover, display_order, typeid, required, publish,
        privacy_user, searchable, ascending, display_only, min_value, max_value)
    VALUES
    (1, 'Pronouns', 'Sample survey stuff', 'What are your pronouns?',
        'Provide as much or as little detail as you are comfortable with.', 10, 60, 0, 1, 0, 0, 1, 0, 0, 8192),
    (2, 'Availability+Preferences', '', '',
        '<p>Use the <a title="Availability" href="my_sched_constr.php" target="_blank" rel="noopener">Availability</a> section of this survey to share when you will be available to participate on Program.</p><p>Use the <a title="General Interests" href="my_interests.php" target="_blank" rel="noopener">General Interests</a> section of this survey to share your interest in an author reading or autographing session and our Saturday Book Launch party as well as preferences regarding panel topics and fellow panelists, including panelists you''d prefer to avoid presenting alongside. (All names submitted will be kept <span style="text-decoration: underline;">strictly confidential</span> within the programming team.) Submit a proposed Program item to us using the <a href="https://forms.gle/" target="_blank">Program Item Suggestion</a> form.  The Program team will review each idea and will add any approved idea to the Programming database as potential Program items.</p>',
        50, 5, 1, 0, 0, 0, 1, 1, 0, 8192),
    (3, 'Accessibility', '', 'Please list any accessibility needs you have.', '', 20, 70, 0, 0, 0, 0, 1, 0, 0, 8192),
    (4, 'Marginalized', '',
        'If you consider yourself a member of a community that can offer a non-traditional viewpoint on programming, please explain.',
        'How would you identify yourself?', 30, 70, 0, 0, 1, 1, 1, 0, 0, 8192),
    (5, 'Experience', '',
        'Please tell us anything else about you that you think might help us understand how you might best be matched with sessions. This is information that isn''t necessarily in your bio, but helps us to know you as a panelist.',
        '', 40, 70, 0, 0, 0, 1, 1, 0, 0, 8192);

INSERT INTO ParticipantSurveyAnswers
    (participantid, questionid, privacy_setting, value, othertext, lastupdate, updatedby)
    VALUES
    ('3', 1, 0, 'he/him', null, '2026-07-03 17:30:05', '3'),
    ('3', 3, 0, '', null, '2026-07-03 17:30:05', '3'),
    ('3', 4, 0, 'I\'m Latino on my mother\'s side and have a lot of relatives on that side.  My perspective is strongly influenced by that community.', null, '2026-07-03 17:30:05', '3'),
    ('3', 5, 0, 'For an upcoming novel, I\'ve been researching historic migrations of people across the current US/Mexico border and cultures shared by people on both sides of that border.', null, '2026-07-03 17:30:05', '3'),
    ('6', 1, 0, '', null, '2026-07-03 17:31:57', '6'),
    ('6', 3, 0, '', null, '2026-07-03 17:31:57', '6'),
    ('6', 4, 0, '', null, '2026-07-03 17:31:57', '6'),
    ('6', 5, 0, 'I\'ve portrayed a doctor on television so often, I can contribute dialog to make scenes involving medical personnel more realistic.', null, '2026-07-03 17:31:57', '6');
