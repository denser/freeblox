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
	include_once("Model/extension_profiles_model.php");
	include_once("Control/date_config.php");

	$_SESSION['pageID'] = "#extension_prof";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$extension_profiles =  ExtensionProfilesConfigModel::getInstance();
	$extensionprofilesRules = $extension_profiles->getExtensionProfilesRules();
	$delete = 0;
	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['submit_action'] == "3") {
			$extension_profiles->deleteExtensionProfilesRule($_POST["EXTENSION_PROFILES"]);
			//$extension_profiles->createActionList(DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG, "Device Access rule deleted", DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG_STR, "");
			$delete = 1;
		} else if($_POST['submit_action'] == "4") { 	// Delete Multiple
			$del_arr = explode(",", $_POST["EXTENSION_PROFILES_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				$extension_profiles-> deleteextensionprofilesRule($del_arr[$i]);
				$delete = 1;
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
		<link rel="stylesheet" type="text/css" href="css/sort.css" media="screen" />
		<script type="text/javascript">
			$(document).ready(function() {
				$('#extensionprofiles').dataTable( {
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
				});
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
				parent.callEditPage(val, 'add_edit_extension_profiles.php', 360, 550);
			}

			function add_data() {
				parent.callAddPage('add_edit_extension_profiles.php', 360, 550);
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
					formObj.EXTENSION_PROFILES.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.EXTENSION_PROFILES_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refreshIframe(val)
			{
				if(val == 1) {
					parent.document.getElementById("container").src = "extension_profiles.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Call Access Control<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="device_access_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="extensionprofiles" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Extension</a></th>
								<th><a href="#">Proxy IP</a></th>
								<th><a href="#">Proxy Port</a></th>
								<th><a href="#">InBound Profile</a></th>
								<th><a href="#">OutBound Profile</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							if ( $extensionprofilesRules ) {
							while($row = $extensionprofilesRules->fetchArray(SQLITE3_ASSOC)) {
								$i++;
							?>
								<tr>
									<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[ExtensionProfilesConfigModel::$EXTENSION]; ?>" onclick="uncheck()" /></td>
									<td><?php echo $row[ExtensionProfilesConfigModel::$EXTENSION]; ?></td>
									<td><?php echo $row[ExtensionProfilesConfigModel::$REDIRECT_PROXY_IP]; ?></td>
									<td><?php echo $row[ExtensionProfilesConfigModel::$REDIRECT_PROXY_PORT]; ?></td>
									<td><?php echo $row[ExtensionProfilesConfigModel::$EXTENSION]; ?></td>
									<td><?php echo $row[ExtensionProfilesConfigModel::$EXTENSION]; ?></td>
									<?php
										$id = str_replace(' ', '_',$row[ExtensionProfilesConfigModel::$EXTENSION]);
									?>
									<td align="left">
										<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $id; ?>')" onfocus="if(this.blur)this.blur()" />
										<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[ExtensionProfilesConfigModel::$EXTENSION]; ?>')" onfocus="if(this.blur)this.blur()" />
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
			<input type="hidden" name="EXTENSION_PROFILES" id="EXTENSION_PROFILES" value="" />
			<input type="hidden" name="EXTENSION_PROFILES_LIST" id="EXTENSION_PROFILES_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
