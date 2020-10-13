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
	include_once("Model/trunk_config_model.php");
	include_once("Control/date_config.php");
	include_once("Model/lcr_model.php");
	include_once("Model/sip_sec_settings_model.php");

	$_SESSION['pageID'] = "#trunk_config_settings";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$trunk_config =  TrunkConfigModel::getInstance();
	$trunk_configRules = $trunk_config->getTrunkConfigRules();
	$siptrunk_config = $trunk_config->getTrunksSipProfileNames();
	$lcr_config = LCRConfigModel::getInstance();
	$lcr_trunk_config = $lcr_config->getLCR_Trunks_Profiles();
	$i = 0;
	while ($row = $siptrunk_config->fetchArray()) {
		$siptrunk_rules[$i] = $row[0];
		$i++;
	}
	$j=0;
	while($row = $lcr_trunk_config->fetchArray()) {
		$lcr_rules[$j] = $row[0];
		$j++;
	}
	for($i=0; $i < count($lcr_rules); $i++) {
		$lcr = $lcr_rules[$i];
	}
	if (count($lcr) > 0) {
		$lcrTrunks = SipSecuritySettingsModel::unSerializeSIPMethods($lcr);
		$lcrCount = count($lcrTrunks);
		for ( $k =0 ; $k < $lcrCount; $k++ ) {
			list($trunks_arr[]) = explode("/", $lcrTrunks[$k]);
		}
	}
	$trunk_names_config = $trunk_config->getAllTrunkConfigNames();
	$i = 0;
	while($row = $trunk_names_config->fetchArray()) {
		$trunk_names[$i] = $row[0];
		$i++;
	}
	if (count($trunk_names) > 0 && count($trunks_arr) < 1 ) {
		$trunk_delete_arr = $trunk_names;
	} else if (count($trunk_names) > 0 && count($trunks_arr) > 0 ) {
		$trunk_delete_arr = array_diff($trunk_names,$trunks_arr);
	}

	if ($_POST['DELETE_RULE'] != NULL) {
		if (in_array($_POST['DELETE_RULE'],$trunk_delete_arr) ) {
			$trunk_config->deletetrunkConfigRule($_POST["DELETE_RULE"]);
			$trunk_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, "Trunk Config rule deleted", DEPLOY_MODULES::			SBC_CONFIG_STR, "");
			$delete = 1;
		} else {
			print "<Div class='deleteTrunks'>";
			print "Can't delete the rule as it is being used by LCR Profiles";
			print "</Div>";
		}
	}
	if ( count($trunk_delete_arr) > 0 ) {
		$delArray = array_values($trunk_delete_arr);
	}

	if ($_POST['TRUNK_CONFIG_LIST'] != NULL) {
		$del_arr = explode(",", $_POST["TRUNK_CONFIG_LIST"]);
		for($k=0; $k < count($del_arr); $k++ ) {
			if  (in_array($del_arr[$k],$trunk_delete_arr) ) {
				$trunk_config-> deletetrunkConfigRule($del_arr[$k]);
				$flag = TRUE;
			} else {
				$flag = FALSE;
			}
		}
		if ( $flag == 1) {
			$trunk_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, 
					"All Trunk Config rules deleted", 
					DEPLOY_MODULES::SBC_CONFIG_STR, "");
			$delete = 1;
		}
		if ( $flag == 0 ) {
			print "<Div class='deleteTrunks'>";
			print "Can't delete the rule as it is being used by LCR Profiles";
			print "</Div>";
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
				parent.callEditPage(val, 'add_edit_trunk_config.php', 360, 800);
			}

			function add_data() {
				parent.callAddPage('add_edit_trunk_config.php', 360, 800);
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
					var formObj = document.forms[0];
					formObj.submit_action.value = "4";
					formObj.TRUNK_CONFIG_LIST.value = rule_list;
					parent.callConfirmBox(rule_list);
				}
			}

			function deleteAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "3";
					formObj.TRUNK_CONFIG.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.TRUNK_CONFIG_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refreshIframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "trunk_config.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refreshIframe('<?php print $delete; ?>');">
		<div class="content">
			<h1>Trunk Configuration<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="trunk_config_body" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="trunkconfig" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Name</a></th>
								<th><a href="#">Server IP Port</a></th>
								<th><a href="#">Description</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
							$i = 0;
							if ( $trunk_configRules ) {
								while($row = $trunk_configRules->fetchArray(SQLITE3_ASSOC)) {
								$i++;
						?>
								<tr>
									<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row[TrunkConfigModel::$NAME]; ?>" onclick="uncheck()" /></td>
									<td><?php echo $row[TrunkConfigModel::$NAME]; ?></td>
									<td><?php echo $row[TrunkConfigModel::$DOMAIN_IP_URI];?></td>
						<?php
							$comments = substr($row[TrunkConfigModel::$DESCRIPTION],0,10); ?>
									<td><a href="" class="ttip" title="<?php print $row[TrunkConfigModel::$DESCRIPTION]; ?>"><?php echo $comments; ?></a></td>
						<?php
							$id = str_replace(' ', '@',$row[TrunkConfigModel::$NAME]);
						?>
									<td align="left">
										<input type="button" class="btn-edit edit-deviceaccesslist" onclick="edit_data('<?php echo $id; ?>')" onfocus="if(this.blur)this.blur()" />
										<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row[TrunkConfigModel::$NAME]; ?>')" onfocus="if(this.blur)this.blur()" />
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
								$("#trunk_config_body").css("height", "335px");
								$("#trunk_config_body").css("overflow-y", "scroll");
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
			<input type="hidden" name="TRUNK_CONFIG" id="TRUNK_CONFIG" value="" />
			<input type="hidden" name="DELETE_RULE" id="DELETE_RULE" value="" />
			<input type="hidden" name="TRUNK_CONFIG_LIST" id="TRUNK_CONFIG_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
