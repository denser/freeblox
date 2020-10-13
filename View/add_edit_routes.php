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
	include_once("Model/routes_model.php");
	include_once("Model/sys_calls.php");
	include_once("Model/devicelist_settings_model.php");

	$routes_config = Routes_ConfigModel::getInstance();
	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$devinterfaces = $devicelist_settings_config->getInterface();
	$res = 0;
	$page_title = "Create route";
	$flag = "add";
	$destination = "";
	$netmask = "";
	$gateway = "";
	$metric = "";
	$interface = "";
	$desc = "";
	$name = "";
	$createdDataNames = array();
	$routesNames = $routes_config->getNames();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace("@"," ",$val);
		$flag = "edit";
		$page_title = "Edit route";
		$row = $routes_config->getRouteEntry($id);
		if($row) {
			$destination = $row[Routes_ConfigModel::$DESTINATION];
			$netmask = $row[Routes_ConfigModel::$NETMASK];
			$gateway = $row[Routes_ConfigModel::$GATEWAY];
			$metric = $row[Routes_ConfigModel::$METRIC];
			$interface = $row[Routes_ConfigModel::$INTERFACE];
			$desc = $row[Routes_ConfigModel::$DESCRIPTION];
			$name = $row[Routes_ConfigModel::$NAME];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$routes_config->addRoutesEntry(trim($_POST['destination']),
							trim($_POST['netmask']),
							trim($_POST['gateway']),
							trim($_POST['metric']),
							$_POST['interfaces1'],
							trim($_POST['comments']),
							trim($_POST['name'])
							);
			$routes_config->createActionList(DEPLOY_MODULES::ROUTES_CONFIG, 
						"Route added", DEPLOY_MODULES::ROUTES_CONFIG_STR, "");
			$res = 1;
		}
		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $routes_config->getRouteEntry($id);
			if($row) {
				$destination = $row[Routes_ConfigModel::$DESTINATION];
				$netmask = $row[Routes_ConfigModel::$NETMASK];
				$gateway = $row[Routes_ConfigModel::$GATEWAY];
				$metric = $row[Routes_ConfigModel::$METRIC];
				$interface = $row[Routes_ConfigModel::$INTERFACE];
				$desc = $row[Routes_ConfigModel::$DESCRIPTION];
				$name = $row[Routes_ConfigModel::$NAME];
				if ( $desc != trim($_POST['comments']) || $netmask != 
					trim($_POST['netmask']) || trim($_POST['gateway']) != 
					$gateway || $metric != trim($_POST['metric']) || 
					$interface != $_POST['interfaces1'] || 
					$destination != trim($_POST['destination']) || 
					$name != trim($_POST['name']) ) {
						$routes_config->editRoutesEntry(
								trim($_POST['destination']), 
								trim($_POST['netmask']), 
								trim($_POST['gateway']),
								trim($_POST['metric']),
								$_POST['interfaces1'],
								trim($_POST['comments']),
								trim($_POST['name']));
						$routes_config->createActionList(
								DEPLOY_MODULES::ROUTES_CONFIG, 
								"Route edited", 
								DEPLOY_MODULES::ROUTES_CONFIG_STR, "");
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
						$sub("#interfaces1").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
					}
				});
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[nwrulename], minSize[5],maxSize[16]]");
				$sub("#DESTINATION").attr("data-validation-engine", "validate[required,custom[ipv4]]");
				$sub("#NETMASK").attr("data-validation-engine", "validate[required,custom[netmask]]");
				$sub("#GATEWAY").attr("data-validation-engine", "validate[required,custom[ipv4]]");
				$sub("#METRIC").attr("data-validation-engine", "validate[required,custom[integer], min[0],max[31]]");
				$sub("#COMMENTS").attr("data-validation-engine", "validate[custom[comments],maxSize[64]]");
				$sub("#adddeviceaccess").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if ( flag == "add") {
					var routesArr = new Array();
					var get_routes_val = document.getElementById("NAME").value;
					var post_routes_val = get_routes_val.trim();
					<?php
						foreach($routesNames as $key=>$val) {
					?>
							routesArr.push('<?php print $val ?>')
					<?php
						}
					?>
					var rouetsCount = routesArr.length;
					for ( var i=0; i < rouetsCount; i++) {
						if (post_routes_val == routesArr[i]) {
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
					parent.document.getElementById("container").src = "routes.php";
				}
			}

			function showIP() {
				var iface = document.getElementById("interfaces1").value;
				var post_val = "1";
					$sub.ajax({
						type : "POST",
						cache: false,
						data : {ip_iface : iface, route_post:post_val},
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
				<form id="adddeviceaccess" name="adddeviceaccess" class="popup-form" action="add_edit_routes.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
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
								<input type="text" class="ipBox" maxlength="16" <?php if ($flag == "edit") print "readonly='readonly'"; ?> name="name" id="NAME" value="<?php print $name; ?>" />
								<img id="network_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td>Destination</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" maxlength="32" name="destination" id="DESTINATION" value="<?php print $destination; ?>" />
								<img id="network_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Netmask</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" maxlength="32" name="netmask" id="NETMASK" value="<?php print $netmask; ?>" />
								<img id="network_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Gateway</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" maxlength="32" name="gateway" id="GATEWAY" value="<?php print $gateway; ?>" />
								<img id="network_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Metric</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" name="metric" maxlength="2" size="2" id="METRIC" value="<?php print $metric; ?>" />
								<img id="network_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Interfaces</td>
							<td width="20"></td>
							<td>
								<select name="interfaces1" id="interfaces1" class="ipDrpBox"  onchange="showIP();" onkeyup="showIP();" onkeydown="showIP();">
								<?php
								$i = 0;
								$interfacetype = "";
								$set =0;
								if($devinterfaces == false) {
									$set =1;
								} else {
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
								</select>
								<img id="network_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="iface_addr"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<textarea cols="30" rows="5" maxlength="64" id="COMMENTS" name="comments"><?php echo $desc; ?></textarea>
								<img id="network_ttip_004" style="vertical-align: top" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
