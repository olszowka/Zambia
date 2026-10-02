## This script makes the character set and collation of two groups of tables consistent with the rest of the schema
## (utf8mb4 / utf8mb4_general_ci, as in EmptyDbase.sql):
##   1) tables created by patches 58, 59, and 60 when those patches were run on MySQL rather than MariaDB, and
##   2) Tags.tagname, which used the older 3-byte utf8 (utf8mb3) character set.
##
## 1) Patches 58-60:
## Those patches create RoomHasSet (58), RegTypes (59), PhotoDenialReasons and PhotoUploadStatus (60) with
## "DEFAULT CHARSET=utf8mb4" but no COLLATE clause, so each server fills in its own default collation for utf8mb4:
## utf8mb4_general_ci on MariaDB (matching the rest of the schema), but utf8mb4_0900_ai_ci on MySQL 8 and later.
## On MySQL, any query that compares their text columns with other tables' -- e.g. the Admin Participants search's
## "LEFT JOIN RegTypes RT USING (regtype)" against CongoDump -- then fails with
## "Illegal mix of collations (utf8mb4_general_ci,IMPLICIT) and (utf8mb4_0900_ai_ci,IMPLICIT)".
##
## Converting them to utf8mb4_general_ci matches EmptyDbase.sql. It is safe to run on any installation, on either
## server: tables that already have the right collation are just rebuilt unchanged (superfluous, but harmless, on
## MariaDB). The foreign keys referencing PhotoDenialReasons and PhotoUploadStatus are on integer columns, so they
## are unaffected.
##
## 2) Tags.tagname carried an explicit column-level "CHARACTER SET utf8 COLLATE utf8_general_ci" (as EmptyDbase.sql
## defined it until this patch), which overrode the table's utf8mb4 default. As a result tag names couldn't contain
## 4-byte characters (such as emoji), and the definition relied on "utf8", which MySQL has deprecated as an alias for
## utf8mb3. utf8mb4 is a superset of utf8mb3, so existing tag names are unchanged. tagname has no index, and the only
## foreign key into Tags is on the integer tagid.
##
##  Created by Peter Olszowka on 2026-10-02;
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE RoomHasSet CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE RegTypes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE PhotoDenialReasons CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE PhotoUploadStatus CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE Tags CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

INSERT INTO PatchLog (patchname) VALUES ('76_fix_mysql_collations.sql');
