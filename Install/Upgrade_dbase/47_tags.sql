## This script renames Publication Characteristics to Tags throughout the schema
##
##	Created by Peter Olszowka on 2019-11-30;
## 	Copyright (c) 2019 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE SessionHasPubChar DROP FOREIGN KEY Fkey2;
RENAME TABLE SessionHasPubChar TO SessionHasTag;
ALTER TABLE SessionHasTag CHANGE pubcharid tagid INT NOT NULL DEFAULT 0;
RENAME TABLE PubCharacteristics TO Tags;
ALTER TABLE Tags CHANGE pubcharid tagid INT NOT NULL AUTO_INCREMENT;
ALTER TABLE Tags CHANGE pubcharname tagname VARCHAR(30) DEFAULT NULL;
ALTER TABLE Tags DROP COLUMN pubchartag;
ALTER TABLE SessionHasTag ADD CONSTRAINT Fkey2 FOREIGN KEY Fkey2 (tagid) REFERENCES Tags (tagid);

INSERT INTO PatchLog (patchname) VALUES ('47_tags.sql');
