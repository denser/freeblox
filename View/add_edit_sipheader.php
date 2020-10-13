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

	$obj = ManipulationTable::getConfig();
	$flag = "add";
	$page_title = "Create SIP Header Conditions";
	$name_val = "";
	$desc_val = "";
	$sipheadernamelist = $obj -> getsipnamelist();
	$arr_con_1 = array();
	$arr_con_11_hd = array();
	$arr_con_11_pattern = array();
	$arr_con_11_hd_params = array();
	$arr_con_11_hd_name = array();
	$arr_con_11_hd_index = array();
	$arr_con_12_others1 = array();
	$arr_action = array();
	$arr_act_p1 = array();
	$arr_act_p2 = array();
	$createdDataNames = array();

	if(isset($_GET['data'])) {
		$trunk = $_GET['data'];
		$edit_data = $obj -> getMT($trunk);
		$flag = "edit";
		$page_title = "Edit SIP Header Conditions : ".$trunk;
		while($row = $edit_data->fetchArray(SQLITE3_ASSOC)) {
			$name_val = $row["NAME"];
			$desc_val = $row["DESCRIPTION"];
		}
		$edit_data1 = $obj -> getM2($trunk);
		while($row1 = $edit_data1 -> fetchArray(SQLITE3_ASSOC)) {
			array_push($arr_con_1, $row1["CON_1"]);
			array_push($arr_con_11_hd, $row1["CON_11_HD"]);
			array_push($arr_con_11_pattern, $row1["CON_11_PATTERN"]);
			array_push($arr_con_11_hd_params, $row1["CON_11_HD_PARAMS"]);
			array_push($arr_con_11_hd_name, $row1["CON_11_HD_NAME"]);
			array_push($arr_con_11_hd_index, $row1["CON_11_HD_INDEX"]);
			array_push($arr_con_12_others1, $row1["CON_12_OTHER1"]);
			array_push($arr_action, $row1["ACTION"]);
			array_push($arr_act_p1, $row1["ACT_P1"]);
			array_push($arr_act_p2, $row1["ACT_P2"]);
		}
	} else {
		//$createdDataNames = $obj -> getAllDataNames();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/validationEngine.jquery.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/tooltip.css" />
		<script src="js/jquery.validationEngine-en.js"></script>
		<script src="js/jquery.validationEngine.js"></script>
		<script src="js/jquery.tooltip.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script type="text/javascript">
			var flag = "<?php echo $flag ?>";
			var count = 0;
			var edit_param = false;
			var setSave = 0;
			var setcondition = 0;
			var con1_rule_edit;
			var conhd_rule_edit;
			var conpattern_rule_edit;
			var hdparam_rule_edit;
			var hdname_rule_edit;
			var hdindex_rule_edit;
			var other1_rule_edit;
			var action_rule_edit;
			var actp1_rule_edit;
			var actp2_rule_edit;
			var elmtid;
			var entityMap = {
			  '&': '&amp;',
			  '<': '&lt;',
			  '>': '&gt;',
			  '"': '&quot;',
			  "'": '&#39;',
			  '/': '&#x2F;',
			  '`': '&#x60;',
			  '=': '&#x3D;'
			}; 
			var arr_list = new Array();

			var arr_con1 = new Array();
			<?php foreach($arr_con_1 as $key => $val){ ?>
				arr_con1.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_conhd = new Array();
			<?php foreach($arr_con_11_hd as $key => $val){ ?>
				arr_conhd.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_conpattern = new Array();
			<?php foreach($arr_con_11_pattern as $key => $val){ ?>
				var tmp = '<?php echo urlencode($val); ?>';
				var decode = decodeURIComponent(tmp.replace(/\+/g, " "));
				arr_conpattern.push(decode);
				//arr_conpattern.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_hdparam = new Array();
			<?php foreach($arr_con_11_hd_params as $key => $val){ ?>
				arr_hdparam.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_hdname = new Array();
			<?php foreach($arr_con_11_hd_name as $key => $val){ ?>
				var tmp = '<?php echo urlencode($val); ?>';
				var decode = decodeURIComponent(tmp.replace(/\+/g, " "));
				arr_hdname.push(decode);
				//arr_hdname.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_hdindex = new Array();
			<?php foreach($arr_con_11_hd_index as $key => $val){ ?>
				arr_hdindex.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_other1 = new Array();
			<?php foreach($arr_con_12_others1 as $key => $val){ ?>
				var tmp = '<?php echo urlencode($val); ?>';
				var decode = decodeURIComponent(tmp.replace(/\+/g, " "));
				arr_other1.push(decode);
				//arr_other1.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_action = new Array();
			<?php foreach($arr_action as $key => $val){ ?>
				arr_action.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_actp1 = new Array();
			<?php foreach($arr_act_p1 as $key => $val){ ?>
				var tmp = '<?php echo urlencode($val); ?>';
				var decode = decodeURIComponent(tmp.replace(/\+/g, " "));
				arr_actp1.push(decode);
			<?php } ?>

			var arr_actp2 = new Array();
			<?php foreach($arr_act_p2 as $key => $val){ ?>
				var tmp = '<?php echo urlencode($val); ?>';
				var decode = decodeURIComponent(tmp.replace(/\+/g, " "));
				arr_actp2.push(decode);
			<?php } ?>

			function escapeHtml (string) {
			  return String(string).replace(/[&<>"'`=\/]/g, function (s) {
			    return entityMap[s];
			  });
			}
			$sub(document).ready(function() {
				initValidation();
				getPageTips("sipheaders_ttip");
				setTooltip();
				$sub("#btn-add").click(function() {
					addTODRRULES();
				});
				if(flag == "edit") {
					for(var i = 0; i < arr_con1.length; i++) {
						var tmp = arr_action[i];
						var tmp1 = "";
						if( tmp == "append_time0" ) {tmp1 = "append_time"; }
						if( tmp == "remove_hf1" ) {tmp1 = "remove_hf"; }
						if( tmp == "append_hf1or2" ) {tmp1 = "append_hf"; }
						if( tmp == "insert_hf1or2" ) {tmp1 = "insert_hf"; }
						if( tmp == "append_urihf2" ) {tmp1 = "append_urihf"; }
						if( tmp == "append_cturi1" ) {tmp1 = "append_cturi"; }
						if( tmp == "append_ctparam1" ) {tmp1 = "append_ctparam";}
						var str = '<tr id="count'+i+'">';
						str += '<td class="todr-rules-con1">'+arr_con1[i]+'</td>';
						str += '<td class="todr-rules-conhd" style="display: none">'+arr_conhd[i] +'</td>';
						str += '<td class="todr-rules-conpattern" style="display: none">'+escapeHtml(arr_conpattern[i])+'</td>';
						str += '<td class="todr-rules-hdparam" style="display: none">'+arr_hdparam[i]+'</td>';
						str += '<td class="todr-rules-hdname" style="display: none">'+escapeHtml(arr_hdname[i])+'</td>';
						str += '<td class="todr-rules-hdindex" style="display: none">'+arr_hdindex[i]+'</td>';
						str += '<td class="todr-rules-other1" style="display: none">'+escapeHtml(arr_other1[i])+'</td>';
						str += '<td class="todr-rules-action" style="display: none">'+arr_action[i]+'</td>';
						str += '<td class="todr-rules-action1">'+tmp1+'</td>';
						str += '<td class="todr-rules-actp1" ><div style="width:50px; height:14px; overflow:hidden;">'+escapeHtml(arr_actp1[i])+'</div></td>';
						str += '<td class="todr-rules-actp2" ><div style="width:50px; height:14px; overflow:hidden;">'+escapeHtml(arr_actp2[i])+'</div></td>';
						str += '<td><input type="button" class="btn-edit" ';
						str += ' id="edit'+i+'" onclick="editRules(this)" ';
						str += ' onfocus="if(this.blur)this.blur()" >';
						str += ' <input type="button" class="btn-delete" ';
						str += ' onclick="removeRules(this)" id="delete'+i+'" ';
						str += ' onfocus="if(this.blur)this.blur()" ></td></tr>';
						$sub(".todr-rules").append(str);
						count++;
						$sub("#newrule").hide();
						var trlen = $sub(".todr-rules tr").length;
						if(trlen=="10"){
							disableAdd();
						}
					}
					setToolTip();
					setField("disabled");
				}
				if(flag == "add") {
					$sub("#btn-add").click(function() {
						addTODRRULES();
					});
				}
			});

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[onlyLetterNumber], minSize[3]]");
				$sub("#addmanipulationtable").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function condition1() {
				var con1 =$sub("#CON_1").val();
				if( con1 == "none" ) {
					$sub("#othersrulestab").hide(500);
					$sub("#headerrulestab").hide(500);
					clear_none_value();
				} if((con1 == "eq") || (con1 == "ne")) {
					$sub("#headerrulestab").show(500);
					$sub("#othersrulestab").hide(500);
					cleareqvalue();	
				} else if( (con1 == "has_no_body") || (con1 == "has_body") || 
						(con1 == "is_not_method") || (con1 == "is_method") || 
						(con1 == "is_present_hf") || 
						(con1 == "is_not_present_hf")) { 
					$sub("#headerrulestab").hide(500);
					$sub("#othersrulestab").show(500);
					if(setcondition == 0) {
						$sub("#CON_12_OTHER1").val("");
					} else {
						setcondition = 0;
					}
					clearothervalue();
					if((con1 == "is_not_present_hf") || (con1 == "is_present_hf")) {
						$sub("#otherrow1").show(500);
						$sub("#other1").html("Header");
					} else if((con1 == "is_method") || (con1 == "is_not_method")) {
						$sub("#otherrow1").show(500);
						$sub("#other1").html("SIP Method");
					} else if((con1 == "has_body") || (con1 == "has_no_body")) {
							$sub("#otherrow1").show(500);
							$sub("#other1").html("Mime");
					}
				}
				condition2();
			}

			function condition2() {
				var con2 =$sub("#CON_11_HD").val();
				if( con2 == "$ct") {
					$sub("#headerrulestab1").show(500);
					$sub("#hdrrulestab").hide(500);
					clear_headerct_values();
				} else if(con2 == "$hdr") {
					clear_headerhdr_values();
					$sub("#hdrrulestab").show(500);
					$sub("#headerrulestab1").hide(500);
				} else {
					clear_header_values();
					$sub("#headerrulestab1").hide(500);
					$sub("#hdrrulestab").hide(500);
				}
			}

			function condition3() {
				var con4 =$sub("#ACTION").val(); 
				if( con4 == "append_time0") {
					$sub("#actionrulestab").hide(500);
					clearactionvalues();
				} else {
					$sub("#actionrulestab").show(500);
					if(setcondition == 0) {
						$sub("#ACT_P1").val("");
						$sub("#ACT_P2").val("");
					} else {
						setcondition = 0;
					}
					if(( con4 == "append_cturi1") || ( con4 == "append_ctparam1") ||
						 (con4 == "remove_hf1")) {
							$sub("#actionrule1").show(500);
							$sub("#actionrule2").hide(500);
							$sub("#ACT_P2").val("");
						} else {
							$sub("#actionrule1").show(500);
							$sub("#actionrule2").show(500);
						}
				}
			}

			function clear_none_value() {
				$sub("#CON_11_HD").val("");
				$sub("#CON_11_PATTERN").val("");
				$sub("#CON_11_HD_PARAMS").val("");
				$sub("#CON_11_HD_NAME").val("");
				$sub("#CON_11_HD_INDEX").val("");
				$sub("#CON_12_OTHER1").val("");
			}

			function cleareqvalue() {
				$sub("#CON_12_OTHER1").val("");
			}

			function clearothervalue() {
				$sub("#CON_11_HD").val("");
				$sub("#CON_11_PATTERN").val("");
				$sub("#CON_11_HD_PARAMS").val("");
				$sub("#CON_11_HD_NAME").val("");
				$sub("#CON_11_HD_INDEX").val("");
			}

			function clear_header_values() {
				$sub("#CON_11_HD_PARAMS").val("");
				$sub("#CON_11_HD_NAME").val("");
				$sub("#CON_11_HD_INDEX").val("");
			}

			function clear_headerct_values() {
				$sub("#CON_11_HD_NAME").val("");
				$sub("#CON_11_HD_INDEX").val("");
			} 

			function clear_headerhdr_values() {
				$sub("#CON_11_HD_PARAMS").val("");
			} 

			function clearactionvalues() {
				$sub("#ACT_P1").val("");
				$sub("#ACT_P2").val("");
			}

			function clearRulesData() {
				$sub('#todr_rules').validationEngine('hideAll');
				$sub("#CON_1").val("none");
				$sub("#CON_11_HD").val("$ai");
				$sub("#CON_11_PATTERN").val("");
				$sub("#CON_11_HD_PARAMS").val("none");
				$sub("#CON_11_HD_NAME").val("");
				$sub("#CON_11_HD_INDEX").val("0");
				$sub("#CON_12_OTHER1").val("");
				$sub("#ACTION").val("append_time0");
				$sub("#ACT_P1").val("");
				$sub("#ACT_P1").val(""); 
				condition1();
				condition2();
				condition3();
			}

			function removeRules(elem) {
				var elem_id = elem.id;
				elem_id = elem_id.substr(6,1);
				count--;
				$sub(".btn-delete").live('click', function(event) {
					$sub(this).parent().parent().remove();
				});
				var index = arr_con1.indexOf($sub("#count"+elem_id+" td.todr-rules-con1").text());
				if (index >= 0) {
					arr_con1[index] = arr_con1[0];
					arr_con1.shift();
					arr_conhd[index] = arr_conhd[0];
					arr_conhd.shift();
					arr_conpattern[index] = arr_conpattern[0];
					arr_conpattern.shift();
					arr_hdparam[index] = arr_hdparam[0];
					arr_hdparam.shift();
					arr_hdname[index] = arr_hdname[0];
					arr_hdname.shift();
					arr_hdindex[index] = arr_hdindex[0];
					arr_hdindex.shift();
					arr_other1[index] = arr_other1[0];
					arr_other1.shift();
					arr_action[index] = arr_action[0];
					arr_action.shift();
					arr_actp1[index] = arr_actp1[0];
					arr_actp1.shift();
					arr_actp2[index] = arr_actp2[0];
					arr_actp2.shift();
				}
				enableAdd();
			}

			function enableAdd() {
				$sub("#btn-add").attr("class", "btn-add1");
				$sub("#btn-add").click(function() {
					addTODRRULES();
				});
			}

			function editRules(elem) {
				var elem_id = elem.id;
				elem_id = elem_id.substr(4,1);
				var edit_rule = new Array();
				$sub("#count"+elem_id+" td").each(function() {
					edit_rule.push($sub(this).text());
				});
				$sub("#newrule").show(500, function() {
					$sub("#CON_1").val(edit_rule[0]);
					con1_rule_edit=edit_rule[0];
					$sub("#CON_11_HD").val(edit_rule[1]);
					conhd_rule_edit=edit_rule[1];
					$sub("#CON_11_PATTERN").val(edit_rule[2]);
					conpattern_rule_edit=edit_rule[2];
					$sub("#CON_11_HD_PARAMS").val(edit_rule[3]);
					hdparam_rule_edit=edit_rule[3];
					$sub("#CON_11_HD_NAME").val(edit_rule[4]);
					hdname_rule_edit=edit_rule[4];
					$sub("#CON_11_HD_INDEX").val(edit_rule[5]);
					hdindex_rule_edit=edit_rule[5];
					$sub("#CON_12_OTHER1").val(edit_rule[6]);
					other1_rule_edit=edit_rule[6];
					$sub("#ACTION").val(edit_rule[7]);
					action_rule_edit=edit_rule[7];
					$sub("#ACT_P1").val(edit_rule[9]);
					actp1_rule_edit=edit_rule[9];
					$sub("#ACT_P2").val(edit_rule[10]);
					actp2_rule_edit=edit_rule[10];
					edit_param = true;
					setSave = 1;
					setcondition = 1;
					condition1();
					condition2();
					setcondition = 1;
					condition3();
				});
				disableAdd();
				elmtid=elem_id;
				for(var i=0 ;i<10 ;i++){
					$sub("#edit"+i).css('visibility', 'hidden');
					$sub("#delete"+i).css('visibility', 'hidden');
				}
			}

			function disableAdd() {
				$sub("#btn-add").attr("class", "btn-add1-disable");
				$sub('#btn-add').attr('onclick','').unbind('click');
			}

			function addTODRRULES() {
				edit_param = false;
				$sub("#btn-add").attr("class", "btn-add1-disable");
				$sub('#btn-add').attr('onclick','').unbind('click');
				clearRulesData();
				$sub("#newrule").show(500);
				setSave = 1;
			}

			function saveTODRRULES() {
				var flag_data = 0;
				var con_1 = $sub("#CON_1").val();
				var con_11_hd = $sub("#CON_11_HD").val();
				var con_11_pattern = $sub("#CON_11_PATTERN").val();
				var con_11_hd_params = $sub("#CON_11_HD_PARAMS").val();
				var con_11_hd_name = $sub("#CON_11_HD_NAME").val();
				var con_11_hd_index = $sub("#CON_11_HD_INDEX").val();
				var con_12_other1 = $sub("#CON_12_OTHER1").val();
				var action = $sub("#ACTION").val();
				var act_p1 = $sub("#ACT_P1").val();
				var act_p2 = $sub("#ACT_P2").val();
				$sub('#todr_rules').validationEngine('hideAll');
				if((con_1 == "is_present_hf") || (con_1 == "is_method") || 
								(con_1 == "is_not_method") || 
								(con_1 == "is_not_present_hf") ) {
					if($sub("#CON_12_OTHER1").val()==""){
						$sub('#CON_12_OTHER1').validationEngine('showPrompt', 
							'* This field is required', 'error', 
							'bottomRight', true);
						flag_data = 1;
						return;
					}
				}
				if((con_1 == "eq") || (con_1 == "ne")  ) {
					if($sub("#CON_11_PATTERN").val()==""){
						$sub('#CON_11_PATTERN').validationEngine('showPrompt', '* This field is required', 'error', 'bottomRight', true);
						flag_data = 1;
						return;
					}else {
						var patternRegEx = new RegExp(/^[a-zA-Z0-9`~!#\$%^&*\(\)\-_\+=\{\}\[\]|?/</>\.:;@]+$/);
						if(!(patternRegEx.test($sub("#CON_11_PATTERN").val()))) {
							$sub('#CON_11_PATTERN').validationEngine('showPrompt', '* Special characters <b>, \" \' \\</b> are  not allowed', 'error', 'bottomRight', true);
							flag_data = 1;
							return;
						}
					}
					if(con_11_hd == "$hdr") {
						if($sub("#CON_11_HD_NAME").val()=="") {
							$sub('#CON_11_HD_NAME').validationEngine('showPrompt', '* This field is required', 'error', 'bottomRight', true);
							flag_data = 1;
							return;
						}else{
							var patternRegEx = new RegExp(/^[a-zA-Z0-9`~!#\$%^&*\(\)\-_\+=\{\}\[\]|?/</>\.:;@]+$/);
							if(!(patternRegEx.test($sub("#CON_11_HD_NAME").val()))) {
								$sub('#CON_11_HD_NAME').validationEngine('showPrompt', '* Special characters <b>, \" \' \\</b> are  not allowed', 'error', 'bottomRight', true);
								flag_data = 1;
								return;
							}
						}
					}
				}
				if(( action == "append_cturi1") || ( action == "append_ctparam1") || (action == "remove_hf1")) {
					if($sub("#ACT_P1").val()==""){
						$sub('#ACT_P1').validationEngine('showPrompt', '* This field is required', 'error', 'bottomRight', true);
						flag_data = 1;
						return;
					}else{
						var patternRegEx = new RegExp(/^[a-zA-Z0-9`~!#\$%^&*\(\)\-_\+=\{\}\[\]|?\<\>\\\.:;@]+$/);
						if(!(patternRegEx.test($sub("#ACT_P1").val()))){
							$sub('#ACT_P1').validationEngine('showPrompt', '* Special characters <b>, \" \' </b> are  not allowed', 'error', 'bottomRight', true);
							flag_data = 1;
							return;
						}
					}
				}
				if(action == "append_urihf2") {
					if($sub("#ACT_P1").val()==""){
						$sub('#ACT_P1').validationEngine('showPrompt', '* This field is required', 'error', 'bottomRight', true);
						flag_data = 1;
						return;
					}else{
						var patternRegEx = new RegExp(/^[a-zA-Z0-9`~!#\$%^&*\(\)\-_\+=\{\}\[\]\s\\|?\<\>\\\.:;@]+$/);
						if(!(patternRegEx.test($sub("#ACT_P1").val()))){
							$sub('#ACT_P1').validationEngine('showPrompt', '* Special characters <b>, \" \' </b> are not allowed', 'error', 'bottomRight', true);
							flag_data = 1;
							return;
						}
					}
					if($sub("#ACT_P2").val()==""){
						$sub('#ACT_P2').validationEngine('showPrompt', '* This field is required', 'error', 'bottomRight', true);
						flag_data = 1;
						return;
					}else{
						var patternRegEx = new RegExp(/^[a-zA-Z0-9`~!#\$%^&*\(\)\-_\+=\{\}\[\]\s\\|?\<\>\\\.:;@]+$/);
						if(!(patternRegEx.test($sub("#ACT_P2").val()))){
							$sub('#ACT_P2').validationEngine('showPrompt', '* Special characters <b>, \" \' </b> are  not allowed', 'error', 'bottomRight', true);
							flag_data = 1;
							return;
						}
					}
				}
				if( ( action == "insert_hf1or2") || ( action == "append_hf1or2") ) {
					if(($sub("#ACT_P1").val()=="") && ($sub("#ACT_P2").val()=="") ) {
						$sub('#ACT_P2').validationEngine('showPrompt', '* Either one field should be entered', 'error', 'bottomRight', true);
						flag_data = 1;
						return;
					} else {
						var patternRegEx = new RegExp(/^[a-zA-Z0-9`~!#\$%^&*\(\)\-_\+=\{\}\[\]\s\\|?\<\>\\\.:;@]+$/);
						if(!(patternRegEx.test($sub("#ACT_P1").val()))){
							$sub('#ACT_P1').validationEngine('showPrompt', '* Special characters <b>, \" \' </b> are  not allowed', 'error', 'bottomRight', true);
							flag_data = 1;
							return;
						}
					}
				}
				var arr_index = 0;
				var flag=false;
				var hideflag=false;
				for(var i = 0; i < arr_con1.length; i++) {
					if(edit_param == true) {
						if((arr_con1[i] == con1_rule_edit) && 
							(arr_conhd[i] == conhd_rule_edit) &&
							(arr_conpattern[i] == conpattern_rule_edit) && 
							(arr_hdparam[i] == hdparam_rule_edit) && 
							(arr_hdname[i] == hdname_rule_edit) &&
							(arr_hdindex[i] == hdindex_rule_edit) &&
							(arr_other1[i] == other1_rule_edit) &&
							(arr_action[i] == action_rule_edit) &&
							(arr_actp1[i] == actp1_rule_edit) &&
							(arr_actp2[i] == actp2_rule_edit)){
								flag_edit = 1;
								flag_data=0;
								arr_index = i;
						}
					} else {
						
						arr_index++;
					}
				}

				if(flag_data == 0) {
					$sub('#todr_rules').validationEngine('hideAll');
					$sub("#btn-add").attr("class", "btn-add1");
					$sub("#btn-add").click(function() {
						addTODRRULES();
					});
					if(edit_param == true) {
						edit_param = false;
						arr_con1[arr_index] = con_1;
						arr_conhd[arr_index] = con_11_hd;
						arr_conpattern[arr_index] = con_11_pattern;
						arr_hdparam[arr_index] = con_11_hd_params;
						arr_hdname[arr_index] = con_11_hd_name;
						arr_hdindex[arr_index] = con_11_hd_index;
						arr_other1[arr_index] = con_12_other1;
						arr_action[arr_index] = action;
						arr_actp1[arr_index] = act_p1;
						arr_actp2[arr_index] = act_p2;
						$sub("#count"+arr_index).remove();
					} else {
						arr_con1.push(con_1);
						arr_conhd.push(con_11_hd);
						arr_conpattern.push(con_11_pattern);
						arr_hdparam.push(con_11_hd_params);
						arr_hdname.push( con_11_hd_name);
						arr_hdindex.push(con_11_hd_index);
						arr_other1.push(con_12_other1);
						arr_action.push(action);
						arr_actp1.push(act_p1);
						arr_actp2.push(act_p2);
					}
					var str2 = '<tr><td class="todr-rules-td">Condition1</td>';
					//str2 += '<td class="todr-rules-td">header</td>';
					//str2 += '<td class="todr-rules-td">Pattern</td>';
					//str2 += '<td class="todr-rules-td">hdparams</td>';
					//str2 += '<td class="todr-rules-td">hdname</td>';
					//str2 += '<td class="todr-rules-td">hdparams</td>';
					//str2 += '<td class="todr-rules-td">hdindex</td>';
					//str2 += '<td class="todr-rules-td">other1</td>';
					//str2 += '<td class="todr-rules-td" >Action</td>';
					str2 += '<td class="todr-rules-td" >Action</td>';
					str2 += '<td class="todr-rules-td">Param1</td>';
					str2 += '<td class="todr-rules-td">Param2</td>';
					str2 += '<td class="todr-rules-td">option</td></tr>';
					var appendVal= str2;
					$sub("#newrule").hide(500, function() {
						for(var i = 0; i < arr_con1.length; i++) {
							if((arr_con1[i]!=null)&&(arr_conhd[i]!=null)&&
									(arr_conpattern[i]!=null)&&
									(arr_hdparam[i]!=null)&&
									(arr_hdname[i]!=null)&&
									(arr_hdindex[i]!=null)&&
									(arr_other1[i]!=null)&&
									(arr_action[i]!=null)&&
									(arr_actp1[i]!=null)&&
									(arr_actp2[i]!=null)){
								var tmp = arr_action[i];
								var tmp1 = "";
								if( tmp == "append_time0" ) {tmp1 = "append_time"; }
								if( tmp == "remove_hf1" ) {tmp1 = "remove_hf"; }
								if( tmp == "append_hf1or2" ) {tmp1 = "append_hf"; }
								if( tmp == "insert_hf1or2" ) {tmp1 = "insert_hf"; }
								if( tmp == "append_urihf2" ) {tmp1 = "append_urihf"; }
								if( tmp == "append_cturi1" ) {tmp1 = "append_cturi"; }
								if( tmp == "append_ctparam1" ) {tmp1 = "append_ctparam"; }
								var str = '<tr id="count'+i+'">';
								str += '<td class="todr-rules-con1">'+arr_con1[i]+'</td>';
								str += '<td class="todr-rules-conhd" style="display: none">'+arr_conhd[i] +'</td>';
								str += '<td class="todr-rules-conpattern" style="display: none">'+escapeHtml(arr_conpattern[i])+'</td>';
								str += '<td class="todr-rules-hdparam" style="display: none">'+arr_hdparam[i]+'</td>';
								str += '<td class="todr-rules-hdname" style="display: none">'+escapeHtml(arr_hdname[i])+'</td>';
								str += '<td class="todr-rules-hdindex" style="display: none">'+arr_hdindex[i]+'</td>';
								str += '<td class="todr-rules-other1" style="display: none">'+escapeHtml(arr_other1[i])+'</td>';
								str += '<td class="todr-rules-action" style="display: none">'+arr_action[i]+'</td>';
								str += '<td class="todr-rules-action1">'+tmp1+'</td>';
								str += '<td class="todr-rules-actp1"><div style="width:50px; height:14px; overflow:hidden;">'+escapeHtml(arr_actp1[i])+'</div></td>';
								str += '<td class="todr-rules-actp2"><div style="width:50px; height:14px; overflow:hidden;">'+escapeHtml(arr_actp2[i])+'</div></td>';
								str += '<td><input type="button" class="btn-edit" ';
								str += ' id="edit'+i+'" onclick="editRules(this)" ';
								str += ' onfocus="if(this.blur)this.blur()" >';
								str += ' <input type="button" class="btn-delete" ';
								str += ' onclick="removeRules(this)" id="delete'+i+'" ';
								str += ' onfocus="if(this.blur)this.blur()" ></td></tr>';
								appendVal=appendVal + str;
							}
						}
						$sub(".todr-rules").html(appendVal);
						setToolTip();
						count++;
						clearRulesData();
						setSave = 0;
					});
					var trlen = $sub(".todr-rules tr").length;
					if(trlen=="10"){
						disableAdd();
					}
				}
			}

			function setField(param) {
				if(param == "disabled") {
					$sub("#NAME").attr("disabled", param);
				} else {
					$sub("#NAME").removeAttr("disabled");
				}
			}

			function submitData() {
				if ( flag == "add") {
					var mediaArr = new Array();
					var get_sipname_val = document.getElementById("NAME").value;
					var post_sipname_val = get_sipname_val.trim();
					<?php
						foreach($sipheadernamelist as $key=>$val) {
					?>
							mediaArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var mediaCount = mediaArr.length;
					for ( var i=0; i < mediaCount; i++) {
						if (post_sipname_val == mediaArr[i]) {
							$sub("#errow1").show();
							$sub("#errow2").show();
							$sub("#errow3").show();
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg"
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
							
						}
						$sub("#errow1").hide();
						$sub("#errow2").hide();
						$sub("#errow3").hide();
					}
				}
				if(setSave == 0) {
					var count = $sub(".todr-rules tr").length;
					if(count > 1) {
						if($sub("#addmanipulationtable").validationEngine('validate')) {
							$sub("#btn-submit").hide();
							$sub("#btn-update").show();
							var formObj = document.forms[0];
							if(flag == "add"){
								formObj.submit_action.value = "2";
							} else {
								setField("enabled");
								formObj.submit_action.value = "1";
							}
							setFormFields();
							formObj.submit();
							return true;
						}
					} else {
						$sub('#allrules_list').validationEngine('showPrompt', 'Create atleast one rule', 'error', true);
					}
				} else {
					$sub('#todr_rules').validationEngine('showPrompt', 'Please save or close this rule for saving the Routing table', 'error', true);
				}
			}

			function setFormFields() {
				var con1_array = new Array();
				$sub(".todr-rules-con1").each(function() {
					con1_array.push($sub(this).text());
				});

				var conhd_array = new Array();
				$sub(".todr-rules-conhd").each(function() {
					conhd_array.push($sub(this).text());
				});

				var conpattern_array = new Array();
				$sub(".todr-rules-conpattern").each(function() {
					conpattern_array.push($sub(this).text());
				});

				var hdparam_array = new Array();
				$sub(".todr-rules-hdparam").each(function() {
					hdparam_array.push($sub(this).text());
				});

				var hdname_array = new Array();
				$sub(".todr-rules-hdname").each(function() {
					hdname_array.push($sub(this).text());
				});

				var hdindex_array = new Array();
				$sub(".todr-rules-hdindex").each(function() {
					hdindex_array.push($sub(this).text());
				});

				var other1_array = new Array();
				$sub(".todr-rules-other1").each(function() {
					other1_array.push($sub(this).text());
				});

				var action_array = new Array();
				$sub(".todr-rules-action").each(function() {
					action_array.push($sub(this).text());
				});

				var actp1_array = new Array();
				$sub(".todr-rules-actp1").each(function() {
					actp1_array.push($sub(this).text());
				});

				var actp2_array = new Array();
				$sub(".todr-rules-actp2").each(function() {
					actp2_array.push($sub(this).text());
				});

				$sub("#CON_1_LIST").val(con1_array);
				$sub("#CON_11_HD_LIST").val(conhd_array);
				$sub("#CON_11_PATTERN_LIST").val(conpattern_array);
				$sub("#CON_11_HD_PARAMS_LIST").val(hdparam_array);
				$sub("#CON_11_HD_NAME_LIST").val(hdname_array);
				$sub("#CON_11_HD_INDEX_LIST").val(hdindex_array);
				$sub("#CON_12_OTHER1_LIST").val(other1_array);
				$sub("#ACTION_LIST").val(action_array);
				$sub("#ACT_P1_LIST").val(actp1_array);
				$sub("#ACT_P2_LIST").val(actp2_array);
			}

			function closeTODRRULES() {
				var cnt=0;
				$sub("#btn-add").attr("class", "btn-add1");
				$sub("#btn-add").click(function() {
					addTODRRULES();
				});

				$sub("#newrule").hide(500, function() {
					clearRulesData();
					setSave = 0;
				});

				for(var i=0 ;i<10 ;i++){
					//$sub("#edit"+i).show();
					//$sub("#delete"+i).show();
					$sub("#edit"+i).css('visibility', 'visible');
					$sub("#delete"+i).css('visibility', 'visible');
				}

				$sub(".todr-rules-con1").each(function() {
					cnt++;
				});
				if(edit_param == true){
					if(cnt==10){
						disableAdd();
					}
				}
				edit_param = false;
			}

			function closeDialog() {
				parent.closeAddEditDialog();
			}

		</script>
		<script type="text/javascript">
			function setToolTip() {
				$sub('a.ttip').tooltip({
					track : true,
					delay : 0,
					showURL : false,
					showBody : " - ",
					fade : 250
				});
			}
		</script>
	</head>
	<body class="popup">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 300px">
				<form id="addmanipulationtable" name="addmanipulationtable" class="popup-form" action="sipheader.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr>
							<td style="padding-left: 40px">Name</td>
							<td width="100"></td>
							<td style="padding-right: 40px">
								<input maxlength="16" id="NAME" name="NAME" value="<?php echo $name_val ?>" type="text" class="ipBox" />
								 <img id="sipheaders_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr id="errow1" style="display: none"><td height="5"></td></tr>
						<tr id="errow2" style="display: none" align="right"> <td colspan="3" aligh="center"> <span id="errorText"></span> </td> </tr>
						<tr id="errow3" style="display: none"><td height="5"></td></tr>
						<tr>
							<td style="padding-left: 40px">Description</td>
							<td width="100"></td>
							<td style="padding-right: 40px">
								<input maxlength="64" id="DESCRIPTION" name="DESCRIPTION" type="text" value="<?php echo $desc_val ?>" class="ipBox" />
								<img id="sipheaders_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td height="5" ></td>
						</tr>
						<tr id="assignedrule">
							<td colspan="3">
								<table id="allrules_list" class="rules-table" cellpadding="0" cellspacing="0" >
									<tr>
										<th>
											List of Conditions
											<input type="button" id="btn-add" class="btn-add1" onfocus="if(this.blur)this.blur()" />
										</th>
									</tr>
									<tr>
										<td>
										<table class="todr-rules" >
											<tr>
												<td class="todr-rules-td">
													Condition1
												</td>
												<!-- <td class="todr-rules-td">
													Header
												</td>
												<td class="todr-rules-td">
													pattern
												</td>
												<td class="todr-rules-td">
													Params
												</td>
												<td class="todr-rules-td">
													hdname
												</td>
												<td class="todr-rules-td">
													hdindex
												</td>
												<td class="todr-rules-td">
													others1
												</td>
												<td class="todr-rules-td">
													others2
												</td> -->
												<td class="todr-rules-td">
													Action
												</td>
												<td class="todr-rules-td">Param1</td> 
												<td class="todr-rules-td">param2</td>	 
												<td class="todr-rules-td">
													Options
												</td>
											</tr>
										</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td height="5"></td></tr>
						<tr id="newrule" style="display: none">
							<td colspan="3">
								<table id="todr_rules" class="rules-form-table" cellpadding="0" cellspacing="0">
									<tr>
										<th>
											Create/Edit Conditions
											<input type="button" class="btn-rule-close1" onclick="closeTODRRULES()" onfocus="if(this.blur)this.blur()" />
											<input type="button" id="btn-save-inner" class="btn-rule-save1" onclick="saveTODRRULES()" onfocus="if(this.blur)this.blur()" />
										</th>
									</tr>
									<tr>
										<td colspan="3">
										<table class="add-todr-rules" border="0" align="center">
											<tr align="center">
												<td align="left" width="50%">Condition</td>
												<td align="left" width="50%">
												<select id="CON_1" name="CON_1" type="text" onchange="condition1()" onkeyup="condition1()" onkeydown="condition1()" class="ipDrpBox" style="width: 120px;" > 
													<option value="none">None</option>
													<option value="eq">eq</option>
													<option value="ne">ne</option>
													<option value="is_present_hf">is_present_hf</option>
													<option value="is_not_present_hf">is_not_present_hf</option>
													<option value="is_method">is_method</option>
													<option value="is_not_method">is_not_method</option>
													<option value="has_body">has_body</option>
													<option value="has_no_body">has_no_body</option>
									
												</select>					
												<img id="sipheaders_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										</tr>
										<tr>
								<td colspan="3">
									<table id="othersrulestab" style="display: none" border="0"  align="center" width="100%">
											<tr align="center" id="otherrow1" style="display: none" >
												<td id="other1">OTHER_P1</td>				
												<td align="left" width="50%">
													<input maxlength="128" value="" id="CON_12_OTHER1" name="CON_12_OTHER1" type="text" class="ipBox" style="width: 120px" />
													<img id="sipheaders_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
											</tr>
										<!--	<tr align="center" id="otherrow2" style="display: none" >
												<td align="left" width="50%" id="other2">OTHER_P2</td>
												<td align="left" width="50%">
													<input maxlength="128" value="" id="CON_12_OTHER2" name="CON_12_OTHER2" type="text" class="ipBox" style="width: 120px" />
												</td>
											</tr> -->
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<table id="headerrulestab" style="display: none" border="0"  align="center" width="100%" >
									<tr align="center">
										<td>Header</td>
											<td align="left" width="50%">
											<select id="CON_11_HD" name="CON_11_HD" type="text" onchange="condition2()" onkeyup="condition2()" onkeydown="condition2()" class="ipDrpBox" style="width: 120px;" >
												<option value="$ai">$ai</option>
												<option value="$adu">$adu</option>
												<option value="$ar">$ar</option>
												<option value="$au">$au</option>
												<option value="$ad">$ad</option>
												<option value="$auth.opaque">$auth.opaque</option>
												<option value="$auth.alg">$auth.alg</option>
												<option value="$auth.qop">$auth.qop</option>
												<option value="$ci">$ci</option>
												<option value="$cl">$cl</option>
												<option value="$cs">$cs</option>
												<option value="$ct">$ct</option>
												<option value="$hdr">$hdr</option>
												<option value="$cT">$cT</option>
												<option value="$dd">$dd</option>
												<option value="$di">$di</option>
												<option value="$dip">$dip</option>
												<option value="$dir">$dir</option>
												<option value="$dp">$dp</option>
												<option value="$dP">$dP</option>
												<option value="$ds">$ds</option>
												<option value="$du">$du</option>
												<option value="$fd">$fd</option>
												<option value="$fn">$fn</option>
												<option value="$ft">$ft</option>
												<option value="$fu">$fu</option>
												<option value="$fU">$fU</option>
												<option value="$ml">$ml</option>
												<option value="$od">$od</option>
												<option value="$op">$op</option>
												<option value="$oP">$oP</option>
												<option value="$ou">$ou</option>
												<option value="$oU">$oU</option>
												<option value="$pd">$pd</option>
												<option value="$pn">$pn</option>
												<option value="$pr">$pr</option>
												<option value="$pU">$pU</option>
												<option value="$pu">$pu</option>
												<option value="$rd">$rd</option>
												<option value="$re">$re</option>
												<option value="$rm">$rm</option>
												<option value="$rp">$rp</option>
												<option value="$rP">$rP</option>
												<option value="$rt">$rt</option>
												<option value="$ru">$ru</option>
												<option value="$rU">$rU</option>
												<option value="$ru_q">$ru_q</option>
												<option value="$Ri">$Ri</option>
												<option value="$Rp">$Rp</option>
												<option value="$si">$si</option>
												<option value="$sp">$sp</option>
												<option value="$td">$td</option>
												<option value="$tn">$tn</option>
												<option value="$tt">$tt</option>
												<option value="$tu">$tu</option>
												<option value="$tU">$tU</option>
												<option value="$time">$time</option>
												<option value="$Tf">$Tf</option>
												<option value="$Ts">$Ts</option>
												<option value="$Tsm">$Tsm</option>
												<option value="$TS">$TS</option>
												<option value="$ua">$ua</option>
											</select>
											<img id="sipheaders_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										</tr>
										<tr>
										<td colspan="3">
										<table id="hdrrulestab" style="display: none" border="0"  align="center" width="100%">
										<tr align="center">
											<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
											<td>Header Name</td>
											<td align="left" width="50%">
												<input maxlength="128" id="CON_11_HD_NAME" name="CON_11_HD_NAME" value="" type="text" class="ipBox" style="width: 120px" />
												<img id="sipheaders_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr align="center">
											<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
											<td>Index</td>
											<td align="left" width="50%">
											<select id="CON_11_HD_INDEX" name="CON_11_HD_INDEX" type="text" onchange="condition2()" onkeyup="condition2()" onkeydown="condition2()" class="ipDrpBox" style="width: 120px" >
												<?php
												$i=0;
												for($i=0;$i<10;$i++) {
												?>
												<option value="<?php echo $i;?>"><?php echo $i;?></option>
												<?php } ?>
												</select>
												<img id="sipheaders_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />	
											</td>
										</tr>
										</table>
										</td>
										</tr>
										<tr align="center"> <td colspan="3">
											<table id="headerrulestab1" style="display: none" border="0"  align="center" width="100%" >
												<tr align="center">
												<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
												<td>Params</td> 
												<td align="left" width="50%">
												<select id="CON_11_HD_PARAMS" name="CON_11_HD_PARAMS" type="text" onchange="condition3()" onkeyup="condition3()" onkeydown="condition3()" class="ipDrpBox" style="width: 120px;" >
													<option value="none">none</option>
													<option value="name">name</option>
													<option value="uri">uri</option>
													<option value="q">q</option>
													<option value="expires">expires</option>
													<option value="methods">methods</option>				
													<option value="received">received</option>
													<option value="params">all</option>
												</select>
												<img id="sipheaders_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
												</tr>
											</table>
											</td>
			 							</tr>
										<tr align="center">
											<td>Pattern</td>				
											<td align="left" width="50%">
											<input maxlength="128" value="" id="CON_11_PATTERN" name="CON_11_PATTERN" type="text" class="ipBox" style="width: 120px" />
											<img id="sipheaders_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
									</table>
								</td> 
							</tr>
							<tr colspan="3" align="center">
							<td align="left" width="50%">Action</td>
								<td align="left" width="50%">
									<select id="ACTION" name="ACTION" type="text" onchange="condition3()" onkeydown="condition3()" onkeyup="condition3()" class="ipDrpBox" style="width: 120px;" >
										<option value="append_time0">append_time</option>		
										<option value="remove_hf1">remove_hf</option> 
										<option value="append_hf1or2">append_hf</option>
										<option value="insert_hf1or2">insert_hf</option>
										<option value="append_urihf2">append_urihf</option>
										<option value="append_cturi1">append_cturi</option>
										<option value="append_ctparam1">append_ctparam</option>
									</select>
									<img id="sipheaders_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
								<tr>
								<td colspan="3">
									<table id="actionrulestab" style="display: none" border="0"  align="center" width="100%">
										<tr id="actionrule1" align="center" style="display: none">
										<td> Param-1</td>
										<td align="left" width="50%">
											<input value="" id="ACT_P1" name="ACT_P1" type="text" class="ipBox" style="width: 120px" />
											<img id="sipheaders_ttip_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										</tr>
										<tr id="actionrule2" align="center" style="display: none">
										<td> Param-2</td>
										<td align="left" width="50%">
											<input value="" id="ACT_P2" name="ACT_P2" type="text" class="ipBox" style="width: 120px" />
	<img id="sipheaders_ttip_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										</tr>
									</table>
									</td>
								</tr>
								</table>
							</td>
							</tr>
							</table>
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="CON_1_LIST" id="CON_1_LIST" value="" />
					<input type="hidden" name="CON_11_HD_LIST" id="CON_11_HD_LIST" value="" />
					<input type="hidden" name="CON_11_PATTERN_LIST" id="CON_11_PATTERN_LIST" value="" />
					<input type="hidden" name="CON_11_HD_PARAMS_LIST" id="CON_11_HD_PARAMS_LIST" value="" /> 
					<input type="hidden" name="CON_11_HD_NAME_LIST" id="CON_11_HD_NAME_LIST" value="" />
					<input type="hidden" name="CON_11_HD_INDEX_LIST" id="CON_11_HD_INDEX_LIST" value="" />
					<input type="hidden" name="CON_12_OTHER1_LIST" id="CON_12_OTHER1_LIST" value="" />
					<input type="hidden" name="ACTION_LIST" id="ACTION_LIST" value="" />
					<input type="hidden" name="ACT_P1_LIST" id="ACT_P1_LIST" value="" />
					<input type="hidden" name="ACT_P2_LIST" id="ACT_P2_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" id="btn-submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input id="btn-update" class="btn-update" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
</html>
