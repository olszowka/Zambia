<?php
// Copyright (c) 2006-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php'); // will check for staff privileges
require('EditPermRoles_FNC.php');
// skip to below all functions

// gets data for a participant to be displayed.  Returns as XML

function import_users() {
    global $linki, $message_error, $message;
    $loggedInUserBadgeId = $_SESSION["badgeid"];
    $idsToAddarr = getArrayOfInts("idsToAdd");
    $rolesToAddArr = getArrayOfInts("rolesToAdd");
    $regdbname = REG_DBNAME;

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

    // start the transaction
    mysqli_query_exit_on_error("START TRANSACTION;");

    // Import reg info to CongoDump
    // MySQL 5.x doesn't support ROW_NUMBER(), so it has to be faked with this kludg
    $sql = <<<EOD
INSERT INTO CongoDump (badgeid,firstname,lastname,badgename,phone,email,postaddress1,postaddress2,postcity,poststate,postzip,postcountry,regtype)
SELECT id, first_name, last_name, badge_name, phone, email_addr, address, addr_2, city, state, zip, country, label
FROM (
    SELECT @row_number := CASE WHEN @id = id THEN @row_number + 1 ELSE 1 END AS num,
    @id := id AS id, first_name, last_name, badge_name, phone, email_addr,
    address, addr_2, city, state, zip, country, label
    FROM (
        SELECT P.id, first_name, last_name, badge_name, phone, email_addr,
        address, addr_2, city, state, zip, country, M.label
        FROM $regdbname.perinfo P
        JOIN $regdbname.reg R ON (R.perid = P.id)
        JOIN $regdbname.memList M ON (R.memID = M.id AND R.conid = M.conid)
        WHERE P.id IN ($idstr)
        ORDER BY P.id, M.conid desc
    ) T, (SELECT @id:=0,@row_number:=0) as ID
) T2
WHERE num = 1;
EOD;
     mysqli_query_with_error_handling($sql);
     $rows = mysqli_affected_rows($linki);

     if (is_null($rows) || $rows !== $usercnt) {
         mysqli_query_with_error_handling("ROLLBACK;");
         RenderErrorAjax("Error: Some of the users to be imported already exist or error importing users");
         exit();
     }
     // now build the participants
     $sql = <<<EOD
INSERT INTO Participants (badgeid, password, pubsname)
SELECT  id, 'invalid', badge_name
FROM $regdbname.perinfo
WHERE id IN ($idstr);
EOD;
     mysqli_query_with_error_handling($sql);
     $rows = mysqli_affected_rows($linki);
     if (is_null($rows) || $rows !== $usercnt) {
         mysqli_query_with_error_handling("ROLLBACK;");
         RenderErrorAjax("Error: creating participants from reg import");
         exit();
     }
     // and add the permissions
     // commit before perms due to foreign key constraint lock on insert on some Mysql implimentations
     mysqli_query_with_error_handling("COMMIT;");

    $sql = <<<EOD
INSERT INTO UserHasPermissionRole (badgeid, permroleid)
SELECT  id, ?
FROM $regdbname.perinfo
WHERE id IN ($idstr);
EOD;
    $paramarray = array();
    foreach ($rolesToAddArr as $id) {
        $paramarray[0] = $id;
        $rows = mysql_cmd_with_prepare($sql, 'i', $paramarray);
        if (is_null($rows) || $rows !== $usercnt) {
            // mysqli_query_with_error_handling("ROLLBACK;");
            RenderErrorAjax("Error: adding permission roles from reg import");
            exit();
        }
    }
    // all done commit the sequence - move commit up, due to locking error on some MYSQL implimentations
    // mysqli_query_with_error_handling("COMMIT;");
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
    global $linki, $message_error;

    $regdbname = REG_DBNAME;
    $searchString = getString("searchString");
    if ($searchString == "")
        exit();
    if (is_numeric($searchString)) {
        $searchString =  mysqli_real_escape_string($linki, $searchString);
        $query["searchReg"] = <<<EOD
SELECT
	id, last_name, first_name, email_addr, badge_name, city, state, zip
FROM
    $regdbname.perinfo P
	LEFT OUTER JOIN CongoDump CD ON P.id = CD.badgeid
WHERE
	P.id = "$searchString" AND CD.badgeid IS NULL AND P.active = 'Y' and P.banned = 'N'
ORDER BY
	P.last_name, P.first_name
EOD;
        $xml = mysql_query_XML($query);
    } else {
        $searchString = '%' . $searchString . '%';
        $query = <<<EOD
SELECT
	id, last_name, first_name, email_addr, badge_name, city, state, zip
FROM
    $regdbname.perinfo P
	LEFT OUTER JOIN CongoDump CD ON P.id = CD.badgeid
WHERE
		(P.badge_name LIKE ?
	OR P.last_name LIKE ?
	OR P.first_name LIKE ?
	OR P.badge_name LIKE ?)
    AND CD.badgeid IS NULL AND P.active = 'Y' and P.banned = 'N'
ORDER BY
	P.last_name, P.first_name
EOD;
    $param_arr = array($searchString,$searchString,$searchString,$searchString);
    $result = mysqli_query_with_prepare_and_exit_on_error($query, "ssss", $param_arr);
    $xml = mysql_result_to_XML("searchReg", $result);
    }

    if (!$xml) {
        echo $message_error;
        exit();
    }
    header("Content-Type: text/html");
    $paramArray = array("userIdPrompt" => USER_ID_PROMPT);
    //echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $xml->saveXML(), "i")); //for debugging only
    RenderXSLT('BalticonImportRegUser.xsl', $paramArray, $xml);
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

function convert_bio() {
    $htmlbio = getString("htmlbio");
    $bio = html_to_text($htmlbio);
    $results = [];
    $results["bio"] = $bio;
    $results["len"] = mb_strlen($bio);
    echo json_encode($results);
}

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;

if (!isLoggedIn() || !may_I('reg_ImportUsers')) {
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
