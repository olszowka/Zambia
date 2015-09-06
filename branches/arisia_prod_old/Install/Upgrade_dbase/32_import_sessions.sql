## This script creates 4 new tables for capture of session data from previous cons and importing into current con.
CREATE TABLE PreviousCons
    (previousconid INT(11) NOT NULL AUTO_INCREMENT,
     previousconname VARCHAR(128) DEFAULT NULL,
     display_order INT(11),
     PRIMARY KEY (previousconid))
    ENGINE=INNODB DEFAULT CHARSET=utf8;
CREATE TABLE PreviousConTracks
    (previousconid INT(11) NOT NULL,
     previoustrackid INT(11) NOT NULL,
     trackname VARCHAR(50) DEFAULT NULL,
     PRIMARY KEY (previousconid,previoustrackid),
     KEY previousconid (previousconid),
     CONSTRAINT PreviousCons_ibfk_1 FOREIGN KEY (previousconid) REFERENCES PreviousCons (previousconid))
    ENGINE=INNODB DEFAULT CHARSET=utf8;
CREATE TABLE TrackCompatibility
    (previousconid INT(11) NOT NULL,
     previoustrackid INT(11) NOT NULL,
     currenttrackid INT(11) NOT NULL,
     PRIMARY KEY (previousconid,previoustrackid),
     KEY currenttrackid (currenttrackid),
     CONSTRAINT TrackCompatibility_ibfk_1 FOREIGN KEY (previousconid,previoustrackid) REFERENCES PreviousConTracks (previousconid,previoustrackid),
     CONSTRAINT TrackCompatibility_ibfk_2 FOREIGN KEY (currenttrackid) REFERENCES Tracks (trackid))
    ENGINE=INNODB DEFAULT CHARSET=utf8;
CREATE TABLE PreviousSessions
    (previousconid INT(11) NOT NULL,
     previoussessionid INT(11) NOT NULL,
     previoustrackid INT(11) NOT NULL,
     previousstatusid INT(11) NOT NULL,
     typeid INT(11) NOT NULL,
     divisionid INT(11) NOT NULL,
     languagestatusid INT(11),
     title VARCHAR(100),
     secondtitle VARCHAR(100),
     pocketprogtext TEXT,
     progguiddesc TEXT,
     persppartinfo TEXT,
     duration TIME,
     estatten INT(11),
     kidscatid INT(11) NOT NULL,
     signupreq TINYINT(1),
     notesforpart TEXT,
     notesforprog TEXT,
     invitedguest TINYINT(1),
     importedsessionid INT(11),
     PRIMARY KEY (previousconid,previoussessionid),
     KEY previousconid (previousconid),
     KEY previoustrackid (previousconid,previoustrackid),
     KEY previousstatusid (previousstatusid),
     KEY typeid (typeid),
     KEY divisionid (divisionid),
     KEY languagestatusid (languagestatusid),
     KEY kidscatid (kidscatid),
     CONSTRAINT FOREIGN KEY (previousconid) REFERENCES PreviousCons (previousconid),
     CONSTRAINT FOREIGN KEY (previousconid,previoustrackid) REFERENCES PreviousConTracks (previousconid,previoustrackid),
     CONSTRAINT FOREIGN KEY (previousstatusid) REFERENCES SessionStatuses (statusid),
     CONSTRAINT FOREIGN KEY (typeid) REFERENCES Types (typeid),
     CONSTRAINT FOREIGN KEY (divisionid) REFERENCES Divisions (divisionid),
     CONSTRAINT FOREIGN KEY (languagestatusid) REFERENCES LanguageStatuses (languagestatusid),
     CONSTRAINT FOREIGN KEY (kidscatid) REFERENCES KidsCategories (kidscatid))
    ENGINE=INNODB DEFAULT CHARSET=utf8;
INSERT INTO PatchLog (patchname) VALUES ('32_import_sessions.sql');

        
