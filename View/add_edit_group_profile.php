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
	include_once("Model/group_profile_model.php");

	$obj = ManipulationTable::getConfig();
	$flag = "add";
	$page_title = "Create Group Profiles";
	$name_val = "";
	$desc_val = "";
	$groupheadernamelist = $obj -> getgroupnamelist();
	$getresourcenames = $obj ->getresourcenames();
	$maxrescount = count($getresourcenames);
	$arr_group_desuri_id = array();
	$arr_group_desuri_gid = array();
	$arr_group_desuri_dsturi = array();
	$arr_group_resource_rid  = array();
	$arr_group_resource_rname = array();
	$arr_group_resource_rcount = array();
	$createdDataNames = array();
	$usedarray = array();

	if(isset($_GET['data'])) {
		$trunk = $_GET['data'];
		$edit_data = $obj -> get_group_profile($trunk);
		$flag = "edit";
		$page_title = "Edit Group Profiles : ".$trunk;
		while($row = $edit_data->fetchArray(SQLITE3_ASSOC)) {
			$group_profile_id = $row["G_ID"];
			$name_val = $row["LB_GRP_NAME"];
			$desc_val = $row["LB_GRP_DESC"];
		}
		$edit_data1 = $obj -> getgroupdesurilist($group_profile_id);
		while($row1 = $edit_data1 -> fetchArray(SQLITE3_ASSOC)) {
			array_push($arr_group_desuri_id, $row1["R_ID"]);
			array_push($arr_group_desuri_gid, $row1["G_ID"]);
			array_push($arr_group_desuri_dsturi, $row1["DSTURI"]);
		}
		for($i=0; $i<count($arr_group_desuri_id); $i++) {
			$edit_data2 = $obj -> getgroup_resourcelist($arr_group_desuri_id[$i]);
			$group_resource_rid= array();
			$group_resource_rname = array();
			$group_resource_rcount = array();
			while($row2 = $edit_data2 -> fetchArray(SQLITE3_ASSOC)) {
				array_push($group_resource_rid, $row2["R_ID"]);
				array_push($group_resource_rname, $row2["R_NAME"]);
				array_push($group_resource_rcount, $row2["R_COUNT"]);
			}
			$resource_id = implode(":",$group_resource_rid);
			$resource_name = implode(":",$group_resource_rname);
			$resource_count = implode(":",$group_resource_rcount);
			array_push($arr_group_resource_rid,$resource_id);
			array_push($arr_group_resource_rname,$resource_name);
			array_push($arr_group_resource_rcount,$resource_count);
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
		<link rel="stylesheet" type="text/css" media="screen" href="css/tooltip1.css" />
		<script src="js/jquery.validationEngine-en.js"></script>
		<script src="js/jquery.validationEngine.js"></script>
		<script src="js/jquery.tooltip.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script type="text/javascript">
			var flag = "<?php echo $flag ?>";
			var maxrescount = "<?php echo $maxrescount ?>";
			var count = 0;
			var edit_param = false;
			var setSave = 0;
			var setcondition = 0;
			var group_desuri_id_edit;
			var group_desuri_gid_edit;
			var group_desuri_dsturi_edit;
			var group_resource_rid_edit;
			var group_resource_rname_edit;
			var group_resource_rcount_edit;
			var usedres = new Array();
			var resnamelist = new Array();
			var tmp1 = "";

			<?php foreach($getresourcenames as $key=>$val) { ?>
					resnamelist.push('<?php print $val ?>');
			<?php } ?>

			var elmtid;
			var arr_list = new Array();
			var arr_group_desuri_id = new Array();

			<?php foreach($arr_group_desuri_id as $key => $val){ ?>
					arr_group_desuri_id.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_group_desuri_gid = new Array();
			<?php foreach($arr_group_desuri_gid as $key => $val){ ?>
					arr_group_desuri_gid.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_group_desuri_dsturi = new Array();
			<?php foreach($arr_group_desuri_dsturi as $key => $val){ ?>
					arr_group_desuri_dsturi.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_group_resource_rid = new Array();
			<?php foreach($arr_group_resource_rid as $key => $val){ ?>
					arr_group_resource_rid.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_group_resource_rname = new Array();
			<?php foreach($arr_group_resource_rname as $key => $val){ ?>
					arr_group_resource_rname.push('<?php echo $val; ?>');
			<?php } ?>

			var arr_group_resource_rcount = new Array();
			<?php foreach($arr_group_resource_rcount as $key => $val){ ?>
					arr_group_resource_rcount.push('<?php echo $val; ?>');
			<?php } ?>

			$sub(document).ready(function() {
				initValidation();
				$sub("#btn-add").click(function() {
					addTODRRULES();
				});
				if(flag == "edit") {
					for(var i = 0; i <arr_group_desuri_id.length; i++) {
						var str = '<tr id="count'+i+'">';
						str += '<td class="todr-rules-group-desuri-id" style="display: none">'+arr_group_desuri_id[i]+'</td>';
						str += '<td class="todr-rules-group-desuri-gid"  style="display: none">'+arr_group_desuri_gid[i] +'</td>';
						str += '<td class="todr-rules-group-desuri-dsturi" >'+arr_group_desuri_dsturi[i] +'</td>';
						str += '<td class="todr-rules-group-resource-rid"  style="display: none">'+arr_group_resource_rid[i]+'</td>';
						str += '<td class="todr-rules-group-resource-rname" style="display: none">'+arr_group_resource_rname[i]+'</td>';
						str += '<td class="todr-rules-group-resource-rcount" style="display: none">'+arr_group_resource_rcount[i]+'</td>';
						str += '<td><input type="button" class="btn-edit" ';
						str += ' id="edit'+i+'" onclick="editRules(this)" ';
						str += ' onfocus="if(this.blur)this.blur()" >';
						str += ' <input type="button" class="btn-delete" ';
						str += ' onclick="removeRules(this)" id="delete'+i+'" '; str += ' onfocus="if(this.blur)this.blur()" ></td></tr>';
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
				$sub("#LB_GRP_NAME").attr("data-validation-engine", "validate[required,custom[onlyLetterNumber], minSize[3]]");
				$sub("#LB_GRP_DESC").attr("data-validation-engine", "validate[required,custom[onlyLetterNumber], minSize[3]]");
				$sub("#addmanipulationtable").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function clearRulesData() {
				var i =0;
				$sub('#todr_rules').validationEngine('hideAll');
				$sub("#GROUP_DESURI_ID").val("");
				$sub("#GROUP_DESURI_GID").val("");
				$sub("#GROUP_DESURI_DESURI").val("");
				$sub("#GROUP_RESOURCE_RID").val("");
				for(i=1;i<=maxrescount;i++) {
					$sub("#GROUP_RESOURCE_RNAME"+ i).val("");
					$sub("#GROUP_RESOURCE_RCOUNT"+ i).val("");
				}
				for (i =1;i<maxrescount;i++) {
					$sub("#resname"+(i+1)).hide();
				}
			}

			function removeRules(elem) {
				var elem_id = elem.id;
				elem_id = elem_id.substr(6,1);
				count--;
				$sub(".btn-delete").live('click', function(event) {
					$sub(this).parent().parent().remove();
				});
				var index = arr_group_desuri_id.indexOf($sub("#count"+elem_id+" td.todr-rules-group-desuri-id").text());
				if (index >= 0) {
					arr_group_desuri_id[index] = arr_group_desuri_id[0];
					arr_group_desuri_id.shift();
					arr_group_desuri_gid[index] = arr_group_desuri_gid[0];
					arr_group_desuri_gid.shift();
					arr_group_desuri_dsturi[index] = arr_group_desuri_dsturi[0];
					arr_group_desuri_dsturi.shift();
					arr_group_resource_rid[index] = arr_group_resource_rid[0];
					arr_group_resource_rid.shift();
					arr_group_resource_rname[index] = arr_group_resource_rname[0];
					arr_group_resource_rname.shift();
					arr_group_resource_rcount[index] = arr_group_resource_rcount[0];
					arr_group_resource_rcount.shift();
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
					var resnames = new Array();
					resnames = edit_rule[4].split(":");
					var rescount = resnames.length;
					var rescounts = new Array();
					rescounts = edit_rule[5].split(":");
					$sub("#GROUP_DESURI_ID").val(edit_rule[0]);
					group_desuri_id_edit=edit_rule[0];
					$sub("#GROUP_DESURI_GID").val(edit_rule[1]);
					group_desuri_gid_edit=edit_rule[1];
					$sub("#GROUP_DESURI_DESURI").val(edit_rule[2]);
					group_desuri_dsturi_edit=edit_rule[2];
					$sub("#GROUP_RESOURCE_RID").val(edit_rule[3]);
					group_resource_rid_edit=edit_rule[3];
					group_resource_rname_edit=edit_rule[4];
					group_resource_rcount_edit=edit_rule[5];
					usedres.splice(0,usedres.length);
					tmp1 = "";
					var j = 0;
					for(j=0;j<rescount;j++) {
						if(j < maxrescount) {
							addelem((j+1));
							$sub("#resname"+(j+1)).show(500);
							$sub("#GROUP_RESOURCE_RNAME"+(j+1)).val(resnames[j]);
							$sub("#GROUP_RESOURCE_RCOUNT"+(j+1)).val(rescounts[j]);	
							usedres.push(resnames[j]);
							if(j != (maxrescount-1)) {
								if(j != (rescount-1)) {
									if(j != (maxrescount-1) ) {
										$sub("#resadd"+(j+2)).hide();
									}
									if(j != 0) {
										$sub("#resdel"+(j+1)).hide();
									}
									document.getElementById("GROUP_RESOURCE_RNAME"+(j+1)).disabled = true;
									document.getElementById("GROUP_RESOURCE_RCOUNT"+(j+1)).disabled = true;
								} else {
									$sub("#resadd"+(j+2)).show();
									$sub("#resdel"+(j+1)).show();
									document.getElementById("GROUP_RESOURCE_RNAME"+(j+1)).disabled = false;
									document.getElementById("GROUP_RESOURCE_RCOUNT"+(j+1)).disabled = false;
									addelem((j+2));
								}
							}
						}
					}
					edit_param = true;
					setSave = 1;
					setcondition = 1;
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
				$sub("#newrule").show(500);
				clearRulesData();
				usedres.splice(0,usedres.length);
				addelem(1);
				$sub("#resadd2").show(500);
				document.getElementById("GROUP_RESOURCE_RNAME1").disabled = false;
				document.getElementById("GROUP_RESOURCE_RCOUNT1").disabled = false;
				setSave = 1;
			}

			function saveTODRRULES() {
				var flag_data = 0;
				var group_resnames = new Array();
				var group_rescounts = new Array();
				if($sub("#GROUP_DESURI_DESURI").val()==""){
					$sub('#GROUP_DESURI_DESURI').validationEngine('showPrompt', '* This field is required', 'error', 'bottomRight', true);
					flag_data = 1;
					return;
				} else {
					var getVal = $sub("#GROUP_DESURI_DESURI").val();
					if ( getVal.indexOf(":") != -1 ) {
						var split_field = getVal.split(':');
						if ( split_field.length < 2 ) {
							$sub('#GROUP_DESURI_DESURI').validationEngine('showPrompt', '* Port Number is required', 'error', 'bottomRight', true);
							flag_data = 1;
							return ;
						}
						var ipaddressVal = split_field[0];
						var port = split_field[1];
					}
					var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
					var match_ipaddr = ipaddr.exec(ipaddressVal);
					var host = new RegExp(/^(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
					var match_host = host.exec(getVal);
					if ( match_ipaddr == null && match_host == null ) {
						$sub('#GROUP_DESURI_DESURI').validationEngine('showPrompt', '* Invalid ! Enter either a valid IP Address:Port (DOMAIN/IPADDRESS:PORT).', 'error', 'bottomRight', true);
						flag_data = 1;
						return;
					}
					if ( parseInt(parseFloat(port)) < 1 || parseInt(parseFloat(port)) > 65535 || (port == "") ) {
						$sub('#GROUP_DESURI_DESURI').validationEngine('showPrompt', '* Invalid ! Enter either a valid IP Address:Port (DOMAIN/IPADDRESS:PORT).', 'error', 'bottomRight', true);
						flag_data = 1;
						return;
					}
				}

				var j=0;
				var index1=0;
				for(j=1;j<=maxrescount;j++){
					document.getElementById("GROUP_RESOURCE_RNAME"+j).disabled = false;
					document.getElementById("GROUP_RESOURCE_RCOUNT"+j).disabled = false;
						if(( $sub("#GROUP_RESOURCE_RCOUNT"+j).val()) != "" ) {
							group_resnames[index1] = $sub("#GROUP_RESOURCE_RNAME"+j).val();
							group_rescounts[index1] = $sub("#GROUP_RESOURCE_RCOUNT"+j).val();
							index1++;
						}
				}

				var group_desuri_id = $sub("#GROUP_DESURI_ID").val();
				var group_desuri_gid = $sub("#GROUP_DESURI_GID").val();
				var group_desuri_desuri = $sub("#GROUP_DESURI_DESURI").val();
				var group_resource_rid = $sub("#GROUP_RESOURCE_RID").val();
				var group_resource_rname = group_resnames.join(":");
				var group_resource_rcount = group_rescounts.join(":");
				$sub('#todr_rules').validationEngine('hideAll');
				var arr_index = 0;
				var flag=false;
				var hideflag=false;
				for(var i = 0; i < arr_group_desuri_id.length; i++) {
					if(edit_param == true) { 
						if((arr_group_desuri_id[i] == group_desuri_id_edit) && (arr_group_desuri_gid[i] == group_desuri_gid_edit) && (arr_group_desuri_dsturi[i] == group_desuri_dsturi_edit) && (arr_group_resource_rid[i] == group_resource_rid_edit) && (arr_group_resource_rname[i] == group_resource_rname_edit) && (arr_group_resource_rcount[i] == group_resource_rcount_edit)){
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
						arr_group_desuri_id[arr_index] = group_desuri_id;
						arr_group_desuri_gid[arr_index] = group_desuri_gid;
						arr_group_desuri_dsturi[arr_index] = group_desuri_desuri;
						arr_group_resource_rid[arr_index] = group_resource_rid;
						arr_group_resource_rname[arr_index] = group_resource_rname;
						arr_group_resource_rcount[arr_index] = group_resource_rcount;
						$sub("#count"+arr_index).remove();
					} else{
						arr_group_desuri_id.push(group_desuri_id);
						arr_group_desuri_gid.push(group_desuri_gid);
						arr_group_desuri_dsturi.push(group_desuri_desuri);
						arr_group_resource_rid.push(group_resource_rid);
						arr_group_resource_rname.push( group_resource_rname);
						arr_group_resource_rcount.push(group_resource_rcount);
					}
					var str2 = '<tr><td class="todr-rules-td" style="display: none">D_ID</td>';
					str2 += '<td class="todr-rules-td" style="display: none" >D_GID</td>';
					str2 += '<td class="todr-rules-td" >Destination URI</td>';
					str2 += '<td class="todr-rules-td" style="display: none">R_ID</td>';
					str2 += '<td class="todr-rules-td" style="display: none">R_NAME</td>';
					str2 += '<td class="todr-rules-td" style="display: none">R_COUNT</td>';
					str2 += '<td class="todr-rules-td">option</td></tr>';

					var appendVal= str2;
					$sub("#newrule").hide(500, function() {
						for(var i = 0; i < arr_group_desuri_id.length; i++) {
							if((arr_group_desuri_id[i]!=null)){
								var str = '<tr id="count'+i+'">';
								str += '<td class="todr-rules-group-desuri-id" style="display: none">'+arr_group_desuri_id[i]+'</td>';
								str += '<td class="todr-rules-group-desuri-gid"  style="display: none">'+arr_group_desuri_gid[i] +'</td>';
								str += '<td class="todr-rules-group-desuri-dsturi" >'+arr_group_desuri_dsturi[i] +'</td>';
								str += '<td class="todr-rules-group-resource-rid"  style="display: none">'+arr_group_resource_rid[i]+'</td>';
								str += '<td class="todr-rules-group-resource-rname" style="display: none" >'+arr_group_resource_rname[i]+'</td>';
								str += '<td class="todr-rules-group-resource-rcount" style="display: none" >'+arr_group_resource_rcount[i]+'</td>';
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
					$sub("#LB_GRP_NAME").attr("disabled", param);
				} else {
					$sub("#LB_GRP_NAME").removeAttr("disabled");
				}
			}

			function addusedgn(val1) {
				if(val1 == 1) {
					usedres.splice(0,usedres.length);
				}
				var addnam = $sub("#GROUP_RESOURCE_RNAME"+val1).val();
				if((addnam != tmp1)&&(tmp1 != "")) {
					usedres.splice((usedres.length-1), 1, addnam); 
					addelem((val1+1));
				} else {
					var tval= usedres.indexOf(addnam);
					if(tval < 0) {
						usedres.push($sub("#GROUP_RESOURCE_RNAME"+val1).val());	
						addelem((val1+1));
					}
				}
			}

			function delusedgn(val1) {
				var delnam = $sub("#GROUP_RESOURCE_RNAME"+val1).val();
				var tval= usedres.indexOf(delnam);
				if(tval >= 0 ) {
					usedres.pop($sub("#GROUP_RESOURCE_RNAME"+val1).val());
				}
				tmp1 = 	$sub("#GROUP_RESOURCE_RNAME"+(val1-1)).val();
			}

			function addelem(val1) {
				var i =0;
				var j =0;
				var reslistcount = resnamelist.length;
				var usedlistcount =  usedres.length;
				document.getElementById("GROUP_RESOURCE_RNAME"+val1).options.length=0;
				for(i=0;i<reslistcount;i++) {
					var eset = 0;
					for(j=0;j<usedlistcount;j++) {
						if(resnamelist[i] == usedres[j] ) {
							eset =1;
						}
					}
					if(eset == 0) {
						var x = document.getElementById("GROUP_RESOURCE_RNAME"+val1);
						var option = document.createElement("option");
						option.text = resnamelist[i];
						option.value = resnamelist[i];
						x.add(option);
					}
				}
			}
// adding resource
			function addresname(val) {
				var pattern = new RegExp(/^[\-\+]?\d+$/);
				if(($sub("#GROUP_RESOURCE_RCOUNT"+(val-1)).val()=="") || ($sub("#GROUP_RESOURCE_RCOUNT"+(val-1)).val()<=0) || ($sub("#GROUP_RESOURCE_RCOUNT"+(val-1)).val()>=999) || (!(pattern.test($sub("#GROUP_RESOURCE_RCOUNT"+(val-1)).val())))) {
					$sub("#GROUP_RESOURCE_RCOUNT"+(val-1)).attr("data-validation-engine","validate[required,custom[integer],min[0],max[999]]");
					return false;
				} else {
					document.getElementById("GROUP_RESOURCE_RNAME"+(val-1)).disabled = true;
					document.getElementById("GROUP_RESOURCE_RCOUNT"+(val-1)).disabled = true;
					$sub("#resadd"+val).hide();
					if(val != 2) {
						$sub("#resdel"+(val-1)).hide();
					}
					$sub("#resname"+val).show(500);
					if(val < maxrescount) {
						$sub("#resadd"+(val+1)).show();
					}
					$sub("#resdel"+(val)).show();
					addusedgn((val-1));
					return true;
				}
			}
// deleting resource
			function delresname(val) {
				document.getElementById("GROUP_RESOURCE_RNAME"+(val-1)).disabled = false;
				document.getElementById("GROUP_RESOURCE_RCOUNT"+(val-1)).disabled = false;
				$sub("#resname"+val).hide();
				$sub("#GROUP_RESOURCE_RCOUNT"+val).val("");
				$sub("#resadd"+val).show(500);
				if(val != 2) {
					$sub("#resdel"+(val-1)).show(500);
				}
				delusedgn(val);
			}

			function submitData() {
				if ( flag == "add") {
					var mediaArr = new Array();
					var get_sipname_val = document.getElementById("LB_GRP_NAME").value;
					var post_sipname_val = get_sipname_val.trim();
					<?php
						foreach($groupheadernamelist as $key=>$val) {
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
							document.getElementById("errorText").className = "alert-box errorMsg";
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
				var group_desuri_id_array = new Array();
				$sub(".todr-rules-group-desuri-id").each(function() {
					group_desuri_id_array.push($sub(this).text());
				});

				var group_desuri_gid_array = new Array();
				$sub(".todr-rules-group-desuri-gid").each(function() {
					group_desuri_gid_array.push($sub(this).text());
				});

				var group_desuri_dsturi_array = new Array();
				$sub(".todr-rules-group-desuri-dsturi").each(function() {
					group_desuri_dsturi_array.push($sub(this).text());
				});

				var group_resource_rid_array = new Array();
				$sub(".todr-rules-group-resource-rid").each(function() {
					group_resource_rid_array.push($sub(this).text());
				});

				var group_resource_rname_array = new Array();
				$sub(".todr-rules-group-resource-rname").each(function() {
					group_resource_rname_array.push($sub(this).text());
				});

				var group_resource_rcount_array = new Array();
				$sub(".todr-rules-group-resource-rcount").each(function() {
					group_resource_rcount_array.push($sub(this).text());
				});

				$sub("#DESURI_ID_LIST").val(group_desuri_id_array);
				$sub("#DESURI_GID_LIST").val(group_desuri_gid_array);
				$sub("#DSTURI_LIST").val(group_desuri_dsturi_array);
				$sub("#RES_RID_LIST").val(group_resource_rid_array);
				$sub("#R_NAME_LIST").val(group_resource_rname_array);
				$sub("#R_COUNT_LIST").val(group_resource_rcount_array);
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
					$sub("#edit"+i).css('visibility', 'visible');
					$sub("#delete"+i).css('visibility', 'visible');
				}
				$sub(".todr-rules-group-desuri-id").each(function() {
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
			<div class="popup-content" style="height: 400px">
				<form id="addmanipulationtable" name="addmanipulationtable" class="popup-form" action="group_profile.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr>
							<td style="padding-left: 40px">Group Name</td>
							<td width="100"></td>
							<td style="padding-right: 40px">
								<input maxlength="30" id="LB_GRP_NAME" name="LB_GRP_NAME" value="<?php echo $name_val ?>" type="text" class="ipBox" />
							</td>
						</tr>
						<tr id="errow1" style="display: none"><td height="5"></td></tr>
						<tr id="errow2" style="display: none" align="right"> <td colspan="3" aligh="center"> <span id="errorText"></span> </td> </tr>
						<tr id="errow3" style="display: none"><td height="5"></td></tr>
						<tr>
							<td style="padding-left: 40px">Group Description</td>
							<td width="100"></td>
							<td style="padding-right: 40px">
								<input maxlength="30" id="LB_GRP_DESC" name="LB_GRP_DESC" type="text" value="<?php echo $desc_val ?>" class="ipBox" />
							</td>
						</tr>
						<tr>
							<td height="5" ></td>
						</tr>
						<tr id="assignedrule">
							<td colspan="3">
								<table id="allrules_list" class="rules-table" cellpadding="0" cellspacing="0" >
									<tr>
										<th>List of Destination URI<input type="button" id="btn-add" class="btn-add1" onfocus="if(this.blur)this.blur()" />
										</th>
									</tr>
									<tr>
										<td>
										<table class="todr-rules" >	
											<tr>
												<td class="todr-rules-td" style="display: none">D_ID</td>
												<td class="todr-rules-td" style="display: none">D_GID</td>
												<td class="todr-rules-td">Destination URI</td>
												<td class="todr-rules-td" style="display: none">R_ID</td> 
												<td class="todr-rules-td" style="display: none">R_NAME</td>
												<td class="todr-rules-td" style="display: none">R_COUNT</td>
												<td class="todr-rules-td">Options</td>
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
										<th>Create/Edit Resource Profile
											<input type="button" class="btn-rule-close1" onclick="closeTODRRULES()" onfocus="if(this.blur)this.blur()" />
											<input type="button" id="btn-save-inner" class="btn-rule-save1" onclick="saveTODRRULES()" onfocus="if(this.blur)this.blur()" />
										</th>
									</tr>
									<tr>
										<td colspan="3">
										<table id="resnamtable" class="add-todr-rules" border="0" align="center">
											<tr align="center" style="display: none">
												<td align="left" width="50%">Desuri ID</td>
												<td align="left" width="50%">
													<input maxlength="30" id="GROUP_DESURI_ID" name="GROUP_DESURI_ID" value="" type="text" class="ipBox" style="width: 120px" />
												</td>
											</tr>
											<tr align="center" style="display: none">
												<td align="left" width="50%">Desuri GID</td>
												<td align="left" width="50%">
													<input maxlength="30" value="" id="GROUP_DESURI_GID" name="GROUP_DESURI_GID" type="text" class="ipBox" style="width: 120px" />
												</td>	
											</tr>
											<tr align="center" style="display: none">
												<td align="left" width="50%">res id</td>
												<td align="left" width="50%">
													<input maxlength="30" value="" id="GROUP_RESOURCE_RID" name="GROUP_RESOURCE_RID" type="text" class="ipBox" style="width: 120px" />
												</td>
											</tr>
											<tr align="center">
												<td align="left" width="50%">Desuri</td>
												<td align="left" width="50%">
													<input maxlength="30" value="" id="GROUP_DESURI_DESURI" name="GROUP_DESURI_DESURI" type="text" class="ipBox" style="width: 120px" />
												</td>
											</tr>
<!-- resource starts -->
											<?php
											$i =0;
											for($i=1;$i<=$maxrescount;$i++) {
											?>

											<tr align="center" id = "resname<?php echo $i; ?>" <?php if($i != 1) {?>style="display: none" <?php } ?>>
												<td align="left" width="50%">Res Name</td>
												<td align="left" width="50%">
													<select id="GROUP_RESOURCE_RNAME<?php echo $i; ?>" name="GROUP_RESOURCE_RNAME<?php echo $i; ?>" type="text" class="ipDrpBox" style="width: 120px;" >
													</select>
												</td>
												<td align="left" width="50%">Res Count</td>
												<td align="left" width="50%">
													<input maxlength="30" value="" id="GROUP_RESOURCE_RCOUNT<?php echo $i; ?>" name="GROUP_RESOURCE_RCOUNT<?php echo $i; ?>" type="text" maxlength= "3" class="ipBox" style="width: 120px" />
												</td>
												<?php
												if($i != $maxrescount) {
												?>
													<td align="left" width="50%"><input type="button" class="btn-add" id="resadd<?php echo $i+1; ?>" onclick="addresname(<?php echo $i+1; ?>)" onfocus="if(this.blur)this.blur()"  > </td>
												<?php
												}
												if($i != 1) {
												?>
													<td align="left" width="50%"><input type="button" class="btn-delete" id="resdel<?php echo $i; ?>" onclick="delresname(<?php echo $i; ?>)" onfocus="if(this.blur)this.blur()"  > </td>
												<?php
												}
												?>
											</tr>
											<?php
											}
											?>
<!-- resource ends -->
										</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="DESURI_ID_LIST" id="DESURI_ID_LIST" value="" />
					<input type="hidden" name="DESURI_GID_LIST" id="DESURI_GID_LIST" value="" />
					<input type="hidden" name="DSTURI_LIST" id="DSTURI_LIST" value="" /> 
					<input type="hidden" name="RES_RID_LIST"  id="RES_RID_LIST" value="" />
					<input type="hidden" name="R_NAME_LIST" id="R_NAME_LIST" value="" />
					<input type="hidden" name="R_COUNT_LIST" id="R_COUNT_LIST" value="" />
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
