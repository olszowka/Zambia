<?php
require_once('StaffCommonCode.php');

/* Global Variables */
global $link;
$conid=$_SESSION['conid'];
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// LOCALIZATIONS
$_SESSION['return_to_page']="LabelsPrint.php";
$title="Labels Print";
$description="<P>Labels for Printing.</P>\n";

// Postscript Header
$header=<<<EOD
%!PS-Adobe-3.0

/deffont {
  findfont exch scalefont def
} bind def

/reencode_font {
  findfont reencode 2 copy definefont pop def
} bind def

% reencode the font
% <encoding-vector> <fontdict> -> <newfontdict>
/reencode { %def
  dup length 5 add dict begin
    { %forall
      1 index /FID ne
      { def }{ pop pop } ifelse
    } forall
    /Encoding exch def

    % Use the font's bounding box to determine the ascent, descent,
    % and overall height; don't forget that these values have to be
    % transformed using the font's matrix.
    % We use 'load' because sometimes BBox is executable, sometimes not.
    % Since we need 4 numbers an not an array avoid BBox from being executed
    /FontBBox load aload pop
    FontMatrix transform /Ascent exch def pop
    FontMatrix transform /Descent exch def pop
    /FontHeight Ascent Descent sub def

    % Define these in case they're not in the FontInfo (also, here
    % they're easier to get to.
    /UnderlinePosition 1 def
    /UnderlineThickness 1 def

    % Get the underline position and thickness if they're defined.
    currentdict /FontInfo known {
      FontInfo

      dup /UnderlinePosition known {
        dup /UnderlinePosition get
        0 exch FontMatrix transform exch pop
        /UnderlinePosition exch def
      } if

      dup /UnderlineThickness known {
        /UnderlineThickness get
        0 exch FontMatrix transform exch pop
        /UnderlineThickness exch def
      } if

    } if
    currentdict
  end
} bind def

/ISO-8859-1Encoding [
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/space /exclam /quotedbl /numbersign /dollar /percent /ampersand /quoteright
/parenleft /parenright /asterisk /plus /comma /minus /period /slash
/zero /one /two /three /four /five /six /seven
/eight /nine /colon /semicolon /less /equal /greater /question
/at /A /B /C /D /E /F /G
/H /I /J /K /L /M /N /O
/P /Q /R /S /T /U /V /W
/X /Y /Z /bracketleft /backslash /bracketright /asciicircum /underscore
/quoteleft /a /b /c /d /e /f /g
/h /i /j /k /l /m /n /o
/p /q /r /s /t /u /v /w
/x /y /z /braceleft /bar /braceright /asciitilde /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/space /exclamdown /cent /sterling /currency /yen /brokenbar /section
/dieresis /copyright /ordfeminine /guillemotleft /logicalnot /hyphen /registered /macron
/degree /plusminus /twosuperior /threesuperior /acute /mu /paragraph /bullet
/cedilla /onesuperior /ordmasculine /guillemotright /onequarter /onehalf /threequarters /questiondown
/Agrave /Aacute /Acircumflex /Atilde /Adieresis /Aring /AE /Ccedilla
/Egrave /Eacute /Ecircumflex /Edieresis /Igrave /Iacute /Icircumflex /Idieresis
/Eth /Ntilde /Ograve /Oacute /Ocircumflex /Otilde /Odieresis /multiply
/Oslash /Ugrave /Uacute /Ucircumflex /Udieresis /Yacute /Thorn /germandbls
/agrave /aacute /acircumflex /atilde /adieresis /aring /ae /ccedilla
/egrave /eacute /ecircumflex /edieresis /igrave /iacute /icircumflex /idieresis
/eth /ntilde /ograve /oacute /ocircumflex /otilde /odieresis /divide
/oslash /ugrave /uacute /ucircumflex /udieresis /yacute /thorn /ydieresis
] def
/ISOTimes-Roman ISO-8859-1Encoding /Times-Roman reencode_font
/labelclip {
	newpath
	1.000000 1.000000 moveto
	179.000000 1.000000 lineto
	179.000000 71.000000 lineto
	1.000000 71.000000 lineto
	closepath
	clip

} def

% end prologue

% set font type and size
ISOTimes-Roman 12 scalefont setfont

EOD;

$startpage=<<<EOD
%%Page: labels

%%BeginPageSetup
11.250000 16.000000 translate
%%EndPageSetup

EOD;

//Set up where things are going to be written.
$positional_array[1]['row']=0;
$positional_array[2]['row']=72;
$positional_array[3]['row']=144;
$positional_array[4]['row']=216;
$positional_array[5]['row']=288;
$positional_array[6]['row']=360;
$positional_array[7]['row']=432;
$positional_array[8]['row']=504;
$positional_array[9]['row']=576;
$positional_array[10]['row']=648;
$positional_array[1]['col']=0;
$positional_array[2]['col']=200;
$positional_array[3]['col']=400;

/* This query returns the pubsname (and probably should return the
 permrolename) for the participants who are either Presenters, General
 Volunteers, or Programming Volunteers.  The individual switch lets us
 work from the premise of printing just one person's information. */
$query = <<<EOD
SELECT 
    P.pubsname,
    GROUP_CONCAT(DISTINCT permrolename SEPARATOR ", ") AS permroles
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN ParticipantOnSession POS USING (sessionid)
    JOIN $ReportDB.Participants P USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('Participant', 'Programming') AND
    UHPR.conid=$conid
  GROUP BY
    P.badgeid
  ORDER BY
    P.pubsname

EOD;

// Retrieve query
list($rows,$header_array,$participant_array)=queryreport($query,$link,$title,$description,0);

/* Printing body. */
header('Content-type: application/postscript');

echo $header;
$k=1;
while ($k <= $rows) {
  echo $startpage;
    for ($i=1; $i<=3; $i++) {
      for ($j=1; $j<=10; $j++) {
	echo "gsave\n";
	echo $positional_array[$i]['col'];
	echo " ";
	echo $positional_array[$j]['row'];
	echo "\ntranslate\nlabelclip\nnewpath\nISOTimes-Roman 12 scalefont setfont\n3.000000 36.000000 moveto\n( ";
	echo $participant_array[$k]['permroles'];
	echo ") show\nISOTimes-Roman 17 scalefont setfont\n3.000000 50.000000 moveto\n ( ";
	echo $participant_array[$k++]['pubsname'];
	echo ") show\nstroke\ngrestore\n\n";
      }
    }
  echo "showpage\n\n";
 }

?>