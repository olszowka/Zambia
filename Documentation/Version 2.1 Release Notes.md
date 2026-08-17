# Zambia Version 2.1 Release Notes

## Changes since release 2.0

### Bug Fixes

#### Configuration Table Editor

* hide tceEditor when switching pages, but catch unsaved changes
* fix initialization of tceEditor when cell value null
* ensure column width fits column name
* improve error handling and fix to edit columns names which are sql reserved words
* On Rooms table remove open and close time fields and generally improve working with this table (Issue 134)
* Fix headers when user has incorrect permissions
* Security improvements

#### Other

* On Admin Participants page, radio will default if unchecked rather than throw error
* On Edit Custom Text page, the block level mechanism now works
* On My Profile page, can save when badgeid is non-numeric (PR 126)
* Fix all sessions search within brainstorm
* On Admin Participants page, fix bug editing permisison roles and participant tags
* Don't allow editing of email address if integration with ConTroll enabled (for ConTroll security reasons)
* On Edit/Create Session page, fix escaping of 2nd title and 2nd description labels
* Add missing constants to db_name_sample and move one there
* On My Profile page, fix scenario when user edits data, saves, reverts edit, and saves again (Issue 114)
* On My Availability page, validate open ended text fields fit within db fields upon save. (Issue 89)
* Schema creation script EmptyDbase.sql was missing necessary data for table PhotoUploadStatus
* Fix broken key on permission table which allowed redundant permission configuration
* Fix Admin Phases page which incorrectly disabled phases by incorrectly identifying the "post con"
phase
### New Features

* New administration page to edit permissions (Issue 101)
  * Includes new permission atom for this page
* New report for printing back of badge schedule labels (Issue 144 / PR 145)
* In password recovery process, trim whitespace around badgeid and email before comparing (Issue 132)
* Email send results page has normal header and menu (Issue 140)
* Enhanced permission analysis report
* Overhaul report engine to segregate deployment-specific report edits (Issue 124)<br />
See [Report Customization.md](Report%20Customization.md)
* On Maintain Room Schedule page, clean up layout and allow for direct editing of start time and duration
* Convert some reports to filter by permrolename to make them more flexible for non-core permission configurations
* Modify back-to-back conflict report to respect STANDARD_BLOCK_LENGTH configuration
* On Administer Participants age, show participant's schedule on bottom
* Implement name-for-sorting field
* Improve error handling when working with participant photos
* Change storage of participant survey responses to json to simplify coding reports to use this data.  The previous mechanism using multiple rows per participant in the table `ParticipantSurveyAnswers` is still functional, but is deprecated and that table will go away some time around mid 2027.  Please update any con-specific reports to use the table `ParticipantSurveyResponses`.

### Invisible code cleanup

* Migrate to DataTables 2.3.8
* Retheme Bootstrap 5 to 36 columns to remove hacked partial columns and other related cleanup
* Migrate several pages to Bootstrap 5
* Migrate several pages to XSLT
* Migrate Bootstrap 5 to 5.3.8
* Migrate Bootstrap 4 to 4.6.2

## Application Notes

### db_name.php

##### The following entries need to be added to `db_name.php`

#### `NEW_ROOM_SLOTS` 

Moved from another file
Controls the number of rows at the bottom of Maintain Room Schedule
Default value: 5

#### `ENABLE_NAME_FOR_SORTING`

Controls whether the new name-for-sorting field appears
Default value: TRUE

#### `REPORT_SYSTEM_OVERRIDE_SUBDIR`

Controls where to look for core sets of report overrides.  Set to "Standard" to apply
a set of overrides located there which primarily hide reports no longer commonly used.
If this constant is missing or blank, the functionality it implements is safely disabled.

##### The following entries were added to the file `db_name_sample.php`, but were actually needed previously.

#### `UPDATE_REG_SYSTEM`

Controls whether edited user demographic data is written to ConTroll.

#### `REG_DBNAME`

If Zambia is integrated with ConTroll, specifies name of the ConTroll database.  It must be on the
same server as Zambia.

#### `REG_CONID`

If Zambia is integrated with ConTroll, specifies the convention id on the ConTroll side.

### Schema Patches

#### 70_remove_room_times.sql

Just apply this patch as normal. These schema changes are recent and haven't been seen previously. These schema
changes support the simplification of the `Rooms` table to ease configuration of it.

#### 71_name_for_sorting.sql

Just apply this patch as normal. These schema changes are recent and haven't been seen previously. These schema
changes support the new name-for-sorting property/feature.

#### 72_configure_permissions.sql

Just apply this patch as normal. These schema changes are recent and haven't been seen previously. These schema
changes support the new feature to configure permissions from the application.

#### 73_another_permissions_cleanup.sql

Just apply this patch as normal. These schema changes are recent and haven't been seen previously. These schema
changes support an additional key to prevent superfluous permissions configurations.

#### 74_survey_responses_json.sql

Just apply this patch as normal. These schema changes are recent and haven't been seen previously. These schema
changes support the change to storing survey responses as json.

#### 75_my_suggestions_custom_text.sql

Just apply this patch as normal. These schema changes are recent and haven't been seen previously. These schema
changes support the addition of new custom text entries.
