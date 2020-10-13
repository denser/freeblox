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
	include_once("Control/login_auth.php");
	include_once("Control/date_config.php");
	include_once('Model/device_settings_model.php');
	include_once("Model/shell_command.php");
	include_once('Model/sys_calls.php');

	$_SESSION['pageID'] = "#trouble";
	$obj = DateTimeConfig::getConfig();
	$result = $obj -> getDTConfig();
	$row = $result -> fetchArray();
	$update_count = $obj -> getActionListCount();
	$dev_settings = DeviceSettingsModel::getInstance();
	//$dpi_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_DPI);
	$dpi_status = SysCalls::is_dpi_up();
	//$firewall_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_FIREWALL);
	$firewall_status = SysCalls::is_firewall_up() ;
	$terminal_status = "0";
	$terminal_status = SysCalls::checkSshConnection();
	$res = 0;
	$refer = "https://". $_SERVER['HTTP_HOST'] .":";

	if(isset($_POST) && isset($_POST['dpi_action']) && $_POST['submit_button'] == 'DPI') {
		//$dev_settings->set(DeviceSettingsModel::$ENABLE_DPI,$_POST['dpi_action']);
		//SysCalls::restart_dpi();
		SysCalls::setDPI($_POST['dpi_action']);
		$res = 1;
	}
	//$dpi_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_DPI);

	if(isset($_POST) && isset($_POST['firewall_action']) && $_POST['submit_button'] == 'Firewall') {
		//$dev_settings->set(DeviceSettingsModel::$ENABLE_FIREWALL,$_POST['firewall_action']);
		SysCalls::setFirewall($_POST['firewall_action']);
		$res = 1;
	}
	//$firewall_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_FIREWALL);
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
			var $sub = $.noConflict(true);
			var siab_box = null;
			var intervalID;

			$sub(window).unload(function() {
				/*if(siab_box != null) {
					parent.dialogGenMsg("Terminal Connection Warning","Terminal connection is closed","red");
					sshTerminalDisconnect();
				}*/
			});

			function checkWindow() {
				if (siab_box && siab_box.closed) {
					window.clearInterval(intervalID);
					sshTerminalDisconnect();
				}
			}

			function submitData() {
				var formObj = document.forms[0];
				if(document.getElementById('dpi_value').innerHTML == "Disable DPI") {
					formObj.dpi_action.value = "0";
				} else {
					formObj.dpi_action.value = "1";
				}
				formObj.submit_button.value = "DPI";
				formObj.submit();
			}

			function submitData_firewall() {
				var formObj = document.forms[0];
				if(document.getElementById('firewall_value').innerHTML == "Disable Firewall") {
					formObj.firewall_action.value = "0";
				} else {
					formObj.firewall_action.value = "1";
				}
				formObj.submit_button.value = "Firewall";
				formObj.submit();
			}

			function sshTerminalAction() {
				if(document.getElementById('terminal_value').innerHTML == "Connect Terminal") {
					parent.ssh_athentication();

				} else if(document.getElementById('terminal_value').innerHTML == "Disconnect Terminal") {
					sshTerminalDisconnect();
				}
			}

			function sshTerminalConnect(val) {
				if(val != "0") {
					var refer = "<?php echo $refer; ?>";
					refer += val;
					document.getElementById("terminal_value").innerHTML = "Disconnect Terminal";
					siab_box = window.open(refer);
					intervalID = window.setInterval(checkWindow, 500);
				} else {
					parent.dialogGenMsg("Terminal Connection Error","Unable to connect terminal","red");
				}
			}

			function sshTerminalDisconnect() {
				if(siab_box != null) {
					siab_box.close();
					siab_box = null;
				}
				$sub.ajax({
					type: "POST",
					cache: false,
					data: {ssh_terminal:"1"},
					url: "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response) {
						if(response != "0") {
							document.getElementById('terminal_value').innerHTML = "Connect Terminal";
						} else {
							parent.dialogGenMsg("Terminal Connection Error","Unable to disconnect terminal","red");
						}
					}
				});
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "troubleshooting.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()" onload = "refresh_iframe('<?php print $res; ?>');">
		<div class="content">
			<h1>Troubleshooting<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="dpi_action" method="post" action="troubleshooting.php">
			<table border="0" cellpadding="0" cellspacing="0" class="table-config no-border">
				<tr>
					<td>
						<a class="btn-grey-wide" id="dpi_button" name="dpi_button"  onclick="submitData();"><span id="dpi_value"><?php if ($dpi_status == 0) echo "Enable DPI"; else echo "Disable DPI";?></span></a>
						<a class="btn-grey-wide" id="firewall_button" name="firewall_button"  onclick="submitData_firewall();"><span id="firewall_value"><?php if ($firewall_status == 0) echo "Enable Firewall"; else echo "Disable Firewall";?></span></a>
						<a class="btn-grey-wide" id="terminal_button" name="terminal_button"  onclick="sshTerminalAction();"><span id="terminal_value"><?php if ($terminal_status == 0) echo "Connect Terminal"; else echo "Disconnect Terminal";?></span></a>
					</td>
				</tr>
			</table>
		</div>
		<div>
			<input type="hidden" name="dpi_action" value="0" />
			<input type="hidden" name="firewall_action" value="0" />
			<input type="hidden" name="submit_button" value="" />
			<label style="font-size: 12px; display: none" id="system_time"></label>
			<label style="font-size: 12px; display: none" id="table_time"></label>
		</div>
		</form>
	<div id="DownloadConfiguration" class="jqmDialog"></div>
	</body>
	<?php
		include_once ("time_panel.php");
		$shell_obj = ShellCommand::getInstance();
		$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", 
					ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		if( $shell_status == 0) {
			$currentTime =trim( $shell_obj->getLastOutput());
		}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
	</script>
</html>
