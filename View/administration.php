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
	include_once("Control/session_secure.php");
	include_once("Control/date_config.php");
	include_once("Control/login_auth.php");
	include_once("Model/shell_command.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/validationEngine.jquery.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/tooltip.css" />
		<script src="js/jquery.validationEngine-en.js"></script>
		<script src="js/jquery.validationEngine.js"></script>
		<script src="js/jquery.tooltip.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
		var upload_file;
		function download_db() {
			parent.downloadDb();
		}

		function factoryReset() {
			parent.window.location = "wait.php?time=2100&val=1";
		}

		function restartService() {
			$.ajax({
				type: "POST",
				cache: false,
				data: {restart_service:1},
				url:  "admin_service.html",
				success: function (html) {
					if(html == 0) {
						submit = "Service restarted sucessfully";
						save_Message(submit);
					} else {
						submit = "Unable to restart service";
						error_Message(submit);
					}
				}
			});
		}

		function rebootDevice() {
			parent.window.location = "wait.php?time=2100&val=2";
		}

		function restoreDevice() {
			$.ajax({
				type: "POST",
				cache: false,
				data: {reboot_device:1},
				url:  "admin_service.html",
				success: function (html) {
					parent.window.location = "wait.php?time=900&val=3";
				}
			});
		}

		function shutDown() {
			$.ajax({
				type: "POST",
				cache: false,
				data: {shutdown_device:1},
				url:  "admin_service.html",
				success: function (html) {
					document.location.href="logout.php";
				}
			});
		}

		function save_Message(success) {
			parent.saveMessage(success);
		}

		function error_Message(failure) {
			parent.errorMessage(failure);
		}

		function callsystemService(yes, action) {
			if(yes == 1 && action == "factoryreset") {
				factoryReset();
			}
			if(yes == 1 && action == "restart") {
				restartService();
			}
			if(yes == 1 && action == "reboot") {
				rebootDevice();
			}
			if(yes == 1 && action == "shutdown") {
				shutDown();
			}
		}

		function confirm_action(val) {
			parent.callConfirm(val);
		}
		</script>
<?php
	include_once("Model/sys_calls.php");

	$_SESSION['pageID'] = "#admin";
	$obj = DateTimeConfig::getConfig();
	$result = $obj->getDTConfig();
	$row = $result->fetchArray();
	$update_count = $obj->getActionListCount();
	if(isset($_POST['rest_apply'])) {
		$cur_dbfile = "/etc/freeblox_conf/DB/freeblox.db";
		$bk_dbfile = "/etc/freeblox_conf/DB/freeblox.db.cr";
		if (!copy($cur_dbfile, $bk_dbfile)) {
			//echo "failed to copy $file...\n";
		} else {
			//echo "copied $bk_dbfile...\n";
		}
		$tmp_name =$_FILES["restore_file"]["tmp_name"];
		if (($_FILES["restore_file"]["type"] == "application/octet-stream") && 
				($_FILES["restore_file"]["error"] <= 0 )) {
			if(!move_uploaded_file($_FILES['restore_file']['tmp_name'], 
				'/var/tmp/' . $_FILES['restore_file']['name'])) {
					die('Error uploading file - check destination is writeable.');
			} else {
				$des_path = "/var/tmp/".$_FILES['restore_file']['name'];
			}
?>
		<script type="text/javascript">
			var tmpfile = '<?php print($des_path);?>';
			parent.window.location = "wait.php?time=100&val=3&filename="+ tmpfile;
		</script>
<?php
		} else {
?>
		<script type="text/javascript">
			upload_file=1;
		</script>
<?php
			}
	}
?>
	</head>
	<body onclick="javascript: parent.hideMenu()">
		<div class="content">
			<h1>Administration<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<table cellpadding="5" cellspacing="5" class="system-settings-table">
				<tr>
					<td><input  type="submit" name="factoryreset" onClick="confirm_action('<?php print "factoryreset" ?>');" value="" class="btn-factory"/></td>
				</tr>
				<tr>
					<td><input type="submit" name="" value="" onClick="confirm_action('<?php print "restart" ?>');" class="btn-restart"/></td>
				</tr>
				<tr>
					<td><input type="submit" name="" value="" onClick="confirm_action('<?php print "reboot" ?>');" class="btn-reboot"/></td>
				</tr>
				<tr style="display:none">
					<td><input type="submit" name="" value="" onClick="confirm_action('<?php print "shutdown" ?>');"class="btn-shutdown"/></td>
				</tr>
			</table>
		<div>
			<table>
				<td>
				<table class="maintenance">
				<tr>
				<td><input type="submit" id="dbdownload" value="" onclick="download_db()" class="btn-backup"/></td>
				</td>
				</tr>
			</table>
				</td> <tr><td>&nbsp;</td></tr>
				<form enctype="multipart/form-data" name="restore_cfg" action="administration.php" method="post">
				<tr class="maintenance">
				<td>
					Select configuration file : <input type="file" name="restore_file" id="file" value=""/>
				</td>
				<td>
					<input type="submit" name="rest_apply" value="" class="btn-restore" />
				(Requires Reboot)
				</td>
				</form>
				</tr>
			</table>
				<div>
					<label style="font-size: 12px; display: none" id="system_time"></label>
					<label style="font-size: 12px; display: none" id="table_time"></label>
		</div>
		</div>
	</body>
<?php
	include_once ("time_panel.php"); 

	$shell_obj = ShellCommand::getInstance();
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if( upload_file == 1) {
			error_Message("Invalid file uploaded");
		}
	</script>
</html>
