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
	include_once("Model/sys_calls.php");
	include_once("Control/date_config.php");
	include_once("Model/action_mapping_model.php");
	include_once('Model/sbc_provisioning.php');
	include_once("Model/devicelist_settings_model.php");

	$_SESSION['pageID'] = "#gen_settings";	
	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$res= 0;
	$hostname = "";
	$sshenable_val = 0;
	$sshport = "";
	$icmp_val = 0;
	$getdata = $devicelist_settings_config->getsshdata();
	$hostname = $getdata['HOST_NAME'];
	$sshenable_val = $getdata['ENABLE_SSH'];
	$sshport = $getdata['SSH_PORT'];
	$icmp_val = $getdata['ALLOW_ICMP'];

	if(isset($_POST) && ($_POST['submit_action'] == 1)) {
		$devicelist_settings_config->updatessh($_POST['host'],$_POST['SSHHIDDEN'],$_POST['sshport'],$_POST['ICMPHIDDEN']);
		$devicelist_settings_config->createActionList(
						DEPLOY_MODULES::GENERAL_SETTING_CONFIG, 
						"General settings Updated", 
						DEPLOY_MODULES::GENERAL_SETTING_CONFIG_STR, "");
		$res = 1;
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
			var submit;
		</script>

		<script type="text/javascript">
			$sub(document).ready(function(){
				initValidation();
				getPageTips("general_settings_ttip");
				setTooltip();
			});

			function submitData() {
				if($sub("#generalsettings").validationEngine('validate')) {
					if($sub("#SSHENABLE_CHK").is(":checked")) {
						$sub("#SSHHIDDEN").val("1");
					}
					if($sub("#ICMP_CHK").is(":checked")) {
						$sub("#ICMPHIDDEN").val("1");
					}
					$sub("#submit_action").val("1");
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function initValidation() {
				$sub("#host").attr("data-validation-engine","validate[required,minSize[3],maxSize[256]]");
				$sub("#sshport").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
				$sub("#generalsettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function enableSSH() {
				var ssh_enable = document.getElementById("SSHENABLE_CHK").checked;
				if( ssh_enable == false ) {
					document.getElementById("sshport").disabled = true;
					document.getElementById("sshport").value = "";
				} else {
					document.getElementById("sshport").disabled = false;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "general_settings.php";
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "general_settings.php";
				}
			}

		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<h1 class="<?php print $class; ?>">General Settings<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="generalsettings" name="generalsettings" method="post" action="general_settings.php" autocomplete="off">
			<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>
					<td style="width: 50%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									General Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="general_settings_ttip_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tr>
								<td>
									<table cellpadding="2" cellspacing="2" class="sub-container-table">
										<tr>
											<td>Host Name</td>
											<td>
												<input type="text" id="host" name="host" value="<?php print $hostname; ?>" size="13" text-align: center" class="ipBox" maxlength="256" name="hostname" />
												<img id="general_settings_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr>
										<td colspan="3">
										<table>
											<tr>
											<td><label for="sshenable1">	Enable SSH</label> </td>
<td width="5"></td>
											<td>
												<input type="checkbox" <?php if($sshenable_val == 1) echo "checked='checked'" ?> id="SSHENABLE_CHK" name="SSHENABLE_CHK" onclick="enableSSH();"/>
												<!--<input id="enablessh" name="enablessh" type="checkbox" <?php if ($sshenable_val == 1) echo "checked=checked"; ?> onclick="enableSSH();"  /> -->
												<img id="general_settings_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
												<td width="30";"> &nbsp;</td>
												<td>SSH Port</td>
												<td>
												<?php 
												$disable = "";
													if($sshenable_val == 0){
														$disable = "disabled";
														$sshport = "";
													}
												?>
													<input type="text" name="sshport" id="sshport" class="ipBox" value="<?php print $sshport; ?>" <?php echo $disable;?> size="4" maxlength="5" />
													<img id="general_settings_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
											</tr>
											</tr>
											<tr style="display:none;">
											<td>
											<label for="icmp">Allow ICMP </label>
											</td>
<td></td>
												<td>
												<input type="checkbox" <?php if($icmp_val == 1) echo "checked='checked'" ?> id="ICMP_CHK" name="ICMP_CHK" />
												<img id="general_settings_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							</table>
							<input type="hidden" name="submit_action"  id="submit_action" value="0" />
							<input type="hidden" id="SSHHIDDEN" name="SSHHIDDEN" value="0" /> 
							<input type="hidden" id="ICMPHIDDEN" name="ICMPHIDDEN" value="0" />
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_generalsettings" name="apply_generalsettings" onclick="submitData();"><span>Apply</span></a>
				<a class="btn-grey-wide-l" id="reset_default" onclick="resetData();"><span>Cancel</span></a>
			</div>
			</form>
		</div>
	</body>	
	<script type="text/javascript">
		initValidation();
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
