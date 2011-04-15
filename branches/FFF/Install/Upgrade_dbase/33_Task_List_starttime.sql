## This script modifies CongoDump to support distinct address fields from Congo.
ALTER TABLE TaskList
    ADD COLUMN activitystart date default NULL AFTER activitynotes; 
INSERT INTO PatchLog (patchname) VALUES ('33_Task_List_starttime.sql');
