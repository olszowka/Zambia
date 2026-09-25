# Zambia Version 2.2 Release Notes

## Changes since release 2.1

### Bug Fixes
* Bootstrap 5 version of header now supports configurable image like the Bootstrap 4 version does
* The defaults for a newly created session did not reflect the configuration provided in EmptyDbase.sql for the Types table.  Those defaults are now based on configuration for Type, Division, Adult/Children Category (kids category), and Roomset.
* The report engine menu builder properly escapes characters such as apostrophies.

### New Features
* Users with the appropriate permission can hide reports from menus using the GUI

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

##### The following entries need to be added to `db_name.php`

### Schema Patches
