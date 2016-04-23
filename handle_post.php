<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>Your Forum Posting</title>
</head>
<body>
<form method="post" action="mailto:jameshood118@comcast.net" name="Application">
<?php // Script 5.2 - handle_post.php

// Address error handing.
ini_set ('display_errors', 1);
error_reporting (E_ALL & ~E_NOTICE);

// In case register_globals is disabled.
$character_name = $_POST['first_name'];
$posting = $_POST['posting'];

// Create a name variable.
$name = $first_name . ' ' . $last_name;

// Print the message.
print "Thank you, $name, for your commenting:<br />
<p>$posting</p>";

?>
<input type="submit" value="submit">
</form>
</body>
</html>
