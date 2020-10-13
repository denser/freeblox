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
	include_once("Model/firewall_model.php");
	include_once("Control/date_config.php");
	include_once("Model/action_mapping_model.php");

	$_SESSION['pageID'] = "#firewall_config";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$firewall = FirewallConfigModel::getInstance();
	$firewallRules = $firewall->getFirewallRules();
	$action_mapping_model = ActionMappingModel::getInstance();
	$action_names = $action_mapping_model->getActionMapping();
	$action_str_array = array();
	while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
		$action_str_array[$row['VAL']] = $row['NAME'];
	}
	$delete = 0;

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['submit_action'] == "3") {
			$firewall->deleteFirewallRule($_POST["FIREWALL_RULE"]); // Delete a rule
			$firewall->createActionList(DEPLOY_MODULES::FIREWALL_CONFIG, "Firewall rule deleted", DEPLOY_MODULES::FIREWALL_CONFIG_STR, "");
			$delete = 1;
		}
		else if($_POST['submit_action'] == "4") { // Delete Multiple

			$del_arr = explode(",", $_POST["FIREWALL_RULE_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				$firewall-> deleteFirewallRule($del_arr[$i]);
				$delete = 1;
			}
			$firewall->createActionList(DEPLOY_MODULES::FIREWALL_CONFIG, "Multiple Firewall rules deleted", DEPLOY_MODULES::FIREWALL_CONFIG_STR, "");
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
				$('#firewall').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "250px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true},
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

			function edit_data(val) {
				parent.callEditPage(val, 'add_edit_firewall.php', 360, 550);
			}

			function add_data() {
				parent.callAddPage('add_edit_firewall.php', 360, 550);
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
					formObj.FIREWALL_RULE.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.FIREWALL_RULE_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function setEnableFirewall(name,fw_enable,post) {
				var enable_val = fw_enable.checked;
				var post_val = "1"; // Sets the Ajax Post Request
				$.ajax({
					type: "POST",
					cache: false,
					data: { fw_name:name, en_fw:enable_val,ajax_post:post_val},
					url:  "add_edit_firewall.php",
					success: function (html) {
						refresh_iframe(1);
					}
				});
			}

			function refresh_iframe(del) {
				if ( del == 1 ) {
					parent.document.getElementById("container").src = "firewall.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()" onload="refresh_iframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Firewall Config<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="firewall_body" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="firewall" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">Src Type</a></th>
								<th><a href="#">Src Addr</a></th>
								<th><a href="#">Dst Type</a></th>
								<th><a href="#">Dst Addr</a></th>
								<th><a href="#">Protocol</a></th>
								<th><a href="#">Port</a></th>
								<th><a href="#">Action</a></th>
								<td width="60px"><label>Enabled</label></td>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							if ( $firewallRules ) {
								while($row = $firewallRules->fetchArray(SQLITE3_ASSOC)) {
									$i++;
									$action = $action_str_array[$row[FirewallConfigModel::$ACTION]];
							?>
								<tr>
									<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[FirewallConfigModel::$NAME]; ?>" onclick="uncheck()" /></td>
									<td><?php echo $row[FirewallConfigModel::$NAME]; ?></td>
									<?php
										if($row[FirewallConfigModel::$ENABLED] == "1")
											$enabled = "checked";
										else
											$enabled = "";
									?>
									<?php
										if( $row[FirewallConfigModel::$SRC_TYPE] == SBC_CONSTANTS::IP_TYPE_ANY )
											$ip_type = SBC_CONSTANTS::IP_TYPE_ANY_STR;
										else if( $row[FirewallConfigModel::$SRC_TYPE] == SBC_CONSTANTS::IP_TYPE_HOST )
											$ip_type = SBC_CONSTANTS::IP_TYPE_HOST_STR;
										else if( $row[FirewallConfigModel::$SRC_TYPE] == SBC_CONSTANTS::IP_TYPE_NETWORK )
											$ip_type = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
										else if( $row[FirewallConfigModel::$SRC_TYPE] == SBC_CONSTANTS::IP_TYPE_RANGE )
											$ip_type = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
										else if( $row[FirewallConfigModel::$SRC_TYPE] == SBC_CONSTANTS::MAC_TYPE_HOST )
											$ip_type = SBC_CONSTANTS::MAC_TYPE_HOST_STR;
									?>
									<td><?php echo $ip_type; ?></td>
									<td><?php echo $row[FirewallConfigModel::$SRC_ADDRESS]; ?></td>
									<?php
										if( $row[FirewallConfigModel::$DST_TYPE] == SBC_CONSTANTS::IP_TYPE_ANY )
											$ip_type = SBC_CONSTANTS::IP_TYPE_ANY_STR;
										else if( $row[FirewallConfigModel::$DST_TYPE] == SBC_CONSTANTS::IP_TYPE_HOST )
											$ip_type = SBC_CONSTANTS::IP_TYPE_HOST_STR;
										else if( $row[FirewallConfigModel::$DST_TYPE] == SBC_CONSTANTS::IP_TYPE_NETWORK)
											$ip_type = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
										else if( $row[FirewallConfigModel::$DST_TYPE] == SBC_CONSTANTS::IP_TYPE_RANGE )
											$ip_type = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
									?>
									<td><?php echo $ip_type; ?></td>
									<td><?php echo $row[FirewallConfigModel::$DST_ADDRESS]; ?></td>
									<td><?php print $row[FirewallConfigModel::$PROTOCOL]; ?></td>
									<td><?php print $row[FirewallConfigModel::$PORT]; ?></td>
									<td><?php print $action; ?></td>
									<script type="text/javascript">
										var post;
									</script>
									<?php
										$name = str_replace(' ', '@', $row[FirewallConfigModel::$NAME]);
									?>
									<td>
										<input name="fw_enable" type="checkbox" <?php print $enabled ?> onchange ="setEnableFirewall('<?php echo $name ?>',this,post);" />
									</td>
									<td align="left">
										<input type="button" class="btn-edit edit-firewall" onclick="edit_data('<?php echo $name?>')" onfocus="if(this.blur)this.blur()" />
										<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[FirewallConfigModel::$NAME] ?>')" onfocus="if(this.blur)this.blur()" />
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
								$("#firewall_body").css("height", "335px");
								$("#firewall_body").css("overflow-y", "scroll");
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
			<input type="hidden" name="FIREWALL_RULE" id="FIREWALL_RULE" value="" />
			<input type="hidden" name="FIREWALL_RULE_LIST" id="FIREWALL_RULE_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
