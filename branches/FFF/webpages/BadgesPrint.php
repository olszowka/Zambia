<?php
require_once('StaffCommonCode.php');
/* include "../phpqrcode-master/qrlib.php"; */

/* Global Variables */
global $link;
$conid=$_SESSION['conid'];
$ConName=CON_NAME; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$ConLogo="NELA-LOGO.eps";
/* $ConLogo=QRcode::eps("https://nelaonline.org/FFF-NE-40/webpages/VolunteerCheckIn.php?badgeid=123"); */
$BoundingBox="0 0 759 222";

// LOCALIZATIONS
$_SESSION['return_to_page']="BadgesPrint.php";
$title="Badge Print";
$description="<P>Badges for Printing.</P>\n";

// Postscript Header
$header=<<<EOD
%!PS-Adobe-3.0

/insertlogo ($ConLogo) def
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
/ISOArial ISO-8859-1Encoding /Arial reencode_font
/ISOArial-Bold ISO-8859-1Encoding /Arial-Bold reencode_font
/labelclip {
	newpath
	1.000000 1.000000 moveto
	251.000000 1.000000 lineto
	251.000000 159.000000 lineto
	1.000000 159.000000 lineto
	closepath
	clip

} def

/BeginEPSF { % def
  /b4_Inc_state save def		%Save state for cleanup
  /dict_count countdictstack def	%Count dict objects on dict stack
  /op_count count 1 sub def		%Count objects on operand stack
  userdict begin			%Push userdict on dict stack
  /showpage { } def			%Redefine showpage null
  0 setgray 0 setlinecap 1 setlinewidth	%Graphics setup
  0 setlinejoin 10 setmiterlimit [] 0 setdash newpath
  /languagelevel where			%if level != 1 then set strokeadjust
  {pop languagelevel			%and overprint to their defaults
  1 ne
    {false setstrokeadjust false setoverprint
    } if
  } if
} bind def

/EndEPSF { % def
  count op_count sub {pop} repeat	%Clean up stacks
  countdictstack dict_count sub {end} repeat
  b4_Inc_state restore
  picwidth 4 div neg -5 translate                       % Attempt at centering
} bind def
/rect { % llx lly w h			Lower Left X&Y Width and Height inputs
  4 2 roll moveto			% mv llx and lly to top and go there 
  1 index 0 rlineto			% gets and copies width, lineto w,0
  0 exch rlineto			% switches 0 for hight, lineto 0,h
  neg 0 rlineto				% negs width, lineto -w,0
  closepath				% back to llx and lly
} bind def
/picinsert { % llx lly urx ury from BoundingBox
  /bi_ury exch def
  /bi_urx exch def
  /bi_lly exch def
  /bi_llx exch def
  /bi_width bi_urx bi_llx sub def
  /bi_height bi_ury bi_lly sub def
  /picwidth 246 def
  /picheight 20 def
  /scale_width picwidth bi_width div def
  /scale_height picheight bi_height div def
  picwidth 4 div 5 translate                       % Attempt at centering
  BeginEPSF
  scale_height scale_height scale                        %figured from BoundingBox
  bi_llx neg bi_lly neg translate       %-llx -lly to lower corner justify
  bi_llx bi_lly 
  picwidth scale_width div 
  picheight scale_height div rect               %playspace
  clip newpath
} bind def

% end prologue

% set font type and size
ISOArial 16 scalefont setfont

EOD;

$startpage=<<<EOD
%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup

EOD;

//Set up where things are going to be written.
$positional_array[1]['row']=0;
$positional_array[2]['row']=160;
$positional_array[3]['row']=320;
$positional_array[4]['row']=480;
$positional_array[1]['col']=0;
$positional_array[2]['col']=252;

/* This query grabs all the schedule elements to be rated, for the selected time period. */
$query=<<<EOD
SELECT 
  DISTINCT badgeid,
  pubsname,
  if ((permrolename='Participant'),concat("Presenter"),concat("Volunteer")) AS Role
  FROM
      Sessions
    JOIN Schedule USING (sessionid)
    JOIN ParticipantOnSession USING (sessionid)
    JOIN $ReportDB.Participants USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
  WHERE
    permrolename IN ('Participant','General','Programming') AND
    UHPR.conid=$conid
  ORDER BY
    pubsname,
    permroleid
EOD;

// Retrive query
list($rows,$header_array,$participant_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  */
header('Content-type: application/postscript');

echo $header;
$k=1;
while ($k <= $rows) {
  echo $startpage;
    for ($i=1; $i<=2; $i++) {
      for ($j=1; $j<=4; $j++) {
	while ((isset($participant_array[$k]['badgeid'])) AND ($participant_array[$k]['badgeid'] == $participant_array[$k-1]['badgeid'])) {$k++;}
	echo "gsave\n";
	echo $positional_array[$i]['col'];
	echo " ";
	echo $positional_array[$j]['row'];
	echo "\ntranslate\n";
	echo "3 28 translate\n".$BoundingBox." picinsert\ngsave\ninsertlogo run\ngrestore\n%%Trailer\nEndEPSF\n-3 -28 translate\n";    
        echo "labelclip\nnewpath\nISOArial 16 scalefont setfont\n3.000000 75.000000 moveto\n( ";
	echo $participant_array[$k]['Role'];
	echo ") show\n";
	echo "ISOArial 16 scalefont setfont\n3.000000 55.000000 moveto\n( ";
	echo $ConName;
	echo ") show\n";
	echo "ISOArial-Bold 24 scalefont setfont\n3.000000 95.000000 moveto\n( ";
	echo $participant_array[$k++]['pubsname'];
	echo ") show\nstroke\ngrestore\n\n";
      }
    }
  echo "showpage\n\n";
 }

?>