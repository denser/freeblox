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
	include_once("Model/dpi_config_model.php");
	include_once("Control/date_config.php");
	include_once("Model/action_mapping_model.php");

	$_SESSION['pageID'] = "#dpi_sigs";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$dpi_config_model = DpiConfigModel::getInstance();
	$action_mapping_model = ActionMappingModel::getInstance();
	$dpisigs = $dpi_config_model->getDpiConfigAll();
	$action_names = $action_mapping_model->getActionMapping();
	$action_str_array = array();
	while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
		$action_str_array[$row['VAL']] = $row['NAME'];
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
			$('#dpiconfig').dataTable( {
				"sScrollX": "1250px",
				"sScrollY": "250px",
				"aaSorting": [[ 1, "asc" ]],
				"aoColumns":[
					{"bSortable": false},
					{"bSortable": false},
					{"bSortable": false},
					{"bSortable": false},
					{"bSortable": false},
					{"bSortable": false},
					{"bSortable": false}
				],
				"bFilter": false,
				"bInfo": false,
				"bPaginate": false
			} );
		});

		function edit_data(val) {
			parent.callEditPage(val, 'add_edit_dpi.php', 360, 640);
		}

		function enableDPI(enable,category) {
			var enable_val = enable.checked;
			$.ajax({
				type: "POST",
				cache: false,
				data: {en_dis:enable_val,cat_name:category},
				url:  "add_edit_dpi.php",
				success: function (html) {
					refresh_iframe(1);
				}
			});
		}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "dpi_signatures.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<h1>SIP Attacks Detection<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="dpiconfig_body" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="dpiconfig" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td style="display:none;"><label>1</label></td>
								<td style="display:none;"><label>ID</label></td>
								<td><label>Category</label></td>
								<td><label>Action</label></td>
								<td><label>Blocking Duration (seconds)</label></td>
								<td><label>Enabled</label></td>
								<td><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
						$i = 0;
						if ( $dpisigs ) {
							while($row = $dpisigs->fetchArray(SQLITE3_ASSOC)) {
								$i++;
								if ( $row[DpiConfigModel::$ACTION] == 4 ) {
									//Dont show option blacklist explicitly in UI. action = block & block_duration != 0 => blacklist
									$row[DpiConfigModel::$ACTION] = 1;
								}
								$action = $action_str_array[$row[DpiConfigModel::$ACTION]];
						?>
						<tr>
							<td style="display:none;">1</td>
							<td style="display:none;"><?php print $row[DpiConfigModel::$ID]; ?></td>
							<?php
								if($row[DpiConfigModel::$ENABLED] == "1")
									$enabled = "checked";
								else
									$enabled = "";
							?>

							<td><?php print $row[DpiConfigModel::$UI_NAME]; ?></td>
							<td><?php print $action; ?></td>
							<?php
								$bl_duration = $row[DpiConfigModel::$BL_DURATION];
								if ( $bl_duration == "0" || $bl_duration == "" )
									$bl_duration = "none";
							?>
							<td><?php print $bl_duration; ?></td>
							<td>
								<input type="checkbox" <?php print $enabled; ?> onchange="enableDPI(this,'<?php print $row[DpiConfigModel::$CATEGORY]?>');"  />
							</td>
							<td align="left">
								<input type="button" class="btn-edit edit-dpiconfig" onclick="edit_data('<?php echo $row[DpiConfigModel::$CATEGORY] ?>')" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<?php
							}
						}
						?>
						</tbody>
						<script>
		/*					var count = <?php echo $i ?>;
							if(count > 10) {
								$("#dpiconfig_body").css("height", "335px");
								$("#dpiconfig_body").css("overflow-y", "scroll");
							} */
						</script>
					</table>
				</table>
			</div>
			<div class="page-btns">
			</div>
		</div>
		<form id="deleteform" method="post" action="">
			<input type="hidden" name="submit_action" value="0" />
			<input type="hidden" name="DPICONFIG_CATEGORY" id="DPICONFIG_CATEGORY" value="" />
			<input type="hidden" name="DPICONFIG_CATEGORY_LIST" id="DPICONFIG_CATEGORY_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
