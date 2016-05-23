# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
    ]

    operations = [
        migrations.CreateModel(
            name='BioEditStatus',
            fields=[
                ('bioeditstatusid', models.AutoField(serialize=False, primary_key=True)),
                ('bioeditstatusname', models.CharField(max_length=60, null=True, blank=True)),
                ('display_order', models.IntegerField()),
            ],
            options={
                'db_table': 'BioEditStatuses',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='CategoryHasReport',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'CategoryHasReport',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='CongoDump',
            fields=[
                ('badgeid', models.CharField(max_length=15, serialize=False, primary_key=True)),
                ('firstname', models.CharField(max_length=30, null=True, blank=True)),
                ('lastname', models.CharField(max_length=40, null=True, blank=True)),
                ('badgename', models.CharField(max_length=51, null=True, blank=True)),
                ('phone', models.CharField(max_length=100, null=True, blank=True)),
                ('email', models.CharField(max_length=100, null=True, blank=True)),
                ('postaddress1', models.CharField(max_length=100, null=True, blank=True)),
                ('postaddress2', models.CharField(max_length=100, null=True, blank=True)),
                ('postcity', models.CharField(max_length=50, null=True, blank=True)),
                ('poststate', models.CharField(max_length=25, null=True, blank=True)),
                ('postzip', models.CharField(max_length=10, null=True, blank=True)),
                ('postcountry', models.CharField(max_length=25, null=True, blank=True)),
                ('regtype', models.CharField(max_length=40, null=True, blank=True)),
            ],
            options={
                'db_table': 'CongoDump',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Credential',
            fields=[
                ('credentialid', models.AutoField(serialize=False, primary_key=True)),
                ('credentialname', models.CharField(max_length=100, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Credentials',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='CustomText',
            fields=[
                ('customtextid', models.AutoField(serialize=False, primary_key=True)),
                ('page', models.CharField(max_length=100, null=True, blank=True)),
                ('tag', models.CharField(max_length=25, null=True, blank=True)),
                ('textcontents', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'CustomText',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Division',
            fields=[
                ('divisionid', models.AutoField(serialize=False, primary_key=True)),
                ('divisionname', models.CharField(max_length=30, null=True, blank=True)),
                ('display_order', models.IntegerField()),
            ],
            options={
                'db_table': 'Divisions',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='EmailCC',
            fields=[
                ('emailccid', models.AutoField(serialize=False, primary_key=True)),
                ('description', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField()),
                ('emailaddress', models.CharField(max_length=255, null=True, blank=True)),
            ],
            options={
                'db_table': 'EmailCC',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='EmailFrom',
            fields=[
                ('emailfromid', models.AutoField(serialize=False, primary_key=True)),
                ('emailfromdescription', models.CharField(max_length=30, null=True, blank=True)),
                ('display_order', models.IntegerField()),
                ('emailfromaddress', models.CharField(max_length=255, null=True, blank=True)),
            ],
            options={
                'db_table': 'EmailFrom',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='EmailQueue',
            fields=[
                ('emailqueueid', models.AutoField(serialize=False, primary_key=True)),
                ('emailto', models.CharField(max_length=255, null=True, blank=True)),
                ('emailfrom', models.CharField(max_length=255, null=True, blank=True)),
                ('emailcc', models.CharField(max_length=255, null=True, blank=True)),
                ('emailsubject', models.CharField(max_length=255, null=True, blank=True)),
                ('body', models.TextField(null=True, blank=True)),
                ('status', models.IntegerField()),
                ('emailtimestamp', models.DateTimeField()),
            ],
            options={
                'db_table': 'EmailQueue',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='EmailTo',
            fields=[
                ('emailtoid', models.AutoField(serialize=False, primary_key=True)),
                ('emailtodescription', models.CharField(max_length=75, null=True, blank=True)),
                ('display_order', models.IntegerField()),
                ('emailtoquery', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'EmailTo',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Feature',
            fields=[
                ('featureid', models.AutoField(serialize=False, primary_key=True)),
                ('featurename', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Features',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='KidsCategory',
            fields=[
                ('kidscatid', models.AutoField(serialize=False, primary_key=True)),
                ('kidscatname', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'verbose_name': 'KidsCategory',
                'db_table': 'KidsCategories',
                'managed': False,
                'verbose_name_plural': 'KidsCategories',
            },
        ),
        migrations.CreateModel(
            name='LanguageStatus',
            fields=[
                ('languagestatusid', models.AutoField(serialize=False, primary_key=True)),
                ('languagestatusname', models.CharField(max_length=30, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'verbose_name': 'LanguageStatus',
                'db_table': 'LanguageStatuses',
                'managed': False,
                'verbose_name_plural': 'LanguageStatuses',
            },
        ),
        migrations.CreateModel(
            name='Participant',
            fields=[
                ('badgeid', models.CharField(max_length=15, serialize=False, primary_key=True)),
                ('password', models.CharField(max_length=32, null=True, blank=True)),
                ('bestway', models.CharField(max_length=12, null=True, blank=True)),
                ('interested', models.IntegerField(null=True, blank=True)),
                ('bio', models.TextField(null=True, blank=True)),
                ('editedbio', models.TextField(null=True, blank=True)),
                ('scndlangbio', models.TextField(null=True, blank=True)),
                ('biolockedby', models.CharField(max_length=15, null=True, blank=True)),
                ('pubsname', models.CharField(max_length=50, null=True, blank=True)),
                ('share_email', models.IntegerField(null=True, blank=True)),
                ('staff_notes', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Participants',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantAvailabilityDay',
            fields=[
                ('day', models.SmallIntegerField()),
                ('maxprog', models.IntegerField(null=True, blank=True)),
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'ParticipantAvailabilityDays',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantAvailabilityTime',
            fields=[
                ('availabilitynum', models.IntegerField()),
                ('starttime', models.TimeField(null=True, blank=True)),
                ('endtime', models.TimeField(null=True, blank=True)),
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'ParticipantAvailabilityTimes',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantHasCredential',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'ParticipantHasCredential',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantHasRole',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'ParticipantHasRole',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantonSession',
            fields=[
                ('moderator', models.IntegerField(null=True, blank=True)),
                ('ts', models.DateTimeField()),
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'ParticipantOnSession',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantSessionInterest',
            fields=[
                ('rank', models.IntegerField(null=True, blank=True)),
                ('willmoderate', models.IntegerField(null=True, blank=True)),
                ('comments', models.TextField(null=True, blank=True)),
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'ParticipantSessionInterest',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PatchLog',
            fields=[
                ('patchname', models.CharField(max_length=40, null=True, blank=True)),
                ('timestamp', models.DateTimeField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'PatchLog',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Permission',
            fields=[
                ('permissionid', models.AutoField(serialize=False, primary_key=True)),
                ('badgeid', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Permissions',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PermissionAtom',
            fields=[
                ('permatomid', models.AutoField(serialize=False, primary_key=True)),
                ('permatomtag', models.CharField(max_length=20)),
                ('elementid', models.IntegerField(null=True, blank=True)),
                ('page', models.CharField(max_length=30, null=True, blank=True)),
                ('notes', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'PermissionAtoms',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PermissionRole',
            fields=[
                ('permroleid', models.AutoField(serialize=False, primary_key=True)),
                ('permrolename', models.CharField(max_length=100, null=True, blank=True)),
                ('notes', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'PermissionRoles',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Phase',
            fields=[
                ('phaseid', models.AutoField(serialize=False, primary_key=True)),
                ('phasename', models.CharField(max_length=100, null=True, blank=True)),
                ('current', models.IntegerField(null=True, blank=True)),
                ('notes', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Phases',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PreviousCon',
            fields=[
                ('previousconid', models.AutoField(serialize=False, primary_key=True)),
                ('previousconname', models.CharField(max_length=128, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'PreviousCons',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PreviousConTrack',
            fields=[
                ('previoustrackid', models.AutoField(serialize=False, primary_key=True)),
                ('trackname', models.CharField(max_length=50, null=True, blank=True)),
            ],
            options={
                'db_table': 'PreviousConTracks',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PreviousParticipant',
            fields=[
                ('badgeid', models.CharField(max_length=15, serialize=False, primary_key=True)),
                ('bio', models.TextField(null=True, blank=True)),
                ('staff_notes', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'PreviousParticipants',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PreviouSsession',
            fields=[
                ('previoussessionid', models.IntegerField()),
                ('title', models.CharField(max_length=100, null=True, blank=True)),
                ('secondtitle', models.CharField(max_length=100, null=True, blank=True)),
                ('pocketprogtext', models.TextField(null=True, blank=True)),
                ('progguiddesc', models.TextField(null=True, blank=True)),
                ('persppartinfo', models.TextField(null=True, blank=True)),
                ('duration', models.TimeField(null=True, blank=True)),
                ('estatten', models.IntegerField(null=True, blank=True)),
                ('signupreq', models.IntegerField(null=True, blank=True)),
                ('notesforpart', models.TextField(null=True, blank=True)),
                ('notesforprog', models.TextField(null=True, blank=True)),
                ('invitedguest', models.IntegerField(null=True, blank=True)),
                ('importedsessionid', models.IntegerField(null=True, blank=True)),
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'PreviousSessions',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PubCharacteristic',
            fields=[
                ('pubcharid', models.AutoField(serialize=False, primary_key=True)),
                ('pubcharname', models.CharField(max_length=30, null=True, blank=True)),
                ('pubchartag', models.CharField(max_length=10, null=True, blank=True)),
                ('display_order', models.IntegerField()),
            ],
            options={
                'db_table': 'PubCharacteristics',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='PubStatus',
            fields=[
                ('pubstatusid', models.AutoField(serialize=False, primary_key=True)),
                ('pubstatusname', models.CharField(max_length=12, null=True, blank=True)),
                ('display_order', models.IntegerField()),
            ],
            options={
                'verbose_name': 'PubStatus',
                'db_table': 'PubStatuses',
                'managed': False,
                'verbose_name_plural': 'PubStatuses',
            },
        ),
        migrations.CreateModel(
            name='Regtype',
            fields=[
                ('regtype', models.CharField(max_length=40, serialize=False, primary_key=True)),
                ('message', models.CharField(max_length=100, null=True, blank=True)),
            ],
            options={
                'db_table': 'RegTypes',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ReportCategory',
            fields=[
                ('reportcategoryid', models.AutoField(serialize=False, primary_key=True)),
                ('description', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField()),
            ],
            options={
                'verbose_name': 'ReportCategory',
                'db_table': 'ReportCategories',
                'managed': False,
                'verbose_name_plural': 'ReportCategories',
            },
        ),
        migrations.CreateModel(
            name='ReportQuery',
            fields=[
                ('reportqueryid', models.AutoField(serialize=False, primary_key=True)),
                ('queryname', models.CharField(max_length=25)),
                ('query', models.TextField(null=True, blank=True)),
            ],
            options={
                'verbose_name': 'ReportQuery',
                'db_table': 'ReportQueries',
                'managed': False,
                'verbose_name_plural': 'ReportQueries',
            },
        ),
        migrations.CreateModel(
            name='ReportType',
            fields=[
                ('reporttypeid', models.AutoField(serialize=False, primary_key=True)),
                ('title', models.CharField(max_length=200)),
                ('description', models.TextField(null=True, blank=True)),
                ('technology', models.CharField(max_length=200, null=True, blank=True)),
                ('oldmechanism', models.IntegerField()),
                ('ondemand', models.IntegerField(null=True, blank=True)),
                ('filename', models.CharField(max_length=60, null=True, blank=True)),
                ('xsl', models.TextField(null=True, blank=True)),
                ('download', models.IntegerField(null=True, blank=True)),
                ('downloadfilename', models.CharField(max_length=25, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'ReportTypes',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Role',
            fields=[
                ('roleid', models.AutoField(serialize=False, primary_key=True)),
                ('rolename', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Roles',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Room',
            fields=[
                ('roomid', models.AutoField(serialize=False, primary_key=True)),
                ('roomname', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
                ('height', models.CharField(max_length=100, null=True, blank=True)),
                ('dimensions', models.CharField(max_length=100, null=True, blank=True)),
                ('area', models.CharField(max_length=100, null=True, blank=True)),
                ('function', models.CharField(max_length=100, null=True, blank=True)),
                ('floor', models.CharField(max_length=50, null=True, blank=True)),
                ('notes', models.TextField(null=True, blank=True)),
                ('opentime1', models.TimeField(null=True, blank=True)),
                ('closetime1', models.TimeField(null=True, blank=True)),
                ('opentime2', models.TimeField(null=True, blank=True)),
                ('closetime2', models.TimeField(null=True, blank=True)),
                ('opentime3', models.TimeField(null=True, blank=True)),
                ('closetime3', models.TimeField(null=True, blank=True)),
                ('is_scheduled', models.IntegerField()),
            ],
            options={
                'db_table': 'Rooms',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='RoomHasSet',
            fields=[
                ('capacity', models.IntegerField(null=True, blank=True)),
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'RoomHasSet',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='RoomSet',
            fields=[
                ('roomsetid', models.AutoField(serialize=False, primary_key=True)),
                ('roomsetname', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'RoomSets',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Schedule',
            fields=[
                ('scheduleid', models.AutoField(serialize=False, primary_key=True)),
                ('starttime', models.TimeField()),
            ],
            options={
                'db_table': 'Schedule',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Service',
            fields=[
                ('serviceid', models.AutoField(serialize=False, primary_key=True)),
                ('servicename', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Services',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Session',
            fields=[
                ('sessionid', models.AutoField(serialize=False, primary_key=True)),
                ('pubsno', models.CharField(max_length=50, null=True, blank=True)),
                ('title', models.CharField(max_length=100, null=True, blank=True)),
                ('secondtitle', models.CharField(max_length=100, null=True, blank=True)),
                ('pocketprogtext', models.TextField(null=True, blank=True)),
                ('progguiddesc', models.TextField(null=True, blank=True)),
                ('persppartinfo', models.TextField(null=True, blank=True)),
                ('duration', models.TimeField(null=True, blank=True)),
                ('estatten', models.IntegerField(null=True, blank=True)),
                ('signupreq', models.IntegerField(null=True, blank=True)),
                ('notesforpart', models.TextField(null=True, blank=True)),
                ('servicenotes', models.TextField(null=True, blank=True)),
                ('notesforprog', models.TextField(null=True, blank=True)),
                ('warnings', models.IntegerField(null=True, blank=True)),
                ('invitedguest', models.IntegerField(null=True, blank=True)),
                ('ts', models.DateTimeField()),
            ],
            options={
                'db_table': 'Sessions',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='SessionEditCode',
            fields=[
                ('sessioneditcode', models.AutoField(serialize=False, primary_key=True)),
                ('description', models.CharField(max_length=40, null=True, blank=True)),
                ('display_order', models.IntegerField()),
            ],
            options={
                'db_table': 'SessionEditCodes',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='SessionEditHistory',
            fields=[
                ('name', models.CharField(max_length=40, null=True, blank=True)),
                ('email_address', models.CharField(max_length=75, null=True, blank=True)),
                ('timestamp', models.DateTimeField()),
                ('editdescription', models.TextField(null=True, blank=True)),
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'SessionEditHistory',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='SessionHasFeature',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'SessionHasFeature',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='SessionHasPubChar',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'SessionHasPubChar',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='SessionHasService',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'SessionHasService',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='SessionStatus',
            fields=[
                ('statusid', models.AutoField(serialize=False, primary_key=True)),
                ('statusname', models.CharField(max_length=50, null=True, blank=True)),
                ('validate', models.IntegerField()),
                ('may_be_scheduled', models.IntegerField()),
                ('display_order', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'verbose_name': 'SessionStatus',
                'db_table': 'SessionStatuses',
                'managed': False,
                'verbose_name_plural': 'SessionStatuses',
            },
        ),
        migrations.CreateModel(
            name='Time',
            fields=[
                ('timeid', models.IntegerField(serialize=False, primary_key=True)),
                ('timedisplay', models.CharField(max_length=14, null=True, blank=True)),
                ('timevalue', models.TimeField(null=True, blank=True)),
                ('next_day', models.IntegerField(null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
                ('avail_start', models.IntegerField(null=True, blank=True)),
                ('avail_end', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Times',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Track',
            fields=[
                ('trackid', models.AutoField(serialize=False, primary_key=True)),
                ('trackname', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
                ('selfselect', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Tracks',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='TrackCompatibility',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'TrackCompatibility',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='Type',
            fields=[
                ('typeid', models.AutoField(serialize=False, primary_key=True)),
                ('typename', models.CharField(max_length=50, null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
                ('selfselect', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Types',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='UserHasPermissionRole',
            fields=[
                ('djangoid', models.AutoField(serialize=False, primary_key=True)),
            ],
            options={
                'db_table': 'UserHasPermissionRole',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipAntavailability',
            fields=[
                ('badgeid', models.ForeignKey(primary_key=True, db_column='badgeid', serialize=False, to='zambia.Participant')),
                ('maxprog', models.IntegerField(null=True, blank=True)),
                ('preventconflict', models.CharField(max_length=255, null=True, blank=True)),
                ('otherconstraints', models.CharField(max_length=255, null=True, blank=True)),
                ('numkidsfasttrack', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'ParticipantAvailability',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantInterest',
            fields=[
                ('badgeid', models.ForeignKey(primary_key=True, db_column='badgeid', serialize=False, to='zambia.Participant')),
                ('yespanels', models.TextField(null=True, blank=True)),
                ('nopanels', models.TextField(null=True, blank=True)),
                ('yespeople', models.TextField(null=True, blank=True)),
                ('nopeople', models.TextField(null=True, blank=True)),
                ('otherroles', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'ParticipantInterests',
                'managed': False,
            },
        ),
        migrations.CreateModel(
            name='ParticipantSuggestion',
            fields=[
                ('badgeid', models.ForeignKey(primary_key=True, db_column='badgeid', serialize=False, to='zambia.Participant')),
                ('paneltopics', models.TextField(null=True, blank=True)),
                ('otherideas', models.TextField(null=True, blank=True)),
                ('suggestedguests', models.TextField(null=True, blank=True)),
            ],
            options={
                'db_table': 'ParticipantSuggestions',
                'managed': False,
            },
        ),
    ]
