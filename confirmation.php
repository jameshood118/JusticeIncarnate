<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Justice Incarnate - Confirmation Page</title>
<link rel="stylesheet" type="text/css" href="styles.css">


</head>

<body>
<p align="center"><img src="images/JIbanner.jpg" alt="Justice Incarnate" /></p>

<table width="100%">
<tr valign="top">
<td><img src="images/bdCorner.gif" /></td>
<td background="images/bdHoriz.gif"></td>
<td><img src="images/bdCorner.gif" /></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td width="100%"></td>
</tr>

<tr valign="top">
<td background="images/bdVert.gif"></td>
<td bgcolor="#301609">
	<!-- This is the menu section -->
	<table>
	<tr><td class="menuLink"><a href="http://www.jamesandjennifer.net/JusticeIncarnate/" class="menuLink">Home</td></tr>
	<tr><td class="menuLink"><a href="/forum" class="menuLink">Forum</a></td></tr>
	<tr><td class="menuLink"><a href="guildCharter.html" class="menuLink">Guild&nbsp;Charter</a></td></tr>
	<tr><td class="menuLink"><a href="reqAddons.html" class="menuLink">Required&nbsp;Addons</a></td></tr>
	<tr><td class="menuLink"><a href="roster/index.php" class="menuLink">Roster</a></td></tr>
	<tr><td class="menuLink"><a href="rosterTutorial.html" class="menuLink">Roster&nbsp;Tutorial</a></td></tr>
	<tr><td class="menuLink"><a href="lootSystem.html" class="menuLink">Loot&nbsp;System</a></td></tr>
	<tr><td class="menuLink"><a href="raidProgress.html" class="menuLink">Raid&nbsp;Progress</a></td></tr>
	<tr><td class="menuLink"><a href="calendar.html" class="menuLink">Calendar</a></td></tr>
	<tr><td class="menuLink"><a href="mailto:katirana@comcast.net" class="menuLink">Contact&nbsp;Administrator</a></td></tr>
	</table>
</td>
<td background="images/bdVert.gif"></td>
<td></td>
<td width="100%"> <!-- Page Content Section -->
	<table background= "images/tile.jpg" width="100%">
<tr>
	<td>



<form method="post" action="mailto:jameshood118@comcast.net" name="Application">
<?php 
ini_set ('display_errors', 1); // Let me learn from my mistakes.
error_reporting (E_ALL & ~ E_NOTICE); // Don't show notices.

print "Character Class: {$_POST['CharacterClass']}<BR />"; 
print "Race: {$_POST['Race']}<BR />";
print "Character Name: {$_POST['CharacterName']}<BR />";
print "Character Level: {$_POST['CharacterLevel']}<BR />";
print "Age: {$_POST['Age']}<BR />";
print "Previous Guild: {$_POST['PreviousGuild']}<BR />";
print "Raid Experience: {$_POST['raidxp']}<BR />";
print "Armory Link: {$_POST['ArmoryLink']}<BR />";
print "Information: {$_POST['Information']}<BR />";
print "Raid Time: {$_POST['raidtimes']}<BR />";
print "Raid Days: {$_POST['Raidday']}<BR />";
print "Email: {$_POST['email']}<BR />";

?>
<input type="submit" value="submit">
</form>
	</tr>
	</table>
</td>
</tr>

<tr>
<td><img src="images/bdCorner.gif" /></td>
<td background="images/bdHoriz.gif"></td>
<td><img src="images/bdCorner.gif" /></td>
<td></td>
<td></td>
</tr>	
</table>

</body>
</html>

