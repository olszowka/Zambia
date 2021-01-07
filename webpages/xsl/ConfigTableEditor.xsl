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
    <div>
      <h1 style="text-align: center;">Configuration Table Editor</h1>
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a href="#overview" class="nav-link active" data-toggle="tab">Overview</a>
        </li>
        <li class="nav-item">
          <a href="#part" class="nav-link" data-toggle="tab">Participants</a>
        </li>
        <li class="nav-item">
          <a href="#session" class="nav-link" data-toggle="tab">Sessions</a>
        </li>
        <li class="nav-item">
          <a href="#layout" class="nav-link" data-toggle="tab">Layout</a>
        </li>
        <li class="nav-item">
          <a href="#facility" class="nav-link" data-toggle="tab">Facility</a>
        </li>
        <li class="nav-item">
          <a href="#emails" class="nav-link" data-toggle="tab">Emails</a>
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
              <a href="#partdesc" class="nav-link active" data-toggle="tab">Participant Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#bioeditstatuses" class="nav-link" data-toggle="tab" id="t-BioEditStatuses">BioEditStatuses</a>
            </li>
            <li class="nav-item">
              <a href="#credentials" class="nav-link" data-toggle="tab" id="t-Credentials">Credentials</a>
            </li>
            <li class="nav-item">
              <a href="#roles" class="nav-link" data-toggle="tab" id="t-Roles">Roles</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane mt-4 fade show active" id="partdesc">
              <H3>Participant Configuration Table Usage</H3>
              <ul>
                <li>BioEditStatuses:</li>
                <p>(currently unused) Participant Biography Editing</p>
                <li>Credentials:</li>
                <p>Credentials list for Professions in Participant Profile</p>
                <li>Roles</li>
                <p>"Roles I'm willing to ake on" in Particpant General Interests</p>
              </ul>
            </div>
            <div class="tab-pane mt-4 fade" id="bioeditstatuses"/>
            <div class="tab-pane mt-4 fade" id="credentials"/>
            <div class="tab-pane mt-4 fade" id="roles"/>
          </div> 
        </div>
        <div class="tab-pane mt-4 fade" id="session">
          <h2>Session Configuration Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#sessiondesc" class="nav-link active" data-toggle="tab">Session Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#kidscategories" class="nav-link" data-toggle="tab" id="t-KidsCategories">KidsCategories</a>
            </li>
            <li class="nav-item">
              <a href="#languagestatuses" class="nav-link" data-toggle="tab" id="t-LanguageStatuses">LanguageStatuses</a>
            </li>
            <li class="nav-item">
              <a href="#pubstatuses" class="nav-link" data-toggle="tab" id="t-PubStatuses">PubStatuses</a>
            </li>
            <li class="nav-item">
              <a href="#sessionstatuses" class="nav-link" data-toggle="tab" id="t-SessionStatuses">SessionStatuses</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane mt-4 fade show active" id="sessiondesc">
              <H3>Session Configuration Table Usage</H3>
              <ul>
                <li>KidsCategories</li>
                <p>Entries in the Kids pulldown on the session pages/reports</p>
                <li>LanguageStatuses</li>
                <p>Language list for "Session Language"</p>
                <li>PubStatuses</li>
                <p>Publication Statuses of Sessions</p>
                <li>SessionStatuses</li>
                <p>Session Status List</p>
              </ul>
            </div>  
            <div class="tab-pane mt-4 fade" id="kidscategories"/>
            <div class="tab-pane mt-4 fade" id="languagestatuses"/>
            <div class="tab-pane mt-4 fade" id="pubstatuses"/>
            <div class="tab-pane mt-4 fade" id="sessionstatuses"/>
          </div> 
        </div>
        <div class="tab-pane mt-4 fade" id="layout">
          <h2>Convention Layout Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#layoutdesc" class="nav-link active" data-toggle="tab">Convention Layout Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#divisions" class="nav-link" data-toggle="tab" id="t-Divisions">Divisions</a>
            </li>
            <li class="nav-item">
              <a href="#regtypes" class="nav-link" data-toggle="tab" id="t-RegTypes">RegTypes</a>
            </li>
            <li class="nav-item">
              <a href="#tags" class="nav-link" data-toggle="tab" id="t-Tags">Tags</a>
            </li>
            <li class="nav-item">
              <a href="#times" class="nav-link" data-toggle="tab" id="t-Times">Times</a>
            </li>
            <li class="nav-item">
              <a href="#tracks" class="nav-link" data-toggle="tab" id="t-Tracks">Tracks</a>
            </li>
            <li class="nav-item">
              <a href="#Types" class="nav-link" data-toggle="tab" id="t-Types">Types</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane mt-4 fade show active" id="layoutdesc">
              <H3>Convention Layout Configuration Table Usage</H3>
              <ul>
                <li>Divisions</li>
                <p>Groups responsible for parts of the convention (Programming, Events, ...)"</p>
                <li>RegTypes</li>
                <p>Registration System types imported or entered in add new participant</p>
                <li>Tags</li>
                <p>The list of potential tags assignable to a session</p>
                <li>Times</li>
                <p>Times sessions can start for use in the grid scheduler</p>
                <li>Tracks</li>
                <p>The lists of tracks that for sessions</p>
                <li>Types</li>
                <p>The list of Session Types (Panel, Workshop, ...)</p>
              </ul>
            </div>
            <div class="tab-pane mt-4 fade" id="divisions"/>
            <div class="tab-pane mt-4 fade" id="regtypes"/>
            <div class="tab-pane mt-4 fade" id="tags"/>
            <div class="tab-pane mt-4 fade" id="times"/>
            <div class="tab-pane mt-4 fade" id="tracks"/>
            <div class="tab-pane mt-4 fade" id="types"/>
          </div>
        </div>
        <div class="tab-pane mt-4 fade" id="facility">
          <h2>Facility Configuration Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#facilitydesc" class="nav-link active" data-toggle="tab">Facility Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#rooms" class="nav-link" data-toggle="tab" id="t-Rooms">Rooms</a>
            </li>
            <li class="nav-item">
              <a href="#roomsets" class="nav-link" data-toggle="tab" id="t-RoomSets">RoomSets</a>
            </li>
            <li class="nav-item">
              <a href="#roomhasset" class="nav-link" data-toggle="tab" id="t-RoomHasSet">RoomHasSet</a>
            </li>
            <li class="nav-item">
              <a href="#features" class="nav-link" data-toggle="tab" id="t-Features">Features</a>
            </li>
            <li class="nav-item">
              <a href="#Services" class="nav-link" data-toggle="tab" id="t-Services">Services</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane mt-4 fade show active" id="facilitydesc">
              <H3>Facility Configuration Table Usage</H3>
              <ul>
                <li>Rooms</li>
                <p>List of rooms a session could be scheduled into"</p>
                <li>RoomSets</li>
                <p>Types of setups for any room</p>
                <li>RoomHasSet</li>
                <p>Which rooms can have which RoomSets</p>
                <li>Features</li>
                <p>Features available in a room for use by sessions</p>
                <li>Services</li>
                <p>Services available in a room for use by sessions</p>
              </ul>
            </div>
            <div class="tab-pane mt-4 fade" id="rooms"/>
            <div class="tab-pane mt-4 fade" id="roomsets"/>
            <div class="tab-pane mt-4 fade" id="roomhasset"/>
            <div class="tab-pane mt-4 fade" id="features"/>
            <div class="tab-pane mt-4 fade" id="services"/>
          </div>
        </div>    
        <div class="tab-pane mt-4 fade" id="emails">
          <h2>Email Configuration Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#emaildesc" class="nav-link active" data-toggle="tab">Email Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#emailfrom" class="nav-link" data-toggle="tab" id="t-EmailFrom">EmailFrom</a>
            </li>
            <li class="nav-item">
              <a href="#emailto" class="nav-link" data-toggle="tab" id="t-EmailTo">EmailTo</a>
            </li>
            <li class="nav-item">
              <a href="#emailcc" class="nav-link" data-toggle="tab" id="t-EmailCC">EmailCC</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane mt-4 fade show active" id="emaildesc">
              <H3>Email Configuration Table Usage</H3>
              <ul>
                <li>EmailFrom</li>
                <p>List of From: lines for emails sent by Zambia</p>
                <li>EmailTo</li>
                <p>List of queries to select emails to be sent by Zambia</p>
                <li>EmailCC</li>
                <p>List of potential CC address choices for emails sent by Zambia</p>
              </ul>
            </div>
            <div class="tab-pane mt-4 fade" id="emailfrom"/>
            <div class="tab-pane mt-4 fade" id="emailto"/>
            <div class="tab-pane mt-4 fade" id="emailcc"/>
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
  </xsl:template>
</xsl:stylesheet>