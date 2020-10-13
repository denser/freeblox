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
	include_once("Model/whitelist_model.php");
	include_once("Control/date_config.php");
	include_once("Model/device_settings_model.php");
	$_SESSION['pageID'] = "#whitelist";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<script src="js/jquery.js"></script>
		<script src="js/jquery-1.8.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.dataTables.js"></script>
		<link rel="stylesheet" type="text/css" href="css/sort.css" media="screen" />
		<script type="text/javascript">
			var submit;
		</script>
		<?php
			$obj = DateTimeConfig::getConfig();
			$update_count = $obj->getActionListCount();
			$res = 0;
			$whitelist = WhiteListConfigModel::getInstance();
			$whitelistRules = $whitelist->getWhiteListRules();
			$dev_settings = DeviceSettingsModel::getInstance();
			$whitelist_preside_val = $dev_settings->get(DeviceSettingsModel::$WHITERULES_PRECEDES);
			if(isset($_POST) && isset($_POST['submit_action'])) {
				if($_POST['submit_action'] == "3") {
					$whitelist->deleteWhiteListRule($_POST["WHITELIST_RULE"]); // Deleting a whitelist rule
					$whitelist->createActionList(DEPLOY_MODULES::WHITELIST_CONFIG, 
						"Whitelist rule deleted", 
						DEPLOY_MODULES::WHITELIST_CONFIG_STR, "");
					$res = 1;
				} else if($_POST['submit_action'] == "4") { // Delete Multiple
					$del_arr = explode(",", $_POST["WHITELIST_RULE_LIST"]);
					for($i = 0; $i < count($del_arr); $i++) {
						$whitelist->deleteWhiteListRule($del_arr[$i]);
						$res = 1;
					}
					$whitelist->createActionList(DEPLOY_MODULES::WHITELIST_CONFIG,
						"All Whitelist rules deleted", 
						DEPLOY_MODULES::WHITELIST_CONFIG_STR,  "");
				}
			}
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#whitelist').dataTable( {
                                        "sScrollX": "1250px",
					"sScrollY": "200px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
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
				parent.callEditPage(val, 'add_edit_whitelist.php', 360, 550);
			}

			function add_data() {
				parent.callAddPage('add_edit_whitelist.php', 360, 550);
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
				var formObj = document.deleteform;
				if(val == 1) {
					formObj.submit_action.value = "3";
					formObj.WHITELIST_RULE.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.deleteform;
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.WHITELIST_RULE_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "whitelist.php";
			}

			function setEnableWhitelist(name,wl_enable,post) {
				var enable_val = wl_enable.checked;
				var post_val = "1"; // Sets the Ajax Post Request
				$.ajax({
					type: "POST",
					cache: false,
					data: { wl_name:name, en_wl:enable_val,ajax_post:post_val},
					url:  "add_edit_whitelist.php",
					success: function (html) {
						refresh_iframe(1);
					}
				});
			}

			function setRulesPrecedesValue ( rules_enable, apost ) {
				var enable_val = rules_enable.checked;
				var post_val = "1"; // Sets the Ajax Post Request
				$.ajax({
					type: "POST",
					cache: false,
					data: { en_rules:enable_val, rules_post:post_val },
					url:  "add_edit_whitelist.php",
					success: function (html) {
						refresh_iframe(1);
					}
				});
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "whitelist.php";
				}
			}

			var val = "Whitelist settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<h1>Whitelist IP Addresses<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="whitelist_body" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
				<table>
					<tr>
					<?php
						if( $whitelist_preside_val == "1" )
							$whitelist_preside_val = "checked";
						else
							$whitelist_preside_val = "";
					?>
						<script type="text/javascript">
							var apost;
						</script>
						<td>
							<input type="checkbox" <?php print $whitelist_preside_val; ?> name="whitelist_over" id="whitelist_over" onchange="setRulesPrecedesValue(this,apost);"  />
						</td>
						<td>
							<label for="whitelist_over">Whitelist IP Rules Precedes over Blacklist IP Rules</label>
						</td>
					</tr>
				</table>
				</form>
					<table id="whitelist" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">IP Type</a></th>
								<th><a href="#">Address</a></th>
								<th><a href="#">Comments</a></th>
								<td width="60px"><label>Enabled</label></td>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
						$i = 0;
						if ( $whitelistRules ) {
							while($row = $whitelistRules->fetchArray(SQLITE3_ASSOC)) {
								$i++;
						?>
							<tr>
								<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[WhiteListConfigModel::$NAME]; ?>" onclick="uncheck()" /></td>
								<td><?php echo $row[WhiteListConfigModel::$NAME] ?></td>
						<?php
								if($row[WhiteListConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_ANY )
									$ip_type = SBC_CONSTANTS::IP_TYPE_ANY_STR;
								else if($row[WhiteListConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_HOST )
									$ip_type = SBC_CONSTANTS::IP_TYPE_HOST_STR;
								else if($row[WhiteListConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_NETWORK )
									$ip_type = SBC_CONSTANTS::IP_TYPE_NETWORK_STR;
								else if($row[WhiteListConfigModel::$IP_TYPE] == SBC_CONSTANTS::IP_TYPE_RANGE )
									$ip_type = SBC_CONSTANTS::IP_TYPE_RANGE_STR;
								else if($row[WhiteListConfigModel::$IP_TYPE] == SBC_CONSTANTS::MAC_TYPE_HOST )
									$ip_type = SBC_CONSTANTS::MAC_TYPE_HOST_STR;
						?>
								<td><?php echo $ip_type; ?></td>
								<td><?php echo $row[WhiteListConfigModel::$NET_ADDRESS]; ?></td>
								<?php
								if($row[WhiteListConfigModel::$ENABLED] == "1")
									$enabled = "checked";
								else
									$enabled = "";
								?>
								<script type="text/javascript">
									var post;
								</script>
								<?php
									$id = str_replace(' ','@', $row[WhiteListConfigModel::$NAME]);
									$comments = substr($row[WhiteListConfigModel::$COMMENTS],0,20);
								?>
								<td><a href="javascript:;" class="ttip" title="<?php print $row[WhiteListConfigModel::$COMMENTS];?>"><?php echo $comments; ?></a></td>
								<td>
									<input type="checkbox" name="enable_wl" <?php echo $enabled; ?> onchange= "setEnableWhitelist('<?php print $id ?>',this,post);" />
								</td>
								<td align="left">
									<input type="button" class="btn-edit edit-whitelist" onclick="edit_data('<?php echo $id;  ?>');" onfocus="if(this.blur)this.blur();" />
									<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[WhiteListConfigModel::$NAME] ?>');" onfocus="if(this.blur)this.blur();" />
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
								$("#whitelist_body").css("height", "335px");
								$("#whitelist_body").css("overflow-y", "scroll");
							}
						</script>
					</table>
				</table>
			</div>
			<div class="page-btns">
				<a class="btn-grey-wide-l" onclick="add_data()"><span>Add New</span></a>
				<a id="delete_selected" class="btn-grey-wide-l-disabled"><span>Delete Selected</span></a>
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
			</div>
		</div>
		<form id="deleteform" name="deleteform" method="post" action="">
			<input type="hidden" name="submit_action" value="0" />
			<input type="hidden" name="WHITELIST_RULE" id="WHITELIST_RULE" value="" />
			<input type="hidden" name="WHITELIST_RULE_LIST" id="WHITELIST_RULE_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		if( submit == "1" ) {
			save_Message(val);
		}
	</script>
</html>
