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
	include_once('Model/device_settings_model.php');
	include_once("Control/web_users.php");
	include_once("Control/date_config.php");
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
		<script type="text/javascript">
			$sub(document).ready(function(){
				initValidation();
				getPageTips("web_settings");
				setTooltip();
			});

			function closeDialog() {
				parent.closeAddEditDialog();
			}

			function submitData() {
				if($sub("#websettings").validationEngine('validate')) {
					var old_pswd = document.getElementById("PASSWD").value;
					var new_pswd = document.getElementById("NEW_PASSWD").value;
					var conf_pswd = document.getElementById("CONF_PASSWD").value;
					var db_pswd = '<?php print $pass; ?>';
					var post_pswd = '<?php print md5($_POST['old_pwd']); ?>';

					if( old_pswd == '' && (new_pswd != conf_pswd)) {
						alert("Please enter old password");
						document.getElementById("PASSWD").focus();
						return false;
					}

					if( old_pswd != '' && (new_pswd == '' ||  conf_pswd == '')) {
						alert("Please enter new password & confirm password");
						return false;
					}

					if( new_pswd != conf_pswd ) {
						alert("Passwords are not same, Please re-enter");
						document.getElementById("NEW_PASSWD").value = '';
						document.getElementById("CONF_PASSWD").value = '';
						document.getElementById("NEW_PASSWD").focus();
						return false;
					}
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function refresh_iframe(val) {
				if( val == 1 ) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = parent.document.getElementById("container").src;
				}
			}

			function initValidation() {
				$sub("#SESSION_TIMEOUT").attr("data-validation-engine","validate[required,custom[integer],min[60],max[3600]]");
				$sub("#websettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var val = "Web settings saved successfully";
			function save_Message(val) {
				parent.closeAddEditDialog();
				parent.saveMessage(val);
			}

			var error = "Old password is incorrect";
			function error_Message(error) {
				parent.errorMessage(error);
			}
		</script>
		<?php
			$dev_settings = DeviceSettingsModel::getInstance();
			$web_settings = WebUsers::getConfig();
			$user = "admin";
			$pass = $web_settings->getUserPassword($user);
			$res = 0;
			$session_timeout = $dev_settings->get(DeviceSettingsModel::$SESSION_TIMEOUT);
			//$web_ui_port = $dev_settings->get(DeviceSettingsModel::$WEBUI_PORT);
			$obj = DateTimeConfig::getConfig();
			$update_count = $obj -> getActionListCount();

			if(isset($_POST) && isset($_POST['submit_action'])) {
				if( trim($_POST['session_timeout']) != $session_timeout ) {
					$dev_settings->set(DeviceSettingsModel::$SESSION_TIMEOUT,trim($_POST['session_timeout']));
					$_SESSION['sessTimeout'] = $_POST['session_timeout'];
					$dev_settings->set(DeviceSettingsModel::$WEBUI_PORT,trim($_POST['web_ui_port']));
					$dev_settings->createActionList(DEPLOY_MODULES::WEBUI_CONFIG, "Web UI Settings updated", DEPLOY_MODULES::WEBUI_CONFIG_STR, "");
				}
				$res = 1;
		?>
				<script type="text/javascript">
					submit = 1;
				</script>
		<?php
				$post_md5_pwd = trim(md5($_POST['old_pwd']));
				if ( $pass == $post_md5_pwd ) {
					$web_settings->setPassword($user,trim($_POST['confirm_pwd']));
		?>
					<script type="text/javascript">
						submit = 2;
					</script>
		<?php
				}
				if ( $pass != $post_md5_pwd && trim($_POST['old_pwd']) != '') {
		?>
					<script type="text/javascript">
						submit = 3;
					</script>
		<?php
				}
			}
		?>
	</head>
	<body class="popup" onload="refresh_iframe('<?php print $res;?>');">
		<div class="mainborder">
			<div class="pgtitle">Web Settings<input type="button" class="btn-close" onclick="closeDialog();" onfocus="if(this.blur)this.blur();" /></div>
			<div class="popup-content" style="height: 430px">
				<form id="websettings" class="popup-form" action="web-settings.php" onsubmit="closeDialog();" target="container" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr>
							<td>Session Timeout : </td>
							<td>
								<input type="text" id="SESSION_TIMEOUT" class="ipBox" size="4" maxlength="4" autocomplete ="off" value="<?php print $session_timeout; ?>" name="session_timeout" />
								<img id="web_settings_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<!--
						<tr>
							<td>Port Number : </td>
							<td><input type="text" class="ipBox" size="5" maxlength="5" autocomplete ="off" value="<?php print $web_ui_port; ?>" name="web_ui_port" /></td>
						</tr>
						-->
						<tr>
							<td>User Name : </td>
							<td>
								<input type="text" class="ipBox" autocomplete ="off" readonly="readonly" value="<?php print "admin"; ?>" name="user_name" />
								<img id="web_settings_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Old Admin Password : </td>
							<td>
								<input type="password" id="PASSWD" class="ipBox" size="" maxlength="128" autocomplete ="off" name="old_pwd" />
								<img id="web_settings_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>New Admin Password : </td>
							<td>
								<input type="password" id="NEW_PASSWD" class="ipBox" maxlength="128" autocomplete ="off" size="" name="new_pwd" />
								<img id="web_settings_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Confirm Admin Password : </td>
							<td>
								<input type="password" id="CONF_PASSWD" class="ipBox" size="" maxlength="128" autocomplete ="off" name="confirm_pwd" />
								<img id="web_settings_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData();" onfocus="if(this.blur)this.blur();" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog();" onfocus="if(this.blur)this.blur();" />
			</form>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		if( submit == "1" ) {
			save_Message(val);
		}
		if( submit == "2" ) {
			document.location.href = 'logout.php';
		}
		if( submit == "3" ) {
			error_Message(error);
		}
	</script>
</html>
