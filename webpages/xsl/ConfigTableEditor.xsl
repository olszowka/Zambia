<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2021-01-04;
	Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="UpdateMessage" select="''"/>
  <xsl:param name="control" select="''"/>
  <xsl:param name="controliv" select="''"/>
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
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
    <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='Administrator']">
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
              <button type="button" id="cancelOpenSearchBUTN" class="btn btn-primary" data-dismiss="modal">Cancel</button>
              <button type="button" id="overrideOpenSearchBUTN" class="btn btn-secondary" onclick="return discardChanges();" >Discard changes</button>
            </div>
          </div>
        </div>
      </div>
      <div>
        <h1 style="text-align: center;">Configuration Table Editor</h1>
        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a href="#overview" class="nav-link active" data-toggle="tab" id="overview-top">Overview</a>
          </li>
          <li class="nav-item">
            <a href="#part" class="nav-link" data-toggle="tab" id="part-top">Participants</a>
          </li>
          <li class="nav-item">
            <a href="#session" class="nav-link" data-toggle="tab" id="session-top">Sessions</a>
          </li>
          <li class="nav-item">
            <a href="#layout" class="nav-link" data-toggle="tab" id="layout-top">Layout</a>
          </li>
          <li class="nav-item">
            <a href="#facility" class="nav-link" data-toggle="tab" id="facility-top">Facility</a>
          </li>
          <li class="nav-item">
            <a href="#emails" class="nav-link" data-toggle="tab" id="emails-top">Emails</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show mt-4 active" id="overview">
            <h2>How to use the Configuration Table Editor</h2>
            <p>
              The editor is broken down into sections for each type configuration table.
              <ul>
                <li>Participants: Tables that effect the participant screens/reports</li>
                <li>Sessions: Tables that effect the session screens/reports</li>
                <li>Layout: Tables that define how the convention defines its organization and scheduling</li>
                <li>Facility: Tables that define the hotel or other facility being used for the convention</li>
                <li>Emails: Tables that define Zambia's ability to send emails to participants</li>
              </ul>
            </p>
            <p>Each section has a description tab for the tables available in that section.  This explains the usage of each of the tables.</p>
            <p>Each table has its own tab. The database table will be presented as an html table which is editable in place.  Changes are not stored in the database until the save button is pressed.</p>
          </div>
          <div class="tab-pane fade show mt-4" id="part">
            <h2>Participant Configuration Tables</h2>
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a href="#partdesc" class="nav-link active" data-toggle="tab" id="part-overview">Participant Configuration Tables</a>
              </li>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_BioEditStatuses']">
                <li class="nav-item">
                  <a href="#bioeditstatuses" class="nav-link" data-toggle="tab" data-top="part-top" id="t-BioEditStatuses">BioEditStatuses</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Credentials']">
                <li class="nav-item">
                  <a href="#credentials" class="nav-link" data-toggle="tab" data-top="part-top" id="t-Credentials">Credentials</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Roles']">
                <li class="nav-item">
                  <a href="#roles" class="nav-link" data-toggle="tab" data-top="part-top" id="t-Roles">Roles</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Interests']">
                <li class="nav-item">
                  <a href="#interests" class="nav-link" data-toggle="tab" data-top="part-top" id="t-Interests">Interests</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_AgeRanges']">
                <li class="nav-item">
                  <a href="#ageranges" class="nav-link" data-toggle="tab" data-top="part-top" id="t-AgeRanges">Age Ranges</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Pronouns']">
                <li class="nav-item">
                  <a href="#pronouns" class="nav-link" data-toggle="tab" data-top="part-top" id="t-Pronouns">Pronouns</a>
                </li>
              </xsl:if>
            </ul>
            <div class="tab-content">
              <div class="tab-pane mt-4 fade show active" id="partdesc">
                <H3>Participant Configuration Table Usage</H3>
                <ul>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_BioEditStatuses']">
                    <li>BioEditStatuses:</li>
                    <p>(currently unused) Participant Biography Editing</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Credentials']">
                    <li>Credentials:</li>
                    <p>Credentials list for Professions in Participant Profile</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Roles']">
                    <li>Roles</li>
                    <p>"Roles I'm willing to take on" in Particpant General Interests</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Interests']">
                    <li>Interests</li>
                    <p>Interests of the participant</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_AgeRanges']">
                    <li>Age Ranges</li>
                    <p>Age Ranges of the participant</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Pronouns']">
                    <li>Pronouns</li>
                    <p>Pronouns of the participant</p>
                  </xsl:if>
                </ul>
              </div>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_BioEditStatuses']">
                <div class="tab-pane mt-4 fade" id="bioeditstatuses"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Credentials']">
                <div class="tab-pane mt-4 fade" id="credentials"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Roles']">
                <div class="tab-pane mt-4 fade" id="roles"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Interests']">
                <div class="tab-pane mt-4 fade" id="interests"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_AgeRanges']">
                <div class="tab-pane mt-4 fade" id="ageranges"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Pronouns']">
                <div class="tab-pane mt-4 fade" id="pronouns"/>
              </xsl:if>
            </div>
          </div>
          <div class="tab-pane mt-4 fade" id="session">
            <h2>Session Configuration Tables</h2>
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a href="#sessiondesc" class="nav-link active" data-toggle="tab">Session Configuration Tables</a>
              </li>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_KidsCategories']">
                <li class="nav-item">
                  <a href="#kidscategories" class="nav-link" data-toggle="tab" data-top="session-top" id="t-KidsCategories">KidsCategories</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_LanguageStatuses']">
                <li class="nav-item">
                  <a href="#languagestatuses" class="nav-link" data-toggle="tab" data-top="session-top" id="t-LanguageStatuses">LanguageStatuses</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_PubStatuses']">
                <li class="nav-item">
                  <a href="#pubstatuses" class="nav-link" data-toggle="tab" data-top="session-top" id="t-PubStatuses">PubStatuses</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_SessionStatuses']">
                <li class="nav-item">
                  <a href="#sessionstatuses" class="nav-link" data-toggle="tab" data-top="session-top" id="t-SessionStatuses">SessionStatuses</a>
                </li>
              </xsl:if>
            </ul>
            <div class="tab-content">
              <div class="tab-pane mt-4 fade show active" id="sessiondesc">
                <H3>Session Configuration Table Usage</H3>
                <ul>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_KidsCategories']">
                    <li>KidsCategories</li>
                    <p>Entries in the Kids pulldown on the session pages/reports</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_LanguageStatuses']">
                    <li>LanguageStatuses</li>
                    <p>Language list for "Session Language"</p>
                  </xsl:if> 
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_PubStatuses']">
                    <li>PubStatuses</li>
                    <p>Publication Statuses of Sessions</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_SessionStatuses']">
                    <li>SessionStatuses</li>
                    <p>Session Status List</p>
                  </xsl:if>
                </ul>
              </div>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_KidsCategories']">
                <div class="tab-pane mt-4 fade" id="kidscategories"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_LanguageStatuses']">
                <div class="tab-pane mt-4 fade" id="languagestatuses"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_PubStatuses']">
                <div class="tab-pane mt-4 fade" id="pubstatuses"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_SessionStatuses']">
                <div class="tab-pane mt-4 fade" id="sessionstatuses"/>
              </xsl:if>
            </div>
          </div>
          <div class="tab-pane mt-4 fade" id="layout">
            <h2>Convention Layout Tables</h2>
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a href="#layoutdesc" class="nav-link active" data-toggle="tab">Convention Layout Configuration Tables</a>
              </li>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Divisions']">
                <li class="nav-item">
                  <a href="#divisions" class="nav-link" data-toggle="tab" data-top="layout-top" id="t-Divisions">Divisions</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RegTypes']">
                <li class="nav-item">
                  <a href="#regtypes" class="nav-link" data-toggle="tab" data-top="layout-top" id="t-RegTypes">RegTypes</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tags']">
                <li class="nav-item">
                  <a href="#tags" class="nav-link" data-toggle="tab" data-top="layout-top" id="t-Tags">Tags</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Times']">
                <li class="nav-item">
                  <a href="#times" class="nav-link" data-toggle="tab" data-top="layout-top" id="t-Times">Times</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tracks']">
                <li class="nav-item">
                  <a href="#tracks" class="nav-link" data-toggle="tab" data-top="layout-top" id="t-Tracks">Tracks</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Types']">
                <li class="nav-item">
                  <a href="#Types" class="nav-link" data-toggle="tab" data-top="layout-top" id="t-Types">Types</a>
                </li>
              </xsl:if>
            </ul>
            <div class="tab-content">
              <div class="tab-pane mt-4 fade show active" id="layoutdesc">
                <H3>Convention Layout Configuration Table Usage</H3>
                <ul>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Divisions']">
                    <li>Divisions</li>
                    <p>Groups responsible for parts of the convention (Programming, Events, ...)"</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RegTypes']">
                    <li>RegTypes</li>
                    <p>Registration System types imported or entered in add new participant</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tags']">
                    <li>Tags</li>
                    <p>The list of potential tags assignable to a session</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Times']">
                    <li>Times</li>
                    <p>Times sessions can start for use in the grid scheduler</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tracks']">
                    <li>Tracks</li>
                    <p>The lists of tracks that for sessions</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Types']">
                    <li>Types</li>
                    <p>The list of Session Types (Panel, Workshop, ...)</p>
                  </xsl:if>
                </ul>
              </div>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Divisions']">
                <div class="tab-pane mt-4 fade" id="divisions"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RegTypes']">
                <div class="tab-pane mt-4 fade" id="regtypes"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tags']">
                <div class="tab-pane mt-4 fade" id="tags"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Times']">
                <div class="tab-pane mt-4 fade" id="times"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Tracks']">
                <div class="tab-pane mt-4 fade" id="tracks"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Types']">
                <div class="tab-pane mt-4 fade" id="types"/>
              </xsl:if>
            </div>
          </div>
          <div class="tab-pane mt-4 fade" id="facility">
            <h2>Facility Configuration Tables</h2>
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a href="#facilitydesc" class="nav-link active" data-toggle="tab">Facility Configuration Tables</a>
              </li>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Rooms']">
                <li class="nav-item">
                  <a href="#rooms" class="nav-link" data-toggle="tab" data-top="facility-top" id="t-Rooms">Rooms</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomSets']">
                <li class="nav-item">
                  <a href="#roomsets" class="nav-link" data-toggle="tab" data-top="facility-top" id="t-RoomSets">RoomSets</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomHasSet']">
                <li class="nav-item">
                  <a href="#roomhasset" class="nav-link" data-toggle="tab" data-top="facility-top" id="t-RoomHasSet">RoomHasSet</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Features']">
                <li class="nav-item">
                  <a href="#features" class="nav-link" data-toggle="tab" data-top="facility-top" id="t-Features">Features</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Services']">
                <li class="nav-item">
                  <a href="#Services" class="nav-link" data-toggle="tab" data-top="facility-top" id="t-Services">Services</a>
                </li>
              </xsl:if>
            </ul>
            <div class="tab-content">
              <div class="tab-pane mt-4 fade show active" id="facilitydesc">
                <H3>Facility Configuration Table Usage</H3>
                <ul>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Rooms']">
                    <li>Rooms</li>
                    <p>List of rooms a session could be scheduled into"</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomSets']">
                    <li>RoomSets</li>
                    <p>Types of setups for any room</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomHasSet']">
                    <li>RoomHasSet</li>
                    <p>Which rooms can have which RoomSets</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Features']">
                    <li>Features</li>
                    <p>Features available in a room for use by sessions</p>
                  </xsl:if>
                  <li>Services</li>
                  <p>Services available in a room for use by sessions</p>
                </ul>
              </div>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Rooms']">
                <div class="tab-pane mt-4 fade" id="rooms"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomSets']">
                <div class="tab-pane mt-4 fade" id="roomsets"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_RoomHasSet']">
                <div class="tab-pane mt-4 fade" id="roomhasset"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Features']">
                <div class="tab-pane mt-4 fade" id="features"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_Services']">
                <div class="tab-pane mt-4 fade" id="services"/>
              </xsl:if>
            </div>
          </div>
          <div class="tab-pane mt-4 fade" id="emails">
            <h2>Email Configuration Tables</h2>
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a href="#emaildesc" class="nav-link active" data-toggle="tab">Email Configuration Tables</a>
              </li>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailFrom']">
                <li class="nav-item">
                  <a href="#emailfrom" class="nav-link" data-toggle="tab" data-top="emails-top" id="t-EmailFrom">EmailFrom</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailTo']">
                <li class="nav-item">
                  <a href="#emailto" class="nav-link" data-toggle="tab" data-top="emails-top" id="t-EmailTo">EmailTo</a>
                </li>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailCC']">
                <li class="nav-item">
                  <a href="#emailcc" class="nav-link" data-toggle="tab" data-top="emails-top" id="t-EmailCC">EmailCC</a>
                </li>
              </xsl:if>
            </ul>
            <div class="tab-content">
              <div class="tab-pane mt-4 fade show active" id="emaildesc">
                <H3>Email Configuration Table Usage</H3>
                <ul>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailFrom']">
                    <li>EmailFrom</li>
                    <p>List of From: lines for emails sent by Zambia</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailTo']">
                    <li>EmailTo</li>
                    <p>List of queries to select emails to be sent by Zambia</p>
                  </xsl:if>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailCC']">
                    <li>EmailCC</li>
                    <p>List of potential CC address choices for emails sent by Zambia</p>
                  </xsl:if>
                </ul>
              </div>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailFrom']">
                <div class="tab-pane mt-4 fade" id="emailfrom"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailTo']">
                <div class="tab-pane mt-4 fade" id="emailto"/>
              </xsl:if>
              <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='ce_EmailCC']">
                <div class="tab-pane mt-4 fade" id="emailcc"/>
              </xsl:if>
            </div>
          </div>
        </div>
      </div>
      <div id="table-div" style="display: none">
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
            <button class="btn btn-secondary" id="undo" name="undo" value="undo" type="button" onclick="Undo()" disabled="true">Undo</button>
          </div>
          <div class="col col-auto">
            <button class="btn btn-secondary" id="redo" name="redo" value="redo" type="button" onclick="Redo()" disabled="true">Redo</button>
          </div>
          <div class="col col-auto">
            <button class="btn btn-secondary" id="add-row" name="add-row" value="new" type="button">Add New</button>
          </div>
          <div class="col col-auto">
            <button class="btn btn-secondary" id="resetbtn" name="resetbtn" value="undo" type="button" onclick="FetchTable()">Reset</button>
          </div>
          <div class="col col-auto">
            <button class="btn btn-primary" id="submitbtn" name="submitbtn" type="save" value="save" onclick="SaveTable()">Save</button>
          </div>
          <div id="saving_div" style="display: none;">
            <span style="color:blue">
              <b>Saving...</b>
            </span>
          </div>
        </div>
        <div class="clearboth">
          <p>
            Click in the table to edit each field.<br/>
            Drag slider icon to reorder the entries.<br/>
            Click the trashcan to delete the row. Rows without trashcans are in use by the count of items shown.<br/>
            Use the Add New button to add a row to the table.
          </p>
        </div>
      </div>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>