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
	include_once('Model/radius_settings_model.php');
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#log";
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
			$radius_settings = RadiusSettingsModel::getInstance();
			$enable = $radius_settings->get(RadiusSettingsModel::$ENABLE);
			$server_ip = $radius_settings->get(RadiusSettingsModel::$SERVER_IP);
			$server_port = $radius_settings->get(RadiusSettingsModel::$SERVER_PORT);
			$res = 0;
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();
			if(isset($_POST) && isset($_POST['submit_action'])) {
				if($_POST['enable_radius'] == "on")
					$post_enable = "1";
				else
					$post_enable = "";
				if( $enable != $post_enable || trim($_POST['server_ip']) != $server_ip || trim($_POST['server_port']) != $server_port ) {
					$radius_settings->set(RadiusSettingsModel::$ENABLE,$post_enable);
					$radius_settings->set(RadiusSettingsModel::$SERVER_IP,trim($_POST['server_ip']));
					$radius_settings->set(RadiusSettingsModel::$SERVER_PORT,trim($_POST['server_port']));
					$radius_settings->createActionList(
							DEPLOY_MODULES::RADIUS_SETTINGS_CONFIG,
							"Radius settings updated", 
							DEPLOY_MODULES::RADIUS_SETTINGS_CONFIG_STR,  "");
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
				getPageTips("radius");
				setTooltip();
			});

			function submitData() {
				if($sub("#radius").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "radius.php";
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "radius.php";
				}
			}

			function initValidation() {
				$sub("#SERVERIP").attr("data-validation-engine","validate[required,custom[ipv4]]");
				$sub("#SERVERPORT").attr("data-validation-engine","validate[required,custom[integer],min[1025],max[65553]]");

				$sub("#radius").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var val = "Radius settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
			}

			var serverIP = '<?php print $server_ip; ?>';
			var serverPort = '<?php print $server_port; ?>';
			function enableRadius() {
				var enable =  document.getElementById("enable_radius").checked;
				if( enable == false ) {
					document.getElementById("SERVERIP").value = "";
					document.getElementById("SERVERPORT").value = "";
					document.getElementById("SERVERIP").disabled = true;
					document.getElementById("SERVERPORT").disabled = true;
				} else {
					document.getElementById("SERVERIP").value = serverIP;
					document.getElementById("SERVERPORT").value = serverPort;
					document.getElementById("SERVERIP").disabled = false;
					document.getElementById("SERVERPORT").disabled = false;
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<h1>Radius<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="radius" method="post" action="radius.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>
					<td style="width: 40%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Radius Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="radius_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tbody>
							<tr> <td>
								<table cellpadding="2" cellspacing="2" class="sub-container-table">
							<tr> <td>
							<?php
								if($enable == "1")
									$enable_radius = "checked";
								else
									$enable_radius = "";
							?>
								<label for="enable_radius">Enable Radius</label>
							</td>
							<td><input type="checkbox" onclick="enableRadius();" <?php print $enable_radius; ?> id="enable_radius" name="enable_radius" /></td>
							</tr>

							<tr>
								<td>Radius Server IP</td>
								<td>
									<input type="text" value="<?php print $remote_login_server; ?>" id="SERVERIP" size="15" maxlength="32" class="ipBox" name="server_ip" />
									<img id="radius_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							<tr>
								<td>Radius Server Port</td>
								<td>
									<input type="text" value="<?php print $remote_login_server; ?>" id="SERVERPORT" size="15" maxlength="5" class="ipBox" name="server_port" />
									<img id="radius_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
						</table>
					</td> </tr>
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
				<a class="btn-grey-wide-l" id="apply_radius" onclick="submitData();"><span>Save</span></a>
				<a class="btn-grey-wide-l" id="cancel" onclick="resetData();"><span>Cancel</span></a>
			</div>
			</form>
		</div>
	</body>
	<?php
	include_once ("time_panel.php");
	$shell_obj = ShellCommand::getInstance();
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", 
				ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	?>
	<script type="text/javascript">
		enableRadius();
	</script>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if( submit == 1 ) {
			save_Message(val);
		}
	</script>
</html>
