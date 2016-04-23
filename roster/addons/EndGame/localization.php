<?php
$versions['versionDate']['itemsetslang'] = '$Date: 2006/04/25 $'; 
$versions['versionRev']['itemsetslang'] = '$Revision: 2.0 $';  
$versions['versionAuthor']['itemsetslang'] = '$Author: Zxaltan $';

if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}

require_once $roster_conf['roster_lang'].".php";

$wordings['addoncredits']['End Game'] = array(
	array(	"name"=>	"Zxaltan",
		"info"=>	"Extra Info"),
);
?>