UPDATE CongoDump SET
    firstname = CONCAT('firstname',badgeid),
    lastname = CONCAT('lastname',badgeid),
    badgename = CONCAT('badgename',badgeid),
    phone = CONCAT('phone',badgeid),
    email = CONCAT('email',badgeid,'@gmail.com'),
    postaddress1 = CONCAT('postaddress1',badgeid),
    postaddress2 = '',
    postcity = CONCAT('postcity',badgeid),
    poststate = 'MA',
    postzip = '02144',
    postcountry = 'USA';

TRUNCATE CongoDumpHistory;

UPDATE EmailHistory SET
    EmailTo = 'an_email@gmail.com';

UPDATE ParticipantPasswordResetRequests SET
    email = 'an_email@gmail.com',
    ipaddress = '192.168.1.1';

UPDATE Participants SET
    bio = CONCAT('This is the bio for ',badgeid,'.'),
    htmlbio = CONCAT('<p>This is the htmlbio for ',badgeid,'.</p>'),
    pubsname = CONCAT('pubsname',badgeid),
    uploadedphotofilename = NULL,
    approvedphotofilename = NULL,
    photodenialreasonothertext = NULL,
    photodenialreasonid = NULL,
    photouploadstatus = NULL;

UPDATE SessionEditHistory SET
    name = CONCAT('name',badgeid),
    email_address = CONCAT('email',badgeid,'@gmail.com');
