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
	include_once('Model/global_fw_settings_model.php');
	include_once("Control/date_config.php");
	include_once("Control/web_users.php");
	include_once("Model/shell_command.php");

	$_SESSION['pageID'] = "#firewall_rate_limiting";
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
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$res = 0;
			$update_count = $obj -> getActionListCount();
			$global_fw_settings = GlobalFWSettingsModel::getInstance();
			$tcp_syn_flood_rate = $global_fw_settings->get(GlobalFWSettingsModel::$TCP_SYN_FLOOD_RATE);
			$tcp_syn_flood_burst = $global_fw_settings->get(GlobalFWSettingsModel::$TCP_SYN_FLOOD_BURST);
			$tcp_flood_rate = $global_fw_settings->get(GlobalFWSettingsModel::$TCP_FLOOD_RATE);
			$tcp_flood_burst = $global_fw_settings->get(GlobalFWSettingsModel::$TCP_FLOOD_BURST);
			$udp_flood_rate = $global_fw_settings->get(GlobalFWSettingsModel::$UDP_FLOOD_RATE);
			$udp_flood_burst = $global_fw_settings->get(GlobalFWSettingsModel::$UDP_FLOOD_BURST);
			$icmp_flood_rate = $global_fw_settings->get(GlobalFWSettingsModel::$ICMP_FLOOD_RATE);
			$icmp_flood_burst = $global_fw_settings->get(GlobalFWSettingsModel::$ICMP_FLOOD_BURST);
			if(isset($_POST) && isset($_POST['submit_action'])) {
				if ( trim($_POST['tcp_syn_flood_rate']) != $tcp_syn_flood_rate || 
					trim($_POST['tcp_syn_flood_burst']) != $tcp_syn_flood_burst
					|| trim($_POST['tcp_flood_rate']) != $tcp_flood_rate || 
					trim($_POST['tcp_flood_burst']) != $tcp_flood_burst || 
					trim($_POST['udp_flood_rate']) != $udp_flood_rate || 
					trim($_POST['udp_flood_burst']) != $udp_flood_burst
					|| trim($_POST['icmp_flood_rate']) != $icmp_flood_rate || 
					trim($_POST['icmp_flood_burst']) != $icmp_flood_burst ) {
						$global_fw_settings->set(GlobalFWSettingsModel::$TCP_SYN_FLOOD_RATE,trim($_POST['tcp_syn_flood_rate']));
						$global_fw_settings->set(GlobalFWSettingsModel::$TCP_SYN_FLOOD_BURST,trim($_POST['tcp_syn_flood_burst']));
						$global_fw_settings->set(GlobalFWSettingsModel::$TCP_FLOOD_RATE,trim($_POST['tcp_flood_rate']));
						$global_fw_settings->set(GlobalFWSettingsModel::$TCP_FLOOD_BURST,trim($_POST['tcp_flood_burst']));
						$global_fw_settings->set(GlobalFWSettingsModel::$UDP_FLOOD_RATE,trim($_POST['udp_flood_rate']));
						$global_fw_settings->set(GlobalFWSettingsModel::$UDP_FLOOD_BURST,trim($_POST['udp_flood_burst']));
						$global_fw_settings->set(GlobalFWSettingsModel::$ICMP_FLOOD_RATE,trim($_POST['icmp_flood_rate']));
						$global_fw_settings->set(GlobalFWSettingsModel::$ICMP_FLOOD_BURST,trim($_POST['icmp_flood_burst']));

						$global_fw_settings->createActionList(
							DEPLOY_MODULES::GLOBAL_FW_CONFIG, 
							"Global Firewall Settings updated", 
							DEPLOY_MODULES::GLOBAL_FW_CONFIG_STR,  "");
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
				getPageTips("dos_control");
				setTooltip();
			});

			function submitData() {
				if($sub("#global_fw_settings").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "dos_control.php";
				}
			}

			function initValidation()
			{
				$sub("#TCP_SYN_FLOOD_RATE").attr("data-validation-engine","validate[required,custom[integer],min[1],max[100000]]");
				$sub("#TCP_SYN_FLOOD_BURST").attr("data-validation-engine","validate[required,custom[integer],min[0],max[100000]]");
				$sub("#TCP_FLOOD_RATE").attr("data-validation-engine","validate[required,custom[integer],min[0],max[100000]]");
				$sub("#TCP_FLOOD_BURST").attr("data-validation-engine","validate[required,custom[integer],min[0],max[100000]]");
				$sub("#UDP_FLOOD_RATE").attr("data-validation-engine","validate[required,custom[integer],min[0],max[100000]]");
				$sub("#UDP_FLOOD_BURST").attr("data-validation-engine","validate[required,custom[integer],min[0],max[100000]]");
				$sub("#ICMP_FLOOD_RATE").attr("data-validation-engine","validate[required,custom[integer],min[0],max[100000]]");
				$sub("#ICMP_FLOOD_BURST").attr("data-validation-engine","validate[required,custom[integer],min[0],max[100000]]");
				$sub("#global_fw_settings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var val = "Global Firewall settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
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
			<h1 class="<?php print $class; ?>">Firewall Rate Limiting<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage();" /></h1>
			<div id="datetimebody" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<form id="global_fw_settings" method="post" action="dos_control.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table">
				<tr>
<!-- Begin : Device Settings -->
					<td style="width: 50%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Firewall Rate Limiting
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="dos_control_009" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tr><td>
						<table cellpadding="2" cellspacing="2" class="sub-container-table">
							<tbody>
								<tr>
									<td>TCP Syn Flood Rate</td>
									<td>
										<input type="text" id="TCP_SYN_FLOOD_RATE" value="<?php print $tcp_syn_flood_rate; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="tcp_syn_flood_rate" />
										<img id="dos_control_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>TCP Syn Flood Burst</td>
									<td>
										<input type="text" id="TCP_SYN_FLOOD_BURST" value="<?php print $tcp_syn_flood_burst; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="tcp_syn_flood_burst" />
										<img id="dos_control_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>TCP Flood Rate</td>
									<td>
										<input type="text" id="TCP_FLOOD_RATE" value="<?php print $tcp_flood_rate; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="tcp_flood_rate" />
										<img id="dos_control_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>TCP Flood Burst</td>
									<td>
										<input type="text" id="TCP_FLOOD_BURST" value="<?php print $tcp_flood_burst; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="tcp_flood_burst" />
										<img id="dos_control_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>UDP Flood Rate</td>
									<td>
										<input type="text" id="UDP_FLOOD_RATE" value="<?php print $udp_flood_rate; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="udp_flood_rate" />
										<img id="dos_control_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>UDP Flood Burst</td>
									<td>
										<input type="text" id="UDP_FLOOD_BURST" value="<?php print $udp_flood_burst; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="udp_flood_burst" />
										<img id="dos_control_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>ICMP Flood Rate</td>
									<td>
										<input type="text" id="ICMP_FLOOD_RATE" value="<?php print $icmp_flood_rate; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="icmp_flood_rate" />
										<img id="dos_control_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>ICMP Flood Burst</td>
									<td>
										<input type="text" id="ICMP_FLOOD_BURST" value="<?php print $icmp_flood_burst; ?>" size="13" text-align: center" class="ipBox" maxlength="6" name="icmp_flood_burst" />
										<img id="dos_control_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							</tbody>
						</table></td></tr>
						</table>
					</td>
					<td style="width: 50%; vertical-align: top;">
						&nbsp;
					</td>
<!-- End : Device Settings -->
				</table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			</div>
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_datetime" onclick="submitData();"><span>Save</span></a>
				<a class="btn-grey-wide-l" id="reset_default"><span>Cancel</span></a>
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
		initValidation();
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if(submit == 1) {
			save_Message(val);
		}
	</script>
</html>
