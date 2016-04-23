<?php
$versions['versionDate']['endgamelang'] = '$Date: 2006/08/20 $'; 
$versions['versionRev']['endgamelang'] = '$Revision: 1.0 $'; 
$versions['versionAuthor']['endgamelang'] = '$Author: Zxaltan $'; 

if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}

$config['menu_name'] = 'End Game';    //<- This is just a general name and can be called anything, as it is used in the array, I generally use the name of the addon.

$config['menu_min_user_level'] = 0;     //<- Do not change, its for a future use :)

$config['menu_index_file'] = array();   //<- Do not change, EVER

$config['menu_index_file'][0][0] = '&amp;EndGame&amp;';  // request query variables delimited by &amp;
$config['menu_index_file'][0][1] = $wordings['EndGame'];         // menu link text
?>