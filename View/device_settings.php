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
	include_once("Model/device_access_model.php");
	include_once("Model/media_settings_model.php");
	include_once("Control/date_config.php");
	include_once("Control/web_users.php");
	include_once("Model/sys_calls.php");
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#gen_settings";
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
			$dev_settings = DeviceSettingsModel::getInstance();
			$hostname = $dev_settings->get(DeviceSettingsModel::$HOSTNAME);
			$lan_conf_mode = $dev_settings->get(DeviceSettingsModel::$LAN_CONFIG_MODE);
			$lan_ip_addr = $dev_settings->get(DeviceSettingsModel::$LAN_IP_ADDR);
			$lan_netmask = $dev_settings->get(DeviceSettingsModel::$LAN_NETMASK);
			$wan_conf_mode = $dev_settings->get(DeviceSettingsModel::$WAN_CONFIG_MODE);
			$wan_ip_addr = $dev_settings->get(DeviceSettingsModel::$WAN_IP_ADDR);
			$wan_netmask = $dev_settings->get(DeviceSettingsModel::$WAN_NETMASK);
			$device_gateway = $dev_settings->get(DeviceSettingsModel::$GATEWAY);
			$device_dnsserver = $dev_settings->get(DeviceSettingsModel::$DNSSERVER);
			$enable_ssh = $dev_settings->get(DeviceSettingsModel::$ENABLE_SSH);
			$ssh_port = $dev_settings->get(DeviceSettingsModel::$SSH_PORT);
			$allow_icmp = $dev_settings->get(DeviceSettingsModel::$ALLOW_ICMP);
			$mgmt_vlan_ip_addr = $dev_settings->get(DeviceSettingsModel::$MGMT_VLAN_IP_ADDR);
			$mgmt_vlan_netmask = $dev_settings->get(DeviceSettingsModel::$MGMT_VLAN_NETMASK);
			$lan_interface = $dev_settings->get(DeviceSettingsModel::$LAN_INTERFACE);
			$wan_interface = $dev_settings->get(DeviceSettingsModel::$WAN_INTERFACE);
			$transcodingStatus = SysCalls::getTranscodingStatus();
			$enable_transcoding = $dev_settings->get(DeviceSettingsModel::$ENABLE_TRANSCODING);
			$transcodingInterface = SysCalls::getTranscodingInterface();
			if ($transcodingStatus == 1 && $enable_transcoding == "1" ) {
				$transcodingIP = $dev_settings->get(DeviceSettingsModel::$TRANSCODING_IP_ADDR);
				$transcodingNetmask = $dev_settings->get(DeviceSettingsModel::$TRANSCODING_NETMASK);
			}
			$interfacesArray = SysCalls::getRealInterfaces();
			$interfaces = array_values($interfacesArray);
			$int_count = count($interfaces);
			$media_settings = MediaConfigModel::getInstance();
			$transCount = $media_settings->getTranscodingCount($transcodingInterface);

			if(isset($_POST) && isset($_POST['submit_action'])) {
				if($_POST['ssh_enable'] == "on")
					$ssh_enable = "1";
				else
					$ssh_enable = "";
				if($_POST['allow_icmp'] == "on")
					$post_icmp = "1";
				else
					$post_icmp = "";
				if($_POST['trans_enable'] == "on")
					$post_trans_enable = "1";
				else
					$post_trans_enable = "";
				if ( trim($_POST['hostname']) != $hostname ) {
					$dev_settings->set(DeviceSettingsModel::$HOSTNAME,trim($_POST['hostname']));
					$dev_settings->createActionList(DEPLOY_MODULES::HOST_CONFIG, 
						"Hostname updated as ".trim($_POST['hostname']), 
						DEPLOY_MODULES::HOST_CONFIG_STR, "");
				}
				if( ($ssh_enable != $enable_ssh) || ((trim($_POST['ssh_port'])) != $ssh_port)) {
					$dev_settings->set(DeviceSettingsModel::$ENABLE_SSH,$ssh_enable);
					$dev_settings->set(DeviceSettingsModel::$SSH_PORT,trim($_POST['ssh_port']));
					$dev_settings->createActionList(DEPLOY_MODULES::SSH_CONFIG, 
						"SSH Settings updated", 
						DEPLOY_MODULES::SSH_CONFIG_STR, "");
				}
				if( $_POST['lan_ip_config'] !=  $lan_conf_mode || 
					trim($_POST['lan_ip_addr']) != $lan_ip_addr || 
					trim($_POST['lan_net_mask']) != $lan_netmask || 
					$_POST['wan_ip_config'] != $wan_conf_mode || 
					trim($_POST['wan_ip_addr']) != $wan_ip_addr || 
					trim($_POST['wan_net_mask']) != $wan_netmask || 
					$_POST['gateway'] != $device_gateway || 
					$_POST['dnsserver'] != $device_dnsserver || 
					$_POST['lan_interface'] != $lan_interface || 
					$_POST['wan_interface'] != $wan_interface) {
						$dev_settings->set(
							DeviceSettingsModel::$LAN_CONFIG_MODE,
							$_POST['lan_ip_config']);
						$dev_settings->set(DeviceSettingsModel::$LAN_IP_ADDR,
							trim($_POST['lan_ip_addr']));
						$dev_settings->set(DeviceSettingsModel::$LAN_NETMASK,
							trim($_POST['lan_net_mask']));
						$dev_settings->set(DeviceSettingsModel::$WAN_CONFIG_MODE,$_POST['wan_ip_config']);
						$dev_settings->set(DeviceSettingsModel::$WAN_IP_ADDR,
							trim($_POST['wan_ip_addr']));
						$dev_settings->set(DeviceSettingsModel::$WAN_NETMASK,
							trim($_POST['wan_net_mask']));
						$dev_settings->set(DeviceSettingsModel::$GATEWAY,
							trim($_POST['gateway']));
						$dev_settings->set(DeviceSettingsModel::$DNSSERVER,
							trim($_POST['dnsserver']));
						$dev_settings->set(DeviceSettingsModel::$LAN_INTERFACE,
							$_POST['lan_interface']);
						$dev_settings->set(DeviceSettingsModel::$WAN_INTERFACE,
							$_POST['wan_interface']);
						$dev_settings->createActionList(
							DEPLOY_MODULES::NET_CONFIG, 
							"Network Settings updated", 
							DEPLOY_MODULES::NET_CONFIG_STR, "");
				}
				if ( $allow_icmp != $post_icmp ) {
					$dev_settings->set(DeviceSettingsModel::$ALLOW_ICMP,$post_icmp);
					$dev_settings->createActionList(
						DEPLOY_MODULES::DEVICE_ACCESS_CONFIG, 
						"Device Access Configuration Changed", 
						DEPLOY_MODULES::DEVICE_ACCESS_CONFIG_STR, "");
				}
				if( trim($_POST['mgmt_vlan_addr']) != $mgmt_vlan_ip_addr || 
					trim($_POST['mgmt_vlan_mask']) != $mgmt_vlan_netmask ) {
						$dev_settings->set(
							DeviceSettingsModel::$MGMT_VLAN_IP_ADDR,
							trim($_POST['mgmt_vlan_addr']));
						$dev_settings->set(
							DeviceSettingsModel::$MGMT_VLAN_NETMASK,
							trim($_POST['mgmt_vlan_mask']));
						$dev_settings->createActionList(
							DEPLOY_MODULES::MGMT_VLAN_CONFIG, 
							"Management Vlan Settings updated", 
							DEPLOY_MODULES::MGMT_VLAN_CONFIG_STR, "");
							$device_access = DeviceAccessConfigModel::getInstance();
							$device_access->updateMgmtVlanAccessRule(
								trim($_POST['mgmt_vlan_addr']), 
								trim($_POST['mgmt_vlan_mask']));
				}
				if ($transcodingStatus == 1) {
					if ( trim($_POST['transcoding_ip_addr']) != $transcodingIP || 
						trim($_POST['transcoding_net_mask']) != 
						$transcodingNetmask || 
						$post_trans_enable != $enable_transcoding ) {
							$dev_settings->set(
							DeviceSettingsModel::$ENABLE_TRANSCODING,
							$post_trans_enable);
							$dev_settings->set(
							DeviceSettingsModel::$TRANSCODING_IP_ADDR,
							trim($_POST['transcoding_ip_addr']));
							$dev_settings->set(
							DeviceSettingsModel::$TRANSCODING_NETMASK,
							trim($_POST['transcoding_net_mask']));
							$dev_settings->createActionList(
							DEPLOY_MODULES::TRANSCODING_CONFIG, 
							"Transcoding Settings updated", 
							DEPLOY_MODULES::TRANSCODING_CONFIG_STR, 
							"");
					}
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
				getPageTips("gen_settings");
				setTooltip();
			});

			function submitData() {
				var formObj = document.forms[0];
				formObj.submit_action.value = "1";
				var checkInt  = interfaceSelect();
				if ( checkInt != "false") {
					if($sub("#systemsettings").validationEngine('validate')) {
						formObj.submit();
					}
				}
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "device_settings.php";
				}
			}

			function resetData() {
					parent.document.getElementById("container").src = "device_settings.php";
			}

			function initValidation() {
				$sub("#HOST").attr("data-validation-engine","validate[required,minSize[3],maxSize[32]]");
				$sub('#wan_ip_config').on('change', function() { {
					var wan_ip_config = $sub("#wan_ip_config option:selected").val();
					if( wan_ip_config == "static" ) {
						$sub("#WAN_IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#Wan_NetMask").attr("data-validation-engine","validate[required,custom[netmask]]");
						$sub("#GATEWAY").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#DNSSERVER").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
				}
				});
				$sub('#lan_ip_config').on('change', function() { {
					var lan_ip_config = $sub("#lan_ip_config option:selected").val();
					if( lan_ip_config == "static" ) {
						$sub("#LAN_IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#Lan_NetMask").attr("data-validation-engine","validate[required,custom[netmask]]");
						$sub("#GATEWAY").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#DNSSERVER").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
				}
				});
				$sub( document ).ready(function() {
					var wan_ip_config = $sub("#wan_ip_config option:selected").val();
					if( wan_ip_config == "static" ) {
						$sub("#WAN_IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#Wan_NetMask").attr("data-validation-engine","validate[required,custom[netmask]]");
						$sub("#GATEWAY").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#DNSSERVER").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
				});
				$sub( document ).ready(function() {
					var lan_ip_config = $sub("#lan_ip_config option:selected").val();
					if( lan_ip_config == "static" ) {
						$sub("#LAN_IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#Lan_NetMask").attr("data-validation-engine","validate[required,custom[netmask]]");
						$sub("#GATEWAY").attr("data-validation-engine","validate[required,custom[ipv4]]");
						$sub("#DNSSERVER").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
				});
				$sub("#TRANSCODING_IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
				$sub("#TRANSCODING_NETMASK").attr("data-validation-engine","validate[required,custom[netmask]]");
				$sub("#MGMTVLANADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
				$sub("#MGMTVLANMASK").attr("data-validation-engine","validate[required,custom[netmask]]");
				$sub("#SSHPORT").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
				$sub("#systemsettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var val = "General settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
			}

			/*function setWANIPConfigType() {
				var wan_ip_config_type = document.getElementById("wan_ip_config").value;
				if ( wan_ip_config_type == "dhcp" ) {
					document.getElementById("WAN_IPADDR").disabled = true;
					document.getElementById("Wan_NetMask").disabled  = true;
					document.getElementById("GATEWAY").disabled  = true;
					document.getElementById("DNSSERVER").disabled  = true;
				}
				if ( wan_ip_config_type == "static" ) {
					document.getElementById("GATEWAY").disabled  = false;
					document.getElementById("DNSSERVER").disabled  = false;
					document.getElementById("WAN_IPADDR").disabled = false;
					document.getElementById("Wan_NetMask").disabled  = false;
				}
			}

			function setLANIPConfigType() {
				var lan_ip_config_type = document.getElementById("lan_ip_config").value;
				if ( lan_ip_config_type == "dhcp" ) {
					document.getElementById("LAN_IPADDR").disabled = true;
					document.getElementById("Lan_NetMask").disabled  = true;
				} else {
					document.getElementById("LAN_IPADDR").disabled = false;
					document.getElementById("Lan_NetMask").disabled  = false;
				}
			}*/

			function interfaceSelect() {
				var lanInterface = document.getElementById('lan_interface').value;
				var wanInterface = document.getElementById('wan_interface').value;
				var flag;
				if (  lanInterface == wanInterface) {
					alert("Invalid! Please select a different interface as it is already being used.");
					flag = "false";
				} else {
					flag = "true";
				}
				return flag;
			}

			function enableSSH() {
				var ssh_enable = document.getElementById("enable").checked;
				if( ssh_enable == false ) {
					document.getElementById("SSHPORT").disabled = true;
				} else {
					document.getElementById("SSHPORT").disabled = false;
				}
			}

			var transcodingStatus = '<?php print $transcodingStatus; ?>';
			function showTransInterfaceName() {
				var transEnable = document.getElementById("trans_enable").checked;
				if ( transEnable == true && transcodingStatus == "1" ) {
					document.getElementById("TRANS_INTERFACE").innerHTML = transInterface;
				} else {
					document.getElementById("TRANS_INTERFACE").innerHTML = "";
				}
			}

			var transInterface = '<?php print $transcodingInterface; ?>';
			var transStatus = '<?php print $transcodingStatus; ?>';
			var en_dis;
			var transCount = '<?php print $transCount ?>';
			function transcodingEnable(en_dis) {
				if ( transStatus == "0" ) {
					document.getElementById("trans_enable").disabled = true;
					document.getElementById("trans_enable").checked = false;
					document.getElementById("TRANSCODING_IPADDR").disabled = true;
					document.getElementById("TRANSCODING_NETMASK").disabled = true;
					document.getElementById("TRANS_INTERFACE").innerHTML = "" ;
				}
				var trans_en_dis = en_dis.checked;
				if (trans_en_dis == true && transStatus == "1" ) {
					document.getElementById("TRANSCODING_IPADDR").disabled = false;
					document.getElementById("TRANSCODING_NETMASK").disabled = false;
					document.getElementById("TRANS_INTERFACE").innerHTML = transInterface;
				} else if ( trans_en_dis == false && transCount == "1" ) {
					alert("Transcoding interface is being used by Media Profiles. Please delete media profile, disable transcoding interface");
					document.getElementById("trans_enable").checked = true;
					return false;
				} else {
					document.getElementById("TRANSCODING_IPADDR").disabled = true;
					document.getElementById("TRANSCODING_NETMASK").disabled = true;
					document.getElementById("TRANS_INTERFACE").innerHTML = "" ;
				}
			}

			function enableDisableTranscoding() {
				var status = document.getElementById("trans_enable").checked;
				if ( status == false ) {
					document.getElementById("TRANSCODING_IPADDR").disabled = true;
					document.getElementById("TRANSCODING_NETMASK").disabled = true;
					document.getElementById("TRANS_INTERFACE").innerHTML = "" ;
				} else {
					document.getElementById("TRANSCODING_IPADDR").disabled = false;
					document.getElementById("TRANSCODING_NETMASK").disabled = false;
					document.getElementById("TRANS_INTERFACE").innerHTML = transInterface;
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
			<h1 class="<?php print $class; ?>">Settings<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage();" /></h1>
			<div id="datetimebody" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<form id="systemsettings" method="post" action="device_settings.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table">
				<tr>
<!-- Begin : Device Settings -->
					<td style="width: 50%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Device Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="gen_settings_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tr><td>
							<table cellpadding="2" cellspacing="2" class="sub-container-table">
							<tbody>
								<tr>
									<td>Host Name </td>
									<td>
										<input type="text" id="HOST" value="<?php print $hostname; ?>" size="13" text-align: center" class="ipBox" maxlength="32" name="hostname" />
										<img id="gen_settings_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>Lan Interface</td>
									<td>
										<select name="lan_interface" id="lan_interface">
										<?php
											for ($i = 0; $i < $int_count; $i++) {
												$select_str = "";
												if ( $interfaces[$i] == $lan_interface )
													$select_str = "selected";
										?>
											<option <?php print $select_str; ?> value="<?php print $interfaces[$i]; ?>"><?php print $interfaces[$i] ?></option>
										<?php 
											}
										?>
										</select>
										<img id="gen_settings_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>LAN IP Configuration </td>
									<td>
										<select class="ipDrpBox" name="lan_ip_config" id="lan_ip_config">
											<!--option value='dhcp' <?php if($lan_conf_mode == "dhcp") print "selected = true" ?>>DHCP</option-->
											<option value='static' <?php if($lan_conf_mode == "static") print "selected = true" ?>>Static</option>
										</select>
										<img id="gen_settings_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>LAN IP Address / Netmask </td>
									<td>
										<input type="text" id="LAN_IPADDR" value="<?php print $lan_ip_addr; ?>" size="13" text-align: center" class="ipBox" maxlength="15" name="lan_ip_addr" />
										<input type="text" value="<?php print $lan_netmask; ?>" size="13" text-align: center" class="ipBox" maxlength="15" id="Lan_NetMask" name="lan_net_mask" />
										<img id="gen_settings_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>Wan Interface</td>
									<td>
										<select name="wan_interface" id="wan_interface">
										<?php
											for ($i = 0; $i < $int_count; $i++) {
												$select_str = "";
												if ( $interfaces[$i] == $wan_interface )
													$select_str = "selected";
										?>
											<option <?php print $select_str; ?> value="<?php print $interfaces[$i]; ?>"><?php print $interfaces[$i] ?></option>
											<?php
											}
											?>
										</select>
										<img id="gen_settings_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>WAN IP Configuration </td>
									<td>
										<select class="ipDrpBox" name="wan_ip_config" id="wan_ip_config">
											<!--option value='dhcp' <?php if($wan_conf_mode == "dhcp") print "selected = true" ?>>DHCP</option-->
											<option value='static' <?php if($wan_conf_mode == "static") print "selected = true" ?>>Static</option>
										</select>
										<img id="gen_settings_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>WAN IP Address / Netmask </td>
									<td>
										<input type="text" id="WAN_IPADDR" value="<?php print $wan_ip_addr; ?>" size="13" text-align: center" class="ipBox" maxlength="15" name="wan_ip_addr" />
										<input type="text" value="<?php print $wan_netmask; ?>" size="13" text-align: center" class="ipBox" maxlength="15" id="Wan_NetMask" name="wan_net_mask" />
										<img id="gen_settings_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>Gateway </td>
									<td>
										<input type="text" id="GATEWAY" value="<?php print $device_gateway; ?>" size="13" text-align: center" class="ipBox" maxlength="15" name="gateway" />
										<img id="gen_settings_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>Dns Server </td>
									<td>
										<input type="text" id="DNSSERVER" value="<?php print $device_dnsserver; ?>" size="13" text-align: center" class="ipBox" maxlength="15" name="dnsserver" />
										<img id="gen_settings_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<table>
											<tr>
												<td><label for="enable">Enable SSH </label>
												<?php
												if ($enable_ssh == "1")
													$sshEnable = "checked";
												else
													$sshEnable = "";
												?>
												</td>
												<td>
													<input type="checkbox" <?php print $sshEnable; ?> name="ssh_enable" onclick="enableSSH();" id="enable" />
													<img id="gen_settings_014" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
												<td width="54px;"> &nbsp;</td>
												<td>SSH Port </td>
												<td>
													<input type="text" name="ssh_port" id="SSHPORT" class="ipBox" value="<?php print $ssh_port; ?>" size="4" maxlength="5" />
													<img id="gen_settings_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td> <label for="allow_icmp">Allow ICMP </label> </td>
									<td>
										<?php
										if( $allow_icmp == "1" )
											$allow_icmp_checked = "checked";
										else
											$allow_icmp_checked = "";
										?>
										<input type="checkbox" name="allow_icmp" id="allow_icmp" <?php print $allow_icmp_checked; ?> />
										<img id="gen_settings_015" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							</tbody>
						</table></td></tr>
						</table>
					</td>
<!-- End : Device Settings -->
<!-- Begin : Transcoding Settings -->
					<td style="width: 50%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Transcoding Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="gen_settings_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tr><td>
						<table cellpadding="2" cellspacing="2" class="sub-container-table">
							<tbody>
								<tr>
									<td>Transcoding Card</td>
									<td>
										<span style=" font-weight: bold;"><?php if ($transcodingStatus == 1) print  "Detected"; else print "Not Detected";?></span>
										<img id="gen_settings_016" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<?php
										if ( $enable_transcoding == "1" ) {
											$enableTranscoding = "checked";
										} else {
											$enableTranscoding = "";
										}
									?>
									<td><label for="trans_enable">Enable / Disable</lable></td>
									<td>
										<input type="checkbox" <?php print $enableTranscoding; ?> onchange="transcodingEnable(this);" id="trans_enable" name="trans_enable" />
										<img id="gen_settings_018" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>Transcoding Interface</td>
									<td>
										<!--<span style=" font-weight: bold;" id="TRANS_INTERFACE"><?php if ($transcodingStatus == 1) print $transcodingInterface;?></span>-->
										<span style=" font-weight: bold;" id="TRANS_INTERFACE"></span>
										<img id="gen_settings_017" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
									<td>Transcoding IP Address / Netmask </td>
									<td>
										<input type="text" id="TRANSCODING_IPADDR" value="<?php print $transcodingIP; ?>" size="13" text-align: center" class="ipBox" maxlength="15" name="transcoding_ip_addr" />
										<input type="text" value="<?php print $transcodingNetmask; ?>" size="13" text-align: center" class="ipBox" maxlength="15" id="TRANSCODING_NETMASK" name="transcoding_net_mask" />
										<img id="gen_settings_013" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							</tbody>
						</table></td></tr>
						</table>
					</td>
<!-- End : Transcoding Settings -->
				</table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
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
		enableSSH();
		initValidation();
		showTransInterfaceName();
		enableDisableTranscoding();
		transcodingEnable(en_dis);
	</script>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if(submit == 1) {
			save_Message(val);
		}
	</script>
</html>
