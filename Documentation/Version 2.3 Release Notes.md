# Zambia Version 2.3 Release Notes

## Changes since release 2.2

### Bug Fixes
* 

### New Features
* On Panel Interests page, add session portion moved to bottom of page
* On Panel Interests page, the ranking-scale instructions and the label above each session's comments box can now
  be customized with Custom Text (page "Panel Interests", tags `ranking_scale` and `comments_label`)
 
### Invisible code cleanup
* Remove redundant calls to fetch custom text and change query to prepared

## Application Notes

1) 

### db_name.php

### Schema Patches

#### 79_panel_interests_custom_text.sql

Just apply this patch as normal. It adds two inactive Custom Text entries for the Panel Interests page, each holding
the page's current built-in text: `ranking_scale`, the ranking-scale instructions at the top of the page, and
`comments_label`, the label above each session's comments box. While an entry is inactive (or missing), the page
shows the built-in default text. To customize either one, edit the entry and make it active.
