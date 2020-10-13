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
	include_once('Model/subscribers_model.php');
	include_once("Control/date_config.php");
	include_once("Control/web_users.php");
	include_once("Model/sys_calls.php");

	$_SESSION['pageID'] = "#presence_settings";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$subscribers_settings_config = SubscribersSettingsModel::getInstance();
	$subscribers_settings = $subscribers_settings_config->getAllsubscribersSettings();

	if(isset($_POST) && isset($_POST['submit_action'])) {
		$delarray = array();
		$dset = 0;
		$delcheck = 0;
		if($_POST['submit_action'] == "3") {
			$delcheck = $subscribers_settings_config->checksubscriber($_POST["DELETE_RULE"]);
			if($delcheck == 0) {
				//$delarray = $_POST["DELETE_RULE"];
				$subscribers_settings_config->deletesubscribersSettings($_POST["DELETE_RULE"]);
				$subscribers_settings_config->createActionList(
					DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG, 
					"subscribers profile deleted", 
					DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG_STR, "");
					$delete = 1;
			} else {
				$val = $subscribers_settings_config-> getusername($_POST["DELETE_RULE"]);
				array_push($delarray, $val);
				$dset = 1;
			}
		}
		if($_POST['submit_action'] == "4") {
			$del_arr = explode(",", $_POST["SUBSCRIBER_SETTINGS_LIST"]);
			$set = 0;
			for($i = 0; $i < count($del_arr); $i++) {
				$delcheck = 0;
				$delcheck = $subscribers_settings_config->checksubscriber($del_arr[$i]);
				if($delcheck == 0) {
					$set = 1;
					$subscribers_settings_config->deletesubscribersSettings($del_arr[$i]);
				} else {
					$val = $subscribers_settings_config-> getusername($del_arr[$i]);
					array_push($delarray, $val);
					$dset = 1;

				}
			}
			if($set != 0) {
				$subscribers_settings_config->createActionList(
					DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG, 
					" All subscribers profiles deleted", 
					DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG_STR, "");
				$delete = 1; 
			}
		}
		if ($dset == 1) {
			$str = "";
			for($j=0; $j<count($delarray); $j++) {
				$str .= " ".$delarray[$j]." ";
			}
			print "<Div class='deleteAllSipProfiles'>";
			print "Can't delete the $str Profiles as they are being used by Presence Event profiles .";
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
		<link rel="stylesheet" type="text/css" href="css/tabs.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="css/sort.css" media="screen" />
		<script type="text/javascript">
			$(document).ready(function() {
				$('#subscribersettings').dataTable( {
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
				parent.callEditPage(val, 'add_edit_subscribers.php', 360, 700);
			}

			function add_data() {
				parent.callAddPage('add_edit_subscribers.php', 360, 700);
			}

			function delete_data(val) {
				var formObj = document.forms[0];
				formObj.submit_action.value = "3";
				formObj.DELETE_RULE.value = val;
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
					alert("There are no records to be deleted!");
					return false;
				} else {
					parent.callConfirmAllBox(rule_list);
				}
			}

			function deleteAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "3";
					formObj.SUBSCRIBER_SETTINGS.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.SUBSCRIBER_SETTINGS_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "subscribers.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Subscribers setting Profile<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="roaming_config_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="subscribersettings" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">User Name</a></th>
								<th><a href="#">Roaming User Profiles</a></th>
								<th><a href="#">Operator</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
							$i = 0;
							if ( $subscribers_settings ) {
								while($row = $subscribers_settings->fetchArray(SQLITE3_ASSOC)) {
									$i++;
						?>
							<tr>
								<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[SubscribersSettingsModel::$ID]; ?>" onclick="uncheck()" /></td>
								<td><?php echo $row[SubscribersSettingsModel::$USERNAME]; ?></td>
								<td><?php echo $row[SubscribersSettingsModel::$ROAMUSERPROFILE]; ?></td>	
								<td><?php if($row[SubscribersSettingsModel::$OPERATOR] == 1) echo "operator"; ?></td>	
								<td align="left">
									<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $row[SubscribersSettingsModel::$ID]; ?>')" onfocus="if(this.blur)this.blur()" />
									<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[SubscribersSettingsModel::$ID]; ?>')" onfocus="if(this.blur)this.blur()" />
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
			<input type="hidden" name="SUBSCRIBER_SETTINGS" id="SUBSCRIBER_SETTINGS" value="" />
			<input type="hidden" name="DELETE_RULE" id="DELETE_RULE" value="" />
			<input type="hidden" name="SUBSCRIBER_SETTINGS_LIST" id="SUBSCRIBER_SETTINGS_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
