<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Roster ajax functions list
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: functions.php 1170 2007-08-11 05:13:21Z Zanix $
 * @link       http://www.wowroster.net
 * @since      File available since Release 1.8.0
 * @package    WoWRoster
 * @subpackage Ajax
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

$ajaxfuncs['menu_button_add'] = array(
	'file'=>ROSTER_AJAX . 'menu.php',
);
$ajaxfuncs['menu_button_del'] = array(
	'file'=>ROSTER_AJAX . 'menu.php',
);
