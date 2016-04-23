<?php
if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}

$authpwd = $roster_conf['roster_upd_pw'];

// ----[ Build the password box ]---------------------------
$loginbox = '
<!-- Begin Password Input Box -->
<form name="authform" action="'.htmlentities( $_SERVER['REQUEST_URI']).'" method="POST" enctype="multipart/form-data" onsubmit="submitonce(this)">
'.border('sred','start','Admin Login').'
<table class="bodyline" cellspacing="0" cellpadding="0">
	<tr>
		<td class="membersRowRight1">Password:<br />
			<input name="pass_word" type="password" size="30" maxlength="30" />
		</td>
	</tr>
	<tr>
		<td class="membersRowRight2" valign="bottom">
			<div align="right"><input type="submit" value="Login" /></div>
		</td>
	</tr>
</table>
'.border('sred','end').'
</form>
<!-- End Password Input Box -->';


// ----[ Check log-in ]-------------------------------------
if( isset( $_REQUEST['logout'] ) )
{
	if( isset($_COOKIE['roster_pass']) )
	setcookie( 'roster_pass','',time()-86400 );
	if( isset($_COOKIE['roster_conf_tab']) )
	setcookie( 'roster_conf_tab','',time()-86400 );
	$authmessage = '<span style="font-size:11px;color:red;">Logged Out</span><br />';
}
else
{
	if(!isset($_COOKIE['roster_pass'])) {
		if(isset($_POST['pass_word'])) {
			if(md5($_POST['pass_word']) == $authpwd)
			{
				setcookie( 'roster_pass',$authpwd );
				$authmessage = '<span style="font-size:10px;color:red;">Logged in:</span><span style="font-size:10px;color:#FFFFFF"> [<a href="'.$_SERVER['REQUEST_URI'].'&logout=logout">Logout</a>]</span><br />';
				$adminMode = 1;
			}
			else
			{
				$authmessage = '<span style="font-size:11px;color:red;">Wrong password</span><br />';
			}
		}
	}
	else
	{
		$BigCookie = $_COOKIE['roster_pass'];

		if( $BigCookie == $authpwd )
		{
			$authmessage = '<span style="font-size:10px;color:red;">Logged in:</span><span style="font-size:10px;color:#FFFFFF"> [<a href="'.$_SERVER['REQUEST_URI'].'&logout=logout">Logout</a>]</span><br />';
			$adminMode = 1;
		}
		else
		{
			setcookie( 'roster_pass','',time()-86400 );
		}
	}
}

?>