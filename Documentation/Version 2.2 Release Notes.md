# Zambia Version 2.2 Release Notes

## Changes since release 2.1

### Bug Fixes
* On MySQL 8 or later (but not MariaDB), schema patches 58-60 created some tables with a collation that doesn't match the rest of the schema, causing "Illegal mix of collations" errors, e.g. in the Admin Participants search. Patch 76 repairs them.

### Schema Patches

#### 76_fix_mysql_collations.sql

Just apply this patch as normal. It is required on installations that ran patches 58, 59, or 60 on MySQL 8 or later,
where those patches created `RoomHasSet`, `RegTypes`, `PhotoDenialReasons`, and `PhotoUploadStatus` with MySQL's
default `utf8mb4_0900_ai_ci` collation instead of the `utf8mb4_general_ci` used by the rest of the schema. It
converts those four tables to `utf8mb4_general_ci`; on MariaDB, and on installations created from `EmptyDbase.sql`,
that part is superfluous but harmless. It also converts `Tags.tagname` (on every installation) from the older 3-byte
`utf8` (`utf8mb3`) character set to `utf8mb4_general_ci`, matching every other text column, so tag names can contain
any Unicode character (e.g. emoji). Existing tag names are unchanged.
