<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Short URL link for characters
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: c.php 1256 2007-08-19 16:47:29Z Zanix $
 * @link       http://www.wowroster.net
 * @since      File available since Release 1.7.2
*/

define('IN_ROSTER',true);

// Set the relative URL here. Use slashes, not backslashes. Use slash in front
// and omit the one at the end. If your windows server does not accept slashes
// in pathnames set the include statement seperately.
$roster_rel = '';

$_GET[ROSTER_PAGE] = 'dummy';

// Environment
include('.'.$roster_rel.'/settings.php');

// Get the char from the query string. To keep the link as short as
// possible, we don't use member= or anything like that
$char = $roster->db->escape(urldecode($_SERVER['QUERY_STRING']));

if( is_numeric($char) )
{
	$where = ' `member_id` = "' . $char . '"';
}
elseif( strpos($char, '@') !== false )
{
	list($name, $realm) = explode('@',$char);
	if( strpos($realm,'-') !== false )
	{
		list($region, $realm) = explode('-',$realm);
		$where = ' `name` = "' . $name . '" AND `server` = "' . $realm . '" AND `region` = "' . strtoupper($region) . '"';
	}
	else
	{
		$where = ' `name` = "' . $name . '" AND `server` = "' . $realm . '"';
	}
}
else
{
	$name = $char;
	$where = ' `name` = "' . $name . '"';
}


// Check if there's a character with this name
$query = "SELECT `member_id` FROM `".$roster->db->table('members')."` WHERE $where;";

$result = $roster->db->query($query);

if( !$result )
{
	die_quietly($roster->db->error(),'Roster Autopointer',__FILE__,__LINE__,$query);
}

if( $row = $roster->db->fetch($result) )
{
	$roster->db->free_result($result);
	header("Location: ".str_replace('&amp;','&',makelink('char-info&amp;member='.$row['member_id'],true)));
	exit();
}


// There's no char with that name? Redirect to guild page.

header('Location: '.ROSTER_URL.$roster_rel);

