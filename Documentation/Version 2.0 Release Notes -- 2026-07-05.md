# Zambia Version 2.0 Release Notes
Note: This is the first time I'm using the github release mechanism, but I'm labelling the release "2.0" because
Zambia is so old.
## Changes since last published PR (previous release mechanism)
* Changed versions of required software supported
  * PHP: Only supporting 8.2 and 8.3
  * MySQL: Only supporting 8.X or 9.X
  * MariaDB: Only supporting 10.11.X
* Enhancements to Custom Text mechanism
  * Many more entries
  * Distinction between block level and in-line level entries
  * Active flag on entries so table can contain example which isn't currently used
  * All entries have hard coded default text or may be skipped entirely
  * There are a few text substitions which can be performed on entries
    * <span>$</span>CON_NAME<span>$</span>
    * <span>$</span>PROGRAM_EMAIL<span>$</span>
    * <span>$</span>CON_NUM_DAYS<span>$</span>
    * <span>$</span>CON_START_DATE<span>$</span>
    * <span>$</span>CON_END_DATE<span>$</span>
* Session form will exclude many properties not used if so configured
* Updated schema creation scripts to latest schema
* Created missing schema patch scripts for all schema changes
* Consolidate support for link properties on sessions: participant link, meeting link, captions link, recording
  link
* Support restricting which permission roles a user may grant
* Implemented declined participant mechanism
* Made brainstorm pages minimally functional
* Added Bootstrap 5 and started converting pages over to it.
* Added "My Suggestions" page back to menus
* Revised default permissions configuration
* Fixed bug saving participant roles
* CSV reports include byte order marker for UTF-8
* Rearranged configuration table editor and improved table documentation within it
* Remove usused older versions of some libraries
* Disable editing of email address when integrated with ConTroll for security reasons
## Application Notes
### db_name.php
The most reliable method is to do a diff with your version of db_name.php and the latest db_name_sample.php.
If you do that, at the very least, ensure you have entries for the four new link fields:  "MEETING_LINK", 
"PANELIST_LINK", "RECORDING_LINK", and "CAPTION_LINK".
### Schema patches
The schema patching process has definitely gotten messed up.  You'll have to manually inspect your schemas to
see which patch scripts you need.
#### 66_permission_cleanup.sql
This script makes no changes to the schema itself, but repopulates all the permission-related tables to match the
current code and standard practices.  If you don't have highly customized permission configuration, apply this
patch directly.  Otherwise, you'll have to rewrite it not to interfere with your customization.

Once this patch has been applied, there will be 6 permission roles which can be applied to users: 
* Administrator
* Senior Staff
* Staff
* Participant
* Declined Participant
* Brainstorm (not implemented)

Staff members should be assigned one and only one of the first 3.  If they are also participants, they should be
assigned the "Participant" role as well.  The "Declined Participant" role is for users who may have been given
access to the system previous (perhaps to fill out the survey), but have not been selected to be on program. Those
users will be only the declined participant page (customizable) when they log in, but have access to no other
pages.  If you don't want to use this new mechanism, just don't assign this role to anyone.

The "Brainstorm" role is vestigial and no longer functions.
#### 67_session_links_cleanup.sql
There are two versions of this patch script.  If your `Sessions` tables has none of the `*link` fields, apply the
`67_session_links_cleanup.sql` script.  If your `Sessions` table has the `panelistlink` field, but not the others,
apply the `67_session_links_cleanup_A.sql` script.
#### 68_custom_text_new_columns.sql
Just apply this patch as normal.  These schema changes are recent and haven't been seen previously.  These
schema changes support the recent enhancements to the custom text functionality.
#### 69_custom_text_cleanup.sql
You may safely apply this patch without concern for the current contents of your CustomText table as long as the
previous schema change patch has been applied.  This script will add any missing rows to the table, but skip any
which already exist.  Also, the new rows will be set to inactive.
