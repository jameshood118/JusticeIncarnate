<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Roster Error Handler
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: roster_error.php 1254 2007-08-19 16:41:28Z Zanix $
 * @link       http://www.wowroster.net
 * @since      File available since Release 1.8.0
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

if( !defined('E_STRICT') )
{
	define('E_STRICT', 2048);
}

class roster_error
{
	// Define variables that store the old error reporting and logging states
	var $old_handler;
	var $old_display_level;
	var $old_error_logging;

	var $report;
	var $active = false;
	var $error_level;

	function roster_error( $error_level=E_ALL )
	{
		if( !$this->active )
		{
			error_reporting($error_level);

			$this->report = false;
			if( CAN_INI_SET )
			{
				$this->old_display_level = ini_set('display_errors', 1);
				$this->old_error_logging = ini_set('log_errors', 0);
			}

			$this->old_handler = set_error_handler(array(&$this, 'handler'));

			$this->error_level = E_ALL;
			$this->active = true;
		}
	}

	function stop( )
	{
		if( $this->active )
		{
			// restore the previous state
			if( !is_bool($this->old_handler) && $this->old_handler )
			{
				set_error_handler($this->old_handler );
			}
			if( CAN_INI_SET )
			{
				ini_set('display_errors', $this->old_display_level);
				ini_set('log_errors', $this->old_error_logging);
			}
			$this->active = false;
			return $this->report;
		}
	}

	// user defined error handling function
	function handler( $errno , $errmsg , $filename , $linenum , $vars='' )
	{
		global $roster;

		$errortype = array (
			E_WARNING         => 'Warning',
			E_NOTICE          => 'Notice',
			E_CORE_ERROR      => 'Core Error',
			E_CORE_WARNING    => 'Core Warning',
			E_COMPILE_ERROR   => 'Compile Error',
			E_COMPILE_WARNING => 'Compile Warning',
			E_USER_ERROR      => 'Roster Error',
			E_USER_WARNING    => 'Roster Warning',
			E_USER_NOTICE     => 'Roster Notice',
			E_STRICT          => 'Runtime Notice'
		);
		// NOTE: E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR and E_COMPILE_WARNING
		// error levels will be handled as per the error_reporting settings.
		if( $errno == E_USER_ERROR )
		{
			if( $roster->config['debug_mode'] )
			{
				die_quietly($errmsg,$errortype[$errno],$filename,$linenum);
			}
			else
			{
				die_quietly("A error occured while processing this page.<br />Please report the following error to the owner of this website.<br /><br /><b>$errmsg</b>",$errortype[$errno]);
			}
		}

		// set of errors for which a trace will be saved
		if( $errno & $this->error_level )
		{
			$this->report[$filename][] = $errortype[$errno]." line $linenum: ".$errmsg;
		}
	}
}
