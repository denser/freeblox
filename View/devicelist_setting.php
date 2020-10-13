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
	include_once('Model/devicelist_settings_model.php');
	include_once("Control/date_config.php");
	include_once("Control/web_users.php");
	include_once("Model/sys_calls.php");

	$_SESSION['pageID'] = "#gen_settings";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$devicelist_settings = $devicelist_settings_config->getAlldevicelistSettings();
	$checkarch =SysCalls::getSysArch();

	if(isset($_POST) && isset($_POST['submit_action'])) {
		$delarray = array();
		$dset =0;
		if($_POST['submit_action'] == "3") {
			$delcheck = 0;
			$delvirtipcheck = $devicelist_settings_config->delvirtualipcheck($_POST["DELETE_RULE"]);
			$delvlancheck = $devicelist_settings_config->delvlancheck($_POST["DELETE_RULE"]);
			$delroutercheck = $devicelist_settings_config->delroutercheck($_POST["DELETE_RULE"]);
			$delportforwcheck = $devicelist_settings_config->delportforwcheck($_POST["DELETE_RULE"]);
			$delmediacheck = $devicelist_settings_config->delmediacheck($_POST["DELETE_RULE"]);
			$delsipprcheck = $devicelist_settings_config->delsipprcheck($_POST["DELETE_RULE"]);
			$delcheck = $delvirtipcheck + $delvlancheck + $delroutercheck + $delportforwcheck + $delmediacheck + $delsipprcheck;

			if($delcheck == 0) {
				$devicelist_settings_config->deletedevicelistSettings($_POST["DELETE_RULE"]);
				$devicelist_settings_config->createActionList(
						DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG, 
						"devicelist profile deleted", 
						DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG_STR, "");
				$delete = 1;
			} else {
				$val = $devicelist_settings_config->getintername($_POST["DELETE_RULE"]);
				array_push($delarray, $val);
				$dset = 1;
			}
		}
		if($_POST['submit_action'] == "4") {
			$del_arr = explode(",", $_POST["DEVICELIST_SETTINGS_LIST"]);
			$set = 0;
			for($i = 0; $i < count($del_arr); $i++) {
				$delcheck = 0;
				$delvirtipcheck = $devicelist_settings_config->delvirtualipcheck($del_arr[$i]);
				$delvlancheck = $devicelist_settings_config->delvlancheck($del_arr[$i]);
				$delroutercheck = $devicelist_settings_config->delroutercheck($del_arr[$i]);
				$delportforwcheck = $devicelist_settings_config->delportforwcheck($del_arr[$i]);
				$delmediacheck = $devicelist_settings_config->delmediacheck($del_arr[$i]);
				$delsipprcheck = $devicelist_settings_config->delsipprcheck($del_arr[$i]);
				$delcheck = $delvirtipcheck + $delvlancheck + $delroutercheck + $delportforwcheck + $delmediacheck + $delsipprcheck;

				if($delcheck == 0) {
					$set = 1;
					$devicelist_settings_config->deletedevicelistSettings($del_arr[$i]);
				} else {
					$val = $devicelist_settings_config->getintername($del_arr[$i]);
					array_push($delarray, $val);
					$dset = 1;
				}
			}
			if($set != 0) {
				$devicelist_settings_config->createActionList(
					DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG, 
					" All devicelist profiles deleted", 
					DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG_STR, "");
				$delete = 1;
			}
		}
		if ($dset == 1) {
			$str = "";
			for($j=0; $j<count($delarray); $j++) {
				$str .= " ".$delarray[$j]." ";
			}
			print "<Div class='deleteAllSipProfiles'>";
			print "Can't delete the $str Profiles as they are being used by SIP Profiles / Virtualip / VLAN / Routers / PORT FORWARDING / Media profiles .";
			print "</Div>";
			$delete = 0;
		}
	}

// Set Enable /Disable parameter via Ajax Post request
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['en_fw'] == "true")
			$post_enable_val = 1;
		else if($_POST['en_fw'] == "false")
			$post_enable_val = 0;
		$fw_name = $_POST['fw_name'];
		$db_val = $devicelist_settings_config->getDbDefaultVal($fw_name);
		if( $post_enable_val != $db_val ) {
			$devicelist_settings_config->defaultinterface($fw_name,$post_enable_val);
			$devicelist_settings_config->createActionList(
					DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG, 
					" All devicelist profiles are edited", 
					DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG_STR, "");
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
				$('#devicelistsettings').dataTable( {
					"sScrollX": "1200px",
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
				parent.callEditPage(val, 'add_edit_devicelist_settings.php', 360, 600);
			}

			function add_data() {
				parent.callAddPage('add_edit_devicelist_settings.php', 360, 600);
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
					formObj.DEVICELIST_SETTINGS.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.DEVICELIST_SETTINGS_LIST.value = deletetoken;
					formObj.submit();
				}
			}


			function setEnableFirewall(name,fw_enable,post) {
				var formObj = document.forms[0];
				var enable_val = fw_enable.checked;
				var post_val = "1"; // Sets the Ajax Post Request
				$.ajax({
					type: "POST",
					cache: false,
					data: { fw_name:name, en_fw:enable_val,ajax_post:post_val},
					url:  "devicelist_setting.php",
					success: function (html) {
						refreshIframe(1);
					}
				});
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "devicelist_setting.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Network Interface Profile<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="roaming_config_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="devicelistsettings" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<?php
								$hidden = "";
								if($checkarch == "armv7l") {
									$hidden = 'style="display:none"';
								}
							?>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Interface Name</a></th>
								<th><a href="#">Interface</a></th>
								<th><a href="#">Ip Type</a></th>
								<th><a href="#">Device ID (MAC ID)</a></th>
								<td width="60px" <?php print $hidden; ?>><label>Default Gateway</label></td>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
						$i = 0;
						if ( $devicelist_settings ) {
							while($row = $devicelist_settings->fetchArray(SQLITE3_ASSOC)) {
								$i++;
								$defgatway = "";
								if($row[DevicelistSettingsModel::$DEF_GWT] == 1) {
									$defgatway = "(Default Gateway)";
								}
								if($row[DevicelistSettingsModel::$DEF_GWT] == "1")
									$enabled = "checked";
								else
									$enabled = "";
						?>
							<tr>
								<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[DevicelistSettingsModel::$ID]; ?>" onclick="uncheck()" /></td>
								<td><?php echo $row[DevicelistSettingsModel::$INTER_NAME]; ?></td>
								<td>
								<?php
									if($row[DevicelistSettingsModel::$I_TYPE] == 0){ 
										echo "Internal";
									} else if($row[DevicelistSettingsModel::$I_TYPE] == 1){ 
										echo "External";
									} else if($row[DevicelistSettingsModel::$I_TYPE] == 2){ 
										echo "Transcoding";
									}
								?>
								</td>
								<td>
								<?php 
									if($row[DevicelistSettingsModel::$IP_TYPE] == 0){
										echo "Static";
									} else {
										echo "DHCP";
									}
								?>
								</td>
								<td>
								<?php echo $row[DevicelistSettingsModel::$DEV_ID]." - (".$row[DevicelistSettingsModel::$MACADDRESS].") ".$defgatway ; ?></td>
								<?php
									$defaultinterface = $row[DevicelistSettingsModel::$INTER_NAME];
									if($row[DevicelistSettingsModel::$I_TYPE] == 1) {
										$disabled = "";
									} else {
										$disabled = "disabled";
									}
								?>
								<td <?php echo $hidden; ?>>
									<input name="fw_enable" id="fw_enable" type="checkbox" <?php  print $enabled; ?> <?php print $disabled; ?> <?php if($enabled == "checked") { print "disabled"; } ?> onchange ="setEnableFirewall('<?php echo $defaultinterface ?>',this,'post')" />
								</td>
								<td align="left">
									<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $row[DevicelistSettingsModel::$ID]; ?>')" onfocus="if(this.blur)this.blur()" />
									<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[DevicelistSettingsModel::$ID]; ?>')" onfocus="if(this.blur)this.blur()" <?php echo $hidden; ?>/>
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
			<div class="page-btns" <?php print $hidden; ?> >
				<a class="btn-grey-wide-l" onclick="add_data()"><span>Add New</span></a>
				<a id="delete_selected" class="btn-grey-wide-l-disabled" <?php echo $hidden; ?>><span>Delete Selected</span></a>
			</div>
		</div>
		<form id="deleteform" method="post" action="">
			<input type="hidden" name="submit_action" value="0" />
			<input type="hidden" name="DEVICELIST_SETTINGS" id="DEVICELIST_SETTINGS" value="" />
			<input type="hidden" name="DELETE_RULE" id="DELETE_RULE" value="" />
			<input type="hidden" name="DEVICELIST_SETTINGS_LIST" id="DEVICELIST_SETTINGS_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</tbody>
</html>
