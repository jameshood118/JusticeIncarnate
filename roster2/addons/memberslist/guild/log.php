<?php
/**
 * WoWRoster.net WoWRoster
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: log.php 1281 2007-08-25 09:15:11Z Zanix $
 * @link       http://www.wowroster.net
 * @package    MembersList
*/

if ( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

include_once ($addon['dir'] . 'inc/memberslist.php');

$memberlist = new memberslist(array('group_alts'=>-1, 'page_size'=>25));

$mainQuery =
	'SELECT *, DATE_FORMAT( `update_time`, "' . $roster->locale->act['timeformat'] . '" ) AS date, '.
	'UNIX_TIMESTAMP(`update_time`) AS date_stamp '.
	'FROM `'.$roster->db->table('memberlog').'` AS members '.
	'WHERE `guild_id` = "'.$roster->data['guild_id'].'"'.
	'ORDER BY ';

$always_sort = ' `members`.`update_time` DESC';


$FIELD['name'] = array(
	'lang_field' => 'name',
	'order'    => array( '`name` ASC' ),
	'order_d'    => array( '`name` DESC' ),
	'js_type' => 'ts_string',
	'display' => 3,
);

$FIELD['class'] = array(
	'lang_field' => 'class',
	'order'    => array( '`class` ASC' ),
	'order_d'    => array( '`class` DESC' ),
	'value' => array($memberlist,'class_value'),
	'js_type' => 'ts_string',
	'display' => $addon['config']['log_class'],
);

$FIELD['level'] = array(
	'lang_field' => 'level',
	'order_d'    => array( '`level` ASC' ),
	'value' => array($memberlist,'level_value'),
	'js_type' => 'ts_number',
	'display' => $addon['config']['log_level'],
);

$FIELD['guild_title'] = array (
	'lang_field' => 'title',
	'order' => array( '`guild_rank` ASC' ),
	'order_d' => array( '`guild_rank` DESC' ),
	'js_type' => 'ts_number',
	'jsort' => 'guild_rank',
	'display' => $addon['config']['log_gtitle'],
);

$FIELD['type'] = array (
	'lang_field' => 'type',
	'order' => array( '`type` ASC' ),
	'order_d' => array( '`type` DESC' ),
	'value' => 'type_value',
	'js_type' => 'ts_number',
	'display' => $addon['config']['log_type'],
);

$FIELD['date'] = array (
	'lang_field' => 'date',
	'order' => array( 'date DESC' ),
	'order_d' => array( 'date ASC' ),
	'jsort' => 'date_stamp',
	'js_type' => 'ts_date',
	'display' => $addon['config']['log_date'],
);

$FIELD['note'] = array (
	'lang_field' => 'note',
	'order' => array( 'nisnull','`note` ASC' ),
	'order_d' => array( 'nisnull','`note` DESC' ),
	'value' => 'note_value',
	'js_type' => 'ts_string',
	'display' => $addon['config']['log_note'],
);

$FIELD['officer_note'] = array (
	'lang_field' => 'onote',
	'order' => array( 'onisnull','`note` ASC' ),
	'order_d' => array( 'onisnull','`note` DESC' ),
	'value' => 'note_value',
	'js_type' => 'ts_string',
	'display' => $addon['config']['log_onote'],
);

$memberlist->prepareData($mainQuery, $always_sort, $FIELD, 'memberslist');

$menu = '';
// Start output
if( $addon['config']['log_update_inst'] )
{
	$menu .= '            <a href="' . makelink('#update') . '"><span style="font-size:20px;">'.$roster->locale->act['update_link'].'</span></a><br /><br />';
}

if ( $addon['config']['log_motd'] == 1 )
{
	$menu .= $memberlist->makeMotd();
}

$roster->output['before_menu'] .= $menu;

if( $addon['config']['log_hslist'] == 1 || $addon['config']['log_pvplist'] == 1 )
{
	echo "<table>\n  <tr>\n";

	if ( $addon['config']['log_hslist'] == 1 )
	{
		echo '    <td valign="top">';
		include_once( ROSTER_LIB.'hslist.php');
		echo generateHsList();
		echo "    </td>\n";
	}

	if ( $addon['config']['honor_pvplist'] == 1 && active_addon('pvplog') )
	{
		echo '    <td valign="top">';
		include_once( ROSTER_ADDONS.'pvplog'.DIR_SEP.'inc'.DIR_SEP.'pvplist.php');
		echo generatePvpList();
		echo "    </td>\n";
	}

	echo "  </tr>\n</table>\n";
}

echo $memberlist->makeFilterBox();

echo $memberlist->makeToolBar('horizontal');

echo "<br />\n".border('syellow','start')."\n";
echo $memberlist->makeMembersList();
echo border('syellow','end');

// Print the update instructions
if( $addon['config']['log_update_inst'] )
{
	print "<br />\n\n<a name=\"update\"></a>\n";

	echo border('sgray','start',$roster->locale->act['update_instructions']);
	echo '<div align="left" style="font-size:10px;background-color:#1F1E1D;">'.sprintf($roster->locale->act['update_instruct'], $roster->config['uploadapp'], $roster->locale->act['index_text_uniloader'], $roster->config['profiler'], makelink('update'), $roster->locale->act['lualocation']);
	echo '</div>'.border('sgray','end');
}


/**
 * Controls Output of a Note Column
 *
 * @param array $row - of character data
 * @return string - Formatted output
 */
function note_value ( $row, $field )
{
	global $roster, $addon;

	if( !empty($row[$field]) )
	{
		$note = htmlspecialchars(nl2br($row[$field]));

		if( $addon['config']['compress_note'] )
		{
			$note = '<img src="'.$roster->config['img_url'].'note.gif" style="cursor:help;" '.makeOverlib($note,$roster->locale->act['note'],'',1,'',',WRAP').' alt="[]" />';
		}
	}
	else
	{
		$note = '&nbsp;';
		if( $addon['config']['compress_note'] )
		{
			$note = '<img src="'.$roster->config['img_url'].'no_note.gif" alt="[]" />';
		}
	}

	return '<div style="display:none; ">'.$row['note'].'</div>'.$note;
}


/**
 * Controls Output of a Type Column
 *
 * @param array $row - of character data
 * @return string - Formatted output
 */
function type_value ( $row, $field )
{
	global $roster, $addon;

	if( $row['type'] == 0 )
	{
		$return = '<span class="red">' . $roster->locale->act['removed'] . '</span>';
	}
	else
	{
		$return = '<span class="green">' . $roster->locale->act['added'] . '</span>';
	}

	return '<div style="display:none; ">'.$row['type'].'</div>'.$return;
}
