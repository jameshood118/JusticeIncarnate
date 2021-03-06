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
 * $Id: motd.php 603 2007-02-14 07:37:56Z zanix $
 *
 ******************************/

//==========[ SETTINGS ]========================================================

$roster_root_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;

if( isset($_GET['motd']) )
{
	$guildMOTD = substr(stripslashes(urldecode($_GET['motd'])),0,145);
}
else
{
	include( $roster_root_path . 'settings.php' );
	$guildMOTD = $wowdb->get_guild_info($roster_conf['server_name'],$roster_conf['guild_name']);
	$guildMOTD = substr(htmlspecialchars($guildMOTD['guild_motd']),0,145);
}


// Path to font folder
$image_path = $roster_root_path . 'img' . DIRECTORY_SEPARATOR;
$font_path = $roster_root_path . 'fonts' . DIRECTORY_SEPARATOR;


motd_img($guildMOTD,$image_path,$font_path);
die();


//==========[ IMAGE GENERATOR ]=================================================

function motd_img( $guildMOTD,$image_path,$font_path )
{
	$guildMOTD = html_entity_decode($guildMOTD);

	// Set ttf font
	$visitor = $font_path . 'VERANDA.TTF';

	// Get sizes of text
	$dimensions = imagettfbbox( 11, 0, $visitor, $guildMOTD );
	$text_length = $dimensions[2] - $dimensions[6];

	// Get how many times to print center
	$image_size = ceil($text_length/198);
	$final_size = 54 + ($image_size*198);
	$text_loc = ($final_size/2) - ($dimensions[2]/2);

	// Create new image
	$img = imagecreatetruecolor( $final_size,38 );

	// Get and combine base images, set colors
	$img_file = imagecreatefrompng($image_path . 'gmotd.png');

	// Copy image file into new image
	// Copy Left part
	imagecopy( $img, $img_file, 0, 0, 0, 0, 38, 38 );

	// Copy center part however times needed
	for( $i=0;$i<$image_size;$i++ )
	{
		imagecopy( $img, $img_file, ($i*198)+38, 0, 39, 0, 198, 38 );
	}
	// Copy Right part
	imagecopy( $img, $img_file, ($image_size*198)+38, 0, 237, 0, 17, 38 );

	$textcolor = imagecolorallocate( $img, 255, 255, 255 );

	imagettftext( $img, 11, 0, $text_loc, 23, $textcolor, $visitor, $guildMOTD );

	header('Content-type: image/png');
	imagepng($img);
	imagedestroy($img);
}
?>