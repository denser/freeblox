<?php
/*
  Copyright (C) 2015-2018 www.allo.com - All Rights Reserved.
  FreeBlox is the Freeware GUI Management Tool for Blox [http://www.blox.org]
  You are granted a non-exclusive License to use the FreeBlox software for
  any purposes for an unlimited period of time. The software product under
  this License is provided free of charge.
  Even though a license fee is not paid for the use of Freeware Version
  software, it does not mean that there are no conditions for using such
  software:

   1. The Software may be installed and used by the Licensee for any legal
      purpose.

   2. The Software may be installed and used by the Licensee on any number
      of systems.

   3. The Software can be copied and distributed under the condition that
      original copyright notice and disclaimer of warranty will stay intact,
      and the Licensee will not charge money or fees for the Software
      product, except to cover distribution costs.

   4. The Licensee will not have any proprietary rights in and to the
      Software. The Licensee acknowledges and agrees that the Licensor retains
      all copyrights and other proprietary rights in and to the Software.

   5. Use within the scope of this License is free of charge and no royalty
      or licensing fees shall be paid by the Licensee.
*/

	include_once("Control/web_users.php");
	include_once("Model/device_settings_model.php");
	include_once("Model/sys_calls.php");
	include_once("Model/shell_command.php");

	$shell_obj = ShellCommand::getInstance();
	$obj = WebUsers::getConfig();
	$dev_settings = DeviceSettingsModel::getInstance();
	$session_timeout = $dev_settings->get(DeviceSettingsModel::$SESSION_TIMEOUT);
	$user_name = "admin";
	$file = '/var/tmp/session.txt';
	$fh=fopen($file, 'r');
	$line=fgets($fh, 1024);
	$temp = explode("\n",$line);
	$cur_ip = $temp[0];

	if(isset($_POST) && isset($_POST['config'])) {
		if(!isset($_SESSION)) {
			$shell_status = $shell_obj->Execute("grep -ln \"Nonconfig|b:0\" /tmp/sess_*", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$cur_session =trim( $shell_obj->getLastOutput());
			}
			if($cur_session != NULL) {
				$shell_status = $shell_obj->Execute("rm -f $cur_session",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			}
			session_start();
			$_SESSION['sessionId'] = session_id();
			$_SESSION['AUTH'] = "AUTH_USER";
			$_SESSION['user'] = $user_name;
			$_SESSION['Nonconfig'] = FALSE;
			$_SESSION['sessTimeout'] = $session_timeout;
			$_SESSION['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['notify_user'] = "1";
			$fh = fopen($file,"w") or die("Can't open file");
			$remote_ip = $_SERVER['REMOTE_ADDR'].PHP_EOL;
			fwrite($fh,$remote_ip);
			fwrite($fh,session_id());
			fclose($fh);
			header("location: https://" . $_SERVER['HTTP_HOST'] . "/View/");
		}
	} elseif(isset($_POST) && isset($_POST['cancel'])) {
		session_start();
		session_destroy();
		header("location:login.php");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<title>:: <?php print SysCalls::getFullProductName(); ?> ::</title>
	</head>
	<body>
		<div style="0 0 0 0px">
		<table style="width: 100%; height: 70px;" cellpadding="0" cellspacing="0">
			<tr>
				<td class="page-header"><div class="main-logo"></div>
				</td>
			</tr>
		</table>
			<table style="width: 100%; height: 480px" cellpadding="0" cellspacing="0">
				<tr>
				<td valign="top">
				<table style="width: 100%; height: 480px" cellpadding="0" cellspacing="0">
					<tr>
						<td id="main-menu" style="width: 15%;" valign="top">&nbsp;</td>

						<td style="width: 85%;" valign="top" class="multi-login-main-container">
						<div class="multi-login-text-container">
						<?php if( $cur_ip == $_SERVER['REMOTE_ADDR'] ) { ?>
								The previous session logged in from the host<span style="color: #F57714;font-weight: bold;"> <?php print $cur_ip; ?></span>.
							was not closed properly.
							Click "Continue" to close the previous session and start a new session.
							Click "Not Now" to cancel your login attempt.
						 <?php } else { ?>
							An administrator is already logged in from the host<span style="color: #F57714;font-weight: bold;"> <?php print $cur_ip; ?></span>.
							If you continue to log in the Configuration Management UI that administrator's
							session will be dropped. Currently you are trying to login
							as administrator from <span style="color: #F57714;font-weight: bold;"><?php print $_SERVER['REMOTE_ADDR']; ?></span>
							Click "Continue" to preempt that user and continue to log in.
							Click "Not Now" to cancel your login attempt.
						<?php } ?>
						</div>
						<div class="multi-login-button-container">
									<form action="login_multi_attempt.php" method="post">
									<input type="submit" name="config" class="btn-grey-input" value="Continue" />
									<input type="submit" name="cancel" class="btn-grey-input" value="Not Now" />
								</form>
						</div>
						</td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td align="center" style="position: fixed; bottom: 0px; border-top: 1px solid #cccccc; width: 100%; background-color: #eeeeee; height: 30px">
				<div style="margin-top: 5px; color: #555; font-size: 11px">
					Copyright &copy; 2015-2018. <?php print SysCalls::getFullProductName(); ?> - Web Panel. All Rights Reserved.
				</div></td>
			</tr>
			</table>
		</div>
	</body>
</html>
