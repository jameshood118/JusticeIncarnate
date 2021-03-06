<?php
/******************************
 * WoWRoster.net  Roster
 * Copyright 2002-2007
 * Licensed under the Creative Commons
 * "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * Short summary
 *  http://creativecommons.org/licenses/by-nc-sa/2.5/
 *
 * Full license information
 *  http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
 * -----------------------------
 *
 * $Id: settings.php 603 2007-02-14 07:37:56Z zanix $
 *
 ******************************/

if( eregi(basename(__FILE__),$_SERVER['PHP_SELF']) )
{
	die("You can't access this file directly!");
}

/**
 * Set PHP error reporting
 */
error_reporting(E_ALL ^ E_NOTICE);



// Be paranoid with passed vars
// Destroy GET/POST/Cookie variables from the global scope
if (intval(ini_get('register_globals')) != 0)
{
	foreach ($_REQUEST AS $key => $val)
	{
		if (isset($$key))
			unset($$key);
	}
}


/**
 * Begin Roster Timing
 */
$starttime = explode(' ', microtime() );
define('ROSTER_STARTTIME',$starttime[1] + $starttime[0]);



/**
 * OS specific Directory Seperator
 */
define('DIR_SEP',DIRECTORY_SEPARATOR);

/**
 * Get the url
 */
$url = explode('/','http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);
array_pop($url);

/**
 * URL to roster's root directory
 */
define('ROSTER_URL',implode('/',$url));


/**
 * Base, absolute roster directory
 */
define('ROSTER_BASE',dirname(__FILE__).DIR_SEP);


/**
 * Base, absolute roster library directory
 */
define('ROSTER_LIB',ROSTER_BASE.'lib'.DIR_SEP);


/**
 * Base, absolute roster admin directory
 */
define('ROSTER_ADMIN',ROSTER_BASE.'admin'.DIR_SEP);


/**
 * Base, absolute roster addons directory
 */
define('ROSTER_ADDONS',ROSTER_BASE.'addons'.DIR_SEP);


/**
 * Full path to roster config file
 */
define('ROSTER_CONF_FILE',ROSTER_BASE.'conf.php');


/**
 * If conf.php is not found, then die to installer link
 */
if ( !file_exists(ROSTER_CONF_FILE) )
{
    exit("<center>Roster is not installed<br />\n<a href=\"install.php\">INSTALL</a></center>");
}
else
{
	require_once (ROSTER_CONF_FILE);
}


/**
 * If ROSTER_INSTALLED is not defined, then die to installer link
 */
if ( !defined('ROSTER_INSTALLED') )
{
    exit("<center>Roster is not installed<br />\n<a href=\"install.php\">INSTALL</a></center>");
}

/**
 * Include roster db file
 */
require_once (ROSTER_LIB.'wowdb.php');



/**
 * Establish our connection and select our database
 */
$roster_dblink = $wowdb->connect($db_host, $db_user, $db_passwd, $db_name);
if( !$roster_dblink )
{
	die(basename(__FILE__).': line['.(__LINE__).']<br />'.'Could not connect to database "'.$db_name.'"<br />MySQL said:<br />'.$wowdb->error());
}


/**
 * NULL DB Connect Info for Safety
 */
$db_user = null;
$db_passwd = null;


/**
 * Include constants file
 */
require_once (ROSTER_LIB.'constants.php');



/**
 * Include common functions
 **/
require_once (ROSTER_LIB.'commonfunctions.lib.php');


/**
 * Slash global data if magic_quotes_gpc is off.
 */
if ( !get_magic_quotes_gpc() )
{
	$_GET = escape_array($_GET);
	$_POST = escape_array($_POST);
	$_COOKIE = escape_array($_COOKIE);
	$_REQUEST = escape_array($_REQUEST);
}


/**
 * Get the current config values
 */
$sql = "SELECT `config_name`, `config_value` FROM `".ROSTER_CONFIGTABLE."` ORDER BY `id` ASC;";
$results = $wowdb->query($sql);

if( !$results || $wowdb->num_rows($results) == 0 )
{
	die("Cannot get roster configuration from database<br />\nMySQL Said: ".$wowdb->error()."<br /><br />\nYou might not have roster installed<br />\n<a href=\"install.php\">INSTALL</a>");
}

/**
 * Fill the config array with values
 */
while( $row = $wowdb->fetch_assoc($results) )
{
	$roster_conf[$row['config_name']] = stripslashes($row['config_value']);
}
$wowdb->free_result($results);


/**
 * Set SQL debug value
 */
$wowdb->setSQLDebug($roster_conf['sqldebug']);



/**
 * Include locale files
 */
include(ROSTER_BASE.'localization'.DIR_SEP.'languages.php');


/**
 * If the version doesnt match the one in constants, redirect to upgrader
 */
if( empty($roster_conf['version']) || $roster_conf['version'] < ROSTER_VERSION )
{
	message_die('Looks like you\'ve loaded a new version of Roster<br />
<br />
Your Version: <span class="red">'.$roster_conf['version'].'</span><br />
New Version: <span class="green">'.ROSTER_VERSION.'</span><br />
<br />
<a href="upgrade.php" style="border:1px outset white;padding:2px 6px 2px 6px;">UPGRADE</a>','Upgrade Roster','sred');
}


/**
 * If the install directory or files exist, die()
 */
if( file_exists(ROSTER_BASE.'install.php') ||  file_exists(ROSTER_BASE.'install') || file_exists(ROSTER_BASE.'upgrade.php') )
{
	if( !file_exists(ROSTER_BASE.'version_match.php') )
	{
		message_die('Please remove the files <span class="green">install.php</span>, <span class="green">upgrade.php</span> and the folder <span class="green">/install/</span> in this directory','Remove Install Files','sred');
	}
}



/**
 * Include roster Login class
 */
require_once(ROSTER_LIB.'login.php');


/**
 * Get guild data from dataabse
 */
$guild_info = $wowdb->get_guild_info($roster_conf['server_name'],$roster_conf['guild_name']);
