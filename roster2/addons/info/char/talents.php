<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Displays character information
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: talents.php 1241 2007-08-16 06:06:25Z Zanix $
 * @link       http://www.wowroster.net
 * @package    CharacterInfo
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

include( $addon['dir'] . 'inc/header.php' );

if( $addon['config']['show_talents'] == 1 )
{
	$char_page .= $char->printTalents();
}

include( $addon['dir'] . 'inc/footer.php' );
