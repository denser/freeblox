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
	include_once("Model/device_access_model.php");
	$device_access = DeviceAccessConfigModel::getInstance();

	$res = 0;
	$page_title = "Create Device Access Rule";
	$flag = "add";
	$name = "";
	$ip_type = "";
	$net_address = "";
	$enabled = "";
	$comments = "";
	$protocols="";
	$port="";
	$createdDataNames = array();
	$daNames = $device_access->getNames();
	$daNamesCount = count($daNames);

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit Device Access Rule";
		$row = $device_access->getDeviceAccessRule($id);
		if($row) {
			$name = $row[DeviceAccessConfigModel::$NAME];
			$ip_val = $row[DeviceAccessConfigModel::$IP_TYPE];
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_ANY)
				$ip_val = SBC_CONSTANTS::IP_TYPE_ANY_STR;
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_HOST)
				$ip_val = SBC_CONSTANTS::IP_TYPE_HOST_STR;
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_NETWORK)
				$ip_val = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_RANGE)
				$ip_val = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
			if( $ip_val == SBC_CONSTANTS::MAC_TYPE_HOST)
				$ip_val = SBC_CONSTANTS::MAC_TYPE_HOST_STR;
			$net_address = $row[DeviceAccessConfigModel::$NET_ADDRESS];
			$enabled = $row[DeviceAccessConfigModel::$ENABLED];
			$comments = $row[DeviceAccessConfigModel::$COMMENTS];
			$protocols= $row[DeviceAccessConfigModel::$PROTOCOL];
			$port= $row[DeviceAccessConfigModel::$PORT];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enable'] == "on")
			$post_enable = "1";
		else
			$post_enable = "";

		if( $_POST['ip_type'] == SBC_CONSTANTS::IP_TYPE_ANY_STR)
			$ip_type = SBC_CONSTANTS::IP_TYPE_ANY;
		if( $_POST['ip_type'] == SBC_CONSTANTS::IP_TYPE_HOST_STR)
			$ip_type = SBC_CONSTANTS::IP_TYPE_HOST;
		if( $_POST['ip_type'] == SBC_CONSTANTS::IP_TYPE_NETWORK_STR)
			$ip_type = SBC_CONSTANTS::IP_TYPE_NETWORK;
		if( $_POST['ip_type'] == SBC_CONSTANTS::IP_TYPE_RANGE_STR)
			$ip_type = SBC_CONSTANTS::IP_TYPE_RANGE;
		if( $_POST['ip_type'] == SBC_CONSTANTS::MAC_TYPE_HOST_STR)
			$ip_type = SBC_CONSTANTS::MAC_TYPE_HOST;
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$device_access->addDeviceAccessRule(trim($_POST['name']), $ip_type, 
						trim($_POST['net_address']), $post_enable, 
						trim($_POST['comments']), trim($_POST['protocols']), 
						trim($_POST['port']));
			$device_access->createActionList(DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG, 
						"Device Access rule added", 
						DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG_STR, "");
			$res = 1;
		}
		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $device_access->getDeviceAccessRule($id);
			if($row) {
				$ip_val = $row[DeviceAccessConfigModel::$IP_TYPE];
				$net_address = $row[DeviceAccessConfigModel::$NET_ADDRESS];
				$enabled = $row[DeviceAccessConfigModel::$ENABLED];
				$comments = $row[DeviceAccessConfigModel::$COMMENTS];
				$protocols= $row[DeviceAccessConfigModel::$PROTOCOL];
				$port= $row[DeviceAccessConfigModel::$PORT];
				if( $ip_val != $ip_type || $net_address != trim($_POST['net_address']) 
					|| $enabled != $post_enable || 
					trim($_POST['comments']) != $comments || 
					trim($_POST['protocols']) != $protocols || 
					trim($_POST['port']) != $port) {
						$device_access->editDeviceAccessRule(
							trim($_POST['name']), $ip_type, 
							trim($_POST['net_address']), 
							$post_enable, trim($_POST['comments']), 
							trim($_POST['protocols']), 
							trim($_POST['port']));
						$device_access->createActionList(
							DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG, 
							"Device Access rule edited", 
							DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG_STR,
							"");
				}
				$res = 1;
			}
		}
	}
// Set Enable / Disable parameter via Ajax Post request
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['en_da'] == "true")
			$post_enable_val = "1";
		else if($_POST['en_da'] == "false")
			$post_enable_val = "";
		$da_name = str_replace('@',' ',$_POST['da_name']);
		$rows = $device_access->getDeviceAccessRule($da_name);
		$db_val = $rows[DeviceAccessConfigModel::$ENABLED];
		if( $post_enable_val != $db_val ) {
			$device_access->setDeviceAccessEnable($da_name,$post_enable_val);
			$device_access->createActionList(DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG, 
					"Device Access rule edited", 
					DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG_STR, "");
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
			$sub(document).ready(function() {
				initValidation();
				getPageTips("add_rule");
				setTooltip();
				setprotocols();
			});
			if(flag == "add") {
				var createdNames = new Array();
				<?php
					foreach($createdDataNames as $key => $val) {
				?>
						createdNames.push('<?php echo $val; ?>');
				<?php
					}
				?>
			}

			var IPAddr = '<?php echo $net_address ?>';

			function setIPType() {
				var ipType = document.getElementById("ip_type").value;
				if( ipType == "ANY") {
					document.getElementById("IPADDR").disabled = true;
					document.getElementById("IPADDR").value = "";
				} else {
					document.getElementById("IPADDR").disabled = false;
					document.getElementById("IPADDR").value = IPAddr;
				}
			}

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[rulename], minSize[5], maxSize[16]]");
				$sub('#ip_type').on('change', function() {
					var ip_type = $sub("#ip_type option:selected").val();
					if( ip_type == "IP_HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( ip_type == "IP_NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( ip_type == "IP_RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
					if( ip_type == "MAC_ADDR" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[macaddr]]");
					}
				});

				$sub('#port').on('change', function() {{
					var protocolval = $sub("#protocols").val();
					if(protocolval == "any") {
						portvalidany();
					} else {
						portvalidtcp();
					}
				}});

				$sub('#protocols').on('change', function() {
					var protocolval = $sub("#protocols").val();
					if(protocolval == "any") {
						portvalidany();
					} else {
						portvalidtcp();
					}
				});

				$sub(document).ready(function() {
					var protocolval = $sub("#protocols").val();
					if(protocolval == "any") {
						portvalidany();
					} else {
						portvalidtcp();
					}
					var ip_type = $sub("#ip_type option:selected").val();
					if( ip_type == "IP_HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
					if( ip_type == "IP_NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipwithCIDR]]");
					}
					if( ip_type == "IP_RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[ipRange]]");
					}
					if( ip_type == "MAC_ADDR" ) {
						$sub("#IPADDR").attr("data-validation-engine", "validate[required,custom[macaddr]]");
					}
				});

				$sub("#COMMENTS").attr("data-validation-engine", "validate[custom[comments],maxSize[64]]");

				$sub("#adddeviceaccess").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function portvalidtcp() {
				var portval = $sub("#port").val();
				if (portval.search(",") > 0 ) {
					$sub("#port").attr("data-validation-engine", "validate[required,custom[commaSepPorts]]");
				} else if ( portval.search("-") > 0 ) {
					$sub("#port").attr("data-validation-engine", "validate[required,custom[icmpportsRange]]");
				} else {
					$sub("#port").attr("data-validation-engine", "validate[required,custom[integer], min[0], max[65535]]");
				}
			}

			function portvalidany() {
				var portval = $sub("#port").val();
				if (portval.search(",") > 0 ) {
					$sub("#port").attr("data-validation-engine", "validate[custom[commaSepPorts]]");
				} else if ( portval.search("-") > 0 ) {
					$sub("#port").attr("data-validation-engine", "validate[custom[icmpportsRange]]");
				} else {
					$sub("#port").attr("data-validation-engine", "validate[custom[integer], min[0],max[65535]]");
				}
			}

			function setprotocols() {
				var formObj = document.forms[0];
				var protoval = formObj.protocols.value;
				if (protoval != "icmp") {
					formObj.port.disabled = false;
				} else {
					formObj.port.disabled = true;
					formObj.port.value = "";
				}
			}

			function submitData() {
				var flag = '<?php print $flag ?>';

				if (flag == "add") {
					var daArr = new Array();
					var get_da_val = document.getElementById("NAME").value;
					var post_da_val = get_da_val.trim();
					<?php
						foreach($daNames as $key=>$val) {
					?>
							daArr.push('<?php print $val; ?>');
					<?php
						}
					?>
					var daCount = daArr.length;
					for ( var i=0; i < daCount; i++) {
						if (post_da_val == daArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}

				if($sub("#adddeviceaccess").validationEngine('validate')) {
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
					parent.document.getElementById("container").src = "device_access.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="adddeviceaccess" name="adddeviceaccess" class="popup-form" action="add_edit_device_access.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
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
							<td>IP Type</td>
							<td width="20"></td>
							<td>
								<select name="ip_type" id="ip_type" class="ipDrpBox" onchange="setIPType();" onkeyup="setIPType();" onkeydown="setIPType();">
								<?php
									$ip_type = DeviceAccessConfigModel::getIPTypeOptions();
									for( $i =0; $i < count($ip_type); $i++) {
								?>
										<option <?php if($ip_val == $ip_type[$i])  print "selected = true"; ?>value="<?php print $ip_type[$i];?>"><?php print $ip_type[$i];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()"/>
							</td>
						</tr>
						<tr>
							<td>Address</td>
							<td width="20"></td>
							<td>
								<?php
									if( $ip_val == SBC_CONSTANTS::IP_TYPE_ANY_STR ) {
								?>
										<input type="text" class="ipBox" id="IPADDR" name="net_address" value="" size="" maxlength="36" />
								<?php
									} else {
								?>
										<input type="text" class="ipBox" id="IPADDR" name="net_address" value="<?php echo $net_address ?>" size="30" maxlength="36" />
								<?php
									}
								?>
								<img id="add_rule_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Protocols</td>
							<td width="20"></td>
							<td>
								<select name="protocols" id="protocols" class="ipDrpBox" onchange="setprotocols();" onkeyup="setprotocols();" onkeydown="setprotocols();">
									<option value="any" <?php if($protocols == "any") echo "selected" ?>>ANY</option>
									<option value="tcp" <?php if($protocols == "tcp") echo "selected" ?>>TCP</option>
									<option value="udp" <?php if($protocols == "udp") echo "selected" ?>>UDP</option>
									<option value="icmp" <?php if($protocols == "icmp") echo "selected" ?>>ICMP</option>
								</select>
								<img id="add_rule_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Port</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="port" name="port"  value="<?php echo $port ?>" size="" maxlength="64" />
								<img id="add_rule_013" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td><label for="enable">Enable</label></td>
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
							<td valign="top">Comments</td>
							<td width="20"></td>
							<td>
								<textarea cols="30" rows="5" maxlength="64" id="COMMENTS" name="comments"><?php echo $comments ?></textarea>
								<img id="add_rule_004" style="vertical-align: top" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
		setIPType();
		initValidation();
	</script>
</html>
