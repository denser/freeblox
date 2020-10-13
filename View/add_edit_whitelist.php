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
	include_once("Model/whitelist_model.php");
	include_once("Model/device_settings_model.php");

	$whitelist = WhiteListConfigModel::getInstance();
	$res = 0;
	$page_title = "Create Whitelist Rule";
	$flag = "add";
	$name = "";
	$ip_type = "";
	$net_address = "";
	$enabled = "";
	$comments = "";
	$createdDataNames = array();
	$wlNames = $whitelist->getNames();
	$wlNamesCount = count($wlNames);

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$flag = "edit";
		$id = str_replace('@',' ',$val);
		$page_title = "Edit Whitelist Rule";
		$row = $whitelist->getWhitelistRule($id);
		if($row) {
			$name = $row[WhiteListConfigModel::$NAME];
			$ip_val = $row[WhiteListConfigModel::$IP_TYPE];
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_ANY )
				$ip_val = SBC_CONSTANTS::IP_TYPE_ANY_STR;
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_HOST)
				$ip_val = SBC_CONSTANTS::IP_TYPE_HOST_STR;
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_NETWORK)
				$ip_val = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
			if( $ip_val == SBC_CONSTANTS::IP_TYPE_RANGE)
				$ip_val = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
			if( $ip_val == SBC_CONSTANTS::MAC_TYPE_HOST )
				$ip_val = SBC_CONSTANTS::MAC_TYPE_HOST_STR;
			$net_address = $row[WhiteListConfigModel::$NET_ADDRESS];
			$enabled = $row[WhiteListConfigModel::$ENABLED];
			$comments = $row[WhiteListConfigModel::$COMMENTS];
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
			$whitelist->addWhiteListRule(trim($_POST['name']), $ip_type, 
					trim($_POST['net_address']), $post_enable, 
					trim($_POST['comments']));
			$whitelist->createActionList(DEPLOY_MODULES::WHITELIST_CONFIG, 
					"Whitelist rule added",DEPLOY_MODULES::WHITELIST_CONFIG_STR, "");
			$res = 1;
		}
		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $whitelist->getWhitelistRule($id);
			if($row) {
				$ip_val = $row[WhiteListConfigModel::$IP_TYPE];
				$net_address = $row[WhiteListConfigModel::$NET_ADDRESS];
				$enabled = $row[WhiteListConfigModel::$ENABLED];
				$comments = $row[WhiteListConfigModel::$COMMENTS];
				if( $ip_type != $ip_val || 
					trim($_POST['net_address']) != $net_address || 
					$post_enable != $enabled || 
					trim($_POST['comments']) != $comments) {
						$whitelist->editWhiteListRule(trim($_POST['name']), 
							$ip_type, trim($_POST['net_address']), 
							$post_enable, trim($_POST['comments']));
						$whitelist->createActionList(
							DEPLOY_MODULES::WHITELIST_CONFIG, 
							"Whitelist rule edited", 
							DEPLOY_MODULES::WHITELIST_CONFIG_STR, "");
				}
				$res = 1;
			}
		}
	}
// Set Enable / Disable parameter via Ajax Post request
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['en_wl'] == "true")
			$post_enable_val = "1";
		else if($_POST['en_wl'] == "false")
			$post_enable_val = "";
		$wl_name = str_replace('@',' ',$_POST['wl_name']);
		$rows = $whitelist->getWhitelistRule($wl_name);
		$db_val = $rows[WhiteListConfigModel::$ENABLED];
		if( $post_enable_val != $db_val ) {
			$whitelist->setWhiteListEnable($wl_name,$post_enable_val);
			$whitelist->createActionList(DEPLOY_MODULES::WHITELIST_CONFIG, "Whitelist rule edited", DEPLOY_MODULES::WHITELIST_CONFIG_STR, "");
		}
	}
// Set Enable/Disable Whitelist IP Rules Precedes over Blacklist IP Rules
	if ( isset($_POST) && $_POST['rules_post'] == "1" ) {
		if ( $_POST['en_rules'] == "true" )
			$post_rules = "1";
		else if ( $_POST['en_rules'] == "false" )
			$post_rules = "";
		$dev_settings = DeviceSettingsModel::getInstance();
		$whitelist_preside_val = $dev_settings->get(DeviceSettingsModel::$WHITERULES_PRECEDES);
		if ( $post_rules != $whitelist_preside_val ) {
			$dev_settings->set(DeviceSettingsModel::$WHITERULES_PRECEDES,$post_rules);
			$dev_settings->createActionList(
					DEPLOY_MODULES::WHITE_LIST_RULES_PRECEDENCE_CONFIG, 
					"Whitelist Precedence updated", 
					DEPLOY_MODULES::WHITE_LIST_RULES_PRECEDENCE_CONFIG_STR,"");
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
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[rulename],minSize[5],maxSize[16]]");
				$sub('#ip_type').on('change', function() { {
					var ip_Type = $sub("#ip_type option:selected").val();
					if(ip_Type == "IP_HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
					if(ip_Type == "IP_NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipwithCIDR]]");
					}
					if(ip_Type == "IP_RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipRange]]");
					}
					if(ip_Type == "MAC_ADDR" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[macaddr]]");
					}
				}
				});

				$sub(document).ready(function() {
					var ip_Type = $sub("#ip_type option:selected").val();
					if(ip_Type == "IP_HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
					if(ip_Type == "IP_NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipwithCIDR]]");
					}
					if(ip_Type == "IP_RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipRange]]");
					}
					if(ip_Type == "MAC_ADDR" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[macaddr]]");
					}
				});
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub("#addwhitelist").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if (flag == "add") {
					var wlArr = new Array();
					var post_wl_val = document.getElementById("NAME").value;
					<?php
					foreach($wlNames as $key=>$val) { 
					?>
						wlArr.push('<?php print $val; ?>');
					<?php } ?>
					var wlCount = wlArr.length;
					for ( var i=0; i < wlCount; i++) {
						if (post_wl_val == wlArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if($sub("#addwhitelist").validationEngine('validate')) {
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
					parent.document.getElementById("container").src = "whitelist.php";
				}
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
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addwhitelist" name="addwhitelist" class="popup-form" action="add_edit_whitelist.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
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
							<td>Ip Type</td>
							<td width="20"></td>
							<td>
								<select name="ip_type" class="ipDrpBox" id="ip_type" onchange="setIPType();" onkeyup="setIPType();" onkeydown="setIPType();" >
								<?php
								$ip_type = WhiteListConfigModel::getIPTypeOptions();
								for( $i =0; $i < count($ip_type); $i++) {
								?>
									<option <?php if($ip_val == $ip_type[$i])  print "selected = true"; ?> value="<?php print $ip_type[$i];?>"><?php print $ip_type[$i];?></option>
								<?php } ?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Address</td>
							<td width="20"></td>
							<td>
							<?php if( $ip_val == SBC_CONSTANTS::IP_TYPE_ANY_STR ) { ?>
									<input type="text" class="ipBox" id="IPADDR" name="net_address" value="" size="30" maxlength="64" />
							<?php } else {?>
									<input type="text" class="ipBox" id="IPADDR" name="net_address" value="<?php echo $net_address ?>" size="30" maxlength="64" />
							<?php } ?>
									<img id="add_rule_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td><label for="enable">Enable</label></td>
							<td width="20"></td>
							<td>
							<?php if($enabled == "1")
									$enabled = "checked";
								else
									$enabled = "";
							?>
								<input type="checkbox" name="enable" <?php echo $enabled; ?> id="enable" />
								<img id="add_rule_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Comments</td>
							<td width="20"></td>
							<td>
								<textarea cols="30" rows="5" maxlength="64" id="COMMENTS" name="comments"><?php echo $comments ?></textarea>
								<img id="add_rule_004" style="vertical-align: top;" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
