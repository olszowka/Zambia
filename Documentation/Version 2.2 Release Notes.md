# Zambia Version 2.2 Release Notes

## Changes since release 2.1

### Bug Fixes
* Bootstrap 5 version of header now supports configurable image like the Bootstrap 4 version does
* The defaults for a newly created session did not reflect the configuration provided in EmptyDbase.sql for the Types table.  Those defaults are now based on configuration for Type, Division, Adult/Children Category (kids category), and Roomset.
* The report engine menu builder properly escapes characters such as apostrophes.
* On MySQL 8 or later (but not MariaDB), schema patches 58-60 created some tables with a collation that doesn't match the rest of the schema, causing "Illegal mix of collations" errors, e.g. in the Admin Participants search. Patch 76 repairs them.

### New Features
* Users with the appropriate permission can hide reports from menus using the GUI
* Complete rewrite of grid scheduler
  * Bug visible in Safari where sessions wouldn't render at the correct start and/or end time when there were overlapping sessions in a room is fixed
  * Rendering resolution of boxes for start and end times is computed to the px, i.e. down to the minute
  * Snap (time resolution for dropping) is adjustable down to 5 minutes
  * Windows will scroll when user drags near edge
  * Grid reflects schedule edits from other users (or tabs) on a more predictable basis and are highlighted
  * TRACK_TAG_USAGE configuration is respected both in search and session box display
  * Division configuration is respected in search tab (hidden if not used)
  * Session box display is configurable
  * Time between grid lines is fully configurable
  * Interruptions in grid timing can be configured (typically used for meal breaks)
  * Distinct start and/or end times can be configured for each day
  * There is no limit to the number of overlapping sessions which can be rendered, but when more than 2 are scheduled, the details of them are not readable on the grid.
  * Optional "Grid Simplification" display draws a default-duration session as if it ran to the next grid line, for rooms and session types configured to allow it
  * Page options (Swap Mode, Snap, Grid Simplification) and which rooms are shown are remembered per user in the browser
* To accommodate relaxing the constraint that the configuration constant `STANDARD_BLOCK_LENGTH` must be "1:00" or "1:30", the _back to back conflict_ report was adjusted.  It now uses slightly different time thresholds to determine a conflict.  

### Invisible code cleanup
* View Session Counts page moved to XSL and BS5/JQ3
* Upgrade JQuery from 3.5.1 to 3.7.1
* Upgrade JQuery from 1.7.2 to 1.12.4
* Upgrade JQueryUI from 1.8.16 to 1.14.2
* Migrate Grid Scheduler page to BS5/JQ3.X
* Migrate Session History page to BS5/JQ3.X
* Migrate pages related to importing sessions from previous cons to BS5/JQ3.X
* Migrate Session Search page to BS5/JQ3.X
* Migrate some miscellaneous error reporting pages to BS5/JQ3.X
* Clarify error message in build report menu process.
* Don't force to bootstrap2 when user hits a page with session expired.
* Add tracking of bootstrap version so error pages are rendered with appropriate bootstrap version.
* Migrate Login and Logout pages to BS5/JQ3.X
* Migrate My Availability page to BS5/JQ3.X
* Migrate Send Email to Participants pages to BS5/JQ3.X
* Create dummy email sending mechanism for testing
* Migrate Panel Interests page to BS5/JQ3.X
* Migrate Data Collection Consent pages to BS5/JQ3.X
* Final elimination of BS2.X & JQ1.X
* Upgrade Tabulator to 6.5.3

## Application Notes

When a user creates a new session, the defaults for the values of Type, Division, Adult/Children Category (kids category), and Roomset will now be taken from the row from the top of the corresponding configuration table, i.e. with the lowest value for display_order.  Adjust your configuration accordingly.

### db_name.php

#### There are additional optional configuration constants in `db_name.php`

#### Grid Scheduler
Existing configurations of grid scheduler-related constants are still supported and will generally
work as previously.  Configuration changes are needed only to take advantage of the new features.
See the new document [Grid Scheduler Configuration](Grid%20Scheduler%20Configuration.md) for more
details.

The following optional configuration constants have been added which apply to the grid scheduler page.
* `GRID_DAY_SEGMENTS`
* `GRID_SPACING_MINUTES`
* `GRID_SESSION_BOX_FIELDS`

The configuration constant `STANDARD_BLOCK_LENGTH` is no longer limited to "1:00" or "1:30" and now can be any value. A multiple of 5 minutes is recommended.

The configuration constants `FIRST_DAY_START_TIME,` `OTHER_DAY_STOP_TIME,` `OTHER_DAY_START_TIME,`
and `LAST_DAY_STOP_TIME` may be left undefined and replaced with a definition of `GRID_DAY_SEGMENTS`
to permit more flexibility in the base grid timing.

### Schema Patches

#### 76_fix_mysql_collations.sql

Just apply this patch as normal. It is required on installations that ran patches 58, 59, or 60
on MySQL 8 or later, where those patches created `RoomHasSet`, `RegTypes`, `PhotoDenialReasons`,
and `PhotoUploadStatus` with MySQL's default `utf8mb4_0900_ai_ci` collation instead of the
`utf8mb4_general_ci` used by the rest of the schema. It converts those four tables to
`utf8mb4_general_ci`; on MariaDB, and on installations created from `EmptyDbase.sql`,
that part is superfluous but harmless. It also converts `Tags.tagname` (on every installation)
from the older 3-byte `utf8` (`utf8mb3`) character set to `utf8mb4_general_ci`, matching every
other text column, so tag names can contain any Unicode character (e.g. emoji). Existing tag
names are unchanged.

#### 77_grid_simplification.sql

Just apply this patch as normal. It adds a `allow_grid_simplification` column to the `Rooms` and `Types`
tables, used by the Grid Scheduler's new Grid Simplification option. Both default to 0 (not allowed);
set them to 1 in the _Configuration Table Editor_ for the rooms and session types where you want it.
See [Grid Scheduler Configuration](Grid%20Scheduler%20Configuration.md).
