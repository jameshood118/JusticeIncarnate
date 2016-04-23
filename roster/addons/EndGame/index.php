<?php
$versions['versionDate']['endgamelang'] = '$Date: 2007/04/25 $';
$versions['versionRev']['endgamelang'] = '$Revision: 2.0 $';
$versions['versionAuthor']['endgamelang'] = '$Author: Dave, Zxaltan $';

if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}


require_once "config.php";
require_once "auth.php";
require_once "percentBar.php";

// Load Configuration
$table = addslashes($config['EndGame_table']);
$showzone = isset($config['showzone']) ? $config['showzone'] : true;
$showinstance = isset($config['showinstance']) ? $config['showinstance'] : true;
$showhealth = isset($config['showhealth']) ? $config['showhealth'] : true;
$showitem = isset($config['showitem']) ? $config['showitem'] : true;
$showfirstkill = isset($config['showfirstkill']) ? $config['showfirstkill'] : true;
$showlastkill = isset($config['showlastkill']) ? $config['showlastkill'] : true;
$shownumkills = isset($config['shownumkills']) ? $config['shownumkills'] : true;
// End Configuration


/****  CODE SECTION - NO DISPLAY YET  ****/

$js  = '<script language="JavaScript" type="text/javascript"><!-- //hide script from antique or braindead browsers'."\n";
$js .= "\tfunction setSort(field) {\n";
$js .= "\t\tif(document.zoneform.sort.value == field.toLowerCase()) {\n";
$js .= "\t\t\tdocument.zoneform.dir.value = document.zoneform.dir.value == 'ASC' ? 'DESC' : 'ASC';\n";
$js .= "\t\t} else {\n";
$js .= "\t\t\tdocument.zoneform.sort.value = field.toLowerCase();\n";
$js .= "\t\t}\n";
$js .= "\t\tdocument.zoneform.submit();\n";
$js .= "\t}\n";
$js .= "// end hiding script --></script>\n";


// Parse query string variables
$zone = '';
if (isset($_REQUEST["zone"])) $zone=$_REQUEST["zone"];
switch(strtolower($_REQUEST['sort'])) {
    case 'zone':
	$sort = 'zone';
	break;
    case 'instance':
        	$sort = 'instance';
	break;
    case 'stat':
    case 'status':
    	$sort = 'stat';
    	break;
    case 'item':
    case 'itemtext':
    case 'itemurl':
    	$sort = 'itemtext';
    	break;
    case 'kill':
    case 'killdate':
    	$sort = 'killdate';
    	break;
    case 'last':
    case 'lastdate':
    	$sort = 'lastdate';
    	break;
    case 'numkills':
    	$sort = 'numkills';
    	break;
    case 'name':
    default:
	$sort = 'name';
}
switch(strtoupper($_REQUEST['dir'])) {
    case 'DESC':
	$sortdir = 'DESC';
	break;
    default:
	$sortdir = 'ASC';
}
switch(strtolower($_REQUEST['action'])) {
    case 'edit':
	$action = 'edit';
	break;
    case 'put':
	$action = 'put';
	break;
    case 'config':
	$action = 'config';
	break;
    case 'setconfig':
	$action = 'setconfig';
	break;
    case 'view':
    default:
	$action = 'view';
}

$selfurl = htmlentities($_SERVER['PHP_SELF'] . "?roster_addon_name=EndGame$authurl");
if ($zone != '') $selfurl .= "&amp;zone=$zone";
if ($sort != 'name') $selfurl .= "&amp;sort=$sort";
if ($sortdir == 'DESC') $selfurl .= "&amp;dir=DESC";

$all_zones = array('AQ20','AQ40','BWL','MC','NAX','WBS','ZG','ONY','GL','ML','KRZ','EYE','SSC');
$form = '';
$form .= '<form action="'.$_SERVER['PHP_SELF'].'" method="GET" name="zoneform">';
$form .= '<input type="hidden" name="roster_addon_name" value="EndGame" />';
$form .= '<input type="hidden" name="sort" value="'.htmlentities($sort).'" />';
$form .= '<input type="hidden" name="dir" value="'.htmlentities($sortdir).'" />';
$form .= '<table class="membersRow1">';
$form .= '<tr><td class="membersRow1">';
$form .= '<select name="zone" size="1" onChange="document.zoneform.submit();">';
$form .= '<option value="">[All Zones]</option>';
foreach ($all_zones as $z) {
	if ($zone == $z) {
		$is_selected = ' selected';
	} else {
		$is_selected = '';
	}
	$form .= '<option value="'.$z.'"'.$is_selected.'>'.$wordings[$z].'</option>';
}

$form .= '</select></td>';
$form .= '</tr></table></form>';


// Make a table to hold the content
$content = '<table class="membersRow1">';



// Display the header
$content .= "<tr>";
$content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('name')\">${wordings['Name']}</a></td>\n";
if($showinstance) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('instance')\">${wordings['instance']}</a></td>\n";
if($showzone) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('zone')\">${wordings['Zone']}</a></td>\n";
if($showhealth) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('status')\">${wordings['Status']}</a></td>\n";
if($showitem) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('item')\">${wordings['Item']}</a></td>\n";
if($showfirstkill) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('kill')\">${wordings['Kill']}</a></td>\n";
if($showlastkill) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('lastdate')\">${wordings['lastdate']}</a></td>\n";
if($shownumkills) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\"><a href=\"javascript:setSort('numkill')\">${wordings['numkill']}</a></td>\n";
if($adminMode) $content .= "\t<td style=\"color: #CBA300\" class=\"membersRow1\">Admin</td>\n";
$content .= "</tr>\n";

// Content varies depending on action requested
if ($action == 'put' && $adminMode) {
	//TODO: validate the input better, if invalid set action to 'edit' to drop through below
	$nm = addslashes($_REQUEST['row']);
	$st = 0 + $_REQUEST['edit_stat'];
	$iu = addslashes($_REQUEST['edit_itemurl']);
	$it = addslashes($_REQUEST['edit_itemtext']);
	$kd = strftime('%Y-%m-%d', strtotime(str_replace('-', '/', $_REQUEST['edit_killdate'])));
	$ld = strftime('%Y-%m-%d', strtotime(str_replace('-', '/', $_REQUEST['edit_lastdate'])));
	$nk = 0 + $_REQUEST['edit_numkills'];
	$query = "UPDATE `$table` SET stat=$st, itemurl='$iu', itemtext='$it', killdate='$kd', lastdate='$ld', numkills=$nk WHERE name='$nm';";
	$wowdb->query($query) or die($wowdb->error());
	switch(mysql_affected_rows()) {
	case 0:
	case -1:
		$message .= 'No records matched or database error. Edit aborted.';
		$action = 'edit';
		break;
	case 1:
		$message .= '1 row successfully modified.';
		$action = 'view';
		break;
	default:
		$message .= 'Collision detected, serious database corruption may have occurred. UPDATE affected '.mysql_affected_rows().' rows.';
		$action = 'edit';
	}
}
if($action == 'edit' && $adminMode) {
	$query = "SELECT name, instance, zone, stat, itemurl, itemtext, killdate, lastdate, numkills FROM `$table` WHERE name='".addslashes($_REQUEST['row'])."';";
	$result = $wowdb->query($query) or die($wowdb->error());
	if($wowdb->num_rows($result) < 1) {
		$message .= 'No records matched or database error. Edit aborted.';
		$action = 'view';
	} else {
		$row = $wowdb->fetch_array($result);
		$nm = empty($nm) ? $row['name'] : stripslashes($nm);
		$st = htmlentities(!isset($st) ? $row['stat'] : $st);
		$iu = htmlentities(empty($iu) ? $row['itemurl'] : stripslashes($iu));
		$it = htmlentities(empty($it) ? $row['itemtext'] : stripslashes($it));
		$kd = strftime('%m-%d-%Y', strtotime(empty($kd) ? $row['killdate'] : stripslashes($kd)));
		$ld = strftime('%m-%d-%Y', strtotime(empty($ld) ? $row['lastdate'] : stripslashes($ld)));
		$nk = htmlentities(!isset($nk) ? $row['numkills'] : $nk);
		$tmpe = $content; // Preserve existing output
		$content = ''; // New output will prepend to the existing output
		$content .= "<a href=\"$selfurl&action=config\">Customize View</a><br />\n";
		$content .= "<form action=\"${_SERVER['PHP_SELF']}\" method=\"GET\" name=\"editform\">\n";
		$content .= '<input type="hidden" name="roster_addon_name" value="EndGame" />';
		$content .= "\n<input type=\"hidden\" name=\"row\" value=\"$nm\" />\n";
		$content .= "<input type=\"hidden\" name=\"zone\" value=\"$zone\" />\n"; 
		$content .= "<input type=\"hidden\" name=\"instance\" value=\"$instance\" />\n"; 
		$content .= "<input type=\"hidden\" name=\"sort\" value=\"$sort\" />\n";
		$content .= "<input type=\"hidden\" name=\"sortdir\" value=\"$sortdir\" />\n";
		$content .= '<input type="hidden" name="action" value="put" />';
		$content .= "\n$tmpe<tr>\n"; // Restore the previously existing output here
		$content .= '<td class="membersRow1">'.htmlentities(isset($wordings[$nm]) ? $wordings[$nm] : $nm)."</td>\n";
		if($showzone) $content .= '<td class="membersRow1">'.htmlentities(isset($wordings[$row['zone']]) ? $wordings[$row['zone']] : $row['zone'])."</td>\n";
		if($showinstance) $content .= '<td class="membersRow1">'.htmlentities(isset($wordings[$row['instance']]) ? $wordings[$row['instance']] : $row['instance'])."</td>\n";
		if($showhealth) $content .= "<td class=\"membersRow1\"><input type=\"text\" name=\"edit_stat\" size=\"3\" value=\"$st\" />%</td>\n";
		if($showitem) $content .= "<td class=\"membersRow1\">URL: <input type=\"text\" name=\"edit_itemurl\" size=\"24\" value=\"$iu\" />";
		if($showitem) $content .= "Label: <input type=\"text\" name=\"edit_itemtext\" size=\"16\" value=\"$it\" /></td>\n";
		if($showfirstkill) $content .= "<td class=\"membersRow1\"><input type=\"text\" name=\"edit_killdate\" size=\"10\" value=\"$kd\" /></td>\n";
		if($showlastkill) $content .= "<td class=\"membersRow1\"><input type=\"text\" name=\"edit_lastdate\" size=\"10\" value=\"$ld\" /></td>\n";
		if($shownumkills) $content .= "<td class=\"membersRow1\"><input type=\"text\" name=\"edit_numkills\" size=\"3\" value=\"$nk\" /></td>\n";
		$content .= "<td class=\"membersRow1\"><input type=\"submit\" value=\"Submit\" /></td></tr>\n";
		$content .= "</table>\n</form>\n";
		$form = ''; // Don't show a zone selection box above an edit form
	}
} 
if($action == 'setconfig' && $adminMode) {
	foreach (array("showzone", "showinstance", "showhealth", "showitem", "showfirstkill", "showlastkill", "shownumkills") as $var) {
		if(isset($_REQUEST[$var])) $$var = $_REQUEST[$var];
		if(isset($config[$var])) $query = "UPDATE `$conf_table` SET `value` = '".$$var."' WHERE `name` = '$var'";
		else $query = "INSERT INTO `$conf_table` (`name`, `value`) VALUES ('$var', '".$$var."')";
		$wowdb->query($query) or $message .= "<br />Failed to save configuration variable $var:".htmlentities($wowdb->error()).", query was ".htmlentities($query)."\n";
	}
	$action = 'config';
}
if($action == 'config' && $adminMode) {
	$content = ''; // Clear header and re-start output
	$content .= "<form action=\"${_SERVER['PHP_SELF']}\" method=\"GET\" name=\"configform\">\n";
	$content .= '<input type="hidden" name="roster_addon_name" value="EndGame" />';
	$content .= '<input type="hidden" name="action" value="setconfig" />';
	$content .= "<table class=\"membersList\" cellspacing=\"0\" cellpadding=\"0\">\n";
	foreach (array("zone", "instance","health", "item", "firstkill", "lastkill", "numkills") as $field) {
		$fieldvar = "show" . $field;
		$content .= "<tr><td class=\"membersRow1\">Show $field in output?</td>";
		$content .= "<td><input type=\"radio\" name=\"show$field\" value=\"1\" ";
		if($$fieldvar) $content .= "checked ";
		$content .= "/>Yes <input type=\"radio\" name=\"show$field\" value=\"0\" ";
		if(!$$fieldvar) $content .= "checked ";
		$content .= "/>No </td></tr>\n";
	}
	$content .= "<td colspan=\"2\" class=\"membersRow1\"><input type=\"submit\" value=\"Submit\" /></td></tr>\n";
	$content .= "</table>\n</form>\n";
	$form = ''; // Don't show a zone selection box above an edit form
}
if($action == 'view' || !$adminMode) {
	if($adminMode) {

		$tmpv = $content;
		$content = "<a href=\"$selfurl&action=config\">Customize View</a><br />\n";
		$content .= $tmpv;
	}
	// Fetch and display the content out of the database
	$query = "SELECT name, instance, zone, stat, itemurl, itemtext, killdate, lastdate, numkills FROM `$table`";
	if(!empty($zone)) $query .= " WHERE grp='".addslashes($zone)."'";
	if(!empty($sort)) $query .= " ORDER BY $sort $sortdir;";
	$result = $wowdb->query($query) or die($wowdb->error());
	if($wowdb->num_rows($result) < 1) {
		$content .= '<tr><td colspan="7" class="membersRow1">No records returned.</td></tr>'."\n";
	} else while ($row = $wowdb->fetch_array($result)) {
		$content .= "<tr>\n";
		$nm = htmlspecialchars(isset($wordings[$row['name']]) ? $wordings[$row['name']] : $row['name']);
		$content .= "\t<td class=\"membersRow1\">$nm</td>\n";
		$zn = htmlspecialchars(isset($wordings[$row['zone']]) ? $wordings[$row['zone']] : $row['zone']);
		$is = htmlspecialchars(isset($wordings[$row['instance']]) ? $wordings[$row['instance']] : $row['instance']);
		$content .= "\t<td class=\"membersRow1\">$is</td>\n";
		if($showzone) $content .= "\t<td class=\"membersRow1\">$zn</td>\n";
		if($showhealth) {
			$content .= "\t<td class=\"membersRow1\">".percentBar((int)($row['stat']),100)."</td>\n";
		}
		if($showitem) {
			$content .= "\t<td class=\"membersRow1\">";
			if($row['itemurl'] != "") $content .= "<a href=\"".$row['itemurl']."\" target=\"_blank\">";
			if($row['itemtext'] != "") $content .= htmlspecialchars(isset($wordings[$row['itemtext']]) ? $wordings[$row['itemtext']] : $row['itemtext']);
			if($row['itemurl'] != "") $content .= "</a>";
			$content .= "</td>\n";
		}
		if($showfirstkill) $content .= "\t<td class=\"membersRow1\">".(strtotime($row['killdate']) <= strtotime("1/1/1980") ? "" : strftime('%m-%d-%Y', strtotime($row['killdate'])))."&nbsp;</td>\n";
		if($showlastkill) $content .= "\t<td class=\"membersRow1\">".(strtotime($row['lastdate']) <= strtotime("1/1/1980") ? "" : strftime('%m-%d-%Y', strtotime($row['lastdate'])))."&nbsp;</td>\n";
		if($shownumkills) $content .= "\t<td class=\"membersRow1\">".htmlspecialchars($row['numkills'])."&nbsp;</td>\n";
		if($adminMode) $content .= "\t<td><a href=\"$selfurl&action=edit&row=${row['name']}\">[Edit]</a></td>\n";
		$content .= "</tr>\n";

	}
	$wowdb->free_result($result);
	// Close the table
	$content .= "</table>\n";
}


/****  DISPLAY SECTION  ****/


// Display the Tier select Form in a stylish border
echo $js;
if(!empty($form)) {
	echo border('syellow','start','  Zone');
	echo $form;
	echo border('syellow','end');
	echo "<br />";
}

// Show logout option
if($adminMode) {
	echo $authmessage.'<br />';
}

if(isset($message)) echo "<div style=\"font-weight: bold; font-size: 12pt\">$message</div>\n";

// Display the content in a Stylish Border
echo border('syellow', 'start', "$header");
echo $content;
echo border('syellow','end');
echo "<br />";

// Admin Login
if(!$adminMode) {
	echo $authmessage;
	echo $loginbox;
}
echo "<br />";

?>