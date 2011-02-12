<?php
    function posting_header_old($title) {
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html xmlns="http://www.w3.org/TR/xhtml1/transitional">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=latin-1">
  <title><?php echo CON_NAME ."-- $title"; ?></title>
  <link rel="stylesheet" href="Common.css" type="text/css">
</head>
<body>
<H1 class="head">The information for <?php echo CON_NAME; ?></H1>
<hr>

<H2 class="head"><?php echo $title ?></H2>
<?php } ?>
