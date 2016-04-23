<?php
/******************************
 * Outland Key listing
 * Addon for Wowroster.com
 * Based on memberinst.php by zanix
 *
 ******************************
 *
 * $Id: OutlandKeys/index.php 19 2007-03-04 00:08:26Z SartriX $
 *
 ******************************/

$versions['versionDate']['outlandkeys'] = '$Date: 2007/03/04 $';
$versions['versionRev']['outlandkeys'] = '$Revision: 1.0.0 $';
$versions['versionAuthor']['outlandkeys'] = '$Author: SartriX, zanix $';

$config['menu_name'] = 'Outland Keys';    //<- This is just a general name and can be called anything, as it is used in the array, I generally use the name of the addon.
$config['menu_min_user_level'] = 0;     //<- Do not change, its for a future use :)
$config['menu_index_file'] = array();   //<- Do not change, EVER
$config['menu_index_file'][0][0] = '';
$config['menu_index_file'][0][1] = 'Outland Keys';
?>