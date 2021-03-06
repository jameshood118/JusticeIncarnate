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
 * $Id: luaparser.php 603 2007-02-14 07:37:56Z zanix $
 *
 ******************************/

if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}

/**
* Wrapper function so that you can parse a file instead of an array.
* @author six
*/
function ParseLuaFile( $file_name , $file_type=null )
{
	if( file_exists($file_name) && is_readable($file_name) )
	{
		if( $file_type == 'gz' )
		{
			$file_as_array = gzfile($file_name);
		}
		else
		{
			$file_as_array = file($file_name);
		}

		return(ParseLuaArray($file_as_array));
	}
	return(false);
}

/**
* Main LUA parsing function
* @author six, originally mordon
*/
function ParseLuaArray( &$file_as_array )
{
	if( !is_array($file_as_array) )
	{
		// return false if not presented with an array
		return(false);
	}
	else
	{
		// Parse the contents of the array
		$stack = array( array( '',  array() ) );
		$stack_pos = 0;
		$last_line = '';
		foreach( $file_as_array as $line )
		{
			// join lines ending in \\ together
			if( substr( $line, -2, 1 ) == '\\' )
			{
				$last_line .= substr($line, 0, -2) . "\n";
				continue;
			}
			if($last_line!='')
			{
				$line = trim($last_line . $line);
				$last_line = '';
			}
			else
			{
				$line = trim($line);
			}

			// Look for end of an array
			if( $line[0] == '}' )
			{
				$hash = $stack[$stack_pos];
				unset($stack[$stack_pos]);
				$stack_pos--;
				$stack[$stack_pos][1][$hash[0]] = $hash[1];
				unset($hash);
			}
			// Handle other cases
			else
			{
				// Check if the key is given
				if( strpos($line,'=') )
				{
					list($name, $value) = explode( '=', $line, 2 );
					$name = trim($name);
					$value = trim($value,', ');
					if($name[0]=='[')
					{
						$name = trim($name, '[]"');
					}
				}
				// Otherwise we'll have to make one up for ourselves
				else
				{
					$value = $line;
					if( empty($stack[$stack_pos][1]) )
					{
						$name = 1;
					}
					else
					{
						$name = max(array_keys($stack[$stack_pos][1]))+1;
					}
					if( strpos($line,'-- [') )
					{
						$value = explode('-- [',$value);
						array_pop($value);
						$value = implode('-- [',$value);
					}
					$value = trim($value,', ');
				}
				if( $value == '{' )
				{
					$stack_pos++;
					$stack[$stack_pos] = array($name, array());
				}
				else
				{
					if($value[0]=='"')
					{
						$value = substr($value,1,-1);
					}
					else if($value == 'true')
					{
						$value = true;
					}
					else if($value == 'false')
					{
						$value = false;
					}
					else if($value == 'nil')
					{
						$value = NULL;
					}
					$stack[$stack_pos][1][$name] = $value;
				}
			}
		}
		return($stack[0][1]);
	}
}

?>
