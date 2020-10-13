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
	include_once("Model/global_settings_model.php");
	include_once("Model/action_mapping_model.php");

	$global_settings = GlobalSettingsConfigModel::getInstance();
	$sbc_interface_type = SBCInterfaceType::getInstance();
	$int_types = $sbc_interface_type->getSBCInterfaceType();
	$sbc_nat_type = SBCNatType::getInstance();
	$nat_types = $sbc_nat_type->getSBCNatType();
	$sbc_int_type = array();
	$sbc_nat_type = array();
	while($row = $int_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_int_type[$row['VAL']] = $row['NAME'];
	}
	while($nat_row = $nat_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_nat_type[$nat_row['VAL']] = $nat_row['NAME'];
	}
	$res = 0;
	$page_title = "Create Global Settings Rule";
	$flag = "add";
	$name = "";
	$interface_type = "";
	$signal_port= "";
	$tls = "";
	$tls_port = "";
	$nat = "";
	$nat_type = "";
	$nat_ip = "";
	$comments = "";
	$createdDataNames = array();
	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit Global Settings Rule";
		$row = $global_settings->getGlobalSettingsRule($id);
		if($row) {
			$name = $row[GlobalSettingsConfigModel::$PROFILE];
			$interface_type = $row[GlobalSettingsConfigModel::$INTERFACE];
			$signal_port= $row[GlobalSettingsConfigModel::$SIGNALLING_PORT];
			$tls = $row[GlobalSettingsConfigModel::$TLS];
			$tls_port = $row[GlobalSettingsConfigModel::$TLS_PORT];
			$nat = $row[GlobalSettingsConfigModel::$NAT];
			$nat_type = $row[GlobalSettingsConfigModel::$NAT_TYPE];
			$nat_ip = $row[GlobalSettingsConfigModel::$NAT_IP];
			$comments = $row[GlobalSettingsConfigModel::$COMMENTS];
			while($row = $int_types->fetchArray(SQLITE3_ASSOC)) {
				if ( $interface_type == $row['VAL'] ) {
					$selected_interface = $row['NAME'];
					break;
				}
			}
			while($nat_row = $nat_types->fetchArray(SQLITE3_ASSOC)) {
				if ( $nat_type == $nat_row['VAL'] ) {
					$selected_nat = $nat_row['NAME'];
					break;
				}
			}
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['tls_enable'] == "on")
			$post_tls = "1";
		else
			$post_tls = "";
		if($_POST['nat_enable'] == "on")
			$post_nat = "1";
		else
			$post_nat = "";
		if ($_POST['ip_type'] == "LAN_TRUNK") {
			$post_ip_type = 1;
		} else if ($_POST['ip_type'] == "WAN_TRUNK") {
			$post_ip_type = 2;
		} else if ($_POST['ip_type'] == "LAN_EXTN") {
			$post_ip_type = 3;
		} else if ($_POST['ip_type'] == "WAN_EXTN") {
			$post_ip_type = 4;
		}
		if ($_POST['nat_type'] == "STUN") {
			$post_nat_type = 1;
		} else if ($_POST['nat_type'] == "STATIC") {
			$post_nat_type = 2;
		}
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$global_settings->addGlobalSettingsRule(trim($_POST['name']), $post_ip_type, 
								trim($_POST['signal_port']), $post_tls, 
								trim($_POST['tls_port']), $post_nat, 
								$post_nat_type, trim($_POST['nat_ip']), 
								trim($_POST['comments']));
			$global_settings->createActionList(DEPLOY_MODULES::GLOBAL_SETTINGS_CONFIG, 
							"Global Settings rule added", 
							DEPLOY_MODULES::GLOBAL_SETTINGS_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $global_settings->getGlobalSettingsRule($id);
			print $_POST['id'];
			if($row) {
				if($_POST['tls_enable'] == "on")
					$post_tls = "1";
				else
					$post_tls = "";
				if($_POST['nat_enable'] == "on")
					$post_nat = "1";
				else
					$post_nat = "";
				$name = $row[GlobalSettingsConfigModel::$PROFILE];
				$interface_type = $row[GlobalSettingsConfigModel::$INTERFACE];
				$signal_port= $row[GlobalSettingsConfigModel::$SIGNALLING_PORT];
				$tls = $row[GlobalSettingsConfigModel::$TLS];
				$tls_port = $row[GlobalSettingsConfigModel::$TLS_PORT];
				$nat = $row[GlobalSettingsConfigModel::$NAT];
				$nat_type = $row[GlobalSettingsConfigModel::$NAT_TYPE];
				$nat_ip = $row[GlobalSettingsConfigModel::$NAT_IP];
				$comments = $row[GlobalSettingsConfigModel::$COMMENTS];
				if ($_POST['ip_type'] == "LAN_TRUNK") {
					$post_ip_type = 1;
				}
				else if ($_POST['ip_type'] == "WAN_TRUNK") {
					$post_ip_type = 2;
				} else if ($_POST['ip_type'] == "LAN_EXTN") {
					$post_ip_type = 3;
				} else if ($_POST['ip_type'] == "WAN_EXTN") {
					$post_ip_type = 4;
				}
				if ($_POST['nat_type'] == "STUN") {
					$post_nat_type = 1;
				} else if ($_POST['nat_type'] == "STATIC") {
					$post_nat_type = 2;
				}
				if ( $post_ip_type != $interface_type || 
					trim($_POST['signal_port']) != $signal_port || 
					$post_tls != $tls || trim($_POST['tls_port']) != $tls_port || 
					$post_nat != $nat || $nat_type != $post_nat_type || 
					trim($_POST['nat_ip']) != $nat_ip || 
					trim($_POST['comments']) != $comments ) {
						$global_settings->editGlobalSettingsRule(
							trim($_POST['name']), $post_ip_type, 
							trim($_POST['signal_port']), $post_tls, 
							trim($_POST['tls_port']), $post_nat, 
							$post_nat_type, trim($_POST['nat_ip']), 
							trim($_POST['comments']));
						$global_settings->createActionList(
							DEPLOY_MODULES::GLOBAL_SETTINGS_CONFIG, 
							"Global Settings rule edited", 
							DEPLOY_MODULES::GLOBAL_SETTINGS_CONFIG_STR, "");
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
				$sub("#COMMENTS").attr("data-validation-engine", 
							"validate[custom[comments],maxSize[64]]");
				$sub("#adddeviceaccess").validationEngine('attach', 
							{promptPosition : "bottomRight"});
			}

			function submitData() {
				if($sub("#adddeviceaccess").validationEngine('validate')) {
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

			function closeDialog() {
				parent.closeAddEditDialog();
			}

			function f1(val) {
				if(val == 1) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = "global_settings.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="adddeviceaccess" name="adddeviceaccess" class="popup-form" action="add_edit_global_settings.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>
						<tr>
							<td>Profile Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Interface</td>
							<td width="20"></td>
							<td>
								<select name="ip_type" id="ip_type" class="ipDrpBox">
								<?php
									for ( $j = 1; $j <= count($sbc_int_type); $j++ ) {
										$select_str = "";
										if ( $sbc_int_type[$j] == $selected_interface )
											$select_str = "selected";
								?>
										<option value="<?php print $sbc_int_type[$j];?>" <?php print  $select_str;?> ><?php print $sbc_int_type[$j];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Signaling Port</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="SIGNAL_PORT" name="signal_port" value="<?php echo $signal_port ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td><label for="tls_enable">TLS</label></td>
							<td width="20"></td>
							<td>
							<?php
								if( $tls == "1" )
									$enabled = "checked";
								else
									$enabled = "";
							?>
								<input type="checkbox" name="tls_enable" <?php print $enabled; ?> id="tls_enable" />
								<img id="setup_3-1_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>TLS Port</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="TLS_PORT" name="tls_port" value="<?php echo $tls_port ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td><label for="nat_enable">NAT</label></td>
							<td width="20"></td>
							<td>
							<?php
								if( $nat == "1" )
									$enabled = "checked";
								else
									$enabled = "";
							?>
								<input type="checkbox" name="nat_enable" <?php print $enabled; ?> id="nat_enable" />
								<img id="setup_3-1_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>NAT TYPE</td>
							<td width="20"></td>
							<td>
								<select name="nat_type" id="NAT_TYPE" class="ipDrpBox">
								<?php
									for ( $j = 1; $j <= count($sbc_nat_type); $j++ ) {
										$select_str = "";
										if ( $sbc_nat_type[$j] == $selected_nat )
											$select_str = "selected";
								?>
										<option value="<?php print $sbc_nat_type[$j];?>" <?php print  $select_str;?> ><?php print  $sbc_nat_type[$j];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>NAT IP</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="NAT_IP" name="nat_ip" value="<?php echo $nat_ip ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Comments</td>
							<td width="20"></td>
							<td>
								<textarea cols="30" rows="5" maxlength="64" id="COMMENTS" name="comments"><?php echo $comments ?></textarea>
								<img id="add_rule_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
