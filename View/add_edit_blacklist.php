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
	include_once("Model/blacklist_model.php");

	$blacklist = BlackListConfigModel::getInstance();
	$res = 0;
	$page_title = "Create Blacklist Rule";
	$flag = "add";
	$name = "";
	$ip_type = "";
	$net_address = "";
	$enabled = "";
	$comments = "";
	$createdDataNames = array();
	$blNames = $blacklist->getNames();
	$blNamesCount = count($blNames);

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@',' ',$val);
		$flag = "edit";
		$page_title = "Edit Blacklist Rule";
		$row = $blacklist->getBlacklistRule($id);
		if($row) {
			$name = $row[BlackListConfigModel::$NAME];
			$ip_val = $row[BlackListConfigModel::$IP_TYPE];
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
			$net_address = $row[BlackListConfigModel::$NET_ADDRESS];
			$enabled = $row[BlackListConfigModel::$ENABLED];
			$comments = $row[BlackListConfigModel::$COMMENTS];
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
			$blacklist->addBlackListRule(trim($_POST['name']), $ip_type, 
						trim($_POST['net_address']), $post_enable, 
						trim($_POST['comments']));
			$blacklist->createActionList(DEPLOY_MODULES::BLACKLIST_CONFIG, "Blacklist rule added", DEPLOY_MODULES::BLACKLIST_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $blacklist->getBlacklistRule($id);
			if($row) {
				$net_address = $row[BlackListConfigModel::$NET_ADDRESS];
				$ip_val = $row[BlackListConfigModel::$IP_TYPE];
				$enabled = $row[BlackListConfigModel::$ENABLED];
				$comments = $row[BlackListConfigModel::$COMMENTS];
				if(trim($_POST['net_address']) != $net_address || 
					$post_enable != $enabled || 
					trim($_POST['comments']) != $comments || 
					$ip_type != $ip_val ) {
						$blacklist->editBlackListRule(trim($_POST['name']), 
									$ip_type, 
									trim($_POST['net_address']), 
									$post_enable, 
									trim($_POST['comments']));
						$blacklist->createActionList(DEPLOY_MODULES::BLACKLIST_CONFIG, "Blacklist rule edited", DEPLOY_MODULES::BLACKLIST_CONFIG_STR, "");
				}
				$res = 1;
			}
		}
	}

// Set Enable / Disable parameter via Ajax Post request
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['en_bl'] == "true")
			$post_enable_val = "1";
		else if($_POST['en_bl'] == "false")
			$post_enable_val = "";
		$bl_name = str_replace('@',' ',$_POST['bl_name']);
		$rows = $blacklist->getBlacklistRule($bl_name);
		$db_val = $rows[BlackListConfigModel::$ENABLED];
		if( $post_enable_val != $db_val ) {
			$blacklist->setBlackListEnable($bl_name,$post_enable_val);
			$blacklist->createActionList(DEPLOY_MODULES::BLACKLIST_CONFIG, "Blacklist rule edited", DEPLOY_MODULES::BLACKLIST_CONFIG_STR, "");
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
				<?php
					foreach($createdDataNames as $key => $val) { 
				?>
						createdNames.push('<?php echo $val; ?>');
				<?php
					}
				?>
			}

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[rulename],minSize[5],maxSize[16]]");
				$sub('#ip_type').on('change', function() {
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
				$sub("#addblacklist").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if (flag == "add") {
					var blArr = new Array();
					var post_bl_val = document.getElementById("NAME").value;
					<?php
						foreach($blNames as $key=>$val) {
					?>
							blArr.push('<?php print $val; ?>');
					<?php
						}
					?>
					var blCount = blArr.length;
					for ( var i=0; i < blCount; i++) {
						if (post_bl_val == blArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if($sub("#addblacklist").validationEngine('validate')) {
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
					parent.document.getElementById("container").src = "blacklist.php";
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
				<form id="addblacklist" name="addblacklist" class="popup-form" action="add_edit_blacklist.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display:none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>"/>
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
									$ip_type = BlackListConfigModel::getIPTypeOptions();
									for( $i =0; $i < count($ip_type); $i++) {
								?>
										<option <?php if($ip_val == $ip_type[$i])  print "selected = true"; ?>value="<?php print $ip_type[$i];?>"><?php print $ip_type[$i];?></option>
								<?php
									}
								?>
								</select>
								<img id="add_rule_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Address</td>
							<td width="20"></td>
							<td>
								<?php 
									if( $ip_val == SBC_CONSTANTS::IP_TYPE_ANY_STR ) {
								?>
										<input type="text" class="ipBox" id="IPADDR" name="net_address" value="" size="30" maxlength="64" />
								<?php
									} else {
								?>
										<input type="text" class="ipBox" id="IPADDR" name="net_address" value="<?php echo $net_address ?>" size="30" maxlength="64" />
								<?php
									}
								?>
										<img id="add_rule_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
								<img id="add_rule_004" src="images/icn-info.png" style="vertical-align: top;" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
