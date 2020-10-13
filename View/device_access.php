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
	include_once("Model/device_access_model.php");
	include_once("Control/date_config.php");

	$_SESSION['pageID'] = "#device_access_settings";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$device_access =  DeviceAccessConfigModel::getInstance();
	$deviceaccessRules = $device_access->getDeviceAccessRules();
	$delete = 0;

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['submit_action'] == "3") {
			$device_access->deleteDeviceAccessRule($_POST["DEVICE_ACCESS_FILTER"]); // Delete a rule
			$device_access->createActionList(DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG, 
					"Device Access rule deleted", 
					DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG_STR, "");
			$delete = 1;
		} else if($_POST['submit_action'] == "4") { // Delete Multiple
			$del_arr = explode(",", $_POST["DEVICE_ACCESS_FILTER_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				$device_access-> deleteDeviceAccessRule($del_arr[$i]);
				$delete = 1;
			}
			$device_access->createActionList(DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG, 
					"All Device Access rules deleted", 
					DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG_STR, "");
		}
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
				$('#deviceaccess').dataTable( {
					"sScrollX": "1200px",
					"sScrollY": "200px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": false},
						{"bSortable": false}
					],
					"bPaginate": false
				} );

				$("#checkall").click(function() {
					if ($("#checkall").is(":checked")) {
						$("input:checkbox[name='chkbox']").attr("checked", "checked");
						$("#delete_selected").attr('class','btn-grey-wide-l');
						$('#delete_selected').click(function() {
							delete_data_all();
						});
					} else {
						$("input:checkbox[name='chkbox']").attr("checked", "");
						$("#delete_selected").attr('class','btn-grey-wide-l-disabled');
						$('#delete_selected').attr('onclick','').unbind('click');
					}
				});
			});

			function uncheck() {
				var i = 0;
				$("input:checkbox[name='chkbox']:checked").each(function() {
					i++;
				});
				if(i == 0) {
					$("#delete_selected").attr('class','btn-grey-wide-l-disabled');
					$('#delete_selected').attr('onclick','').unbind('click');
				} else {
					$("#delete_selected").attr('class','btn-grey-wide-l');
					$('#delete_selected').click(function() {
						delete_data_all();
					});
				}
				if ($("#checkall").is(":checked")) {
					$("#checkall").attr("checked", "");
				}
			}

			function edit_data(val,edit_flag) {
				if ( edit_flag == "1" ) {
					var edit_val = "Default management access entry can not be edited.";
					parent.mgmtAccessDefault(edit_val);
				} else {
					parent.callEditPage(val, 'add_edit_device_access.php', 360, 550);
				}
			}

			function add_data() {
				parent.callAddPage('add_edit_device_access.php', 360, 550);
			}

			function delete_data(val,del_flag) {
				if ( del_flag == "1" ) {
					var delete_val = "Default management access entry can not be deleted.";
					parent.mgmtAccessDefault(delete_val);
				} else {
					parent.callConfirmBox(val);
				}
			}

			function delete_data_all() {
				var all_rules = new Array();
				var i = 0;
				$("input:checkbox[name='chkbox']:checked").each(function() {
					all_rules[i++] = $(this).val();
				});
				var rule_list = all_rules.toString();
				if( rule_list.length < 1 ) {
					alert("There are no rules to be deleted!");
					return false;
				} else {
					parent.callConfirmAllBox(rule_list);
				}
			}

			function deleteAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "3";
					formObj.DEVICE_ACCESS_FILTER.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.DEVICE_ACCESS_FILTER_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function setEnableDeviceAccess( name, da_enable, post ) {
				var enable_val = da_enable.checked;
				var post_val = "1"; // Sets the Ajax Post Request
				$.ajax({
					type: "POST",
					cache: false,
					data: { da_name:name, en_da:enable_val,ajax_post:post_val},
					url:  "add_edit_device_access.php",
					success: function (html) {
						refreshIframe(1);
					}
				});
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "device_access.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Device Access<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="device_access_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="deviceaccess" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">IP Type</a></th>
								<th><a href="#">Protocols</a></th>
								<th><a href="#">Address</a></th>
								<th><a href="#">Comments</a></th>
								<td width="60px"><label>Enabled</label></td>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							if ( $deviceaccessRules ) {
								while($row = $deviceaccessRules->fetchArray(SQLITE3_ASSOC)) {
									$i++;
							?>
								<tr>
							<?php
									if ( $row[DeviceAccessConfigModel::$NAME] == "MgmtVlanAccess") {
										$del_flag = "1";
										$edit_flag = "1";
									} else {
										$del_flag = "";
										$edit_flag = "";
									}
							?>
							<?php if ( $row[DeviceAccessConfigModel::$NAME] == "MgmtVlanAccess") {
							?>
								<td><input disabled="disabled" id="checkbox<?php echo $i ?>" name="chkbox"type='checkbox' align="center" /></td>
							<?php } else { ?>
								<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[DeviceAccessConfigModel::$NAME]; ?>" onclick="uncheck()" /></td>
							<?php } ?>
								<td><?php echo $row[DeviceAccessConfigModel::$NAME]; ?></td>
							<?php
								if( $row[DeviceAccessConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_ANY )
									$ip_type = SBC_CONSTANTS::IP_TYPE_ANY_STR;
								if( $row[DeviceAccessConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_HOST )
									$ip_type = SBC_CONSTANTS::IP_TYPE_HOST_STR;
									else if( $row[DeviceAccessConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_NETWORK )
											$ip_type = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
										else if( $row[DeviceAccessConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_RANGE )
											$ip_type = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
										else if( $row[DeviceAccessConfigModel::$IP_TYPE] == SBC_CONSTANTS::MAC_TYPE_HOST)
											$ip_type = SBC_CONSTANTS::MAC_TYPE_HOST_STR;
							?>
								<td><?php echo $ip_type; ?></td>
								<td><?php echo $row[DeviceAccessConfigModel::$PROTOCOL]; ?></td>
								<td><?php echo $row[DeviceAccessConfigModel::$NET_ADDRESS]; ?></td>
							<?php
								if($row[DeviceAccessConfigModel::$ENABLED] == "1")
									$enabled = "checked";
								else
									$enabled = "";
							?>
							<?php
								$comments = substr($row[DeviceAccessConfigModel::$COMMENTS],0,20);
							?>
								<td><a href="javascript:;" class="ttip" title="<?php echo $row[DeviceAccessConfigModel::$COMMENTS]; ?>"><?php echo $comments; ?></a></td>
							<?php
								$id = str_replace(' ', '@',$row[DeviceAccessConfigModel::$NAME]);
							?>
							<script type="text/javascript">
								var post;
							</script>
							<?php if ( $row[DeviceAccessConfigModel::$NAME] == "MgmtVlanAccess") {
							?>
								<td>
									<input type="checkbox" name="enable_da" disabled="disabled" <?php echo $enabled; ?> >
								</td>
							<?php } else { ?>
								<td>
									<input type="checkbox" name="enable_da" <?php echo $enabled; ?> onchange = "setEnableDeviceAccess('<?php echo $id; ?>',this,post);" >
								</td>
							<?php
								}
							?>
							<td align="left">
								<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $id; ?>','<?php print $edit_flag ?>')" onfocus="if(this.blur)this.blur()" />
								<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[DeviceAccessConfigModel::$NAME]; ?>','<?php print $del_flag ?>')" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<?php
							}
							}
						?>
						</tbody>
						<script>
							var count = <?php echo $i ?>;
							if(count > 10) {
								$("#device_access_body").css("height", "335px");
								$("#device_access_body").css("overflow-y", "scroll");
							}
						</script>
					</table>
				</table>
			</div>
			<div class="page-btns">
				<a class="btn-grey-wide-l" onclick="add_data()"><span>Add New</span></a>
				<a id="delete_selected" class="btn-grey-wide-l-disabled"><span>Delete Selected</span></a>
			</div>
		</div>
		<form id="deleteform" method="post" action="">
			<input type="hidden" name="submit_action" value="0" />
			<input type="hidden" name="DEVICE_ACCESS_FILTER" id="DEVICE_ACCESS_FILTER" value="" />
			<input type="hidden" name="DEVICE_ACCESS_FILTER_LIST" id="DEVICE_ACCESS_FILTER_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
