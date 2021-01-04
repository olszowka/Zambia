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
    <xsl:if test="$UpdateMessage != ''">
      <div class="alert alert-success mt-4">
        <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
      </div>
    </xsl:if>
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
              <a href="#bioeditstatuses" class="nav-link" data-toggle="tab">BioEditStatuses</a>
            </li>
            <li class="nav-item">
              <a href="#credentials" class="nav-link" data-toggle="tab">Credentials</a>
            </li>
            <li class="nav-item">
              <a href="#roles" class="nav-link" data-toggle="tab">Roles</a>
            </li>
          </ul>
          <div cass="tab-content">
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
            <div class="tab-pane mt-4 fade" id="bioeditstatuses">
            </div>
            <div class="tab-pane mt-4 fade" id="credentials">
            </div>
            <div class="tab-pane mt-4 fade" id="roles">
            </div>
          </div> 
        </div>
        <div class="tab-pane mt-4 fade" id="session">
          <h2>Session Configuration Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#sessiondesc" class="nav-link active" data-toggle="tab">Session Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#kidscategories" class="nav-link" data-toggle="tab">KidsCategories</a>
            </li>
            <li class="nav-item">
              <a href="#languagestatuses" class="nav-link" data-toggle="tab">LanguageStatuses</a>
            </li>
            <li class="nav-item">
              <a href="#pubstatuses" class="nav-link" data-toggle="tab">PubStatuses</a>
            </li>
            <li class="nav-item">
              <a href="#sessionstatuses" class="nav-link" data-toggle="tab">SessionStatuses</a>
            </li>
          </ul>
          <div cass="tab-content">
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
            <div class="tab-pane mt-4 fade" id="kidscategories">
            </div>
            <div class="tab-pane mt-4 fade" id="languagestatuses">
            </div>
            <div class="tab-pane mt-4 fade" id="pubstatuses">
            </div>
            <div class="tab-pane mt-4 fade" id="sessionstatuses">
            </div>
          </div> 
        </div>
        <div class="tab-pane mt-4 fade" id="layout">
          <h2>Convention Layout Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#layoutdesc" class="nav-link active" data-toggle="tab">Convention Layout Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#divisions" class="nav-link" data-toggle="tab">Divisions</a>
            </li>
            <li class="nav-item">
              <a href="#regtypes" class="nav-link" data-toggle="tab">RegTypes</a>
            </li>
            <li class="nav-item">
              <a href="#tags" class="nav-link" data-toggle="tab">Tags</a>
            </li>
            <li class="nav-item">
              <a href="#times" class="nav-link" data-toggle="tab">Times</a>
            </li>
            <li class="nav-item">
              <a href="#tracks" class="nav-link" data-toggle="tab">Tracks</a>
            </li>
            <li class="nav-item">
              <a href="#Types" class="nav-link" data-toggle="tab">Types</a>
            </li>
          </ul>
          <div cass="tab-content">
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
            <div class="tab-pane mt-4 fade" id="divisions">
            </div>
            <div class="tab-pane mt-4 fade" id="regtypes">
            </div>
            <div class="tab-pane mt-4 fade" id="tags">
            </div>
            <div class="tab-pane mt-4 fade" id="times">
            </div>
            <div class="tab-pane mt-4 fade" id="tracks">
            </div>
            <div class="tab-pane mt-4 fade" id="types">
            </div>
          </div>
        </div>
        <div class="tab-pane mt-4 fade" id="facility">
          <h2>Facility Configuration Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#facilitydesc" class="nav-link active" data-toggle="tab">Facility Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#rooms" class="nav-link" data-toggle="tab">Rooms</a>
            </li>
            <li class="nav-item">
              <a href="#roomsets" class="nav-link" data-toggle="tab">RoomSets</a>
            </li>
            <li class="nav-item">
              <a href="#roomhasset" class="nav-link" data-toggle="tab">RoomHasSet</a>
            </li>
            <li class="nav-item">
              <a href="#features" class="nav-link" data-toggle="tab">Features</a>
            </li>
            <li class="nav-item">
              <a href="#Services" class="nav-link" data-toggle="tab">Services</a>
            </li>
          </ul>
          <div cass="tab-content">
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
            <div class="tab-pane mt-4 fade" id="rooms">
            </div>
            <div class="tab-pane mt-4 fade" id="roomsets">
            </div>
            <div class="tab-pane mt-4 fade" id="roomhasset">
            </div>
            <div class="tab-pane mt-4 fade" id="features">
            </div>
            <div class="tab-pane mt-4 fade" id="services">
            </div>
          </div>
        </div>    
        <div class="tab-pane mt-4 fade" id="emails">
          <h2>Email Configuration Tables</h2>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#emaildesc" class="nav-link active" data-toggle="tab">Email Configuration Tables</a>
            </li>
            <li class="nav-item">
              <a href="#emailfrom" class="nav-link" data-toggle="tab">EmailFrom</a>
            </li>
            <li class="nav-item">
              <a href="#emailto" class="nav-link" data-toggle="tab">EmailTo</a>
            </li>
            <li class="nav-item">
              <a href="#emailcc" class="nav-link" data-toggle="tab">EmailCC</a>
            </li>
          </ul>
          <div cass="tab-content">
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
            <div class="tab-pane mt-4 fade" id="emailfrom">
            </div>
            <div class="tab-pane mt-4 fade" id="emailto">
            </div>
            <div class="tab-pane mt-4 fade" id="emailcc">
            </div>
          </div>
        </div>
      </div>
    </div>
  </xsl:template>
  <xsl:template match="editor">
    <xsl:param name="table"
  </xsl:template>
</xsl:stylesheet>