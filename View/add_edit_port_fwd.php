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
	include_once("Model/port_fwd_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/sys_calls.php");
	include_once("Model/devicelist_settings_model.php");

	$firewall = FirewallConfigModel::getInstance();
	$port_fwd_config = Port_Forwarding_Model::getInstance();
	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$devinterfaces = $devicelist_settings_config->getInterface();
	$res = 0;
	$page_title = "Create Port Forwarding Rule";
	$flag = "add";
	$interface = "";
	$ext_address = "";
	$ext_port = "";
	$int_address = "";
	$int_port = "";
	$desc = "";
	$protocol = "";
	$name = "";
	$createdDataNames = array();
	$portfwdNames = $port_fwd_config->getNames();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@',' ',$val);
		$flag = "edit";
		$page_title = "Edit Port Forwarding Rule";
		$row = $port_fwd_config->getPortFwdRule($id);
		if($row) {
			$interface = $row[Port_Forwarding_Model::$INTERFACE];
			$ext_address = $row[Port_Forwarding_Model::$EXTERNAL_ADDRESS];
			$ext_port = $row[Port_Forwarding_Model::$EXTERNAL_PORT];
			$int_address = $row[Port_Forwarding_Model::$INTERNAL_ADDRESS];
			$int_port = $row[Port_Forwarding_Model::$INTERNAL_PORT];
			$desc = $row[Port_Forwarding_Model::$DESCRIPTION];
			$protocol = $row[Port_Forwarding_Model::$PROTOCOL];
			$name = $row[Port_Forwarding_Model::$NAME];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$port_fwd_config->addPortFwdRule($_POST['interfaces1'],
								trim($_POST['external_address']),
								trim($_POST['external_port']),
								trim($_POST['internal_address']),
								trim($_POST['internal_port']),
								trim($_POST['comments']),
								$_POST['protocol'],
								trim($_POST['name'])
								);
			$port_fwd_config->createActionList(DEPLOY_MODULES::PORT_FORWARDING_CONFIG, 
							"Port Forwarding rule added", 
							DEPLOY_MODULES::PORT_FORWARDING_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $port_fwd_config->getPortFwdRule($id);
			if($row) {
				$interface = $row[Port_Forwarding_Model::$INTERFACE];
				$ext_address = $row[Port_Forwarding_Model::$EXTERNAL_ADDRESS];
				$ext_port = $row[Port_Forwarding_Model::$EXTERNAL_PORT];
				$int_address = $row[Port_Forwarding_Model::$INTERNAL_ADDRESS];
				$int_port = $row[Port_Forwarding_Model::$INTERNAL_PORT];
				$desc = $row[Port_Forwarding_Model::$DESCRIPTION];
				$protocol = $rows[Port_Forwarding_Model::$PROTOCOL];
				$name = $row[Port_Forwarding_Model::$NAME];
				if ( $desc != trim($_POST['comments']) || 
					$ext_address != trim($_POST['external_address']) || 
					$ext_port != trim($_POST['external_port']) || 
					$int_address !=  trim($_POST['internal_address']) || 
					$int_port != trim($_POST['internal_port']) || 
					$_POST['protocol'] != $protocol || 
					$name != trim($_POST['name'])) {
						$port_fwd_config->editPortFwdRule($_POST['interfaces1'],
								trim($_POST['external_address']),
								trim($_POST['external_port']),
								trim($_POST['internal_address']),
								trim($_POST['internal_port']),
								trim($_POST['comments']),
								$_POST['protocol'],
								trim($_POST['name']));
						$port_fwd_config->createActionList(
							DEPLOY_MODULES::PORT_FORWARDING_CONFIG, 
							"Port Forwarding rule edited", 
							DEPLOY_MODULES::PORT_FORWARDING_CONFIG_STR, "");
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
				getPageTips("port_fwd_ttip");
				setTooltip();
			});

			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			function initValidation() {
				$sub( document ).ready(function() {
					var sel_interface= $sub("#interfaces1").val();
					if ( sel_interface == "--No Interfaces--") {
						$sub("#interfaces1").attr("data-validation-engine", "validate[required,custom[selectInterfaces]]");
					} 
				});
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[rulename], minSize[5],maxSize[16]]");
				$sub("#External_Address").attr("data-validation-engine", "validate[required,custom[ipv4]]");
				$sub("#External_Port").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[65535]]");
				$sub("#Internal_Address").attr("data-validation-engine", "validate[required,custom[ipv4]]");
				$sub("#Internal_Port").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[65535]]");
				$sub("#COMMENTS").attr("data-validation-engine", "validate[custom[comments],maxSize[64]]");
				$sub("#addportforwarding").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function showIP() {
				var iface = document.getElementById("interfaces1").value;
				var post_val = "1";
					$sub.ajax({
						type : "POST",
						cache: false,
						data : {ip_iface : iface, portfwd_post:post_val},
						url : "show_iface_ip.php",
						success: function (ipaddr) {
							document.getElementById("iface_addr").innerHTML = ipaddr;
						}
				});
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if ( flag == "add" ) {
					var portArr = new Array();
					var get_port_val = document.getElementById("NAME").value;
					var post_port_val = get_port_val.trim();
					<?php
						foreach($portfwdNames as $key=>$val) {
					?>
							portArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var portCount = portArr.length;
					for (var i = 0; i < portCount; i++ ) {
						if ( post_port_val == portArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub('#errorText').show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if($sub("#addportforwarding").validationEngine('validate')) {
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
					parent.document.getElementById("container").src = "port_forwarding.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 290px">
				<form id="addportforwarding" name="addportforwarding" class="popup-form" action="add_edit_port_fwd.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name; ?>" />
							</td>
						</tr>
						<tr>
							<td>Name</td>
							<td width="20"></td>
							<td>
								<input type="text" <?php if ($flag == "edit") print "readonly='readonly'"; ?> value="<?php print $name; ?>" class="ipBox" id="NAME" name="name" maxlength="16"  />
								<img id="port_fwd_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td>Interfaces</td>
							<td width="20"></td>
							<td>
								<select id="interfaces1" class="ipDrpBox" name="interfaces1" onchange="showIP();" onKeydown="showIP();" onKeyup="showIP();">

							<?php
							$i = 0;
							$interfacetype = "";
							$set =0;
							if($devinterfaces == false) {
								$set =1;
							} else 	{
								while($row = $devinterfaces->fetchArray(SQLITE3_ASSOC)) {
									$i++;
									$select_str = "";
									if($flag == "edit" && ($row[DevicelistSettingsModel::$INTER_NAME] == $interface)) {
										$select_str = "selected";
										$set = 1;
							?>

										<option value="<?php print $row[DevicelistSettingsModel::$INTER_NAME];?>" <?php print $select_str;?>><?php print $row[DevicelistSettingsModel::$INTER_NAME]; ?></option>
							<?php
									} else {
								$set = 1;
							?>
										<option value="<?php print $row[DevicelistSettingsModel::$INTER_NAME];?>"><?php print $row[DevicelistSettingsModel::$INTER_NAME];  ?></option>
							<?php
										}
								}
							}
							if($set == 0) {
							?>
										<option value="--No Interfaces--">--No Interfaces--</option>
							<?php
							}
							?>
								</select>
								<img id="port_fwd_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="iface_addr"></span>
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
								<img id="port_fwd_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr style="display:none">
							<td>External Address</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $ext_address; ?>" class="ipBox" id="External_Address" name="external_address" maxlength="32" />
								<img id="port_fwd_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>External Port</td>
							<td width="20"></td>
							<td>
								<input type="text" size="5" maxlength="5" value="<?php print $ext_port; ?>" class="ipBox" id="External_Port" name="external_port" />
								<img id="port_fwd_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Internal Address</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $int_address; ?>" class="ipBox" id="Internal_Address" name="internal_address" maxlength="32" />
								<img id="port_fwd_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Internal Port</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $int_port; ?>" size="5" maxlength="5" class="ipBox" id="Internal_Port" name="internal_port" />
								<img id="port_fwd_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<textarea cols="30" rows="5" maxlength="64" id="COMMENTS" name="comments"><?php echo $desc; ?></textarea>
								<img id="port_fwd_ttip_006" style="vertical-align: top;" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
