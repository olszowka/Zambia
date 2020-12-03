<?xml version='1.0' encoding="UTF-8"?>
<!--
    Created by Peter Olszowka on 2020-04-12;
    Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="title" select="''" />
  <!-- Page title -->
  <xsl:param name="reportMenuList" select="''"/>
  <!-- Set of <li> elements; contents of ReportMenuInclude.php -->
  <xsl:variable name="ConfigureReports" select="/doc/query[@queryname='permission_set']/row[@permatomtag='ConfigureReports']"/>
  <xsl:variable name="AdminPhases" select="/doc/query[@queryname='permission_set']/row[@permatomtag='AdminPhases']"/>
  <xsl:variable name="Administrator" select="/doc/query[@queryname='permission_set']/row[@permatomtag='Administrator']"/>
  <xsl:template match="/">
    <nav id="staffNav" class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container" style="width: auto;">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"/>
            <span class="icon-bar"/>
            <span class="icon-bar"/>
          </a>
          <span class="brand inactive">
            <xsl:value-of select="$title"/>
          </span>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="dropdown">
                <a href="#sessions" class="dropdown-toggle" data-toggle="dropdown">
                  Sessions
                  <b class="caret"/>
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="StaffSearchSessions.php">Search Sessions</a>
                  </li>
                  <li>
                    <a href="CreateSession.php">Create New Session</a>
                  </li>
                  <li>
                    <a href="ViewSessionCountReport.php">View Session Counts</a>
                  </li>
                  <li>
                    <a href="ViewAllSessions.php">View All Sessions</a>
                  </li>
                  <li>
                    <a href="ViewPrecis.php?showlinks=0">View Precis</a>
                  </li>
                  <li>
                    <a href="ViewPrecis.php?showlinks=1">View Precis with Links</a>
                  </li>
                  <li>
                    <a href="StaffSearchPreviousSessions.php">Import Sessions</a>
                  </li>
                  <li>
                    <a href="SessionHistory.php">Session History</a>
                  </li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#participants" class="dropdown-toggle" data-toggle="dropdown">
                  Participants
                  <b class="caret"/>
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="AdminParticipants.php">Administer</a>
                  </li>
                  <li>
                    <a href="InviteParticipants.php">Invite to a Session</a>
                  </li>
                  <li>
                    <a href="StaffAssignParticipants.php">Assign to a Session</a>
                  </li>
                  <xsl:if test="/doc/query[@queryname='permission_set']/row[@permatomtag='SendEmail']">
                    <li>
                      <a href="StaffSendEmailCompose.php">Send email</a>
                    </li>
                  </xsl:if>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  Reports
                  <b class="caret"/>
                </a>
                <ul class="dropdown-menu">
                  <xsl:choose>
                    <xsl:when test="$reportMenuList != ''">
                      <xsl:value-of select="$reportMenuList" disable-output-escaping="yes"/>
                    </xsl:when>
                    <xsl:otherwise>
                      <li>
                        <div class='menu-error-entry'>Report menus not built!</div>
                      </li>
                    </xsl:otherwise>
                  </xsl:choose>
                  <li class='divider'/>
                  <li>
                    <a href='staffReportsInCategory.php'>All Reports</a>
                  </li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#scheduling" class="dropdown-toggle" data-toggle="dropdown">
                  Scheduling
                  <b class="caret"/>
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="MaintainRoomSched.php">Maintain Room Schedule</a>
                  </li>
                  <li>
                    <a href="StaffMaintainSchedule.php">Grid Scheduler</a>
                  </li>
                </ul>
              </li>
              <li class="divider-vertical"/>
              <li>
                <a href="StaffPage.php">Overview</a>
              </li>
              <li>
                <form method="post" action="ShowSessions.php" class="navbar-search pull-left">
                  <input type="text" name="searchtitle" class="search-query" placeholder="Search for sessions by title"/>
                  <input type="hidden" value="ANY" name="track"/>
                  <input type="hidden" value="ANY" name="status"/>
                  <input type="hidden" value="ANY" name="type"/>
                  <input type="hidden" value="" name="sessionid"/>
                  <input type="hidden" value="ANY" name="divisionid"/>
                </form>
              </li>
              <xsl:variable name="AdminMenu" select="$AdminPhases or $ConfigureReports or $Administrator" />
              <xsl:if test="$AdminMenu">
                <li class="dropdown">
                  <a href="#admin" class="dropdown-toggle" data-toggle="dropdown">
                    Admin
                    <b class="caret"/>
                  </a>
                  <ul class="dropdown-menu">
                    <xsl:if test="$AdminPhases">
                      <li>
                        <a href="AdminPhases.php">Administer Phases</a>
                      </li>
                    </xsl:if>
                    <xsl:if test="$ConfigureReports">
                      <li>
                        <a href="BuildReportMenus.php">Build Report Menus</a>
                      </li>
                    </xsl:if>
                    <xsl:if test="$Administrator">
                      <li>
                        <a href="EditCustomText.php">Edit Custom Text</a>
                      </li>
                    </xsl:if>
                  </ul>
                </li>
              </xsl:if>
            </ul>
            <ul class="nav pull-right">
              <li class="divider-vertical"/>
              <li>
                <a id="ParticipantView" href="welcome.php">Participant View</a>
              </li>
            </ul>
          </div>
          <!--/.nav-collapse -->
        </div>
      </div>
    </nav>
  </xsl:template>
</xsl:stylesheet>