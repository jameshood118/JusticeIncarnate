<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Displays the guild information text
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: index.php 1241 2007-08-16 06:06:25Z Zanix $
 * @link       http://www.wowroster.net
 * @package    GuildInfo
*/

if( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

$roster->output['title'] = $roster->locale->act['guildinfo'];

$guild_info_text = empty($roster->data['guild_info_text']) ? '&nbsp;' : $roster->data['guild_info_text'];

print messagebox('<div class="GuildInfoText">' . nl2br($guild_info_text) . '</div>',$roster->locale->act['guildinfo'],'syellow');

