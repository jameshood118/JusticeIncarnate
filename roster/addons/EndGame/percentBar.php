<?php
function percentBar($percent, $totalWidth) {
	global $roster_conf;
	if(basename($_SERVER['PHP_SELF']) == "addon.php") {
		$images = dirname($_SERVER['PHP_SELF']) . '/addons/' . $_REQUEST['roster_addon_name'];
	} else {
		$images = $roster_conf['img_url'];
	}
	$html = "\n";
	$html .= "<div style=\"width: ${totalWidth}px; border: 0; padding: 0;\">\n";
	$html .= "\t<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"$totalWidth\">\n";
	$html .= "\t\t<tr height=\"14\">\n";
	$html .= "\t\t\t<td style=\"text-align: center; background-image: url($images/health-var2.png);";
	$html .= " padding: 0; spacing: 0;\" width=\"$percent%\">";
	$html .= '<img src="$images/pixel.gif" height="14" width="1" alt="">';
	if($percent > 20) $html .= "$percent%";
	$html .= "</td>\n\t\t\t<td width=\"".(100-$percent)."%\">";
	if($percent <= 20) $html .= "$percent%";
	$html .= "</td>\n\t\t</tr>\n\t</table>\n</div>\n";
	return $html;
}
?>