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
	include_once('Model/sys_calls.php');
	include_once("Model/shell_command.php");

	$_SESSION['pageID'] = "#signature";
	$shell_obj = ShellCommand::getInstance();
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

			var val = "No Signature updates found.";
			function sig_UpdateMsg(val) {
				parent.sigUpdateMsg(val);
			}

			function updateSipSig() {
//				setTimeout(sig_UpdateMsg(val), 15);
				sig_UpdateMsg(val);
				/*$sub.ajax({
					type: "POST",
					cache: false,
					data: {sig_update:1},
					url:  "updates.html",
					success: function (html) {
						if( html != '')
						{
							sig_UpdateMsg(html);
						}
					}
				});*/
			}

		</script>
		<?php
			$dev_settings = DeviceSettingsModel::getInstance();
			$enable_dpi = $dev_settings->get(DeviceSettingsModel::$ENABLE_DPI_UPDATE);
			$subscription_key = $dev_settings->get(DeviceSettingsModel::$SUBSCRIPTION_KEY);
			$dpi_update_schedule = $dev_settings->get(DeviceSettingsModel::$DPI_UPDATE_SCHEDULE);
			$get_schedule = explode(':',$dpi_update_schedule);
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();
			$res = 0;

			if(isset($_POST) && isset($_POST['submit_action'])) {
				if($_POST['enable_dpi'] == "on")
					$post_enable = "1";
				else
					$post_enable = "";
				if( ($post_enable != $enable_dpi || 
						trim($_POST['subscription_key']) != $subscription_key ||
						$get_schedule[0] != trim($_POST['hour']) || 
						$get_schedule[1] != trim($_POST['minute']) || 
						$get_schedule[2] != $_POST['am_pm'] || 
						$get_schedule[3] != $_POST['weekly'] || 
						$get_schedule[4] != $_POST['monthly'] || 
						$get_schedule[5] != $_POST['update_monthly']) && 
						($_POST['submit_action'] == "1")) {
							$dev_settings->set(DeviceSettingsModel::$ENABLE_DPI_UPDATE,$post_enable);
							$dev_settings->set(DeviceSettingsModel::$SUBSCRIPTION_KEY,trim($_POST['subscription_key']));
							$week = $_POST['weekly'] ? $_POST['weekly']:'*';
							$date = $_POST['update_monthly'] ? $_POST['update_monthly']:'*';
							$update_schedule = trim($_POST['hour']).":".trim($_POST['minute']).":".$_POST['am_pm'].":".$week.":".$_POST['monthly'].":".$date;
							$dev_settings->set(DeviceSettingsModel::$DPI_UPDATE_SCHEDULE,$update_schedule);
							if($post_enable == "1" && ($_POST['submit_update_now'] == 0)) {
								$cmd = "bloxconfig"." ".DEPLOY_MODULES::SIGUPDATE_CONFIG;
								$ret = "";
								$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
								if($shell_status == 0) {
									$ret = $shell_obj->getLastOutput();
								}
								$cmdres=$ret ;
							}
		?>
		<script type="text/javascript">
			submit = 1;
		</script>
		<?php
				}
				if($_POST['submit_update_now'] == 1) {
					$dev_settings->set(DeviceSettingsModel::$SUBSCRIPTION_KEY,trim($_POST['subscription_key']));
		?>
		<script type="text/javascript">
			updateSipSig();
		</script>
		<?php
				}
				$res = 1;
			}
			$dev_settings = DeviceSettingsModel::getInstance();
			$enable_dpi = $dev_settings->get(DeviceSettingsModel::$ENABLE_DPI_UPDATE);
			$subscription_key = $dev_settings->get(DeviceSettingsModel::$SUBSCRIPTION_KEY);
			$dpi_update_schedule = $dev_settings->get(DeviceSettingsModel::$DPI_UPDATE_SCHEDULE);
			$get_schedule = explode(':',$dpi_update_schedule);
		?>
		<script type="text/javascript">
			$sub(document).ready(function() {
				initValidation();
				getPageTips("sig_update");
				setTooltip();
			});

			function submitData(val) {
				if($sub("#sigUpdate").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(val == 1) {
						formObj.submit_update_now.value = "1";
						formObj.submit_action.value = "0";
					}else {
						formObj.submit_action.value = "1";
					}
					formObj.submit();
					return true;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "sig_update.php";
			}

			function initValidation() {
				$sub("#HOUR").attr("data-validation-engine","validate[required,custom[integer],min[00],max[12]]");
				$sub("#MINUTE").attr("data-validation-engine","validate[required,custom[integer],min[00],max[60]]");
				$sub("#SUB_KEY").attr("data-validation-engine","validate[required,maxSize[2048]]");
				$sub("#sigUpdate").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "sig_update.php";
				}
			}

			function changeSchedType() {
				var schedType = document.getElementById("MONTHLY").value;
				if( schedType == "daily" ) {
					document.getElementById("WEEKLY").disabled = true;
					document.getElementById("UPDATE_MONTHLY").disabled = true;
				} else if( schedType == "weekly" ) {
					document.getElementById("WEEKLY").disabled = false;
					document.getElementById("UPDATE_MONTHLY").disabled = true;
				} else if( schedType == "monthly" ) {
					document.getElementById("WEEKLY").disabled = true;
					document.getElementById("UPDATE_MONTHLY").disabled = false;
				}
			}

			var val = "SigUpdate settings saved successfully";
			function save_Message(val)
			{
				parent.saveMessage(val);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();">
		<div class="content">
			<h1>Signature Update<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="sigUpdate" method="post" action="sig_update.php" autocomplete="off">
			<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>
					<td style="width: 60%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Signature Update Settings
								<img src="images/Icn_info_blue_bg.png" class="ttip" id="sig_update_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tbody>
								<tr> <td>
									<table cellpadding="2" cellspacing="2" class="sub-container-table">
										<tr>
											<td>
											<?php
											if($enable_dpi == "1")
												$enable_dpi = "checked";
											else
												$enable_dpi = "";
											?>
											<label for="enable_dpi">Enable Update </label>
											</td>
											<td><input type="checkbox" id="enable_dpi" <?php print $enable_dpi; ?> name="enable_dpi" /></td>
										</tr>
										<tr>
											<td>Time Schedule </td>
											<td>
												<table>
													<tr>
													<?php
													$dpi_sched_arr = explode(":",$dpi_update_schedule);
													?><td>
													<input type="text" value="<?php print $dpi_sched_arr[0];?>" maxlength="2" class="ipBox" id="HOUR" name="hour" size="1" />
													<input type="text" value="<?php print $dpi_sched_arr[1];?>" maxlength="2" class="ipBox" id="MINUTE" name="minute" size="1"  />
													<select name="am_pm" class="ipDrpBox">
														<option <?php if($dpi_sched_arr[2] == "AM") print "selected=true;"?>value="AM">AM</option>
														<option <?php if($dpi_sched_arr[2] == "PM") print "selected=true;"?> value="PM">PM</option>
													</select>
													<select name="monthly" id="MONTHLY" class="ipDrpBox" onchange="changeSchedType();">
														<option <?php if($dpi_sched_arr[4] == "daily") print "selected=true;"?> value="daily">Daily</option>
														<option <?php if($dpi_sched_arr[4] == "weekly") print "selected=true;"?> value="weekly">Weekly</option>
														<option <?php if($dpi_sched_arr[4] == "monthly") print "selected=true;"?>  value="monthly">Monthly</option>
													</select>
													<?php
													$weekdays = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
													?>
													<select name="weekly" id="WEEKLY" class="ipDrpBox">
													<?php
													for( $w = 0; $w < count($weekdays); $w++) {
													?>
														<option <?php if($dpi_sched_arr[3] == $weekdays[$w] ) print "selected=true"; ?>value="<?php print $weekdays[$w]; ?>"> <?php print $weekdays[$w]; ?> </option>
													<?php
													}
													?>
													</select>
													<select name="update_monthly" id="UPDATE_MONTHLY" class="ipDrpBox">
													<?php
													for($m = 1;  $m <= 31; $m++) {
													?>
														<option <?php if($dpi_sched_arr[5] == $m) print "selected=true;"?> value="<?php print $m;?>"><?php print $m;?></option>
												<?php } ?>
													</selelct>
													<img id="sig_update_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td> </tr>
									</table>
								</td> </tr>
							</table>
						</td> </tr>
					</tbody>
				</table>
					</td>
						<td style="width: 50%; vertical-align: top;"> &nbsp; </td>
				</tr></table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="submit_update_now" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_sigupdate" onclick="submitData('<?php echo '0';?>');"><span>Apply</span></a>
				<a class="btn-grey-wide-l" id="cancel" onclick="resetData();"><span>Cancel</span></a>
				<a class="btn-grey-wide-l" id="apply_sigupdate_now" onclick="submitData('<?php echo '1'; ?>');"><span>Update Signatures now</span></a>
			</div>
			</form>
		</div>
	</body>
	<?php
	include_once ("time_panel.php");
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	?>
	<script type="text/javascript">
		changeSchedType();
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if( submit == 1 ) {
			save_Message(val);
		}
	</script>
</html>
