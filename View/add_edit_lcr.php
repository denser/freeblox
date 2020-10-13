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
	include_once("Model/lcr_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/trunk_config_model.php");
	include_once("Model/sip_profile_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/devicelist_settings_model.php");

	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$internalinterface=$devicelist_settings_config->getsipnamesvalues(0);
	$lcr_config = LCRConfigModel::getInstance();
	$sip_config = SIPProfileConfigModel::getInstance();
	$trunk_config = TrunkConfigModel::getInstance();
	$sip_config_profiles = $sip_config->getSIPProfilesName();
	$trunks_config_profiles = $trunk_config->getAllTrunkConfigNames();
	$sip_lan_profiles = $sip_config->getSIPInternalProfiles();
	$lanProfiles = $sip_config->sipLanProfiles();
	$lanProfilesCount = count($lanProfiles);
	$lcrNames = $lcr_config->getNames();
	$sipLanProfilesCount = count($sip_lan_profiles);
	$k = 0;
	while ($row = $trunks_config_profiles->fetchArray()) {
		$trunk_profiles[$k] = $row[0];
		$k++;
	}
	$lcr_sip_profiles_conf = $lcr_config->getLCR_SIP_Profiles();
	$a = 0;
	while ($row = $lcr_sip_profiles_conf->fetchArray()) {
		$sip_lcr_profiles[$a] = $row[0];
		$a++;
	}
	$res = 0;
	$page_title = "Create LCR Rule";
	$flag = "add";
	$name = "";
	$desc = "";
	$match_digits = "";
	$sip_profiles = "";
	$trunks = "";
	$email_id = "";
	$createdDataNames = array();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit LCR Rule";
		$row = $lcr_config->getLCRRule($id);
		if($row) {
			$name = $row[LCRConfigModel::$NAME];
			$desc = $row[LCRConfigModel::$DESCRIPTION];
			$match_digits = $row[LCRConfigModel::$MATCH_DIGITS];
			$sip_profiles = $row[LCRConfigModel::$SIP_PROFILE];
			$trunks = $row[LCRConfigModel::$TRUNKS];
			$get_trunks = SipSecuritySettingsModel::unSerializeSIPMethods($row[LCRConfigModel::$TRUNKS]);
			$email_id = "";
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ($_POST['submit_action'] == "2") { // Add a new rule
			if ( $_POST['sip_lan_profile'] == "--No Profile--") {
				$post_sip_profile = NULL;
			} else {
				$post_sip_profile = $_POST['sip_lan_profile'];
			}
			if ( $_POST['trunksSel'] != "") {
				$post_trunks = SipSecuritySettingsModel::serializeSIPMethods($_POST['trunksSel']);
			}
			$email_id = "";
			$lcr_config->addLCRRule(trim($_POST['name']),
						trim($_POST['comments']),
						trim($_POST['digits_to_match']),
						$post_sip_profile,
						$post_trunks,
						$email_id);
			$lcr_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, 
					"LCR rule added", DEPLOY_MODULES::SBC_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") { // Edit a rule
			$id = $_POST['id'];
			$row = $lcr_config->getLCRRule($id);
			if ( $_POST['sip_lan_profile'] == "--No Profile--") {
				$post_sip_profile = NULL;
			} else {
				$post_sip_profile = $_POST['sip_lan_profile'];
			}
			if ( $_POST['trunksSel'] != "") {
				$post_trunks = SipSecuritySettingsModel::serializeSIPMethods($_POST['trunksSel']);
			}
			if($row) {
				$name = $row[LCRConfigModel::$NAME];
				$desc = $row[LCRConfigModel::$DESCRIPTION];
				$match_digits = $row[LCRConfigModel::$MATCH_DIGITS];
				$sip_profiles = $row[LCRConfigModel::$SIP_PROFILE];
				$trunks = $row[LCRConfigModel::$TRUNKS];
				$get_trunks = SipSecuritySettingsModel::unSerializeSIPMethods($row[LCRConfigModel::$TRUNKS]);
				$email_id = "";
				if ( $desc != trim($_POST['comments']) || $trunks != $post_trunks || 
					(strcmp($match_digits,trim($_POST['digits_to_match'])!=0)) || 
					$sip_profiles != $post_sip_profile || 
					$domain != trim($_POST['domain']) || 
					$password != trim($_POST['password']) ) {
						$lcr_config->editLCRRule(trim($_POST['name']), 
							trim($_POST['comments']), 
							trim($_POST['digits_to_match']) ,
							$post_sip_profile, $post_trunks, $email_id);
					$lcr_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, 
							"LCR rule edited", 
							DEPLOY_MODULES::SBC_CONFIG_STR, "");
				}
				$res = 1;
			}
		}
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
			$sub(document).ready(function(){
				initValidation();
				getPageTips("lcr_ttip");
				setTooltip();
			});

			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			function initValidation() {
				$sub( document ).ready(function() {
					var sip_profile_val = $sub("#sip_lan_profile").val();
					var trunks_val;
					$sub('#trunks option').each(function() {
						trunks_val = $sub(this).val();
					});
					if ( sip_profile_val == "--No Profile--" || trunks_val == null ) {
						$sub("#sip_lan_profile").attr("data-validation-engine","validate[required,custom[selectProfiles]]");
						$sub("#trunks").attr("data-validation-engine","validate[required,custom[selectProfiles]]");
					}
				});
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[rulename],minSize[5],maxSize[16]]");
				$sub("#digits_to_match").attr("data-validation-engine","validate[custom[onlyNumber],minSize[1],maxSize[16]]");
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub("#TRUNK_PREFIX").attr("data-validation-engine","validate[custom[onlyNumber],minSize[1],maxSize[16]]");

				$sub("#addlcrrule").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			$sub(document).ready(function(){
				$sub('input[type="button"]').click(function(){
					var $op = $sub('#trunksSel option:selected'),
						$this = $sub(this);
					if($op.length){
						($this.val() == 'Up') ?
							$op.first().prev().before($op) :
							$op.last().next().after($op);
					}
				});
			});

			function submitData() {
				var flag = '<?php print $flag ?>';
				if (flag == "add") {
					var lcrArr = new Array();
					var get_lcr_val = document.getElementById("NAME").value;
					var post_lcr_val = get_lcr_val.trim();
					<?php
						foreach($lcrNames as $key=>$val) {
					?>
							lcrArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var lcrCount = lcrArr.length;
					for (var i=0; i < lcrCount; i++) {
						if (post_lcr_val == lcrArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub('#errorText').show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				trunkcount = $sub('#trunksSel option').length; 
				if(trunkcount > 20) {
					document.getElementById("trunk_error").innerHTML = "Invalid! trunk config shouldn't exceed 20.";
					document.getElementById("trunk_error").className = "alert-box errorMsg";
					$sub('#trunk_error').show().delay(2000).fadeOut();
					return false;
				}
				if($sub("#addlcrrule").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					$sub('#trunksSel option').attr('selected', 'selected');
					formObj.submit();
					return true;
				}
			}

			function closeDialog() {
				parent.closeAddEditDialog();
			}

			function f1(val) {
				if(val == 1) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = "least_call_routing.php";
				}
			}
// function to copy address group values to another list
			function copyTrunksToSelectedList() {
				var i,j,k;
				var trunksLst =  document.getElementById('trunks');
				var trunksSelLst =  document.getElementById('trunksSel');
				var grpLstSelected = document.getElementById('trunks').value;
				var trunk_prefix_value =  document.getElementById('TRUNK_PREFIX').value;

				if(trunksLst.value == '' && trunksLst.length > 0) {
					alert("Please select an option to add!");
					trunksLst.focus();
					return false;
				} else if(trunksLst.length <= 0) {
					alert("There are no trunks in the list to be added.");
					return false;
				}
				if ( trunk_prefix_value == "" ) {
					alert("Invalid! Please enter a numeric value");
				}
				if ( trunk_prefix_value != "" ) {
					for( j=0; j< trunksLst.options.length; j++) {
						if(trunksLst[j].selected == true) {
							if(trunksSelLst.options.length == 0) {
								var selListTxt = trunksLst.options[j].text;
								var selListVal = trunksLst.options[j].value;
								var selGrpList = document.createElement('option');
								selGrpList.text = selListTxt+"/"+trunk_prefix_value;
								selGrpList.value = selListVal+"/"+trunk_prefix_value;;
								trunksSelLst.add(selGrpList,trunksSelLst.options[null]);
								document.getElementById('TRUNK_PREFIX').value = "";
								try {
									trunksSelLst.add(selGrpList,trunksSelLst.options[null]);
									document.getElementById('TRUNK_PREFIX').value = "";
								}
								catch(ex) {
									trunksSelLst.add(selGrpList,trunksSelLst.options[null]);
									document.getElementById('TRUNK_PREFIX').value = "";
								}
							}
							for ( k = 0; k < trunksSelLst.options.length; k++ ) {
								var splitCopiedArr = trunksSelLst.options[k].value.split("/");
								if ( trunksLst.options[j].value == splitCopiedArr[0] ) {
									break;
								}
								if(k == trunksSelLst.options.length - 1) {
									var selListTxt = trunksLst.options[j].text;
									var selListVal = trunksLst.options[j].value;
									var selGrpList = document.createElement('option');
									selGrpList.text = selListTxt+"/"+trunk_prefix_value;;
									selGrpList.value = selListVal+"/"+trunk_prefix_value;;
									trunksSelLst.add(selGrpList,trunksSelLst.options[null]);
									document.getElementById('TRUNK_PREFIX').value = "";
									try {
										trunksSelLst.add(selGrpList,trunksSelLst.options[null]);
										document.getElementById('TRUNK_PREFIX').value = "";
									}
									catch(ex) {
										trunksSelLst.add(selGrpList,trunksSelLst.options[null]);
										document.getElementById('TRUNK_PREFIX').value = "";
									}
								}
							}
						}
					}
				}
			}

			function copyAllTrunksToSelectedList() {
				var i,j;
				var found = 0;
				var trunksLst =  document.getElementById('trunks');
				var trunksSelLst =  document.getElementById('trunksSel');
				var trunk_prefix_value =  document.getElementById('TRUNK_PREFIX').value;

				if(trunksLst.length <= 0) {
					alert("There are no trunks in the list to be added.");
					return false;
				}
				if ( trunk_prefix_value == "" ) {
					alert("Invalid! Please enter a numeric value");
				}
				if ( trunk_prefix_value != "" ) {
					removeAllTrunksFromSelectedList();
					for ( i = 0 ; i < trunksLst.options.length; i++ ) {
						for ( j = 0; j < trunksSelLst.options.length; j++ ) {
							if ( trunksLst.options[i].value == trunksSelLst.options[j].value ) {
									found = 1;
									return false;
							}
						}
						if ( found !=  1 ) {
							var newOption = document.createElement("option");
							newOption.text = trunksLst.options[i].text+"/"+trunk_prefix_value;
							newOption.value = trunksLst.options[i].value+"/"+trunk_prefix_value;
							trunksSelLst.add(newOption,trunksSelLst.options[null]);
							document.getElementById('TRUNK_PREFIX').value = "";
						}
					}
				}
			}

			function removeTrunksFromSelectedList() {
				var i,j;
				var trunksSelLst =  document.getElementById('trunksSel');
				var trunksSelLstSelcted =  document.getElementById('trunksSel').value;
				var GrpListLen = trunksSelLst.options.length;
				var grpSelSI = trunksSelLst.selectedIndex;
				for(j = 0 ; j < GrpListLen; j++) {
					if(grpSelSI == -1) {
						document.getElementById('trunksSel').focus();
						return false;
					}
				}
				if(trunksSelLst.options.length < 1) {
					return false;
				}
				if(trunksSelLstSelcted == '') {
					return false;
				}
				for (i = trunksSelLst.options.length - 1; i >= 0; i--) {
					if (trunksSelLst.options[i].selected) {
						trunksSelLst.remove(i);
					}
				}
				return false;
			}

			function removeAllTrunksFromSelectedList() {
				var i;
				var trunksSelLst =  document.getElementById('trunksSel');
				if(trunksSelLst.options.length < 1) {
					return false;
				}
				for (i = trunksSelLst.options.length - 1; i >= 0; i--) {
					if (trunksSelLst.options[i]) {
						trunksSelLst.remove(i);
					}
				}
				return false;
			}

			function showLanIP() {
				var iface = document.getElementById("sip_lan_profile").value;
				if(iface != '--No Profile--') {
					var post_val = "1";
					$sub.ajax({
						type: "POST",
						cache : false,
						data : {lcr_lan_ip : iface, lcr_lan_post:post_val},
						url : "show_iface_ip.php",
						success : function(ipaddr) {
							document.getElementById("lanipaddr").innerHTML = ipaddr;
						}
					});
				} else {
					document.getElementById("lanipaddr").innerHTML = "";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addlcrrule" name="addlcrrule" class="popup-form" action="add_edit_lcr.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>
						<tr>
							<td>Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="15" maxlength="16" />
								<img id="lcr_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $desc; ?>"  size="48" maxlength="64" id="COMMENTS" class="ipBox" name="comments" />
								<img id="lcr_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Prefix To Match</td>
							<td width="20"></td>
							<td>
								<input type="text" size="18" maxlength="16" class="ipBox" value="<?php print $match_digits; ?>" name="digits_to_match" id="digits_to_match" />
								<img id="lcr_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Internal SIP Profile</td>
							<td width="20"></td>
						<!--	<?php
									if ( $lanProfiles != null ){
										$lanProfilesArray = array_values($lanProfiles); }
									$selectedLanProfile = array($sip_profiles);
									if ( count($lanProfilesArray) > 0 ) {
										$lan_profiles_avail = array_merge($lanProfilesArray,$selectedLanProfile);
										$count_lan_avail = count($lan_profiles_avail);
									} else {
										$lan_profiles_avail = $selectedLanProfile;
										$count_lan_avail = count($lan_profiles_avail);
									}
							?> -->
							<td>
								<select style="width: 160px;" name="sip_lan_profile" id="sip_lan_profile" class="ipDrpBox" onchange="showLanIP();" onkeyup="showLanIP();" onkeydown="showLanIP();">
							<?php
								$set =1;
								if(count($internalinterface) == 0) {
									$set =1;
								} else {
									for($i=0;$i<count($internalinterface);$i++) {
										$select_str = "";
										if($flag == "edit" && ($internalinterface[$i] == $sip_profiles)) {
											$select_str = "selected";
											$set = 0;
							?>
									<option value="<?php echo $internalinterface[$i];?>" <?php print $select_str;?> > <?php echo $internalinterface[$i];?> </option>
							<?php
										} else {
											$check = $lcr_config->checksipprofiles($internalinterface[$i]);
											if($check == 0) {
												$set = 0;
								?>
									<option value="<?php echo $internalinterface[$i];?>"> <?php echo $internalinterface[$i];?> </option>
						<?php
											}
										}
									}
								}
								if($set == 1) { 
								?>
									<option value="--No Profile--">--No Profile--</option>
							<?php
								}
							?>
						<!--	<?php
								if ($flag == "add" ) {
									if ( $sipLanProfilesCount < 1 || $lanProfilesCount < 1 ) {
							?>
										<option value="--No Profile--">--No Profile--</option>
							<?php
									} else if ( $lanProfilesCount > 0 ) {
										for ( $i = 0; $i < $lanProfilesCount; $i++) {
							?>
									<option value="<?php print $lanProfilesArray[$i];?>"><?php print $lanProfilesArray[$i];?></option>
							<?php			}
									}
								} else if ( $flag == "edit" ) {
									for ( $i = 0; $i < $count_lan_avail; $i++) {
										$select_str = "";
										if ( $lan_profiles_avail[$i] == $sip_profiles )
											$select_str = "selected";
							?>
									<option value="<?php print $lan_profiles_avail[$i];?>" <?php print  $select_str;?> ><?php print $lan_profiles_avail[$i];?></option>
							<?php
									}
								}
							?> -->
								</select>
								<img id="lcr_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="lanipaddr"></span> 
							</td>
						</tr>
						<tr>
							<td valign="top">Trunks Config</td>
							<td width="20"></td>
							<td colspan="4">
							<table>
								<tr>
									<td>
										<select style="width: 160px; height: 150px;" name="trunks[]" id="trunks" multiple="multiple" size="9">
										<?php
											for ( $k = 0; $k < count($trunk_profiles); $k++ ) {
										?>
												<option value="<?php print $trunk_profiles[$k];?>"><?php print  $trunk_profiles[$k];?></option>
										<?php
											}
										?>
										</select>
									</td>
									<td valign="top">
										<img id="lcr_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
									</td>
									<td>
									<table>
										<tr><td><input type="text" maxlength="16" value="" class="ipBox" id="TRUNK_PREFIX" name="trunk_prefix" size="1">
											<img id="lcr_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
</td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="copyTrunksToSelectedList();"><span> > </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="copyAllTrunksToSelectedList();"><span> >> </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeTrunksFromSelectedList();"><span> < </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeAllTrunksFromSelectedList();"><span> << </span></a></td></tr>
									</table>
									</td>
									<td>
										<select name="trunksSel[]" style="width: 160px; height: 150px;" id="trunksSel" size="9" multiple="multiple">
									<?php
									for ( $k=0; $k < count($get_trunks); $k++) {
										if ( $get_trunks[$k] != "" ) {
									?>
											<option value="<?php print $get_trunks[$k]; ?>" <?php print "selected"; ?>> <?php print $get_trunks[$k]; ?></option>
									<?php
										}
									}
									?>
										</select>
									</td>
									<td valign="top">
										<img id="lcr_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
									<td valign="top">
										<table>
											<tr>
												<td style="padding-top: 43px;"><input class="btn-up" type="button" value="Up"></td>
												</tr>
												<tr>
													<td style="padding-top: 5px;"><input class="btn-down" type="button" value="Down"></td>
												</tr>
										</table>
									</td>
								</tr>
								<tr height="20" align="right"> <td colspan="6" aligh="center"> <span id="trunk_error"></span> </td> </tr>
							</table>
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="WHITELIST_RULE_LIST" id="WHITELIST_RULE_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
	<script type="text/javascript">
		initValidation();
		showLanIP();
	</script>
</html>
