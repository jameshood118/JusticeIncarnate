<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Roster global class
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: roster.php 1272 2007-08-23 23:17:44Z ds $
 * @link       http://www.wowroster.net
 * @since      File available since Release 1.8.0
 * @package    WoWRoster
 * @subpackage RosterClass
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

class roster
{
	var $config = array();
	var $multilanguages = array();

	/**
	 * Roster Locale Object
	 *
	 * @var roster_locale
	 */
	var $locale;

	/**
	 * Roster database Object
	 *
	 * @var roster_db
	 */
	var $db;
	var $pages;
	var $scope;
	var $data = false; // scope data
	var $addon_data;

	/**
	 * Roster Error Handler Object
	 *
	 * @var roster_error
	 */
	var $error; // Error handler class

	/**
	 * Roster Cache Class Object
	 *
	 * @var RosterCache
	 */
	var $cache;

	var $output = array(
		'http_header' => true,
		'show_header' => true,
		'show_menu'   => array('util','realm','guild'),
		'show_footer' => true,

		// used on rostercp pages
		'header'  => '',
		'menu'    => '',
		'body'    => '',
		'pagebar' => '',
		'footer'  => '',

		// used on other pages
		'content' => '',

		// header stuff
		'title'       => '',
		'html_head'   => '',
		'body_attr'   => '',
		'body_onload' => '',
		'before_menu' => ''
	);

	/**
	 * Roster Template Object
	 *
	 * @var RosterTemplate
	 */
	var $tpl;								// Template object
	var $row_class         = 2;				// For row striping in templates

	/**
	 * Load the DBAL
	 */
	function load_dbal()
	{
		global $db_config;

		switch( $db_config['dbtype'] )
		{
			case 'mysql':
				include_once(ROSTER_LIB . 'dbal' . DIR_SEP . 'mysql.php');
				break;

			default:
				include_once(ROSTER_LIB . 'dbal' . DIR_SEP . 'mysql.php');
				break;
		}

		$this->db = new roster_db($db_config['host'], $db_config['database'], $db_config['username'], $db_config['password'], $db_config['table_prefix']);

		if ( !$this->db->link_id )
		{
			die(__FILE__ . ': line[' . __LINE__ . ']<br />Could not connect to database "' . $db_config['database'] . '"<br />MySQL said:<br />' . $this->db->connect_error());
		}
	}

	/**
	 * Load the config
	 */
	function load_config()
	{
		$query = "SELECT `config_name`, `config_value` FROM `" . $this->db->table('config') . "` ORDER BY `id` ASC;";
		$results = $this->db->query($query);

		if( !$results || $this->db->num_rows($results) == 0 )
		{
			die("Cannot get roster configuration from database<br />\nMySQL Said: " . $this->db->error() . "<br /><br />\nYou might not have roster installed<br />\n<a href=\"install.php\">INSTALL</a>");
		}

		while( $row = $this->db->fetch($results) )
		{
			$this->config[$row['config_name']] = $row['config_value'];
		}
		$this->db->free_result($results);
	}

	/**
	 * Figure out the page to load, and put it in $this->pages and ROSTER_PAGE_NAME
	 */
	function get_page_name()
	{
		// cmslink function to resolve SEO linking etc.
		parse_params();

		// --[ Determine the module request ]--
		if( isset($_GET[ROSTER_PAGE]) && !empty($_GET[ROSTER_PAGE]) )
		{
			$page = $_GET[ROSTER_PAGE];
		}
		elseif( !strpos($this->config['default_page'], '&amp;') )
		{
			$page = $this->config['default_page'];
		}
		else
		{
			// --[ Insert directly into GET request ]--
			list($page, $gets) = explode('&amp;',$this->conf['default_page'],2);
			foreach( explode('&amp;',$gets) as $get )
			{
				list($key, $value) = explode('=',$get,2);
				$_GET[$key] = $value;
			}
		}

		define('ROSTER_PAGE_NAME', $page);

		$this->pages = explode('-', $page);

		// --[ We only accept certain characters in our page ]--
		if( preg_match('/[^a-zA-Z0-9_-]/', ROSTER_PAGE_NAME) )
		{
			roster_die($this->locale->act['invalid_char_module'],$this->locale->act['roster_error']);
		}
	}

	/**
	 * Get the data for the current scope and assign it to $this->data
	 */
	function get_scope_data()
	{
		// --[ Fetch the right data for the scope ]--
		switch( $this->pages[0] )
		{
			case 'char':
				$this->scope = 'char';

				// Check if the member attribute is set
				if( !isset($_GET['member']) )
				{
					roster_die('You need to provide a member id or name@server','WoWRoster');
				}

				// Parse the attribute
				if( is_numeric($_GET['member']) )
				{
					$where = ' `players`.`member_id` = "' . $_GET['member'] . '"';
				}
				elseif( strpos($_GET['member'], '@') !== false )
				{
					list($name, $realm) = explode('@',$_GET['member']);
					if( strpos($realm,'-') !== false )
					{
						list($region, $realm) = explode('-',$realm);
						$where = ' `players`.`name` = "' . $name . '" AND `players`.`server` = "' . $realm . '" AND `players`.`region` = "' . strtoupper($region) . '"';
					}
					else
					{
						$where = ' `players`.`name` = "' . $name . '" AND `players`.`server` = "' . $realm . '"';
					}
				}
				else
				{
					$name = $_GET['member'];
					$where = ' `players`.`name` = "' . $name . '"';
				}

				// Get the data
				$query = 'SELECT *, DATE_FORMAT(  DATE_ADD(`players`.`dateupdatedutc`, INTERVAL '
					   . $this->config['localtimeoffset'] . ' HOUR ), "' . $this->locale->act['timeformat'] . '" ) AS "update_format" '
					   . 'FROM `' . $this->db->table('players') . '` players '
					   . 'LEFT JOIN `'.$this->db->table('members') . '` members ON `players`.`member_id` = `members`.`member_id` '
					   . 'LEFT JOIN `'.$this->db->table('guild').'` guild ON `players`.`guild_id` = `guild`.`guild_id` '
					   . 'WHERE ' . $where;

				$result = $this->db->query($query);

				if( !$result )
				{
					die_quietly($this->db->error(),'Database error',__FILE__,__LINE__,$query);
				}

				if(!( $this->data = $this->db->fetch($result)) )
				{
					roster_die('This member is not in the database',$this->locale->act['roster_error']);
				}

				$this->db->free_result($result);

				break;

			case 'guild':
				$this->scope = 'guild';

				// Check if the guild attribute is set
				if( !isset($_GET['guild']) )
				{
					// Get the default selected guild from the upload rules
					$query =  "SELECT `name`, `server`, `region`"
							. " FROM `" . $this->db->table('upload') . "`"
							. " WHERE `default` = '1' LIMIT 1;";

					$this->db->query($query);

					$data = $this->db->fetch();

					$name = $this->db->escape( $data['name'] );
					$realm = $this->db->escape( $data['server'] );
					$region = $this->db->escape( $data['region'] );
					$where = ' `guild_name` = "' . $name . '" AND `server` = "' . $realm . '" AND `region` = "' . $region . '"';
				}
				// Parse the attribute
				elseif( is_numeric($_GET['guild']) )
				{
					$where = ' `guild_id` = "' . $_GET['guild'] . '"';
				}
				elseif( strpos($_GET['guild'],'@') !== false )
				{
					list($name, $realm) = explode('@',$_GET['guild']);
					if( strpos($realm,'-') !== false )
					{
						list($region, $realm) = explode('-',$realm);
						$where = ' `guild_name` = "' . $name . '" AND `server` = "' . $realm . '" AND `region` = "' . strtoupper($region) . '"';
					}
					else
					{
						$where = ' `guild_name` = "' . $name . '" AND `server` = "' . $realm . '"';
					}
				}
				else
				{
					$name = $this->db->escape( $data['name'] );
					$where = ' `guild_name` = "' . $name . '"';
				}

				// Get the data
				$query = "SELECT * ".
					"FROM `" . $this->db->table('guild') . "` ".
					"WHERE " . $where . ";";

				$result = $this->db->query($query);

				if( !$result )
				{
					die_quietly($this->db->error(),'Database Error',__FILE__.'<br />Function: '.__FUNCTION__,__LINE__,$query);
				}

				if(!( $this->data = $this->db->fetch($result)) )
				{
					// These are set for the roster menu
					$this->data['guild_name'] = $name;
					$this->data['region'] = $region;
					$this->data['server'] = $realm;
					$this->data['guild_id'] = '';
					roster_die( sprintf($this->locale->act['nodata'], $name, $realm, makelink('update'), makelink('rostercp') ), $this->locale->act['nodata_title'] );
				}

				$this->db->free_result($result);

				break;

			case 'realm':
				$this->scope = 'realm';

				// Check if the realm attribute is set
				if( !isset($_GET['realm']) )
				{
					// Get the default selected guild from the upload rules
					$query  =  "SELECT `server`, `region`"
							. " FROM `" . $this->db->table('upload') . "`"
							. " WHERE `default` = '1' LIMIT 1;";

					$this->db->query($query);

					$data = $this->db->fetch();

					$realm = $this->db->escape( $data['server'] );
					$region = $this->db->escape( $data['region'] );
					$where = ' `server` = "' . $realm . '" AND `region` = "' . $region . '"';
				}
				elseif( strpos($_GET['realm'],'-') !== false )
				{
					list($region, $realm) = explode('-',$_GET['realm']);
					$where = ' `server` = "' . $realm . '" AND `region` = "' . strtoupper($region) . '"';
				}
				else
				{
					//$realm = $_GET['realm'];
					//$where = ' `server` = "' . $realm . '"';
					roster_die('You must specify a region code in the realm name','Region Code Required');
				}

				// Get the selected data
				$query = "SELECT DISTINCT `server`, `region`"
					   . " FROM `" . $this->db->table('guild') . "`"
					   . " UNION SELECT DISTINCT `server`, `region` FROM `" . $this->db->table('players') . "`"
					   . " WHERE $where"
					   . " LIMIT 1;";

				$result = $this->db->query($query);

				if( !$result )
				{
					die_quietly($this->db->error(), 'Database Error', __FILE__ . '<br />Function: ' . __FUNCTION__, __LINE__, $query);
				}

				if(!( $this->data = $this->db->fetch($result,SQL_ASSOC)) )
				{
					roster_die( sprintf($this->locale->act['nodata'], '', $realm, makelink('update'), makelink('rostercp') ), $this->locale->act['nodata_title'] );
				}

				$this->data = array('server' => stripslashes($realm),'region' => $region);

				break;

			default:
				$this->scope = 'util';
				$this->data = array();
				break;
		}

		// Set menu array
		if( isset($this->data['member_id']) )
		{
			$this->output['show_menu'][] = 'char';
		}
	}

	/**
	 * Fetch all addon data. We need to cache the active status for addon_active()
	 * and fetching everything isn't much slower and saves extra fetches later on.
	 */
	function get_addon_data()
	{
		$query = "SELECT * FROM `" . $this->db->table('addon') . "`;";
		$result = $this->db->query($query);
		$this->addon_data = array();
		while( $row = $this->db->fetch($result,SQL_ASSOC) )
		{
			$this->addon_data[$row['basename']] = $row;
		}
	}

	/**
	 * Switches the class for row coloring
	 *
	 * @param bool $set_new
	 * @return int
	 */
	function switch_row_class( $set_new = true )
	{
		$row_class = ( $this->row_class == 1 ) ? 2 : 1;

		if( $set_new )
		{
			$this->row_class = $row_class;
		}

		return $row_class;
	}
}
