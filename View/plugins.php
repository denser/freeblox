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
	include_once("Model/plugins_model.php");
	include_once("Control/date_config.php");

	$_SESSION['pageID'] = "#plugins_help";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$plugin_config =  Plugins_ConfigModel::getInstance();
	$plugin_entries = $plugin_config->getAllPlugins();
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
				$('#plugin').dataTable( {
					"sScrollX": "1200px",
					"sScrollY": "200px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
						{"bSortable": false},
						{"bSortable": false}
					],
					"bPaginate": false
				} );
			} );

			function edit_data(val) {
				parent.callEditPage(val, 'edit_plugins.php', 340, 750);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();">
		<div class="content">
			<h1>Plugins<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="plugin_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="plugin" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td><label>Name</label></td>
								<td><label>Enable</label></td>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							if ( $plugin_entries ) {
								while($row = $plugin_entries->fetchArray(SQLITE3_ASSOC)) {
									$i++;
									if ($row[Plugins_ConfigModel::$ENABLE] == "1")
										$enabled = "checked";
									else
										$enabled = "";
							?>
								<tr>
									<td><?php print $row[Plugins_ConfigModel::$NAME]; ?></td>
									<td><input type="checkbox" id="enable" <?php print $enabled; ?> name="enable" /></td>
									<?php
										$id = str_replace(' ','@',$row[Plugins_ConfigModel::$NAME]);
									?>
									<td align="left">
										<input type="button" class="btn-edit edit-plugin" onclick="edit_data('<?php echo $id; ?>');" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							<?php
								}
							}
							?>
						</tbody>
						<script>
							var count = '<?php echo $i; ?>';
							if(count > 10) {
								$("#plugin_body").css("height", "335px");
								$("#plugin_body").css("overflow-y", "scroll");
							}
						</script>
					</table>
				</table>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
