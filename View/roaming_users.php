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
	include_once("Model/roaming_users_model.php");
	include_once("Control/date_config.php");
	include_once("Model/devicelist_settings_model.php");

	$_SESSION['pageID'] = "#roaming_user_settings";
	$obj = DateTimeConfig::getConfig();
	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$internalinterface=$devicelist_settings_config->getsipnamesvalues(0);
	$externalinterface=$devicelist_settings_config->getsipnamesvalues(1);
	$internalinterfacenames=$devicelist_settings_config->getsipnames(0);
	$externalinterfacenames=$devicelist_settings_config->getsipnames(1);
	$_SESSION['pageID'] = "#mgmt_access";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$roaming_users_config = RoamingUsersModel::getInstance();
	$roaming_users = $roaming_users_config->getRoamingUsers();
	$delete = 0;

	if(isset($_POST) && isset($_POST['submit_action'])) {
		$delarray = array();
		$dset = 0;
		$delcheck = 0;
		if($_POST['submit_action'] == "3") {
			$subscribercheck = $roaming_users_config->checkroamingname($_POST["ROAMING_USERS"]);
			$eventscheck = $roaming_users_config->checkroamingevents($_POST["ROAMING_USERS"]);
			$delcheck = $subscribercheck + $eventscheck;
			if($delcheck == 0) {
				$roaming_users_config->deleteRoamingUser($_POST["ROAMING_USERS"]);
				$roaming_users_config->createActionList(
						DEPLOY_MODULES::SBC_CONFIG, 
						"Roaming User deleted", 
						DEPLOY_MODULES::SBC_CONFIG_STR, "");
				$delete = 1;
			} else {
				$val = $_POST["ROAMING_USERS"];
				array_push($delarray, $val);
				$dset = 1;
			}
		} else if($_POST['submit_action'] == "4") { // Delete Multiple
			$del_arr = explode(",", $_POST["ROAMING_USERS_LIST"]);
			$set = 0;
			for($i = 0; $i < count($del_arr); $i++) {
				$delcheck = 0;
				$subscribercheck = $roaming_users_config->checkroamingname($del_arr[$i]);
				$eventscheck = $roaming_users_config->checkroamingevents($del_arr[$i]);
				$delcheck = $subscribercheck + $eventscheck;
				if($delcheck == 0) {
					$set = 1;
					$roaming_users_config-> deleteRoamingUser($del_arr[$i]);
					$delete = 1;
				} else {
					$val = $del_arr[$i];
					array_push($delarray, $val);
					$dset = 1;
					}
			}
			if($set != 0) {
				$roaming_users_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, 
					"All Roaming Users are deleted", 
					DEPLOY_MODULES::SBC_CONFIG_STR, "");
				$delete = 1; 
			}
		}
		if ($dset == 1) {
			$str = "";
			for($j=0; $j<count($delarray); $j++) {
				$str .= " ".$delarray[$j]." ";
			}
			print "<Div class='deleteAllSipProfiles'>";
			print "Can't delete the $str Profiles as they are being used by Presence Subscribers and Event profiles .";
			print "</Div>";
			$delete = 0;
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
				$('#trunkconfig').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "200px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
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
				parent.callEditPage(val, 'add_edit_roam_users.php', 360, 800);
			}

			function add_data() {
				parent.callAddPage('add_edit_roam_users.php', 360, 750);
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
					formObj.ROAMING_USERS.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.ROAMING_USERS_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "roaming_users.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Roaming Users<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="roaming_config_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="trunkconfig" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">Force Expire</a></th>
								<th><a href="#">Description</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							if ( $roaming_users ) {
								while($row = $roaming_users->fetchArray(SQLITE3_ASSOC)) {
									$i++;
							?>
							<tr>
								<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[RoamingUsersModel::$NAME]; ?>" onclick="uncheck()" /></td>
								<td><?php echo $row[RoamingUsersModel::$NAME]; ?></td>
								<td><?php echo $row[RoamingUsersModel::$FORCE_EXPIRE]; ?></td>
								<?php $comments = substr($row[RoamingUsersModel::$DESCRIPTION],0,20); ?>
								<td><a href=""title="<?php print $row[RoamingUsersModel::$DESCRIPTION]; ?>"><?php echo $comments; ?></a></td>
							<?php
								$id = str_replace(' ', '@',$row[RoamingUsersModel::$NAME]);
							?>
								<td align="left">
									<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $id; ?>')" onfocus="if(this.blur)this.blur()" />
									<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[RoamingUsersModel::$NAME]; ?>')" onfocus="if(this.blur)this.blur()" />
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
								$("#roaming_config_body").css("height", "335px");
								$("#roaming_config_body").css("overflow-y", "scroll");
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
			<input type="hidden" name="ROAMING_USERS" id="ROAMING_USERS" value="" />
			<input type="hidden" name="ROAMING_USERS_LIST" id="ROAMING_USERS_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
