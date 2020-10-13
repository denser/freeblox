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
	include_once("Model/shell_command.php");
	include_once('Model/device_settings_model.php');
	$_SESSION['pageID'] = "#logging";
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
			var submit = 0;
		</script>
		<?php
		$dev_settings = DeviceSettingsModel::getInstance();
		$enable_remote_login = $dev_settings->get(DeviceSettingsModel::$ENABLE_REMOTE_LOGGING);
		$remote_login_server = $dev_settings->get(DeviceSettingsModel::$REMOTE_LOG_SERVER);
		$res = 0;
		$obj = DateTimeConfig::getConfig();
		$result = $obj -> getDTConfig();
		$row = $result -> fetchArray();
		$update_count = $obj -> getActionListCount();

		if(isset($_POST) && isset($_POST['submit_action'])) {
			if($_POST['enable_remote_login'] == "on")
				$enable = "1";
			else
				$enable = "";
			if( $enable_remote_login != $enable || 
				trim($_POST['log_server']) != $remote_login_server ) {
					$dev_settings->set(DeviceSettingsModel::$ENABLE_REMOTE_LOGGING,
							$enable);
					$dev_settings->set(DeviceSettingsModel::$REMOTE_LOG_SERVER,
							trim($_POST['log_server']));
					$dev_settings->createActionList(DEPLOY_MODULES::LOGGING_CONFIG,
						"Logging settings updated", 
						DEPLOY_MODULES::LOGGING_CONFIG_STR,  "");
			}
			$res = 1;
		?>
		<script type="text/javascript">
			submit = 1;
		</script>
		<?php
		}
		?>
		<script type="text/javascript">
			$sub(document).ready(function() {
				initValidation();
				getPageTips("logging");
				setTooltip();
			});

			function submitData() {
				if($sub("#logging").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "logging.php";
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "logging.php";
				}
			}

			function initValidation() {
				$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
				$sub("#logging").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var val = "Logging settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
			}

			var sysLogServer = '<?php print $remote_login_server; ?>';
			function enableRemoteLog() {
				var enable =  document.getElementById("enable_remote_login").checked;
				if( enable == false ) {
					document.getElementById("IPADDR").value = "";
					document.getElementById("IPADDR").disabled = true;
				} else {
					document.getElementById("IPADDR").value = sysLogServer;
					document.getElementById("IPADDR").disabled = false;
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<?php
				if ($update_count > 0 ) {
					include_once("notification.html");
					$class = "notify_changes";
				}
			?>
			<h1 class="<?php print $class; ?>">Logging<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="logging" method="post" action="logging.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>
					<td style="width: 40%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Logging
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="logging_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tbody>
								<tr> <td>
								<table cellpadding="2" cellspacing="2" class="sub-container-table">
								<tr> <td>
								<?php
									if($enable_remote_login == "1")
										$enable = "checked";
									else
										$enable = "";
								?>
								<label for="enable_remote_login">Remote Logging:</label>
								</td>
								<td><input type="checkbox" onclick="enableRemoteLog();" <?php print $enable; ?> id="enable_remote_login" name="enable_remote_login" />
								<img id="logging_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
								</tr>
								<tr>
									<td>Syslog server  </td>
									<td>
										<input type="text" value="<?php print $remote_login_server; ?>" id="IPADDR" size="15" maxlength="32" class="ipBox" name="log_server" />
										<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							</table>
						</td>
						</tr>
						</tbody>
					</table>
				</td>
				<td style="width: 50%; vertical-align: top;"> &nbsp; </td>
			</tr>
			</table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_logging" onclick="submitData();"><span>Save</span></a>
				<a class="btn-grey-wide-l" id="cancel" onclick="resetData();"><span>Cancel</span></a>
			</div>
			</form>
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
		enableRemoteLog();
	</script>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if( submit == 1 ) {
			save_Message(val);
		}
	</script>
</html>
