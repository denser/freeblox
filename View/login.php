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
	$obj = WebUsers::getConfig();
	$dev_settings = DeviceSettingsModel::getInstance();
	$session_timeout = $dev_settings->get(DeviceSettingsModel::$SESSION_TIMEOUT);

	if($_POST["UNAME"] != "'") {
		$pass = $obj -> getUserPassword($_POST["UNAME"]);
		$user_name = "admin";
		$post_md5_pwd = md5($_POST['AUTHPSWD']);
		$file = "/var/tmp/session.txt";
		$notify_user = "0";
	}

	if (isset($_POST) && isset($_POST['submit_action'])) {
		if(!isset($_SESSION)) {
			if ($_POST['submit_action'] == "1") {
				if((!file_exists($file) && ($pass == $post_md5_pwd)) && 
						$_POST['UNAME'] != "'" ) {
					session_start();
					$_SESSION['sessionId'] = session_id();
					$_SESSION['AUTH'] = "AUTH_USER";
					$_SESSION['user'] = $user_name[0];
					$_SESSION['Nonconfig'] = FALSE;
					$_SESSION['sessTimeout'] = $session_timeout;
					$_SESSION['notify_user'] = "1";
					$_SESSION['remote_ip'] = $_SERVER['REMOTE_ADDR'].PHP_EOL;
					$fh = fopen($file,"w+") or die("Can't open file");
					fwrite($fh,$_SESSION['remote_ip']);
					fwrite($fh,session_id());
					fclose($fh);
				} else if( file_exists($file) && ($pass == $post_md5_pwd)) {
?>
					<script type="text/javascript">
						document.location.href = 'login_multi_attempt.php';
					</script>
				<?php
				} else {
					header("location: https://" . $_SERVER['HTTP_HOST'] . "/index.php?error_type=1");
					exit ;
				}
			}
		}
	}
	header("location: https://" . $_SERVER['HTTP_HOST'] . "/View/");
?>
