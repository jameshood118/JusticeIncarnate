<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Shows items for every guild bank character
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: index.php 1259 2007-08-21 00:46:34Z Zanix $
 * @link       http://www.wowroster.net
 * @package    GuildBank
*/

# guildbank.php -- display items held by a guild's banker characters.
# Copyright 2005 vaccafoeda.hellscream@gmail.com
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the Affero General Public License as published by
# Affero, Incorporated; either version 1, or (at your option) any later
# version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# Affero General Public License for more details.
#
# You should have received a copy of the Affero General Public License
# along with this program; if not, download it from http://www.affero.org/

// Multiple edits done for WoWRoster

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

include( ROSTER_LIB . 'item.php' );

if( isset($_GET['mode']) )
{
	$gbank_mode = ( $_GET['mode'] == 'inv' ? '2' : '1' );
	$gbank_mode = ( $_GET['mode'] == 'full' ? '1' : '2' );
}
else
{
	$gbank_mode = $addon['config']['guildbank_ver'];
}

$columns = ( $gbank_mode == '2' ? '15' : '2' );

$roster->output['title'] = $roster->locale->act['guildbank'];

$muleNameQuery = "SELECT m.member_id, m.name AS member_name, m.note AS member_note, m.officer_note AS member_officer_note, p.money_g AS gold, p.money_s  AS silver, p.money_c AS copper"
			   . " FROM `" . $roster->db->table('players') . "` AS p, `" . $roster->db->table('members') . "`  AS m"
			   . " WHERE m." . $addon['config']['banker_fieldname'] . " LIKE '%" . $addon['config']['banker_rankname'] . "%' AND p.member_id = m.member_id AND m.guild_id = " . $roster->data['guild_id']
			   . " ORDER BY m.name";

$muleNames = $roster->db->query($muleNameQuery);

$bank_menu = '<table cellpadding="3" cellspacing="0" class="menubar">' . "\n<tr>\n";

$bank_menu .= '<td class="membersHeader"><a href="' . makelink('&amp;mode=full') . '">' . $roster->locale->act['gbank_list'] . "</a></td>\n";
$bank_menu .= '<td class="membersHeaderRight"><a href="' . makelink('&amp;mode=inv') . '">' . $roster->locale->act['gbank_inv'] . "</a></td>\n";

$bank_menu .= "</tr>\n</table>\n";

if( $addon['config']['bank_money'] )
{
	$mulemoney = $roster->db->fetch($roster->db->query(
			   "SELECT SUM( p.money_g ) AS gold, SUM( p.money_s ) AS silver, SUM( p.money_c ) as copper"
			   . " FROM `" . $roster->db->table('players') . "` AS p, `" . $roster->db->table('members') . "` AS m"
			   . " WHERE m." . $addon['config']['banker_fieldname'] . " LIKE '%" . $addon['config']['banker_rankname'] . "%'"
			   . " AND p.member_id = m.member_id AND m.guild_id = " . $roster->data['guild_id']
			   . " ORDER  BY m.name"
	));
	$addsilver=0;
	if( $mulemoney['copper']>=100 )
	{
		$mulemoney['copper'] = $mulemoney['copper']/100;
		$addsilver= (int)$mulemoney['copper'];
		$mulemoney['copper'] = explode ('.', $mulemoney['copper']);
		$mulemoney['copper'] = $mulemoney['copper'][1];
	}
	$mulemoney['silver'] = $mulemoney['silver'] + $addsilver;
	$addgold=0;
	if( $mulemoney['silver']>=100 )
	{
		$mulemoney['silver'] = $mulemoney['silver']/100;
		$addgold = (int)$mulemoney['silver'];
		$mulemoney['silver'] = explode ('.', $mulemoney['silver']);
		$mulemoney['silver'] = $mulemoney['silver'][1];
	}
	$mulemoney['gold'] = $mulemoney['gold'] + $addgold;

	$bank_money = $roster->locale->act['guildbank_totalmoney'] . ' <div class="money">'
				. $mulemoney['gold'] . ' <img src="' . $roster->config['img_url'] . 'coin_gold.gif" alt="g" /> '
				. $mulemoney['silver'] . ' <img src="' . $roster->config['img_url'] . 'coin_silver.gif" alt="s" /> '
				. $mulemoney['copper'] . ' <img src="' . $roster->config['img_url'] . 'coin_copper.gif" alt="c" /></div>';
}

$bankers = array();
$bank_print = '';

while( $muleRow = $roster->db->fetch($muleNames) )
{
	$bankers[$muleRow['member_id']] = $muleRow['member_name'];

	// Parse the note field for possible html characters
	$prg_find = array('/"/','/&/','|\\>|','|\\<|',"/\\n/");
	$prg_rep  = array('&quot;','&amp;','&gt;','&lt;','<br />');

	$note = preg_replace($prg_find, $prg_rep, $muleRow['member_note']);

	$date_char_data_updated = DateCharDataUpdated($muleRow['member_id']);

	$bank_print_member = ( active_addon('info') ? '<a href="' . makelink('char-info&amp;member=' . $muleRow['member_id']) . '">' . $muleRow['member_name'] . '</a>' : $muleRow['member_name']);
	$bank_print .= '<a id="c_' . $muleRow['member_id'] . '"></a>' . border('sgray','start',$bank_print_member . ' (' . $note . ') - <small>' . $roster->locale->act['lastupdate'] . ': ' . $date_char_data_updated . '</small>')
				 . '<table class="bodyline" cellspacing="0" cellpadding="0">'
				 . ( $addon['config']['bank_money'] ?
				 '<tr>
    <td colspan="' . $columns . '" class="membersRowRight2">'
				 . '<div class="money" align="center">'
				 . $muleRow['gold'] . ' <img src="' . $roster->config['img_url'] . 'coin_gold.gif" alt="g" /> '
				 . $muleRow['silver'] . ' <img src="' . $roster->config['img_url'] . 'coin_silver.gif" alt="s" /> '
				 . $muleRow['copper'] . ' <img src="' . $roster->config['img_url'] . 'coin_copper.gif" alt="c" /></div>'
				 . "</td>\n</tr>\n" : '' );

	$localeQuery = "SELECT `clientLocale` FROM `" . $roster->db->table('players') . "` WHERE  member_id = " . $muleRow['member_id'] ;
	$mulelocale = $roster->db->query_first($localeQuery);

	$itemsOnMuleQuery = "SELECT i.*,LEFT(i.item_id, (LOCATE(':',i.item_id)-1)) as real_itemid,sum(i.item_quantity) as total_quantity"
					  . " FROM `" . $roster->db->table('items') . "` as i"
					  . " WHERE " . $muleRow['member_id'] . " = i.member_id"
					  . " AND i.item_parent!='bags'"
					  . " AND i.item_parent!='equip'"
					  . " AND (i.item_tooltip"
					  . " NOT LIKE '%" . $roster->locale->wordings[$mulelocale]['tooltip_soulbound'] . "%'"
					  . " OR i.item_tooltip"
					  . " LIKE '%" . $roster->locale->wordings[$mulelocale]['tooltip_boe'] . "%')"
					  . " GROUP BY real_itemid"
					  . " ORDER BY i.item_name";

	$itemsOnMule = $roster->db->query($itemsOnMuleQuery);

	$itemRow=$roster->db->fetch($itemsOnMule);
	if( $itemRow==false )
	{
		$bank_print .= '  <tr>
    <td class="membersRowRight1">' . sprintf($roster->locale->act['gbank_not_loaded'],$muleRow['member_name']) . "</td>
  </tr>\n";

	}
	else
	{
		$bank_print .= '  <tr>
    <td class="membersRowRight1">';
		$column_counter = 1;
		$bank_print .= '<table width="100%" cellspacing="0" cellpadding="2">';

		$striping_counter = 1;

		while( $itemRow )
		{
			if( $column_counter == 1 )
			{
				$striping_counter++;
			}

			$stripe_class = 'membersRow' . ( ( $striping_counter % 2 ) + 1 );
			$stripe_class_right = 'membersRowRight' . ( ( $striping_counter % 2 ) + 1 );

			if( $column_counter==1 )
			{
				$bank_print .= "  <tr>\n";
			}

			// Item texture and quantity column
			if( $gbank_mode == '1' )
			{
				$bank_print .= '    <td valign="top" align="center" class="' . $stripe_class . '">';
			}
			else
			{
				$bank_print .= '    <td valign="top" align="center">';
			}

			$itemRow['item_quantity'] = $itemRow['total_quantity'];

			$item = new item($itemRow);
			$bank_print .= $item->out();

			$bank_print .= "    </td>\n";
			if( $gbank_mode == '1' )
			{
				$bank_print .= '    <td valign="top" class="' . $stripe_class_right . ' overlib_maintext" style="width:220px;">';
				$bank_print .= $item->html_tooltip;
//				$bank_print .= colorTooltip(stripslashes($itemRow['item_tooltip']),$itemRow['item_color']);
				$bank_print .= '    </td>';
			}

			if( $column_counter==$columns )
			{
				$bank_print .= "  </tr>\n";
				$column_counter=0;
			}
			$column_counter++;
			$itemRow = $roster->db->fetch($itemsOnMule);
		}
		if( $column_counter >= 0 && substr($bank_print,-6) != "</tr>\n" )
		{
			$bank_print .= "  </tr>\n";
		}
		$bank_print .= "</table></td>\n</tr>\n";
	}
	$bank_print .= '</table>' . border('sgray','end') . '<br />';
}


$banker_list = '- ';
foreach( $bankers as $banker_id => $banker  )
{
	$banker_list .= '<a href="' . makelink('#c_'.$banker_id) . '">' . $banker . '</a> - ';
}

print messagebox($bank_menu,$roster->locale->act['guildbank'],'sorange');

print '<br />';

print $banker_list . "\n<br /><br />\n" . (isset($bank_money) ? $bank_money : '') . "\n<br />\n" . $bank_print;

/**
 * Gets the last upload date for a character
 *
 * @param int $id | Member ID
 * @return string
 */
function DateCharDataUpdated( $id )
{
	global $roster;

	$query = "SELECT `dateupdatedutc`, `clientLocale` FROM `" . $roster->db->table('players') . "` WHERE `member_id` = '$id'";
	$result = $roster->db->query($query);
	$data = $roster->db->fetch($result);
	$roster->db->free_result($result);

	list($year,$month,$day,$hour,$minute,$second) = sscanf($data['dateupdatedutc'],"%d-%d-%d %d:%d:%d");
	$localtime = mktime($hour+$roster->config['localtimeoffset'] ,$minute, $second, $month, $day, $year, -1);
	return date($roster->locale->wordings[$data['clientLocale']]['phptimeformat'], $localtime);
}
