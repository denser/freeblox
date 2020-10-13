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
	include_once("Control/login_auth.php");
	include_once("Model/geo_ip_filter_model.php");
	include_once('Model/device_settings_model.php');
	include_once("Control/date_config.php");
	include_once("Model/sbc_constants.php");

	$_SESSION['pageID'] = "#geo_ip";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$geo_ip_filter = GeoIpSettingsModel::getInstance();
	$geo_ip_filter_rules = $geo_ip_filter->getGeoIpFilters();
	$count = $geo_ip_filter->getGeoIpFiltersCount();
	$allow_const = SBC_CONSTANTS::GEO_IP_ALLOW_STR;
	$block_const = SBC_CONSTANTS::GEO_IP_BLOCK_STR;
	$all_allowed_count = $geo_ip_filter->getGeoIpFiltersActionCount ( $allow_const );
	$all_blocked_count = $geo_ip_filter->getGeoIpFiltersActionCount ( $block_const );
	$dev_settings = DeviceSettingsModel::getInstance();
	$dpi_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_DPI);
	$res = 0;

	if(isset($_POST['submit_apply']) && $_POST['submit_apply']) {
		$allowed = "1";
		$geo_ip_filter->setAllowedAll($allowed);
		if ( $count != $all_allowed_count ) {
			$geo_ip_filter->createActionList(DEPLOY_MODULES::GEOIP_CONFIG, 
						"All GeoIP locations are set to Allow", 
						DEPLOY_MODULES::GEOIP_CONFIG_STR, "");
		}
		$res = 1;
?>
		<script type="text/javascript">
			submit = 1;
		</script>
<?php
	}
	if(isset($_POST) && isset($_POST['submit_block'])) {
		$blocked = "";
		$geo_ip_filter->setBlockedAll($blocked);
		if ( $count != $all_blocked_count ) {
			$geo_ip_filter->createActionList(DEPLOY_MODULES::GEOIP_CONFIG, "All GeoIP locations are set to Block", DEPLOY_MODULES::GEOIP_CONFIG_STR, "");
		}
		$res = 1;
?>
		<script type="text/javascript">
			submit = 1;
		</script>
<?php
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<script src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.dataTables.js"></script>
		<link rel="stylesheet" type="text/css" href="css/sort.css" media="screen" />
		<script type="text/javascript">
			$(document).ready(function() {
				$('#geoiplist').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "260px",
					"aaSorting": [[ 0, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
						{"bSortable": false},
						{"bSortable": false},
						{"bSortable": false}
					],
					"bPaginate": false
				});
			});

			function edit_data(val) {
				parent.callEditPage(val, 'add_edit_geo.php', 360, 550);
			}

			function submitData() {
				var formObj = document.geoip_form;
				formObj.submit_apply.value = "1";
				formObj.submit();
				return true;
			}

			function submitBlock() {
				var formObj = document.geoip_block;
				formObj.submit_block.value = "1";
				formObj.submit();
				return true;
			}

			var val = "Geo IP Update is in progress, Please wait....";
			var success = "Geo IP Update successfully applied.";
			function sig_UpdateMsg(val) {
				parent.geoIPMessage(val);
			}

			function updateGeoIPUpdate() {
				setTimeout(sig_UpdateMsg(val),900);
				$.ajax({
					type: "POST",
					cache: false,
					data: {geo_update:1},
					url:  "run_geoip_update.html",
					success: function (html) {
						sig_UpdateMsg(success);
					}
				});
			}

			function submitGeoIP() {
				var formObj = document.geoip_update;
				formObj.submit_update.value = "1";
				formObj.submit();
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "geo_ip_filter.php";
				}
			}

			function setGeoAllow_Block(country,action,post) {
				var allow_block = action.checked;
				var post_val = "1"; // Sets the Ajax Post Request
				$.ajax({
					type: "POST",
					cache: false,
					data: {country_name:country, action_val:allow_block,ajax_post:post_val},
					url:  "add_edit_geo.php",
					success: function (html) {
						refresh_iframe(1);
					}
				});
			}
		</script>
		<?php
			if($_POST['submit_update'] == 1) {
		?>
				<script type="text/javascript">
					updateGeoIPUpdate();
				</script>
		<?php
				$res = 2;
			}
		?>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<h1>Geo IP Filters<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="GEOIP_body" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<form id="geoip_form" name="geoip_form" method="post" action="geo_ip_filter.php">
					<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
						<tr>
							<td>
								<input type="hidden" name="submit_apply" value="0" />
								<a class="btn-grey-wide-l" name="submit_set" onclick="submitData();"><span>Allow All Countries</span></a>
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;
							</td>
						</tr>
					</table>
					</form>
					<form id="geoip_block" name="geoip_block" method="post" action="geo_ip_filter.php">
					<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
						<tr>
							<td style="position: fixed; top: 44px; left: 165px;">
								<input type="hidden" name="submit_block" value="0" />
								<a class="btn-grey-wide-l" name="submit_set" onclick="submitBlock();"><span>Block All Countries</span></a>
							</td>
						</tr>
					</table>
					</form>
					<form id="geoip_update" name="geoip_update" method="post" action="geo_ip_filter.php">
					<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
						<tr>
							<td style="position: fixed; top: 44px; left: 330px;">
								<input type="hidden" name="submit_update" value="0" />
								<a class="btn-grey-wide" name=""  onclick="submitGeoIP();"><span>Update Geo IP</span></a>
							</td>
						</tr>
					</table>
					</form>
					<table id="geoiplist" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td style="display:none;"><label>Id</label></td>
								<td><label>Country Name</label></td>
								<td><label>Allowed</label></td>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
							$i = 0;
							if ( $geo_ip_filter_rules ) {
								while($row = $geo_ip_filter_rules->fetchArray(SQLITE3_ASSOC)) {
								$i++;
						?>
								<tr>
									<td style="display:none;"><?php print "1"; ?> </td>
									<td><?php print $row[GeoIpSettingsModel::$COUNTRY_NAME]; ?></td>
									<?php
										if($row[GeoIpSettingsModel::$ALLOWED] == "1")
											$allowed = "checked";
										else
											$allowed = "";
										$id = str_replace(' ','_',$row[GeoIpSettingsModel::$COUNTRY_NAME]);
									?>
									<script type="text/javascript">
										var post; // Pass this variable to set Ajax Post Request
									</script>
									<td>
										<input type="checkbox" <?php print $allowed ?> onchange ="setGeoAllow_Block('<?php echo $id ?>',this,post);" />
									</td>
									<td align="left">
										<input type="button" class="btn-edit edit-geoiplist" onclick="edit_data('<?php echo $id ?>');" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							<?php
								}
							}
							?>
						</tbody>
						<script type="text/javascript">
							var count = '<?php echo $i ?>';
							if(count > 10) {
								$("#GEOIP_body").css("height", "335px");
							}
						</script>
					</table>
				</table>
			</div>
			<div class="page-btns">
			</div>
		</div>
		<form id="deleteform" method="post" allowed="">
			<input type="hidden" name="submit_allowed" value="0" />
			<input type="hidden" name="GEOIP_RULE" id="GEOIP_RULE" value="" />
			<input type="hidden" name="GEOIP_RULE_LIST" id="GEOIP_RULE_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
