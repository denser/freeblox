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
	$page_title = "Regenerate Device Root CA";
	$flag = "add";
	$common_name = "";
	$state_province_name = "";
	$countryName = "";
	$email_address = "";
	$organization_name = "";
	$encryption_strength = "";
	$validity = "";
	$createdDataNames = array();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/validationEngine.jquery.css"/>
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
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[rulename],minSize[5], maxSize[16]]");
				$sub('#ip_type').on('change', function() {{
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
				}
				});
				$sub(document).ready(function() {
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
							<td>Common Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="COMMON_NAME" name="common_name" value="<?php echo $common_name; ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>State or Province Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="STATE_PROVINCE_NAME" name="state_province_name" value="<?php echo $name ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Country Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="COUNTRY_NAME" name="country_name" value="<?php echo $name ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Email Address</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="EMAIL_ID" name="email_id" value="<?php echo $name ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Organization Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="ORGANIZATION_NAME" name="organization_name" value="<?php echo $name ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Encryption Strength</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="ENCRYPTION_STRENGTH" name="encryption_strength" value="<?php echo $name ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Validity</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="VALIDITY" name="validity" value="<?php echo $name ?>" size="" maxlength="16" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
