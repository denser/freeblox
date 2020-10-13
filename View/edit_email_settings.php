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
	include_once("Model/device_access_model.php");
	include_once("Control/date_config.php");
	include_once('Model/device_settings_model.php');
	include_once('Model/sys_calls.php');

	$dev_settings = DeviceSettingsModel::getInstance();
	$get_data = $_GET['id'];
	$get_array_data = explode("?", $get_data, 2);
	$pageID = $get_array_data[0];

	if ($pageID == "signal_logs") {
		$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SIGNAL_LOGS);
		$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_SIGNAL_LOGS);
		$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SIGNAL_LOGS);
	}

	if ($pageID == "media_logs") {
		$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_MEDIA_LOGS);
		$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_MEDIA_LOGS);
		$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_MEDIA_LOGS);
	}

	if ($pageID == "lcr_logs") {
		$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_LCR_LOGS);
		$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_LCR_LOGS);
		$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_LCR_LOGS);
	}

	if ($pageID == "system_logs") {
		$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SYSTEM_LOGS);
		$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_SYSTEM_LOGS);
		$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SYSTEM_LOGS);
	}

	if ($pageID == "security_logs") {
		$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SECURITY_LOGS);
		$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_SECURITY_LOGS);
		$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SECURITY_LOGS);
	}

	$res = 0;
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj -> getActionListCount();
	$res = 0;
	$page_title = "Edit E-mail Server Settings";
	$createdDataNames = array();

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ($_POST['submit_action'] == "signal_logs") {
			$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SIGNAL_LOGS);
			$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_SIGNAL_LOGS);
			$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SIGNAL_LOGS);

			if($_POST['enable_email_server'] == "on")
				$enable_signal = "1";
			else
				$enable_signal = "0";

			if ( $enable_email_server != $enable_signal || 
				$email_frequency != trim($_POST['email_freq']) || 
				$_POST['email_freq_type'] != $email_frequency_type ) {
					$dev_settings->set(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SIGNAL_LOGS,$enable_signal);
					$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_SIGNAL_LOGS,trim($_POST['email_freq']));
					$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SIGNAL_LOGS,trim($_POST['email_freq_type']));
					$dev_settings->createActionList(DEPLOY_MODULES::EMAIL_CONFIG,"Email settings for Signal Logs updated", DEPLOY_MODULES::EMAIL_CONFIG_STR,  "");
				}
				$res = 1;
		}
		if ($_POST['submit_action'] == "media_logs") {
			$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_MEDIA_LOGS);
			$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_MEDIA_LOGS);
			$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_MEDIA_LOGS);

			if($_POST['enable_email_server'] == "on")
				$enable_media = "1";
			else
				$enable_media = "0";
			if( $enable_email_server != $enable_media || 
				$email_frequency != trim($_POST['email_freq']) || 
				$_POST['email_freq_type'] != $email_frequency_type ) {
					$dev_settings->set(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_MEDIA_LOGS,$enable_media);
					$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_MEDIA_LOGS,trim($_POST['email_freq']));
					$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_MEDIA_LOGS,trim($_POST['email_freq_type']));
					$dev_settings->createActionList(DEPLOY_MODULES::EMAIL_CONFIG,"Email settings for Media Logs updated", DEPLOY_MODULES::EMAIL_CONFIG_STR,  "");
			}
			$res = 1;
		}
		if ($_POST['submit_action'] == "lcr_logs") {
			$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_LCR_LOGS);
			$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_LCR_LOGS);
			$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_LCR_LOGS);
			if($_POST['enable_email_server'] == "on")
				$enable_lcr = "1";
			else
				$enable_lcr = "0";
				if( $enable_email_server != $enable_lcr || 
					$email_frequency != trim($_POST['email_freq']) || 
					$_POST['email_freq_type'] != $email_frequency_type ) {
						$dev_settings->set(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_LCR_LOGS,$enable_lcr);
						$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_LCR_LOGS,trim($_POST['email_freq']));
						$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_LCR_LOGS,trim($_POST['email_freq_type']));
						$dev_settings->createActionList(DEPLOY_MODULES::EMAIL_CONFIG,"Email settings for LCR Logs updated", DEPLOY_MODULES::EMAIL_CONFIG_STR,  "");
				}
				$res = 1;
		}

		if ($_POST['submit_action'] == "system_logs") {
			$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SYSTEM_LOGS);
			$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_SYSTEM_LOGS);
			$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SYSTEM_LOGS);
			if($_POST['enable_email_server'] == "on")
				$enable_system = "1";
			else
				$enable_system = "0";
				if( $enable_email_server != $enable_system || 
					$email_frequency != trim($_POST['email_freq']) || 
					$_POST['email_freq_type'] != $email_frequency_type ) {
						$dev_settings->set(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SYSTEM_LOGS,$enable_system);
						$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_SYSTEM_LOGS,trim($_POST['email_freq']));
						$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SYSTEM_LOGS,trim($_POST['email_freq_type']));
						$dev_settings->createActionList(DEPLOY_MODULES::EMAIL_CONFIG,"Email settings for System Logs updated", DEPLOY_MODULES::EMAIL_CONFIG_STR,  "");
				}
				$res = 1;
		}
		if ($_POST['submit_action'] == "security_logs") {
			$enable_email_server = $dev_settings->get(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SECURITY_LOGS);
			$email_frequency = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_SECURITY_LOGS);
			$email_frequency_type = $dev_settings->get(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SECURITY_LOGS);
			if($_POST['enable_email_server'] == "on")
				$enable_security = "1";
			else
				$enable_security = "0";
				if( $enable_email_server != $enable_security || 
					$email_frequency != trim($_POST['email_freq']) || 
					$_POST['email_freq_type'] != $email_frequency_type ) {
						$dev_settings->set(DeviceSettingsModel::$ENABLE_EMAIL_SERVER_SECURITY_LOGS,$enable_security);
						$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_SECURITY_LOGS,trim($_POST['email_freq']));
						$dev_settings->set(DeviceSettingsModel::$EMAIL_FREQUENCY_TYPE_SECURITY_LOGS,trim($_POST['email_freq_type']));
						$dev_settings->createActionList(DEPLOY_MODULES::EMAIL_CONFIG,"Email settings for Security Logs updated", DEPLOY_MODULES::EMAIL_CONFIG_STR,  "");
				}
				$res = 1;
		}
	}
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
		</script>
		<script type="text/javascript">
			var flag = "<?php echo $flag ?>";
			$sub(document).ready(function(){
				initValidation();
				getPageTips("email");
				setTooltip();
			});

			function initValidation() {
				$sub("#EMAIL_FREQ").attr("data-validation-engine","validate[required,custom[integer],min[1],max[365]]");
				$sub("#emailsettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var pageID = '<?php print $pageID; ?>';
			function submitData() {
				if($sub("#emailsettings").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit_action.value = pageID;
					formObj.submit();
					return true;
				}
			}

			function closeDialog() {
				parent.closeAddEditDialog();
			}

			function f1(val) {
				if(val == 1) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = parent.document.getElementById("container").src;
				}
			}

			var val = "E-mail Server settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
			}

			function enableEmailServer() {
				var enable_email =  document.getElementById("enable_email_server").checked;
				if( enable_email == false ) {
					document.getElementById("EMAIL_FREQ").disabled = true;
					document.getElementById("email_freq_type").disabled = true;
				} else {
					document.getElementById("EMAIL_FREQ").disabled = false;
					document.getElementById("email_freq_type").disabled = false;
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="emailsettings" name="emailsettings" class="popup-form" action="edit_email_settings.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr>
							<td>
						<?php
							if($enable_email_server == "1")
								$enable = "checked";
							else
								$enable = "";
						?>
							<label for="enable_email_server">Enable E-mail Notification:</label>
							</td>
							<td><input type="checkbox" onclick="enableEmailServer();" <?php print $enable; ?> id="enable_email_server" name="enable_email_server" /></td>
						</tr>
						<tr>
							<td>Notify once in every</td>
							<td>
								<table>
									<tr>
										<td>
											<input type="text" value="<?php print $email_frequency; ?>" id="EMAIL_FREQ" size="3" maxlength="3" class="ipBox" name="email_freq" />
											<img id="email_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										<td>
											<select name="email_freq_type" id="email_freq_type" class="ipDrpBox">
												<option value="hour" <?php if( $email_frequency_type == "hour") print "selected = true" ?> >Hour</option>
												<option value="day" <?php if( $email_frequency_type == "day") print "selected = true" ?>>Day</option>
												<option value="week" <?php if( $email_frequency_type == "week") print "selected = true" ?>>Week</option>
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
				</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="WHITELIST_RULE_LIST" id="WHITELIST_RULE_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
	<script type="text/javascript">
		initValidation();
		enableEmailServer();
		setAuthentication();
	</script>
</html>
