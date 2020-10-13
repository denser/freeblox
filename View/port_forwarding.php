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
	include_once("Model/port_fwd_model.php");
	include_once("Control/date_config.php");
	include_once("Model/devicelist_settings_model.php");

	$_SESSION['pageID'] = "#firewall_portfwd";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$port_fwd_config =  Port_Forwarding_Model::getInstance();
	$port_fwd_rules = $port_fwd_config->getPortFwdRules();
	$delete = 0;

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['submit_action'] == "3") {
			$port_fwd_config->deletePortFwdRule($_POST["PORT_FWD_RULE"]); // Delete a rule
			$port_fwd_config->createActionList(DEPLOY_MODULES::PORT_FORWARDING_CONFIG, 
					"Port Forwarding rule deleted", 
					DEPLOY_MODULES::PORT_FORWARDING_CONFIG_STR, "");
			$delete = 1;
		} else if($_POST['submit_action'] == "4") { // Delete Multiple
			$del_arr = explode(",", $_POST["PORT_FWD_RULE_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				$port_fwd_config-> deletePortFwdRule($del_arr[$i]);
				$delete = 1;
			}
			$port_fwd_config->createActionList(DEPLOY_MODULES::PORT_FORWARDING_CONFIG, 
				"All Port Forwarding rules deleted", 
				DEPLOY_MODULES::PORT_FORWARDING_CONFIG_STR, "");
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
				$('#portfwdconfig').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "200px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": false}
					],
					"bPaginate": false
				} );

				$("#checkall").click(function() {
					if ($("#checkall").is(":checked")) {
						$("input[type='checkbox']").attr("checked", "checked");
						$("#delete_selected").attr('class','btn-grey-wide-l');
						$('#delete_selected').click(function() {
							delete_data_all();
						});
					} else {
						$("input[type='checkbox']").attr("checked", "");
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

			function edit_data(val) {
				parent.callEditPage(val, 'add_edit_port_fwd.php', 380, 560);
			}

			function add_data() {
				parent.callAddPage('add_edit_port_fwd.php', 380, 560);
			}

			function delete_data(val) {
				parent.callConfirmBox(val);
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
					formObj.PORT_FWD_RULE.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.PORT_FWD_RULE_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "port_forwarding.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Port Forwarding<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="portfwd_config_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="portfwdconfig" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">Interface</a></th>
								<th><a href="#">Protocol</a></th>
								<th><a href="#">External Port</a></th>
								<th><a href="#">Internal Address </a></th>
								<th><a href="#">Internal Port</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
							$i = 0;
							if ( $port_fwd_rules ) {
								while($row = $port_fwd_rules->fetchArray(SQLITE3_ASSOC)) {
									$i++;
						?>
							<tr>
								<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php print $row[Port_Forwarding_Model::$NAME];; ?>" onclick="uncheck()" /></td>
								<td><?php print $row[Port_Forwarding_Model::$NAME]; ?></td>
								<td><?php print $row[Port_Forwarding_Model::$INTERFACE]; ?> </td>
								<td><?php print $row[Port_Forwarding_Model::$PROTOCOL]; ?></td>
								<td><?php print $row[Port_Forwarding_Model::$EXTERNAL_PORT]; ?></td>
								<td><?php print $row[Port_Forwarding_Model::$INTERNAL_ADDRESS]; ?></td>
								<td><?php print $row[Port_Forwarding_Model::$INTERNAL_PORT]; ?></td>
						<?php
							$comments = substr($row[Port_Forwarding_Model::$DESCRIPTION],0,20);
							$id = str_replace(' ','@',$row[Port_Forwarding_Model::$NAME]);
						?>
						<td align="left">
							<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $id; ?>')" onfocus="if(this.blur)this.blur()" />
							<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[Port_Forwarding_Model::$NAME]; ?>')" onfocus="if(this.blur)this.blur()" />
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
								$("#portfwd_config_body").css("height", "335px");
								$("#portfwd_config_body").css("overflow-y", "scroll");
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
			<input type="hidden" name="PORT_FWD_RULE" id="PORT_FWD_RULE" value="" />
			<input type="hidden" name="PORT_FWD_RULE_LIST" id="PORT_FWD_RULE_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
