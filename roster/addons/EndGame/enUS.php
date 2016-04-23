<?PHP
$versions['versionDate']['endgamelang'] = '$Date: 2007/04/25 $';
$versions['versionRev']['endgamelang'] = '$Revision: 2.0 $';
$versions['versionAuthor']['endgamelang'] = '$Author: Zxaltan $';

if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}


include_once "percentBar.php";


// Percentbar
$wordings ['Bar'] = 'percentBar( percent , width )';

//Menu localization
$wordings['EndGame'] = 'End Game';

// header localization
$wordings['Name'] = 'Name';
$wordings['Zone'] = 'Zone';
$wordings['instance'] = 'Instance';
$wordings['Status'] = 'Health';
$wordings['Item'] = 'Item Drops';
$wordings['Kill'] = 'First Kill Date';
$wordings['lastdate'] = 'Last Kill Date';
$wordings['numkill'] = 'Times Killed'; 


//Zone localization Work in progress
$wordings['Ashenvale'] = 'Ashenvale';
$wordings['Azshara '] = 'Azshara ';
$wordings['Blackrock'] = 'Blackrock Mountain';
$wordings['Blasted'] = 'Blasted Lands';
$wordings['Deadwind '] = 'Deadwind Pass';
$wordings['Duskwood'] = 'Duskwood';
$wordings['Dustwallow'] = 'Dustwallow Marsh';
$wordings['Plaguelands'] = 'Eastern Plaguelands';
$wordings['Feralas'] = 'Feralas';
$wordings['Hinterlands'] = 'Hinterlands';
$wordings['Tanaris'] = 'Tanaris ';
$wordings['Silithus'] = 'Silithus';
$wordings['Stranglethorn'] = 'Stranglethorn Vale';
//OutLand Zone localization
$wordings['Blade'] = 'Blade\'s Edge Mountains';
$wordings['Hellfire'] = 'Hellfire Peninsula';
$wordings['Nagrand'] = 'Nagrand';
$wordings['Netherstorm'] = 'Netherstorm';
$wordings['Shadowmoon'] = 'Shadowmoon Valley';
$wordings['Zangarmarsh'] = 'Zangarmarsh';


// Instance
$wordings['AQ20'] = 'Ahn\'Qiraj 20';
$wordings['AQ40'] = 'Ahn\'Qiraj40';
$wordings['BWL'] = 'BlackWing Lair';
$wordings['MC'] = 'Molten Core';
$wordings['NAX'] = 'Naxxramas';
$wordings['WBS'] = 'World Bosses';
$wordings['ZG'] = ' Zul\'Gurub';
$wordings['AZ'] = 'Azshara';
$wordings['BL'] = 'Blasted Lands';
$wordings['DW'] = 'Duskwood';
$wordings['HL'] = 'The Hinterlands';
$wordings['FL'] = 'Feralas';
$wordings['AV'] = 'Ashenvale';
$wordings['ONY'] = 'Onyxia\'s Lair';
//OutLand Instance
$wordings['GL'] = 'Gruul\'s Lair';
$wordings['ML'] = 'Magtheridon\'s Lair';
$wordings['SSC'] = 'Serpentshrine Cavern';
$wordings['TBT'] = 'The Black Temple';
$wordings['OUT'] = 'Outland';
$wordings['KRZ'] = 'Karazhan';
$wordings['EYE'] = 'The Eye';

 
// Boss Onyxia's Lair
$wordings['Onyxia'] = 'Onyxia';
 

//Boss Names AQ20
$wordings['Ayamiss'] = 'Ayamiss the Hunter';
$wordings['Buru'] = 'Buru the Gorger';
$wordings['Rajaxx'] = 'General Rajaxx';
$wordings['Kurinnaxx'] = 'Kurinnaxx';
$wordings['Moam'] = 'Moam';
$wordings['Ossirian'] = 'Ossirian the Unscarred';
 

//Boss Names AQ40
$wordings['Sartura'] = 'Battleguard Sartura';
$wordings['Cthun'] = 'C\'thun';
$wordings['Eperors'] = 'The Twin Emperors';
$wordings['Fankriss'] = 'Fankriss the Unyielding';
$wordings['Huhuran'] = 'Princess Huhuran';
$wordings['Ouro'] = 'Ouro';
$wordings['Prophet'] = 'Prophet Skeram';
$wordings['Viscidus'] = 'Viscidus';
$wordings['Vem'] = 'Vem, Lord Kri, Princess Yauj';
 

//Boss Names BWL
$wordings['Lashlayer'] = 'Broodlord Lashlayer';
$wordings['Chromaggus'] = 'Chromaggus';
$wordings['Ebonroc'] = 'Ebonroc';
$wordings['Firemaw'] = 'Firemaw';
$wordings['Flamegor'] = 'Flamegor';
$wordings['Nefarian'] = 'Nefarian';
$wordings['Razorgore'] = 'Razorgore the Untamed';
$wordings['Vaealstrasz'] = 'Vaealstrasz the Corrupt';


//Boss Names MC
$wordings['Baron'] = 'Baron Geddon';
$wordings['Garr'] = 'Garr';
$wordings['Gehennas'] = 'Gehennas';
$wordings['Golemagg'] = 'Golemagg the Incinerator';
$wordings['Lucifron'] = 'Lucifron';
$wordings['Magmadar'] = 'Magmadar';
$wordings['Majordomo'] = 'Majordomo Excecutus';
$wordings['Ragnaros'] = 'Ragnaros';
$wordings['Shazzrah'] = 'Shazzrah';
$wordings['Sulfurion'] = 'Sulfuron Harbinger';
 

//Boss Names NAX
$wordings['Anub'] = 'Anub\'Rekhan ';
$wordings['Faerlina'] = 'Grand Widow Faerlina';
$wordings['Gluth'] = 'Gluth';
$wordings['Gothik'] = 'Gothik The Harvester';
$wordings['Grobulus'] = 'Grobbulus';
$wordings['Heigan'] = 'Heigan The Unclean';
$wordings['Four'] = 'The Four Horsemen';
$wordings['Loatheb'] = 'Loatheb';
$wordings['Maexxna'] = 'Maexxna';
$wordings['Noth'] = 'Noth The Plaguebringer';
$wordings['Patchwerk'] = 'Patchwerk';
$wordings['Razuvious'] = 'Instructor Razuvious';
$wordings['Sapphiron'] = 'Sapphiron';
$wordings['Thaddius'] = 'Thaddius';
 

//Boss Names World Bosses
$wordings['Taerar'] = 'Taerar';
$wordings['Emeriss'] = 'Emeriss';
$wordings['Ysondre'] = 'Ysondre';
$wordings['Lethon'] = 'Lethon';
$wordings['Kazzak'] = 'Lord Kazzak';
$wordings['Azuregos'] = 'Azuregos';
 

//Boss Names ZG
$wordings['Mandokir'] = 'Bloodlord Mandokir';
$wordings['Gahz'] = 'Gahz\'ranka ';
$wordings['Gri'] = 'Gri\'lek ';
$wordings['Hakkar'] = 'Hakkar';
$wordings['Hazza'] = 'Hazza\'rah ';
$wordings['Thekal'] = 'High Priest Thekal';
$wordings['Venoxis'] = 'High Priest Venoxis';
$wordings['Arlokk'] = 'High Priestess Arlokk';
$wordings['Jeklik'] = 'High Priestess Jeklik';
$wordings['Mar'] = 'High Priestess Mar\'li';
$wordings['Jin'] = 'Jin\'do the Hexxer ';
$wordings['Renatki'] = 'Renataki';
$wordings['wushooly'] = 'Wushoolay ';


//OutLand Boss Names
// Gruul's Lair
$wordings['Maulgar'] = 'High King Maulgar';
$wordings['Gruul'] = 'Gruul the Dragonslayer';
 

// Magtheridon's Lair
$wordings['Magtheridon'] = 'Magtheridon';
 

//Serpentshrine Cavern
$wordings['Hydross'] = 'Hydross the Unstable ';
$wordings['Leotheras'] = 'Leotheras the Blind';
$wordings['Karathress'] = 'Fathom-Lord Karathress';
$wordings['Morogrim'] = 'Morogrim Tidewalker';
$wordings['Vashj'] = 'Lady Vashj';
 

//The Eye
$wordings['Void Reaver'] = 'Void Reaver';
$wordings['Alar'] = 'Al\'ar';
$wordings['Solarian'] = ' High Astromancer Solarian';
$wordings['Kaelthas'] = 'Kael\'thas Sunstrider';
 

//Karazhan
$wordings['Malchezaar'] = 'Prince Malchezaar';
$wordings['Nightbane'] = 'Nightbane';
$wordings['Netherspite'] = 'Netherspite';
$wordings['Shade of Aran'] = 'Shade of Aran';
$wordings['Terestian Illhoof'] = 'Terestian Illhoof';
$wordings['The Curator'] = 'The Curator';
$wordings['Maiden of Virtue'] = 'Maiden of Virtue';
$wordings['Moroes'] = 'Moroes';
$wordings['Huntsman'] = 'Attumen the Huntsman & Midnight';

//OutLand World Bosses
$wordings['Kazzak'] = 'Doom Lord Kazzak';
$wordings['Doomwalker'] = 'Doomwalker';


//Battle Of Mt Hyjal not open yet
//$wordings[''] = '';
 

//Black Temple not open yet 
//$wordings[''] = '';


?>