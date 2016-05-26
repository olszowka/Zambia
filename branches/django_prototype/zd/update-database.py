a = (
 ('CategoryHasReport', 'categoryhasreportid'),
 ('ParticipantAvailabilityDays', 'participantavailabilitydaysid'),
 ('ParticipantAvailabilityTimes', 'participantavailabilitytimesid'),
 ('ParticipantHasCredential', 'participanthascredentialid'),
 ('ParticipantHasRole', 'participanthasroleid'),
 ('ParticipantSessionInterest', 'participantsessioninterestid'),
 ('PreviousConTracks', 'previouscontrackid'),
 ('PreviousSessions', 'melvin'),
 ('RoomHasSet', 'roomhassetid'),
 ('SessionEditHistory', 'sessionedithistoryid'),
 ('SessionHasFeature', 'sessionhasfeatureid'),
 ('SessionHasPubChar', 'sessionhaspubcharid'),
 ('SessionHasService', 'sessionhasserviceid'),
 ('TrackCompatibility', 'trackcompatibilityid'),
 ('UserHasPermissionRole', 'userhaspermissionroleid'),
)
for (t, f) in a:
    print 'alter table', t, 'add', f, 'int(11) NOT NULL;'
    print 'alter table', t, 'add key', f + '_key (', f, ');'
    print 'alter table', t, 'change', f, f, 'int(11) NOT NULL auto_increment;'
    print 'alter table', t, 'add unique (', f, ');'
    print 'alter table', t, 'drop key', f + '_key', ';'

