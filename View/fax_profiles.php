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
	include_once("Model/fax_profiles_model.php");
	include_once("Control/date_config.php");
	include_once("Model/sys_calls.php");
	include_once("Model/media_settings_model.php");

	$_SESSION['pageID'] = "#t38_fax_settings";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$fax_profiles_config = FaxProfilesConfigModel::getInstance();
	$fax_profile_settings = $fax_profiles_config->getAllFaxProfileSettings();
	$fax_count = $fax_profiles_config->getT38ProfilesCount();
	$delFaxProfiles = $fax_profiles_config->getRoamTrunkFaxProfiles();
	$transcodingInterface = SysCalls::getTranscodingInterface();
	$media_config = MediaConfigModel::getInstance();
	$gettransmediaprofiles = $media_config->getTransMediaProfiles();
	$transCount = count($gettransmediaprofiles);

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['submit_action'] == "3") {
			if ( in_array($_POST['DELETE_RULE'],$delFaxProfiles,true)) {
				$fax_profiles_config->deleteFaxProfiles($_POST["DELETE_RULE"]);
				$fax_profiles_config->createActionList(DEPLOY_MODULES::FAX_PROFILE_CONFIG, "Fax profile deleted", DEPLOY_MODULES::FAX_PROFILE_CONFIG_STR, "");
				$delete = 1;
			} else {
				print "<Div class='deleteSipProfiles'>";
				print "Can't delete the Fax Profile as it is being used by Trunk/Roaming Profiles.";
				print "</Div>";
			}
		}

		if($_POST['submit_action'] == "4") {
			$del_arr = explode(",", $_POST["FAX_PROFILES_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				if ( in_array($del_arr[$i],$delFaxProfiles,true)) {
					$fax_profiles_config->deleteFaxProfiles($del_arr[$i]);
					$flag = TRUE;
				} else {
					$flag = FALSE;
				}
			}
			if ( $flag == TRUE ) {
				$fax_profiles_config->createActionList(DEPLOY_MODULES::FAX_PROFILE_CONFIG, " All Fax profiles deleted", DEPLOY_MODULES::FAX_PROFILE_CONFIG_STR, "");
				$delete = 1;
			}
			if ( $flag == FALSE ) {
				print "<Div class='deleteAllSipProfiles'>";
				print "Can't delete the T38 Fax Profiles as they are being used by Trunk/Roaming Profiles.";
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
				$('#faxprofiles').dataTable( {
					"sScrollX": "1100px",
					"sScrollY": "200px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
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
				parent.callEditPage(val, 'add_edit_fax_profiles.php', 360, 680);
			}

			function add_data() {
				var transCount = '<?php print $transCount;?>';
				var faxCount = '<?php print $fax_count; ?>';
				if(parseInt(transCount) < 1)
				{
					alert("Invalid! There is no media profile created with trasnscoding interface to be selected");
					return false;
				}
				if(parseInt(faxCount) >= 1)
				{
					alert("Invalid! Can't create more than one fax profile");
					return false;
				}
				else
				{
					parent.callAddPage('add_edit_fax_profiles.php', 360, 680);
				}
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
				$("input:checkbox[name='chkbox']:checked").each(function()
				{
					all_rules[i++] = $(this).val();
				});
				var rule_list = all_rules.toString();
				if( rule_list.length < 1 )
				{
					alert("There are no rules to be deleted!");
					return false;
				}
				else
				{
					parent.callConfirmAllBox(rule_list);
				}
			}

			function deleteAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "3";
					formObj.FAX_PROFILES.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.FAX_PROFILES_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "fax_profiles.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>T38 Fax Profiles<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="fax_profiles_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="faxprofiles" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">Fax Version</a></th>
								<th><a href="#">Max Bit Rate</a></th>
								<th><a href="#">Description</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							if ( $fax_profile_settings ) {
								while($row = $fax_profile_settings->fetchArray(SQLITE3_ASSOC)) {
								$i++;
							?>
								<tr>
									<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[FaxProfilesConfigModel::$NAME]; ?>" onclick="uncheck()" /></td>
									<td><?php echo $row[FaxProfilesConfigModel::$NAME]; ?></td>
									<td><?php echo $row[FaxProfilesConfigModel::$VERSION]; ?></td>
									<td><?php echo $row[FaxProfilesConfigModel::$MAX_BIT_RATE]; ?></td>
									<?php
										$comments = substr($row[FaxProfilesConfigModel::$DESCRIPTION],0,20);
									?>
									<td><a href=""title="<?php print $row[FaxProfilesConfigModel::$DESCRIPTION]; ?>"><?php echo $comments; ?></a></td>
									<?php
										$id = str_replace(' ', '@',$row[FaxProfilesConfigModel::$NAME]);
									?>
									<td align="left">
										<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $id; ?>')" onfocus="if(this.blur)this.blur()" />
										<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[FaxProfilesConfigModel::$NAME]; ?>')" onfocus="if(this.blur)this.blur()" />
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
								$("#fax_profiles_body").css("height", "335px");
								$("#fax_profiles_body").css("overflow-y", "scroll");
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
			<input type="hidden" name="FAX_PROFILES" id="FAX_PROFILES" value="" />
			<input type="hidden" name="DELETE_RULE" id="DELETE_RULE" value="" />
			<input type="hidden" name="FAX_PROFILES_LIST" id="FAX_PROFILES_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
