<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Justice Incarnate - Application</title>
<link rel="stylesheet" type="text/css" href="styles.css">
<script language="JavaScript" type="text/JavaScript">
	<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function emailCheck (emailStr) {
var emailPat=/^(.+)@(.+)$/
var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]"
var validChars="\[^\\s" + specialChars + "\]"
var quotedUser="(\"[^\"]*\")"
var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/
var atom=validChars + '+'
var word="(" + atom + "|" + quotedUser + ")"
var userPat=new RegExp("^" + word + "(\\." + word + ")*$")
var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$")

var matchArray=emailStr.match(emailPat)
if (matchArray==null) {
	alert("Email address seems incorrect (check @ and .'s)")
	return false
}
var user=matchArray[1]
var domain=matchArray[2]

if (user.match(userPat)==null) {
    alert("The username doesn't seem to be valid.")
    return false
}

var IPArray=domain.match(ipDomainPat)
if (IPArray!=null) {
	  for (var i=1;i<=4;i++) {
	    if (IPArray[i]>255) {
	        alert("Destination IP address is invalid!")
		return false
	    }
    }
    return true
}

var domainArray=domain.match(domainPat)
if (domainArray==null) {
	alert("The domain name doesn't seem to be valid.")
    return false
}

var atomPat=new RegExp(atom,"g")
var domArr=domain.match(atomPat)
var len=domArr.length
if (domArr[domArr.length-1].length<2 || 
    domArr[domArr.length-1].length>3) {
   alert("The address must end in a three-letter domain, or two letter country.")
   return false
}

if (len<2) {
   var errStr="This address is missing a hostname!"
   alert(errStr)
   return false
}

return true;
}
-->
</script>
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
	<tr><td class="menuLink"><a href="/forum" class="menuLink">Forums</a></td></tr>
	<tr><td class="menuLink"><a href="guildCharter.html" class="menuLink">Guild&nbsp;Charter</a></td></tr>
	<tr><td class="menuLink"><a href="reqAddons.html" class="menuLink">Required&nbsp;Addons</a></td></tr>
	<tr><td class="menuLink"><a href="roster/index.php" class="menuLink">Roster</a></td></tr>
	<tr><td class="menuLink"><a href="rosterTutorial.html" class="menuLink">Roster&nbsp;Tutorial</a></td></tr>
	<tr><td class="menuLink"><a href="lootSystem.html" class="menuLink">Loot&nbsp;System</a></td></tr>
	<tr><td class="menuLink"><a href="raidProgress.html" class="menuLink">Raid&nbsp;Progress</a></td></tr>
	<tr><td class="menuLink"><a href="mailto:katirana@comcast.net" class="menuLink">Contact&nbsp;Administrator</a></td></tr>
	</table>
</td>
<td background="images/bdVert.gif"></td>
<td></td>
<td width="100%"> <!-- Page Content Section -->
	<table background="images/tile.jpg" width="100%">
	<tr>
	<td>
<?php 

function show_form($CharacterClass="",$Race="",$CharacterName="",$CharacterLevel="",$Age="",$PreviousGuild="",$raidxp="",$ArmoryLink="",$Information="",$raidtimes="",$Raidday="",$Email="") { 

?>
<form method="post" action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>">

Character Class:  
<input type="text" size="15" name="CharacterClass" value="<?php echo $CharacterClass; ?>"> 
<label>Race</label> 
<select name="race">
	<option value="Undead/Forsaken">Undead</option>
	<option value="Orc">Orc</option>
	<option value="Troll">Troll</option>
	<option value="Tauren">Tauren</option>
	<option value="Blood Elf">Blood Elf</option>
	</select><BR /><BR />
Character Name:  <input type="text" size="40" name="CharacterName" value="<?php echo $CharacterName; ?>"> Character Level: <input type="text" size="10" name="CharacterLevel" value="<?php echo $CharacterLevel; ?>"> Your Age: <input type="text" size="4" name="Age" value="<?php echo $Age; ?>"> <BR /><BR />
Previous Guilds: <input type="text" size="40" name="PreviousGuild" value="<?php echo $PreviousGuild; ?>">
Previous Raiding Experience:
	<select name="raidxp">
		<option value="none">None</option>
		<option value="Some Old World">Some Old World</option>
		<option value="Some TBC">Some TBC</option>
		<option value="Extensive">Extensive</option>
		</select>
	  <BR /><BR />
Armory Link: <input type="text" size="40" name="ArmoryLink" value="<?php echo $ArmoryLink; ?>"><BR /><BR />
Information: 
<textarea cols="50" rows="5" name="Information" value="<?php echo $Information; ?>"></textarea>
<BR />
<BR />
Preferred Raiding Times: 
<select name="raidtimes">
	<option value="Morning">Morning</option>
	<option value="Afternoon">Afternoon</option>
	<option value="Evening">Evening</option>
	<option value="Late Nights">Late Nights</option>
	</select><BR /><BR />
Preferred Raiding Days: <BR />
<label><input type="checkbox" name="Raidday" value="Monday" id="Raidday1" />Monday</label>
<label><input type="checkbox" name="Raidday" value="Tuesday" id="Raidday2" />Tuesday</label>
<label><input type="checkbox" name="Raidday" value="Wednesday" id="Raidday3" />Wednesday</label>
<label><input type="checkbox" name="Raidday" value="Thursday" id="Raidday4" />Thursday</label>
<label><input type="checkbox" name="Raidday" value="Friday" id="Raidday5" />Friday</label>
<label><input type="checkbox" name="Raidday" value="Saturday" id="Raidday6" />Saturday</label>
<label><input type="checkbox" name="Raidday" value="Sunday" id="Raidday7" />Sunday</label>

<BR /><BR />

<form name="emailform" onSubmit="return emailCheck(this.email.value);">
Your Email Address:  <input type=text name="email"> <input type=submit value="Validate">
</form>
	<input type="submit" name="confirm" value="Confirmation" onClick="MM_openBrWindow('thanks.html','','width=520,height=500');MM_validateForm('name','','R');return document.MM_returnValue" >
	</p>
	<p>Reset this <a href="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>">form</a>
	</form>	
	<?php 
// end of show_form function

if($HTTP_SERVER_VARS['REQUEST_METHOD']!='POST') {

  show_form();

  } else {

  if ((empty($HTTP_POST_VARS['CharacterClass'])) || (empty($HTTP_POST_VARS['Race']))) {
  
    echo "<p>You did not fill in all the fields, please try again!</p>\n";
  
    show_form($HTTP_POST_VARS['first'],$HTTP_POST_VARS['last'],$HTTP_POST_VARS['interest']);
  
    } else {
    
	echo "<p>Thank you, $HTTP_POST_VARS[CharacterName], you entered ";
    
	echo join(' and ', $HTTP_POST_VARS['email']);
	
	echo " as your email.</p>\n";
	
	}
  }

?>
</td>
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