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
	include_once("Model/sipheader_model.php");
	include_once("Control/date_config.php");
	include_once("Model/lcr_model.php");
	include_once("Model/sip_sec_settings_model.php");

	$_SESSION['pageID'] = "#sip_headers_settings";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$obj1 = ManipulationTable::getConfig();
	$result = $obj1->getMTList();
	$rulecount = $obj1->getCOUNT();
	//$usedMT = $obj1 -> getUsedMT();
	$res = 0;

	if(isset($_POST) && isset($_POST['submit_action'])) {
		$dependancy = "0";
		$depname = "";
		if($_POST['submit_action'] == "3") { // Delete
			$dependancy = $obj1->gettrunkroamdepdency($_POST["TRUNK_CONFIG"]);
			if( $dependancy == 0) {
				$obj1 -> delMT($_POST["TRUNK_CONFIG"]);
				$obj1->createActionList(DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG, $_POST["TRUNK_CONFIG"]." Rule deleted", DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG_STR, "");
				$res =1;
			} else {
				$depname = $_POST["TRUNK_CONFIG"];
				print "<Div class='deleteAllSipProfiles'>";
				print "Can't delete the $depname Profile as they are being used by SIP Profile .";
				print "</Div>";
			}
		} else if($_POST['submit_action'] == "4") { // Delete Multiple
			$del_arr = explode(",", $_POST["TRUNK_CONFIG_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				$dependancy = $obj1->gettrunkroamdepdency($del_arr[$i]);
				if( $dependancy == 0) {
					$obj1 -> delMT($del_arr[$i]);
					$obj1->createActionList(
							DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG, 
							$del_arr[$i]." Rules deleted", 
							DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG_STR, 
							"");
				} else {
					if($depname == "") {
						$depname .= $del_arr[$i];
					} else {
						$depname .= ",".$del_arr[$i];
					}
				}
			}
			if( $depname != "") {
				print "<Div class='deleteAllSipProfiles'>";
				print "Can't delete the $depname Profiles as they are being used by SIP Profile .";
				print "</Div>";
			}
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ($_POST['submit_action'] == "2") { // Add
			$data_array = array(
				"NAME" => $_POST['NAME'],
				"DESCRIPTION" => $_POST['DESCRIPTION']);
			$obj1 -> addMT($data_array);
			if ($_POST['CON_1_LIST']!="") {
				$con_1_str = explode(",", $_POST['CON_1_LIST']);
				$con_11_hd_str = explode(",", $_POST['CON_11_HD_LIST']);
				$con_11_pattern_str = explode(",", $_POST['CON_11_PATTERN_LIST']);
				$con_11_params_str = explode(",", $_POST['CON_11_HD_PARAMS_LIST']);
				$con_11_hdname_str = explode(",", $_POST['CON_11_HD_NAME_LIST']);
				$con_11_hdindex_str = explode(",", $_POST['CON_11_HD_INDEX_LIST']);
				$con_12_other1_str = explode(",", $_POST['CON_12_OTHER1_LIST']);
				$action_str = explode(",", $_POST['ACTION_LIST']);
				$act_p1_str = explode(",", $_POST['ACT_P1_LIST']);
				$act_p2_str = explode(",", $_POST['ACT_P2_LIST']);
				for($i=0; $i<count($con_1_str); $i++) {
					if( $con_1_str[$i] != "eq" && $con_1_str[$i] != "ne" ) {
						$con_11_hd_str[$i] = "";
						$con_11_pattern_str[$i] = "";
						$con_11_params_str[$i] = "";
						$con_11_hdname_str[$i] = "";
						$con_11_hdindex_str[$i] = "";
					}
					if( ($con_1_str[$i] == "eq") || ($con_1_str[$i] == "ne") ) {
						if( ($con_11_hd_str[$i] != '$ct') && ($con_11_hd_str[$i] != '$hdr') ){
							$con_11_params_str[$i] = "";
							$con_11_hdname_str[$i] = "";
							$con_11_hdindex_str[$i] = "";
						}
						if($con_11_hd_str[$i] == '$ct') {
							$con_11_hdname_str[$i] = "";
							$con_11_hdindex_str[$i] = "";
						}
						if($con_11_hd_str[$i] == '$hdr') {
							$con_11_params_str[$i] = "";
						}
					}
					if($action_str[$i] == "append_urihf2") {
						$act_p2_str[$i] = $act_p2_str[$i] ;
					}
					if(($action_str[$i] == "insert_hf1or2") || 
						( $action_str[$i] == "append_hf1or2")) {
							$act_p1_str[$i] = $act_p1_str[$i] ;
					}
					$data_array = array(
							"C_ID"=> $i+1,
							"NAME" => $_POST['NAME'],
							"CON_1" => $con_1_str[$i],
							"CON_11_HD" => $con_11_hd_str[$i],
							"CON_11_PATTERN" => $con_11_pattern_str[$i],
							"CON_11_HD_PARAMS" => $con_11_params_str[$i],
							"CON_11_HD_NAME" => $con_11_hdname_str[$i],
							"CON_11_HD_INDEX" => $con_11_hdindex_str[$i],
							"CON_12_OTHER1" => $con_12_other1_str[$i],
							"ACTION" => $action_str[$i],
							"ACT_P1" => $act_p1_str[$i],
							"ACT_P2" => $act_p2_str[$i]
					);
					$obj1 -> addMM($data_array);
				}
			}
			$obj1->createActionList(DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG, 
				" ".$_POST['NAME']." Rule Added", 
				DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") { // Edit
			$data_array = array(
				"NAME" => $_POST['NAME'],
				"DESCRIPTION" => $_POST['DESCRIPTION']
			);
			$obj1 -> setMT($_POST['NAME'], $data_array);
			$obj1 -> deleteMM($data_array);
			if($_POST['CON_1_LIST']!=""){
				$con_1_str = explode(",", $_POST['CON_1_LIST']);
				$con_11_hd_str = explode(",", $_POST['CON_11_HD_LIST']);
				$con_11_pattern_str = explode(",", $_POST['CON_11_PATTERN_LIST']);
				$con_11_params_str = explode(",", $_POST['CON_11_HD_PARAMS_LIST']);
				$con_11_hdname_str = explode(",", $_POST['CON_11_HD_NAME_LIST']);
				$con_11_hdindex_str = explode(",", $_POST['CON_11_HD_INDEX_LIST']);
				$con_12_other1_str = explode(",", $_POST['CON_12_OTHER1_LIST']);
				$action_str = explode(",", $_POST['ACTION_LIST']);
				$act_p1_str = explode(",", $_POST['ACT_P1_LIST']);
				$act_p2_str = explode(",", $_POST['ACT_P2_LIST']);
				for($i=0; $i<count($con_1_str); $i++) {
					if( $con_1_str[$i] != "eq" && $con_1_str[$i] != "ne" ) {
						$con_11_hd_str[$i] = "";
						$con_11_pattern_str[$i] = "";
						$con_11_params_str[$i] = "";
						$con_11_hdname_str[$i] = "";
						$con_11_hdindex_str[$i] = "";
					}
					if( ($con_1_str[$i] == "eq") || ($con_1_str[$i] == "ne") ) {
						if( ($con_11_hd_str[$i] != '$ct') && ($con_11_hd_str[$i] != '$hdr') ){
							$con_11_params_str[$i] = "";
							$con_11_hdname_str[$i] = "";
							$con_11_hdindex_str[$i] = "";
						}
						if($con_11_hd_str[$i] == '$ct') {
							$con_11_hdname_str[$i] = "";
							$con_11_hdindex_str[$i] = "";
						}
						if($con_11_hd_str[$i] == '$hdr') {
							$con_11_params_str[$i] = "";
						}
					}
					if($action_str[$i] == "append_urihf2") {
						$act_p2_str[$i]	= $act_p2_str[$i];
					}
					if(($action_str[$i] == "insert_hf1or2") || ( $action_str[$i] == "append_hf1or2")) {
						$act_p1_str[$i] = $act_p1_str[$i];
					}
					$data_array = array(
						"C_ID"=> $i+1,
						"NAME" => $_POST['NAME'],
						"CON_1" => $con_1_str[$i],
						"CON_11_HD" => $con_11_hd_str[$i],
						"CON_11_PATTERN" => $con_11_pattern_str[$i],
						"CON_11_HD_PARAMS" => $con_11_params_str[$i],
						"CON_11_HD_NAME" => $con_11_hdname_str[$i],
						"CON_11_HD_INDEX" => $con_11_hdindex_str[$i],
						"CON_12_OTHER1" => $con_12_other1_str[$i],
						"ACTION" => $action_str[$i],
						"ACT_P1" => $act_p1_str[$i],
						"ACT_P2" => $act_p2_str[$i]
						);
						$obj1 -> addMM($data_array);
				}
			}
			$obj1->createActionList(DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG, 
				" ".$_POST['NAME']." Rule updated", 
				DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG_STR, "");
			$res =1;
		}
	}
	//$paging = $_SESSION['PAGING'];
	//$update_count = $obj1->getActionListCount();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/tooltip1.css" />
		<style type="text/css" title="currentStyle">
			@import "css/demo_page.css";
			@import "css/demo_table_jui.css";
			@import "css/jquery-ui-1.8.4.custom.css";
		</style>
		<script src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.dataTables.js"></script>
		<link rel="stylesheet" type="text/css" href="css/sort.css" media="screen" />
		<script type="text/javascript">
			var maxrule = 20;
			var rulecount = "<?php echo $rulecount; ?>";
			$(document).ready(function() {
				if(rulecount < maxrule) {
					$("#add_new").attr('class','btn-grey-wide-l');
				} else {
					$("#add_new").attr('class','btn-grey-wide-l-disabled');
					$('#add_new').attr('onclick','').unbind('click');
				}
				$('#manipulationtablerules').dataTable( {
					"sScrollX": "1200px",
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
				parent.callEditPage(val, 'add_edit_sipheader.php', 360, 600);
			}

			function add_data() {
				parent.callAddPage('add_edit_sipheader.php', 360, 600);
			}

			function delete_data(val) {
				var formObj = document.forms[0];
				formObj.submit_action.value = "3";
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
					parent.callConfirmAllBox(rule_list);
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
					parent.document.getElementById("container").src = "sipheader.php";
				}
			}

			function f1(val) {
				if(val == 1) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = "sipheader.php";
				}
			}
		</script>
		<style>
			#tooltip {
				max-width: 1400px;
			}
		</style>
		<script src="js/jquery-1.8.2.min.js"></script>
		<script src="js/jquery.tooltip.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script>
			$sub(document).ready(function(){
				$sub('a.ttip').tooltip({
					track : true,
					delay : 0,
					showURL : false,
					showBody : " - ",
					fade : 250
				});
			});
		</script>
	</head>
		<body onclick="javascript: parent.hideMenu();" onload="f1('<?php print $res; ?>');">
		<div class="content">
			<h1>SIP Header Conditions<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="manipulationtablerulesbody" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
 				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="manipulationtablerules" class="tablesorter" cellpadding="0" cellspacing="1px" style="width:100%">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Rule Name</a></th>
								<th><a href="#">Description</a></th>
								<th style="display: none"><a href="#">Rules</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
						<?php
							$i = 0;
							while($row = $result->fetchArray(SQLITE3_ASSOC)) {
								$i++;
								$rules_data = $obj1 -> getM2($row["NAME"]);
								$table = "<table class='tiptable' cellpadding='3' cellspacing='0' border='0'><tr><th style='text-decoration: underline'>Pattern</th><th>&nbsp;&nbsp;</th><th style='text-decoration: underline'>Left Strip</th><th>&nbsp;&nbsp;</th><th style='text-decoration: underline'>Right Strip</th><th>&nbsp;&nbsp;</th><th style='text-decoration: underline'>Prefix</th><th>&nbsp;&nbsp;</th><th style='text-decoration: underline'>Suffix</th><th>&nbsp;&nbsp;</th><th style='text-decoration: underline'>Reserve</th></tr>";
								while($rule = $rules_data->fetchArray(SQLITE3_ASSOC)) {
									$table .= "<tr><td>".$rule["PATTERN"]."</td><td>&nbsp;&nbsp;</td><td>".$rule["DIGIT_LS"]."</td><td>&nbsp;&nbsp;</td><td>".$rule["DIGIT_RS"]."</td><td>&nbsp;&nbsp;</td><td>".$rule["PREFIX"]."</td><td>&nbsp;&nbsp;</td><td>".$rule["SUFFIX"]."</td><td>&nbsp;&nbsp;</td><td>".$rule["DIGIT_RSV"]."</td></tr>";
								}
								$table .= "</table>";
							//if(in_array($row["NAME"], $usedMT) == FALSE) {
						?>
						<tr>
							<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $row['NAME'] ?>" onclick="uncheck()" /></td>
							<td><?php echo $row['NAME'] ?></td>
							<td><?php echo $row['DESCRIPTION'] ?></td>
							<td style="display: none"><a href="#" style="color: #222" class="ttip" title="<?php echo $table ?>" onfocus="if(this.blur)this.blur()" >Rules</a></td>
							<td>
								<input type="button" class="btn-edit edit-trunk" onclick="edit_data('<?php echo $row['NAME'] ?>')" onfocus="if(this.blur)this.blur()" />
								<input type="button" class="btn-delete" onclick="delete_data('<?php echo $row['NAME'] ?>')" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<?php
						/* }
							else {
								$str = $row["NAME"].' has dependency with <b>Routing Table</b>';
						?>
							<tr class="gradeA">
								<td></td>
								<td><?php echo $row['NAME'] ?></td>
								<td><?php echo $row['DESC'] ?></td>
								<td><a href="#" style="color: #222" class="ttip" title="<?php echo $table ?>" onfocus="if(this.blur)this.blur()" >Rules</a></td>
								<td>
									<input type="button" class="btn-edit edit-trunk" onclick="edit_data('<?php echo $row['NAME'] ?>')" onfocus="if(this.blur)this.blur()" />
									<input type="button" class="btn-delete" style="" onclick="parent.applyError('<?php echo $str ?>')" onfocus="if(this.blur)this.blur()"  />
								</td>
							</tr>
							<?php
						} */
							}
							?>
						</tbody>
					</table>
				</table>
			<!--<div style="height: 10px">&nbsp;</div> -->
			</div>
			<div class="page-btns">
				<a id="add_new" onclick="add_data()"><span>Add New</span></a>
				<a id="delete_selected" class="btn-grey-wide-l-disabled"><span>Delete Selected</span></a>
			</div>
		</div>
		<form id="deleteform" method="post" action="">
			<input type="hidden" name="submit_action" value="0" />
			<input type="hidden" name="TRUNK_CONFIG" id="TRUNK_CONFIG" value="" />
			<input type="hidden" name="TRUNK_CONFIG_LIST" id="TRUNK_CONFIG_LIST" value="" />
		</form>
	</body>
<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
