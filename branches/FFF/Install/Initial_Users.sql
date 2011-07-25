INSERT INTO CongoDump (badgeid,firstname,lastname,badgename) VALUES
(100,'Idea','Submissions','Idea Submissions'), 
(101,'First','Staffmember','First Staffmember'),
(102,'Test','Participant','Test Participant') ;
INSERT INTO Participants (badgeid,pubsname,password) VALUES
(100,'Idea Submission','c79bdf421714f5087fc34b7c538b6807'),
(101,'First Staffmember','4cb9c8a8048fd02294477fcb1a41191a'),
(102,'First Participant','4cb9c8a8048fd02294477fcb1a41191a') ;
INSERT INTO UserHasPermissionRole (badgeid,permroleid) VALUES
(100,4),(101,2),(101,4),(102,3);
INSERT INTO TaskList (badgeid,activity,activitynotes,donestate) VALUES
(101,'Make Tasklist','First thing to be started.','N');
