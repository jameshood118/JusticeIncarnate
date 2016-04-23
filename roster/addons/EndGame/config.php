<?php
$versions['versionDate']['endgamelang'] = '$Date: 2007/04/25 $';
$versions['versionRev']['endgamelang'] = '$Revision: 1.2 $';
$versions['versionAuthor']['endgamelang'] = '$Author: Dave, Zxaltan $';

if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}


// CONFIGURATION
$conf_table = 'EndGame_Conf';  //!! Hard-coded name of config table !!

// LOAD VALUES FROM DATABASE
$query = 'SELECT * FROM '.$conf_table;
$result = $wowdb->query($query) or die("Database error: ".$wowdb->error()." while running query $query");
while ($row = $wowdb->fetch_assoc($result)) $config[$row['name']] = $row['value'];

// CLEAN UP
$wowdb->free_result($result);
unset($row, $result);

?>