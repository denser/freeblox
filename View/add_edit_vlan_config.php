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
	include_once("Model/vlan_config_model.php");
	include_once("Model/sys_calls.php");
	include_once('Model/devicelist_settings_model.php');
	
	$vlan_config = VLAN_ConfigModel::getInstance();
	$interfaces = SysCalls::getRealInterfaces();
	$devlist = DevicelistSettingsModel::getInstance();
	$interfacename=$devlist->getinterface(); 
	$get_nat_settings = $devlist->getNatSettings();
	$count_nat_settings = count($get_nat_settings);	
	$res = 0;
	$page_title = "Create VLAN";
	$flag = "add";
	$tag_id = "";
	$ipaddress = "";
	$interface = "";
	$netmask = "";
	$description = "";
	$name = "";
	$nat_settings="";
	$natip_address = "";
	$createdDataNames = array();
	$vlanNames = $vlan_config->getNames();

	if(isset($_GET['data'])) {
		$id = $_GET['data'];
		$flag = "edit";
		$page_title = "Edit VLAN";
		$row = $vlan_config->getVLANRule($id);
		if($row) {
			$tag_id = $row[VLAN_ConfigModel::$TAG_ID];
			$ipaddress = $row[VLAN_ConfigModel::$IPADDRESS];
			$interface = $row[VLAN_ConfigModel::$INTERFACE];
			$netmask = $row[VLAN_ConfigModel::$NETMASK];
			$description = $row[VLAN_ConfigModel::$DESCRIPTION];
			$name = $row[VLAN_ConfigModel::$NAME];
			$nat_settings=$row[VLAN_ConfigModel::$NAT_SETTINGS];
			$natip_address=$row[VLAN_ConfigModel::$STUN_STATIC_IPADDR];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		$postname = $_POST['interfaces1'].".".trim($_POST['tag_id']);
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$vlan_config->addVLANRule(trim($_POST['tag_id']),
						$_POST['interfaces1'],
						trim($_POST['ip_addr']),
						trim($_POST['netmask']),
						trim($_POST['comments']),
						$postname,
						$_POST['nat_settings'],
						$_POST['ip_address']);
			$vlan_config->createActionList(DEPLOY_MODULES::VLAN_CONFIG, 
						"VLAN Config rule added", 
						DEPLOY_MODULES::VLAN_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") { // Edit a rule
			$id = $_POST['id'];
			$row = $vlan_config->getVLANRule($id);
			if($row) {
				$tag_id = $row[VLAN_ConfigModel::$TAG_ID];
				$ipaddress = $row[VLAN_ConfigModel::$IPADDRESS];
				$interface = $row[VLAN_ConfigModel::$INTERFACE];
				$netmask = $row[VLAN_ConfigModel::$NETMASK];
				$description = $row[VLAN_ConfigModel::$DESCRIPTION];
				$name = $row[VLAN_ConfigModel::$NAME];
				$nat_settings=$row[VLAN_ConfigModel::$NAT_SETTINGS];
				$natip_address=$row[VLAN_ConfigModel::$STUN_STATIC_IPADDR];
				$natsettingsval = $_POST['nat_settings'];
				$postipaddress = $_POST['ip_address'];
				$externalinterfaceval = $devlist->checkExternalInterface($_POST['interfaces1']);
				if($externalinterfaceval != 1) {
					$natsettingsval="NO NAT";
					$postipaddress= "";
				} 
				if ( $description != trim($_POST['comments']) || 
					$ipaddress != trim($_POST['ip_addr']) || 
					$interface != $_POST['interfaces1'] || 
					trim($_POST['netmask']) != $netmask || 
					$nat_settings != $natsettingsval || 
					$natip_address != $postipaddress ) {
						$vlan_config->editVLANRule ( trim($_POST['tag_id']),
							$_POST['interfaces1'], trim($_POST['ip_addr']),
							trim($_POST['netmask']), 
							trim($_POST['comments']), $id, $postname,
							$natsettingsval, $postipaddress);
					$vlan_config->createActionList(DEPLOY_MODULES::VLAN_CONFIG, 
							"VLAN Config rule edited", 
							DEPLOY_MODULES::VLAN_CONFIG_STR, "");
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
				getPageTips("network_ttip");
				setTooltip();
				showStunStatic();
			});
			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			if(flag == "edit") {
				document.getElementById("interfaces1").disabled = true;
			}

			function initValidation() {
				$sub( document ).ready(function() {
					var sel_interface= $sub("#interfaces1").val();
					if ( sel_interface == "--No Interfaces--") {
						$sub("#interfaces1").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
					}
					var nat_settings  = $sub("#nat_settings option:selected").val();
					if( nat_settings == "STUN" ) {
						$sub("#ip_address").attr("data-validation-engine","validate[required,custom[IP_DOMAIN]]");
					}
					if( nat_settings == "STATIC" ) {
						$sub("#ip_address").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}

				});
				$sub("#TAG_ID").attr("data-validation-engine","validate[required,custom[integer],min[1],max[4092]]");
				$sub("#IP_ADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
				$sub("#NETMASK").attr("data-validation-engine","validate[required,custom[netmask]]");
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub('#nat_settings').on('change', function() { {
					var nat_settings  = $sub("#nat_settings option:selected").val();
					if( nat_settings == "STUN" ) {
						$sub("#ip_address").attr("data-validation-engine","validate[required,custom[IP_DOMAIN]]");
					}
					if( nat_settings == "STATIC" ) {
						$sub("#ip_address").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
				}});
				$sub("#adddeviceaccess").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function showStunStatic() {
				var natSettings = document.getElementById("nat_settings").value;
				if ( natSettings== "NO NAT") {
					document.getElementById("ip_address").disabled = true;
					document.getElementById("ip_address").value = "";
				} else {
					document.getElementById("ip_address").disabled = false;
				}
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if (flag == "add") {
					var vlanArr = new Array();
					var get_vlan_val = document.getElementById("TAG_ID").value;
					var post_vlan_val = get_vlan_val.trim();
					var get_vlan_interface =document.getElementById("interfaces1").value;
					var post_vlan_interface = get_vlan_interface.trim();
					<?php foreach($vlanNames as $key=>$val) { ?>
							vlanArr.push('<?php print $val; ?>');
					<?php } ?>
					var vlanCount = vlanArr.length;
					for ( var i=0; i < vlanCount; i++) {
						var vlantagid = vlanArr[i].split("-");
						if ( (post_vlan_val == vlantagid[0]) && (post_vlan_interface == vlantagid[1]) ) {
							document.getElementById("errorText").innerHTML = "Invalid! Vlan Tag ID with same Interface is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				document.getElementById("interfaces1").disabled = false;
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
					parent.document.getElementById("container").src = "vlan_config.php";
				}
			}

			function showIP() {
				var iface = document.getElementById("interfaces1").value;
				var post_val1 = "1";
				$sub.ajax({
					type: "POST",
					cache: false,
					data: { ip_iface : iface, vlan_ext_inter_post : post_val1},
					url:  "show_iface_ip.php",
					success: function (data) {
						if(data == 1) {
							$sub("#natsettings").show(500);
						} else {
							$sub("#natsettings").hide();
						}
					}
				});
				var post_val = "1";
					$sub.ajax({
						type : "POST",
						cache: false,
						data : {ip_iface : iface, vlan_post:post_val},
						url : "show_iface_ip.php",
						success: function (ipaddr) {
							document.getElementById("iface_addr").innerHTML = ipaddr;
						}
				});
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="adddeviceaccess" name="adddeviceaccess" class="popup-form" action="add_edit_vlan_config.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name; ?>" />
							</td>
						</tr>
						<tr>
							<td>Tag ID</td>
							<td width="20"></td>
							<td>
								<input type="text" maxlength="4" size="4" class="ipBox" name="tag_id" id="TAG_ID" <?php if ($flag == "edit") echo "readonly='readonly'"; ?> value="<?php print $tag_id; ?>" />
								<img id="network_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td>Interfaces</td>
							<td width="20"></td>
							<td>
								<select id="interfaces1" name="interfaces1" class="ipDrpBox" onchange="showIP();" onkeyup="showIP();" onkeydown="showIP();">
						<?php
						$i = 0;
						$set =0;
						if($interfacename == false) { 
							$set =1;
 						} else {
							while($row = $interfacename->fetchArray(SQLITE3_ASSOC)) {
								$i++;
								$select_str = "";
					
									if($flag == "edit" && (  $row[DevicelistSettingsModel::$INTER_NAME] == $interface )) {
										$select_str = "selected";
										$set = 1;
						?>
											<option value="<?php print $row[DevicelistSettingsModel::$INTER_NAME];?>" <?php print $select_str;?>><?php print $row[DevicelistSettingsModel::$INTER_NAME];?></option>
						<?php
									} else {
										$set = 1;
						?>
										<option value="<?php print $row[DevicelistSettingsModel::$INTER_NAME];?>"><?php print $row[DevicelistSettingsModel::$INTER_NAME];?></option>
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
						<!--<?php
						for ($j=0; $j < count($interfaces); $j++) {
							$select_str = "";
							if ( $interfaces[$j] == $interface )
								$select_str = "selected";
						?>
							<option value="<?php print $interfaces[$j]; ?>" <?php print $select_str;?>><?php print $interfaces[$j]; ?></option>
						<?php } ?> -->
								</select>
								<img id="network_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="iface_addr"></span>
							</td>
						</tr>
						<tr>
							<td>IP Address</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" name="ip_addr" maxlength="32" id="IP_ADDR" value="<?php print $ipaddress; ?>" />
								<img id="network_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Netmask</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" name="netmask" maxlength="32" id="NETMASK" value="<?php print $netmask; ?>" />
								<img id="network_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<textarea cols="30" rows="5" maxlength="64" id="COMMENTS" name="comments"><?php echo $description; ?></textarea>
								<img id="network_ttip_004" style="vertical-align: top" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
<!-- NAT settings -->
						<tr id="natsettings" style="display:none" width="100%">
							<td valign="top">NAT Settings</td>
							<td width="20"></td>
							<td>
								<select class="ipDrpBox" id="nat_settings" name="nat_settings" onchange="showStunStatic();" onkeydown="showStunStatic();" onkeyup="showStunStatic();">
							<?php
								for ( $i = 0; $i < $count_nat_settings; $i++ ) {
									$select_str = "";
									if ( $get_nat_settings[$i] == $nat_settings )
										$select_str = "selected";
							?>
								<option value="<?php print $get_nat_settings[$i];?>" <?php print  $select_str;?> ><?php print $get_nat_settings[$i];?></option>
							<?php } ?>
											</select>
											<img id="network_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
							<td><label for="ipaddress">IP Address</label>
							</td>
							<td width="5"></td>
							<td>
								<input type="text" size="10" maxlength="32" value="<?php print $natip_address; ?>" class="ipBox" id="ip_address" name="ip_address" />
								<img id="network_ttip_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
		showIP();
		initValidation();
	</script>
</html>
