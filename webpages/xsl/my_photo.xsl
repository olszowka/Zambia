<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2011-07-24;
	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved.
	See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="conName" select="''"/>
    <xsl:param name="defaultPhotoName" select="''"/>
    <xsl:param name="approvedPhotoURL" select="''"/>
    <xsl:param name="enablePhotos" select="'0'"/>
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <xsl:variable name="photoNote" select="/doc/customText/@photo_note" />
        <div id="resultBoxDIV">
            <span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span>
        </div>
        <div class="container-fluid">
            <form name="photoform" class="container mt-2 mb-4">
              <xsl:if test="$enablePhotos = 1">
                <div class="row mt-4">
                  <div class="col-sm-5 card alert-secondary">
                    <input type="file" id="chooseFileName" name="chooseFileName" accept="image/png, image/jpeg, image/jpg" style="display: none"/>
                    <p class="card-title">
                      Upload Photo: Drag/Drop file or <button type="button" class="btn btn-secondary btn-sm" id="uploadPhoto">Choose File</button>
                    </p>
                    <div class="card-body" id="photoUploadArea" style="margin-right: auto; margin-left: auto; margin-top:0;">
                      <input type="hidden" name="defaultPhoto" id="default_photo">
                        <xsl:attribute name="value">
                          <xsl:choose>
                            <xsl:when test="/doc/query[@queryName='participant_info']/row/@uploadedphotofilename != ''">
                              <xsl:text>0</xsl:text>
                            </xsl:when>
                            <xsl:otherwise>
                              <xsl:text>1</xsl:text>
                            </xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                      </input>
                      <img class="upload-image" style="width: 400px; height: 400px; object-fit: scale-down; margin-top:0; margin-right: auto; margin-left: auto;" id="uploadedPhoto">
                        <xsl:attribute name="src">
                          <xsl:choose>
                            <xsl:when test="/doc/query[@queryName='participant_info']/row/@uploadedphotofilename != ''">
                              <xsl:text>SubmitMyContact.php?ajax_request_action=fetchPhoto</xsl:text>
                            </xsl:when>
                            <xsl:otherwise>
                              <xsl:value-of select="$approvedPhotoURL"/>
                              <xsl:text>/</xsl:text>
                              <xsl:value-of select="$defaultPhotoName"/>
                            </xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                      </img>
                    </div>
                    <div class="btn-group" role="group" aria-label="crop actions">
                      <button type="button" class="btn btn-primary btn-sm" id="crop" style="display: none;" onClick="myPhoto.crop();">Crop</button>
                      <button type="button" class="btn btn-primary btn-sm" id="save_crop" style="display: none; margin-right: 10px;" onClick="myPhoto.savecrop();">Save Crop</button>
                      <button type="button" class="btn btn-secondary btn-sm" id="rotate_left" style="display: none; margin-right: 10px;" onClick="myPhoto.rotate(90);">Rotate Left</button>
                      <button type="button" class="btn btn-secondary btn-sm" id="rotate_right" style="display: none; margin-right: 10px;" onClick="myPhoto.rotate(-90);">Rotate Right</button>
                      <button type="button" class="btn btn-warning btn-sm" id="cancel_crop" style="display: none" onClick="myPhoto.cancelcrop();">Cancel Crop</button>
                    </div>
                  </div>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-5 card alert-secondary">
                    <p class="card-title">Approved Photo</p>
                    <div class="card-body">
                      <img class="approved-image" style="width: 400px; height: 400px; object-fit: scale-down; margin-top:0; margin-right: auto; margin-left: auto;" id="approvedPhoto">
                        <xsl:attribute name="src">
                          <xsl:value-of select="$approvedPhotoURL"/>
                          <xsl:text>/</xsl:text>
                          <xsl:choose>
                            <xsl:when test="/doc/query[@queryName='participant_info']/row/@approvedphotofilename != ''">
                              <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@approvedphotofilename"/>
                            </xsl:when>
                            <xsl:otherwise>
                              <xsl:value-of select="$defaultPhotoName"/>
                            </xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                      </img>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-5">
                    <input type="hidden">
                      <xsl:attribute name="value">
                        <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@photouploadstatus"/>
                      </xsl:attribute>
                    </input>
                    <xsl:text>Photo Status: </xsl:text>
                    <span id="uploadedPhotoStatus">
                      <xsl:choose>
                        <xsl:when test="/doc/query[@queryName='participant_info']/row/@photouploadstatus = '-1'">No Photo Uploaded</xsl:when>
                        <xsl:otherwise>
                          <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@statustext"/>
                        </xsl:otherwise>
                      </xsl:choose>
                    </span>
                  </div>
                </div>
                <xsl:if test="/doc/query[@queryName='participant_info']/row/@photodenialreasonid > 0">
                  <div class="row mt-1">
                    <div class="col-sm-5">
                      <xsl:text>Denial Reason: </xsl:text>
                      <span id="DeniallReason">
                        <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@reasontext"/>
                        <xsl:if test="/doc/query[@queryName='participant_info']/row/@photodenialreasonothertext != ''">
                          <xsl:text> (</xsl:text>
                          <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@photodenialreasonothertext"/>
                          <xsl:text>)</xsl:text>
                        </xsl:if>
                      </span>
                    </div>
                  </div>
                </xsl:if>
                <div class="row mt-1">
                  <div class="col-sm-5">
                    <div class="btn-group" role="group" aria-label="Photo Update/Delete actions">
                      <button type="button" class="btn btn-danger btn-sm" id="deleteUploadPhoto" onClick="myPhoto.deleteuploadedphoto();">
                        <xsl:if test="string-length(/doc/query[@queryName='participant_info']/row/@uploadedphotofilename) = 0">
                          <xsl:attribute name="style">display: none;</xsl:attribute>
                        </xsl:if>
                        Delete Uploaded Photo
                      </button>
                      <button type="button" class="btn btn-primary btn-sm" id="updateUploadPhoto" style="display: none;" onClick="myPhoto.starttransfer();">
                        Upload Updated Photo
                      </button>
                    </div>
                  </div>
                  <xsl:if test="/doc/query[@queryName='participant_info']/row/@approvedphotofilename != ''">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5">
                      <button type="button" class="btn btn-danger btn-sm" id="deleteApprovedPhoto" onClick="myPhoto.deleteapprovedphoto();">
                        Delete Approved Photo
                      </button>
                    </div>
                  </xsl:if>
                </div>
                <div class="row mt-1">
                  <div class="col-sm-12">
                    <xsl:value-of select="$photoNote" disable-output-escaping="yes"/>
                  </div>
                </div> 
              </xsl:if>
            </form>
        </div>
    </xsl:template>
</xsl:stylesheet>
