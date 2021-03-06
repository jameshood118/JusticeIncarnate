<?php
/**
 * WoWRoster.net WoWRoster
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: install.def.php 1281 2007-08-25 09:15:11Z Zanix $
 * @link       http://www.wowroster.net
 * @package    InstanceKeys
 * @subpackage Installer
*/

if ( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

/**
 * Installer Instance Keys Addon
 *
 * @package    InstanceKeys
 * @subpackage Installer
 */
class keys
{
	var $active = true;
	var $icon = 'inv_misc_key_06';

	var $version = '2.0.0.0';

	var $fullname = 'keys';
	var $description = 'keys_desc';
	var $credits = array(
		array(	"name"=>	"WoWRoster Dev Team",
				"info"=>	"Original Author")
	);


	/**
	 * Install Function
	 *
	 * @return bool
	 */
	function install()
	{
		global $installer;

		// Master and menu entries
		$installer->add_config("'1','startpage','keys_conf','display','master'");
		$installer->add_config("'110','keys_conf',NULL,'blockframe','menu'");

		$installer->add_config("'1010','colorcmp','#00ff00','color','keys_conf'");
		$installer->add_config("'1020','colorcur','#ffd700','color','keys_conf'");
		$installer->add_config("'1030','colorno','#ff0000','color','keys_conf'");

		$installer->add_menu_button('keybutton','guild');
		return true;
	}

	/**
	 * Upgrade Function
	 *
	 * @param string $oldversion
	 * @return bool
	 */
	function upgrade($oldversion)
	{
		// Nothing to upgrade from yet
		return true;
	}

	/**
	 * Un-Install Function
	 *
	 * @return bool
	 */
	function uninstall()
	{
		global $installer;

		$installer->remove_all_config();

		$installer->remove_all_menu_button();
		return true;
	}
}
