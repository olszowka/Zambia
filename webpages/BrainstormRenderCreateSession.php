<?php
// Copyright (c) 2011-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
    // This function will output the page with the form to add or create a session
    // Variables
    //     action: "brainstorm"
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function BrainstormRenderCreateSession ($session, $message1, $message2) {
    global $name, $email, $title;
    require_once("BrainstormCommonCode.php");
    $_SESSION['return_to_page']='BrainstormRenderCreateSession.php';
    $title="Brainstorm New Session";
    brainstorm_header($title);
    
    // still inside function RenderAddCreateSession
    if (strlen($message1)>0) {
      echo "<p id=\"message1\" style='color:red'>".$message1."</p>\n";
    }
    if (strlen($message2)>0) {
      echo "<p id=\"message2\" style='color:red'>".$message2."</p>\n";
      exit(); // If there is a message2, then there is a fatal error.
    }
    //error_log("Zambia: ".print_r($session,TRUE));
  ?>

<div class="formbox">
    <form name="sessform" class="bb" method=POST action="SubmitEditCreateSession.php">
        <input type="hidden" name="type" value="<?php echo $session["type"]; ?>" />
        <input type="hidden" name="divisionid" value="<?php echo $session["divisionid"]; ?>" />
        <input type="hidden" name="roomset" value="<?php echo $session["roomset"]; ?>" />
        <input type="hidden" name="languagestatusid" value="<?php echo $session["languagestatusid"]; ?>" />
        <input type="hidden" name="pubstatusid" value="<?php echo $session["pubstatusid"]; ?>" />
        <input type="hidden" name="pubno" value="<?php echo htmlspecialchars($session["pubno"],ENT_COMPAT);?>" />
        <input type="hidden" name="duration" value="<?php echo htmlspecialchars($session["duration"],ENT_COMPAT);?>" />
        <input type="hidden" name="atten" value="<?php echo htmlspecialchars($session["atten"],ENT_COMPAT);?>" />
        <input type="hidden" name="kids" value="<?php echo $session["kids"];?>" />
        <input type="hidden" name="status" value="<?php echo $session["status"];?>" />
        <input type="hidden" name="action" value="brainstorm" />
        <input type="reset" value="Reset">&nbsp;
        <input type=submit id="sButtonTop" value="Save">
        <p>Note: items in red must be completed before you can save.</p>
        <table>
            <tr>
                <td class="form1">
                   <label for="name" id="nameLabel">Your name:</label><br />
                   <input type="text" id="name" name="name" onkeypress="return checkSubmitButton();"
                   <?php if ($name!="")
                            echo "value=\"$name\" "; ?>
                       ></td></tr>
            <tr>
                <td class="form1">&nbsp;<br />
                   <label for="email" id="emailLabel">Your email address:</label><br />
                   <input type="text" id="email" name="email" size="50" onKeyPress="return checkSubmitButton();"
                   <?php if ($email!="")
                            echo "value=\"$email\" "; ?>
                       >
                </td>
            </tr> 
            <tr>
                <td class="form1">&nbsp;<br />
                    <label for="track" id="trackLabel">Track:</label><br />
                    <select id="track" name="track" onChange="return checkSubmitButton();">
                        <?php populate_select_from_table("Tracks", $session["track"], "SELECT", FALSE); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form1">
                    &nbsp;<br />
                    <label for="title" ID="title">Title: </label>
                    <br />
                    <?php echo "<input type=text size=\"50\" name=\"title\" value=\"";
                        echo htmlspecialchars($session["title"],ENT_COMPAT)."\" onKeyPress=\"return checkSubmitButton();\">"; ?>
                </td>
             </tr>
            <tr>
                <td class="form1">
                    &nbsp;<br />
                    <label for="progguiddesc" id="progguiddescLabel">Description:</label><br />
                    <textarea id="progguiddesc" cols="70" rows="5" name="progguiddesc" onKeyPress="return checkSubmitButton();"><?php echo htmlspecialchars($session["progguiddesc"],ENT_NOQUOTES); ?></textarea>
                </td>
             </tr>
            <tr>
                <td class="form1">
                    &nbsp;<br />
                    <label for="notesforprog" id="notesforproglabel">Additional info for Programming Committee:</label><br />
                    <textarea id="notesforprog" cols="70" rows="7" name="notesforprog" ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES); ?></textarea>
                </td>
             </tr>
         </table>
        <input type=reset value="Reset">&nbsp;
        <input type=submit ID="sButtonBottom" value="Save" />
    </form>
</div>
<?php brainstorm_footer(); } ?>
