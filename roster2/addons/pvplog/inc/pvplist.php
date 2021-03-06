<?php
/**
 * WoWRoster.net WoWRoster
 *
 * PvPLog ranking list
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: pvplist.php 1241 2007-08-16 06:06:25Z Zanix $
 * @link       http://www.wowroster.net
 * @package    PvPLog
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

function pvprankMid( $sc )
{
	return '    <td class="membersRow' . $sc . '">';
}
function pvprankRight( $sc )
{
	return '    <td class="membersRowRight' . $sc . '">';
}
function generatePvpList( )
{
	global $roster;

	$output = '<table width="100%" cellpadding="0" cellspacing="0" class="bodyline">' . "\n";

	$striping_counter = 0;

	// Guild that suffered most at our hands
	$query = "SELECT `pvp`.`guild`, COUNT(`pvp`.`guild`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` AS pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` AS members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`guild` != '' AND `pvp`.`win` = '1' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`guild` ORDER BY countg DESC";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );
	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=guildwins') . '">'.$roster->locale->act['pvplist1'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		if( $row['guild'] == '' )
		{
			$guildname = '(unguilded)';
		}
		else
		{
			$guildname = $row['guild'];
		}
		$output .= $guildname;
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));
		$output .= $row['countg'];
		$output .= "</td>\n  </tr>\n";
	}


	// Guild that killed us the most
	$query = "SELECT `pvp`.`guild`, COUNT(`pvp`.`guild`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` AS pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` AS members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`guild` != '' AND `pvp`.`win` = '0' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`guild` ORDER BY countg DESC";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );
	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=guildlosses') . '">'.$roster->locale->act['pvplist2'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		if( $row['guild'] == '' )
		{
			$guildname = '(unguilded)';
		}
		else
		{
			$guildname = $row['guild'];
		}
		$output .= $guildname;
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));
		$output .= $row['countg'];
		$output .= "</td>\n  </tr>\n";
	}


	// Player who we killed the most
	$query = "SELECT `pvp`.`name`, COUNT(`pvp`.`name`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` AS pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` AS members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`guild` != '' AND `pvp`.`win` = '1' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`name` ORDER BY countg DESC";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );
	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=enemywins') . '">' . $roster->locale->act['pvplist3'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= $row['name'];
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));
		$output .= $row['countg'];
		$output .= "</td>\n  </tr>\n";
	}


	// Player who killed us the most
	$query = "SELECT `pvp`.`name`, COUNT(`pvp`.`name`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` AS pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` AS members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`win` = '0' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`name` ORDER BY countg DESC";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );
	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=enemylosses') . '">' . $roster->locale->act['pvplist4'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= $row['name'];
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));
		$output .= $row['countg'];
		$output .= "</td>\n  </tr>\n";
	}


	// Member with the most kills
	$query = "SELECT `pvp`.`member_id`, `members`.`name` AS gn, COUNT(`pvp`.`member_id`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`win` = '1' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`member_id` ORDER BY countg DESC;";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );
	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;

		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=purgewins') . '">' . $roster->locale->act['pvplist5'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= $row['gn'];
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));
		$output .= $row['countg'];
		$output .= "</td>\n  </tr>\n";
	}


	// Member who has died the most
	$query = "SELECT `pvp`.`member_id`, `members`.`name` AS gn, COUNT(`pvp`.`member_id`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`win` = '0' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`member_id` ORDER BY countg DESC;";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );

	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;

		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=purgelosses') . '">' . $roster->locale->act['pvplist6'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= $row['gn'];
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));
		$output .= $row['countg'];
		$output .= "</td>\n  </tr>\n";
	}


	// Member with best kill average
	$query = "SELECT `pvp`.`member_id`, `members`.`name` AS gn, AVG(`pvp`.`leveldiff`) AS ave, COUNT(`pvp`.`member_id`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` AS pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` AS members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`win` = '1' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`member_id` ORDER BY ave DESC";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );

	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;

		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=purgeavewins') . '">' . $roster->locale->act['pvplist7'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= $row['gn'];
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));

		$ave = round($row['ave'], 2);

		if( $ave > 0 )
		{
			$ave = '+'.$ave;
		}
		$output .= $ave;
		$output .= "</td>\n  </tr>\n";
	}


	// Member with best loss average
	$query = "SELECT `pvp`.`member_id`, `members`.`name` AS gn, AVG(`pvp`.`leveldiff`) AS ave, COUNT(`pvp`.`member_id`) AS countg"
		   . " FROM `" . $roster->db->table('pvp2') . "` AS pvp"
		   . " LEFT JOIN `" . $roster->db->table('members') . "` AS members ON `members`.`member_id` = `pvp`.`member_id`"
		   . " WHERE `members`.`guild_id` = '" . $roster->data['guild_id'] . "' AND `pvp`.`win` = '0' AND `pvp`.`enemy` = '1'"
		   . " GROUP BY `pvp`.`member_id` ORDER BY ave DESC";

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	$row = $roster->db->fetch( $result );

	if( $row )
	{
		// Striping rows
		$output .= "  <tr>\n";

		// Increment counter so rows are colored alternately
		++$striping_counter;
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= '<a href="' . makelink('guild-pvplog&amp;type=purgeavelosses') . '">' . $roster->locale->act['pvplist8'] . '</a></td>' . "\n";
		$output .= pvprankMid((($striping_counter % 2) +1));
		$output .= $row['gn'];
		$output .= "</td>\n";
		$output .= pvprankRight((($striping_counter % 2) +1));

		$ave = round($row['ave'], 2);

		if( $ave > 0 )
		{
			$ave = '+'.$ave;
		}
		$output .= $ave;
		$output .= "</td>\n  </tr>\n";
	}

	$output .= "</table>\n";
	$roster->db->free_result($result);

	return messageboxtoggle($output, $roster->locale->act['pvplist'], 'sgray', false, $width='400px');
}
