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
	include_once("Model/action_mapping_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/sys_calls.php");
	include_once('Model/events_model.php');

	$events_settings_config = EventsSettingsModel::getInstance();
	$res = 0;
	$page_title = "Create Events list Profile";
	$flag = "add";
	$fusername = "";
	$roaminguserprofile = "";
	$subscriberlist = "";
	$events="presence";
	$acpackets="application/pidf+xml";
	$expire="";
	$subscribers="";
	$get_subscribers="";
	$createdDataNames = array();
	$eventfusernames = $events_settings_config->getNames();
	$subroamuserprofile = $events_settings_config->getroamuserprofile();
	$getFromUsers = $events_settings_config->getFromUsers();
	$getRoamingUser = $events_settings_config->getRomaingUser();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = $val;
		$postid = $val;
		$flag = "edit";
		$page_title = "Edit Subscribers Profile";
		$row1 = $events_settings_config->geteventsSettings($id);
		if($row1) {
			$fusername = $row1[EventsSettingsModel::$FUSER];
			$roaminguserprofile = $row1[EventsSettingsModel::$RUSERPROFILE];
			$subscribers = $row1[EventsSettingsModel::$SUB_LIST];
			$get_subscribers = EventsSettingsModel::unSerializeSIPMethods($row1[EventsSettingsModel::$SUB_LIST]);
			$events = $row1[EventsSettingsModel::$EVENTS];
			$acpackets = $row1[EventsSettingsModel::$AC_PKT];
			$expire = $row1[EventsSettingsModel::$EXPIRE];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ( $_POST['subscribersSel'] != "") {
			$post_subscribers = EventsSettingsModel::serializeSIPMethods($_POST['subscribersSel']);
		}
		if ($_POST['submit_action'] == "1") {
			$events_settings_config->editeventsSettings ($_POST['editid'], 
							trim($_POST['fusername']), 
							trim($_POST['roaminguserprofile']), 
							trim($post_subscribers), 
							trim($_POST['events1']), 
							trim($_POST['acpackets']), 
							trim($_POST['expire']));
			$events_settings_config->createActionList(
							DEPLOY_MODULES::EVENTS_SETTINGS_CONFIG, 
							"Events Profile edited", 
							DEPLOY_MODULES::EVENTS_SETTINGS_CONFIG_STR, "");
			$res = 1;
		} 
		if ($_POST['submit_action'] == "2") {
			$events_settings_config->addeventsSettings (trim($_POST['fusername']), 
							trim($_POST['roaminguserprofile']), 
							trim($post_subscribers), 
							trim($_POST['events1']), 
							trim($_POST['acpackets']), 
							trim($_POST['expire']));
			$events_settings_config->createActionList(
							DEPLOY_MODULES::EVENTS_SETTINGS_CONFIG, 
							"Events Profile Added", 
							DEPLOY_MODULES::EVENTS_SETTINGS_CONFIG_STR, 
							"");
			$res = 1;
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
		<link rel="stylesheet" type="text/css" href="css/tabs.css" media="screen" />
		<script src="js/jquery.validationEngine-en.js"></script>
		<script src="js/jquery.validationEngine.js"></script>
		<script src="js/jquery.tooltip.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script type="text/javascript">
			var flag = "<?php echo $flag ?>";
			var roamuserArr = new Array();	
			var checkroamuserArr = new Array();
			var subscriberlist = new Array();
			var roamFromUserArr = new Array();

			<?php
				$i = 0;
				for($i = 0; $i < count($getRoamingUser); $i++) {
			?>
					roamFromUserArr.push('<?php echo $getRoamingUser[$i]; ?>');
			<?php
				}
			?>

			var editroamingval = '<?php echo $roaminguserprofile; ?>';
			<?php
				$i = 0;
				for($i = 0; $i < count($subroamuserprofile); $i++) {
			?>
					roamuserArr.push('<?php echo $subroamuserprofile[$i]; ?>');
			<?php
				}
			?>
			var countroamuser = roamuserArr.length;
			<?php
				$i = 0;
				for($i = 0; $i < count($eventfusernames); $i++) {
			?>
					checkroamuserArr.push('<?php echo $eventfusernames[$i]; ?>');
			<?php
				}
			?>
			var countcheckroamuserArr = checkroamuserArr.length; 
			if(countroamuser == 0) {
				var eleObj = document.getElementById("roaminguserprofile");
				var option = document.createElement("option");
				option.text = "--No Profile--";
				option.value = "NONE";
				eleObj.add(option);
			} else {
				for(i=0;i<countroamuser;i++) {
					var editval = '<?php echo $roaminguserprofile; ?>';
					if((flag == "edit") && (roamuserArr[i] == editval )) {
						var eleObj = document.getElementById("roaminguserprofile");
						var option = document.createElement("option");
						option.text = roamuserArr[i];
						option.value = roamuserArr[i];
						eleObj.add(option,0);
						eleObj.options[0].selected = true;
					} else {
						var eleObj = document.getElementById("roaminguserprofile");
						if(eleObj != null) {
							var option = document.createElement("option");
							option.text = roamuserArr[i];
							option.value = roamuserArr[i];
							eleObj.add(option);
						}

					}
				}
			}

			$sub(document).ready(function() {
				initValidation();
				getPageTips("events_settings_ttip");
				setTooltip();
				setUserName();
			});

			function initValidation() {
				var subscribers_val;
				$sub('#subscribers_id option').each(function() {
					subscribers_val = $sub(this).val();
				});
				$sub("#fusername").attr("data-validation-engine","validate[required,custom[onlyLetterNumberDotHyphenUnderScore],maxSize[64]]");
				$sub("#events1").attr("data-validation-engine","validate[required]");
				$sub("#acpackets").attr("data-validation-engine","validate[required]");
				$sub("#expire").attr("data-validation-engine","validate[required,custom[integer],min[300],max[36000]]")
				$sub("#addeventssettings").validationEngine('attach', {promptPosition : "bottomRight"});
				$sub(document).ready(function(){
					$sub('input[type="button"]').click(function(){
						var $op = $sub('#subscribersSel option:selected'),
							$this = $sub(this);
						if($op.length){
							($this.val() == 'Up') ?
								$op.first().prev().before($op) :
								$op.last().next().after($op);
						}
					});
				});
			}

			function setUserName() {
				var formObj = document.forms[0];
				var fromuser = formObj.fusername.value;
				var i = 0;
				if(fromuser != "NONE") {
					for(i=0;i<roamFromUserArr.length;i++) {
						var roamUser = roamFromUserArr[i].split("|");
						if( roamUser[0] == fromuser) {
							document.getElementById("roaminguserprofile").value = roamUser[1];
							document.getElementById("roaminguserprofile").disabled = true;
							setroamuser();
						}
					}	
				} else {
					document.getElementById("roaminguserprofile").disabled = true;
					document.getElementById("errorText").innerHTML = "Invalid! No Subscriber Profile.";
					document.getElementById("errorText").className = "alert-box errorMsg"
					$sub("#errorText").show().delay(2000).fadeOut();
					return false;
				}
			}

			function setroamuser() {
				var formObj = document.forms[0];
				var roamval = formObj.roaminguserprofile.value;
				var post_val = "1";
				$sub.ajax({
					type: "POST",
					cache : false,
					data : {lcr_lan_ip : roamval, lcr_lan_post:post_val},
					url : "presence_event.php",
					success : function(sublist) {
						subcriberlists(sublist);
					}
				});
			}

			function subcriberlists(sublist) {
				subscriberlist = new Array();
				var len =0 ;
				var roamval1 = document.getElementById("roaminguserprofile").value;
				if(sublist != "") {
					subscriberlist = sublist.split(',');
				}
				len = subscriberlist.length;
				document.getElementById('subscribers_id').innerHTML = "";
				if( flag == "add") {
					document.getElementById('subscribersSel').innerHTML = "";
				}
				if(flag == "edit" && editroamingval != roamval1) {
					document.getElementById('subscribersSel').innerHTML = "";
				}
				for(i=0;i<len-1;i++) {
					var sub_listbox = document.getElementById('subscribers_id');
					var option = document.createElement('option');
					option.text = subscriberlist[i];
					option.value = subscriberlist[i];
					sub_listbox.add(option);
				}
			}

			function submitData() {
				var flag = "<?php echo $flag ?>";
					if ( flag == "add") {
						var mediaArr = new Array();
						var getfusername = document.getElementById("fusername").value;
						for ( var i=0; i < countcheckroamuserArr; i++) {
							if (getfusername == checkroamuserArr[i]) {
								document.getElementById("errorText").innerHTML = "Invalid! User Name  is already in use.";
								document.getElementById("errorText").className = "alert-box errorMsg"
								$sub("#errorText").show().delay(2000).fadeOut();
								return false;
							}
						}
						if(getfusername == "NONE") {

							document.getElementById("errorText").innerHTML = "Invalid! No  Subscriber Profile.";
							document.getElementById("errorText").className = "alert-box errorMsg"
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
					if(flag == "add" || flag == "edit") {
						var formObj = document.forms[0];
						var val = formObj.roaminguserprofile.value;
						if(val == "NONE") {
							document.getElementById("errorText1").innerHTML = "Invalid! NO Roaming Profile.";
							document.getElementById("errorText1").className = "alert-box errorMsg"
							$sub("#errorText1").show().delay(2000).fadeOut();
							return false;
						}
					}
					if(flag == "add" || flag == "edit") {
						var formObj = document.forms[0];
						var val = formObj.subscribersSel.options.length;
						if(val == 0) {
							document.getElementById("errorText2").innerHTML = "Invalid! NO User Profile Selected.";
							document.getElementById("errorText2").className = "alert-box errorMsg"
							$sub("#errorText2").show().delay(2000).fadeOut();
							return false;
						}
					}
					if($sub("#addeventssettings").validationEngine('validate')) {
						var formObj = document.forms[0];
						if(flag == "add") {
							formObj.submit_action.value = "2";
						} else {
							formObj.submit_action.value = "1";
						}
						$sub('#subscribersSel option').attr('selected', 'selected');

						document.getElementById("fusername").disabled = false;
						document.getElementById("roaminguserprofile").disabled = false;
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
					parent.document.getElementById("container").src = "events_settings.php";
				}
			}

			function copysubscribersToSelectedList() {
				var i,j,k;
				var subscribersLst =  document.getElementById('subscribers_id');
				var subscribersSelLst =  document.getElementById('subscribersSel');
				var grpLstSelected = document.getElementById('subscribers_id').value;
				
				if(subscribersLst.value == '' && subscribersLst.length > 0) {
					alert("Please select an option to add!");
					subscribersLst.focus();
					return false;
				} else if(subscribersLst.length <= 0) {
					alert("There are no subscribers in the list to be added.");
					return false;
				}
				for( j=0; j< subscribersLst.options.length; j++) {
					if(subscribersLst[j].selected == true) {
						if(subscribersSelLst.options.length == 0) {
							var selListTxt = subscribersLst.options[j].text;
							var selListVal = subscribersLst.options[j].value;
							var selGrpList = document.createElement('option');
							selGrpList.text = selListTxt;;
							selGrpList.value = selListVal;;
							subscribersSelLst.add(selGrpList,subscribersSelLst.options[null]);
							try {
								subscribersSelLst.add(selGrpList,subscribersSelLst.options[null]);
							}
							catch(ex) {
								subscribersSelLst.add(selGrpList,subscribersSelLst.options[null]);
							}

						}

						for ( k = 0; k < subscribersSelLst.options.length; k++ ) {
							var splitCopiedArr = subscribersSelLst.options[k].value.split("/");
							if ( subscribersLst.options[j].value == splitCopiedArr[0] ) {
								break;
							}
							if(k == subscribersSelLst.options.length - 1) {
								var selListTxt = subscribersLst.options[j].text;
								var selListVal = subscribersLst.options[j].value;
								var selGrpList = document.createElement('option');
								selGrpList.text = selListTxt;;
								selGrpList.value = selListVal;;
								subscribersSelLst.add(selGrpList,subscribersSelLst.options[null]);
								try {
									subscribersSelLst.add(selGrpList,subscribersSelLst.options[null]);
								}
								catch(ex) {
									subscribersSelLst.add(selGrpList,subscribersSelLst.options[null]);
								}
							}
						}
					}
				}
			}

			function copyAllsubscribersToSelectedList() {
				var i,j;
				var found = 0;
				var subscribersLst =  document.getElementById('subscribers_id');
				var subscribersSelLst =  document.getElementById('subscribersSel');
				if(subscribersLst.length <= 0) {
					alert("There are no subscribers in the list to be added.");
					return false;
				}
				removeAllsubscribersFromSelectedList();
				for ( i = 0 ; i < subscribersLst.options.length; i++ ) {
					for ( j = 0; j < subscribersSelLst.options.length; j++ ) {
						if ( subscribersLst.options[i].value == subscribersSelLst.options[j].value ) {
							found = 1;
							return false;
						}
					}
					if ( found != 1 ) {
						var newOption = document.createElement("option");
						newOption.text = subscribersLst.options[i].text;
						newOption.value = subscribersLst.options[i].value;
						subscribersSelLst.add(newOption,subscribersSelLst.options[null]);
					}
				}
			}

			function removesubscriberFromSelectedList() {
				var i,j;
				var subscribersSelLst =  document.getElementById('subscribersSel');
				var subscribersSelLstSelcted =  document.getElementById('subscribersSel').value;
				var GrpListLen = subscribersSelLst.options.length;
				var grpSelSI = subscribersSelLst.selectedIndex;
				for(j = 0 ; j < GrpListLen; j++) {
					if(grpSelSI == -1) {
						document.getElementById('subscribersSel').focus();
						return false;
					}
				}
				if(subscribersSelLst.options.length < 1) {
					return false;
				}
				if(subscribersSelLstSelcted == '') {
					return false;
				}
				for (i = subscribersSelLst.options.length - 1; i >= 0; i--) {
					if (subscribersSelLst.options[i].selected) {
						subscribersSelLst.remove(i);
					}
				}
				return false;
			}

			function removeAllsubscribersFromSelectedList() {
				var i;
				var subscribersSelLst =  document.getElementById('subscribersSel');
				if(subscribersSelLst.options.length < 1) {
					return false;
				}
				for (i = subscribersSelLst.options.length - 1; i >= 0; i--) {
					if (subscribersSelLst.options[i]) {
						subscribersSelLst.remove(i);
					}
				}
				return false;
			}
	</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 285px">
				<form id="addeventssettings" name="addeventssettings" class="popup-form" action="add_edit_events.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
				<div id="tabdevicelist" class="tab_content">
					<table>
						<tr>
							<td>From Username</td>
							<td width="20"></td>
							<td>
								<select name="fusername" id="fusername" class="ipDrpBox" <?php if($flag == "edit") echo "disabled='disabled'" ?> onchange="setUserName()" onkeyup="setUserName()" onkeydown="setUserName()">
								<?php
									if(!empty($getFromUsers)) {
										foreach ($getFromUsers as $key => $val) { ?>
											<option value="<?php echo $val; ?>" <?php if($fusername == $val) echo "selected" ?> > <?php echo $val; ?> </option>
								<?php		}
									} else { ?>
										<option value="NONE">--No Profile--</option>
								<?php	}
								?>
								</select>
								<img id="events_settings_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Roaming User Profile</td>
							<td width="20"></td>
							<td>
								<select name="roaminguserprofile" id="roaminguserprofile" class="ipDrpBox" onchange="setroamuser()" onkeyup="setroamuser()" onkeydown="setroamuser()">
								</select>
								<img id="events_settings_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText1"></span>
							</td>
						</tr>
						<tr>
							<td valign="center">Subscribers List</td>
							<td width="20"></td>
							<td colspan="4">
								<table>
									<tr>
										<td>
											<select size="5" style="width: 160px; font-size: 12px" name="subscribers[]" id="subscribers_id" multiple="multiple">
											</select>
										</td>
										<td valign="middle">
											<img id="events_settings_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										<td>
											<table>
												<tr>
							<!--			<td><input type="text" maxlength="16" value="" class="ipBox" id="TRUNK_PREFIX" name="trunk_prefix" size="5">
								<img id="lcr_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
</td> --> </tr>
												<tr><td><a class="btn-usergrp-wide" onclick="copysubscribersToSelectedList();"><span> > </span></a></td></tr>
												<tr><td><a class="btn-usergrp-wide" onclick="copyAllsubscribersToSelectedList();"><span> >> </span></a></td></tr>
												<tr><td><a class="btn-usergrp-wide" onclick="removesubscriberFromSelectedList();"><span> < </span></a></td></tr>
												<tr><td><a class="btn-usergrp-wide" onclick="removeAllsubscribersFromSelectedList();"><span> << </span></a></td></tr>
											</table>
										</td>
										<td>
											<select name="subscribersSel[]" size="5" style="width: 160px; font-size: 12px" id="subscribersSel" multiple="multiple">
											<?php
												for ( $k=0; $k < count($get_subscribers); $k++) {
													if ( $get_subscribers[$k] != "" ) {
											?>
													<option value="<?php print $get_subscribers[$k]; ?>" <?php print "selected"; ?>><?php print $get_subscribers[$k]; ?></option>
											<?php
													}
												}
											?>
											</select>
										</td>
										<td valign="middle">
											<img id="events_settings_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										<td valign="top" style="display:none;">
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
								</table>
							</td>
						</tr>
<!-------------SUBSCRIBER LIST END--------->
						<tr>
						<tr>
							<td width = "30"></td>
							<td></td>
							<td width = "30"> <span id="errorText2"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Events</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $events; ?>"  size="10" maxlength="32" id="events1" class="ipBox" name="events1" />
								<img id="events_settings_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">AC Packets</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $acpackets; ?>"  size="10" maxlength="32" id="acpackets" class="ipBox" name="acpackets" />
								<img id="events_settings_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Expire</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $expire; ?>"  size="10" maxlength="15" id="expire" class="ipBox" name="expire" />
								<img id="events_settings_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<input type="hidden" name="submit_action"  id="submit_action" value="0" />
				<input type="hidden" name="editid"  id="editid" value="<?php echo $id;?>" />
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
	</script>
</html>
