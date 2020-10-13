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
	include_once("Model/extension_profiles_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/global_settings_model.php");

	$sbc_media_type = SBCMediaType::getInstance();
	$media_types = $sbc_media_type->getSBCMediaType();
	$sbc_transport_type = SBCTransportType::getInstance();
	$transport_types = $sbc_transport_type->getSBCTransportType();
	$sbc_media = array();
	$sbc_transport = array();
	while($row = $media_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_media[$row['VAL']] = $row['NAME'];
	}
	while($tr_row = $transport_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_transport[$tr_row['VAL']] = $tr_row['NAME'];
	}
	$extension_profiles = ExtensionProfilesConfigModel::getInstance();
	$global_settings =  GlobalSettingsConfigModel::getInstance();
	$global_settingsRules = $global_settings->getGlobalSettingsRules();
	$ib_if = "3";
	$inbound_profs = $global_settings->getInboundProfiles($ib_if);
	$ob_if = "4";
	$outbound_profs = $global_settings->getInboundProfiles($ob_if);
	$i = 0;
	while ($row = $outbound_profs->fetchArray()) {
		$outbound_profiles[$i] = $row[0];
		$i++;
	}
	$j = 0;
	while ($row = $inbound_profs->fetchArray()) {
		$inbound_profiles[$j] = $row[0];
		$j++;
	}
	$res = 0;
	$page_title = "Create Extension Profiles Rule";
	$flag = "add";
	$extension = "";
	$redirect_proxy_ip = "";
	$redirect_proxy_port = "";
	$user = "";
	$password = "";
	$registration_int = "";
	$inbound_sbc_prof = "";
	$inbound_sbc_transport = "";
	$inbound_media_type = "";
	$outbound_sbc_prof = "";
	$outbound_media_type = "";
	$createdDataNames = array();
	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('_', ' ', $val);
		$flag = "edit";
		$page_title = "Edit Extension Profiles Rule";
		$row = $extension_profiles->getExtensionProfilesRule($id);
		if($row) {
			$extension = $row[ExtensionProfilesConfigModel::$EXTENSION];
			$redirect_proxy_ip = $row[ExtensionProfilesConfigModel::$REDIRECT_PROXY_IP];
			$redirect_proxy_port = $row[ExtensionProfilesConfigModel::$REDIRECT_PROXY_PORT];
			$user = $row[ExtensionProfilesConfigModel::$USER];
			$password = $row[ExtensionProfilesConfigModel::$PASSWD];
			$registration_int = $row[ExtensionProfilesConfigModel::$REGISTRATION_INTERVAL];
			$inbound_sbc_prof = $row[ExtensionProfilesConfigModel::$INBOUND_SBC_PROFILE];
			$inbound_sbc_transport = $row[ExtensionProfilesConfigModel::$INBOUND_SBC_TRANSPORT];
			$inbound_media_type = $row[ExtensionProfilesConfigModel::$INBOUND_MEDIA_TYPE];
			$outbound_sbc_prof = $row[ExtensionProfilesConfigModel::$OUTBOUND_SBC_PROFILE];
			$outbound_media_type = $row[ExtensionProfilesConfigModel::$OUTBOUND_MEDIA_TYPE];
			while($tr_row = $transport_types->fetchArray(SQLITE3_ASSOC)) {
				if ( $inbound_sbc_transport == $tr_row['VAL']) {
					$selected_transport = $tr_row['NAME'];
					break;
				}
			}
			while($row = $media_types->fetchArray(SQLITE3_ASSOC)) {
				if ( $inbound_media_type == $row['VAL']) {
					$selected_ib_media = $row['NAME'];
					break;
				}
			}
			while($row = $media_types->fetchArray(SQLITE3_ASSOC)) {
				if ( $outbound_media_type == $row['VAL']) {
					$selected_ob_media = $row['NAME'];
					break;
				}
			}
			$j = 0;
			while ($row = $inbound_profs->fetchArray()) {
				if ( $inbound_sbc_prof == $inbound_profiles[$j]) {
					$selected_ib_sbc_prof = $inbound_profiles[$j];
					break;
				}
				$j++;
			}
			$i = 0;
			while ($row = $outbound_profs->fetchArray()) {
				if ($outbound_sbc_prof == $outbound_profiles[$i]) {
					$selected_ob_sbc_prof = $outbound_profiles[$i];
				}
				$i++;
			}
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enable'] == "on")
			$post_enable = "1";
		else
			$post_enable = "";
		if ($_POST['sip_transport'] == "TCP") {
			$post_sip_transport = 1;
		} else if ($_POST['sip_transport'] == "UDP") {
			$post_sip_transport = 2;
		}
		if ($_POST['inbound_media'] == "RTP") {
			$post_ib_media = 1;
		}
		if ($_POST['outbound_media'] == "RTP") {
			$post_ob_media = 1;
		}
		if ( $_POST['inbound_sip_prof'] == '--No Profile--') {
			$post_ib_prof = NULL;
		} else {
			$post_ib_prof = $_POST['inbound_sip_prof'];
		}
		if ( $_POST['outbound_sip_prof'] == '--No Profile--') {
			$post_ob_prof = NULL;
		} else {
			$post_ob_prof = $_POST['outbound_sip_prof'];
		}
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$extension_profiles->addExtensionProfilesRule(trim($_POST['extension']),
										trim($_POST['ip']),
										trim($_POST['port']),
										trim($_POST['user']),
										trim( $_POST['passwd']),
										trim($_POST['reg_int']),
										$post_ib_prof,
										$post_sip_transport,
										$post_ib_media,
										$post_ob_prof,
										$post_ob_media);
			$extension_profiles->createActionList(
							DEPLOY_MODULES::EXTENSION_PROFILES_CONFIG, 
							"Extension Profile rule added", 
							DEPLOY_MODULES::EXTENSION_PROFILES_CONFIG_STR, 
							"");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $extension_profiles->getExtensionProfilesRule($id);
			if($row) {
				$extension = $row[ExtensionProfilesConfigModel::$EXTENSION];
				$redirect_proxy_ip = $row[ExtensionProfilesConfigModel::$REDIRECT_PROXY_IP];
				$redirect_proxy_port = $row[ExtensionProfilesConfigModel::$REDIRECT_PROXY_PORT];
				$user = $row[ExtensionProfilesConfigModel::$USER];
				$password = $row[ExtensionProfilesConfigModel::$PASSWD];
				$registration_int = $row[ExtensionProfilesConfigModel::$REGISTRATION_INTERVAL];
				$inbound_sbc_prof = $row[ExtensionProfilesConfigModel::$INBOUND_SBC_PROFILE];
				$inbound_sbc_transport = $row[ExtensionProfilesConfigModel::$INBOUND_SBC_TRANSPORT];
				$inbound_media_type = $row[ExtensionProfilesConfigModel::$INBOUND_MEDIA_TYPE];
				$outbound_sbc_prof = $row[ExtensionProfilesConfigModel::$OUTBOUND_SBC_PROFILE];
				$outbound_media_type = $row[ExtensionProfilesConfigModel::$OUTBOUND_MEDIA_TYPE];
				if ($_POST['sip_transport'] == "TCP") {
					$post_sip_transport = 1;
				} else if ($_POST['sip_transport'] == "UCP") {
					$post_sip_transport = 2;
				}
				if ($_POST['inbound_media'] == "RTP") {
					$post_ib_media = 1;
				}
				if ($_POST['outbound_media'] == "RTP") {
					$post_ob_media = 1;
				}
				if ( trim($_POST['ip']) != $redirect_proxy_ip || 
					trim($_POST['port']) != $redirect_proxy_port || 
					trim($_POST['user']) != $user || 
					trim( $_POST['passwd']) != $password || 
					trim($_POST['reg_int']) != $registration_int || 
					$_POST['inbound_sip_prof'] != $inbound_sbc_prof || 
					$post_sip_transport != $inbound_sbc_transport || 
					$post_ib_media != $inbound_media_type || 
					$_POST['outbound_sip_prof'] != $outbound_sbc_prof || 
					$post_ob_media != $outbound_media_type) {
						$extension_profiles->editExtensionProfilesRule(
								$_POST['extension'], 
								trim($_POST['ip']), 
								trim($_POST['port']), 
								trim($_POST['user']), 
								trim( $_POST['passwd']), 
								trim($_POST['reg_int']), 
								$_POST['inbound_sip_prof'], 
								$post_sip_transport, 
								$post_ib_media, 
								$_POST['outbound_sip_prof'], 
								$post_ob_media);
						$extension_profiles->createActionList(
							DEPLOY_MODULES::EXTENSION_PROFILES_CONFIG, 
							"Extension Profile rule edited", 
							DEPLOY_MODULES::EXTENSION_PROFILES_CONFIG_STR, 
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
				getPageTips("add_rule");
				setTooltip();
			});

			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[onlyLetterNumber], minSize[5],maxSize[32]]");
				$sub('#ip_type').on('change', function() {{
					var ip_type = $sub("#ip_type option:selected").val();
					if( ip_type == "HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( ip_type == "NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( ip_type == "RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
				}});
				$sub(document).ready(function() {
					var ip_type = $sub("#ip_type option:selected").val();
					if( ip_type == "HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( ip_type == "NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( ip_type == "RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
				});
				$sub("#COMMENTS").attr("data-validation-engine", "validate[custom[comments],maxSize[64]]");
				$sub("#addextensionprofiles").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				if($sub("#addextensionprofiles").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
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
					parent.document.getElementById("container").src = "extension_profiles.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addextensionprofiles" name="addextensionprofiles" class="popup-form" action="add_edit_extension_profiles.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $extension ?>" />
							</td>
						</tr>
						<tr>
							<td>Extension</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="EXTENSION" name="extension" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $extension ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Redirect Proxy IP</td>
							<td width="20"></td>
							<td>
								<table>
									<tr>
										<td>
											<input type="text" class="ipBox" id="REDIRECT_IP" name="ip" value="<?php echo $redirect_proxy_ip ?>" size="" maxlength="" />
											<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										<td>Port</td>
										<td>
											<input type="text" class="ipBox" id="REDIRECT_PORT" name="port" value="<?php echo $redirect_proxy_port ?>" size="4" maxlength="" />
											<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>SIP UserName</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="USER" name="user" value="<?php echo $user ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>SIP Password</td>
							<td width="20"></td>
							<td>
								<input type="password" class="ipBox" id="PASSWD" name="passwd" value="<?php echo $password ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Registration Interval</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="REG_INT" name="reg_int" value="<?php echo $registration_int ?>" size="4" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Inbound SIP Profile</td>
							<td width="20"></td>
							<td>
								<table>
									<tr>
										<td>
											<select name="inbound_sip_prof" id="inbound_sip_prof" class="ipDrpBox" >
											<?php 
											if (count($inbound_profiles) < 1) { 
											?>
												<option>--No Profile--</option>
											<?php 
											}
											for ( $j = 0; $j < count($inbound_profiles); $j++ ) {
												$select_str = "";
												if ( $inbound_profiles[$j] == $selected_ib_sbc_prof )
													$select_str = "selected";
											?>
												<option value="<?php print $inbound_profiles[$j];?>" <?php print $select_str;?> ><?php print  $inbound_profiles[$j];?></option>
											<?php
											}
											?>
											</select>
											<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										<td>SIP Transport</td>
										<td>
											<select name="sip_transport" id="sip_transport" class="ipDrpBox" >
											<?php
											for ( $k = 1; $k <= count($sbc_transport); $k++ ) {
												$select_str = "";
												if ( $sbc_transport[$k] == $selected_transport )
													$select_str = "selected";
											?>
												<option value="<?php print $sbc_transport[$k];?>" <?php print $select_str;?> ><?php print $sbc_transport[$k];?></option>
											<?php
											}
											?>
											</select>
											<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Inbound Media</td>
							<td width="20"></td>
							<td>
								<select name="inbound_media" id="inbound_media" class="ipDrpBox" >
								<?php
									for ( $j = 1; $j <= count($sbc_media); $j++ ) {
									$select_str = "";
									if ( $sbc_media[$j] == $selected_ib_media )
										$select_str = "selected";
								?>
									<option value="<?php print $sbc_media[$j];?>" <?php print  $select_str;?> ><?php print $sbc_media[$j];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Outbound SIP Profile</td>
							<td width="20"></td>
							<td>
								<select name="outbound_sip_prof" id="outbound_sip_prof" class="ipDrpBox" >
									<?php 
									if (count($outbound_profiles) < 1) {
									?>
										<option>--No Profile--</option>
									<?php
									}
									for ( $j = 0; $j < count($outbound_profiles); $j++ ) {
										$select_str = "";
										if ( $outbound_profiles[$j] == $selected_ob_sbc_prof )
											$select_str = "selected";
									?>
										<option value="<?php print $outbound_profiles[$j];?>" <?php print $select_str;?> ><?php print  $outbound_profiles[$j];?></option>
									<?php
									}
									?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Outband Media</td>
							<td width="20"></td>
							<td>
								<select name="outbound_media" id="outbound_media" class="ipDrpBox" >
								<?php
								for ( $j = 1; $j <= count($sbc_media); $j++ ) {
									$select_str = "";
									if ( $sbc_media[$j] == $selected_ob_media )
										$select_str = "selected";
								?>
									<option value="<?php print $sbc_media[$j];?>" <?php print  $select_str;?> ><?php print $sbc_media[$j];?></option>
								<?php
								}
								?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
	</script>
</html>
