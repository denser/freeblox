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
	include_once("Model/fax_profiles_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/device_settings_model.php");
	include_once("Model/media_settings_model.php");
	include_once("Model/sys_calls.php");

	$fax_profiles_config = FaxProfilesConfigModel::getInstance();
	$media_settings_config = MediaConfigModel::getInstance();
	$versionArr = $fax_profiles_config->getFaxVersion();
	$version_count = count($versionArr);
	$maxbitrateArr = $fax_profiles_config->getMaxBitRate();
	$maxbit_count = count($maxbitrateArr);
	$faxmgmtArr = $fax_profiles_config->getFaxMgmt();
	$faxudpArr = $fax_profiles_config->getFaxUdpEC();
	$transcodingInterface = SysCalls::getTranscodingInterface();
	$transCount = $fax_profiles_config->getTranscodingCount($transcodingInterface);
	$transMediaProfilelist = $media_settings_config->getTransMediaProfiles();
	$transMediaProfile = $transMediaProfilelist[0];
	$res = 0;
	$page_title = "Create T38 Fax Profile";
	$flag = "add";
	$name = "";
	$desc = "";
	$version = "";
	$max_bit_rate = "9600";
	$fax_rate_mgmt = "2";
	$fax_ecm_enable = "1";
	$fax_udp_ec = "";
	$createdDataNames = array();
	$faxnames = $fax_profiles_config->getNames();
	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit T38 Fax Profile";
		$row = $fax_profiles_config->getFaxProfileSettings($id);
		if($row) {
			$name = $row[FaxProfilesConfigModel::$NAME];
			$desc = $row[FaxProfilesConfigModel::$DESCRIPTION];
			$version = $row[FaxProfilesConfigModel::$VERSION];
			$max_bit_rate = $row[FaxProfilesConfigModel::$MAX_BIT_RATE];
			$fax_rate_mgmt = $row[FaxProfilesConfigModel::$FAX_RATE_MANAGEMENT];
			$fax_ecm_enable = $row[FaxProfilesConfigModel::$FAX_ECM_ENABLE];
			$fax_udp_ec = $row[FaxProfilesConfigModel::$FAX_UDP_EC];
		}
	}
	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['fax_ecm_enable'] == "on") {
			$post_fax_ecm_enable = "1";
		} else {
			$post_fax_ecm_enable = "";
		}
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$fax_profiles_config->addFaxProfile(trim($_POST['name']), 
							trim($_POST['comments']), 
							$_POST['fax_version'], 
							$_POST['max_bit_rate'], 
							$_POST['fax_rate_mgmt'], 
							$post_fax_ecm_enable, 
							$_POST['fax_udp_ec'], 
							$_POST['media_profile']);
			$fax_profiles_config->createActionList(DEPLOY_MODULES::FAX_PROFILE_CONFIG, 
							"Fax Profile Added", 
							DEPLOY_MODULES::FAX_PROFILE_CONFIG_STR, 
							"");
			$res = 1;
		}
		if ($_POST['submit_action'] == "1") {	 // Edit a rule
			$id = $_POST['id'];
			$row = $fax_profiles_config->getFaxProfileSettings($id);
			if($row) {
				$name = $row[FaxProfilesConfigModel::$NAME];
				$desc = $row[FaxProfilesConfigModel::$DESCRIPTION];
				$version = $row[FaxProfilesConfigModel::$VERSION];
				$max_bit_rate = $row[FaxProfilesConfigModel::$MAX_BIT_RATE];
				$fax_rate_mgmt = $row[FaxProfilesConfigModel::$FAX_RATE_MANAGEMENT];
				$fax_ecm_enable = $row[FaxProfilesConfigModel::$FAX_ECM_ENABLE];
				$fax_udp_ec = $row[FaxProfilesConfigModel::$FAX_UDP_EC];
				if ( $desc != trim($_POST['comments']) || 
					$_POST['fax_version'] != $version || 
					$max_bit_rate != $_POST['max_bit_rate'] || 
					$fax_rate_mgmt != $_POST['fax_rate_mgmt'] || 
					$fax_ecm_enable != $post_fax_ecm_enable || 
					$_POST['fax_udp_ec'] != $fax_udp_ec ) {
						$fax_profiles_config->editFaxProfiles(
								trim($_POST['name']), 
								trim($_POST['comments']), 
								$_POST['fax_version'], 
								$_POST['max_bit_rate'], 
								$_POST['fax_rate_mgmt'], 
								$post_fax_ecm_enable, 
								$_POST['fax_udp_ec'], 
								$_POST['media_profile']);
						$fax_profiles_config->createActionList(
								DEPLOY_MODULES::FAX_PROFILE_CONFIG, 
								"Fax Profile edited", 
								DEPLOY_MODULES::FAX_PROFILE_CONFIG_STR, 
								"");
				}
				$res = 1;
			}
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
				getPageTips("fax_profiles_ttip");
				setTooltip();
				emcEnable();
			});
			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[rulename], minSize[5],maxSize[16]]");
				$sub("#COMMENTS").attr("data-validation-engine", "validate[custom[comments],maxSize[64]]");
				$sub("#addfaxprofiles").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if (flag == "add") {
					var faxArr = new Array();
					var get_fax_val = document.getElementById("NAME").value;
					var post_fax_val = get_fax_val.trim();
					<?php 
					foreach($faxnames as $key=>$val) {
					?>
						faxArr.push('<?php print $val; ?>');
					<?php
					}
					?>
					var daCount = faxArr.length;
					for ( var i=0; i < daCount; i++) {
						if (post_fax_val == faxArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if($sub("#addfaxprofiles").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add") {
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					formObj.submit();
					return true;
				}
			}

			function emcEnable() {
				if($sub("#fax_ecm_enable").is(":checked")) {
					document.getElementById("fax_udp_ec").disabled = false;
					document.getElementById("fax_udp_ec").value = "1";
				} else {
					document.getElementById("fax_udp_ec").disabled = true;
					document.getElementById("fax_udp_ec").value = "1";
				}
			}

			function closeDialog() {
				parent.closeAddEditDialog();
			}

			function f1(val) {
				if(val == 1) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = "fax_profiles.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addfaxprofiles" name="addfaxprofiles" class="popup-form" action="add_edit_fax_profiles.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>
						<tr>
							<td>Media Profile</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="MEDIA_PROFILE" name="media_profile" <?php  echo "readonly='readonly'" ?> value="<?php echo $transMediaProfile; ?>" size="15" maxlength="32" />
							</td>
						</tr>
						<tr>
							<td>Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="15" maxlength="16" />
								<img id="fax_profiles_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $desc; ?>"  size="48" maxlength="64" id="COMMENTS" class="ipBox" name="comments" />
								<img id="fax_profiles_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">T38 Fax Version</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="fax_version" id="fax_version" class="ipDrpBox" onchange="showExternalIP();" Onkeyup="showExternalIP();" Onkeydown="showExternalIP();">
								<?php
								for ($i=0; $i<$version_count;$i++) {
								?>
									<option value="<?php print $versionArr[$i]; ?>" <?php if ($versionArr[$i] == $version ) print "selected=true"; ?>><?php print $versionArr[$i]; ?></option>
								<?php
								}
								?>
								</select>
								<img id="fax_profiles_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">T38 Max Bit Rate</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="max_bit_rate" id="max_bit_rate" class="ipDrpBox" onchange="showExternalIP();" Onkeyup="showExternalIP();" Onkeydown="showExternalIP();">
								<?php
								for ($i=0;$i<$maxbit_count;$i++) {
								?>
									<option value="<?php print $maxbitrateArr[$i]; ?>" <?php if ($maxbitrateArr[$i] == $max_bit_rate) print "selected=true"; ?>><?php print $maxbitrateArr[$i]; ?></option>
								<?php
								}
								?>
								</select>
								<img id="fax_profiles_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">T38 Fax Rate Management</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="fax_rate_mgmt" id="fax_rate_mgmt" class="ipDrpBox" onchange="showExternalIP();" Onkeyup="showExternalIP();" Onkeydown="showExternalIP();">
								<?php
								foreach($faxmgmtArr as $key=>$val) {
								?>
									<option value="<?php print $key; ?>" <?php if ($key == $fax_rate_mgmt ) print "selected=true"; ?>><?php print $val; ?></option>
								<?php
								}
								?>
								</select>
								<img id="fax_profiles_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top"><label for="fax_ecm_enable">T38 Fax ECM Enable</label></td>
							<td width="20"></td>
							<td>
							<?php
							if($fax_ecm_enable == "1")
								$checked="checked";
							else
								$checked="";
							?>
								<input type="checkbox" <?php print $checked; ?> id="fax_ecm_enable" name="fax_ecm_enable" onclick="emcEnable()" />
								<img id="fax_profiles_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">T38 Fax Udp EC</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="fax_udp_ec" id="fax_udp_ec" class="ipDrpBox" onchange="showExternalIP();" Onkeyup="showExternalIP();" Onkeydown="showExternalIP();">
								<?php
								foreach($faxudpArr as $key=>$val) {
								?>
									<option value="<?php print $key; ?>" <?php if ($key == $fax_udp_ec ) print "selected=true"; ?>><?php print $val; ?></option>
								<?php
								}
								?>
								</select>
								<img id="fax_profiles_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="MEDIA_SETTINGS_LIST" id="MEDIA_SETTINGS_LIST" value="" />
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
	</script>
</html>
