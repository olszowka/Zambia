<?php
require_once ('error_functions.php');

function configureNewUser($badgeid) {
    global $linki, $reg_link;
    if($badgeid == "brainstorm") return;
    
    // If the user does not appear in the CongoDump table, copy their record from the Capricon Registration database
    // and set them up as a Participant.
    $result = mysqli_query($linki, "SELECT badgeid FROM CongoDump WHERE badgeid = $badgeid");
    if(mysqli_num_rows($result) == 0) {
        $result = mysqli_query($reg_link, "SELECT PeopleID, BadgeName, FirstName, LastName, Phone1, Email, Address1, Address2, City, " . 
            "State, ZipCode, Country FROM People WHERE PeopleID = " . mysqli_real_escape_string($reg_link, $badgeid));
        $user = mysqli_fetch_object($result);
        mysqli_free_result($result);
        
        mysqli_query($linki, "INSERT INTO CongoDump (badgeid, badgename, firstname, lastname, phone, email, postaddress1, " .
            "postaddress2, postcity, poststate, postzip, postcountry, regtype) VALUES ($badgeid, '" . 
            $user->BadgeName . "', '" . 
            $user->FirstName . "', '" . 
            $user->LastName . "', '" . 
            $user->Phone1 . "', '" . 
            $user->Email . "', '" . 
            $user->Address1 . "', '" . 
            $user->Address2 . "', '" . 
            $user->City . "', '" . 
            $user->State . "', '" . 
            $user->ZipCode . "', '" . 
            $user->Country . "', 'noReg')");
        
        $pubsname = $user->FirstName . ' ' . $user->LastName;
        $sortedpubsname = '"' . $user->LastName . ', ' . $user->FirstName . '"';
        $query = "INSERT INTO Participants (badgeid, password, pubsname, sortedpubsname) VALUES ($badgeid, 'ManagedByCapReg', '$pubsname', $sortedpubsname) ";
        $query .= " ON DUPLICATE KEY UPDATE";
        $query .= " password = 'ManagedByCapReg',";
        $query .= " pubsname = '$pubsname',";
        $query .= " sortedpubsname = $sortedpubsname";
        if (!mysqli_query($linki, $query))
        {
            echo("Error description: " . mysqli_error($linki));
        }
        
        // permroleid values: 1 = Admin, 2 = Staff, 3 = Participant, 4 = Brainstorm
        $result = mysqli_query($linki, "SELECT badgeid FROM UserHasPermissionRole WHERE badgeid = $badgeid AND permroleid = 3");
        if(mysqli_num_rows($result) == 0) {
            mysqli_query($linki, "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES ($badgeid, 3)");
        }
        mysqli_free_result($result);
    }
}

function updateUser($badgeid) {
    global $linki, $reg_link, $message;
    if($badgeid == "brainstorm") return;
    
    //$badgeid = mysqli_real_escape_string($reg_link, $badgeid);
    $regconyear = CAP_REG_CON_YEAR;

    
    // If the user appears in the CongoDump table, update their data in the CongoDump table with data from reg table.
    // Get all data from registration system
    $query = <<<EOD
    SELECT
        P.PeopleID, 
        P.BadgeName, 
        P.FirstName, 
        P.LastName, 
        P.Phone1, 
        P.Email, 
        P.Address1, 
        P.Address2, 
        P.City, 
        P.State, 
        P.ZipCode, 
        P.Country, 
        PB.BadgeNumber,
        BT.Name
    FROM 
                        People P
        LEFT OUTER JOIN PurchasedBadges PB ON (P.PeopleID = PB.PeopleID AND PB.Year = '$regconyear' AND PB.Status = 'Paid')
        LEFT JOIN       BadgeTypes BT USING (BadgeTypeID)
    WHERE
        P.PeopleID = $badgeid
EOD;
    //$result2 = custom_mysqli_query_with_error_handling($reg_link, $query);
    $result2 = mysqli_query($reg_link, $query);
    
    if(mysqli_num_rows($result2) !== 0) {
        $user = mysqli_fetch_object($result2);

        $sql =  "UPDATE CongoDump SET ";
        $sql .= "  badgename='" . $user->BadgeName . "' ,";
        $sql .= "  firstname='" . $user->FirstName . "' ,";
        $sql .= "  lastname='" . $user->LastName . "' ,";
        $sql .= "  phone='" . $user->Phone1 . "' ,";
        $sql .= "  postaddress1='" . $user->Address1 . "' ,";
        $sql .= "  postaddress2='" . $user->Address2 . "' ,";
        $sql .= "  postcity='" . $user->City . "' ,";
        $sql .= "  poststate='" . $user->State . "' ,";
        $sql .= "  postzip='" . $user->ZipCode . "' ,";
        $sql .= "  postcountry='" . $user->Country . "' ,";
        if (!empty($user->Name))
            $sql .= "  regtype='" . $user->Name . "' ,";
        if (!empty($user->BadgeNumber))
            $sql .= "  badgenumber='" . $user->BadgeNumber . "' ,";
        $sql .= "  last_reg_update=NOW()";
        $sql .= "  WHERE badgeid = " . $badgeid;
        //echo "<p>" . $sql . "</p>";
        //exit(0);

        // Update name and address data.
        $result3 = mysqli_query($linki, $sql);
        if (!$result3) {
            $message .= mysqli_error($linki);
            //echo "<br>" . mysqli_error($linki) . "<br>" . "<pre>" . print_r($user,1) . "</pre>";
            //exit(0);
        }
    }
    else {
        $message .= " Count = " . mysqli_num_rows($result2);
    }
    mysqli_free_result($result2);

    
    // Make sure that there is an entry in the Participants table
    $result = mysqli_query($linki, "SELECT badgeid FROM Participants WHERE badgeid = $badgeid");
    if(mysqli_num_rows($result) == 0) {
        $pubsname = $user->FirstName . ' ' . $user->LastName;
        $sortedpubsname = '"' . $user->LastName . ', ' . $user->FirstName . '"';
        mysqli_query($linki, "INSERT INTO Participants (badgeid, password, pubsname, sortedpubsname) VALUES ($badgeid, 'ManagedByCapReg', '$pubsname', $sortedpubsname)");
        
        // permroleid values: 1 = Admin, 2 = Staff, 3 = Participant, 4 = Brainstorm
        $result2 = mysqli_query($linki, "SELECT badgeid FROM UserHasPermissionRole WHERE badgeid = $badgeid AND permroleid = 3");
        if(mysqli_num_rows($result2) == 0) {
            mysqli_query($linki, "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES ($badgeid, 3)");
        }
        mysqli_free_result($result2);
    }
    mysqli_free_result($result);
}



function updateUserAlternate($badgeid,$alternatebadgeid) {
    global $linki, $reg_link, $message;
    if($badgeid == "brainstorm") return;
    
    // If the user appears in the CongoDump table, update their data in the CongoDump table with data from reg table.
    $result = mysqli_query($linki, "SELECT badgeid, alternatebadgeid FROM CongoDump WHERE alternatebadgeid = $alternatebadgeid");
    if(mysqli_num_rows($result) !== 0) {
        $result2 = mysqli_query($reg_link, "SELECT PeopleID, BadgeName, FirstName, LastName, Phone1, Email, Address1, Address2, City, " . 
            "State, ZipCode, Country FROM People WHERE PeopleID = " . mysqli_real_escape_string($reg_link, $alternatebadgeid));
        
        if(mysqli_num_rows($result2) == 0) {
            printf(" Found Zed reg data. ");
        }
        
        if(mysqli_num_rows($result2) !== 0) {
            $user = mysqli_fetch_object($result2);
            if (mysqli_query($linki, "UPDATE CongoDump " .
                "  SET badgename='" . mysqli_real_escape_string($linki, $user->BadgeName) . "'" .
                "  , firstname='" . mysqli_real_escape_string($linki, $user->FirstName) . "'" .
                "  , lastname='" . mysqli_real_escape_string($linki, $user->LastName) . "'" .
                "  , phone='" . mysqli_real_escape_string($linki, $user->Phone1) . "'" .
                "  , postaddress1='" . mysqli_real_escape_string($linki, $user->Address1) . "'" .
                "  , postaddress2='" . mysqli_real_escape_string($linki, $user->Address2) . "'" .
                "  , postcity='" . mysqli_real_escape_string($linki, $user->City) . "'" .
                "  , poststate='" . mysqli_real_escape_string($linki, $user->State) . "'" .
                "  , postzip='" . mysqli_real_escape_string($linki, $user->ZipCode) . "'" .
                "  , postcountry='" . mysqli_real_escape_string($linki, $user->Country) . "'" .
                "  , last_reg_update=NOW()" .
                "  WHERE alternatebadgeid = " . mysqli_real_escape_string($linki, $alternatebadgeid)) === TRUE) {
                printf(" Successfully updated Zed reg data. ");
            }
            
            $result3 = mysqli_query($reg_link, "SELECT Name, BadgeNumber FROM PurchasedBadges, BadgeTypes " .
                " WHERE PurchasedBadges.PeopleID = " . mysqli_real_escape_string($reg_link, $alternatebadgeid) .
                " AND PurchasedBadges.Year = '" . REG_CON_YEAR . "' " .
                " AND PurchasedBadges.Status = 'Paid'" .
                " AND PurchasedBadges.BadgeTypeID = BadgeTypes.BadgeTypeID");
            if(mysqli_num_rows($result3) == 0) {
                printf(" No badge data found. ");
            }
            if(mysqli_num_rows($result3) !== 0) {
                $user1 = mysqli_fetch_object($result3);
                printf(" Found badge data. ");
                if (mysqli_query($linki, "UPDATE CongoDump " .
                    "  SET regtype='" . mysqli_real_escape_string($linki, $user1->Name) . "', " .
                    "      badgenumber='" . mysqli_real_escape_string($linki, $user1->BadgeNumber) . "'" .
                    "  WHERE alternatebadgeid = " . mysqli_real_escape_string($linki, $alternatebadgeid)) === TRUE) {
                    printf(" Updated badge data. ");
                }
            }
            mysqli_free_result($result3);
        }
        mysqli_free_result($result2);
    }
    mysqli_free_result($result);
}

$reg_link = mysqli_connect(CAPREG_DBHOSTNAME, CAPREG_DBUSERID, CAPREG_DBPASSWORD, CAPREG_DBDB);
if ($reg_link === false) {
    $message_error="Unable to connect to Registration database.<BR>No further execution possible.";
    RenderError($message_error);
    exit();
};
if (!mysqli_select_db($reg_link, CAPREG_DBDB)) {
    $message_error="Unable to open Registration database.<BR>No further execution possible.";
    RenderError($message_error);
    exit();
};
?>
