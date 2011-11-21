## This script creates the Reports table and prepopulates some
## reports.
## NOTE this should be applied to the Reports or General database, not to
## the main one, so replace REPORTDB with your DB name.  If you are only
## hosting one database (and not having your REPORTDB available to
## multiple cons) you can remove all instances of "REPORTDB." below.
## The PersonalFlow should be local to the year, so that is the only
## one created in the base DB.

CREATE TABLE REPORTDB.Reports (
  `reportid` INT(11) NOT NULL auto_increment,
  `reportname` varchar(30) NOT NULL default '',
  `reporttitle` text,
  `reportdescription` text,
  `reportadditionalinfo` text,
  `reportquery` text,
  PRIMARY KEY  (`reportid`),
  KEY `reportname` (`reportname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE PersonalFlow (
  `pflowid` INT(11) NOT NULL auto_increment,
  `reportid` INT(11) NOT NULL default '0',
  `badgeid` varchar(15) NOT NULL default '',
  `pfloworder` INT(11) NOT NULL default '0',
  `phaseid` INT(11) default NULL,
  PRIMARY KEY  (`pflowid`),
  CONSTRAINT `GroupFlow_ibfk_1` FOREIGN KEY (`reportid`) REFERENCES `REPORTDB.Reports` (`reportid`)
  CONSTRAINT `PersonalFlow_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE REPORTDB.GroupFlow (
  `gflowid` INT(11) NOT NULL auto_increment,
  `reportid` INT(11) NOT NULL default '0',
  `gflowname` varchar(15) NOT NULL default '',
  `gfloworder` INT(11) NOT NULL default '0',
  `phaseid` INT(11) default NULL,
  PRIMARY KEY  (`gflowid`), 
  CONSTRAINT `GroupFlow_ibfk_1` FOREIGN KEY (`reportid`) REFERENCES `Reports` (`reportid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO PatchLog (patchname) VALUES ('26_migrate_reports.sql');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('finalschedbreif','Schedule','<P>Below is the Panel, Events, Film, Anime, Video and Arisia TV schedule.</P>
','','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    roomname, 
    trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title
  FROM
      Sessions S, 
      Schedule SCH, 
      Tracks T, 
      Rooms R 
  WHERE
    R.roomid=SCH.roomid and
    T.trackid=S.trackid and
    SCH.sessionid = S.sessionid and
    S.pubstatusid = 2
  ORDER BY
    SCH.starttime,
    T.trackname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictpartintnotcomp','Conflict Report - Interested Participants that wont comp','<P>Comps are limited to participants on 3 or more panels.  These folks are on less than 3 scheduled panels.</P>
','','SELECT
    P.badgeid, 
    P.pubsname, 
    if (X.Schd is NULL, 0, X.Schd) Schd, 
    if (Y.Intr is NULL, 0, Y.Intr) Intr
  FROM
      CongoDump C, 
      Participants P 
    LEFT JOIN (SELECT
                   POS.badgeid, 
                   count(POS.sessionid) Schd
                 FROM
                     ParticipantOnSession POS,
                     Schedule
                 WHERE
                   S.sessionid=POS.sessionid
                 GROUP BY
                   POS.badgeid) X on P.badgeid=X.badgeid 
    LEFT JOIN (SELECT
                   PSI.badgeid, 
                   count(PSI.sessionid) as Intr
                 FROM
                     ParticipantSessionInterest PSI
                 GROUP BY
                     PSI.badgeid) Y on Y.badgeid=P.badgeid
  WHERE
    C.badgeid=P.badgeid and
    interested=1 and
    C.regtype is NULL 
  HAVING
    Schd < 3
  ORDER BY
    Intr DESC,
    cast(C.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('assignedsession','Assigned Session by Session','<P>Shows who has been assigned to each session. (Sorted by track and then sessionid.)</P>
','','SELECT 
    Trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    P.pubsname,
    if ((moderator=1), \'Yes\', \' \') as \'Moderator\',
    Statusname 
  FROM
      ParticipantOnSession POS,
      Sessions S,
      Participants P,
      Tracks T,
      SessionStatuses SS
  WHERE
    POS.badgeid=P.badgeid and
    POS.sessionid=S.sessionid and
    T.trackid=S.trackid and
    S.statusid=SS.statusid 
  ORDER BY
    trackname, 
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('participantinterestedcount','Participant Interested Count','<P>Show the number of people that are interested in attending.</P>
','<P>Interested, 1=yes, 2=no, 0=did not pick, NULL=did not hit save.</P>','SELECT
    P.Interested as "Interest Flag",
    count(P.badgeid) as Count
  FROM
      Participants P 
  GROUP BY
    P.interested');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('schedpartavail','Participant availablity','<P>When they said they were available.</P>
','','SELECT
        P.badgeid, P.pubsname, 
        DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') AS \'Start Time\', 
        DATE_FORMAT(ADDTIME(\'$ConStartDatim\',endtime),\'%a %l:%i %p\') AS \'End Time\',
        PA.otherconstraints,
        PA.preventconflict
    FROM
        Participants AS P LEFT JOIN
        ParticipantAvailabilityTimes AS PAT USING (badgeid)
        JOIN ParticipantAvailability PA USING (badgeid)
    WHERE
        P.interested=1
    ORDER BY
        CAST(P.badgeid AS UNSIGNED),starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('panelmerge','CSV -- Report for Program Panel Merge','<P>sessionid,room,start time,duration,track,title,participants</P>
','','SELECT
    S.sessionid, 
    R.roomname AS room, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',SCH.starttime),\'%a %l:%i %p\') AS \'start time\', 
    CASE
      WHEN HOUR(S.duration) < 1 THEN
        concat(date_format(S.duration,\'%i\'),\'min\')
      WHEN MINUTE(S.duration)=0 THEN
        concat(date_format(S.duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(S.duration,\'%k\'),\'hr \',date_format(S.duration,\'%i\'),\'min\')
      END AS duration,
    T.trackname AS track, 
    S.title, 
    group_concat(P.pubsname, if(POS.moderator=1,\'(m)\',\'\') ORDER BY POS.moderator DESC SEPARATOR \', \') AS participants,
    PUB.pubstatusname AS status
  FROM
      Sessions S
    JOIN Schedule SCH USING(sessionid)
    JOIN Rooms R USING(roomid)
    JOIN Tracks T USING(trackid)
    JOIN PubStatuses PUB USING(pubstatusid)
    LEFT JOIN ParticipantOnSession POS USING(sessionid)
    LEFT JOIN Participants P USING(badgeid)
  GROUP BY
    S.sessionid
  ORDER BY
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessionedithistoryall','Session Edit History Report - All','<P>For each session, show the entire edit history.</P>
','','SELECT
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    T.trackname as \'Track\', 
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    SS.statusname as \'Current<BR>Status\',
    timestamp as \'When\', 
    concat(name,\' (\', email_address,\') \') as \'Who\', 
    concat(SEC.description, \' \',SS2.statusname) as \'What\' 
  FROM
      Sessions S, 
      Tracks T, 
      SessionStatuses SS, 
      SessionEditHistory SEH, 
      SessionEditCodes SEC, 
      SessionStatuses SS2 
  WHERE
    S.trackid=T.trackid and
    S.statusid = SS.statusid and
    S.sessionid = SEH.sessionid and
    SEH.sessioneditcode=SEC.sessioneditcode and
    SS2.statusid=SEH.statusid and
    S.statusid >= 1 and
    S.statusid <= 7
  ORDER BY
    S.sessionid, 
    SEH.timestamp');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('partbio','Conflict Report - All Interested Participant Web/Book Bios','<P>Show the badgeid, pubsname, edited web bio, URI block and edited program book bio for each participant who indicated attendance.</P>
','','SELECT
    DISTINCT(P.badgeid), 
    concat('<A HREF=StaffEditCreateParticipant.php?action=edit&partid=',P.badgeid,'>',P.pubsname,'</A>') AS Pubsname, 
    concat('<A HREF=StaffEditBios.php?badgeid=',P.badgeid,'>',BWE.biotext,'</A>') AS 'Web Bio',
    concat('<A HREF=StaffEditBios.php?badgeid=',P.badgeid,'>',BUE.biotext,'</A>') AS 'URI Block',
    concat('<A HREF=StaffEditBios.php?badgeid=',P.badgeid,'>',BBE.biotext,'</A>') AS 'Program Book Bio'
  FROM
      Participants P
    LEFT JOIN (SELECT 
                   badgeid,
                   biotext
                 FROM
                     $BioDB.Bios
                   JOIN $BioDB.BioTypes using (biotypeid)
                   JOIN $BioDB.BioStates using (biostateid)
                 WHERE
                   biolang in ('en-us') AND
                   biotypename in ('web') AND 
                   biostatename in ('edited')) BWE USING (badgeid)
    LEFT JOIN (SELECT 
                   badgeid,
                   biotext
                 FROM
                     $BioDB.Bios
                   JOIN $BioDB.BioTypes using (biotypeid)
                   JOIN $BioDB.BioStates using (biostateid)
                 WHERE
                   biolang in ('en-us') AND
                   biotypename in ('book') AND 
                   biostatename in ('edited')) BBE USING (badgeid)
    LEFT JOIN (SELECT 
                   badgeid,
                   biotext
                 FROM
                     $BioDB.Bios
                   JOIN $BioDB.BioTypes using (biotypeid)
                   JOIN $BioDB.BioStates using (biostateid)
                 WHERE
                   biolang in ('en-us') AND
                   biotypename in ('uri') AND 
                   biostatename in ('edited')) BUE USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    P.interested=1 AND
    (permrolename in ('Participant') or
     permrolename like '%Super%')
  ORDER BY
    substring_index(pubsname," ",-1)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('progpacketmerge','Full Participant Schedule for the Program Packet Merge','<P>pubsname, (day, time, duration, room, mod)</P>
','<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>
','SELECT
    POS.badgeid,
    pubsname,
    group_concat(roomname,\'\",\"\',
		 DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\'),\'\",\"\',
		 concat(\'(\', 
                        CASE 
                          WHEN HOUR(duration) < 1 THEN 
                            concat(date_format(duration,\'%i\'),\'min\') 
                          WHEN MINUTE(duration)=0 THEN 
                            concat(date_format(duration,\'%k\'),\'hr\') 
                          ELSE
                            concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
                        END
			,\')\'),\'\",\"\',
		 trackname,\'\",\"\',
		 title,\'\",\"\',
		 if(moderator=1,\'M\',\'\'),\'\",\"\'
		 ORDER BY starttime) AS panelinfo 
  FROM
      Participants P,
      Rooms R,
      Sessions S,
      Schedule SCH,
      ParticipantOnSession POS,
      CongoDump C,
      Tracks T
  WHERE
    P.badgeid=C.badgeid and
    S.sessionid=SCH.sessionid and
    POS.sessionid=S.sessionid and
    POS.badgeid=C.badgeid and
    T.trackid=S.trackid and
    SCH.roomid = R.roomid and
    SCH.sessionid = S.sessionid
  GROUP BY
    badgeid
  ORDER BY
    pubsname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessioninterestpart','Session Interest by participant (all info)','<P>Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report.  (All data included including for invited sessions.) order by participant.</P>
','','SELECT 
    P.badgeid BadgeID, 
    P.pubsname, 
    T.trackname Track, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',SCH.starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    I.rank Rank, 
    I.comments Comments 
  FROM
      Participants as P, 
      ParticipantSessionInterest as I, 
      Tracks as T,
      Sessions as S
      left join Schedule SCH 
      on S.sessionid=SCH.sessionid 
  WHERE
    P.badgeid=I.badgeid and
    S.sessionid=I.sessionid and 
    T.trackid=S.trackid 
  ORDER BY
    cast(P.badgeid as unsigned) ');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictpartbio','Conflict Report - Participant Web Bio Different From Edited Version','<P>Show the pubsname and conflicting/missing web bio entries for each participant who indicated attendance.</P>
','','SELECT
    DISTINCT(concat('<A HREF=StaffEditCreateParticipant.php?action=edit&partid=',badgeid,'>',pubsname,'</A>')) AS Pubsname, 
    concat('<A HREF=StaffEditBios.php?badgeid=',badgeid,'>',BWR.biotext,'</A>') AS 'Presenter Generated Program Bio',
    concat('<A HREF=StaffEditBios.php?badgeid=',badgeid,'>',BWE.biotext,'</A>') AS 'Staff Edited Program Bio'
  FROM
      Participants P
    LEFT JOIN (SELECT 
                   badgeid,
                   bioid,
                   biotext
                 FROM
                     $BioDB.Bios
                   JOIN $BioDB.BioTypes using (biotypeid)
                   JOIN $BioDB.BioStates using (biostateid)
                 WHERE
                   biolang in ('en-us') AND
                   biotypename in ('web') AND 
                   biostatename in ('raw')) BWR USING (badgeid)
    LEFT JOIN (SELECT 
                   badgeid,
                   bioid,
                   biotext
                 FROM
                     $BioDB.Bios
                   JOIN $BioDB.BioTypes using (biotypeid)
                   JOIN $BioDB.BioStates using (biostateid)
                 WHERE
                   biolang in ('en-us') AND
                   biotypename in ('web') AND 
                   biostatename in ('edited')) BWE USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    P.interested=1 AND
    (permrolename in ('Participant') or
     permrolename like '%Super%') AND
    (BWR.bioid IS NULL OR
     BWE.bioid IS NULL OR
     BWR.biotext != BWE.biotext) 
  ORDER BY
    substring_index(pubsname," ",-1)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('intvschedpanel','Interest v Schedule - sorted by track, then title','<P>Show who is interested in each panel and if they are assigned to it.  Also show the scheduling information.</P>
','','SELECT
    X.trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',X.sessionid,\'>\', X.sessionid,\'</a>\') as Sessionid,
    X.title, 
    X.badgeid, 
    X.pubsname,  
    (if (X.assigned is NULL,\'no\',\'yes\')) as \'Assigned?\', 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',Y.roomid,\'>\', Y.roomname,\'</a>\') as Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Y.starttime),\'%a %l:%i %p\') as \'Start Time\' 
  FROM
      (SELECT
           PI.badgeid,
           PI.pubsname,
           PI.sessionid,
           POS.sessionid as assigned,
           title,
           trackname 
         FROM
             (SELECT
                  T.trackname,
                  S.title,
                  S.sessionid,
                  P.badgeid,
                  P.pubsname 
               FROM
                   Tracks T,
                   ParticipantSessionInterest PSI, 
                   Participants P,
                   Sessions S
               WHERE
                 S.trackid=T.trackid and
                 P.interested=1 and
                 P.badgeid=PSI.badgeid and
                 S.sessionid=PSI.sessionid ) PI 
                   left join ParticipantOnSession POS 
                          on POS.badgeid=PI.badgeid and POS.sessionid=PI.sessionid) X 
           LEFT JOIN (SELECT
                          SCH.starttime,
                          R.roomname,
                          R.roomid,
                          SCH.sessionid 
                       FROM 
                           Schedule SCH,
                           Rooms R 
                       WHERE
                           R.roomid=SCH.roomid) as Y on X.sessionid=Y.sessionid 
  ORDER BY
    X.trackname,
    X.title');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessioncomment','Session Commentary','<P>Comments recorded for Sessions.  <A HREF=\"CommentOnSessions.php\">(Add a comment)</A></P>
','','SELECT
    S.title,
    COS.commenter,
    COS.comment
  FROM
      Sessions S 
    JOIN
      CommentsOnSessions COS USING (sessionid)
  ORDER BY
    S.title');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictpickypeople','Conflict Report - Picky people','<P>Show who the picky people do not want to be on a panel with and who they are on panels with.</P>
','','SELECT 
    X.b AS badgeid, 
    X.pn AS pubsname, 
    X.no AS nopeople, 
    X.tn AS track, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',X.s,\'>\', X.s,\'</a>\') AS Sessionid, 
    X.sn AS sessionname, 
    group_concat(DISTINCT P2.pubsname,concat(\' (\',P2.badgeid,\')\') SEPARATOR \', \') AS \'others on this panel\' 
  FROM 
      (SELECT
            PI.badgeid as b,
            P.pubsname as pn,
            S.sessionid as s,
            nopeople as no,
            title as sn,
            trackname as tn 
          FROM
              ParticipantInterests PI,
              ParticipantOnSession PS, 
              Sessions S,
              Participants P,
              Tracks T 
          WHERE
            T.trackid=S.trackid and
            S.sessionid=PS.sessionid and
            PS.badgeid=PI.badgeid and
            P.badgeid=PI.badgeid and
            (nopeople is not null and nopeople!=\'\')) X, 
      Participants P2,
      ParticipantOnSession PSO 
  WHERE
    X.s=PSO.sessionid and P2.badgeid=PSO.badgeid 
  GROUP BY
    X.s 
  ORDER BY
    cast(X.b as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('publongdesc','CSV - Session Characteristics plus long description','<P>For Scheduled items ONLY. Show sessionid, track, type, divisionid, pubstatusid, pubno, pubchardest, kids, title, long description.</P>
','','SELECT
    S.sessionid,
    T.trackname AS track,
    TY.typename AS type,
    DV.divisionname AS division,
    PS.pubstatusname AS \'publication status\',
    S.pubsno,
    group_concat(PC.pubcharname SEPARATOR \' \') AS \'publication characteristics\',
    K.kidscatname AS \'kids category\',
    S.title,
    S.progguiddesc as description
  FROM
      Schedule SCH
    JOIN Sessions S USING(sessionid)
    JOIN Tracks T USING(trackid)
    JOIN Types TY USING(typeid)
    JOIN Divisions DV USING(divisionid)
    JOIN PubStatuses PS USING(pubstatusid)
    JOIN KidsCategories K USING(kidscatid)
    LEFT JOIN SessionHasPubChar SHPC USING(sessionid)
    LEFT JOIN PubCharacteristics PC USING(pubcharid)
  WHERE 
    PS.pubstatusname = \'Public\'
  GROUP BY
    scheduleid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessioninterestpartcount','Session Interest Counts by Participant','<P>Just how many panels did each participant sign up for anyway?</P>
','','SELECT
    P.Badgeid, 
    P.Pubsname, 
    count(sessionid) as Interested 
  FROM
      Participants P 
    LEFT JOIN ParticipantSessionInterest PSI on P.badgeid=PSI.badgeid 
  WHERE
    P.interested=1 
  GROUP BY
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('gameroomshedroom','Gaming Schedule','<P>All Gaming and Gaming Panels.  All these reports include both.</P>
','','SELECT
    roomname AS Room,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') AS \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE 
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    title AS Title,
    pocketprogtext AS Description,
    group_concat(\' \',pubsname,\' (\',P.badgeid,\')\') as \'Participants\'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE 
    trackname in (\'Gaming\')
  GROUP BY
    SCH.scheduleid
  ORDER BY
    R.roomname,
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('kidfasttracksched4con','FastTrack Schedule (for con)','<P>What is happening in FastTrack - The At-Con Version.</P>
','','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',SCH.starttime),\'%a %l:%i %p\') as \'Start Time\',
    R.roomname,
    S.title,
    group_concat(concat(P.pubsname,\' (\',P.badgeid,\')\') SEPARATOR \', \') as \'Participants\'
  FROM
      Schedule SCH
    JOIN Rooms R USING(roomid)
    JOIN Sessions S USING(sessionid)
    JOIN Tracks TR USING(trackid)
    LEFT JOIN ParticipantOnSession POS USING(sessionid)
    LEFT JOIN Participants P USING(badgeid)
  WHERE
    TR.trackname=\'FAST TRACK\'
  GROUP BY
    SCH.scheduleid
  ORDER BY
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('allroomschedtime','Full Room Schedule by time then room','<P>Lists all Sessions Scheduled in all Rooms (includes \"Public\", \"Do Not Print\" and \"Staff Only\").</P>
','','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname,
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    Function,
    Trackname,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',title,\'</a>\') as Title,
    PS.pubstatusname as PubStatus,
    group_concat(\' \',P.pubsname,\' (\',P.badgeid,\')\') as \'Participants\'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN PubStatuses PS USING (pubstatusid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
  GROUP BY
    SCH.scheduleid
  ORDER BY
    SCH.starttime,
    R.roomname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('thsessionservicesservice','Session Services by Service','<P>Which Session needs which Services? (Sorted by service then time.)</P>
','','SELECT
    X.Servicename as Service,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    Roomname,
    Trackname, 
    X.Sessionid,
    concat(\'<a href=EditSession.php?id=\',X.sessionid,\'>\',X.title,\'</a>\') Title
  FROM
      (SELECT
           duration, 
           trackname, 
           S.sessionid,
           title,
           servicename 
         FROM
             Tracks T, 
             Sessions S, 
             SessionHasService SF, 
             Services F 
         WHERE
           T.trackid=S.trackid and
           S.sessionid=SF.sessionid and
           F.serviceid=SF.serviceid) X,
      Rooms R, 
      Schedule SCH 
  WHERE
    X.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid 
  ORDER BY
    X.servicename, 
    starttime,
    roomname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessioninterestcount','Session Interest Report (counts)','<P>For each session, show number of participants who have put it on their interest list. (Excludes invited guest sessions.)</P>
','','SELECT
    T.trackname as Track, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as \'Session<BR>ID\',
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    count(PSI.badgeid) as \'Number<BR>of<BR>Participants\' 
  FROM
      Sessions AS S 
    JOIN Tracks AS T ON S.trackid=T.trackid 
    LEFT JOIN ParticipantSessionInterest AS PSI ON S.sessionid=PSI.sessionid 
  WHERE
    T.selfselect=1 and
    statusid in (2,3,7) 
  GROUP BY
    T.trackid, 
    S.sessionid 
  ORDER BY
    T.display_order, 
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictunder3assigned','Conflict Report - Scheduled Programming sessions without enough people','<P>This report runs against scheduled sessions in division program only.   If these are panels, you need at least 3 people.  All other types require at least 1.</P>
','','SELECT
    T.trackname, 
    Y.typename,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid, 
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    if(X.assigned is NULL, 0, X.assigned) assigned, concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname
  FROM
      Sessions  S
    LEFT JOIN (SELECT
                   S1.sessionid, count(badgeid) assigned 
                 FROM
                     Sessions S1 ,
                     ParticipantOnSession POS 
                 WHERE
                     S1.sessionid=POS.sessionid
                 GROUP BY
                     S1.sessionid ) X on S.sessionid=X.sessionid,
      Schedule SCH,
      Tracks T,
      Types Y,
      Divisions D,
      Rooms R
  WHERE
    S.sessionid=SCH.sessionid and
    S.trackid=T.trackid and
    S.typeid=Y.typeid and
    S.divisionid=D.divisionid and
    SCH.roomid=R.roomid and
    S.statusid=3 and
    D.divisionname=\'Programming\' and
    ((Y.typename = \'Panel\' and assigned<3) or
     (Y.typename != \'Panel\'  and assigned<1) or
     assigned is NULL)
  ORDER BY
    T.trackname,
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('invitation','Invited Guest Report','<P>For each invited guest session, list the participants who have been invited (and have not deleted the invitation.)</P>
','','SELECT
    T.trackname as Track,
    concat("<a href=EditSession.php?id=",S.sessionid,">",S.sessionid,"</a>") as "Session<BR>ID",
    S.title as Title,
    PSI.badgeid as BadgeId,
    P.pubsname as Pubsname
  FROM
      Sessions S
    JOIN Tracks T ON S.trackid=T.trackid
    LEFT JOIN ParticipantSessionInterest PSI ON S.sessionid=PSI.sessionid
    LEFT JOIN Participants P on PSI.badgeid=P.badgeid
  WHERE
    T.selfselect=1 and
    S.invitedguest=1 and
    statusid=2
  ORDER BY
    T.display_order,
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('allroomschedtrackroom','Full Room Schedule by track then room then time','<P>Lists all Sessions Scheduled in all Rooms.</P>
','','SELECT
    Trackname,
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname,
    Function,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    group_concat(\' \',pubsname,\' (\',P.badgeid,\')\') as \'Participants\'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
  GROUP BY
    SCH.scheduleid
  ORDER BY
    T.trackname,
    R.roomname,
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictschedassn','Conflict Report - Assigned v. Scheduled issue','<P>These are sessions that are either in the grid and have no one assigned or the have people assigned and are not in the grid.</P>
','','SELECT
    trackname,
    typename,
    divisionname,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid, 
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',title,\'</a>\') Title,
    if ((SCH.sessionid is NULL), \'no room\', concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\')) in_grid, 
    if ((num_assigned is NULL), 0, num_assigned) as num_assigned,
    if ((num_int is NULL), 0, num_int) as num_int
  FROM
      Tracks T, 
      Types Y,
      Divisions D,
      Sessions S 
    LEFT JOIN Schedule SCH on S.sessionid=SCH.sessionid 
    LEFT JOIN (SELECT
                   sessionid, 
                   count(badgeid) as num_assigned 
                 FROM
                     ParticipantOnSession 
                 GROUP BY
                     sessionid) A on A.sessionid=S.sessionid 
    LEFT JOIN (SELECT
                   sessionid,
                   count(badgeid) as num_int 
                 FROM
                     ParticipantSessionInterest
                 GROUP BY
                     sessionid) B on B.sessionid=S.sessionid
    LEFT JOIN Rooms R on R.roomid=SCH.roomid 
  WHERE
    T.trackid=S.trackid and
    Y.typeid=S.typeid and
    D.divisionid=S.divisionid
  HAVING 
    (in_grid=\'no room\' and num_assigned>0) or
    (in_grid!=\'no room\' and (num_assigned<1 or num_assigned is NULL))
  ORDER BY
    num_assigned DESC,
    in_grid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictpartdup','Conflict Report - Participant Double Booked','<P>Find all instances where a participant is scheduled to be in two or more places at once.</P>
','<P>Click on the session id to edit the volunteer or announcer.</P>','SELECT
    concat(P.pubsname, \'(\', P.badgeid, \')\') as Participant,
    \' \',
    TA.trackname as \'Track A\', 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',RA.roomid,\'>\', RA.roomname,\'</a>\') as \'Room A\', 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',Asess,\'>\', Asess,\'</a>\') as \'Session ID A\', 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Astart),\'%a %l:%i %p\') as \'Start Time A\', 
    left(Adur,5) as \'Dur A\',
    \' \',
    TB.trackname as \'Track B\', 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',RB.roomid,\'>\', RB.roomname,\'</a>\') as \'Room B\', 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',Bsess,\'>\', Bsess,\'</a>\') as \'Session ID B\', 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Bstart),\'%a %l:%i %p\') as \'Start Time B\',
    left(Bdur,5) as \'Dur B\'
  FROM
      Rooms RA, 
      Rooms RB, 
      Tracks TA, 
      Tracks TB, 
      Participants P,
      (SELECT
          POSA.badgeid, 
          SCHA.roomid AS Aroom, 
          SCHA.sessionid AS Asess, 
          SCHA.starttime AS Astart, 
          ADDTIME(SCHA.starttime, SA.duration) AS Aend, 
          SA.trackid AS Atrack, 
	  SA.duration AS Adur,
          SCHB.sessionid AS Bsess, 
          SCHB.roomid AS Broom, 
          SCHB.starttime AS Bstart, 
          ADDTIME(SCHB.starttime, SB.duration) AS Bend, 
          SB.trackid AS Btrack,
	  SB.duration AS Bdur
        FROM ParticipantOnSession POSA, 
            ParticipantOnSession POSB, 
            Schedule SCHA, 
            Schedule SCHB, 
            Sessions SA, 
            Sessions SB 
        WHERE
          POSA.sessionid = SA.sessionid and
          SCHA.sessionid=POSA.sessionid and
          POSB.sessionid = SB.sessionid and
          SCHB.sessionid=POSB.sessionid and
          POSA.badgeid=POSB.badgeid and
          (SCHA.starttime<SCHB.starttime or 
           (SCHA.starttime=SCHB.starttime and 
            SCHA.sessionid<SCHB.sessionid)) and
          ADDTIME(SCHA.starttime, SA.duration)>SCHB.starttime and
          POSA.sessionid<>POSB.sessionid) as Foo 
  WHERE
    Aroom=RA.roomid and
    P.badgeid=Foo.badgeid and
    Broom=RB.roomid and
    TA.trackid=Atrack and
    TB.trackid=Btrack
  ORDER BY
    cast(P.badgeid as unsigned), 
    Astart');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('prognamesannvolprog','Program Participant Number, Names, Contact, and Involvement','<P>Full listing of the names and contact, and how many classes, as a Program Participant, Announcer or Volunteer they are involved in.  Replaces the non-csv version of 4progthankyounotereport.</P>
','','SELECT
    P.badgeid, 
    P.pubsname, 
    C.firstname, 
    C.lastname, 
    C.email, 
    SCH.sessioncount as \'Total involvement\', 
    SCH.volcount as \'Volunteer Sessions\',
    SCH.anncount as \'Announcer Sessions\',
    (SCH.sessioncount-SCH.volcount-SCH.anncount) as \'Program Sessions\' 
  FROM
      CongoDump as C, 
      Participants as P 
    LEFT JOIN (SELECT
                   POS1.badgeid as badgeid , 
                   count(SCH1.sessionid) as sessioncount,
                   sum(if(volunteer=1,1,0)) as volcount,
                   sum(if(announcer=1,1,0)) as anncount
                 FROM
                     ParticipantOnSession POS1, 
                     Schedule SCH1, 
                     Sessions S, 
                     Tracks T 
               WHERE
                 POS1.sessionid=SCH1.sessionid and
                 SCH1.sessionid=S.sessionid and
                 S.trackid=T.trackid 
               GROUP BY
                 POS1.badgeid) as SCH on P.badgeid=SCH.badgeid 
  WHERE 
    SCH.sessioncount is not NULL and
    C.badgeid=P.badgeid 
  GROUP BY
    (P.badgeid) 
  ORDER BY
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('gohintvschedulepanel','Interest v Schedule - sorted by GoHs','<P>For each GoH, show which panels (but not Events) they are interested in,  and if they are assigned to it.  Also show the scheduling information.</P>
','','SELECT 
    concat(X.pubsname, \' (\', X.badgeid, \')\') as Pubsname,
    X.trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',X.sessionid,\'>\', X.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',X.sessionid,\'>\',X.title,\'</a>\') as Title,
    (if (X.assigned is NULL,\' \',\'yes\')) as \'Assigned?\', (if (moderator is NULL or moderator=0,\' \',\'yes\')) as \'Moderator?\', concat(\'<a href=MaintainRoomSched.php?selroom=\',Y.roomid,\'>\', Y.roomname,\'</a>\') as Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Y.starttime),\'%a %l:%i %p\') as \'Start Time\' 
  FROM 
      (SELECT
           PI.badgeid,
           PI.pubsname,
           PI.sessionid,
           POS.sessionid as assigned,
           moderator,
           title,
           trackname 
         FROM
             (SELECT
                  T.trackname,
                  S.title,
                  S.sessionid,
                  P.badgeid,
                  P.pubsname 
               FROM
                   Tracks T,
                   ParticipantSessionInterest PSI, 
                   Participants P,
                   Sessions S
               WHERE
                 S.trackid=T.trackid and
                 P.interested=1 and
		 P.badgeid in $GohBadgeList and
                 P.badgeid=PSI.badgeid and 
                 S.sessionid=PSI.sessionid) PI 
           LEFT JOIN ParticipantOnSession POS on POS.badgeid=PI.badgeid and POS.sessionid=PI.sessionid) X 
    LEFT JOIN (SELECT
                   SCH.starttime,
                   R.roomname,
                   R.roomid,
                   SCH.sessionid 
                FROM
                    Schedule SCH,
                    Rooms R 
                WHERE
                  R.roomid=SCH.roomid) as Y on X.sessionid=Y.sessionid
  ORDER BY
    badgeid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('arisiatvroomsched','Arisia TV by time.','<P>Just things in TV room.</P>
','','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    trackname, 
    S.sessionid,
    title,
    group_concat(\' \',pubsname,\' (\',P.badgeid,\')\') as \'Participants\'
  FROM
      Tracks T,
      Rooms R,
      Sessions S,
      Schedule SCH
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid = POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid = P.badgeid
  WHERE
    T.trackid = S.trackid and
    SCH.roomid = R.roomid and
    SCH.sessionid = S.sessionid and
    roomname in (\'ArisiaTV\')
  GROUP BY
    SCH.sessionid
  ORDER BY
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('allroomschedtrack','Full Room Schedule by track then time.','<P>Lists all Sessions Scheduled in all Rooms.</P>
','','SELECT
    Trackname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname,
    Function,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    group_concat(\' \',pubsname,\' (\',P.badgeid,\')\') as \'Participants\'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
  GROUP BY
    SCH.scheduleid
  ORDER BY 
    T.trackname,
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('gohschedule','GoH Schedule','<P>The GoH schedules.</P>
','','SELECT 
    G.pubsname, 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS duration,
      trackname, 
      concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid, 
      concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',title,\'</a>\') title,
      if ((S.pubstatusid=1), \'S-O\', if((S.pubstatusid=3), \'DNP\', \' \')) as \'Pubs Status\',
      if ((moderator=1), \'Yes\', \' \') as \'moderator\'
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
    JOIN Participants G ON G.badgeid = POS.badgeid
  WHERE
    G.badgeid in $GohBadgeList
  ORDER BY
    G.pubsname,
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('participantinterests','Participant Interests','<P>What is that participant interested in?</P>
','','SELECT
    P.badgeid,
    P.pubsname,
    yespanels as "New Panel Ideas",
    nopanels as "Panel Not Interested",
    yespeople,
    nopeople,
    otherroles 
  FROM
      ParticipantInterests PI, 
      Participants P
  WHERE
    P.badgeid=PI.badgeid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessioninterest','Session Interest Report (all info)','<P>Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report.  (All data included including for invited sessions.)</P>
','','SELECT
    T.trackname as \'Track\',
    CONCAT(\'<A HREF=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</A>\') as Sessionid,
    CONCAT(\'<A HREF=EditSession.php?id=\',S.sessionid,\'>\',title,\'</A>\') as title,
    P.pubsname,
    P.badgeid as \'BadgeID\',
    PSI.rank as \'Rank\',
    PSI.willmoderate as \'Mod?\',
    PSI.comments as \'Comments\'
  FROM
      Participants P,
      ParticipantSessionInterest PSI,
      Sessions S,
      Tracks T
  WHERE
    P.badgeid=PSI.badgeid AND
    S.sessionid=PSI.sessionid AND
    T.trackid=S.trackid
  ORDER BY
    T.trackname,
    title');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('assignedsessionbypart','Assigned Session by Participant','<P>Shows who has been assigned to each session ordered by badgeid.</P>
','','SELECT
    P.Badgeid, 
    P.Pubsname, 
    if ((moderator=1), \'Yes\', \' \') as \'Moderator\',
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',Sessions.sessionid,\'>\', Sessions.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',Sessions.sessionid,\'>\',Sessions.title,\'</a>\') Title
  FROM
      ParticipantOnSession, 
      Sessions, 
      Participants P
  WHERE
    ParticipantOnSession.badgeid=P.badgeid and
    ParticipantOnSession.sessionid=Sessions.sessionid 
  ORDER BY
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('pubs','Report for Pubs','<P>Report for the Pocket Program.</P>
','','SELECT
    S.sessionid, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a\') as Day, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%l:%i %p\') as \'Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    roomname, 
    trackname as TRACK, 
    typename as TYPE,
    K.kidscatname,
    title, 
    progguiddesc as \'Long Text\', 
    group_concat(\' \',pubsname, if (moderator=1,\' (m)\',\'\')) as \'PARTIC\' 
  FROM 
      Rooms R, 
      KidsCategories K,
      Sessions S, 
      Tracks T,
      Types Ty, 
      Schedule SCH 
    LEFT JOIN ParticipantOnSession POS on SCH.sessionid=POS.sessionid 
    LEFT JOIN Participants P on POS.badgeid=P.badgeid
  WHERE
    R.roomid = SCH.roomid and
    K.kidscatid=S.kidscatid and
    SCH.sessionid = S.sessionid and
    T.trackid=S.trackid and
    S.typeid=Ty.typeid and
    S.pubstatusid = 2
  GROUP BY
    SCH.sessionid 
  ORDER BY
    SCH.starttime, 
    R.roomname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('pubssched','Schedule report for Pubs','<P>Lists all Sessions Scheduled in all Rooms.</P>
','<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>
','SELECT
    S.sessionid,
    R.roomname, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',SCH.starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE 
      WHEN HOUR(duration) < 1 THEN 
        concat(date_format(duration,\'%i\'),\'min\') 
      WHEN MINUTE(duration)=0 THEN 
        concat(date_format(duration,\'%k\'),\'hr\') 
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    T.trackname,
    S.title, 
    PS.pubstatusname
  FROM 
      Sessions S
    JOIN Schedule SCH using (sessionid)
    JOIN Rooms R using (roomid)
    JOIN Tracks T using (trackid)
    JOIN PubStatuses PS using (pubstatusid) 
  ORDER BY
    SCH.starttime, 
    R.roomname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('assignedsessionbypartdiff','Differential Assigned Session by Participant','<P>Recent changes to whom has been assigned to each session ordered by time, then badgeid.</P>
','','SELECT
    P.badgeid, 
    P.pubsname, 
    Sessions.sessionid as SessionId, 
    title, 
    if ((moderator=1), \'Yes\', \' \') as \'moderator\',
    POS.ts as changed
  FROM
      ParticipantOnSession POS, 
      Sessions, 
      Participants P 
  WHERE
    POS.badgeid=P.badgeid and
    POS.sessionid=Sessions.sessionid and
    POS.ts>\'2009-1-7 13:50:00\'
  ORDER BY
    POS.ts,
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictpartnums','Conflict Report - Participant Number of Sessions','<P>Compare number of sessions participants requested with the number of which they were assigned.</P>
','','SELECT
    PA.badgeid, 
    P.pubsname, 
    if ((fridaymaxprog is NULL), \'<div style=background:lightgray\;>-</div>\',concat(\'<div style=background:lightgray\;>\',fridaymaxprog,\'</div>\')) as \'Fri Reqd.\', 
    if ((frisched is NULL), \'-\', frisched) as \'Fri Asgnd.\', 
    if ((saturdaymaxprog is NULL), \'<div style=background:lightgray\;>-</div>\',concat(\'<div style=background:lightgray\;>\',saturdaymaxprog,\'</div>\')) as \'Sat Reqd.\', 
    if ((satsched is NULL), \'-\', satsched) as \'Sat Asgnd.\', 
    if ((sundaymaxprog is NULL), \'<div style=background:lightgray\;>-</div>\',concat(\'<div style=background:lightgray\;>\',sundaymaxprog,\'</div>\')) as \'Sun Reqd.\', 
    if ((sunsched is NULL), \'-\', sunsched) as \'Sun Asgnd.\', 
    if ((mondaymaxprog is NULL), \'<div style=background:lightgray\;>-</div>\',concat(\'<div style=background:lightgray\;>\',mondaymaxprog,\'</div>\')) as \'Mon Reqd.\', 
    if ((monsched is NULL), \'-\', monsched) as \'Mon Asgnd.\', 
    if ((maxprog is NULL), \'<div style=background:lightgray\;>-</div>\',concat(\'<div style=background:lightgray\;>\',maxprog,\'</div>\')) as \'Total Reqd.\', 
    if ((totsched is NULL), \'-\', totsched) as \'Tot Asgnd.\'
  FROM
      ParticipantAvailability PA,  
      Participants P 
    LEFT JOIN (SELECT
                   badgeid, 
                   sum(if(starttime<\'24:00:00\',1,0)) as frisched, 
                   sum(if((starttime>=\'24:00:00\' && starttime<\'48:00:00\'),1,0)) as satsched, 
                   sum(if((starttime>=\'48:00:00\' && starttime<\'72:00:00\'),1,0)) as sunsched, 
                   sum(if(starttime>=\'72:00:00\',1,0)) as monsched, 
                   count(*) as totsched 
                 FROM
                     (SELECT
                          POS.badgeid, 
                          POS.sessionid, 
                          SCH.starttime 
                        FROM
                            ParticipantOnSession POS, 
                            Schedule SCH 
                        WHERE
                          POS.sessionid=SCH.sessionid) as FOO
                 GROUP BY
                   badgeid) as BAR on P.badgeid=BAR.badgeid 
    LEFT JOIN (SELECT 
                   badgeid,
                   sum(if(day=1,maxprog,0)) as fridaymaxprog,
                   sum(if(day=2,maxprog,0)) as saturdaymaxprog,
                   sum(if(day=3,maxprog,0)) as sundaymaxprog,
                   sum(if(day=4,maxprog,0)) as mondaymaxprog
                 FROM
                     ParticipantAvailabilityDays
                 GROUP BY
                     badgeid) as PAD on P.badgeid = PAD.badgeid
  WHERE 
    PA.badgeid = P.badgeid and
    P.interested=1 
  ORDER BY
    cast(PA.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('allroomsched','Full Room Schedule by room then time.','<P>Lists all Sessions Scheduled in all Rooms.</P>
','','SELECT 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname,
    Function, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    Trackname,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    group_concat(\' \',P.pubsname,\' (\',P.badgeid,\')\') as \'Participants\' 
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
  GROUP BY
    SCH.scheduleid 
  ORDER BY 
    R.roomname, 
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessionnotes','Session Notes','<P>Interesting info on a Session for sessions whose status is one of EditMe, Brainstorm, Vetted, Assigned, or Scheduled.</P>
','','SELECT
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as \'Session<BR>id\',
    Trackname, 
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    if (invitedguest,\'yes\',\'no\') as \'Invited Guest?\',
    servicenotes as \'Hotel and Tech notes\', 
    notesforprog as \'Notes for Programming\' 
  FROM
      Tracks T, 
      Sessions S, 
      SessionStatuses SS 
  WHERE
    T.trackid=S.trackid and
    SS.statusid=S.statusid and
    SS.statusname in (\'EditMe\', \'Brainstorm\', \'Vetted\', \'Assigned\', \'Scheduled\') and
    (invitedguest=1 or notesforprog is not NULL or servicenotes is not NULL)
  ORDER BY
   S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictsessdup','Conflict Report - Duplicate Session','<P>Lists all sessions scheduled more than once.</P>
','','SELECT
    S.Sessionid, 
    S.Title, 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', 
    R.roomname,\'</a>\') as Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',SCH.starttime),\'%a %l:%i %p\') as \'Start Time\' 
  FROM
      Sessions S, 
      Rooms R, 
      Schedule SCH, 
      (SELECT
           sessionid, 
           count(*) as mycount 
         FROM
             Schedule 
         GROUP BY
             sessionid 
         HAVING
             mycount>1) X 
  WHERE
    S.sessionid=X.sessionid and
    S.sessionid=SCH.sessionid and
    R.roomid = SCH.roomid
  ORDER BY
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('pubsnew','Pubs - Session Characteristics plus long description','<P>For Scheduled items ONLY. Show sessionid, track, type, divisionid, pubstatusid, pubno, pubchardest, kids, title, long description.</P>
','<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>
','SELECT
    S.sessionid, 
    trackname, 
    typename, 
    divisionname, 
    pubcharname, 
    kidscatname, 
    title, 
    roomname, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime), \'%a %l:%i %p\') as \'Start Time\', 
    CASE 
      WHEN HOUR(duration) < 1 THEN 
        concat(date_format(duration,\'%i\'),\'min\') 
      WHEN MINUTE(duration)=0 THEN 
        concat(date_format(duration,\'%k\'),\'hr\') 
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END as duration,
    if(group_concat(\' \',pubsname) is NULL,\'\',group_concat(\' \',pubsname)) as \'Participants\' ,
    progguiddesc as \'Long Description\'
  FROM
      Tracks T, 
      Types Ty, 
      Divisions D, 
      PubStatuses PS, 
      KidsCategories K, 
      Rooms R, 
      Sessions S 
    LEFT JOIN SessionHasPubChar SHPC ON S.sessionid=SHPC.sessionid 
    LEFT JOIN PubCharacteristics PC ON SHPC.pubcharid=PC.pubcharid, 
      Schedule SCH 
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
    LEFT JOIN CongoDump C ON POS.badgeid=C.badgeid 
    LEFT JOIN Participants P ON C.badgeid=P.badgeid
  WHERE 
    S.trackid=T.trackid and
    S.typeid=Ty.typeid and
    S.divisionid=D.divisionid and
    S.pubstatusid=PS.pubstatusid and
    PS.pubstatusname = \'Public\' and
    S.kidscatid=K.kidscatid and
    S.sessionid=SCH.sessionid and
    R.roomid=SCH.roomid
  GROUP BY
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessionedit','Session Edit History Report','<P>Show the most recent edit activity for each session (sorted by time).</P>
','','SELECT
    SEH2.timestamp as "When",
    concat("<a href=StaffAssignParticipants.php?selsess=",S.sessionid,">", S.sessionid,"</a>") as Sessionid,
    T.trackname as "Track", 
    concat("<a href=EditSession.php?id=",S.sessionid,">",S.title,"</a>") Title,
    SS.statusname as "Current<BR>Status", 
    concat(SEH1.name," (",SEH1.email_address,")") as "Who", 
    SEC.description as "What"
  FROM
      Sessions S, 
      Tracks T, 
      SessionStatuses SS, 
      SessionEditHistory SEH1, 
      SessionEditCodes SEC, 
      (SELECT
           SEH3.sessionid, 
           Max(SEH3.timestamp) as timestamp 
         FROM
             SessionEditHistory SEH3 
         GROUP BY
           SEH3.sessionid) SEH2 
  WHERE
    S.trackid=T.trackid and
    S.sessionid = SEH1.sessionid and
    S.sessionid = SEH2.sessionid and
    SEH1.timestamp = SEH2.timestamp and
    S.statusid = SS.statusid and
    SEH1.sessioneditcode = SEC.sessioneditcode and
    S.statusid >= 1 and
    S.statusid <= 7 
  ORDER BY
    SEH2.timestamp Desc');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('intvschedpanelnames','Interest v Schedule - sorted by pubsname','<P>Show who is interested in each panel and if they are assigned to it.  Also show the scheduling information (sorted by pubsname).</P>
','','SELECT 
    concat(X.pubsname, \'(\', X.badgeid, \')\') as Pubsname,
    X.trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',X.sessionid,\'>\', X.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',X.sessionid,\'>\',X.title,\'</a>\') Title,
    X.rank as Rank,
    (if (X.assigned is NULL,\' \',\'yes\')) as \'Asgn?\', 
    (if (moderator is NULL or moderator=0,\' \',\'yes\')) as \'Mod?\', 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',Y.roomid,\'>\', Y.roomname,\'</a>\') as Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Y.starttime),\'%a %l:%i %p\') as \'Start Time\' 
    FROM 
        (SELECT
             PI.badgeid,
             PI.pubsname,
             PI.sessionid,
             POS.sessionid as assigned,
             moderator,
             title,
             trackname,
             rank
           FROM
               (SELECT
                    T.trackname,
                    S.title,
                    S.sessionid,
                    P.badgeid,
                    P.pubsname,
                    PSI.rank
                 FROM
                     Tracks T,
                     ParticipantSessionInterest PSI, 
                     Participants P,
                     Sessions S
                 WHERE
                   S.trackid=T.trackid and
                   P.interested=1 and
                   P.badgeid=PSI.badgeid and
                   S.sessionid=PSI.sessionid ) PI 
             LEFT JOIN ParticipantOnSession POS on POS.badgeid=PI.badgeid and POS.sessionid=PI.sessionid) X 
      LEFT JOIN (SELECT
                     SCH.starttime,
		     R.roomname,
		     R.roomid,
		     SCH.sessionid 
                   FROM
                       Schedule SCH,
                       Rooms R 
                   WHERE
                     R.roomid=SCH.roomid) as Y on X.sessionid=Y.sessionid 
  ORDER BY
    substring_index(pubsname,\' \',-1),
    pubsname,
    trackname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictnomod','Conflict Report - Sessions with no moderator','<P>Panels need a moderator.  Other activities may not.  Think before you jump.  (This is limited to items in the schedule which have at least one participant.)</P>
','<P>Click on the session id to edit the moderator for the session.</P>
','SELECT
    typename as \'Type\',
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>(\', S.sessionid,\')</a> \',title) as \'Title\'
  FROM
      Sessions S 
    JOIN 
      Types T USING (typeid) 
    LEFT JOIN
      (SELECT
           sessionid,                
           count(*) as parts,
           sum(if(moderator=1,1,0)) as mods
         FROM ParticipantOnSession
         GROUP BY sessionid) X USING (sessionid)
  WHERE 
    X.parts>0 AND
    X.mods=0
  ORDER BY
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('prelimschedbrief','Preliminary Schedule','<P>Preliminary panel schedule.</P>
','<P>Please keep in mind that is it still changing as
','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',  
    trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title
  FROM
      Sessions S, 
      Schedule SCH, 
      Tracks T 
  WHERE
    T.trackid=S.trackid and
    SCH.sessionid = S.sessionid and
    trackname not in $removed_tracks
  ORDER BY
    T.trackname, 
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('thsessionservices','Session Services','<P>Which Session needs which Services? (Sorted by room then time.)</P>
','','SELECT
    Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    Trackname, 
    X.Sessionid,
    concat(\'<a href=EditSession.php?id=\',X.sessionid,\'>\',X.title,\'</a>\') Title,
    X.Servicename 
  FROM
      (SELECT
           trackname, 
           S.sessionid, 
           title,
           duration,
           servicename 
        FROM
            Tracks T, 
            Sessions S, 
            SessionHasService SF, 
            Services F 
        WHERE
          T.trackid=S.trackid and
          S.sessionid=SF.sessionid and
          F.serviceid=SF.serviceid) X, 
      Rooms R, 
      Schedule SCH
  WHERE
    X.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid 
  ORDER BY
    roomname, 
    starttime;');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictnotattending','Conflict Report - not attending people that are on panels.','<P>If the interested field is set to 2, pull them off the panel.  If the interested field is set otherwise, escalate to a div-head.</P>
','','SELECT
    P.badgeid, 
    P.pubsname, 
    S.sessionid, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as sessionid,
    P.interested 
  FROM
      Sessions S, 
      Schedule SCH, 
      Participants P, 
      ParticipantOnSession POS 
  WHERE
    P.badgeid=POS.badgeid and
    SCH.sessionid=S.sessionid and
    SCH.sessionid=POS.sessionid and
    P.interested!=1
  ORDER BY
    P.badgeid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('progpanelmerge','Full Participant Schedule for the Program Packet Merge','<P>sessionid, room, starttime, duration, (badgeid, pubsname, mod)</P>
','','SELECT
    POS.sessionid, 
    roomname, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') starttime, 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    trackname, 
    title, 
    group_concat( pubsname, \' MAKEMEACOMMA \', if(moderator=1,\'M\',\'\'), \' MAKEMEACOMMA \' order by moderator, pubsname) panelinfo,
    pubstatusname
  FROM 
      Rooms R, 
      Sessions S, 
      Schedule SCH 
    LEFT JOIN ParticipantOnSession POS on SCH.sessionid=POS.sessionid
    LEFT JOIN CongoDump C on C.badgeid=POS.badgeid
    LEFT JOIN Participants P on P.badgeid=POS.badgeid,
      Tracks T,
      PubStatuses PUB
  WHERE
    S.sessionid=SCH.sessionid and
    POS.sessionid=S.sessionid and
    T.trackid=S.trackid and
    SCH.roomid = R.roomid and
    SCH.sessionid = S.sessionid and
    PUB.pubstatusid = S.pubstatusid
  GROUP BY
    POS.sessionid 
  ORDER BY
    pubsname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictnoannvol','Conflict Report - Sessions with no volunteer or announcer','<P>Classes and Panels need a volunteer and announcer.  Others may not.  Think before you jump.</P>
','<P>Click on the session id to edit the volunteer or announcer.</P>
','SELECT
    typename as \'Type\',
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>(\', S.sessionid,\')</a> \',title) as \'Title\', 
    concat(DATE_FORMAT(ADDTIME(\'$ConStartDatim\',SCH.starttime),\'%a %l:%i %p\')) as \'StartTime\',
    GROUP_CONCAT(DISTINCT if((POS.volunteer=1),P.pubsname,\'\') SEPARATOR \' \') as \'Volunteer\', 
    GROUP_CONCAT(DISTINCT if((POS.announcer=1),P.pubsname,\'\') SEPARATOR \'\') as \'Announcer\' 
  FROM
      Sessions S 
    LEFT JOIN
      ParticipantOnSession POS on S.sessionid=POS.sessionid, 
      Types T, 
      Participants P,
      Schedule SCH
  WHERE 
    S.sessionid=SCH.sessionid and
    T.typeid=S.typeid AND 
    P.badgeid=POS.badgeid AND
    S.statusid=3
  GROUP BY
    S.sessionid
  ORDER BY
    typename,
    SCH.starttime,
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('congoinfo','Congo Info (all info).','<P>Shows the information retreived from Congo.</P>
','','SELECT
    badgename,
    badgeid,
    regtype,
    lastname,
    firstname,
    phone,
    email,
    postaddress
  FROM
      CongoDump
  WHERE
    badgeid is not NULL
  ORDER BY
    badgename');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictfewassigned','Conflict Report - Sessions with under 4 people assigned','<P>Not all of these are actually conflict, you want to think about them.</P>
','','SELECT
    trackname,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    title,
    count(badgeid) assigned
  FROM
          Sessions S
      LEFT JOIN ParticipantOnSession POS on S.sessionid=POS.sessionid,
      Tracks T
  WHERE
    T.trackid=S.trackid and
    S.statusid=3 AND
    POS.volunteer=0 AND
    POS.announcer=0
  GROUP BY
    sessionid having assigned<4
  ORDER BY
    trackname,
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('assignedsessioncounts','Assigned Session by Session (counts)','<P>How many people are assinged to each session? (Sorted by track then sessionid.)</P>
','','SELECT 
    Trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',Sessions.sessionid,\'>\', Sessions.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',Sessions.sessionid,\'>\',Sessions.title,\'</a>\') Title,
    Statusname, 
    count(badgeid) as NumAssigned 
  FROM
      ParticipantOnSession, 
      Sessions, 
      Tracks, 
      SessionStatuses  
  WHERE
    ParticipantOnSession.sessionid=Sessions.sessionid and
    Tracks.trackid=Sessions.trackid and
    Sessions.statusid=SessionStatuses.statusid
  GROUP BY
    sessionid 
  ORDER BY
    trackname,
    sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('assignedmoderator','Assigned Moderator by Session','<P>Shows who has been assigned to moderate each session (sorted by track then sessionid).</P>
','','SELECT
    Trackname,
    P.Pubsname, 
    P.Badgeid, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',title,\'</a>\') as Title,
    Statusname 
  FROM
      ParticipantOnSession, 
      Sessions S, 
      Participants P,
      Tracks, 
      SessionStatuses 
  WHERE
    ParticipantOnSession.badgeid=P.badgeid and
    ParticipantOnSession.sessionid=S.sessionid and
    Tracks.trackid=S.trackid and
    S.statusid=SessionStatuses.statusid and
    moderator=1 
  ORDER BY
    trackname, 
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('programcomment','Overall Program Commentary','<P>Comments recorded overall for Programming.  <A HREF=\"CommentOnProgramming.php\">(Add a comment)</A></P>
','','SELECT
    COP.commenter,
    COP.comment
  FROM
      CommentsOnProgramming COP');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('palm','CSV -- Report for uploading in PDA format','<P>StartTime, Duration, Room, Track, Title, Participants</P>
','','SELECT
            DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a\') AS Day,
            DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%l:%i %p\') AS \'Start Time\',
            left(duration,5) AS Duration,
	    roomname AS \'Room Name\',
            trackname as Track,
            Title,
            if(group_concat(pubsname) is NULL,\'\',group_concat(pubsname SEPARATOR \', \')) as Participants
    FROM
            Rooms R
       JOIN Schedule SCH USING (roomid)
       JOIN Sessions S USING (sessionid)
  LEFT JOIN Tracks T USING (trackid)
  LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
  LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE
            S.pubstatusid = 2
    GROUP BY
            SCH.sessionid
    ORDER BY
            SCH.starttime, R.roomname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictschedsched','Conflict Report - Schedule but not','<P>These are sessions that are either in the grid and not set as scheduled or they are set as scheduled and not in the grid.</P>
','','SELECT
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    if ((SCH.sessionid is NULL),\'no\',\'yes\') in_grid, 
    if(statusid=3,\'yes\',\'no\') status_sched 
  FROM
      Sessions S 
    LEFT JOIN Schedule SCH on S.sessionid=SCH.sessionid 
  HAVING
    (in_grid=\'no\' and status_sched=\'yes\') or
    (in_grid=\'yes\' and status_sched=\'no\')');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('allpartschedbyparttime','Full Participant Schedule by time','<P>The schedule sorted by participant.</P>
','','SELECT 
    if ((P.pubsname is NULL), \' \', concat(\' \',P.pubsname,\' (\',P.badgeid,\')\')) as \'Participants\', 
    if ((moderator=1),\'moderator\', \' \') as Moderator,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname,
    Function, 
    Trackname,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title
  FROM 
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid 
    LEFT JOIN Tracks T ON T.trackid=S.trackid 
  WHERE
    S.typeid not in (10, 12)
  ORDER BY
    cast(P.badgeid as unsigned),
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('thsessionfeature','Session Features','<P>Which Session needs which Features? (Sorted by room then time.)</P>
','','SELECT
    Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
      Trackname, 
      X.Sessionid,
      concat(\'<a href=EditSession.php?id=\',X.sessionid,\'>\',X.title,\'</a>\') Title,
      X.Featurename 
    FROM
        (SELECT
	     duration,
             trackname,
             S.sessionid,
             title,
             featurename 
           FROM
               Sessions S, 
               SessionHasFeature SF, 
               Features F, 
               Tracks T 
           WHERE
             T.trackid=S.trackid and
             S.sessionid=SF.sessionid and
             F.featureid=SF.featureid) X,
        Rooms R, 
        Schedule SCH 
  WHERE
    X.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid 
  ORDER BY
    roomname, 
    starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('kidfasttracksched','FastTrack Schedule (easy troubleshooting)','<P>What is happening in FastTrack.</P>
','','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',SCH.starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(S.duration) < 1 THEN
        concat(date_format(S.duration,\'%i\'),\'min\')
      WHEN MINUTE(S.duration)=0 THEN
        concat(date_format(S.duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(S.duration,\'%k\'),\'hr \',date_format(S.duration,\'%i\'),\'min\')
      END AS Duration,
    R.roomname,
    S.title,
    group_concat(concat(P.pubsname,\' (\',P.badgeid,\')\') SEPARATOR \', \') as \'Participants\'
  FROM
      Schedule SCH
    JOIN Rooms R USING(roomid)
    JOIN Sessions S USING(sessionid)
    JOIN Tracks TR USING(trackid)
    LEFT JOIN ParticipantOnSession POS USING(sessionid)
    LEFT JOIN Participants P USING(badgeid)
  WHERE
    TR.trackname=\'FAST TRACK\'
  GROUP BY
    SCH.scheduleid
  ORDER BY
    SCH.starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('participantroles','Participant Roles','<P>What Roles is a participant willing to take?</P>
','','SELECT
    P.badgeid,
    P.pubsname,
    rolename 
  FROM
      Participants P,
      ParticipantHasRole PR,
      Roles 
  WHERE
    P.badgeid=PR.badgeid and
    PR.roleid=Roles.roleid 
  ORDER BY
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('finalschedbreifdiff','Schedule - brief diff','<P>Recent changes to the PUBLIC Panel, Events, Film, Anime, Video and Arisia TV schedule.</P>
','<P>This has a hard-coded \"date\" $change_since_date from which it determines \"recent\".  This should probably be a vector value from today.','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    roomname, 
    trackname, 
    title,
    S.ts as changed
  FROM
      Sessions S, 
      Schedule SCH, 
      Tracks T, 
      Rooms R
  WHERE
    R.roomid=SCH.roomid and
    T.trackid=S.trackid and
    SCH.sessionid = S.sessionid and
    S.pubstatusid = 2 and
    S.ts > $change_since_date
  ORDER BY S.ts, 
    SCH.starttime,
    T.trackname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('allassigned','All Sessions that are assigned','<P>Who is assigned to what.</P>
','','SELECT
    trackname as Trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    group_concat(\' \',P.pubsname,\' (\',P.badgeid,\')\') as \'Participants\',
    GROUP_CONCAT(DISTINCT if((POS.moderator=1),P.pubsname,\'\') SEPARATOR \' \') as \'Moderator\', 
    GROUP_CONCAT(DISTINCT if((POS.volunteer=1),P.pubsname,\'\') SEPARATOR \' \') as \'Volunteer\', 
    GROUP_CONCAT(DISTINCT if((POS.announcer=1),P.pubsname,\'\') SEPARATOR \'\') as \'Announcer\' 
  FROM
      Sessions S, 
      Participants P, 
      ParticipantOnSession POS, 
      Tracks 
  WHERE
    P.badgeid=POS.badgeid AND
    POS.sessionid=S.sessionid AND
    Tracks.trackid=S.trackid 
  GROUP BY
    S.sessionid 
  ORDER BY
    Trackname,
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('participantnumpanel','Participant Number of Pannels and Constraints','<P>How many panels does each person want to be on and the other constraints they indicated.</P>
','','SELECT
    P.badgeid,
    P.pubsname,
    interested,
    Friday,
    Saturday,
    Sunday,
    Monday,
    maxprog,
    preventconflict,
    otherconstraints
  FROM
      ParticipantAvailability PA, Participants P
    LEFT JOIN (SELECT
	           badgeid,
	           sum(if(day=1,maxprog,0)) as Friday,
                   sum(if(day=2,maxprog,0)) as Saturday,
                   sum(if(day=3,maxprog,0)) as Sunday,
	           sum(if(day=4,maxprog,0)) as Monday
                 FROM
	             ParticipantAvailabilityDays
                 GROUP BY badgeid) PADQ ON P.badgeid = PADQ.badgeid
  WHERE
    P.badgeid=PA.badgeid
  ORDER BY
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictroomdup','Conflict Report - Room Schedule Overlaps.','<P>Find any pairs of sessions whose times overlap in the same room.</P>
','','SELECT
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname,
    SA.title as \'Title A\',
    Asess as \'Sessionid A\',
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Astart),\'%a %l:%i %p\') as \'Start Time A\',
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Aend),\'%a %l:%i %p\') as \'End Time A\',
    SB.title as \'Title B\',
    Bsess as \'Sessionid B\',
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Bstart),\'%a %l:%i %p\') as \'Start Time B\',
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',Bend),\'%a %l:%i %p\') as \'End Time B\'
  FROM
      Sessions SA,
      Sessions SB,
      Rooms R,
      (SELECT
           A.roomid,
           A.sessionid as Asess,
           A.starttime as Astart,
           ADDTIME(A.starttime, SA.duration) as Aend,
           B.sessionid as Bsess,
           B.starttime as Bstart,
           ADDTIME(B.starttime, SB.duration) as Bend
         FROM
             Schedule A,
             Schedule B,
             Sessions SA,
             Sessions SB
         WHERE
           A.roomid = B.roomid and
           A.starttime<=B.starttime and
           ADDTIME(A.starttime, SA.duration)>B.starttime and
           A.sessionid<>B.sessionid and
           A.sessionid=SA.sessionid and
           B.sessionid=SB.sessionid) as Foo
  WHERE
    Foo.roomid = R.roomid and
    Foo.Asess=SA.sessionid and
    Foo.Bsess=SB.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('participantcomment','Participant Commentary','<P>Comments recorded for Participants. <A HREF=\"CommentOnParticipants.php\">(Add a comment)</A></P>
','','SELECT
    P.pubsname,
    COP.commenter,
    COP.comment
  FROM
      Participants P 
    JOIN
      CommentsOnParticipants COP USING (badgeid)
  ORDER BY
    P.pubsname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('progannvol','Program Announcers and Volunteers Assigned hours','<P>Prefered name, firstname, lastname, mailing address, count of scheduled sessions.</P>
','','SELECT
    P.badgeid, 
    P.pubsname, 
    C.firstname, 
    C.lastname, 
    C.email, 
    SCH.sessioncount as \'Total involvement\', 
    SCH.volcount as \'Volunteer Sessions\',
    SCH.anncount as \'Announcer Sessions\',
    (SCH.sessioncount-SCH.volcount-SCH.anncount) as \'Program Sessions\' 
  FROM
      CongoDump as C,
      UserHasPermissionRole as UP,
      Participants as P 
    LEFT JOIN
      (SELECT
           POS1.badgeid as badgeid , 
           count(SCH1.sessionid) as sessioncount,
           sum(if(volunteer=1,1,0)) as volcount,
           sum(if(announcer=1,1,0)) as anncount
         FROM
             ParticipantOnSession POS1, 
             Schedule SCH1, 
             Sessions S, 
             Tracks T 
         WHERE
           POS1.sessionid=SCH1.sessionid and
           SCH1.sessionid=S.sessionid and
           S.trackid=T.trackid 
         GROUP BY
           POS1.badgeid) as SCH on P.badgeid=SCH.badgeid 
  WHERE 
    UP.permroleid=5 and
    C.badgeid=P.badgeid and
    C.badgeid=UP.badgeid
  GROUP BY
    (P.badgeid) 
  ORDER BY 
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('benpalm','Report for Ben and the palm.','<P>StartTime Duration Room Track Title Participants.</P>
','','SELECT
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a\') as \'Day\',
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%l:%i %p\') as \'Start Time\',
    concat(\'(\',left(duration,5),\')\') Length,
    Roomname,
    trackname as Track,
    Title,
    if(group_concat(pubsname) is NULL,\'\',group_concat(pubsname SEPARATOR \', \')) as \'Participants\'
  FROM
      Rooms R
    JOIN Schedule SCH USING (roomid)
    JOIN Sessions S USING (sessionid)
    LEFT JOIN Tracks T USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    S.pubstatusid = 2
  GROUP BY
    SCH.sessionid
  ORDER BY
    SCH.starttime,
    R.roomname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('sessionintassncounts','Assigned, Interested and Not-scheduled Report','<P>These are sessions that are in need of a home in the schedule.</P>
','','SELECT
    if ((num_int is NULL), 0, num_int) as Intr,
    if ((num_assigned is NULL), 0, num_assigned) as Assn,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid, 
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',title,\'</a>\') Title,
    trackname, 
    typename
  FROM
      Tracks T, 
      Types Y,
      Divisions D,
      Sessions S 
    LEFT JOIN Schedule SCH on S.sessionid=SCH.sessionid 
    LEFT JOIN (SELECT
                   sessionid, 
                   count(badgeid) as num_assigned 
                 FROM
	             ParticipantOnSession 
                 GROUP BY
                   sessionid) A on A.sessionid=S.sessionid 
    LEFT JOIN (SELECT
                   sessionid, count(badgeid) as num_int 
                 FROM
	             ParticipantSessionInterest
                 GROUP BY
                   sessionid) B on B.sessionid=S.sessionid
    LEFT JOIN Rooms R on R.roomid=SCH.roomid 
  WHERE
    T.trackid=S.trackid and
    Y.typeid=S.typeid and
    D.divisionid=S.divisionid and
    D.divisionname = \'Programming\' and
    SCH.sessionid is NULL
  HAVING
    Intr>=4
  ORDER BY
    Intr DESC,
    Assn DESC');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictnotreg','Conflict Report - Not Registered','<P>This is a report of participants sorted by number of panels they are on that are actually running, with some registration information.  It is useful for cons that comp program participants based on a minimum number of panels.  In this case, this report helps make sure people get their comps.  Also, participants who have not earned a comp may need some kind of consideration.</P>
','','SELECT
    P.badgeid, 
    P.pubsname, 
    if ((regtype is NULL), \' \', regtype) as \'regtype\', 
    if ((assigned is NULL), \'0\', assigned) as \'assigned\'
  FROM
      CongoDump C,
      Participants P 
    left join (SELECT
                   POS.badgeid, 
                   count(POS.sessionid) as assigned 
                 FROM
                     ParticipantOnSession POS,
                     Schedule S
                 WHERE
                   S.sessionid=POS.sessionid 
                 GROUP BY
                   badgeid) X on P.badgeid=X.badgeid 
  WHERE
    C.badgeid=P.badgeid and
    interested!=2 
  ORDER BY 
    regtype, 
    cast(assigned as unsigned) desc,
    substring_index(pubsname,\' \',-1)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictpartatime','Conflict Report - Participants Scheduled Outside Available Times','<P>Show all participant-sessions scheduled outside set of times participant has listed as being available.</P>
','','SELECT 
    FOO.badgeid, 
    P.pubsname, 
    TR.trackname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',FOO.sessionid,\'>\', FOO.sessionid,\'</a>\') as Sessionid, 
    concat(\'<a href=MaintainRoomSched.php?selroom=\',R.roomid,\'>\', R.roomname,\'</a>\') as Roomname, 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',FOO.starttime),\'%a %l:%i %p\') as \'Start Time\', 
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',FOO.endtime),\'%a %l:%i %p\') as \'End Time\', 
    FOO.hours as \'Ttl. Hours Avail.\' 
  FROM 
      (SELECT
           SCHD.badgeid, 
           SCHD.trackid, 
           SCHD.sessionid, 
           SCHD.starttime, 
           SCHD.endtime, 
           SCHD.roomid, 
           PAT.availabilitynum, 
           HRS.hours 
         FROM
             (SELECT
                  POS.badgeid, 
                  SCH.sessionid, 
                  SCH.starttime, 
	          SCH.roomid, 
	          ADDTIME(SCH.starttime,S.duration) as endtime, 
	          S.trackid 
                FROM
                    Schedule SCH, 
                    ParticipantOnSession POS, 
	            Sessions S 
                WHERE
                  SCH.sessionid = POS.sessionid and
                  SCH.sessionid = S.sessionid) as SCHD 
           LEFT JOIN ParticipantAvailabilityTimes PAT on SCHD.badgeid = PAT.badgeid and
             SCHD.starttime>=PAT.starttime and
             SCHD.endtime<=PAT.endtime 
           LEFT JOIN (SELECT
                          badgeid,
                          sum(hour(subtime(endtime,starttime))) as hours 
                        FROM
                            ParticipantAvailabilityTimes 
                        GROUP BY
		          badgeid) as HRS on SCHD.badgeid=HRS.badgeid 
                        HAVING
                          PAT.availabilitynum is null) as FOO, 
    Tracks TR, 
    Participants P, 
    Rooms R 
  WHERE
    FOO.badgeid = P.badgeid and
    FOO.trackid = TR.trackid and
    FOO.roomid = R.roomid 
  HAVING
    FOO.hours is not NULL 
  ORDER BY
    cast(FOO.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('participantsuggestions','Participant Suggestions','<P>What is did each participant suggest?</P>
','<P>This form originally had a index of \"DELETEME\" should it be removed?</P>','SELECT
    P.badgeid,
    P.pubsname,
    paneltopics,
    otherideas,
    suggestedguests 
  FROM
      ParticipantSuggestions as PS, 
      Participants as P
  WHERE
    P.badgeid=PS.badgeid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('gameroomshedtime','Gaming Schedule by time then room','<P>Just things in track gaming (gaming and gaming panels).</P>
','','SELECT
    roomname,
    function,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    trackname,
    S.sessionid,
    title,
    group_concat(\' \', pubsname,\' (\',P.badgeid,\')\') as \'Participants\'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    trackname in (\'Gaming\')
  GROUP BY
    SCH.scheduleid
  ORDER BY
    SCH.starttime,
    R.roomname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictmanyassigned','Conflict Report - Sessions with over 5 people assigned.','<P>Not all of these are actually conflict, you want to think about them.</P>
','','SELECT
    Trackname,
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',S.sessionid,\'>\', S.sessionid,\'</a>\') as Sessionid,
    Title,
    count(badgeid) as Assigned
  FROM
      Sessions S 
    LEFT JOIN ParticipantOnSession POS on S.sessionid=POS.sessionid,
      Tracks T 
  WHERE
    T.trackid=S.trackid and
    S.statusid=3
  GROUP BY
    sessionid HAVING Assigned>5
  ORDER BY
    trackname,
    S.sessionid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictunknownregtype','Conflict Report - Unknown RegTypes','<P>Congo RegTypes that Zambia does not recognize.</P>
','','SELECT
    distinct(C.regtype)
  FROM
      CongoDump C 
    LEFT JOIN RegTypes R on C.regtype=R.regtype
  WHERE
    R.regtype is NULL and
    C.regtype is not NULL
  ORDER BY
    C.Regtype');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('pubsbio','Pubs - Participant Bio and pubname','<P>Show the badgeid, lastname, firstname, badgename, pubsname and book edited bio for each participant who is on at least one scheduled session.</P>
','','SELECT
    concat('<A HREF=StaffEditCreateParticipant.php?action=edit&partid=',P.badgeid,'>',P.badgeid,'</A>') AS badgeid,
    CD.lastname,
    CD.firstname,
    CD.badgename,
    P.pubsname,
    concat('<A HREF=StaffEditBios.php?badgeid=',P.badgeid,'>',BBE.biotext,'</A>') AS 'Program Book Bio'
  FROM
      Participants P
    LEFT JOIN (SELECT 
                   badgeid,
                   biotext
                 FROM
                     $BioDB.Bios
                   JOIN $BioDB.BioTypes using (biotypeid)
                   JOIN $BioDB.BioStates using (biostateid)
                 WHERE
                   biolang in ('en-us') AND
                   biotypename in ('book') AND 
                   biostatename in ('edited')) BBE USING (badgeid)
    JOIN CongoDump CD USING (badgeid)
    JOIN (SELECT
	      DISTINCT(badgeid)
            FROM
	        ParticipantOnSession POS
              JOIN Schedule SCH USING (sessionid)
              JOIN Sessions S USING (sessionid)
              JOIN Types T USING (typeid)
            WHERE
              typename in ('Panel', 'Class', 'Presentation', 'Author Reading', 'Lounge', 'Performance') AND
	      POS.sessionid=SCH.sessionid AND
              POS.volunteer=0 AND
              POS.introducer=0 AND
              POS.aidedecamp=0) as X using (badgeid)
  ORDER BY
    IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,\' \',-1)),
    CD.firstname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('thsessiontechnotes','Session Tech and Hotel notes','<P>What notes are in on this panel for tech and hotel? (Sorted by room then time.)</P>
','','SELECT
    Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start&nbsp;Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    Trackname, 
    S.Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    Servicenotes 
  FROM
      Tracks T, 
      Sessions S, 
      Rooms R, 
      Schedule SCH 
  WHERE
    T.trackid=S.trackid and
    S.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid and
    S.servicenotes!=\' \' 
  ORDER BY
    Roomname, 
    Starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('name','Name Report','<P>Maps badgeid, pubsname, badgename and first and last name together (includes every record in the database regardless of status).</P>
','','SELECT
    C.badgeid,
    P.pubsname,
    C.badgename,
    C.lastname,
    C.firstname 
  FROM
      CongoDump C,
      Participants P 
  WHERE
    C.badgeid=P.badgeid and 
    P.badgeid is not NULL 
  ORDER BY
    IF(instr(P.pubsname,C.lastname)>0,C.lastname,substring_index(P.pubsname,\' \',-1)),
    C.firstname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('thsessionroomsets','Session roomsets','<P>What roomsets are we using (Sorted by Room then Time.)</P>
','','SELECT
    Roomname,
    DATE_FORMAT(ADDTIME(\'$ConStartDatim\',starttime),\'%a %l:%i %p\') as \'Start Time\', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    Trackname, 
    S.Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title,
    Roomsetname 
  FROM
      RoomSets RS, 
      Tracks T, 
      Sessions S, 
      Rooms R, 
      Schedule SCH 
  WHERE
    T.trackid=S.trackid and
    S.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid and
    RS.roomsetid=S.roomsetid 
  ORDER BY
    roomname, 
    starttime');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('kidcount','Participant Kid Count','<P>How many kids did the participants say they are bringing for Fast Track?</P>
','','SELECT
    P.Badgeid, 
    Pubsname, 
    Numkidsfasttrack  
  FROM
      Participants P, 
      ParticipantAvailability PA 
  WHERE
      P.badgeid=PA.badgeid
  ORDER BY
      Numkidsfasttrack DESC');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('staffmembers','Staff Members','<P>List Staff Members and their privileges.</P>
','','SELECT
      badgeid as Badgeid,
      if(P.pubsname is null or P.pubsname = \'\',concat(firstname,\' \',lastname),P.pubsname) as Name,
      if (password=\'4cb9c8a8048fd02294477fcb1a41191a\',\'changme\',\'OK\') as Password,
      group_concat(permrolename SEPARATOR \', \') as Privileges
    FROM
        Participants P
            JOIN CongoDump using (badgeid)
            JOIN UserHasPermissionRole using (badgeid)
            JOIN PermissionRoles using (permroleid)
    WHERE badgeid in 
          (SELECT DISTINCT badgeid FROM UserHasPermissionRole where permroleid=2)
    GROUP BY badgeid, name, password');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('namechanges','Name Report - Changes','<P>(in progress... try back in a bit). Do these folks want to update thier badgenames?   The pubsname and badgename do not match.   Report shows badgeid, pubsname, badgename, firstname and lastname.</P>
','','SELECT
    C.badgeid,
    P.pubsname,
    C.badgename,
    C.lastname,
    C.firstname 
  FROM
      CongoDump C,
      Participants P 
  WHERE
    C.badgeid=P.badgeid and
    P.badgeid is not NULL 
  ORDER BY
    P.pubsname and
    strcmp(C.badgename, P.pubsname) and
    C.badgename=P.pubsname,
    P.pubsname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('conflictnotinter','Conflict Report - people on panels they are not interested in','<P>This can happen two ways: Either someone used the feature at the bottom of the assign page to do this deliberately or the participant removed his or her interest after being assigned.</P>
','','SELECT
    POS.Badgeid, 
    P.Pubsname, 
    concat(\'<a href=StaffAssignParticipants.php?selsess=\',POS.sessionid,\'>\', POS.sessionid,\'</a>\') as Sessionid,
    concat(\'<a href=EditSession.php?id=\',S.sessionid,\'>\',S.title,\'</a>\') Title
  FROM
      Sessions S,
      Participants P, 
      ParticipantOnSession POS
    left join ParticipantSessionInterest PSI ON POS.badgeid=PSI.badgeid and POS.sessionid=PSI.sessionid 
  WHERE
    P.badgeid=POS.badgeid and
    POS.sessionid = S.sessionid and
    PSI.sessionid is NULL');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('attending','Attending Query (all info)','<P>Shows who has responded and if they are attending.  (Interested, 1=yes, 2=no, 0=did not pick, blank=did not hit save.)</P>
','','SELECT
    P.pubsname,
    P.badgeid,
    P.interested,
    P.bestway 
  FROM
      Participants P 
  ORDER BY
    P.pubsname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('progthankyounote','Program Participant Thank you note query','<P>prefered name, firstname, lastname, mailing address, count of scheduled sessions (for only some tracks!)</P>
','<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>
','SELECT
    P.badgeid, 
    P.pubsname, 
    C.firstname, 
    C.lastname, 
    C.email, 
    SCH.sessioncount as \'Total involvement\', 
    SCH.volcount as \'Volunteer Sessions\',
    SCH.anncount as \'Announcer Sessions\',
    (SCH.sessioncount-SCH.volcount-SCH.anncount) as \'Program Sessions\' 
  FROM
      CongoDump as C, 
      Participants as P 
    LEFT JOIN (SELECT
                   POS1.badgeid as badgeid , 
                   count(SCH1.sessionid) as sessioncount,
                   sum(if(volunteer=1,1,0)) as volcount,
                   sum(if(announcer=1,1,0)) as anncount
                 FROM
                     ParticipantOnSession POS1, 
                     Schedule SCH1, 
                     Sessions S, 
                     Tracks T 
               WHERE POS1.sessionid=SCH1.sessionid and
                     SCH1.sessionid=S.sessionid and
                     S.trackid=T.trackid
               GROUP BY
                     POS1.badgeid) as SCH on P.badgeid=SCH.badgeid 
  WHERE
    SCH.sessioncount is not NULL and
    C.badgeid=P.badgeid 
  GROUP BY
    (P.badgeid) 
  ORDER BY
    cast(P.badgeid as unsigned)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('partpicky','Participants With People to Avoid','<P>Show the badgeid, pubsname and list of people to avoid for each participant who indicated he is attending and listed people with whom he does not want to share a panel.</P>
','','SELECT
    PI.badgeid,
    P.pubsname,
    PI.nopeople
  FROM
      ParticipantInterests PI
    JOIN Participants P USING (badgeid)
  WHERE
    P.interested=1 and
    (nopeople is not null and nopeople!=\'\')
  ORDER BY
    substring_index(pubsname,\' \',-1)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('pubswhoisonwhich','Pubs - Who is on Which Session','<P>Show the badgeid, pubsname and session info for each participant that are on at least one scheduled session.</P>
','<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>
','SELECT
    P.badgeid, 
    P.pubsname, 
    s as Sessions 
  FROM
      (SELECT 
           distinct(badgeid), 
           group_concat(\' \',POS.sessionid, if (moderator=1,\' (m)\',\'\')) as s
         FROM
             ParticipantOnSession POS, 
             Schedule SCH 
         WHERE
             POS.sessionid=SCH.sessionid 
         GROUP BY
             badgeid) as X, 
      Participants P
  WHERE
    P.badgeid=X.badgeid');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('personalflow','Personal Flow Reports','<P>Here is a list of all the reports, that you have added to your personal flow, that are available to be generated during this phase.</P>
','<P><A HREF=EditPersonalFlows.php>Change</A> the ordering of this page.</P>
','SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",reportid,">",reporttitle,"</A> (<A HREF=genreport.php?reportid=",reportid,"&csv=y>csv</A>)") AS Title,
    reportdescription AS Description
  FROM
      PersonalFlow
      JOIN $ReportDB.Reports USING (reportid)
      LEFT JOIN Phases USING (phaseid)
  WHERE
    badgeid=$mybadgeid AND
    phaseid is null OR
    current = TRUE
  ORDER BY
    pfloworder');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('ViewAllSessions','View All Sessions','<P>Shows all sessions, regardless of their status.</P>
','','SELECT
    concat(\'<A HREF=StaffAssignParticipants.php?selsess=\',sessionid,\'>\',sessionid,\'</A>\') AS \'Sess.<BR>ID\',
    trackname AS Track,
    concat(\'<A HREF=EditSession.php?id=\',sessionid,\'>\',title,\'</A>\') AS Title,
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,\'%i\'),\'min\')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,\'%k\'),\'hr\')
      ELSE
        concat(date_format(duration,\'%k\'),\'hr \',date_format(duration,\'%i\'),\'min\')
      END AS Duration,
    estatten AS \'Est.<BR>Atten.\',
    statusname AS Status
  FROM
      Sessions
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses USING (statusid)
  WHERE
    statusname != \'Dropped\'
  ORDER BY
    trackname,
    statusname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('tasklistdisplay','Display Task List','<P>Display the Task List.</P>
','<P><A HREF="TaskListUpdate.php?activityid=-1">New</A> Task. (To update, click on the task name.)</P>
','SELECT
    CONCAT("<A HREF=TaskListUpdate.php?activityid=",activityid,">",activity,"</A>") as Tasks,
    activitynotes as Notes,
    pubsname as "Assigned",
    targettime as "Due Date",
    donestate "Complete?",
    donetime "Finshed On"
  FROM
      TaskList
    JOIN Participants USING (badgeid)');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('meetingagendadisplay','Display Agendas','<P>Display Meeting Agendas.</P>
','<P><A HREF="MeetingAgenda.php?agendaid=-1">New</A> Agenda. (To update, click on the Agenda name.)</P>
','SELECT
    CONCAT(permrolename,
      ":<br><A HREF=MeetingAgenda.php?agendaid=",
      agendaid,
      ">",
      agendaname,
      "</A><br>On: ",
      meetingtime,
      "<br><A HREF=MeetingAgendaPrint.php?agendaid=",
      agendaid,
      ">(print)</A>") as "Agenda Name",
    agenda as Agenda,
    agendanotes as "Agenda Notes"
  FROM
      AgendaList
    JOIN PermissionRoles using (permroleid)
  ORDER BY
    meetingtime, permrolename, agendaname');
INSERT INTO REPORTDB.Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ('emailtolist','Possible EmailTo Recipients','<P>This is a list of all the existing EmailTo Recipients</P>
','<P><A HREF="StaffEmailToUpdate.php?emailtoid=-1">New</A> EmailTo query. (To update, click on the name.)</P>
','SELECT
    CONCAT("<A HREF=StaffEmailToUpdate.php?emailtoid=",
      emailtoid,
      ">",
     emailtodescription,
     "</A>") AS Name,
    emailtoquery AS Query
  FROM
      EmailTo
  ORDER BY
    display_order');
INSERT INTO REPORTDB.GroupFlow (reportid, gflowname, gfloworder) VALUES (1,'Pubs',1),(1,'Prog',1),(2,'Conflict',1),(2,'Reg',1),(3,'Prog',2),(3,'Events',1),(4,'Prog',3),(5,'Prog',4),(6,'Pubs',2),(7,'Prog',5),(7,'Events',2),(8,'Prog',6),(8,'Conflict',2),(9,'Pubs',3),(10,'Prog',7),(11,'Conflict',3),(11,'Prog',8),(12,'Prog',9),(13,'Prog',10),(14,'Conflict',4),(15,'Pubs',4),(16,'Prog',11),(17,'Gaming',1),(18,'Fasttrack',1),(19,'Prog',12),(19,'Events',3),(19,'Goh',1),(20,'Prog',13),(20,'Events',4),(20,'Tech',1),(20,'Hotel',1),(21,'Prog',14),(22,'Conflict',5),(23,'Prog',15),(24,'Prog',16),(24,'Events',5),(24,'Goh',2),(25,'Conflict',6),(26,'Conflict',7),(27,'Prog',17),(28,'Goh',3),(28,'Prog',18),(29,'Arisiatv',1),(30,'Prog',19),(30,'Events',6),(30,'Goh',4),(31,'Goh',5),(31,'Prog',20),(32,'Prog',21),(33,'Prog',22),(34,'Prog',23),(34,'Events',7),(35,'Pubs',5),(36,'Pubs',6),(37,'Prog',24),(37,'Events',8),(37,'Goh',6),(37,'Pubs',7),(38,'Conflict',8),(38,'Reg',2),(39,'Prog',25),(39,'Events',9),(39,'Goh',7),(40,'Prog',26),(41,'Conflict',9),(42,'Pubs',8),(43,'Prog',27),(43,'Events',10),(44,'Prog',28),(45,'Conflict',10),(46,'Pubs',9),(46,'Prog',29),(47,'Prog',30),(47,'Events',11),(47,'Tech',2),(47,'Hotel',2),(48,'Conflict',11),(49,'Pubs',10),(50,'Conflict',12),(50,'Prog',31),(51,'Prog',32),(51,'Events',12),(51,'Reg',3),(52,'Conflict',13),(53,'Prog',33),(53,'Events',13),(54,'Prog',34),(54,'Events',14),(55,'Prog',35),(56,'Pubs',11),(57,'Conflict',14),(58,'Prog',36),(58,'Goh',8),(59,'Prog',37),(59,'Events',15),(59,'Tech',3),(59,'Hotel',3),(60,'Fasttrack',2),(61,'Prog',38),(62,'Pubs',12),(62,'Prog',39),(63,'Prog',40),(64,'Prog',41),(65,'Conflict',15),(66,'Prog',42),(67,'Prog',43),(68,'Pubs',13),(69,'Prog',44),(70,'Conflict',16),(70,'Reg',4),(71,'Conflict',17),(72,'Prog',45),(73,'Gaming',2),(73,'Prog',46),(74,'Conflict',18),(75,'Conflict',19),(75,'Reg',5),(76,'Pubs',14),(77,'Prog',47),(77,'Events',16),(77,'Tech',4),(77,'Hotel',4),(78,'Prog',48),(78,'Events',17),(78,'Reg',6),(79,'Prog',49),(79,'Events',18),(79,'Tech',5),(79,'Hotel',5),(80,'Fasttrack',3),(81,'Admin',1),(82,'Prog',50),(82,'Events',19),(82,'Reg',7),(83,'Conflict',20),(84,'Prog',51),(84,'Events',20),(84,'Pubs',15),(85,'Prog',52),(86,'Prog',53),(87,'Pubs',16),(88,'My',1),(89,'Prog',54),(90,'Prog',55),(91,'Prog',56),(91,'Events',21),(91,'Pubs',17)(92,'Admin',2);
