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
	include_once("Control/date_config.php");
	include_once('Model/device_settings_model.php');
	include_once('Model/sys_calls.php');
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#geo_ip_update";
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
			var val = "Geo IP Update is in progress, Please wait....";
			var success = "Geo IP Update successfully applied.";

			function sig_UpdateMsg(val) {
				parent.geoIPMessage(val);
			}

			function updateGeoIPUpdate() {
				setTimeout(sig_UpdateMsg(val),5);
				$.ajax({
					type: "POST",
					cache: false,
					data: {geo_update:1},
					url: "run_geoip_update.html",
					success: function (html) {
						save_Message(success);
					}
				 });
			}

			function submitData() {
				var formObj = document.forms[0];
				formObj.geo_ip_update.value = "1";
				formObj.submit();
			}

			function refresh_iframe(val) {
				if(val == 1) {
					//parent.document.getElementById("container").src = "geo_ip_update.php";
				}
			}

			function save_Message(val) {
				parent.geoIPMessage(val);
			}
		</script>
		<?php
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();
			$dev_settings = DeviceSettingsModel::getInstance();
			$dpi_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_DPI);
			$res = 0;
			if($_POST['geo_ip_update'] == 1) {
		?>
				<script type="text/javascript">
					updateGeoIPUpdate();
				</script>
		<?php
				$res = 1;
			}
		?>
	</head>
	<body onclick="javascript: parent.hideMenu()" onload = "refresh_iframe('<?php print $res; ?>');">
		<div class="content">
			<h1>Geo IP Update<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="geo_ip_update" method="post" action="geo_ip_update.php">
			<table border="0" cellpadding="0" cellspacing="0" class="table-config no-border">
				<tr>
					<td>
						<a class="btn-grey-wide" name=""  onclick="submitData();"><span id="dpi_value">Update Geo IP</span></a>
					</td>
				</tr>
			</table>
		</div>
		<div>
			<input type="hidden" name="geo_ip_update" value="0" />
			<label style="font-size: 12px; display: none" id="system_time"></label>
			<label style="font-size: 12px; display: none" id="table_time"></label>
		</div>
		</form>
	<div id="DownloadConfiguration" class="jqmDialog"></div>
	</body>
	<?php
	include_once ("time_panel.php");
	$shell_obj = ShellCommand::getInstance();
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
	</script>
</html>
