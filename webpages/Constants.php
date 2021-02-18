<?php
//	Copyright (c) 2011-2017 The Zambia Group. All rights reserved. See copyright document for more details.
define("ParticipantAddSession", 1000);
define("StaffInviteSession", 1001);
// which header has been used
define("HEADER_BRAINSTORM", 2001);
define("HEADER_PARTICIPANT", 2002);
define("HEADER_STAFF", 2003);
// is a submit too old to process, in seconds
define("SubmitAgeLimit", 3600);
// Photo Upload Status
// bit 0: Uploaded Photo Available (0 = no, 1 = yes)
define("PHOTO_UPLOAD_MASK", 1);
// bit 1: Uploaded Photo Denied (0 = no, 1 = yes)
define("PHOTO_DENIED_MASK", 2);
// bit 2: Approved Photo Availalbe (0 = no, 1 = yes)
define("PHOTO_APPROVED_MASK", 4);
// Photos needing Approval Mask = Uploaded + !denied
define("PHOTO_NEED_APPROVAL_MASK", 3);
define("PHOTO_NEED_APPROVAL", 1);
?>