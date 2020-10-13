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
	include_once("Model/firewall_model.php");
	include_once("Model/action_mapping_model.php");
	include_once('Model/devicelist_settings_model.php');

	$devlist = DevicelistSettingsModel::getInstance();
	$firewall = FirewallConfigModel::getInstance();
	$action_mapping_model = ActionMappingModel::getInstance();
	$action_names = $action_mapping_model->getActionMapping();
	$action_str_array = array();
	$action_val_array = array();
	while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
		$action_str_array[$row['VAL']] = $row['NAME'];
		$action_val_array[$row['NAME']] = $row['VAL'];
	}
	$fwNames = $firewall->getNames();
	$fwNamesCount = count($fwNames);
	$wlaninterfaces = $devlist-> getWaninterface();
	$res = 0;
	$page_title = "Create Firewall Rule";
	$flag = "add";
	$name = "";
	$enabled = "";
	$src_type = "";
	$src_address = "";
	$dst_type = "";
	$dst_address = "";
	$protocol = "";
	$port = "";
	$action = "";
	$dst_iface = "";

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit Firewall Rule";
		$row = $firewall->getFirewallRule($id);
		if($row) {
			$name = $row[FirewallConfigModel::$NAME];
			$enabled = $row[FirewallConfigModel::$ENABLED];
			$src_type = $row[FirewallConfigModel::$SRC_TYPE];
			if( $src_type == SBC_CONSTANTS::IP_TYPE_ANY )
				$src_type = SBC_CONSTANTS::IP_TYPE_ANY_STR;
			else if( $src_type == SBC_CONSTANTS::IP_TYPE_HOST )
				$src_type = SBC_CONSTANTS::IP_TYPE_HOST_STR;
			else if( $src_type == SBC_CONSTANTS::IP_TYPE_NETWORK )
				$src_type = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
			else if( $src_type == SBC_CONSTANTS::IP_TYPE_RANGE )
				$src_type = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
			else if( $src_type == SBC_CONSTANTS::MAC_TYPE_HOST )
				$src_type = SBC_CONSTANTS::MAC_TYPE_HOST_STR;
			$src_address = $row[FirewallConfigModel::$SRC_ADDRESS];
			$dst_type = $row[FirewallConfigModel::$DST_TYPE];
			if( $dst_type == SBC_CONSTANTS::IP_TYPE_ANY)
				$dst_type = SBC_CONSTANTS::IP_TYPE_ANY_STR;
			else if( $dst_type == SBC_CONSTANTS::IP_TYPE_HOST )
				$dst_type = SBC_CONSTANTS::IP_TYPE_HOST_STR;
			else if( $dst_type == SBC_CONSTANTS::IP_TYPE_NETWORK)
				$dst_type = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
			else if( $ip_val == SBC_CONSTANTS::IP_TYPE_RANGE )
				$dst_type = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
			$dst_address = $row[FirewallConfigModel::$DST_ADDRESS];
			$protocol = $row[FirewallConfigModel::$PROTOCOL];
			$port = $row[FirewallConfigModel::$PORT];
			$action = $row[FirewallConfigModel::$ACTION];
			$dst_iface = $row[FirewallConfigModel::$DST_IFACE];
			$action_names = $action_mapping_model->getActionMapping();
			while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
				if ( $action == $row['VAL'] ) {
					$selected_action = $row['NAME'];
					break;
				}
			}
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enable'] == "on")
			$post_enable = "1";
		else
			$post_enable = "";
		if( $_POST['src_type'] == SBC_CONSTANTS::IP_TYPE_ANY_STR )
			$src_type = SBC_CONSTANTS::IP_TYPE_ANY;
		if( $_POST['src_type'] == SBC_CONSTANTS::IP_TYPE_HOST_STR )
			$src_type = SBC_CONSTANTS::IP_TYPE_HOST;
		if( $_POST['src_type'] == SBC_CONSTANTS::IP_TYPE_NETWORK_STR)
			$src_type = SBC_CONSTANTS::IP_TYPE_NETWORK;
		if( $_POST['src_type'] == SBC_CONSTANTS::IP_TYPE_RANGE_STR )
			$src_type = SBC_CONSTANTS::IP_TYPE_RANGE;
		if( $_POST['src_type'] == SBC_CONSTANTS::MAC_TYPE_HOST_STR )
			$src_type = SBC_CONSTANTS::MAC_TYPE_HOST;
		if( $_POST['dst_type'] == SBC_CONSTANTS::IP_TYPE_ANY_STR )
			$dst_type = SBC_CONSTANTS::IP_TYPE_ANY;
		if( $_POST['dst_type'] == SBC_CONSTANTS::IP_TYPE_HOST_STR )
			$dst_type = SBC_CONSTANTS::IP_TYPE_HOST;
		if( $_POST['dst_type'] == SBC_CONSTANTS::IP_TYPE_NETWORK_STR )
			$dst_type = SBC_CONSTANTS::IP_TYPE_NETWORK;
		if( $_POST['dst_type'] == SBC_CONSTANTS::IP_TYPE_RANGE_STR )
			$dst_type = SBC_CONSTANTS::IP_TYPE_RANGE;
		if( $_POST['action'] == "Block")
			$post_action = "1";
		if( $_POST['action'] == "Allow")
			$post_action = "2";
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$firewall->addFirewallRule(trim($_POST['name']), $post_enable, $src_type, 
						trim($_POST['src_address']), $dst_type, 
						trim($_POST['dst_address']), $_POST['protocol'], 
						trim($_POST['port']), $post_action, 
						trim($_POST['dst_iface']));
			$firewall->createActionList(DEPLOY_MODULES::FIREWALL_CONFIG, 
						"Firewall rule added", 
						DEPLOY_MODULES::FIREWALL_CONFIG_STR, "");
			$res = 1;
		}
		$id = $_POST['id'];
		$rows = $firewall->getFirewallRule($id);
		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$selected_action = "";
			$action_names = $action_mapping_model->getActionMapping();
			while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
				if ( $action == $row['VAL'] ) {
					$selected_action = $row['NAME'];
					break;
				}
			}
			if($rows) {
				$name = $rows[FirewallConfigModel::$NAME];
				$enabled = $rows[FirewallConfigModel::$ENABLED];
				$Src_type = $rows[FirewallConfigModel::$SRC_TYPE];
				$src_address = $rows[FirewallConfigModel::$SRC_ADDRESS];
				$Dst_type = $rows[FirewallConfigModel::$DST_TYPE];
				$dst_address = $rows[FirewallConfigModel::$DST_ADDRESS];
				$protocol = $rows[FirewallConfigModel::$PROTOCOL];
				$port = $rows[FirewallConfigModel::$PORT];
				$action = $rows[FirewallConfigModel::$ACTION];
				$dst_iface = $row[FirewallConfigModel::$DST_IFACE];
				for($i=0;$i<count($action_str_array);$i++) {
					if ( $action == $action_val_array[$i] ) {
						$selected_action = $action_str_array[$i];
						break;
					}
				}
				if( $_POST['action'] == "Block")
					$post_action = "1";
				if( $_POST['action'] == "Allow")
					$post_action = "2";
				if( $post_enable != $enabled || $src_type != $Src_type || 
					trim($_POST['src_address']) != $src_address || 
					$dst_type != $Dst_type || 
					trim($_POST['dst_address']) != $dst_address || 
					$_POST['protocol'] != $protocol || 
					trim($_POST['port']) != $port || $post_action != $action || 
					trim($_POST['dst_iface']) != $dst_iface) {
						$firewall->editFirewallRule(trim($_POST['name']), 
								$post_enable, $src_type, 
								trim($_POST['src_address']), 
								$dst_type, trim($_POST['dst_address']), 
								$_POST['protocol'], 
								trim($_POST['port']), $post_action, 
								trim($_POST['dst_iface']));
						$firewall->createActionList(
								DEPLOY_MODULES::FIREWALL_CONFIG, 
								"Firewall rule edited", 
								DEPLOY_MODULES::FIREWALL_CONFIG_STR, "");
				}
				$res = 1;
			}
		}
	}
// Set Enable /Disable parameter via Ajax Post request
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['en_fw'] == "true")
			$post_enable_val = "1";
		else if($_POST['en_fw'] == "false")
			$post_enable_val = "";
		$fw_name = str_replace('@',' ',$_POST['fw_name']);
		$rows = $firewall->getFirewallRule($fw_name);
		$db_val = $rows[FirewallConfigModel::$ENABLED];
		if( $post_enable_val != $db_val ) {
			$firewall->setFirewallEnable($fw_name,$post_enable_val);
			$firewall->createActionList(DEPLOY_MODULES::FIREWALL_CONFIG, 
						"Firewall rule edited", 
						DEPLOY_MODULES::FIREWALL_CONFIG_STR, "");
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
				setIPType();
			});

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[nwrulename],minSize[5],maxSize[16]]");
				$sub('#src_type').on('change', function() {{
					var src_type = $sub("#src_type option:selected").val();
					if( src_type == "IP_HOST" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( src_type == "IP_NETWORK" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( src_type == "IP_RANGE" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
					if( src_type == "MAC_ADDR" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[macaddr]]");
					}
				}
				});
				$sub('#dst_type').on('change', function() {{
					var dst_type = $sub("#dst_type option:selected").val();
					if( dst_type == "IP_HOST" ) {
						$sub("#DST_ADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( dst_type == "IP_NETWORK" ) {
						$sub("#DST_ADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( dst_type == "IP_RANGE" ) {
						$sub("#DST_ADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
				}
				});
				$sub( document ).ready(function() {
					var protocol_type = $sub("#protocol option:selected").val();
					if( protocol_type == "icmp") {
						$sub("#PORT").attr("data-validation-engine", "validate[required,custom[icmpPorts]]");
					} else {
						$sub("#PORT").attr("data-validation-engine", "validate[required,custom[commaSepPorts]]");
					}
					var dst_type = $sub("#dst_type option:selected").val();
					if( dst_type == "IP_HOST" ) {
						$sub("#DST_ADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( dst_type == "IP_NETWORK" ) {
						$sub("#DST_ADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( dst_type == "IP_RANGE" ) {
						$sub("#DST_ADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
					var src_type = $sub("#src_type option:selected").val();
					if( src_type == "IP_HOST" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( src_type == "IP_NETWORK" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( src_type == "IP_RANGE" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
					if( src_type == "MAC_ADDR" ) {
						$sub("#SRC_ADDR").attr("data-validation-engine", "validate[required,custom[macaddr]]");
					}
				});
				$sub('#protocol').on('change', function() {{
					var protocol_type = $sub("#protocol option:selected").val();
					if( protocol_type == "icmp") {
						$sub("#PORT").attr("data-validation-engine", "validate[required,custom[icmpPorts]]");
					} else {
						$sub("#PORT").attr("data-validation-engine", "validate[required,custom[commaSepPorts]]");
					}
				}});
				$sub("#addfirewall").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if (flag == "add") {
					var fwArr = new Array();
					var post_fw_val = document.getElementById("NAME").value;
					<?php
						foreach($fwNames as $key=>$val) {
					?>
							fwArr.push('<?php print $val; ?>');
					<?php
						}
					?>
					var fwCount = fwArr.length;
					for ( var i=0; i < fwCount; i++) {
						if (post_fw_val == fwArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if($sub("#addfirewall").validationEngine('validate')) {
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
					parent.document.getElementById("container").src = "firewall.php";
				}
			}

			var srcAddr = '<?php echo $src_address; ?>';
			var dstAddr = '<?php echo $dst_address; ?>';

			function setIPType() {
				var srcType = document.getElementById("src_type").value;
				var dstType = document.getElementById("dst_type").value;
				if( srcType == "ANY") {
					document.getElementById("SRC_ADDR").disabled = true;
				} else {
					document.getElementById("SRC_ADDR").disabled = false;
				}
				if ( dstType == "ANY" ) {
					document.getElementById("DST_ADDR").disabled = true;
				} else {
					document.getElementById("DST_ADDR").disabled = false;
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addfirewall" name="addfirewall" class="popup-form" action="add_edit_firewall.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display:none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>
						<tr>
							<td>Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText" title=""></span>
							</td>
						</tr>
						<tr>
							<td><label for="enable">Enabled</label></td>
							<td width="20"></td>
							<td>
							<?php
								if( $enabled == "1" )
									$enabled = "checked";
								else
									$enabled = "";
							?>
								<input type="checkbox" name="enable" <?php print $enabled; ?> id="enable" />
								<img id="add_rule_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Src Type</td>
							<td width="20"></td>
							<td>
								<select name="src_type" id="src_type" class="ipDrpBox" onchange="setIPType();" onkeydown="setIPType();" onkeyup="setIPType();">
								<?php
									$Srctype = FirewallConfigModel::getIPTypeOptions();
									for( $i =0; $i < count($Srctype); $i++) {
								?>
										<option <?php if($src_type == $Srctype[$i])  print "selected = true"; ?>value="<?php print $Srctype[$i];?>"><?php print $Srctype[$i];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Src Address</td>
							<td width="20"></td>
							<td>
							<?php
								if( $src_type == SBC_CONSTANTS::IP_TYPE_ANY_STR ) {
							?>
									<input type="text" class="ipBox" id="SRC_ADDR" name="src_address" value="" size="30" maxlength="64" />
							<?php
								} else {
							?>
									<input type="text" class="ipBox" id="SRC_ADDR" name="src_address" value="<?php echo $src_address; ?>" size="30" maxlength="64" />
							<?php
								}
							?>
									<img id="add_rule_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Dst Type</td>
							<td width="20"></td>
							<td>
								<select name="dst_type" id="dst_type" class="ipDrpBox" onchange="setIPType();" onkeyup="setIPType();" onkeydown="setIPType();">
								<?php
									$Dsttype = FirewallConfigModel::getIPTypeOptions();
									for( $i =0; $i < count($Dsttype) -1; $i++) {
								?>
										<option <?php if($dst_type == $Dsttype[$i])  print "selected = true"; ?>value="<?php print $Dsttype[$i];?>"><?php print $Dsttype[$i];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Dst Address</td>
							<td width="20"></td>
							<td>
							<?php
								if( $dst_type == SBC_CONSTANTS::IP_TYPE_ANY_STR ) {
							?>
									<input type="text" class="ipBox" id="DST_ADDR" name="dst_address" value="" size="30" maxlength="64"/>
							<?php
								} else {
							?>
									<input type="text" class="ipBox" id="DST_ADDR" name="dst_address" value="<?php echo $dst_address; ?>" size="30" maxlength="64" />
							<?php
								}
							?>
									<img id="add_rule_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Protocol</td>
							<td width="20"></td>
							<td>
								<select name="protocol" id="protocol" class="ipDrpBox">
								<?php
									$protocol_opt = FirewallConfigModel::getProtocolOptions();
									for( $i =0; $i < count($protocol_opt); $i++) {
								?>
										<option <?php if($protocol == $protocol_opt[$i])  print "selected = true"; ?>value="<?php print $protocol_opt[$i];?>"><?php print $protocol_opt[$i];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr style="display:none"> 
							<td>Dst Interface</td>
							<td width="20"></td>
							<td>
								<select name="dst_iface" id="dst_iface" class="ipDrpBox">
								<?php
									if(count($wlaninterfaces) != 0) {
										foreach($wlaninterfaces as $val) {
								?>
											<option value="<?php echo $val;?>" <?php if($val == $dst_iface) echo "selected"; ?>><?php echo $val;?></option>
								<?php
										}
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td id="port_label">Port</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="PORT" name="port" value="<?php echo $port; ?>" size="" maxlength="64" />
								<img id="add_rule_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Action</td>
							<td width="20"></td>
							<td>
							<select name="action" class="ipDrpBox">
							<?php
								for ( $j = 1; $j <= count($action_str_array)-2; $j++ ) {
									$select_str = "";
									if ( $action_str_array[$j] == $selected_action )
										$select_str = "selected";
							?>
									<option value="<?php print $action_str_array[$j];?>" <?php print  $select_str;?> ><?php print  $action_str_array[$j];?></option>
							<?php
								}
							?>
							</select>
							<img id="add_rule_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<!--tr>
							<td><label for="enable_nat">Enable NAT(Masquerade)</label></td>
							<td width="20"></td>
							<td>
							<?php
								if( $enable_nat == "1" )
									$enable_nat = "checked";
								else
									$enable_nat = "";
							?>
								<input type="checkbox" name="enable_nat" <?php print $enable_nat; ?> id="enable_nat" />
								<img id="setup_3-1_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr -->
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="FIREWALL_RULE_LIST" id="FIREWALL_RULE_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
	<script type="text/javascript">
		setIPType();
		initValidation();
	</script>
</html>
