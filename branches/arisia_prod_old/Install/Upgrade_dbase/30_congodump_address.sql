## This script modifies CongoDump to support distinct address fields from Congo.
ALTER TABLE CongoDump
    CHANGE COLUMN postaddress postaddress1 VARCHAR(100),
    ADD COLUMN postaddress2 VARCHAR(100) AFTER postaddress1, 
    ADD COLUMN postcity VARCHAR(50) AFTER postaddress2, 
    ADD COLUMN poststate VARCHAR(25) AFTER postcity, 
    ADD COLUMN postzip VARCHAR(10) AFTER poststate, 
    ADD COLUMN postcountry VARCHAR(25) AFTER postzip; 
INSERT INTO PatchLog (patchname) VALUES ('30_congodump_address.sql');
