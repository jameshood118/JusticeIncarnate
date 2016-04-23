<?php
/******************************
 * worldofwarcraftguilds.com
 * Copyright 2006
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
 * $Id: localization.php 1 2006-06-27 21:11:52Z nightfighter $
 *
 ******************************/
error_reporting(E_ALL);

if (eregi("index.php",$_SERVER['PHP_SELF'])) {
    die("You can't access this file directly!");
}

$header_title = $wordings[$roster_conf['roster_lang']]['guildstats'];

require ($addonDir.'gstats.php');

echo $content;
?>