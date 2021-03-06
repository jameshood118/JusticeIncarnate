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
 * $Id: upgrade.php 603 2007-02-14 07:37:56Z zanix $
 *
 ******************************/

/**
* NOTICE: This file was not written by the WoWRoster Dev Team
* It was originally written for the EQdkp project and used here
*
* It has been modified and adapted to fit WoWRoster's needs.
*
* The EQdkp Group's original notice appears below
*/

/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * upgrade.php
 * Began: Tue July 1 2003
 *
 * $Id: upgrade.php 603 2007-02-14 07:37:56Z zanix $
 *
 ******************************/
error_reporting(E_ALL);

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

set_magic_quotes_runtime(0);
if ( !get_magic_quotes_gpc() )
{
	$_GET = slash_global_data($_GET);
	$_POST = slash_global_data($_POST);
}

// ---------------------------------------------------------
// Config file Downloader
// ---------------------------------------------------------
if( isset($_POST['send_file']) && $_POST['send_file'] == 1 && !empty($_POST['config_data']) )
{
	header('Content-Type: text/x-delimtext; name="conf.php"');
	header('Content-disposition: attachment; filename="conf.php"');

	// We need to stripslashes no matter what the setting of magic_quotes_gpc is
	// because we add slashes at the top if its off, and they are added automaticlly
	// if it is on.
	echo stripslashes($_POST['config_data']);

	exit;
}

define('DIR_SEP',DIRECTORY_SEPARATOR);
$roster_root_path = dirname(__FILE__).DIR_SEP;


include_once($roster_root_path.'conf.php');
include_once($roster_root_path.'lib'.DIR_SEP.'wowdb.php');

$DEFAULTS = array(
	'version'        => '1.7.3',
);


// ---------------------------------------------------------
// Template Wrap class
// ---------------------------------------------------------
if ( !include_once($roster_root_path . 'install'.DIR_SEP.'template.php') )
{
	die('Could not include ' . $roster_root_path . 'install'.DIR_SEP.'template.php - check to make sure that the file exists!');
}


/**
 * Establish our connection and select our database
 */
$roster_dblink = $wowdb->connect($db_host, $db_user, $db_passwd, $db_name);
if( !$roster_dblink )
{
	$tpl = new Template_Wrap('upgrade_message.html','upgrade_header.html','upgrade_tail.html');
	$tpl->message_die('Could not connect to database "'.$db_name.'"<br />MySQL said:<br />'.$wowdb->error(), 'Database Error');
	exit();
}

define('CONFIG_TABLE', $db_prefix . 'config');


/**
 * Get the current version value
 */

// Detect $version variable from Roster 1.6.0
if( !isset($version) )
{
	if( !defined('ROSTER_CONFIGTABLE') )
		define('ROSTER_CONFIGTABLE', $db_prefix . 'config');

	$sql = "SELECT `config_value` FROM `".ROSTER_CONFIGTABLE."` WHERE `config_name` = 'version';";
	$results = $wowdb->query($sql);

	if( !$results )
	{
		$tpl = new Template_Wrap('upgrade_message.html','upgrade_header.html','upgrade_tail.html');
		$tpl->message_die('Cannot get roster configuration from database "'.$db_name.'"<br />MySQL said:<br />'.$wowdb->error(), 'Database Error');
		exit();
	}
	else
	{
		$row = $wowdb->fetch_assoc($results);
		$version = $row['config_value'];
	}
}
define('ROSTER_OLDVERSION',$version);



if( ROSTER_OLDVERSION >= $DEFAULTS['version'] )
{
	$tpl = new Template_Wrap('upgrade_message.html','upgrade_header.html','upgrade_tail.html');
	$tpl->message_die('You have already upgraded Roster<br />Or you have a newer version than this upgrader', 'Upgrade Error');
	exit();
}





class Upgrade
{
	var $versions = array('1.6.0','1.7.0','1.7.1','1.7.2');
	var $messages;


	function upgrade()
	{
		if ( isset($_POST['upgrade']) )
		{
			// Find out what version we're upgrading from
			$version_from = $_POST['version'];
			foreach ( $this->versions as $index => $version )
			{
				if ( str_replace('.', '', $version) == $version_from )
				{
					$method = 'upgrade_' . $version_from;
					$this->$method($index);
				}
			}
		}
		else
		{
			$this->display_form();
		}
	}

	function finalize($index)
	{
		if ( isset($this->versions[$index + 1]) )
		{
			$method = 'upgrade_' . str_replace('.', '', $this->versions[$index + 1]);
			$this->$method($index + 1);
		}
		else
		{
			$tpl = new Template_Wrap('upgrade_message.html','upgrade_header.html','upgrade_tail.html');

			if( !empty($this->messages) )
			{
				$tpl->message_append($this->messages);
			}

			$tpl->message_append('Your Roster installation has been successfully upgraded.<br /><br /><b class="negative">For extra security, remove this file!</b>', 'Success');
			$tpl->page_header();
			$tpl->page_tail();
		}
	}

	//--------------------------------------------------------------
	// Upgrade methods
	//--------------------------------------------------------------

	function upgrade_172($index)
	{
		$this->standard_upgrader('172');
		$this->finalize($index);
	}

	function upgrade_171($index)
	{
		$this->standard_upgrader('171');
		$this->finalize($index);
	}

	function upgrade_170($index)
	{
		$this->standard_upgrader('170');
		$this->finalize($index);
	}


	/**
	 * The upgrader for Roster 1.6.0
	 * This has to do alot, whew
	 *
	 * @param unknown_type $index
	 */
	function upgrade_160($index)
	{
		global $wowdb, $roster_root_path,
			$db_host, $db_name, $db_user, $db_passwd, $db_prefix,
			$roster_lang, $roster_upd_pw, $guild_name, $server_name;

		//
		// Lets get some roster 160 db values before we upgrade the db
		//
		$query_string = "SELECT `member_id`,`stat_int`,`stat_agl`,`stat_sta`,`stat_str`,`stat_spr`,`armor`,`defense`,".
			"`res_frost`,`res_arcane`,`res_fire`,`res_shadow`,`res_nature`".
			" FROM `".ROSTER_PLAYERSTABLE."`";
		$result = $wowdb->query($query_string);

		//
		// Now loop through this so we can get each players stats to upgrade
		//
		$playerData = array();
		while( $player_row = $wowdb->fetch_assoc($result) )
		{
			$playerData[] = $player_row;
		}


		$db_structure_file = $roster_root_path . 'install'.DIR_SEP.'db'.DIR_SEP.'upgrade_160.sql';
		$db_data_file      = $roster_root_path . 'install'.DIR_SEP.'db'.DIR_SEP.'mysql_data.sql';


		// Parse structure file and create database tables
		$sql = @fread(@fopen($db_structure_file, 'r'), @filesize($db_structure_file));
		$sql = preg_replace('#renprefix\_(\S+?)([\s\.,]|$)#', $db_prefix . '\\1\\2', $sql);

		$sql = remove_remarks($sql);
		$sql = parse_sql($sql, ';');

		$sql_count = count($sql);
		for ( $i = 0; $i < $sql_count; $i++ )
		{
			$wowdb->query($sql[$i]);
		}
		unset($sql);


		// Parse the data file and populate the database tables
		$sql = @fread(@fopen($db_data_file, 'r'), @filesize($db_data_file));
		$sql = preg_replace('#renprefix\_(\S+?)([\s\.,]|$)#', $db_prefix . '\\1\\2', $sql);

		$sql = remove_remarks($sql);
		$sql = parse_sql($sql, ';');

		$sql_count = count($sql);
		for ( $i = 0; $i < $sql_count; $i++ )
		{
			$wowdb->query($sql[$i]);
		}
		unset($sql);

	    //
	    // Determine server settings
	    //
	    if (!empty($_SERVER['SERVER_NAME']) || !empty($_ENV['SERVER_NAME']))
		{
			$website_address = 'http://'.((!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : $_ENV['SERVER_NAME']);
		}
		else if (!empty($_SERVER['HTTP_HOST']) || !empty($_ENV['HTTP_HOST']))
		{
			$website_address = 'http://'.((!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_ENV['HTTP_HOST']);
		}
		else
		{
			$website_address = '';
		}
		$server_path = str_replace('/upgrade.php', '', $_SERVER['PHP_SELF']);

		//
		// Update some config settings
		//
		$wowdb->query("UPDATE `" . CONFIG_TABLE . "` SET `config_value`='".$roster_lang."' WHERE `config_name`='roster_lang'");
		$wowdb->query("UPDATE `" . CONFIG_TABLE . "` SET `config_value`='".$server_path."' WHERE `config_name`='roster_dir'");
		$wowdb->query("UPDATE `" . CONFIG_TABLE . "` SET `config_value`='".$website_address."' WHERE `config_name`='website_address'");
		$wowdb->query("UPDATE `" . CONFIG_TABLE . "` SET `config_value`='".md5($roster_upd_pw)."' WHERE `config_name`='roster_upd_pw';");
		$wowdb->query("UPDATE `" . CONFIG_TABLE . "` SET `config_value`='".$guild_name."' WHERE `config_name`='guild_name'");
		$wowdb->query("UPDATE `" . CONFIG_TABLE . "` SET `config_value`='".$server_name."' WHERE `config_name`='server_name';");


		//
		// Now loop through this again and insert it into the db
		//
		$wowdb->reset_values();
		foreach( $playerData as $value )
		{
			if( !empty($value['stat_int']) )
			{
				$stats = explode(':',$value['stat_int']);
				$wowdb->add_value( 'stat_int', $stats[0] );
				$wowdb->add_value( 'stat_int_c', $stats[1] );
				$wowdb->add_value( 'stat_int_b', $stats[2] );
				$wowdb->add_value( 'stat_int_d', $stats[3] );
			}
			if( !empty($value['stat_agl']) )
			{
				$stats = explode(':',$value['stat_agl']);
				$wowdb->add_value( 'stat_agl', $stats[0] );
				$wowdb->add_value( 'stat_agl_c', $stats[1] );
				$wowdb->add_value( 'stat_agl_b', $stats[2] );
				$wowdb->add_value( 'stat_agl_d', $stats[3] );
			}
			if( !empty($value['stat_sta']) )
			{
				$stats = explode(':',$value['stat_sta']);
				$wowdb->add_value( 'stat_sta', $stats[0] );
				$wowdb->add_value( 'stat_sta_c', $stats[1] );
				$wowdb->add_value( 'stat_sta_b', $stats[2] );
				$wowdb->add_value( 'stat_sta_d', $stats[3] );
			}
			if( !empty($value['stat_str']) )
			{
				$stats = explode(':',$value['stat_str']);
				$wowdb->add_value( 'stat_str', $stats[0] );
				$wowdb->add_value( 'stat_str_c', $stats[1] );
				$wowdb->add_value( 'stat_str_b', $stats[2] );
				$wowdb->add_value( 'stat_str_d', $stats[3] );
			}
			if( !empty($value['stat_spr']) )
			{
				$stats = explode(':',$value['stat_spr']);
				$wowdb->add_value( 'stat_spr', $stats[0] );
				$wowdb->add_value( 'stat_spr_c', $stats[1] );
				$wowdb->add_value( 'stat_spr_b', $stats[2] );
				$wowdb->add_value( 'stat_spr_d', $stats[3] );
			}
			if( !empty($value['armor']) )
			{
				$stats = explode(':',$value['armor']);
				$wowdb->add_value( 'stat_armor', $stats[0] );
				$wowdb->add_value( 'stat_armor_c', $stats[1] );
				$wowdb->add_value( 'stat_armor_b', $stats[2] );
				$wowdb->add_value( 'stat_armor_d', $stats[3] );
			}
			if( !empty($value['defense']) )
				$wowdb->add_value( 'stat_def_c', $value['defense'] );

			if( !empty($value['res_frost']) )
				$wowdb->add_value( 'res_frost_c', $value['res_frost'] );
			if( !empty($value['res_arcane']) )
				$wowdb->add_value( 'res_arcane_c', $value['res_arcane'] );
			if( !empty($value['res_fire']) )
				$wowdb->add_value( 'res_fire_c', $value['res_fire'] );
			if( !empty($value['res_shadow']) )
				$wowdb->add_value( 'res_shadow_c', $value['res_shadow'] );
			if( !empty($value['res_nature']) )
				$wowdb->add_value( 'res_nature_c', $value['res_nature'] );

			unset($stats);

			$querystr = "UPDATE `".ROSTER_PLAYERSTABLE."` SET ".$wowdb->assignstr." WHERE `member_id` = '".$value['member_id']."';";
			$result = $wowdb->query($querystr);
		}


		//
		// Write the config file
		//
		$config_file  = '';
		$config_file .= '<?php' . "\n";
		$config_file .= "/******************************\n";
		$config_file .= " * AUTO-GENERATED CONF FILE\n";
		$config_file .= " * DO NOT EDIT !!!\n";
		$config_file .= " ******************************/\n\n";

		$config_file .= '$db_host   = "' . $db_host    . '";' . "\n";
		$config_file .= '$db_name   = "' . $db_name    . '";' . "\n";
		$config_file .= '$db_user   = "' . $db_user    . '";' . "\n";
		$config_file .= '$db_passwd = "' . $db_passwd  . '";' . "\n";
		$config_file .= '$db_prefix = "' . $db_prefix  . '";' . "\n\n";

		$config_file .= 'define(\'ROSTER_INSTALLED\', true);' . "\n";

		$config_file .= '?>';

		// Set our permissions to execute-only
		@umask(0111);

		if ( !$fp = @fopen('conf.php', 'w') )
		{
			$this->messages .=
'<form method="post" action="upgrade.php" name="post">
	<input type="hidden" name="config_data" value="'.htmlspecialchars($config_file).'" />
	<input type="hidden" name="send_file" value="1" />
	<table cellspacing="1" cellpadding="2">
		<tr>
			<th align="left" colspan="2">Download conf.php</th>
		</tr>
		<tr>
			<td class="row1" align="center">&nbsp;<br />Download conf.php and upload it to your server into the directory that contains your roster files (eg. index.php, char.php, etc.)<br /><span style="font-weight: bold;" class="negative">BEFORE</span> you continue<br />&nbsp;</td>
		</tr>
		<tr>
			<td class="row2" align="center"><input type="submit" name="download" value="Download" class="mainoption" /></td>
		</tr>
	</table>
	<br />
</form>';
		}
		else
		{
			@fputs($fp, $config_file);
			@fclose($fp);

			$this->messages .= 'Your configuration file has been written, but upgrade will not be complete until you configure Roster';
		}
		$this->finalize($index);
	}

	/**
	 * The standard upgrader
	 * This parses the requested sql file for database upgrade
	 * Most upgrades will use this function
	 *
	 * @param string $ver
	 */
	function standard_upgrader($ver)
	{
		global $wowdb, $roster_root_path, $db_prefix;

		$db_structure_file = $roster_root_path . 'install'.DIR_SEP.'db'.DIR_SEP.'upgrade_'.$ver.'.sql';

		// Parse structure file and create database tables
		$sql = @fread(@fopen($db_structure_file, 'r'), @filesize($db_structure_file));
		$sql = preg_replace('#renprefix\_(\S+?)([\s\.,]|$)#', $db_prefix . '\\1\\2', $sql);

		$sql = remove_remarks($sql);
		$sql = parse_sql($sql, ';');

		$sql_count = count($sql);
		for ( $i = 0; $i < $sql_count; $i++ )
		{
			$wowdb->query($sql[$i]);
		}
		unset($sql);

		return;
	}

	function display_form()
	{
		$tpl = new Template_Wrap('upgrade.html','upgrade_header.html','upgrade_tail.html');

		foreach ( $this->versions as $version )
		{
			$selected = ( $version == ROSTER_OLDVERSION ) ? ' selected="selected"' : '';

			$tpl->assign_block_vars('version_row', array(
				'VALUE'    => str_replace('.', '', $version),
				'SELECTED' => $selected,
				'OPTION'   => 'Roster ' . $version,
				)
			);
		}

		$tpl->assign_vars(array(
			'ROSTER_UPGRADE'  => 'Roster Upgrade',
			'SELECT_VERSION' => 'Select the version you\'re upgrading from',
			'UPGRADE'        => 'Upgrade',
			)
		);

		$tpl->page_header();
		$tpl->page_tail();
	}
}

$upgrade = new Upgrade();

// And the upgrade-o-matic 5000 takes care of the rest.



/**
* Applies addslashes() to the provided data
*
* @param    mixed   $data   Array of data or a single string
* @return   mixed           Array or string of data
*/
function slash_global_data(&$data)
{
    if ( is_array($data) )
    {
        foreach ( $data as $k => $v )
        {
            $data[$k] = ( is_array($v) ) ? slash_global_data($v) : addslashes($v);
        }
    }
    return $data;
}


/**
 * Removes comments from a SQL data file
 *
 * @param    string  $sql    SQL file contents
 * @return   string
 */
function remove_remarks($sql)
{
	if ( $sql == '' )
	{
		die('Could not obtain SQL structure/data');
	}

	$retval = '';
	$lines  = explode("\n", $sql);
	unset($sql);

	foreach ( $lines as $line )
	{
		// Only parse this line if there's something on it, and we're not on the last line
		if ( strlen($line) > 0 )
		{
			// If '#' is the first character, strip the line
			$retval .= ( substr($line, 0, 1) != '#' ) ? $line . "\n" : "\n";
		}
	}
	unset($lines, $line);

	return $retval;
}

/**
 * Parse multi-line SQL statements into a single line
 *
 * @param    string  $sql    SQL file contents
 * @param    char    $delim  End-of-statement SQL delimiter
 * @return   array
 */
function parse_sql($sql, $delim)
{
	if ( $sql == '' )
	{
		die('Could not obtain SQL structure/data');
	}

	$retval     = array();
	$statements = explode($delim, $sql);
	unset($sql);

	$linecount = count($statements);
	for ( $i = 0; $i < $linecount; $i++ )
	{
		if ( ($i != $linecount - 1) || (strlen($statements[$i]) > 0) )
		{
			$statements[$i] = trim($statements[$i]);
			$statements[$i] = str_replace("\r\n", '', $statements[$i]) . "\n";

			// Remove 2 or more spaces
			$statements[$i] = preg_replace('#\s{2,}#', ' ', $statements[$i]);

			$retval[] = trim($statements[$i]);
		}
	}
	unset($statements);

	return $retval;
}

?>