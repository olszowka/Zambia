<?php
// Copyright (c) 2006-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors, $pgconn;
$returnAjaxErrors = true;
$return500errors = true;
$pgconn = null;
require_once('StaffCommonCode.php'); // will check for staff privileges
require('EditPermRoles_FNC.php');
// skip to below all functions

// gets data for a participant to be displayed.  Returns as XML

function import_users() {
    global $linki, $message_error, $message, $pgconn;
    $loggedInUserBadgeId = $_SESSION["badgeid"];
    $idsToAddarr = getArrayOfInts("idsToAdd");
    $rolesToAddArr = getArrayOfInts("rolesToAdd");

    if ($idsToAddarr === false || count($idsToAddarr) === 0) {
        RenderErrorAjax("No users selected to import");
        exit();
    }

    if ($rolesToAddArr === false || count($rolesToAddArr) === 0) {
        RenderErrorAjax("No roles assigned to users being imported");
        exit();
    }

    ['mayIEditAllRoles' => $mayIEditAllRoles, 'rolesIMayEditArr' => $rolesIMayEditArr] = fetchMyEditableRoles($loggedInUserBadgeId);
    if (!$mayIEditAllRoles) {
        if (count(array_diff($paramArray['permissionRoles'], $rolesToAddArr)) > 0) {
           RenderErrorAjax("Server configuration error: You attempting to add roles you do not have permission to add. Seek assistance.");
           exit();
        }
    }

    //error_log("ids to add arr:");
    //var_error_log($idsToAddarr);
    //error_log("roles to add arr:");
    //var_error_log($rolesToAddArr);

    $idstr = join(",", $idsToAddarr);
    $usercnt = count($idsToAddarr);

    if ($pgconn == null) {
        //error_log("making new Postgress connection");
        $pgconn = pg_connect(WELLINGTONPROD);
        if (!$pgconn) {
            RenderErrorAjax("Unable to connect to Wellington");
            exit();
        }
    }
    //error_log("postgress connection good");


    // get the array of users to add to CongoDump
    $sql = <<<EOD
SELECT
	r.membership_number AS badgeid,
	CASE
		WHEN COALESCE(ct.preferred_first_name, '') <> '' THEN ct.preferred_first_name
		ELSE ct.first_name
	END AS firstname,
    CASE
		WHEN COALESCE(ct.preferred_last_name, '') <> '' THEN ct.preferred_last_name
		ELSE ct.last_name
	END AS lastname,
	CASE
		WHEN ct.badge_title <> '' THEN ct.badge_title
		ELSE TRIM(
			CASE
				WHEN COALESCE(ct.preferred_first_name, '') <> '' THEN ct.preferred_first_name
				ELSE ct.first_name
			END || ' ' ||
			CASE
				WHEN COALESCE(ct.preferred_last_name, '') <> '' THEN ct.preferred_last_name
				ELSE ct.last_name
			END
		)
	END	AS badgename,
    u.email AS email,
    ct.address_line_1 AS postaddress1,
    ct.address_line_2 AS postaddress2,
	ct.city AS postcity,
    ct.province AS poststate,
    ct.postal AS postzip,
    ct.country AS postcountry,
	m.name AS regtype
FROM public.reservations r
JOIN public.claims cl ON (cl.reservation_id = r.id AND cl.active_to IS NULL)
JOIN public.dc_contacts ct ON (cl.id = ct.claim_id)
JOIN public.users u ON (cl.user_id = u.id)
JOIN public.orders o ON (r.id = o.reservation_id AND o.active_to IS NULL)
JOIN public.memberships m ON (o.membership_id = m.id)
WHERE
	r.membership_number IN ($idstr);
EOD;
    $result = pg_query($pgconn, $sql);
    if (!$result) {
        RenderErrorAjax("Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING));
        exit();
    }

    $sqlcd = "INSERT INTO CongoDump (badgeid,firstname,lastname,badgename,email,postaddress1,postaddress2,postcity,poststate,postzip,postcountry,regtype)\n VALUES ";
    $sqlpart = "INSERT INTO Participants (badgeid, password, pubsname)\n VALUES ";
    $sqlrole = "INSERT INTO UserHasPermissionRole (badgeid, permroleid)\n VALUES ";

    $rows = 0;
    while ($row = pg_fetch_assoc($result)) {
        if ($rows > 0) {
            $sqlcd .= ",\n ";
            $sqlpart .= ",\n ";
            $sqlrole .= ",\n ";
        }

        $rows++;
        $sqlcd .= "('"
            . mysqli_real_escape_string($linki, $row['badgeid']) . "', '"
            . mysqli_real_escape_string($linki, $row['firstname']) . "', '"
            . mysqli_real_escape_string($linki, $row['lastname']) . "', '"
            . mysqli_real_escape_string($linki, $row['badgename']) . "', '"
            . mysqli_real_escape_string($linki, $row['email']) . "', '"
            . mysqli_real_escape_string($linki, $row['postaddress1']) . "', '"
            . mysqli_real_escape_string($linki, $row['postaddress2']) . "', '"
            . mysqli_real_escape_string($linki, $row['postcity']) . "', '"
            . mysqli_real_escape_string($linki, $row['poststate']) . "', '"
            . mysqli_real_escape_string($linki, $row['postzip']) . "', '"
            . mysqli_real_escape_string($linki, $row['postcountry']) . "', '"
            . mysqli_real_escape_string($linki, $row['regtype'])
            . "')";

        $sqlpart .= "('"
            . mysqli_real_escape_string($linki, $row['badgeid']) . "', '"
            . "invalid', '"
            . mysqli_real_escape_string($linki, $row['badgename'])
             . "')";

        $ids = 0;
        foreach ($rolesToAddArr as $id) {
            if ($ids > 0)
                $sqlrole .= ",\n ";
            $ids++;

            $sqlrole .= "('"
                . mysqli_real_escape_string($linki, $row['badgeid']) . "', '"
                . mysqli_real_escape_string($linki, $id)
            . "')";
        }
    }
    $sqlcd .= ";\n";
    $sqlpart .= ";\n";
    $sqlrole .= ";\n";

    error_log($sqlcd);
    error_log($sqlpart);
    error_log($sqlrole);
    // start the transaction
    mysqli_query_exit_on_error("START TRANSACTION;");

    // Import reg info to CongoDump
    mysqli_query_with_error_handling($sqlcd);
    $rows = mysqli_affected_rows($linki);

     if (is_null($rows) || $rows !== $usercnt) {
         mysqli_query_with_error_handling("ROLLBACK;");
         RenderErrorAjax("Error: Some of the users to be imported already exist or error importing users");
         exit();
     }
     // now build the participants

     mysqli_query_with_error_handling($sqlpart);
     $rows = mysqli_affected_rows($linki);
     if (is_null($rows) || $rows !== $usercnt) {
         mysqli_query_with_error_handling("ROLLBACK;");
         RenderErrorAjax("Error: creating participants from reg import");
         exit();
     }
     // and add the permissions
     mysqli_query_with_error_handling($sqlrole);
     $rows = mysqli_affected_rows($linki);

    if (is_null($rows) || $rows < $usercnt) {
        mysqli_query_with_error_handling("ROLLBACK;");
        RenderErrorAjax("Error: adding permission roles from reg import");
        exit();
    }
    // all done commit the sequence
    mysqli_query_with_error_handling("COMMIT;");
    $message = "<p>Users imported successfully.</p>";
?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    </div>
</div>
<?php
}

function perform_search() {
    global $linki, $message_error, $pgconn;

    if ($pgconn == null) {
        //error_log("making new Postgress connection");
        $pgconn = pg_connect(WELLINGTONPROD);
        if (!$pgconn) {
            RenderErrorAjax("Unable to connect to Wellington");
            exit();
        }
    }
    //error_log("postgress connection good");

    $searchString = getString("searchString");
    if ($searchString == "")
        exit();
           $query = <<<EOD
SELECT
	r.membership_number AS id,
	CASE
		WHEN COALESCE(ct.preferred_last_name, '') <> '' THEN ct.preferred_last_name
		ELSE ct.last_name
	END AS last_name,
	CASE
		WHEN COALESCE(ct.preferred_first_name, '') <> '' THEN ct.preferred_first_name
		ELSE ct.first_name
	END AS first_name, u.email AS email_addr,
	CASE
		WHEN ct.badge_title <> '' THEN ct.badge_title
		ELSE TRIM(
			CASE
				WHEN COALESCE(ct.preferred_first_name, '') <> '' THEN ct.preferred_first_name
				ELSE ct.first_name
			END || ' ' ||
			CASE
				WHEN COALESCE(ct.preferred_last_name, '') <> '' THEN ct.preferred_last_name
				ELSE ct.last_name
			END
		)
	END	AS badge_name,
	ct.city, ct.province AS state, ct.postal AS zip,
	m.name AS regtype
FROM public.reservations r
JOIN public.claims cl ON (cl.reservation_id = r.id AND cl.active_to IS NULL)
JOIN public.dc_contacts ct ON (cl.id = ct.claim_id)
JOIN public.users u ON (cl.user_id = u.id)
JOIN public.orders o ON (r.id = o.reservation_id AND o.active_to IS NULL)
JOIN public.memberships m ON (o.membership_id = m.id)
EOD;

    if (is_numeric($searchString)) {
        //error_log("Numeric string");
        $query .= <<<EOD

WHERE
	r.membership_number = $1
ORDER BY
	last_name, first_name
EOD;
    } else {
        //error_log("non numberic string");
        $searchString = '%' . $searchString . '%';
        $query .= <<<EOD
WHERE
    (ct.badge_title ILIKE $1
    OR ct.last_name ILIKE $1
    OR ct.first_name ILIKE $1
    OR ct.badge_title ILIKE $1
    OR ct.preferred_first_name ILIKE $1
    OR ct.preferred_last_name ILIKE $1)
ORDER BY
	ct.last_name, ct.first_name
EOD;
    }
    //error_log("query = '" . $query . "'");

    $param_arr = array($searchString);
    $result = pg_query_params($pgconn, $query, $param_arr);
    if (!$result) {
        RenderErrorAjax("Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING));
        exit();
    }

    // fetch all existing users in Zambia
    $badgeids = [];
    $sql = "SELECT badgeid FROM CongoDump;";
    $results = mysqli_query_with_error_handling($sql);
    while ($row = mysqli_fetch_assoc($results))
        $badgeids[$row["badgeid"]] = 1;

    mysqli_free_result($results);

    $results = [];
    while ($row = pg_fetch_assoc($result))
        if (array_key_exists($row["id"], $badgeids) == false)
            $results[] = $row;

    $xml = ObjecttoXML("searchReg", $results);
    pg_free_result($result);

    if (!$xml) {
        echo $message_error;
        exit();
    }
    header("Content-Type: text/html");
    $paramArray = array("userIdPrompt" => USER_ID_PROMPT);
    //echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $xml->saveXML(), "i")); //for debugging only
    RenderXSLT('Discon3ImportRegUser.xsl', $paramArray, $xml);
	exit();
}

function fetch_user_perm_roles() {
    global $message_error;
    if (may_I('EditUserPermRoles')) {
        $loggedInUserBadgeId = $_SESSION['badgeid'];
        ['mayIEditAllRoles' => $mayIEditAllRoles, 'rolesIMayEditArr' => $rolesIMayEditArr] = fetchMyEditableRoles($loggedInUserBadgeId);
        if ($mayIEditAllRoles) {
            $query["permroles"] = "SELECT PR.permrolename, PR.permroleid, 1 AS mayedit FROM PermissionRoles PR ORDER BY PR.display_order;";
            $resultXML = mysql_query_XML($query);
        } else { // has permission to edit only specific perm roles
            $query["query"] = <<<EOD
SELECT
        PR.permrolename, PR.permroleid, IF(ISNULL(SQ.elementid), 0, 1) AS mayedit
    FROM PermissionRoles PR
    LEFT JOIN (
            SELECT
                    PA.elementid
                FROM
                         UserHasPermissionRole UHPR
                    JOIN Permissions P USING (permroleid)
                    JOIN PermissionAtoms PA USING (permatomid)
                WHERE
                        UHPR.badgeid = ?
                    AND PA.permatomtag = 'EditUserPermRoles'
                    AND PA.elementid IS NOT NULL
                    ) AS SQ ON SQ.elementid = PR.permroleid
    ORDER BY mayedit DESC, PR.display_order;
EOD;
            $resultXML = mysql_prepare_query_XML(
                $query,
                array("permroles" => "s"),
                array("permroles" => array($loggedInUserBadgeId)));
        }
    } else { // has no permission to edit user perm roles
        $query["permroles"] = <<<EOD
SELECT PR.permrolename, PR.permroleid, 0 AS mayedit
    FROM PermissionRoles PR
    ORDER BY PR.display_order;
EOD;
        $resultXML = mysql_query_XML($query);

    }
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
    }
    // $foo = mb_ereg_replace("<(row|query)([^>]*)/[ ]*>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"); //for debugging only
    RenderXSLT('FetchUserPermRoles.xsl', array(), $resultXML);
}

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
if (!isLoggedIn() || !may_I('d3_ImportUsers')) {
    $message_error = "You are not logged in or your session has expired.";
    RenderErrorAjax($message_error);
    exit();

}
$ajax_request_action = getString("ajax_request_action");
if (is_null($ajax_request_action)) {
    $message_error = "Internal error.";
    RenderErrorAjax($message_error);
    exit();
}
//error_log("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action");
switch ($ajax_request_action) {
    case "perform_search":
        perform_search();
        break;
    case "import_users":
        import_users();
        break;
    case "fetch_user_perm_roles":
        fetch_user_perm_roles();
        break;
    default:
        $message_error = "Internal error.";
        RenderErrorAjax($message_error);
        exit();
}

?>
