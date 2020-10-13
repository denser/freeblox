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
	include_once("Model/sys_calls.php");
	include_once("Control/date_config.php");

	$_SESSION['pageID'] = "#dyn_blacklist";
	$obj = DateTimeConfig::getConfig();
	$update_count = $obj->getActionListCount();
	$del = 0;
	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['submit_action'] == "3") {
			SysCalls::deleteDynBlRule($_POST["DYNAMIC_RULE"]);	// Delete a rule
			$del =1;
		} else if($_POST['submit_action'] == "4") {
			$del_arr = explode(",", $_POST["DYNAMIC_RULE_LIST"]);
			for($i = 0; $i < count($del_arr); $i++) {
				SysCalls::deleteDynBlRule($del_arr[$i]);
			}
			$del = 1;
		}
	} else {
		SysCalls::getDynBlRules();
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
				$('#dyn_blacklist').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "200px",
					"aaSorting": [[ 1, "asc" ]],
					"aoColumns":[
						{"bSortable": false},
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
					formObj.DYNAMIC_RULE.value = deletetoken;
					formObj.submit();
				}
			}

			function deleteAllAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "4";
					formObj.DYNAMIC_RULE_LIST.value = deletetoken;
					formObj.submit();
				}
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "dynamic_blacklist.php";
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()" onload="refresh_iframe('<?php print $del; ?>')" >
		<div class="content">
			<h1>Dynamic Blacklist IP Addresses<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="dynamic_list_body" style="overflow-y: hidden; width: 90%; overflow-x: hidden">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<table id="dyn_blacklist" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<td width="25px"><input type="checkbox" id="checkall" align="center" /></td>
								<th><a href="#">Address</a></th>
								<td width="60px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
								$source = '/tmp/dynbl.out';
								if( file_exists($source)) {
								$source_handle = fopen( $source, 'r' );
								while (!feof( $source_handle )) {
									$line = fgets( $source_handle, 1024) ;
									$expl_line = explode("\n",$line);
									if ( $expl_line[0] != "" ) {
										$temp = explode( " ",$expl_line[0] );
										if( $temp[3] != "") {
											$dynamicIPAddr = $temp[3];
							?>
								<tr>
									<td><input id="checkbox<?php echo $i ?>" name="chkbox" type='checkbox' align="center" value="<?php echo $temp[3]; ?>" onclick="uncheck()" /></td>
									<td><?php echo $dynamicIPAddr; ?></td>
									<td align="left">
										<input type="button" class="btn-delete" onclick="delete_data('<?php echo $temp[3]; ?>');" onfocus="if(this.blur)this.blur();" />
									</td>
								</tr>
							<?php
										}
									}
								}
								fclose( $source_handle );
								}
							?>
						</tbody>
						<script type="text/javascript">
							var count = '<?php echo $i; ?>';
							if(count > 10) {
								$("#dynamic_list_body").css("height", "335px");
								$("#dynamic_list_body").css("overflow-y", "scroll");
							}
						</script>
					</table>
				</table>
			</div>
			<div class="page-btns">
				<a id="delete_selected" class="btn-grey-wide-l-disabled"><span>Delete Selected</span></a>
			</div>
		</div>
		<form id="deleteform" method="post" action="">
			<input type="hidden" name="submit_action" value="0" />
			<input type="hidden" name="DYNAMIC_RULE" id="DYNAMIC_RULE" value="" />
			<input type="hidden" name="DYNAMIC_RULE_LIST" id="DYNAMIC_RULE_LIST" value="" />
		</form>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
	</script>
</html>
