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
	include_once("Model/media_settings_model.php");
	include_once("Control/date_config.php");
	include_once('Model/devicelist_settings_model.php');
	include_once("Model/sys_calls.php");

        $hidden = "";
        if(SysCalls::getSysArch() == "armv7l") {
                $hidden = 'style="display:none"';
        }
	$devlist = DevicelistSettingsModel::getInstance();
	$_SESSION['pageID'] = "#media_profile_settings";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$media_settings_config = MediaConfigModel::getInstance();
	$media_settings = $media_settings_config->getAllMediaSettings();
	$trunkMediaProfiles = $media_settings_config->getTrunkMediaProfiles();
	$roamingMediaProfiles = $media_settings_config->getRoamingMediaProfiles();
	$delMediaProfiles = $media_settings_config->delMediaProfiles();

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['submit_action'] == "3") {
			if ( in_array($_POST['DELETE_RULE'],$delMediaProfiles,true)) {
				$media_settings_config->deleteMediaSettings($_POST["DELETE_RULE"]);
				$media_settings_config->createActionList(DEPLOY_MODULES::MEDIA_PROFILE_CONFIG, "Media profile deleted", DEPLOY_MODULES::MEDIA_PROFILE_CONFIG_STR, "");
				$delete = 1;
			} else {
				print "<Div class='deleteSipProfiles'>";
				print "Can't delete the Media Profile as it is being used by T38/Trunk/Roaming Profiles.";
				print "</Div>";
			}
		}
		if($_POST['submit_action'] == "4") {
			$del_arr = explode(",", $_POST["MEDIA_SETTINGS_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				if ( in_array($del_arr[$i],$delMediaProfiles,true)) {
					$media_settings_config->deleteMediaSettings($del_arr[$i]);
					$flag = TRUE;
				} else {
					$flag = FALSE;
				}
			}
			if ( $flag == TRUE ) {
				$media_settings_config->createActionList(DEPLOY_MODULES::MEDIA_PROFILE_CONFIG, " All Media profiles deleted", DEPLOY_MODULES::MEDIA_PROFILE_CONFIG_STR, "");
				$delete = 1;
			}
			if ( $flag == FALSE ) {
				print "<Div class='deleteAllSipProfiles'>";
				print "Can't delete the Media Profiles as they are being used by T38/Trunk/Roaming Profiles.";
				print "</Div>";
			}
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
				$('#mediasettings').dataTable( {
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
				parent.callEditPage(val, 'add_edit_media_settings.php', 360, 600);
			}

			function add_data() {
				parent.callAddPage('add_edit_media_settings.php', 360, 600);
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
					formObj.MEDIA_SETTINGS.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.MEDIA_SETTINGS_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "media_settings.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Media Profile<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="roaming_config_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="mediasettings" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">External Interface</a></th>
								<th><a href="#">Internal Interface</a></th>
								<th <?php echo $hidden; ?>><a href="#">Media Interface</a></th>
								<th><a href="#">Description</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
						$i = 0;
						if ( $media_settings ) {
							while($row = $media_settings->fetchArray(SQLITE3_ASSOC)) {
							$i++;
						?>
							<tr>
								<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[MediaConfigModel::$NAME]; ?>" onclick="uncheck()" /></td>
								<td><?php echo $row[MediaConfigModel::$NAME]; ?></td>
								<td>
								<?php echo $row[MediaConfigModel::$EXTERNAL_INTERFACE]; ?> </td>
								<td>
								<?php echo $row[MediaConfigModel::$INTERNAL_INTERFACE]; ?>   </td>
								<td <?php echo $hidden; ?>>
								<?php echo $row[MediaConfigModel::$MEDIA_INTERFACE]; ?> </td>
								<?php
									$comments = substr($row[MediaConfigModel::$DESCRIPTION],0,20);
								?>
								<td><a href=""title="<?php print $row[MediaConfigModel::$DESCRIPTION]; ?>"><?php echo $comments; ?></a></td>
						<?php
							$id = str_replace(' ', '@',$row[MediaConfigModel::$NAME]);
						?>
								<td align="left">
									<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $id; ?>')" onfocus="if(this.blur)this.blur()" />
									<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[MediaConfigModel::$NAME]; ?>')" onfocus="if(this.blur)this.blur()" />
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
			<input type="hidden" name="MEDIA_SETTINGS" id="MEDIA_SETTINGS" value="" />
			<input type="hidden" name="DELETE_RULE" id="DELETE_RULE" value="" />
			<input type="hidden" name="MEDIA_SETTINGS_LIST" id="MEDIA_SETTINGS_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
