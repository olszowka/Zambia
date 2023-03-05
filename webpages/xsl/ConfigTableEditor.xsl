<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2021-01-04;
	Copyright (c) 2021-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="UpdateMessage" select="''"/>
    <xsl:param name="control" select="''"/>
    <xsl:param name="controliv" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="html"/>
    <xsl:template match="/">
        
        <!-- This is set to false because it is not currently used
        <xsl:variable name="editBioEditStatuses"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_BioEditStatuses' or @permatomtag='ce_All']" />-->
        <xsl:variable name="editBioEditStatuses" select="false()" />
        <xsl:variable name="editCredentials"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Credentials' or @permatomtag='ce_All']" />
        <xsl:variable name="editRoles"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Roles' or @permatomtag='ce_All']" />
        <xsl:variable name="editTimes"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Times' or @permatomtag='ce_All']" />
        <!-- This is set to false because it is not implemented yet
        <xsl:variable name="editPhotoDenialReasons"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_PhotoDenialReasons' or @permatomtag='ce_All']" />-->
        <xsl:variable name="editPhotoDenialReasons" select="false()" />
        <xsl:variable name="editAnyParticipant" select="$editBioEditStatuses or $editCredentials or $editRoles or $editTimes or $editPhotoDenialReasons" />
        <xsl:variable name="editTracks"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tracks' or @permatomtag='ce_All']" />
        <xsl:variable name="editTags"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tags' or @permatomtag='ce_All']" />
        <xsl:variable name="editTypes"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Types' or @permatomtag='ce_All']" />
        <xsl:variable name="editKidsCategories"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_KidsCategories' or @permatomtag='ce_All']" />
        <xsl:variable name="editLanguageStatuses"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_LanguageStatuses' or @permatomtag='ce_All']" />
        <xsl:variable name="editPubStatuses"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_PubStatuses' or @permatomtag='ce_All']" />
        <xsl:variable name="editSessionStatuses"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_SessionStatuses' or @permatomtag='ce_All']" />
        <xsl:variable name="editAnySession" select="$editTracks or $editTags or $editTypes or $editKidsCategories
            or $editLanguageStatuses or $editPubStatuses or $editSessionStatuses" />
        <xsl:variable name="editRooms"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Rooms' or @permatomtag='ce_All']" />
        <xsl:variable name="editRoomSets"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomSets' or @permatomtag='ce_All']" />
        <xsl:variable name="editRoomHasSet"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomHasSet' or @permatomtag='ce_All']" />
        <xsl:variable name="editFeatures"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Features' or @permatomtag='ce_All']" />
        <xsl:variable name="editServices"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Services' or @permatomtag='ce_All']" />
        <xsl:variable name="editAnyFacility" select="$editRooms or $editRoomSets or $editRoomHasSet or $editFeatures
            or $editServices" />
        <xsl:variable name="editEmailFrom"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailFrom' or @permatomtag='ce_All']" />
        <xsl:variable name="editEmailTo"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailTo' or @permatomtag='ce_All']" />
        <xsl:variable name="editEmailCC"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailCC' or @permatomtag='ce_All']" />
        <xsl:variable name="editAnyEmail" select="$editEmailFrom or $editEmailTo or $editEmailCC" />
        <xsl:variable name="editDivisions"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Divisions' or @permatomtag='ce_All']" />
        <xsl:variable name="editRegTypes"
            select="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RegTypes' or @permatomtag='ce_All']" />
        <xsl:variable name="editAnyMisc" select="$editDivisions or $editRegTypes" />
        <xsl:variable name="editAnyThing" select="$editAnyParticipant or $editAnySession or $editAnyFacility or $editAnyEmail or $editAnyMisc" />
        <div id="message" class="alert alert-success mt-4">
            <xsl:choose>
                <xsl:when test="$UpdateMessage = ''">
                    <xsl:attribute name="class">
                        <xsl:text>alert alert-success mt-4 hidden</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="class">
                        <xsl:text>alert alert-success mt-4</xsl:text>
                    </xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
        </div>
        <xsl:if test="$editAnyThing">
            <div id="unsavedWarningModal" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Data Not Saved</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&#215;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>
                                You have unsaved changes highlighted in the table below!
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="cancelOpenSearchBUTN" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                            <button type="button" id="overrideOpenSearchBUTN" class="btn btn-secondary"
                                    onclick="return discardChanges();">Discard changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <h1 style="text-align: center;">Configuration Table Editor</h1>
                <ul class="nav nav-tabs">
                    <li class="nav-item ml-3">
                        <a href="#overview" class="nav-link active" data-toggle="tab" id="overview-top">Overview</a>
                    </li>
                    <xsl:if test="$editAnyParticipant">
                        <li class="nav-item">
                            <a href="#part" class="nav-link" data-toggle="tab" id="part-top">Participants</a>
                        </li>
                    </xsl:if>
                    <xsl:if test="$editAnySession">
                        <li class="nav-item">
                            <a href="#session" class="nav-link" data-toggle="tab" id="session-top">Sessions</a>
                        </li>
                    </xsl:if>
                    <xsl:if test="$editAnyFacility">
                        <li class="nav-item">
                            <a href="#facility" class="nav-link" data-toggle="tab" id="facility-top">Facility</a>
                        </li>
                    </xsl:if>
                    <xsl:if test="$editAnyEmail">
                        <li class="nav-item">
                            <a href="#emails" class="nav-link" data-toggle="tab" id="emails-top">Emails</a>
                        </li>
                    </xsl:if>
                    <xsl:if test="$editAnyMisc">
                        <li class="nav-item">
                            <a href="#misc" class="nav-link" data-toggle="tab" id="misc-top">Miscellaneous</a>
                        </li>
                    </xsl:if>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show mt-4 active" id="overview">
                        <h2>How to use the Configuration Table Editor</h2>
                        <p>
                            The editor is broken down into sections for each type configuration table.
                            <ul>
                                <xsl:if test="$editAnyParticipant">
                                    <li>Participants: Tables that effect the participant screens/reports</li>
                                </xsl:if>
                                <xsl:if test="$editAnySession">
                                    <li>Sessions: Tables that effect the session screens/reports</li>
                                </xsl:if>
                                <xsl:if test="$editAnyFacility">
                                    <li>Facility: Tables that define the hotel or other facility being used for the
                                        convention
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editAnyEmail">
                                    <li>Emails: Tables that define Zambia's ability to send emails to participants</li>
                                </xsl:if>
                                <xsl:if test="$editAnyMisc">
                                    <li>Miscellaneous: Tables not otherwise categorized
                                    </li>
                                </xsl:if>
                            </ul>
                        </p>
                        <p>Each section has a description tab for the tables available in that section. This explains
                            the usage of each of the tables.
                        </p>
                        <p>Each table has its own tab. The database table will be presented as an html table which is
                            editable in place. Changes are not stored in the database until the save button is pressed.
                        </p>
                    </div>
                    <xsl:if test="$editAnyParticipant">
                        <div class="tab-pane fade show mt-4" id="part">
                            <h2>Participant Configuration Tables</h2>
                            <ul class="nav nav-tabs">
                                <li class="nav-item ml-3">
                                    <a href="#partdesc" class="nav-link active" data-toggle="tab" id="part-overview">
                                        Participant Configuration Tables
                                    </a>
                                </li>
                                <xsl:if test="$editBioEditStatuses">
                                    <li class="nav-item">
                                        <a href="#bioeditstatuses" class="nav-link" data-toggle="tab" data-top="part-top"
                                           id="t-BioEditStatuses">BioEditStatuses
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editCredentials">
                                    <li class="nav-item">
                                        <a href="#credentials" class="nav-link" data-toggle="tab" data-top="part-top"
                                           id="t-Credentials">Credentials
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editRoles">
                                    <li class="nav-item">
                                        <a href="#roles" class="nav-link" data-toggle="tab" data-top="part-top"
                                           id="t-Roles">Roles
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editTimes">
                                    <li class="nav-item">
                                        <a href="#times" class="nav-link" data-toggle="tab" data-top="part-top"
                                           id="t-Times">Times
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editPhotoDenialReasons">
                                    <li class="nav-item">
                                        <a href="#photodenialreasons" class="nav-link" data-toggle="tab" data-top="part-top"
                                           id="t-PhotoDenialReasons">PhotoDenialReasons
                                        </a>
                                    </li>
                                </xsl:if>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane mt-4 fade show active" id="partdesc">
                                    <h3>Participant Configuration Table Usage</h3>
                                    <ul>
                                        <xsl:if test="$editBioEditStatuses">
                                            <li>BioEditStatuses:</li>
                                            <p>(currently unused) Participant Biography Editing</p>
                                        </xsl:if>
                                        <xsl:if test="$editCredentials">
                                            <li>Credentials:</li>
                                            <p>Professions a participant may designate themselves as being</p>
                                        </xsl:if>
                                        <xsl:if test="$editRoles">
                                            <li>Roles</li>
                                            <p>Ways participants may offer to participate</p>
                                        </xsl:if>
                                        <xsl:if test="$editTimes">
                                            <li>Times</li>
                                            <p>Time boundaries participants can indicate they are available for scheduling</p>
                                        </xsl:if>
                                        <xsl:if test="$editPhotoDenialReasons">
                                            <li>PhotoDenialReasons</li>
                                            <p>Reasons a participant's photo may be denied for publication </p>
                                        </xsl:if>
                                    </ul>
                                </div>
                                <xsl:if test="$editBioEditStatuses">
                                    <div class="tab-pane mt-4 fade" id="bioeditstatuses"/>
                                </xsl:if>
                                <xsl:if test="$editCredentials">
                                    <div class="tab-pane mt-4 fade" id="credentials"/>
                                </xsl:if>
                                <xsl:if test="$editRoles">
                                    <div class="tab-pane mt-4 fade" id="roles"/>
                                </xsl:if>
                                <xsl:if test="$editTimes">
                                    <div class="tab-pane mt-4 fade" id="times"/>
                                </xsl:if>
                                <xsl:if test="$editPhotoDenialReasons">
                                    <div class="tab-pane mt-4 fade" id="photodenialreasons"/>
                                </xsl:if>
                            </div>
                        </div>
                    </xsl:if>
                    <xsl:if test="$editAnySession">
                        <div class="tab-pane mt-4 fade" id="session">
                            <h2>Session Configuration Tables</h2>
                            <ul class="nav nav-tabs">
                                <li class="nav-item ml-3">
                                    <a href="#sessiondesc" class="nav-link active" data-toggle="tab">Session
                                        Configuration
                                        Tables
                                    </a>
                                </li>
                                <xsl:if test="$editTracks">
                                    <li class="nav-item">
                                        <a href="#tracks" class="nav-link" data-toggle="tab" data-top="session-top"
                                           id="t-Tracks">Tracks
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editTags">
                                    <li class="nav-item">
                                        <a href="#tags" class="nav-link" data-toggle="tab" data-top="session-top"
                                           id="t-Tags">Tags
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editTypes">
                                    <li class="nav-item">
                                        <a href="#Types" class="nav-link" data-toggle="tab" data-top="session-top"
                                           id="t-Types">Types
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editKidsCategories">
                                    <li class="nav-item">
                                        <a href="#kidscategories" class="nav-link" data-toggle="tab"
                                           data-top="session-top"
                                           id="t-KidsCategories">KidsCategories
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editLanguageStatuses">
                                    <li class="nav-item">
                                        <a href="#languagestatuses" class="nav-link" data-toggle="tab"
                                           data-top="session-top" id="t-LanguageStatuses">LanguageStatuses
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editPubStatuses">
                                    <li class="nav-item">
                                        <a href="#pubstatuses" class="nav-link" data-toggle="tab" data-top="session-top"
                                           id="t-PubStatuses">PubStatuses
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editSessionStatuses">
                                    <li class="nav-item">
                                        <a href="#sessionstatuses" class="nav-link" data-toggle="tab"
                                           data-top="session-top"
                                           id="t-SessionStatuses">SessionStatuses
                                        </a>
                                    </li>
                                </xsl:if>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane mt-4 fade show active" id="sessiondesc">
                                    <h3>Session Configuration Table Usage</h3>
                                    <ul>
                                        <xsl:if test="$editTracks">
                                            <li>Tracks</li>
                                            <p>For categorizing a session by topic; One and only one of these must be
                                                assigned to a session
                                            </p>
                                        </xsl:if>
                                        <xsl:if test="$editTags">
                                            <li>Tags</li>
                                            <p>For categorizing a session by topic; Any number of these may be assigned
                                                to a session
                                            </p>
                                        </xsl:if>
                                        <xsl:if test="$editTypes">
                                            <li>Types</li>
                                            <p>The format of activity to take place at the session</p>
                                        </xsl:if>
                                        <xsl:if test="$editKidsCategories">
                                            <li>KidsCategories</li>
                                            <p>The classification of the suitability of a session for children</p>
                                        </xsl:if>
                                        <xsl:if test="$editLanguageStatuses">
                                            <li>LanguageStatuses</li>
                                            <p>Language session will be given in (Vestigial feature which may no longer
                                                work)
                                            </p>
                                        </xsl:if>
                                        <xsl:if test="$editPubStatuses">
                                            <li>PubStatuses</li>
                                            <p>Whether a session is to be published (Rename only; do not insert or
                                                delete)
                                            </p>
                                        </xsl:if>
                                        <xsl:if test="$editSessionStatuses">
                                            <li>SessionStatuses</li>
                                            <p>Progress of session through workflow (Rename only; do not insert or
                                                delete)
                                            </p>
                                        </xsl:if>
                                    </ul>
                                </div>
                                <xsl:if test="$editTracks">
                                    <div class="tab-pane mt-4 fade" id="tracks"/>
                                </xsl:if>
                                <xsl:if test="$editTags">
                                    <div class="tab-pane mt-4 fade" id="tags"/>
                                </xsl:if>
                                <xsl:if test="$editTypes">
                                    <div class="tab-pane mt-4 fade" id="types"/>
                                </xsl:if>
                                <xsl:if test="$editKidsCategories">
                                    <div class="tab-pane mt-4 fade" id="kidscategories"/>
                                </xsl:if>
                                <xsl:if test="$editLanguageStatuses">
                                    <div class="tab-pane mt-4 fade" id="languagestatuses"/>
                                </xsl:if>
                                <xsl:if test="$editPubStatuses">
                                    <div class="tab-pane mt-4 fade" id="pubstatuses"/>
                                </xsl:if>
                                <xsl:if test="$editSessionStatuses">
                                    <div class="tab-pane mt-4 fade" id="sessionstatuses"/>
                                </xsl:if>
                            </div>
                        </div>
                    </xsl:if>
                    <xsl:if test="$editAnyFacility">
                        <div class="tab-pane mt-4 fade" id="facility">
                            <h2>Facility Configuration Tables</h2>
                            <ul class="nav nav-tabs">
                                <li class="nav-item ml-3">
                                    <a href="#facilitydesc" class="nav-link active" data-toggle="tab">Facility Configuration
                                        Tables
                                    </a>
                                </li>
                                <xsl:if test="$editRooms">
                                    <li class="nav-item">
                                        <a href="#rooms" class="nav-link" data-toggle="tab" data-top="facility-top"
                                           id="t-Rooms">Rooms
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editRoomSets">
                                    <li class="nav-item">
                                        <a href="#roomsets" class="nav-link" data-toggle="tab" data-top="facility-top"
                                           id="t-RoomSets">RoomSets
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editRoomHasSet">
                                    <li class="nav-item">
                                        <a href="#roomhasset" class="nav-link" data-toggle="tab" data-top="facility-top"
                                           id="t-RoomHasSet">RoomHasSet
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editFeatures">
                                    <li class="nav-item">
                                        <a href="#features" class="nav-link" data-toggle="tab" data-top="facility-top"
                                           id="t-Features">Features
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editServices">
                                    <li class="nav-item">
                                        <a href="#Services" class="nav-link" data-toggle="tab" data-top="facility-top"
                                           id="t-Services">Services
                                        </a>
                                    </li>
                                </xsl:if>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane mt-4 fade show active" id="facilitydesc">
                                    <h3>Facility Configuration Table Usage</h3>
                                    <ul>
                                        <xsl:if test="$editRooms">
                                            <li>Rooms</li>
                                            <p>The location where a session can be scheduled</p>
                                        </xsl:if>
                                        <xsl:if test="$editRoomSets">
                                            <li>RoomSets</li>
                                            <p>The categorization of the arrangement of the furniture in the room</p>
                                        </xsl:if>
                                        <xsl:if test="$editRoomHasSet">
                                            <li>RoomHasSet</li>
                                            <p>Which rooms can have which RoomSets</p>
                                        </xsl:if>
                                        <xsl:if test="$editFeatures">
                                            <li>Features</li>
                                            <p>Characteristics of the location required by the session</p>
                                        </xsl:if>
                                        <xsl:if test="$editServices">
                                            <li>Services</li>
                                            <p>Transportable additions to the location required by the session</p>
                                        </xsl:if>
                                    </ul>
                                </div>
                                <xsl:if test="$editRooms">
                                    <div class="tab-pane mt-4 fade" id="rooms"/>
                                </xsl:if>
                                <xsl:if test="$editRoomSets">
                                    <div class="tab-pane mt-4 fade" id="roomsets"/>
                                </xsl:if>
                                <xsl:if test="$editRoomHasSet">
                                    <div class="tab-pane mt-4 fade" id="roomhasset"/>
                                </xsl:if>
                                <xsl:if test="$editFeatures">
                                    <div class="tab-pane mt-4 fade" id="features"/>
                                </xsl:if>
                                <xsl:if test="$editServices">
                                    <div class="tab-pane mt-4 fade" id="services"/>
                                </xsl:if>
                            </div>
                        </div>
                    </xsl:if>
                    <xsl:if test="$editAnyEmail">
                        <div class="tab-pane mt-4 fade" id="emails">
                            <h2>Email Configuration Tables</h2>
                            <ul class="nav nav-tabs">
                                <li class="nav-item ml-3">
                                    <a href="#emaildesc" class="nav-link active" data-toggle="tab">Email Configuration
                                        Tables
                                    </a>
                                </li>
                                <xsl:if test="$editEmailFrom">
                                    <li class="nav-item">
                                        <a href="#emailfrom" class="nav-link" data-toggle="tab" data-top="emails-top"
                                           id="t-EmailFrom">EmailFrom
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editEmailTo">
                                    <li class="nav-item">
                                        <a href="#emailto" class="nav-link" data-toggle="tab" data-top="emails-top"
                                           id="t-EmailTo">EmailTo
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editEmailCC">
                                    <li class="nav-item">
                                        <a href="#emailcc" class="nav-link" data-toggle="tab" data-top="emails-top"
                                           id="t-EmailCC">EmailCC
                                        </a>
                                    </li>
                                </xsl:if>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane mt-4 fade show active" id="emaildesc">
                                    <h3>Email Configuration Table Usage</h3>
                                    <ul>
                                        <xsl:if test="$editEmailFrom">
                                            <li>EmailFrom</li>
                                            <p>List of From: lines for emails sent by Zambia</p>
                                        </xsl:if>
                                        <xsl:if test="$editEmailTo">
                                            <li>EmailTo</li>
                                            <p>List of queries to select emails to be sent by Zambia</p>
                                        </xsl:if>
                                        <xsl:if test="$editEmailCC">
                                            <li>EmailCC</li>
                                            <p>List of potential CC address choices for emails sent by Zambia</p>
                                        </xsl:if>
                                    </ul>
                                </div>
                                <xsl:if test="$editEmailFrom">
                                    <div class="tab-pane mt-4 fade" id="emailfrom"/>
                                </xsl:if>
                                <xsl:if test="$editEmailTo">
                                    <div class="tab-pane mt-4 fade" id="emailto"/>
                                </xsl:if>
                                <xsl:if test="$editEmailCC">
                                    <div class="tab-pane mt-4 fade" id="emailcc"/>
                                </xsl:if>
                            </div>
                        </div>
                    </xsl:if>
                    <xsl:if test="$editAnyMisc">
                        <div class="tab-pane mt-4 fade" id="misc">
                            <h2>Miscellaneous Tables</h2>
                            <ul class="nav nav-tabs">
                                <li class="nav-item ml-3">
                                    <a href="#miscdesc" class="nav-link active" data-toggle="tab">Miscellaneous
                                        Configuration Tables
                                    </a>
                                </li>
                                <xsl:if test="$editDivisions">
                                    <li class="nav-item">
                                        <a href="#divisions" class="nav-link" data-toggle="tab" data-top="misc-top"
                                           id="t-Divisions">Divisions
                                        </a>
                                    </li>
                                </xsl:if>
                                <xsl:if test="$editRegTypes">
                                    <li class="nav-item">
                                        <a href="#regtypes" class="nav-link" data-toggle="tab" data-top="misc-top"
                                           id="t-RegTypes">RegTypes
                                        </a>
                                    </li>
                                </xsl:if>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane mt-4 fade show active" id="miscdesc">
                                    <h3>Miscellaneous Configuration Table Usage</h3>
                                    <ul>
                                        <xsl:if test="$editDivisions">
                                            <li>Divisions</li>
                                            <p>Mechanism for distinguishing sessions in reports only</p>
                                        </xsl:if>
                                        <xsl:if test="$editRegTypes">
                                            <li>RegTypes</li>
                                            <p>The type of member registration the participant has</p>
                                        </xsl:if>
                                    </ul>
                                </div>
                                <xsl:if test="$editDivisions">
                                    <div class="tab-pane mt-4 fade" id="divisions"/>
                                </xsl:if>
                                <xsl:if test="$editRegTypes">
                                    <div class="tab-pane mt-4 fade" id="regtypes"/>
                                </xsl:if>
                            </div>
                        </div>
                    </xsl:if>
                </div>
            </div>
            <div id="table-div" style="display: none">
                <div class="row mt-4">
                    <div class="col col-12 pl-4">
                        <div id="table" />
                    </div>
                </div>
                <div id="tceedit-div" style="display: none">
                    <div class="row mt-4">
                        <div class="col col-12">
                            <textarea id="tceedit-textarea" />
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col col-auto">
                        <button class="btn btn-secondary" id="undo" name="undo" value="undo" type="button"
                                onclick="Undo()" disabled="true">Undo
                        </button>
                    </div>
                    <div class="col col-auto">
                        <button class="btn btn-secondary" id="redo" name="redo" value="redo" type="button"
                                onclick="Redo()" disabled="true">Redo
                        </button>
                    </div>
                    <div class="col col-auto">
                        <button class="btn btn-secondary" id="add-row" name="add-row" value="new" type="button">Add
                            New
                        </button>
                    </div>
                    <div class="col col-auto">
                        <button class="btn btn-secondary" id="resetbtn" name="resetbtn" value="undo" type="button"
                                onclick="FetchTable()">Reset
                        </button>
                    </div>
                    <div class="col col-auto">
                        <button class="btn btn-primary" id="submitbtn" name="submitbtn" type="save" value="save"
                                onclick="SaveTable()">Save
                        </button>
                    </div>
                    <div id="saving_div" style="display: none;">
                        <span style="color:blue">
                            <b>Saving...</b>
                        </span>
                    </div>
                </div>
                <div class="clearboth mt-3">
                    <p class="mb-1">Click in the table to edit each field.</p>
                    <p class="mb-1">Drag slider icon to reorder the entries.</p>
                    <p class="mb-1">Click the trashcan to delete the row. Rows without trashcans are in use by the count of items shown.</p>
                    <p class="mb-1">Use the Add New button to add a row to the table.</p>
                </div>
            </div>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
