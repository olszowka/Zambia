### List of tables in alphabetical order (* includes data at end)

* BioEditStatuses*
* CongoDump
* CongoDumpHistory
* Credentials*
* CustomText*
* Divisions*
* EmailCC
* EmailFrom
* EmailHistory
* EmailQueue
* EmailTo
* Features
* KidsCategories*
* LanguageStatuses*
* ParticipantAvailability
* ParticipantAvailabilityDays
* ParticipantAvailabilityTimes
* ParticipantHasCredential
* ParticipantHasRole
* ParticipantHasTag
* ParticipantInterests
* ParticipantOnSession
* ParticipantOnSessionHistory
* ParticipantPasswordResetRequests
* Participants
* ParticipantSessionInterest
* ParticipantSuggestions
* ParticipantSurveyAnswers
* ParticipantTags
* PatchLog*
* PermissionAtoms*
* PermissionRoles*
* Permissions*
* Phases*
* PhotoDenialReasons
* PhotoUploadStatus
* PreviousCons
* PreviousConTracks
* PreviousParticipants
* PreviousSessions
* PubStatuses*
* RegTypes
* Roles
* RoomHasSet
* Rooms
* RoomSets*
* Schedule
* Services
* SessionEditCodes*
* SessionEditHistory
* SessionHasFeature
* SessionHasService
* SessionHasTag
* Sessions
* SessionStatuses*
* SurveyQuestionConfig
* SurveyQuestionOptionConfig
* SurveyQuestionTypeDefaults
* SurveyQuestionTypes
* Tags
* Times*
* TrackCompatibility
* Tracks*
* Types*
* UserHasPermissionRole

### List of tables in foreign key safety order

* BioEditStatuses ✔
* CongoDump ✔
* Credentials ✔
* CustomText
* Divisions
* EmailCC
* EmailFrom
* EmailHistory
* EmailQueue
* EmailTo
* Features
* KidsCategories
* LanguageStatuses
* ParticipantPasswordResetRequests
* Participants
* ParticipantTags
* PatchLog
* PermissionAtoms
* PermissionRoles
* Phases
* PhotoDenialReasons
* PhotoUploadStatus
* PreviousCons
* PreviousParticipants
* PubStatuses
* RegTypes
* Roles
* RoomSets
* Rooms
* Services
* SessionEditCodes
* SessionStatuses
* SurveyQuestionTypes
* Tags
* Times
* Tracks
* Types
* CongoDumpHistory
* Sessions
* ParticipantAvailability
* ParticipantAvailabilityDays
* ParticipantAvailabilityTimes
* ParticipantHasCredential
* ParticipantHasRole
* ParticipantHasTag
* ParticipantInterests
* ParticipantOnSession
* ParticipantOnSessionHistory
* ParticipantSessionInterest
* ParticipantSuggestions
* Permissions
* PreviousConTracks
* PreviousSessions
* RoomHasSet
* Schedule
* SessionEditHistory
* SessionHasFeature
* SessionHasTag
* SessionHasService
* SurveyQuestionConfig
* SurveyQuestionOptionConfig
* SurveyQuestionTypeDefaults
* ParticipantSurveyAnswers
* TrackCompatibility
* UserHasPermissionRole

### List of CustomText tags
| tag                    | page                   | required | default                                              | html block level | notes                                                                                             |
|------------------------|------------------------|----------|------------------------------------------------------|------------------|---------------------------------------------------------------------------------------------------|
| biography_note         | My Profile             | no       | \<skipped>                                           | yes              |                                                                                                   |
| registration_data      | My Profile             | no       | \<skipped>                                           | yes              |                                                                                                   |
| policy_block_at_top    | My Profile             | no       | \<skipped>                                           | yes              |                                                                                                   |
| note_before_time_slots | My Availability        | no       | \<empty string>                                      | yes              |                                                                                                   |
| note_after_times       | My Availability        | no       | \<empty string>                                      | yes              | tag called _note_after_time_slots_ in some branches                                               |
| ~~enough_panels~~      | My Schedule            | no       | \<empty string>                                      | no               | code to render is currently commented out                                                         |
| ~~not_enough_panels~~  | My Schedule            | no       | \<empty string>                                      | no               | code to render is currently commented out                                                         |
| all_panelists_1        | My Schedule            | no       | \<empty string>                                      | yes              |                                                                                                   |
| all_panelists_2        | My Schedule            | no       | \<empty string>                                      | yes              |                                                                                                   |
| consent                | Data Retention Consent | no       | \<see below>                                         | yes              | Default content recently implemented.                                                             |
| photo_note             | My Photo               | no       | \<see below>                                         | yes              | Default content recently implemented. Leaving empty results in a little extra vertical whitespace |
| survey_displayonly     | Participant Survey     | no       | \<see below>                                         | yes              | Default content recently implemented.                                                             |
| part_overview          | Participant Overview   | no       | \<empty string>                                      | yes              |                                                                                                   |
| staff_overview         | Staff Overview         | no       | \<see below>                                         | yes              | Default content recently implemented.                                                             |
| declined_particpant    | Declined to Invite     | no       | \<see below>                                         | yes              | Default content recently implemented.                                                             |
| panel_types_not_int    | General Interests      | no       | Panel types I am not interested in participating in: | no               |                                                                                                   |
| other_role_desc        | General Interests      | no       | Description for "Other" Roles:                       | no               |                                                                                                   |
| roles_checkboxes_label | General Interests      | no       | Roles I'm willing to take on:                        | no               |                                                                                                   |
| stuff_id_like_to_run   | General Interests      | no       | Workshops or presentations I'd like to run:          | no               |                                                                                                   |
| people_want_on_sess_label | General Interests | no | People with whom I'd like to be on a session: (Leave blank for none) | no | Entry recently added |
| people_dont_want_label | General Interests | no | People with whom I'd rather not be on a session: (Leave blank for none) | no | Entry recently added |


#### Custom tag text for long ones

##### consent

&lt;p>We collect your personal data to allow us to schedule you onto programming items, to publish data about your participation in programming items, and to administer $CON_NAME$.  We retain this data for the duration of this convention and to assist in planning future conventions.&lt;/p><br/>
&lt;p>We do not share personal data with other conventions or organizations. Public data, such as your biography, photo, and socials, will be published in our printed program guide and online schedule.&lt;/p><br/>
&lt;p>Without your consent to collect and use your data in this way, we are unable to have you as a program participant for $CON_NAME$.&lt;/p><br/>

##### staff_overview

&lt;p>Please note the tabs above. One of them will take you to your participant view. Another will allow you to manage Sessions. Note that Sessions is the generic term Zambia uses for anything it can schedule, e.g. Panels, Events, Readings, etc.&lt;/p><br/>
&lt;p>The general flow of sessions over time is:&lt;/p><br/>
&lt;dl><br/>
&lt;dt>Brainstorm&lt;/dt><br/>
&lt;dd>If $CON_NAME$ is using the brainstorm functionality, these are sessions created by non-staff members which haven't yet been edited by a staff member.&lt;/dd><br/>
&lt;dt>Edit Me&lt;/dt><br/>
&lt;dd>New session idea that a staff member entered. An idea entered by a brainstorm user that is non-offensive and the least bit feasible should be moved to this status. These are still rough and may well have issues. There still could be duplicates.&lt;/dd><br/>
&lt;dt>Vetted&lt;/dt><br/>
&lt;dd>A real session that we'd like to see happen. At this point the language should be fairly close to final in the description. Proofreading should have happened. More fields are required at this point.  This is the minimal status that participants are allowed to sign up for. Avoid duplicates, but many of these still will not happen for various reasons.&lt;/dd><br/>
&lt;dt>Assigned&lt;/dt><br/>
&lt;dd>Session has participants assigned to it.&lt;/dd><br/>
&lt;dt>Scheduled&lt;/dt><br/>
&lt;dd>Session is in the schedule (don't set this by hand as Zambia actually sets this for you when you schedule it in a room!) The language needs to match what you want to see &lt;strong>published&lt;/strong>.&lt;/dd><br/>
&lt;/dl><br/>
&lt;p>There are 3 other statuses that a session can have:&lt;/p><br/>
&lt;dl><br/>
&lt;dt>Dropped&lt;/dt><br/>
&lt;dd>This item is no longer under consideration and is unlikely even to be mined for future ideas.&lt;/dd><br/>
&lt;dt>Duplicate&lt;/dt><br/>
&lt;dd>Might have been a good session, but was too close or identical to another one.&lt;/dd><br/>
&lt;dt>Cancelled&lt;/dt><br/>
&lt;dd>Over all a good idea, but it isn't going to happen this year. Generally used later in the programming process. You should probably still say why it was cancelled in the "Notes for Program Committee" field. This is a category we can mine for ideas in future years&lt;/dd><br/>
&lt;/dl><br/>
&lt;p>Some details regarding $CON_NAME$":&lt;/p><br/>
&lt;dl class="ms-4"><br/>
&lt;dd>Convention dates: $CON_START_DATE$ - $CON_END_DATE$&lt;/dd><br/>
&lt;dd>Number of days: $CON_NUM_DAYS$&lt;/dd><br/>
&lt;/dl><br/>

##### declined_participant

&lt;h3 class="mb-2">Thank you so much for contacting $CON_NAME$.&lt;/h3><br/>
&lt;p>If you are receiving this message, your record in the Zambia system has been closed. A closed record indicates one or more of three things:&lt;/p><br/>
&lt;ol><br/>
&lt;li>You contacted $CON_NAME$ to let us know that you are unable to participate in the program this year.&lt;/li><br/>
&lt;li>You did not meet a deadline to contact us or provide required information.&lt;/li><br/>
&lt;li>You were not selected to be on the $CON_NAME$ program. We received far more requests to be on program from qualified and amazing people than it is possible to accommodate.&lt;/li><br/>
&lt;/ol><br/>
&lt;p>If you have any questions or if you believe that an error has been made, please contact us at &lt;a href="mailto:$PROGRAM_EMAIL$">$PROGRAM_EMAIL$&lt;/a>&lt;/p><br/>

##### photo_note

&lt;p>Note: Photos should be of type JPEG (.jpg) or PNG (.png), 1 MB or less in size and should be about 800x800. If you need to optimize your photo''s file size we suggest "Save For Web".&lt;/p><br />
&lt;p>After uploading, you will be able to crop and rotate the image. To upload a photo, either drag the file to the upload photo area or click the upload photo button to use a file picker to select the file to upload.&lt;/p><br />
&lt;p>All photos uploaded will be reviewed for approval—a default placeholder stock image will appear prior to approval. The approved photo will be available for use in publications, on-line guides, and marketing materials. If you already have an approved photo, any new photo uploaded will be added to the review queue and it will replace the approved photo once reviewed and accepted.&lt;/p>

##### survey_displayonly

&lt;p>Note: Some questions may no longer allow you to enter/change their answers. The time has passed for when you can change them and they have been changed from answerable to display only.&lt;/p><br />
&lt;p>If you need to have a display only answer changed, please reach out to programming at the email address below.&lt;/p>
