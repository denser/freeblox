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
	include_once('Model/sbc_gen_settings_model.php');
	include_once("Model/action_mapping_model.php");
	include_once("Control/date_config.php");
	include_once("Control/web_users.php");
	include_once("Model/shell_command.php");
	include_once("Model/sys_calls.php");
	$_SESSION['pageID'] = "#gen_settings_signal";
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
		<?php
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$res = 0;
			$update_count = $obj -> getActionListCount();
			$sbc_gen_settings = SBC_General_SettingsModel::getInstance();
			$shell_obj = ShellCommand::getInstance();
			$df_user_agent = "";
			$checkUbuntu = SysCalls::checkUbuntuCentos();
                        if($checkUbuntu != "") {
				$shell_status_dpkg = $shell_obj->Execute(
					"dpkg --list | grep blox-opensips", 
					ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($shell_status_dpkg == 0) {
					$dpkg_str = $shell_obj->getLastOutput();
				}
				$dpkg_data = preg_split('/\s+/',$dpkg_str);
				$df_user_agent = "Blox-".$dpkg_data[2];
			} else {
				$shell_status_dpkg = $shell_obj->Execute(
					"rpm -qa | grep blox_opensips", 
					ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($shell_status_dpkg == 0) {
					$rpm_str = $shell_obj->getLastOutput();
				}
				$rpm_data = explode('-',$rpm_str,2);
				$parts = explode(".",$rpm_data[1]);
				$last = array_pop($parts);
				$version = implode(".",$parts);
				$df_user_agent = "Blox-".$version;
				$ping_nat = "0";
			}

			if(isset($_POST) && isset($_POST['submit_action'])) {
				if ( $_POST['ka_methods'] != $keep_alive_method || $ka_interval != trim($_POST['ka_interval']) || $ka_from_uri != trim($_POST['ka_from_uri']) || trim($_POST['user_agent']) != $user_agent ) {
					$keepalivefrom = trim($_POST['sipcolon']).trim($_POST['ka_from_uri']);
					$sbc_gen_settings->set(SBC_General_SettingsModel::$KEEPALIVE_SIP_METHOD,$_POST['ka_methods']);
					$sbc_gen_settings->set(SBC_General_SettingsModel::$KEEPALIVE_INTERVAL,trim($_POST['ka_interval']));
					$sbc_gen_settings->set(SBC_General_SettingsModel::$KEEPALIVE_FROM_URI,$keepalivefrom);
					$sbc_gen_settings->set(SBC_General_SettingsModel::$USER_AGENT,trim($_POST['user_agent']));
					$sbc_gen_settings->set(SBC_General_SettingsModel::$CDR_MAX_RECORD,trim($_POST['CDR_MAX_RECORD']));
					$sbc_gen_settings->set(SBC_General_SettingsModel::$PING_NAT,trim($_POST['PING_NAT_HID']));
					$sbc_gen_settings->createActionList(DEPLOY_MODULES::SBC_CONFIG,  "SBC NAT settings updated", DEPLOY_MODULES::SBC_CONFIG_STR, "");
				}
				$res = 1;
			}
			$ping_nat = $sbc_gen_settings->get(SBC_General_SettingsModel::$PING_NAT);
			$keep_alive_method = $sbc_gen_settings->get(SBC_General_SettingsModel::$KEEPALIVE_SIP_METHOD);
			$ka_interval = $sbc_gen_settings->get(SBC_General_SettingsModel::$KEEPALIVE_INTERVAL);
			$ka_from_uri1 = $sbc_gen_settings->get(SBC_General_SettingsModel::$KEEPALIVE_FROM_URI);
			$user_agent = $sbc_gen_settings->get(SBC_General_SettingsModel::$USER_AGENT);
			if($user_agent == "" ) {
				$user_agent = $df_user_agent;
			} 
			$cdr_max_record = $sbc_gen_settings->get(SBC_General_SettingsModel::$CDR_MAX_RECORD);
			$get_keepalive_methods = SBC_General_SettingsModel::keepalive_methods();
			$ka_methods_count = count($get_keepalive_methods);
			$ka_from_uriarray = explode("sip:",$ka_from_uri1);
			$ka_from_uri = $ka_from_uriarray[1];
		?>
		<script type="text/javascript">
			var df_user_agent = "<?php echo $df_user_agent; ?>";
			$sub(document).ready(function() {
				initValidation();
				getPageTips("sig_settings");
				setTooltip();
			});

			function submitData() {
				var formObj = document.forms[0];
				formObj.submit_action.value = "1";
				if($sub("#PING_NAT").is(":checked")) {
					$sub("#PING_NAT_HID").val("1");
				} else {
					$sub("#PING_NAT_HID").val("0");
				}
				var user_agent_val = document.getElementById("USER_AGENT").value;
				if(user_agent_val == "" ) {
					document.getElementById("USER_AGENT").value = df_user_agent;
				}
				if($sub("#gen_settings").validationEngine('validate')) {
					document.getElementById("sipcolon").disabled = false;
					formObj.submit();
				}
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "sbc_gen_settings.php";
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "sbc_gen_settings.php";
			}

			function initValidation() {
				$sub("#KEEPALIVE_INTERVAL").attr("data-validation-engine","validate[required,custom[integer],min[5],max[86400]]");
				$sub("#CDR_MAX_RECORD").attr("data-validation-engine","validate[required,custom[integer],min[100],max[999999]]");
				$sub("#KEEPALIVE_FROM_URI").attr("data-validation-engine","validate[required,custom[USER_AT_OR_IP_DOMAIN]]");
				//$sub("#USER_AGENT").attr("data-validation-engine","validate[custom[userAgent]]");
				$sub("#gen_settings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function default_userAgent() {
				var user_agent_val = document.getElementById("USER_AGENT").value;
				if(user_agent_val == df_user_agent) {
					document.getElementById("USER_AGENT").value = "";
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
			<h1 class="<?php print $class; ?>">General Settings<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage();" /></h1>
			<div id="datetimebody" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<form id="gen_settings" method="post" action="sbc_gen_settings.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table">
				<tr>
<!-- Begin : Global Settings -->
					<td style="width: 60%;vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Global Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="sig_settings_006" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tr>
								<td align="left">
									<table cellpadding="2" cellspacing="2" class="sub-container-table">
										<tbody>
											<tr >
												<td>User Agent </td>
												<td>
													<input type="text" id="USER_AGENT" name="user_agent" value="<?php print $user_agent; ?>" size="23" maxlength="256" class="ipBox" onclick="default_userAgent();" />
													<img id="sig_settings_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
											</tr>
											<tr>
												<td> Maximum CDR Record</td>
												<td>
													<input type="text" id="CDR_MAX_RECORD" value="<?php print $cdr_max_record; ?>" size="10" maxlength="6" class"ipBox" name="CDR_MAX_RECORD" />  <img id="sig_settings_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</table>
					</td>
<!-- End : Global Settings -->
					<td style="width: 30%; vertical-align: top;"> &nbsp; </td>
				</tr>
				<tr>
					<td style="width: 50%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									NAT Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="sig_settings_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tr>
							<td>
								<table cellpadding="2" cellspacing="2" class="sub-container-table">
									<tbody>
										<tr>
											<td>Ping NAT Only</td>
											<td>
												<input type="checkbox" <?php if($ping_nat == 1) echo "checked='checked'" ?> id="PING_NAT" name="PING_NAT"/>
												<img id="sig_settings_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr>
											<td>Keepalive SIP Method</td>
											<td>
												<select class="ipDrpBox" name="ka_methods" id="ka_methods" onchange="setNATType();" >

										<?php
											for ( $j = 0; $j < $ka_methods_count; $j++ ) {
											$select_str = "";
											if ( $get_keepalive_methods[$j] == $keep_alive_method )
												$select_str = "selected";
										?>
											<option value="<?php print $get_keepalive_methods[$j];?>" <?php print  $select_str;?> ><?php print  $get_keepalive_methods[$j];?></option>
										<?php } ?>

												</select>
												<img id="sig_settings_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr>
											<td>Keepalive Interval </td>
											<td>
													<input type="text" id="KEEPALIVE_INTERVAL" value="<?php print $ka_interval; ?>" size="6" text-align: center" class="ipBox" maxlength="5" name="ka_interval" />
													<img id="sig_settings_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr>
											<td>Keepalive From-URI </td>
											<td><input type="text" id="sipcolon" name="sipcolon" value="sip:" readonly disabled size="1" text-align: center" class="ipBox" maxlength="3"/>
												<input type="text" id="KEEPALIVE_FROM_URI" value="<?php print $ka_from_uri; ?>" size="16" text-align: center" class="ipBox" maxlength="256" name="ka_from_uri" />
												<img id="sig_settings_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
<!-- End : NAT Settings -->
			<input type="hidden" name="submit_action" value="0" />
			<input type="hidden" name="DATECHANGE" value="false" />
			<input type="hidden" name="PING_NAT_HID" id="PING_NAT_HID" value="0" />
			</div>
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_datetime" onclick="submitData();"><span>Save</span></a>
				<a class="btn-grey-wide-l" id="reset_default" onclick="resetData();"><span>Cancel</span></a>
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
	</script>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
	</script>
</html>
