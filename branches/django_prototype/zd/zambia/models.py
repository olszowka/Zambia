# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
#
# Also note: You'll have to insert the output of 'django-admin sqlcustom [app_label]'
# into your database.
from __future__ import unicode_literals

from django.db import models

class ReportCategory(models.Model):
    reportcategoryid = models.AutoField(primary_key=True)
    description = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField()

    def __unicode__(self):
        return str(self.reportcategoryid) + ': ' + self.description

    class Meta:
        managed = False
        db_table = 'ReportCategories'
        verbose_name = 'Report category'
        verbose_name_plural = 'Report categories'
        ordering = ['display_order']

class ReportType(models.Model):
    reporttypeid = models.AutoField(primary_key=True)
    title = models.CharField(max_length=200)
    description = models.TextField(blank=True, null=True)
    technology = models.CharField(max_length=200, blank=True, null=True)
    oldmechanism = models.IntegerField()
    ondemand = models.IntegerField(blank=True, null=True)
    filename = models.CharField(max_length=60, blank=True, null=True)
    xsl = models.TextField(blank=True, null=True)
    download = models.IntegerField(blank=True, null=True)
    downloadfilename = models.CharField(max_length=25, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.reporttypeid) + ': ' + self.title

    class Meta:
        managed = False
        db_table = 'ReportTypes'
        ordering = ['title']


class CategoryHasReport(models.Model):
    reportcategory = models.ForeignKey(ReportCategory, db_column='reportcategoryid')
    reporttype = models.ForeignKey(ReportType, db_column='reporttypeid')
    categoryhasreportid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.reportcategory.__unicode__() + ' has ' + self.reporttype.__unicode__()

    class Meta:
        managed = False
        db_table = 'CategoryHasReport'
        unique_together = (('reportcategory', 'reporttype'),)
        ordering = ['reportcategory', 'reporttype']


class CongoDump(models.Model):
    badgeid = models.CharField(primary_key=True, max_length=15)
    firstname = models.CharField(max_length=30, blank=True, null=True)
    lastname = models.CharField(max_length=40, blank=True, null=True)
    badgename = models.CharField(max_length=51, blank=True, null=True)
    phone = models.CharField(max_length=100, blank=True, null=True)
    email = models.CharField(max_length=100, blank=True, null=True)
    postaddress1 = models.CharField(max_length=100, blank=True, null=True)
    postaddress2 = models.CharField(max_length=100, blank=True, null=True)
    postcity = models.CharField(max_length=50, blank=True, null=True)
    poststate = models.CharField(max_length=25, blank=True, null=True)
    postzip = models.CharField(max_length=10, blank=True, null=True)
    postcountry = models.CharField(max_length=25, blank=True, null=True)
    regtype = models.CharField(max_length=40, blank=True, null=True)

    def __unicode__(self):
        return str(self.badgeid) + ': ' + self.firstname + ' ' + self.lastname

    class Meta:
        managed = False
        db_table = 'CongoDump'


class CustomText(models.Model):
    customtextid = models.AutoField(primary_key=True)
    page = models.CharField(max_length=100, blank=True, null=True)
    tag = models.CharField(max_length=25, blank=True, null=True)
    textcontents = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return str(self.customtextid) + ': ' + self.page + ' ' + self.tag

    class Meta:
        managed = False
        db_table = 'CustomText'
        unique_together = (('page', 'tag'),)


class EmailCC(models.Model):
    emailccid = models.AutoField(primary_key=True)
    description = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField()
    emailaddress = models.CharField(max_length=255, blank=True, null=True)

    def __unicode__(self):
        return str(self.emailccid) + ': ' + self.description + ' ' + self.emailaddress

    class Meta:
        managed = False
        db_table = 'EmailCC'


class EmailFrom(models.Model):
    emailfromid = models.AutoField(primary_key=True)
    emailfromdescription = models.CharField(max_length=30, blank=True, null=True)
    display_order = models.IntegerField()
    emailfromaddress = models.CharField(max_length=255, blank=True, null=True)

    def __unicode__(self):
        return str(self.emailfromid) + ': ' + self.emailfromdescription + ' ' + self.emailfromaddress

    class Meta:
        managed = False
        db_table = 'EmailFrom'


class EmailQueue(models.Model):
    emailqueueid = models.AutoField(primary_key=True)
    emailto = models.CharField(max_length=255, blank=True, null=True)
    emailfrom = models.CharField(max_length=255, blank=True, null=True)
    emailcc = models.CharField(max_length=255, blank=True, null=True)
    emailsubject = models.CharField(max_length=255, blank=True, null=True)
    body = models.TextField(blank=True, null=True)
    status = models.IntegerField()
    emailtimestamp = models.DateTimeField()

    def __unicode__(self):
        return str(self.emailqueueid) + ': to ' + self.emailto + ' from ' + self.emailfrom + ' on ' + self.emailsubject

    class Meta:
        managed = False
        db_table = 'EmailQueue'


class EmailTo(models.Model):
    emailtoid = models.AutoField(primary_key=True)
    emailtodescription = models.CharField(max_length=75, blank=True, null=True)
    display_order = models.IntegerField()
    emailtoquery = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return str(self.emailtoid) + ': ' + self.emailtodescription + ' ' + self.emailtoquery

    class Meta:
        managed = False
        db_table = 'EmailTo'


class Feature(models.Model):
    featureid = models.AutoField(primary_key=True)
    featurename = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.featureid) + ': ' + self.featurename

    class Meta:
        managed = False
        db_table = 'Features'


class BioEditStatus(models.Model):
    bioeditstatusid = models.AutoField(primary_key=True)
    bioeditstatusname = models.CharField(max_length=60, blank=True, null=True)
    display_order = models.IntegerField()

    def __unicode__(self):
        return str(self.bioeditstatusid) + ': ' + self.bioeditstatusname

    class Meta:
        managed = False
        db_table = 'BioEditStatuses'
        verbose_name = 'Bio edit status'
        verbose_name_plural = 'Bio edit statuses'


class Participant(models.Model):
    badgeid = models.CharField(primary_key=True, max_length=15)
    password = models.CharField(max_length=32, blank=True, null=True)
    bestway = models.CharField(max_length=12, blank=True, null=True)
    interested = models.IntegerField(blank=True, null=True)
    bio = models.TextField(blank=True, null=True)
    editedbio = models.TextField(blank=True, null=True)
    scndlangbio = models.TextField(blank=True, null=True)
    bioeditstatus = models.ForeignKey(BioEditStatus, db_column='bioeditstatusid')
    biolockedby = models.CharField(max_length=15, blank=True, null=True)
    pubsname = models.CharField(max_length=50, blank=True, null=True)
    share_email = models.IntegerField(blank=True, null=True)
    staff_notes = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return self.badgeid + ': ' + self.bestway

    class Meta:
        managed = False
        db_table = 'Participants'
        permissions = (
            ("participant", "Can access participant functionality"),
            ("public", "Can view public functionality (ie brainstorm)"),
            ("brainstorm", "Can make brainstorm changes"),
            ("brainstorm_search", "Can search brainstorm sessions"),
        )



class ParticipantAvailability(models.Model):
    badge = models.OneToOneField(Participant, db_column='badgeid', primary_key=True)
    maxprog = models.IntegerField(blank=True, null=True)
    preventconflict = models.CharField(max_length=255, blank=True, null=True)
    otherconstraints = models.CharField(max_length=255, blank=True, null=True)
    numkidsfasttrack = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return self.badge.__unicode__() + ': ' + str(self.maxprog)

    class Meta:
        managed = False
        db_table = 'ParticipantAvailability'


class ParticipantAvailabilityDay(models.Model):
    badge = models.ForeignKey(Participant, db_column='badgeid')
    day = models.SmallIntegerField()
    maxprog = models.IntegerField(blank=True, null=True)
    participantavailabilitydayid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.badge.__unicode__() + ': ' + str(self.day)

    class Meta:
        managed = False
        db_table = 'ParticipantAvailabilityDays'
        unique_together = (('badge', 'day'),)


class ParticipantAvailabilityTime(models.Model):
    badge = models.ForeignKey(Participant, db_column='badgeid')
    availabilitynum = models.IntegerField()
    starttime = models.TimeField(blank=True, null=True)
    endtime = models.TimeField(blank=True, null=True)
    participantavailabilitytime = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.badge.__unicode__() + ': ' + str(self.availabilitynum)

    class Meta:
        managed = False
        db_table = 'ParticipantAvailabilityTimes'
        unique_together = (('badge', 'availabilitynum'),)


class Credential(models.Model):
    credentialid = models.AutoField(primary_key=True)
    credentialname = models.CharField(max_length=100, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.credentialid) + ': ' + self.credentialname

    class Meta:
        managed = False
        db_table = 'Credentials'


class ParticipantHasCredential(models.Model):
    badge = models.ForeignKey(Participant, db_column='badgeid')
    credential = models.ForeignKey(Credential, db_column='credentialid')
    participanthascredentialid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.badge.__unicode__() + ' has ' + self.credential.__unicode__()

    class Meta:
        managed = False
        db_table = 'ParticipantHasCredential'
        unique_together = (('badge', 'credential'),)


class Role(models.Model):
    roleid = models.AutoField(primary_key=True)
    rolename = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.roleid) + ': ' + self.rolename

    class Meta:
        managed = False
        db_table = 'Roles'


class ParticipantHasRole(models.Model):
    badge = models.ForeignKey(Participant, db_column='badgeid')
    role = models.ForeignKey(Role, db_column='roleid')
    participanthasroleid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.badge.__unicode__() + ' has ' + self.role.__unicode__()

    class Meta:
        managed = False
        db_table = 'ParticipantHasRole'
        unique_together = (('badge', 'role'),)


class ParticipantInterest(models.Model):
    badge = models.OneToOneField(Participant, db_column='badgeid', primary_key=True)
    yespanels = models.TextField(blank=True, null=True)
    nopanels = models.TextField(blank=True, null=True)
    yespeople = models.TextField(blank=True, null=True)
    nopeople = models.TextField(blank=True, null=True)
    otherroles = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return self.badge.__unicode__() + ' interests'

    class Meta:
        managed = False
        db_table = 'ParticipantInterests'


class Track(models.Model):
    trackid = models.AutoField(primary_key=True)
    trackname = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)
    selfselect = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.trackid) + ': ' + self.trackname

    class Meta:
        managed = False
        db_table = 'Tracks'


class Type(models.Model):
    typeid = models.AutoField(primary_key=True)
    typename = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)
    selfselect = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.typeid) + ': ' + self.typename

    class Meta:
        managed = False
        db_table = 'Types'


class Division(models.Model):
    divisionid = models.AutoField(primary_key=True)
    divisionname = models.CharField(max_length=30, blank=True, null=True)
    display_order = models.IntegerField()

    def __unicode__(self):
        return str(self.divisionid) + ': ' + self.divisionname

    class Meta:
        managed = False
        db_table = 'Divisions'


class PubStatus(models.Model):
    pubstatusid = models.AutoField(primary_key=True)
    pubstatusname = models.CharField(max_length=12, blank=True, null=True)
    display_order = models.IntegerField()

    def __unicode__(self):
        return str(self.pubstatusid) + ': ' + self.pubstatusname

    class Meta:
        managed = False
        db_table = 'PubStatuses'
        verbose_name = 'Pub status'
        verbose_name_plural = 'Pub statuses'


class LanguageStatus(models.Model):
    languagestatusid = models.AutoField(primary_key=True)
    languagestatusname = models.CharField(max_length=30, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.languagestatusid) + ': ' + self.languagestatusname

    class Meta:
        managed = False
        db_table = 'LanguageStatuses'
        verbose_name = 'Language status'
        verbose_name_plural = 'Language statuses'


class KidsCategory(models.Model):
    kidscatid = models.AutoField(primary_key=True)
    kidscatname = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.kidscatid) + ': ' + self.kidscatname

    class Meta:
        managed = False
        db_table = 'KidsCategories'
        verbose_name = 'Kids category'
        verbose_name_plural = 'Kids categories'


class RoomSet(models.Model):
    roomsetid = models.AutoField(primary_key=True)
    roomsetname = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.roomsetid) + ': ' + self.roomsetname

    class Meta:
        managed = False
        db_table = 'RoomSets'


class SessionStatus(models.Model):
    statusid = models.AutoField(primary_key=True)
    statusname = models.CharField(max_length=50, blank=True, null=True)
    validate = models.IntegerField()
    may_be_scheduled = models.IntegerField()
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.statusid) + ': ' + self.statusname

    class Meta:
        managed = False
        db_table = 'SessionStatuses'
        verbose_name = 'Session status'
        verbose_name_plural = 'Session statuses'


class Session(models.Model):
    sessionid = models.AutoField(primary_key=True)
    track = models.ForeignKey(Track, db_column='trackid')
    type = models.ForeignKey(Type, db_column='typeid')
    division = models.ForeignKey(Division, db_column='divisionid')
    pubstatus = models.ForeignKey(PubStatus, db_column='pubstatusid', blank=True, null=True)
    languagestatus = models.ForeignKey(LanguageStatus, db_column='languagestatusid', blank=True, null=True)
    pubsno = models.CharField(max_length=50, blank=True, null=True)
    title = models.CharField(max_length=100, blank=True, null=True)
    secondtitle = models.CharField(max_length=100, blank=True, null=True)
    pocketprogtext = models.TextField(blank=True, null=True)
    progguiddesc = models.TextField(blank=True, null=True)
    persppartinfo = models.TextField(blank=True, null=True)
    duration = models.TimeField(blank=True, null=True)
    estatten = models.IntegerField(blank=True, null=True)
    kidscat = models.ForeignKey(KidsCategory, db_column='kidscatid')
    signupreq = models.IntegerField(blank=True, null=True)
    roomset = models.ForeignKey(RoomSet, db_column='roomsetid')
    notesforpart = models.TextField(blank=True, null=True)
    servicenotes = models.TextField(blank=True, null=True)
    status = models.ForeignKey(SessionStatus, db_column='statusid')
    notesforprog = models.TextField(blank=True, null=True)
    warnings = models.IntegerField(blank=True, null=True)
    invitedguest = models.IntegerField(blank=True, null=True)
    ts = models.DateTimeField()

    def __unicode__(self):
        return str(self.sessionid) + ': ' + self.title
#  + ' ' + self.track.__unicode__() + ' ' + self.type.__unicode__() + self.division.__unicode__()

    class Meta:
        managed = False
        db_table = 'Sessions'

class ParticipantOnSession(models.Model):
    participantonsessionid = models.AutoField(primary_key = True)
    badge = models.ForeignKey(Participant, db_column='badgeid')
    session = models.ForeignKey(Session, db_column='sessionid')
    moderator = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return self.badge.__unicode__() + ' on ' + self.session.__unicode__()

    class Meta:
        managed = False
        db_table = 'ParticipantOnSession'
        unique_together = (('badge', 'session'),)


class ParticipantOnSessionHistory(models.Model):
    participantonsessionhistoryid = models.AutoField(primary_key = True)
    badge = models.ForeignKey(Participant, db_column='badgeid')
    session = models.ForeignKey(Session, db_column='sessionid')
    moderator = models.IntegerField(blank=True, null=True)
    createdts = models.DateTimeField()
    createdbybadgeid = models.CharField(max_length=15)
    inactivatedts = models.DateTimeField()
    inactivatedbybadgeid = models.CharField(max_length = 15, null = True, blank = True)

    def __unicode__(self):
        return str(self.participantonsessionhistoryid) + ': ' + self.badge.__unicode__() + ' on ' + self.session.__unicode__()

    class Meta:
        managed = False
        db_table = 'ParticipantOnSessionHistory'
        verbose_name = 'Participant on session history'
        verbose_name_plural = 'Participant on session histories'


class ParticipantSessionInterest(models.Model):
    badge = models.ForeignKey(Participant, db_column='badgeid')
    session = models.ForeignKey(Session, db_column='sessionid')
    rank = models.IntegerField(blank=True, null=True)
    willmoderate = models.IntegerField(blank=True, null=True)
    comments = models.TextField(blank=True, null=True)
    participantsessioninterestid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.badge.__unicode__() + ' interested in ' + self.session.__unicode__()

    class Meta:
        managed = False
        db_table = 'ParticipantSessionInterest'
        unique_together = (('badge', 'session'),)


class ParticipantSuggestion(models.Model):
    badge = models.OneToOneField(Participant, db_column='badgeid', primary_key=True)
    paneltopics = models.TextField(blank=True, null=True)
    otherideas = models.TextField(blank=True, null=True)
    suggestedguests = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return self.badge.__unicode__()

    class Meta:
        managed = False
        db_table = 'ParticipantSuggestions'


class PatchLog(models.Model):
    patchname = models.CharField(max_length=40, blank=True, null=True)
    timestamp = models.DateTimeField(primary_key = True)

    def __unicode__(self):
        return self.patchname + ' on ' + self.timestamp.isoformat()

    class Meta:
        managed = False
        db_table = 'PatchLog'


class PermissionAtom(models.Model):
    permatomid = models.AutoField(primary_key=True)
    permatomtag = models.CharField(max_length=20)
    elementid = models.IntegerField(blank=True, null=True)
    page = models.CharField(max_length=30, blank=True, null=True)
    notes = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return str(self.permatomid) + ': ' + self.permatomtag

    class Meta:
        managed = False
        db_table = 'PermissionAtoms'
        unique_together = (('permatomtag', 'elementid'),)


class Phase(models.Model):
    phaseid = models.AutoField(primary_key=True)
    phasename = models.CharField(max_length=100, blank=True, null=True)
    current = models.IntegerField(blank=True, null=True)
    notes = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return str(self.phaseid) + ': ' + self.phasename

    class Meta:
        managed = False
        db_table = 'Phases'


class PermissionRole(models.Model):
    permroleid = models.AutoField(primary_key=True)
    permrolename = models.CharField(max_length=100, blank=True, null=True)
    notes = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return str(self.permroleid) + ': ' + self.permrolename

    class Meta:
        managed = False
        db_table = 'PermissionRoles'


class Permission(models.Model):
    permissionid = models.AutoField(primary_key=True)
    permatom = models.ForeignKey(PermissionAtom, db_column='permatomid')
    phase = models.ForeignKey(Phase, db_column='phaseid', blank=True, null=True)
    permrole = models.ForeignKey(PermissionRole, db_column='permroleid', blank=True, null=True)
    badge = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.permissionid) + ': ' + self.permatom.__unicode__() + ' ' + self.phase.__unicode__() + ' ' + self.permrole.__unicode__() + ' ' + self.badge.__unicode__()

    class Meta:
        managed = False
        db_table = 'Permissions'
        unique_together = (('permatom', 'phase', 'permrole', 'badge'),)


class PreviousCon(models.Model):
    previousconid = models.AutoField(primary_key=True)
    previousconname = models.CharField(max_length=128, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.previousconid) + ': ' + self.previousconname

    class Meta:
        managed = False
        db_table = 'PreviousCons'


class PreviousConTrack(models.Model):
    previouscon = models.ForeignKey(PreviousCon, db_column='previousconid')
    previoustrackid = models.IntegerField()
    trackname = models.CharField(max_length=50, blank=True, null=True)
    previouscontrackid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.previouscon.__unicode__() + '.' + str(self.previoustrackid) + ' ' + self.trackname

    class Meta:
        managed = False
        db_table = 'PreviousConTracks'
        unique_together = (('previouscon', 'previoustrackid'),)


class PreviousParticipant(models.Model):
    badgeid = models.CharField(primary_key=True, max_length=15)
    bio = models.TextField(blank=True, null=True)
    staff_notes = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return self.badgeid

    class Meta:
        managed = False
        db_table = 'PreviousParticipants'


class PreviousSession(models.Model):
    previouscon = models.ForeignKey(PreviousCon, db_column='previousconid')
    previoussessionid = models.IntegerField()
    previoustrackid = models.IntegerField()
    previousstatus = models.ForeignKey(SessionStatus, db_column='previousstatusid')
    type = models.ForeignKey(Type, db_column='typeid')
    division = models.ForeignKey(Division, db_column='divisionid')
    languagestatus = models.ForeignKey(LanguageStatus, db_column='languagestatusid', blank=True, null=True)
    title = models.CharField(max_length=100, blank=True, null=True)
    secondtitle = models.CharField(max_length=100, blank=True, null=True)
    pocketprogtext = models.TextField(blank=True, null=True)
    progguiddesc = models.TextField(blank=True, null=True)
    persppartinfo = models.TextField(blank=True, null=True)
    duration = models.TimeField(blank=True, null=True)
    estatten = models.IntegerField(blank=True, null=True)
    kidscat = models.ForeignKey(KidsCategory, db_column='kidscatid')
    signupreq = models.IntegerField(blank=True, null=True)
    notesforpart = models.TextField(blank=True, null=True)
    notesforprog = models.TextField(blank=True, null=True)
    invitedguest = models.IntegerField(blank=True, null=True)
    importedsessionid = models.IntegerField(blank=True, null=True)
    melvin = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.previouscon.__unicode__() + ':' + str(self.previoussessionid) + ' ' + self.title
# self.previoustrack.__unicode__() + self.

    class Meta:
        managed = False
        db_table = 'PreviousSessions'
        unique_together = (('previouscon', 'previoussessionid'),)


class PubCharacteristic(models.Model):
    pubcharid = models.AutoField(primary_key=True)
    pubcharname = models.CharField(max_length=30, blank=True, null=True)
    pubchartag = models.CharField(max_length=10, blank=True, null=True)
    display_order = models.IntegerField()

    def __unicode__(self):
        return str(self.pubcharid) + ': ' + self.pubcharname

    class Meta:
        managed = False
        db_table = 'PubCharacteristics'


class RegType(models.Model):
    regtype = models.CharField(primary_key=True, max_length=40)
    message = models.CharField(max_length=100, blank=True, null=True)

    def __unicode__(self):
        return self.regtype

    class Meta:
        managed = False
        db_table = 'RegTypes'


class ReportQuery(models.Model):
    reportqueryid = models.AutoField(primary_key=True)
    reporttype = models.ForeignKey(ReportType, db_column='reporttypeid')
    queryname = models.CharField(max_length=25)
    query = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return str(self.reportqueryid) + ': ' + self.reporttype.__unicode__() + ' ' + self.queryname

    class Meta:
        managed = False
        db_table = 'ReportQueries'
        verbose_name = 'Report query'
        verbose_name_plural = 'Report queries'


class Room(models.Model):
    roomid = models.AutoField(primary_key=True)
    roomname = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)
    height = models.CharField(max_length=100, blank=True, null=True)
    dimensions = models.CharField(max_length=100, blank=True, null=True)
    area = models.CharField(max_length=100, blank=True, null=True)
    function = models.CharField(max_length=100, blank=True, null=True)
    floor = models.CharField(max_length=50, blank=True, null=True)
    notes = models.TextField(blank=True, null=True)
    opentime1 = models.TimeField(blank=True, null=True)
    closetime1 = models.TimeField(blank=True, null=True)
    opentime2 = models.TimeField(blank=True, null=True)
    closetime2 = models.TimeField(blank=True, null=True)
    opentime3 = models.TimeField(blank=True, null=True)
    closetime3 = models.TimeField(blank=True, null=True)
    is_scheduled = models.IntegerField()

    def __unicode__(self):
        return str(self.roomid) + ': ' + self.roomname

    class Meta:
        managed = False
        db_table = 'Rooms'


class RoomHasSet(models.Model):
    room = models.ForeignKey(Room, db_column='roomid')
    roomset = models.ForeignKey(RoomSet, db_column='roomsetid')
    capacity = models.IntegerField(blank=True, null=True)
    roomhassetid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.room.__unicode__() + ': ' + self.roomset.__unicode__()

    class Meta:
        managed = False
        db_table = 'RoomHasSet'
        unique_together = (('room', 'roomset'),)


class Schedule(models.Model):
    scheduleid = models.AutoField(primary_key=True)
    session = models.ForeignKey(Session, db_column='sessionid')
    room = models.ForeignKey(Room, db_column='roomid')
    starttime = models.TimeField()

    def __unicode__(self):
        return str(self.scheduleid) + ': ' + self.session.__unicode__() + ' ' + self.room.__unicode__() + ' ' + self.starttime.isoformat()

    class Meta:
        managed = False
        db_table = 'Schedule'


class Service(models.Model):
    serviceid = models.AutoField(primary_key=True)
    servicename = models.CharField(max_length=50, blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.serviceid) + ': ' + self.servicename

    class Meta:
        managed = False
        db_table = 'Services'


class SessionEditCode(models.Model):
    sessioneditcode = models.AutoField(primary_key=True)
    description = models.CharField(max_length=40, blank=True, null=True)
    display_order = models.IntegerField()

    def __unicode__(self):
        return str(self.sessioneditcode) + ': ' + self.description

    class Meta:
        managed = False
        db_table = 'SessionEditCodes'


class SessionEditHistory(models.Model):
    session = models.ForeignKey(Session, db_column='sessionid')
    badge = models.ForeignKey(Participant, db_column='badgeid', blank=True, null=True)
    name = models.CharField(max_length=40, blank=True, null=True)
    email_address = models.CharField(max_length=75, blank=True, null=True)
    timestamp = models.DateTimeField()
    sessioneditcode = models.ForeignKey(SessionEditCode, db_column='sessioneditcode')
    status = models.ForeignKey(SessionStatus, db_column='statusid')
    editdescription = models.TextField(blank=True, null=True)
    sessionedithistoryid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.sessionid.__unicode__() + ' ' + self.timestamp

    class Meta:
        managed = False
        db_table = 'SessionEditHistory'
        unique_together = (('session', 'timestamp'),)
        verbose_name = 'Session edit history'
        verbose_name_plural = 'Session edit histories'


class SessionHasFeature(models.Model):
    session = models.ForeignKey(Session, db_column='sessionid')
    feature = models.ForeignKey(Feature, db_column='featureid')
    sessionhasfeatureid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.session.__unicode__() + ' has ' + self.feature.__unicode__()

    class Meta:
        managed = False
        db_table = 'SessionHasFeature'
        unique_together = (('session', 'feature'),)


class SessionHasPubChar(models.Model):
    session = models.ForeignKey(Session, db_column='sessionid')
    pubchar = models.ForeignKey(PubCharacteristic, db_column='pubcharid')
    sessionhaspubcharid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.session.__unicode__() + ' has ' + self.pubchar.__unicode__()

    class Meta:
        managed = False
        db_table = 'SessionHasPubChar'
        unique_together = (('session', 'pubchar'),)


class SessionHasService(models.Model):
    session = models.ForeignKey(Session, db_column='sessionid')
    service = models.ForeignKey(Service, db_column='serviceid')
    sessionhasserviceid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.session.__unicode__() + ' has ' + self.service.__unicode__()

    class Meta:
        managed = False
        db_table = 'SessionHasService'
        unique_together = (('session', 'service'),)


class TimeSlot(models.Model):
    timeid = models.IntegerField(primary_key=True)
    timedisplay = models.CharField(max_length=14, blank=True, null=True)
    timevalue = models.TimeField(blank=True, null=True)
    next_day = models.IntegerField(blank=True, null=True)
    display_order = models.IntegerField(blank=True, null=True)
    avail_start = models.IntegerField(blank=True, null=True)
    avail_end = models.IntegerField(blank=True, null=True)

    def __unicode__(self):
        return str(self.timeid) + ': ' + self.timedisplay + ' ' + self.timevalue

    class Meta:
        managed = False
        db_table = 'Times'


class TrackCompatibility(models.Model):
    previouscon = models.ForeignKey(PreviousCon, db_column='previousconid')
    previoustrackid = models.IntegerField()
    currenttrack = models.ForeignKey(Track, db_column='currenttrackid')
    trackcompatibilityid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.previouscon.__unicode__() + '.' + str(self.previoustrackid) + ' to ' + self.currenttrack.__unicode__()

    class Meta:
        managed = False
        db_table = 'TrackCompatibility'
        unique_together = (('previouscon', 'previoustrackid'),)
        verbose_name = 'Track compatibility'
        verbose_name_plural = 'Track compatibilities'


class UserHasPermissionRole(models.Model):
    badge = models.ForeignKey(Participant, db_column='badgeid')
    permrole = models.ForeignKey(PermissionRole, db_column='permroleid')
    userhaspermissionroleid = models.AutoField(primary_key = True)

    def __unicode__(self):
        return self.badge.__unicode__() + ' has ' + self.permrole.__unicode__()

    class Meta:
        managed = False
        db_table = 'UserHasPermissionRole'
        unique_together = (('badge', 'permrole'),)

# EnumField looted from a public comment on stackoverflow.com
# http://stackoverflow.com/questions/21454/specifying-a-mysql-enum-in-a-django-model
class EnumField(models.Field):
    def __init__(self, *args, **kwargs):
        super(EnumField, self).__init__(*args, **kwargs)
        assert self.choices, "Need choices for enumeration"

    def db_type(self, connection):
        if not all(isinstance(col, basestring) for col, _ in self.choices):
            raise ValueError("MySQL ENUM values should be strings")
        return "ENUM({})".format(','.join("'{}'".format(col) 
                                          for col, _ in self.choices))

class ConfigType(EnumField, models.CharField):
    def __init__(self, *args, **kwargs):
        types = [('string', 'String'),
                 ('number', 'Number'),
                 ('date', 'Date'),
                 ('bool', 'Boolean'),
        ]
        kwargs['choices'] = types
        super(ConfigType, self).__init__(*args, **kwargs)

class ConfigConfig(EnumField, models.CharField):
    def __init__(self, *args, **kwargs):
        types = [
            ('CON_START_DATIM', 'CON_START_DATIM'),
            ('CON_NUM_DAYS', 'CON_NUM_DAYS'),
            ('DAY_CUTOFF_HOUR', 'DAY_CUTOFF_HOUR'),
            ('PREF_TTL_SESNS_LMT', 'PREF_TTL_SESNS_LMT'),
            ('PREF_DLY_SESNS_LMT', 'PREF_DLY_SESNS_LMT'),
            ('AVAILABILITY_ROWS', 'AVAILABILITY_ROWS'),
            ('MAX_BIO_LEN', 'MAX_BIO_LEN'),
            ('DURATION_IN_MINUTES', 'DURATION_IN_MINUTES'),
            ('MY_AVAIL_KIDS', 'MY_AVAIL_KIDS'),
            ('ENABLE_SHARE_EMAIL_QUESTION','ENABLE_SHARE_EMAIL_QUESTION'),
            ('ENABLE_BESTWAY_QUESTION', 'ENABLE_BESTWAY_QUESTION'),
            ('BILINGUAL', 'BILINGUAL'),
            ('SHOW_BRAINSTORM_LOGIN_HINT', 'SHOW_BRAINSTORM_LOGIN_HINT'),
            ('ANALYTICS_ENABLED', 'ANALYTICS_ENABLED'),
            ('CON_NAME', 'CON_NAME'),
            ('ADMIN_EMAIL', 'ADMIN_EMAIL'),
            ('BRAINSTORM_EMAIL', 'BRAINSTORM_EMAIL'),
            ('PROGRAM_EMAIL', 'PROGRAM_EMAIL'),
            ('REG_EMAIL', 'REG_EMAIL'),
            ('FIRST_DAY_START_TIME', 'FIRST_DAY_START_TIME'),
            ('OTHER_DAY_START_TIME', 'OTHER_DAY_START_TIME'),
            ('OTHER_DAY_STOP_TIME', 'OTHER_DAY_STOP_TIME'),
            ('LAST_DAY_STOP_TIME', 'LAST_DAY_STOP_TIME'),
            ('STANDARD_BLOCK_LENGTH', 'STANDARD_BLOCK_LENGTH'),
            ('DEFAULT_DURATION', 'DEFAULT_DURATION'),
            ('SMTP_ADDRESS', 'SMTP_ADDRESS'),
            ('SWIFT_DIRECTORY', 'SWIFT_DIRECTORY'),
            ('SECOND_LANG', 'SECOND_LANG'),
            ('SECOND_TITLE_CAPTION', 'SECOND_TITLE_CAPTION'),
            ('SECOND_DESCRIPTION_CAPTION', 'SECOND_DESCRIPTION_CAPTION'),
            ('SECOND_BIOGRAPHY_CAPTION', 'SECOND_BIOGRAPHY_CAPTION'),
            ('BASESESSIONDIR', 'BASESESSIONDIR'),
            ('CON_LOGO_IMAGE', 'CON_LOGO_IMAGE'),
        ]
        kwargs['choices'] = types
        super(ConfigConfig, self).__init__(*args, **kwargs)


class Config(models.Model):
    configid = models.AutoField(primary_key = True)
    configtype = ConfigType(max_length = 6)
    configname = ConfigConfig(max_length=40, unique = True)
    configvalue = models.CharField(max_length=255)
    configdescription = models.TextField()
    def __unicode__(self):
        return self.config
