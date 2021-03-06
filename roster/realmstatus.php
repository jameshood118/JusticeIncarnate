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
 * $Id: realmstatus.php 603 2007-02-14 07:37:56Z zanix $
 *
 ******************************/

// WOW Server Status
// Version 3.2
// Copyright 2005 Nick Schaffner
// http://53x11.com

// EDITED BY: http://wowroster.net for use in wowroster
// XML parsing by Swipe
// Most other changes by Zanix

// Disable generation of headers from settings.php
$no_roster_headers = true;

require_once( 'settings.php' );
require_once( ROSTER_LIB.'xmlparse.php' );


//==========[ GET FROM CONF.PHP ]====================================================

if( empty($roster_conf['realmstatus']) )
	$realmstatus = utf8_decode($roster_conf['server_name']);
else
	$realmstatus = utf8_decode($roster_conf['realmstatus']);

$realmstatus = trim($realmstatus);

if( $roster_conf['rs_mode'] )
	$generate_image = true;
elseif( !$roster_conf['rs_mode'] )
	$generate_image = false;
else
	$generate_image = true;


//==========[ OTHER SETTINGS ]=========================================================

// Minutes between status update refresh 0-60
	$timer = 10;

// Path to image folder
	$image_path = ROSTER_BASE.'img'.DIR_SEP.'realmstatus'.DIR_SEP;

// Path to font folder
	$font_path = ROSTER_BASE.'fonts'.DIR_SEP;

// URL for status page
	$url = $roster_conf['realmstatus_url'];

#--[ MYSQL CONNECT AND STORE ]=========================================================

// Read info from Database
	$querystr = "SELECT * FROM `".ROSTER_REALMSTATUSTABLE."` WHERE `server_name` = '".$wowdb->escape($realmstatus)."'";
	$sql = $wowdb->query($querystr);
	if( $sql )
	{
		$realmData = $wowdb->fetch_array($sql);
	}
	else
	{
		return;
	}

//==========[ STATUS GENERATION CODE ]=================================================

// Check timestamp, update when ready
	$current_time = date('i')*1;


if( $current_time >= ($realmData['timestamp']+$timer) || $current_time < $realmData['timestamp'] )
{
	$xml_parse =& new xmlParser();
	$err = 1;
	if( $xml_parse->parse($url) )
	{
		if( count( $xml_parse->output ) )
		{
			if( is_array($xml_parse->output[0]['child']) )
			{
				foreach( $xml_parse->output[0]['child'] as $xml_array )
				{
					$xml_server = $xml_array['attrs'];
					if ( $xml_server['N'] == $realmstatus )
					{
						$err = 0;
						switch( strtoupper( $xml_server['S'] ) )
						{
							case '0':
							case 'DOWN':
								$realmData['serverstatus'] = 'down';
								break;

							case '1':
							case 'UP':
								$realmData['serverstatus'] = 'Up';
								break;

							case '2':
							case 'MAINTENANCE':  // <-- unchecked
								$realmData['serverstatus'] = 'maintenance';
								break;

							default:
								$realmData['serverstatus'] = 'unknown';
						}
						switch( strtoupper( $xml_server['T'] ) )
						{
							case '0':
							case 'RPPVP':
							case 'RP-PVP':
								$realmData['servertype'] = $wordings[$roster_conf['roster_lang']]['rs']['RPPVP'];
								$realmData['servertypecolor'] = '535600';
								break;

							case '1':
							case 'PVE':
								$realmData['servertype'] = $wordings[$roster_conf['roster_lang']]['rs']['PVE'];
								$realmData['servertypecolor'] = '234303';
								break;

							case '2':
							case 'PVP':
								$realmData['servertype'] = $wordings[$roster_conf['roster_lang']]['rs']['PVP'];
								$realmData['servertypecolor'] = '660D02';
								break;

							case '3':
							case 'RP':
								$realmData['servertype'] = $wordings[$roster_conf['roster_lang']]['rs']['RP'];
								$realmData['servertypecolor'] = '535600';
								break;

							default:
								$realmData['servertype'] = $wordings[$roster_conf['roster_lang']]['rs']['UNKNOWN'];
								$realmData['servertypecolor'] = '860D02';
						}
						switch( strtoupper( $xml_server['L'] ) )
						{
							case '1':
							case 'LOW':
								$realmData['serverpop'] = $wordings[$roster_conf['roster_lang']]['rs']['LOW'];
								$realmData['serverpopcolor'] = '234303';
								break;

							case '2':
							case 'MEDIUM':
								$realmData['serverpop'] = $wordings[$roster_conf['roster_lang']]['rs']['MEDIUM'];
								$realmData['serverpopcolor'] = '535600';
								break;

							case '3':
							case 'HIGH':
								$realmData['serverpop'] = $wordings[$roster_conf['roster_lang']]['rs']['HIGH'];
								$realmData['serverpopcolor'] = '660D02';
								break;

							case '4':
							case 'MAX': // <-- unused?
								$realmData['serverpop'] = $wordings[$roster_conf['roster_lang']]['rs']['MAX'];
								$realmData['serverpopcolor'] = '860D02';
								break;

							default:
								$realmData['serverpop'] = $wordings[$roster_conf['roster_lang']]['rs']['ERROR'];
								$realmData['serverpopcolor'] = '860D02';
						}
					}
				}
			}
		}
	}

//==========[ WRITE INFO TO DATABASE ]=================================================

	if( !$err ) // Don't write to DB if there has been an error
	{
		$wowdb->reset_values();
		$wowdb->add_value('server_name', $realmstatus);
		$wowdb->add_value('servertype', $realmData['servertype']);
		$wowdb->add_value('servertypecolor', $realmData['servertypecolor']);
		$wowdb->add_value('serverstatus', $realmData['serverstatus']);
		$wowdb->add_value('serverpop', $realmData['serverpop']);
		$wowdb->add_value('serverpopcolor', $realmData['serverpopcolor']);
		$wowdb->add_value('timestamp', $current_time);
		if( $realmstatus == $realmData['server_name'] )
		{
			$querystr = "UPDATE `".ROSTER_REALMSTATUSTABLE."` SET ".$wowdb->assignstr." WHERE `server_name` = '".$wowdb->escape($realmstatus)."';";
		}
		else
		{
			$querystr = "TRUNCATE `".ROSTER_REALMSTATUSTABLE."`;";
			$wowdb->query($querystr);

			$querystr = "INSERT INTO `".ROSTER_REALMSTATUSTABLE."` SET ".$wowdb->assignstr.";";
			$realmData['server_name'] = $realmstatus;
		}
		// Give only debug info with text-status enabled
		// Otherwise the debug-statement will destroy the png-generation
		if ($roster_conf['sqldebug'] && !$generate_image)
		{
			print "<!-- $querystr -->\n";
		}

		$wowdb->query($querystr);
	}
}


//==========[ "NOW, WHAT TO DO NEXT?" ]================================================

// Error control
if( $realmData['serverstatus'] == 'down' || $realmData['serverstatus'] == 'maintenance' )
{
	$realmData['serverstatus'] = 'down';
	$realmData['serverpop'] = $wordings[$roster_conf['roster_lang']]['rs']['OFFLINE'];
	$realmData['serverpopcolor'] = '660D02';
}

// Check to see if data from the DB is non-existant
if( empty($realmData['serverstatus']) || empty($realmData['serverpop']) )
{
	$err = 1;
}
else
{
	$err = 0;
}


// Set to Unknown values upon error
if( $err )
{
	$realmData['serverstatus'] = 'unknown';
	$realmData['serverpop'] = $wordings[$roster_conf['roster_lang']]['rs']['NOSTATUS'];
	$realmData['serverpopcolor'] = '660D02';
}

// Generate image or text?
if( $generate_image )
{
	img_output($realmData,$err,$image_path,$font_path);
}
else
{
	echo text_output($realmData);
}

return;

//==========[ TEXT OUTPUT MODE ]=======================================================
function text_output($realmData)
{
	global $roster_conf;

	$outtext = '
<!-- Begin Realmstatus -->
<div style="width:88px;font-family:arial;font-weight:bold;">
	<div style="width:88px;height:41px;background-image:url('.$roster_conf['img_url'].'realmstatus/'.strtolower($realmData['serverstatus']).'.png);"></div>';

	if ($roster_conf['rs_display'] == 'full')
	{
		$outtext .= '
	<div style="vertical-align:middle;text-align:center;width:88px;height:54px;background-image:url('.$roster_conf['img_url'].'realmstatus/'.strtolower($realmData['serverstatus']).'2.png);">
		<div style="padding-top:7px;color:black;font-size:10px;">'.$realmData['server_name'].'</div>
		<div style="color:#'.$realmData['serverpopcolor'].';font-size:12px;">'.$realmData['serverpop'].'</div>
		<div style="color:#'.$realmData['servertypecolor'].';font-size:9px;">'.$realmData['servertype'].'</div>
	</div>';
	}
	$outtext .= '
</div>
<!-- End Realmstatus -->
';
	return $outtext;
}


//==========[ IMAGE GENERATOR ]========================================================

function img_output ($realmData,$err,$image_path,$font_path)
{
	global $roster_conf;

	$serverfont = $font_path . 'VERANDA.TTF';
	$serverfontsize = 7;

	$typefont = $font_path . 'silkscreenb.ttf';
	$typefontsize = 6;

	$serverpopfont = $font_path . 'GREY.TTF';
	$serverpopfontsize = 11;



	// Get and combine base images, set colors
	$top = @imagecreatefrompng( $image_path . strtolower($realmData['serverstatus']) . '.png' );
	if( !$top )
	{
		return;
	}

	if ($roster_conf['rs_display'] == 'full')
	{
		// Get with of the top image
		$topwidth = imagesx($top);

		// Make the bottom part
		$bottom = imagecreatefrompng( $image_path . strtolower(($err ? 'down' : $realmData['serverstatus'])) . '2.png' );

		// Create a new image
		$full = imagecreatetruecolor( $topwidth, (imagesy($top)+imagesy($bottom)) );

		// Turn off apha so we can get a clean boder to set to transparent
		imagealphablending( $full, false );

		// Set the trans color
		$col = getColor('22ff22',$full);
		imagefilledrectangle( $full, 0, 0, $topwidth, (imagesy($top)+imagesy($bottom)), $col );

		imagecopy($full,$top,0,0,0,0,$topwidth,imagesy($top));
		imagecopy($full,$bottom,0,imagesy($top),0,0,imagesx($bottom),imagesy($bottom));
		imagealphablending( $full, true );

		$popcolor = getColor($realmData['serverpopcolor'],$full);
		$typecolor = getColor($realmData['servertypecolor'],$full);
		$textcolor = getColor('000000',$full);
		$shadow = getColor('95824e',$full);

		$top = $full;


		// Ouput centered $server name
		$maxw = 62;

		$output = '';
		$box = imagettfbbox($serverfontsize,0,$serverfont,$realmData['server_name']);
		$w = abs($box[0]) + abs($box[2]);

		if ($w > $maxw)
		{
			$i = $w;
			$t = strlen($realmData['server_name']);
			while ($i > $maxw)
			{
				$t--;
				$box = imagettfbbox($serverfontsize, 0,$serverfont,substr($realmData['server_name'],0,$t));
			  	$i = abs($box[0]) + abs($box[2]);
			}
			$t = strrpos(substr($realmData['server_name'], 0, $t), ' ');
			$output[0] = substr($realmData['server_name'], 0, $t);
			$output[1] = ltrim(substr($realmData['server_name'], $t));
			$vadj = -6;
		}
		else
			$output[0] = $realmData['server_name'];

		$i = 0;
		foreach($output as $value)
		{
			$box = imagettfbbox($serverfontsize,0,$serverfont,$value);
			$w = abs($box[0]) + abs($box[2]);
			writeText($top,$serverfontsize, round(($topwidth-$w)/2), 54+($i*8)+$vadj,$textcolor,$serverfont,$value,$shadow);
			$i++;
		}

		unset($output);
		$vadj = 0;

		// Ouput centered $realmData['serverpop']
		if ($realmData['serverpop'])
		{
			$box = imagettfbbox($serverpopfontsize,0,$serverpopfont,$realmData['serverpop']);
			$w = abs($box[0]) + abs($box[2]);

			if ($w > $maxw)
			{
				$i = $w;
				$t = strlen($realmData['serverpop']);
				while ($i > $maxw)
				{
					$t--;
					$box = imagettfbbox($serverpopfontsize, 0,$serverpopfont,substr($realmData['serverpop'],0,$t));
				  	$i = abs($box[0]) + abs($box[2]);
				}
				$t = strrpos(substr($realmData['serverpop'], 0, $t), ' ');
				$output[0] = substr($realmData['serverpop'], 0, $t);
				$output[1] = ltrim(substr($realmData['serverpop'], $t));
				$vadj = -4;
			}
			else
			{
				$output[0] = $realmData['serverpop'];
			}

			$i = 0;
			foreach($output as $value)
			{
				$box = imagettfbbox($serverpopfontsize,0,$serverpopfont,$value);
				$w = abs($box[0]) + abs($box[2]);
				writeText($top,$serverpopfontsize, round(($topwidth-$w)/2), 73+($i*10)+$vadj,$popcolor,$serverpopfont,$value,$shadow);
				$i++;
			}
		}

		// Ouput centered $realmData['servertype']
		if ($realmData['servertype'] && !$err)
		{
			$box = imagettfbbox($typefontsize,0,$typefont,$realmData['servertype']);
			$w = abs($box[0]) + abs($box[2]);
			writeText($top,$typefontsize, round(($topwidth-$w)/2), 88,$typecolor,$typefont,$realmData['servertype'],$shadow);
		}
	}

	header('Content-type: image/png');

	imagealphablending( $top, false );
	imagesavealpha( $top, true );
	imagepng($top);
	imagedestroy($top);
}

// Function to set color of text
function getColor( $color,$image )
{
	$red = 100;
	$green = 100;
	$blue = 100;

	$ret = '';
	if( eregi("[#]?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})",$color,$ret) )
	{
		$red = hexdec($ret[1]);
		$green = hexdec($ret[2]);
		$blue = hexdec($ret[3]);
	}

	return imagecolorallocate( $image,$red,$green,$blue );
}

// Write Text
function writeText( $im,$fontsize,$xpos,$ypos,$color,$font,$text,$shadow_color )
{
	// Create the pseudo-shadow
	if( !empty($shadow_color) )
	{
		shadowText( $im,$fontsize,$xpos,$ypos,$font,$text,$shadow_color );
	}

	// Write the text
	@imageTTFText( $im,$fontsize,0,$xpos,$ypos,$color,$font,$text );
}

// Shadow Text
function shadowText( $im,$fontsize,$xpos,$ypos,$font,$text,$color )
{
	$_x = array( 0, 1, 1 );
	$_y = array( 1, 0, 1 );

	for( $n=0; $n<=2; $n++ )
		@imageTTFText( $im,$fontsize,0,$xpos+$_x[$n],$ypos+$_y[$n],$color,$font,$text );
}

?>