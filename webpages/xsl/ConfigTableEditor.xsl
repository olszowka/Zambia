<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Syd Weinstein on 2021-01-04;
    Copyright (c) 2021-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="UpdateMessage" select="''"/>
    <xsl:param name="control" select="''"/>
    <xsl:param name="controliv" select="''"/>

    <xsl:param name="mayEditAll" select="0"/>

    <!-- <xsl:param name="mayEditBioEditStatuses" select="0"/> BioEditStatuses not currently implemented -->
    <xsl:param name="mayEditCredentials" select="0"/>
    <xsl:param name="mayEditParticipantTags" select="0"/>
    <xsl:param name="mayEditPhotoDenialReasons" select="0"/>
    <xsl:param name="mayEditRoles" select="0"/>
    <xsl:param name="mayEditTimes" select="0"/>
    <xsl:param name="mayEditAnyParticipantTables" select="0"/>

    <xsl:param name="mayEditDivisions" select="0"/>
    <xsl:param name="mayEditKidsCategories" select="0"/>
    <!-- <xsl:param name="mayEditLanguageStatuses" select="0"/> LanguageStatuses not currently implemented -->
    <xsl:param name="mayEditPubStatuses" select="0"/>
    <xsl:param name="mayEditServices" select="0"/>
    <xsl:param name="mayEditSessionStatuses" select="0"/>
    <xsl:param name="mayEditTags" select="0"/>
    <xsl:param name="mayEditTracks" select="0"/>
    <xsl:param name="mayEditTypes" select="0"/>
    <xsl:param name="mayEditAnySessionTables" select="0"/>

    <xsl:param name="mayEditEmailCC" select="0"/>
    <xsl:param name="mayEditEmailFrom" select="0"/>
    <xsl:param name="mayEditEmailTo" select="0"/>
    <xsl:param name="mayEditAnyEmailTables" select="0"/>

    <xsl:param name="mayEditFeatures" select="0"/>
    <xsl:param name="mayEditRooms" select="0"/>
    <xsl:param name="mayEditRoomSets" select="0"/>
    <xsl:param name="mayEditRoomHasSet" select="0"/>
    <xsl:param name="mayEditAnyFacilityTables" select="0"/>

    <xsl:param name="mayEditRegTypes" select="0"/>
    <xsl:param name="mayEditAnyOtherTables" select="0"/>

    <xsl:output encoding="UTF-8" indent="yes" method="html"/>
    <xsl:template match="/">
        <div id="message" class="alert alert-success mt-4">
            <xsl:if test="$UpdateMessage = ''">
                <xsl:attribute name="style">
                    <xsl:text>display: none;</xsl:text>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="$UpdateMessage != ''">
                <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
            </xsl:if>
        </div>
        <div id="unsavedWarningModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Data Not Saved</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <p>
                            You have unsaved changes highlighted in the table below!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="cancelOpenSearchBUTN" class="btn btn-primary"
                                data-bs-dismiss="modal">Cancel
                        </button>
                        <button type="button" id="overrideOpenSearchBUTN" class="btn btn-secondary"
                                onclick="return discardChanges();">Discard changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="config-table-editor-nav" class="mt-3">
            <h1 style="text-align: center;">Configuration Table Editor</h1>
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-overview-tab" data-bs-toggle="pill" data-bs-target="#pills-overview"
                        type="button" role="tab" aria-controls="pills-overview" aria-selected="true">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="participants-top" data-bs-toggle="pill" data-bs-target="#pills-participants"
                        type="button" role="tab" aria-controls="pills-participants" aria-selected="true">
                        <xsl:choose>
                            <xsl:when test="$mayEditAnyParticipantTables = 1">
                                <xsl:attribute name="class">nav-link</xsl:attribute>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:text>Participants</xsl:text>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sessions-top" data-bs-toggle="pill" data-bs-target="#pills-sessions"
                        type="button" role="tab" aria-controls="pills-sessions" aria-selected="false">
                        <xsl:choose>
                            <xsl:when test="$mayEditAnySessionTables = 1">
                                <xsl:attribute name="class">nav-link</xsl:attribute>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:text>Sessions</xsl:text>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="email-top" data-bs-toggle="pill" data-bs-target="#pills-email"
                        type="button" role="tab" aria-controls="pills-email" aria-selected="false">
                        <xsl:choose>
                            <xsl:when test="$mayEditAnyEmailTables = 1">
                                <xsl:attribute name="class">nav-link</xsl:attribute>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:text>Email</xsl:text>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="facility-top" data-bs-toggle="pill" data-bs-target="#pills-facility"
                        type="button" role="tab" aria-controls="pills-facility" aria-selected="false">
                        <xsl:choose>
                            <xsl:when test="$mayEditAnyFacilityTables = 1">
                                <xsl:attribute name="class">nav-link</xsl:attribute>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:text>Facility</xsl:text>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="other-top" data-bs-toggle="pill" data-bs-target="#pills-other"
                        type="button" role="tab" aria-controls="pills-other" aria-selected="false">
                        <xsl:choose>
                            <xsl:when test="$mayEditAnyOtherTables = 1">
                                <xsl:attribute name="class">nav-link</xsl:attribute>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:text>Other</xsl:text>
                    </button>
                </li>
            </ul>
            <div class="tab-content mt-4" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab" tabindex="0">
                    <h2>How to use the Configuration Table Editor</h2>
                    <p>
                        The editor is broken down into sections for each type configuration table.
                        <dl class="ms-4">
                            <dt>Participants</dt>
                            <dd>Tables controlling participant pages</dd>
                            <dt>Sessions</dt>
                            <dd>Tables of properties associated with sessions</dd>
                            <dt>Email</dt>
                            <dd>Tables that control sending email</dd>
                            <dt>Facility</dt>
                            <dd>Tables that specify the facility being used for the convention</dd>
                            <dt>Other</dt>
                            <dd>The remaining tables which do not fall into the categories above</dd>
                        </dl>
                    </p>
                    <p>Each section has a description tab for the tables available in that section. This explains
                        the usage of each of the tables.
                    </p>
                    <p>Each table has its own tab. The database table will be presented as an html table which is
                        editable in place. Changes are not stored in the database until the save button is pressed.
                    </p>
                </div>
                <div class="tab-pane fade show" id="pills-participants" role="tabpanel" aria-labelledby="part-top" tabindex="1">
                    <h2>Participants Configuration Tables</h2>
                    <ul class="nav nav-pills" id="participants-pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="participants-overview-tab" data-bs-toggle="pill" data-bs-target="#participants-overview"
                                type="button" role="tab" aria-controls="participants-overview" aria-selected="true">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" id="t-BioEditStatuses" data-bs-toggle="pill" data-bs-target="#bioeditstatuses-table"
                                type="button" role="tab" aria-controls="bioeditstatuses-table" aria-selected="false" data-top="participants-top">BioEditStatuses</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Credentials" data-bs-toggle="pill" data-bs-target="#credentials-table" type="button"
                                role="tab" aria-controls="credentials-table" aria-selected="false" data-top="participants-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditCredentials = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Credentials</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-ParticipantTags" data-bs-toggle="pill" data-bs-target="#participanttags-table" type="button"
                                role="tab" aria-controls="participanttags-table" aria-selected="false" data-top="participants-top">
                                <xsl:choose>
                                    <xsl:when test="$mayEditParticipantTags = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>ParticipantTags</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-PhotoDenialReasons" data-bs-toggle="pill" data-bs-target="#photodenialreasons-table" type="button"
                                role="tab" aria-controls="photodenialreasons-table" aria-selected="false" data-top="participants-top">
                                <xsl:choose>
                                    <xsl:when test="$mayEditPhotoDenialReasons = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>PhotoDenialReasons</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Roles" data-bs-toggle="pill" data-bs-target="#roles-table" type="button"
                                role="tab" aria-controls="roles-table" aria-selected="false" data-top="participants-top">
                                <xsl:choose>
                                    <xsl:when test="$mayEditRoles = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Roles</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Times" data-bs-toggle="pill" data-bs-target="#times-table" type="button"
                                role="tab" aria-controls="times-table" aria-selected="false" data-top="participants-top">
                                <xsl:choose>
                                    <xsl:when test="$mayEditTimes = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Times</xsl:text>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mt-4" id="participants-tabContent">
                        <div class="tab-pane fade show active" id="participants-overview" role="tabpanel" aria-labelledby="participants-overview-tab" tabindex="10">
                            <H3>Participant Configuration Table Usage</H3>
                            <dl class="ms-4 mt-4">
                                <dt>BioEditStatuses:</dt>
                                <dd>(currently unused) Tracking workflow for participant biography proofreading and translating</dd>
                                <dt>Credentials:</dt>
                                <dd>List of Professions in Participant Profile</dd>
                                <dt>ParticipantTags:</dt>
                                <dd>For use by staff to categorize participants</dd>
                                <dt>PhotoDenialReasons:</dt>
                                <dd>For use by staff to explain denial of uploaded particpant photo</dd>
                                <dt>Roles:</dt>
                                <dd>"Roles I'm willing to take on" in Particpant General Interests</dd>
                                <dt>Times:</dt>
                                <dd>Controls the "Times" dropdown on the "Availability" page</dd>
                            </dl>
                        </div>
                        <div class="tab-pane fade" id="bioeditstatuses-table" role="tabpanel" aria-labelledby="t-BioEditStatuses" tabindex="11" />
                        <div class="tab-pane fade" id="credentials-table" role="tabpanel" aria-labelledby="t-Credentials" tabindex="12" />
                        <div class="tab-pane fade" id="participanttags-table" role="tabpanel" aria-labelledby="t-ParticipantTags" tabindex="13" />
                        <div class="tab-pane fade" id="photodenialreasons-table" role="tabpanel" aria-labelledby="t-PhotoDenialReasons" tabindex="14" />
                        <div class="tab-pane fade" id="roles-table" role="tabpanel" aria-labelledby="t-Roles" tabindex="15" />
                    </div>
                </div>
                <div class="tab-pane fade show" id="pills-sessions" role="tabpanel" aria-labelledby="sessions-top" tabindex="2">
                    <h2>Sessions Configuration Tables</h2>
                    <ul class="nav nav-pills" id="sessions-pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sessions-overview-tab" data-bs-toggle="pill" data-bs-target="#sessions-overview"
                                type="button" role="tab" aria-controls="sessions-overview" aria-selected="true">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Divisions" data-bs-toggle="pill" data-bs-target="#divisions-table" type="button"
                                role="tab" aria-controls="divisions-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditDivisions = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Divisions</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-KidsCategories" data-bs-toggle="pill" data-bs-target="#divisions-table" type="button"
                                role="tab" aria-controls="kidscategories-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditKidsCategories = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>KidsCategories</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-LanguageStatuses" data-bs-toggle="pill" data-bs-target="#languagestatuses-table" type="button" role="tab"
                                aria-controls="languagestatuses-table" aria-selected="false" data-top="sessions-top" class="nav-link disabled">
                                <xsl:text>LanguageStatuses</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-PubStatuses" data-bs-toggle="pill" data-bs-target="#pubstatuses-table" type="button"
                                role="tab" aria-controls="pubstatuses-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditPubStatuses = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>PubStatuses</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Services" data-bs-toggle="pill" data-bs-target="#services-table" type="button"
                                role="tab" aria-controls="services-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditServices = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Services</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-SessionStatuses" data-bs-toggle="pill" data-bs-target="#sessionstatuses-table" type="button"
                                role="tab" aria-controls="sessionstatuses-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditSessionStatuses = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>SessionStatuses</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Tags" data-bs-toggle="pill" data-bs-target="#tags-table" type="button"
                                role="tab" aria-controls="tags-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditTags = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Tags</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Tracks" data-bs-toggle="pill" data-bs-target="#tracks-table" type="button"
                                role="tab" aria-controls="tracks-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditTracks = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Tracks</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Types" data-bs-toggle="pill" data-bs-target="#types-table" type="button"
                                role="tab" aria-controls="types-table" aria-selected="false" data-top="sessions-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditTypes = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Types</xsl:text>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mt-4" id="sessions-tabContent">
                        <div class="tab-pane fade show active" id="sessions-overview" role="tabpanel" aria-labelledby="sessions-overview-tab" tabindex="20">
                            <h3>Session Configuration Table Usage</h3>
                            <dl class="ms-4 mt-4">
                                <dt>Divisions</dt>
                                <dd>A property of a session intended to categorize them by convention division.  If you don't need to use this, configure a
                                    single entry with divisionid = 1 and this field will be hidden automatically from sessions entry and search forms.</dd>
                                <dt>KidsCategories</dt>
                                <dd>A property of a session intended to categorize them by appropriateness for children.  If you don't need to use this, configure a
                                    single entry with kidscatid = 1 and this field will be hidden automatically from sessions entry.</dd>
                                <dt>LanguageStatuses</dt>
                                <dd>A property of a session intended to categorize them by language and translation status.  This property is currently not implemented.</dd>
                                <dt>PubStatuses</dt>
                                <dd>A property of a session intended to indicate whether and how this session should be published.</dd>
                                <dt>Services</dt>
                                <dd>A list of things which need to be provided for the session. If you don't need to use this, configure the table completely empty
                                    and this field with be hidden automatically from session entry.</dd>
                                <dt>SessionStatuses</dt>
                                <dd>The stages in the workflow for a session.  Edit with caution.</dd>
                                <dt>Tags</dt>
                                <dd>A list of keywords which can be associated with a session for searching.  If you don't need this, configure TRACK_TAG_USAGE = "TRACK_ONLY" in db_name.php</dd>
                                <dt>Tracks</dt>
                                <dd>A property of a session intended to categorize by topic which may be published or used for dividing scheduling work. If you've disabled this by setting
                                    TRACK_TAG_USAGE = "TAG_ONLY" in db_name.php, leave a single row in the table with trackid = 1.</dd>
                                <dt>Types</dt>
                                <dd>A property of a session intended to categorize by structure or format of the session.</dd>
                            </dl>
                        </div>
                        <div class="tab-pane fade" id="divisions-table" role="tabpanel" aria-labelledby="t-Divisions" tabindex="21" />
                        <div class="tab-pane fade" id="kidscategories-table" role="tabpanel" aria-labelledby="t-KidsCategories" tabindex="22" />
                        <div class="tab-pane fade" id="languagestatuses-table" role="tabpanel" aria-labelledby="t-LanguageStatuses" tabindex="23" />
                        <div class="tab-pane fade" id="pubstatuses-table" role="tabpanel" aria-labelledby="t-PubStatuses" tabindex="24" />
                        <div class="tab-pane fade" id="services-table" role="tabpanel" aria-labelledby="t-Services" tabindex="25" />
                        <div class="tab-pane fade" id="sessionstatuses-table" role="tabpanel" aria-labelledby="t-SessionStatuses" tabindex="26" />
                        <div class="tab-pane fade" id="tags-table" role="tabpanel" aria-labelledby="t-Tags" tabindex="27" />
                        <div class="tab-pane fade" id="tracks-table" role="tabpanel" aria-labelledby="t-Tracks" tabindex="28" />
                        <div class="tab-pane fade" id="types-table" role="tabpanel" aria-labelledby="t-Types" tabindex="29" />
                    </div>
                </div>
                <div class="tab-pane fade show" id="pills-email" role="tabpanel" aria-labelledby="email-top" tabindex="3">
                    <h2>Email Configuration Tables</h2>
                    <ul class="nav nav-pills" id="email-pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="email-overview-tab" data-bs-toggle="pill" data-bs-target="#email-overview"
                                type="button" role="tab" aria-controls="email-overview" aria-selected="true">
                                Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-EmailCC" data-bs-toggle="pill" data-bs-target="#emailcc-table" type="button"
                                role="tab" aria-controls="emailcc-table" aria-selected="false" data-top="email-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditEmailCC = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>EmailCC</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-EmailFrom" data-bs-toggle="pill" data-bs-target="#emailfrom-table" type="button"
                                role="tab" aria-controls="emailfrom-table" aria-selected="false" data-top="email-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditEmailFrom = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>EmailFrom</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-EmailTo" data-bs-toggle="pill" data-bs-target="#emailto-table" type="button"
                                role="tab" aria-controls="emailto-table" aria-selected="false" data-top="email-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditEmailTo = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>EmailTo</xsl:text>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mt-4" id="email-tabContent">
                        <div class="tab-pane fade show active" id="email-overview" role="tabpanel" aria-labelledby="email-overview-tab" tabindex="30">
                            <h3>Email Configuration Table Usage</h3>
                            <dl class="ms-4 mt-4">
                                <dt>EmailCC</dt>
                                <dd>The list of email addresses which may be specified in the "CC" field of an email sent from Zambia</dd>
                                <dt>EmailFrom</dt>
                                <dd>The list of email addresses which may be specified in the "From" field of an email sent from Zambia</dd>
                                <dt>EmailTo</dt>
                                <dd>The list of possible subgroups of Zambia users who may be chosen as the destination of an email sent from Zambia.  Note,
                                    security policy of some servers prevents editing this table from this tool.</dd>
                            </dl>
                        </div>
                        <div class="tab-pane fade" id="emailcc-table" role="tabpanel" aria-labelledby="t-EmailCC" tabindex="31" />
                        <div class="tab-pane fade" id="emailfrom-table" role="tabpanel" aria-labelledby="t-EmailFrom" tabindex="32" />
                        <div class="tab-pane fade" id="emailto-table" role="tabpanel" aria-labelledby="t-EmailTo" tabindex="33" />
                    </div>
                </div>
                <div class="tab-pane fade show" id="pills-facility" role="tabpanel" aria-labelledby="facility-top" tabindex="4">
                    <h2>Facility Configuration Tables</h2>
                    <ul class="nav nav-pills" id="email-pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="facility-overview-tab" data-bs-toggle="pill" data-bs-target="#facility-overview"
                                type="button" role="tab" aria-controls="facility-overview" aria-selected="true">
                                Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Features" data-bs-toggle="pill" data-bs-target="#features-table" type="button"
                                    role="tab" aria-controls="features-table" aria-selected="false" data-top="facility-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditFeatures = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Features</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-Rooms" data-bs-toggle="pill" data-bs-target="#rooms-table" type="button"
                                role="tab" aria-controls="rooms-table" aria-selected="false" data-top="facility-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditRooms = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>Rooms</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-RoomSets" data-bs-toggle="pill" data-bs-target="#roomsets-table" type="button"
                                role="tab" aria-controls="roomsets-table" aria-selected="false" data-top="facility-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditRoomSets = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>RoomSets</xsl:text>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-RoomHasSet" data-bs-toggle="pill" data-bs-target="#roomhasset-table" type="button"
                                role="tab" aria-controls="roomhasset-table" aria-selected="false" data-top="facility-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditRoomHasSet = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>RoomHasSet</xsl:text>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mt-4" id="facility-tabContent">
                        <div class="tab-pane fade show active" id="facility-overview" role="tabpanel" aria-labelledby="facility-overview-tab" tabindex="40">
                            <h3>Facility Configuration Table Usage</h3>
                            <dl class="ms-4 mt-4">
                                <dt>Features</dt>
                                <dd>The required characteristics of a location needed for a session to be scheduled in that location.  If you don't need to use this,
                                    configure the table completely empty and this field with be hidden automatically from session entry.</dd>
                                <dt>Rooms</dt>
                                <dd>The list of locations where a session may be scheduled.</dd>
                                <dt>RoomSets</dt>
                                <dd>The furniture layout in a location. If you don't need to use this, configure a single entry with roomsetid = 1 and this
                                    field will be hidden automatically from sessions entry.</dd>
                                <dt>RoomHasSet</dt>
                                <dd>Which furniture layouts are available for each room and what the resulting attendance capacity is.</dd>
                            </dl>
                        </div>
                        <div class="tab-pane fade" id="features-table" role="tabpanel" aria-labelledby="t-Features" tabindex="41" />
                        <div class="tab-pane fade" id="rooms-table" role="tabpanel" aria-labelledby="t-Rooms" tabindex="42" />
                        <div class="tab-pane fade" id="roomsets-table" role="tabpanel" aria-labelledby="t-RoomSets" tabindex="43" />
                        <div class="tab-pane fade" id="roomhasset-table" role="tabpanel" aria-labelledby="t-RoomHasSet" tabindex="44" />
                    </div>
                </div>
                <div class="tab-pane fade show" id="pills-other" role="tabpanel" aria-labelledby="other-top" tabindex="5">
                    <h2>Other Configuration Tables</h2>
                    <ul class="nav nav-pills" id="other-pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="other-overview-tab" data-bs-toggle="pill" data-bs-target="#other-overview"
                                type="button" role="tab" aria-controls="other-overview" aria-selected="true">
                                Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="t-RegTypes" data-bs-toggle="pill" data-bs-target="#regtypes-table" type="button"
                                role="tab" aria-controls="regtypes-table" aria-selected="false" data-top="other-top" >
                                <xsl:choose>
                                    <xsl:when test="$mayEditRegTypes = 1">
                                        <xsl:attribute name="class">nav-link</xsl:attribute>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">nav-link disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text>RegTypes</xsl:text>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mt-4" id="other-tabContent">
                        <div class="tab-pane fade show active" id="other-overview" role="tabpanel" aria-labelledby="other-overview-tab" tabindex="50">
                            <h3>Other Configuration Table Usage</h3>
                            <dl class="ms-4 mt-4">
                                <dt>RegTypes</dt>
                                <dd>Possible membership types participants may be assigned by the registration system.</dd>
                            </dl>
                        </div>
                        <div class="tab-pane fade" id="regtypes-table" role="tabpanel" aria-labelledby="t-RegTypes" tabindex="51" />
                    </div>
                </div>
            </div>
        </div>
        <xsl:if test="not ($mayEditAll = 1)">
            <div class="row mt-4">
                <div class="col col-12">
                    You have permission to edit only some tables, so some pages or tables will be disabled.
                </div>
            </div>
        </xsl:if>
        <div id="table-div" style="display: none" class="fade">
            <div class="row mt-4">
                <div class="col col-12">
                    <div id="table"></div>
                </div>
            </div>
            <div id="tceedit-div" style="display: none">
                <div class="row mt-4">
                    <div class="col col-12">
                        <textarea id="tceedit-textarea"></textarea>
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
                <p>Click in the table to edit each field.</p>
                <p>Drag slider icon to reorder the entries.</p>
                <p>Click the trashcan to delete the row. Rows without trashcans are in use by the count of items shown.</p>
                <p>Use the Add New button to add a row to the table.</p>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
