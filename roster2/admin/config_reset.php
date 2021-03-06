<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Configuration reset
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: config_reset.php 1181 2007-08-12 01:38:06Z Zanix $
 * @link       http://www.wowroster.net
 * @since      File available since Release 1.8.0
 * @package    WoWRoster
 * @subpackage RosterCP
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

$roster->output['title'] .= $roster->locale->act['pagebar_configreset'];

if( isset($_POST['doit']) && ($_POST['doit'] == 'doit') )
{
	$query = "TRUNCATE `roster_config`;";
	$roster->db->query($query);

    $db_data_file = ROSTER_LIB . 'dbal' . DIR_SEP . 'structure' . DIR_SEP . 'mysql_data.sql';

    // Parse the data file and populate the database tables
    $sql = @fread(@fopen($db_data_file, 'r'), @filesize($db_data_file));
    $sql = preg_replace('#renprefix\_(\S+?)([\s\.,]|$)#', $roster->db->prefix . '\\1\\2', $sql);

    $sql = parse_sql($sql, ';');

    $sql_count = count($sql);
    for ( $i = 0; $i < $sql_count; $i++ )
    {
        $roster->db->query($sql[$i]);
    }
    unset($sql);

	$body .= messagebox($roster->locale->act['config_is_reset'],$roster->locale->act['roster_cp']);
	return;
}

$body .= '<form action="' . makelink() . '" method="post" enctype="multipart/form-data" id="conf_change_pass" onsubmit="return confirm(\'' . $roster->locale->act['config_reset_confirm'] . '\') &amp;&amp; submitonce(this);">
<input type="hidden" name="doit" value="doit" />
' . border('sred','start',$roster->locale->act['pagebar_configreset']) . '
	<table class="bodyline" cellspacing="0" cellpadding="0">
		<tr>
			<td class="membersRowRight1" colspan="2"><div style="white-space:normal;">' . $roster->locale->act['config_reset_help'] . '</div></td>
		</tr>
		<tr>
			<td class="membersRow2">' . $roster->locale->act['password'] . ':</td>
			<td class="membersRowRight2"><input class="wowinput192" type="password" name="password" value="" /></td>
		</tr>
		<tr>
			<td colspan="2" class="membersRowRight1" valign="bottom"><div align="center">
				<input type="submit" value="' . $roster->locale->act['proceed'] . '" /></div></td>
		</tr>
	</table>
' . border('sred','end') . '
</form>';



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
