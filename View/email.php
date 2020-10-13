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
	include_once('Model/device_settings_model.php');
	include_once('Model/sys_calls.php');
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#email_server_settings";
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
			$email_server = $dev_settings->get(DeviceSettingsModel::$EMAIL_SERVER);
			$email_server_port = $dev_settings->get(DeviceSettingsModel::$EMAIL_SERVER_PORT);
			$sender_email_id = $dev_settings->get(DeviceSettingsModel::$EMAIL_ID);
			$receiver_email_id = $dev_settings->get(DeviceSettingsModel::$RECEIVER_EMAIL_ID);
			$authentication = $dev_settings->get(DeviceSettingsModel::$AUTHENTICATION);
			$user_name = $dev_settings->get(DeviceSettingsModel::$UNAME);
			$db_password = $dev_settings->get(DeviceSettingsModel::$PASSWORD);
			$password = base64_decode($db_password);
			$res = 0;
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();

			if(isset($_POST) && isset($_POST['submit_action'])) {
				$post_pswd = base64_encode(trim($_POST['passwd']));
				if ( trim($_POST['email_server']) != $email_server || 
					trim($_POST['email_server_port']) != $email_server_port ||
					trim($_POST['sender_email_id']) != $sender_email_id || 
					trim($_POST['receiver_email_id']) != $receiver_email_id ||
					$_POST['authentication'] != $authentication || 
					trim($_POST['uname']) !=  $user_name ||
					trim($_POST['passwd']) != $password ) {
						$dev_settings->set(DeviceSettingsModel::$EMAIL_SERVER,trim($_POST['email_server']));
						$dev_settings->set(DeviceSettingsModel::$EMAIL_SERVER_PORT,trim($_POST['email_server_port']));
						$dev_settings->set(DeviceSettingsModel::$EMAIL_ID,trim($_POST['sender_email_id']));
						$dev_settings->set(DeviceSettingsModel::$RECEIVER_EMAIL_ID,trim($_POST['receiver_email_id']));
						$dev_settings->set(DeviceSettingsModel::$AUTHENTICATION,$_POST['authentication']);
						$dev_settings->set(DeviceSettingsModel::$UNAME,trim($_POST['uname']));
						$dev_settings->set(DeviceSettingsModel::$PASSWORD,$post_pswd);
						$dev_settings->createActionList(
								DEPLOY_MODULES::EMAIL_CONFIG_STR,
								"Email settings updated", 
								DEPLOY_MODULES::EMAIL_CONFIG_STR,  "");
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
				getPageTips("email_ttip");
				setTooltip();
			});

			function submitData() {
				if($sub("#email").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "email.php";
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "email.php";
				}
			}

			function initValidation() {
				$sub("#EMAILIP").attr("data-validation-engine","validate[required,custom[IP_DOMAIN]]");
				$sub("#EMAILIP_PORT").attr("data-validation-engine","validate[required,custom[integer],min[22],max[65535]]");
				$sub("#SENDER_EMAIL_ID").attr("data-validation-engine","validate[required,custom[email]]");
				$sub("#RECEIVER_EMAIL_ID").attr("data-validation-engine","validate[required,custom[email]]");
				$sub("#UNAME").attr("data-validation-engine","validate[required,custom[onlyLetterNumberDotHyphenUnderScore],minSize[5],maxSize[64]]");
				$sub("#PASSWD").attr("data-validation-engine","validate[required,custom[onlyLetterNumber],minSize[5],maxSize[64]]");
				$sub("#email").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var val = "E-mail Server settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
			}

			function setAuthentication() {
				var auth_type =  document.getElementById("authentication").value;
				if ( auth_type == "Auth_plain" ) {
					document.getElementById("UNAME").disabled = true;
					document.getElementById("PASSWD").disabled = true;
				}
				if ( auth_type == "Auth_login" ) {
					document.getElementById("UNAME").disabled = false;
					document.getElementById("PASSWD").disabled = false;
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<h1>E-mail Server<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="email" method="post" action="email.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>

					<td style="width: 40%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									E-mail Notification Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="email_ttip_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
					<tbody>
						<tr>
							<td>
								<table cellpadding="2" cellspacing="2" class="sub-container-table">
									<tr>
										<td>Server IP / Port</td>
										<td colspan="2">
											<table>
											<tr>
											<td>
												<input type="text" value="<?php print $email_server; ?>" id="EMAILIP" size="32" maxlength="" class="ipBox" name="email_server" />
												<img id="email_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
											<td>
												<input type="text" value="<?php print $email_server_port; ?>" id="EMAILIP_PORT" size="6" maxlength="5" class="ipBox" name="email_server_port" />
												<img id="email_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
											</tr>
								</table>

							</td>
						</tr>
						<tr>
							<td>Sender E-mail ID</td>
							<td>
								<input type="text" value="<?php print $sender_email_id; ?>" id="SENDER_EMAIL_ID" size="32" maxlength="254" class="ipBox" name="sender_email_id" />
								<img id="email_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Receiver E-mail ID</td>
							<td>
								<input type="text" value="<?php print $receiver_email_id; ?>" id="RECEIVER_EMAIL_ID" size="32" maxlength="254" class="ipBox" name="receiver_email_id" />
								<img id="email_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Authentication</td>
							<td>
								<select class="ipDrpBox" name="authentication" id="authentication" onchange="setAuthentication();" >
									<option value='Auth_plain' <?php if($authentication == "Auth_plain") print "selected = true" ?>>Auth_plain</option>
									<option value='Auth_login' <?php if($authentication == "Auth_login") print "selected = true" ?>>Auth_login</option>
								</select>
								<img id="email_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Username</td>
							<td>
								<input type="text" value="<?php print $user_name; ?>" id="UNAME" size="" maxlength="256" class="ipBox" name="uname" />
								<img id="email_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Password</td>
							<td>
								<input type="password" value="<?php print $password; ?>" id="PASSWD" size="" maxlength="256" class="ipBox" name="passwd" />
								<img id="email_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					</td>
					</tr>
					</tbody>
				</table>
				</td>
				<td style="width: 40%; vertical-align: top;"> &nbsp; </td>
			</tr>
			</table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_email" onclick="submitData();"><span>Save</span></a>
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
		setAuthentication();
	</script>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if( submit == 1 ) {
			save_Message(val);
		}
	</script>
</html>
