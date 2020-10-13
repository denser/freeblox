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
	include_once("Model/geo_ip_filter_model.php");

	$geo_ip_filter = GeoIpSettingsModel::getInstance();
	$res = 0;
	$page_title = "Create Geo IP Filter";
	$flag = "add";
	$net_address = "";
	$enabled = "";
	$comments = "";

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('_',' ',$val);
		$flag = "edit";
		$page_title = "Edit Geo IP Filter";
		$row = $geo_ip_filter->getGeoIPFilter($id);
		if($row) {
			$country_name = $row[GeoIpSettingsModel::$COUNTRY_NAME];
			$allowed = $row[GeoIpSettingsModel::$ALLOWED];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['action'] == "on")
			$post_action = "1";
		else
			$post_action = "";
		if ($_POST['submit_action'] == "1") {	// Edit a geo filter rule
			$id = $_POST['id'];
			$row = $geo_ip_filter->getGeoIPFilter($id);
			if($row) {
				$allowed = $row[GeoIpSettingsModel::$ALLOWED];
				if( $post_action != $allowed) {
					$geo_ip_filter->setAllowed($_POST['country_name'],$post_action);
					$geo_ip_filter->createActionList(DEPLOY_MODULES::GEOIP_CONFIG, 
								"GeoIP Filter rule edited", 
								DEPLOY_MODULES::GEOIP_CONFIG_STR, "");
				}
				$res = 1;
			}
		}
	}
// Set Action (Allowed/Blocked) parameter via Ajax Post request
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['action_val'] == "true")
			$post_action_val = "1";
		else if($_POST['action_val'] == "false")
			$post_action_val = "";
		$country = str_replace('_',' ',$_POST['country_name']);
		$rows = $geo_ip_filter->getGeoIPFilter($country);
		$db_val = $rows[GeoIpSettingsModel::$ALLOWED];
		if( $post_action_val != $db_val ) {
			$geo_ip_filter->setAllowed($country,$post_action_val);
			$geo_ip_filter->createActionList(DEPLOY_MODULES::GEOIP_CONFIG, 
						"GeoIP Filter rule edited", 
						DEPLOY_MODULES::GEOIP_CONFIG_STR, "");
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
				getPageTips("geo_ips");
				setTooltip();
			});

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[onlyLetterNumber], checkExists,minSize[3]]");
				$sub("#addgeoipfilter").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				if($sub("#addgeoipfilter").validationEngine('validate')) {
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
					parent.document.getElementById("container").src = "geo_ip_filter.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addgeoipfilter" name="addgeoipfilter" class="popup-form" action="add_edit_geo.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $country_name; ?>" />
							</td>
						</tr>
						<tr>
							<td>Country Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" name="country_name" maxlength="32" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $country_name; ?>" />
								<img id="geo_ips_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td><label for="enable">Allowed</label></td>
							<td width="20"></td>
							<td>
							<?php
								if($allowed == "1")
									$allowed = "checked";
								else
									$allowed = "";
							?>
								<input type="checkbox" name="action" <?php print $allowed; ?> id="enable" />
								<img id="geo_ips_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="GEOIP_FILTER_LIST" id="GEOIP_FILTER_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
</html>
