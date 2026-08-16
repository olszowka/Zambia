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
* Schema creation script EmptyDbase.sql was missing necessary data for table PhotoUploadReasons

### New Features

* New administration page to edit permissions (Issue 101)
  * Includes new permission atom for this page
* New report for printing back of badge schedule labels (Issue 144 / PR 145)
* In password recovery process, trim whitespace around badgeid and email before comparing (Issue 132)
* Email send results page has normal header and menu (Issue 140)
* Enhanced permission analysis report
* Overhaul report engine to segregate deployment-specific report edits (Issue 124)
* On Maintain Room Schedule page, clean up layout and allow for direct editing of start time and duration
* Convert some reports to filter by permrolename to make them more flexible for non-core permission configurations
* Modify back-to-back conflict report to respect STANDARD_BLOCK_LENGTH configuration
* On Administer Participants age, show participant's schedule on bottom
* Implement name-for-sorting field
* Improve error handling when working with participant photos

### Invisible code cleanup

* Migrate to DataTables2.3.8
* Retheme Bootstrap 5 to 36 columns to remove hacked partial columns and other related cleanup
* Migrate several pages to Bootstrap 5
* Migrate several pages to XSLT

## Application Notes

### db_name.php

The following entries need to be added to `db_name.php`

#### `NEW_ROOM_SLOTS` 

Moved from another file
Controls the number of rows at the bottom of Maintain Room Schedule
Default value: 5

#### `ENABLE_NAME_FOR_SORTING`

Controls whether the new name-for-sorting field appears
Default value: TRUE
