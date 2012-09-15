<?xml version="1.0" encoding="UTF-8" ?>
<!--
	my_profile
	Created by Peter Olszowka on 2011-07-24.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
   <xsl:variable name="conName"><xsl:value-of select="/doc/options/@conName"/></xsl:variable>
    <xsl:variable name="enableParticipant"><xsl:value-of select="/doc/options/@enableParticipant"/></xsl:variable>
    <xsl:variable name="enableAdministration"><xsl:value-of select="/doc/options/@enableAdministration"/></xsl:variable>
    <form name="partform" method="POST" action="SubmitCreateParticipant.php">
		<div class="userFormDiv">
			<div class="labelNinput">
				<div class="narrowlabel"><label for="create_email">Email Addr:</label></div>
				<span class="inputhadlabel">
					<input type="text" size="20" name="create_email" id="create_email" class="userFormINPTXT">
						<xsl:if test="$enableParticipant!='1'">
							<xsl:attribute name="readonly">readonly</xsl:attribute>
						</xsl:if>
					</input>
				</span>
			</div>
			<div class="labelNinput">
				<div class="narrowlabel"><label for="create_pname">Participant Name:</label></div>
				<span class="inputhadlabel">
					<input type="text" size="20" name="create_pname" id="create_pname" class="userFormINPTXT">
						<xsl:if test="$enableParticipant!='1'">
							<xsl:attribute name="readonly">readonly</xsl:attribute>
						</xsl:if>
					</input>
				</span>
			</div>
			<div class="labelNinput">
				<div class="narrowlabel"><label for="create_regtype">Registration Status</label></div>
				<span class="inputhadlabel">
					<select name="create_regtype" 	id="create_regtype" class="userFormINPTXT">
						<xsl:if test="$enableParticipant!='1'">
							<xsl:attribute name="readonly">readonly</xsl:attribute>
						</xsl:if>
						<xsl:variable name="currentRegType"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@regtype" /></xsl:variable>
						<xsl:for-each select="/doc/query[@queryName='reg_types']/row">
							<option value="{@regtype}">
								<xsl:if test="@regtype='None'">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="@regtype"/>
							</option>
						</xsl:for-each>
					</select>
				</span>
			</div>
			<div class="labelNinput">
				<div class="narrowlabel"><label for="create_regdepartment">Registration Department</label></div>
				<span class="inputhadlabel">
					<input type="text" size="20" name="create_regdepartment" id="create_regdepartment" class="userFormINPTXT">
						<xsl:if test="$enableParticipant!='1'">
							<xsl:attribute name="readonly">readonly</xsl:attribute>
						</xsl:if>
					</input>
				</span>
			</div>
			<div class="labelNinput">
				<div class="narrowlabel"><label for="create_adminStatus">Admin:</label></div>
				<span class="inputhadlabel">
					<input type="checkbox" name="create_adminStatus" id="create_adminStatus">
						<xsl:if test="$enableAdministration!='1'">
							<xsl:attribute name="readonly">readonly</xsl:attribute>
						</xsl:if>
					</input>
				</span>
			</div>
			<div class="labelNinput">
				<div class="narrowlabel"><label for="create_staffStatus">Staff:</label></div>
				<span class="inputhadlabel">
					<input type="checkbox" name="create_staffStatus" id="create_staffStatus">
						<xsl:if test="$enableAdministration!='1'">
							<xsl:attribute name="readonly">readonly</xsl:attribute>
						</xsl:if>
					</input>
				</span>
			</div>
			
			<div class="labelNinput">
				<div class="narrowlabel"><label for="create_partStatus">Participant:</label></div>
				<span class="inputhadlabel">
					<input type="checkbox" name="create_partStatus" id="create_partStatus" checked="checked">
						<xsl:if test="$enableAdministration!='1'">
							<xsl:attribute name="readonly">readonly</xsl:attribute>
						</xsl:if>
					</input>
				</span>
			</div>
		</div>
	</form>
</xsl:template>
</xsl:stylesheet>