<?php
/**
 * WoWRoster.net WoWRoster
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: search.inc.php 1281 2007-08-25 09:15:11Z Zanix $
 * @link       http://www.wowroster.net
 * @package    QuestList
 * @subpackage Search
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

class questlist_search
{
	var $options;
	var $result = array();
	var $result_count = 0;
	var $link_next;
	var $link_prev;
	var $data = array();	// Addon data

	// class constructor
	function questlist_search()
	{
		global $roster;

		$quests[0] = 'All';
		$quest_list = $roster->db->query("SELECT `zone`, `quest_name` FROM `" . $roster->db->table('quests') . "` ORDER BY `quest_name`;");
		while( list($zoneid, $questid) = $roster->db->fetch($quest_list) )
		{
			$quests[$zoneid] = $zoneid;
			$quests[$questid] = $questid;
		}
		$roster->db->free_result($quest_list);
	}

	function search( $search , $url_search , $limit=10 , $page=0 )
	{
		global $roster;

		$first = $page*$limit;

		$zone = isset($_POST['zoneid']) ? intval($_POST['zoneid']) : 0;
		$questid = isset($_POST['questid']) ? intval($_POST['questid']) : 0;

		$search_id = ($questid == 0) ? '' : "`q`.`quest_name` = '$questid' AND";

		$result = $roster->db->query("SELECT `q`.`quest_name`, `p`.`region`, `p`.`server`
			FROM `" . $roster->db->table('quests') . "` AS q
			LEFT JOIN `" . $roster->db->table('players') . "` AS p USING (`member_id`)
			WHERE `q`.`quest_name` LIKE '%$search%'
			GROUP BY `quest_name`
			LIMIT $first," . ($limit+1));
		$nrows = $roster->db->num_rows($result);

		$x = ($limit > $nrows) ? $nrows : $limit;
		if( $nrows > 0 )
		{
			while( $x > 0 )
			{
				list($quest_name, $region, $server) = $roster->db->fetch($result);

				$item['title'] = $quest_name;
				$item['image'] = 'inv_misc_note_02';

				$item['url'] = makelink('realm-questlist&amp;realm=' . $region . '-' . urlencode($server) . '&amp;questid=' . urlencode($quest_name));

				//$item['footer'] = 'this is a custom footer section great place for credits';

				$this->add_result($item);
				unset($item);
				$x--;
			}
		}

		if( $page > 0 )
		{
			$this->link_prev = '<a href="' . makelink('search&amp;page=' . ($page-1) . '&amp;search=' . $url_search . '&amp;s_addon=' . $this->data['basename']) . '"><strong>' . $roster->locale->act['search_previous_matches'] . $this->data['fullname'] . '</strong></a>';
		}
		if( $nrows > $limit )
		{
			$this->link_next = '<a href="' . makelink('search&amp;page=' . ($page+1) . '&amp;search=' . $url_search . '&amp;s_addon=' . $this->data['basename']) . '"><strong> ' . $roster->locale->act['search_next_matches'] . $this->data['fullname'] . '</strong></a>';
		}
	}

	function add_result( $resultarray )
	{
		$this->result[$this->result_count++] = $resultarray;
	}
}
