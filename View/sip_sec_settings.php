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
	include_once('Model/sip_sec_settings_model.php');
	include_once("Control/date_config.php");
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#sip_settings";
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
			var submit = 0;
		</script>
		<?php
		$obj = DateTimeConfig::getConfig();
		$sip_settings = SipSecuritySettingsModel::getInstance();
		$result = $obj -> getDTConfig();
		$row = $result -> fetchArray();
		$enable_auto_disc = $sip_settings->get(SipSecuritySettingsModel::$ENABLE_AUTO_DISCOVERY);
		$sip_ports = $sip_settings->get(SipSecuritySettingsModel::$SIP_PORTS);
		$enable_dyn_media = $sip_settings->get(SipSecuritySettingsModel::$DYNAMIC_MEDIA_PORTS);
		$media_ports = $sip_settings->get(SipSecuritySettingsModel::$MEDIA_PORTS);
		$max_sessions = $sip_settings->get(SipSecuritySettingsModel::$MAX_SESSIONS);
		$max_dialogs = $sip_settings->get(SipSecuritySettingsModel::$MAX_DIALOGS);
		$max_uri_len = $sip_settings->get(SipSecuritySettingsModel::$MAX_URI_LEN);
		$max_callid = $sip_settings->get(SipSecuritySettingsModel::$MAX_CALLID);
		$max_req_len = $sip_settings->get(SipSecuritySettingsModel::$MAX_REQ_LEN);
		$max_from_len = $sip_settings->get(SipSecuritySettingsModel::$MAX_FROM_LEN);
		$max_to_len = $sip_settings->get(SipSecuritySettingsModel::$MAX_TO_LEN);
		$max_via_len = $sip_settings->get(SipSecuritySettingsModel::$MAX_VIA_LEN);
		$max_cntct_len = $sip_settings->get(SipSecuritySettingsModel::$MAX_CONTACT_LEN);
		$max_cont_len = $sip_settings->get(SipSecuritySettingsModel::$MAX_CONTENT_LEN);
		$sip_transport = $sip_settings->get(SipSecuritySettingsModel::$SIP_TRANSPORT);
		$media_transport = $sip_settings->get(SipSecuritySettingsModel::$MEDIA_TRANSPORT);
		$get_sip_sec_settings = array($enable_auto_disc,$sip_ports,$enable_dyn_media,
					$media_ports,$max_sessions,$max_dialogs,$max_uri_len, 
					$max_callid,$max_req_len,$max_from_len,$max_to_len,$max_via_len,
					$max_cntct_len,$max_cont_len, $sip_transport,$media_transport);
		$allow_http = $sip_settings->get(SipSecuritySettingsModel::$ALLOW_HTTP);
		$http_ports = $sip_settings->get(SipSecuritySettingsModel::$HTTP_PORTS);
		$allow_ssh = $sip_settings->get(SipSecuritySettingsModel::$ALLOW_SSH);
		$ssh_ports = $sip_settings->get(SipSecuritySettingsModel::$SSH_PORTS);
		$get_sip_device_settings = array($allow_http,$http_ports,$allow_ssh,$ssh_ports);
		$sip_methods_arr = $sip_settings->get(SipSecuritySettingsModel::$SIP_METHODS);
		$get_sip_methods = SipSecuritySettingsModel::unSerializeSIPMethods($sip_methods_arr);
		$transport_str_array = array("udp","tcp","any");
		$res = 0;
		$update_count = $obj -> getActionListCount();

		if(isset($_POST) && isset($_POST['submit_action'])) {
			if ( $_POST['sipMethodsSelected'] !=  '' ) {
				$post_sip_methods = SipSecuritySettingsModel::serializeSIPMethods($_POST['sipMethodsSelected']);
			}
			if ( trim($_POST['sip_ports']) != $sip_ports || 
				trim($_POST['media_ports']) != $media_ports || 
				trim($_POST['max_sess']) != $max_sessions|| 
				trim($_POST['max_dia_sess']) != $max_dialogs || 
				trim($_POST['max_uri_len']) != $max_uri_len || 
				trim($_POST['max_callid_len']) != $max_callid || 
				trim($_POST['max_req_len']) != $max_req_len || 
				trim($_POST['max_frm_len']) != $max_from_len || 
				trim($_POST['max_to_len']) !=  $max_to_len|| 
				trim($_POST['max_via_len']) != $max_via_len || 
				trim($_POST['max_via_len']) != $max_via_len || 
				trim($_POST['max_ctct_len']) !=  $max_cntct_len || 
				trim($_POST['max_cnt_len']) != $max_cont_len|| 
				$_POST['sip_transport'] != $sip_transport || 
				$_POST['media_transport'] != $media_transport || 
				$post_sip_methods != $sip_methods_arr ) {

					$sip_settings->set(SipSecuritySettingsModel::$SIP_PORTS,trim($_POST['sip_ports']));
					$sip_settings->set(SipSecuritySettingsModel::$MEDIA_PORTS,trim($_POST['media_ports']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_SESSIONS,trim($_POST['max_sess']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_DIALOGS,trim($_POST['max_dia_sess']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_URI_LEN,trim($_POST['max_uri_len']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_CALLID,trim($_POST['max_callid_len']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_REQ_LEN,trim($_POST['max_req_len']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_FROM_LEN,trim($_POST['max_frm_len']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_TO_LEN,trim($_POST['max_to_len']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_VIA_LEN,trim($_POST['max_via_len']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_CONTACT_LEN,trim($_POST['max_ctct_len']));
					$sip_settings->set(SipSecuritySettingsModel::$MAX_CONTENT_LEN,trim($_POST['max_cnt_len']));
					//$sip_settings->set(SipSecuritySettingsModel::$ALLOW_HTTP,$post_http);
					$sip_settings->set(SipSecuritySettingsModel::$HTTP_PORTS,trim($_POST['http_ports']));
					//$sip_settings->set(SipSecuritySettingsModel::$ALLOW_SSH,$post_ssh);
					//$sip_settings->set(SipSecuritySettingsModel::$SSH_PORTS,trim($_POST['sssh_telnet_ports']));
					$sip_settings->set(SipSecuritySettingsModel::$SIP_TRANSPORT,$_POST['sip_transport']);
					$sip_settings->set(SipSecuritySettingsModel::$MEDIA_TRANSPORT,$_POST['media_transport']);
					$sip_settings->set(SipSecuritySettingsModel::$SIP_METHODS,$post_sip_methods);
					$sip_settings->createActionList(
						DEPLOY_MODULES::SIP_SECURITY_CONFIG, 
						"SIP Settings updated", 
						DEPLOY_MODULES::SIP_SECURITY_CONFIG_STR, "");
			}
			$res = 1;
		?>
		<script type="text/javascript">
			submit = 1;
		</script>
	<?php
	}
	?>
	<script type="text/javascript">
		$sub(document).ready(function() {
			initValidation();
			getPageTips("sip_sec");
			setTooltip();
		});

		function submitData() {
			if($sub("#sipSettings").validationEngine('validate')) {
				var formObj = document.forms[0];
				$sub('#sipMethodsSelected option').attr('selected', 'selected');
				formObj.submit();
				return true;
			}
		}

		function resetData() {
			parent.document.getElementById("container").src = "sip_sec_settings.php";
		}

		function refresh_iframe(val) {
			if(val == 1) {
				parent.document.getElementById("container").src = "sip_sec_settings.php";
			}
		}

		function initValidation() {
			$sub("#MAX_SESS").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#MAX_DIA_SESS").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#MAX_URI_LEN").attr("data-validation-engine","validate[required,custom[integer],min[0],max[65535]]");
			$sub("#MAX_CALLID_LEN").attr("data-validation-engine","validate[required,custom[integer],min[0],max[65535]]");
			$sub("#MAX_REQ_LEN").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#MAX_FRM_LEN").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#MAX_TO_LEN").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#MAX_VIA_LEN").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#MAX_CTCT_LEN").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#MAX_CNT_LEN").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
			$sub("#SIP_PORTS").attr("data-validation-engine","validate[required,custom[commaSepPorts]]");
			$sub("#MEDIA_PORTS").attr("data-validation-engine","validate[required,custom[portsRange]]");
			$sub("#sipSettings").validationEngine('attach', {promptPosition : "bottomRight"});
		}

		var val = "SIP Security settings saved successfully";
		function save_Message(val) {
			parent.saveMessage(val);
		}

		function copySipMethodsToSelectedList() {
			var i,j,k;
			var sipMethodLst =  document.getElementById('sip_methods');
			var sipMethodSelLst =  document.getElementById('sipMethodsSelected');
			var grpLstSelected = document.getElementById('sip_methods').value;

			if(sipMethodLst.value == '' && sipMethodLst.length > 0) {
				alert("Please select an option to add!");
				sipMethodLst.focus();
				return false;
			} else if(sipMethodLst.length <= 0) {
				alert("There are no sipMethod in the list to be added.");
				return false;
			}

			for( j=0; j< sipMethodLst.options.length; j++) {
				if(sipMethodLst[j].selected == true) {
					if(sipMethodSelLst.options.length == 0) {
						var selListTxt = sipMethodLst.options[j].text;
						var selListVal = sipMethodLst.options[j].value;
						var selGrpList = document.createElement('option');
						selGrpList.text = selListTxt;
						selGrpList.value = selListVal;
						sipMethodSelLst.add(selGrpList,sipMethodSelLst.options[null]);
						try {
							sipMethodSelLst.add(selGrpList,sipMethodSelLst.options[null]);
						}
						catch(ex) {
							sipMethodSelLst.add(selGrpList,sipMethodSelLst.options[null]);
						}

					}
					for ( k = 0; k < sipMethodSelLst.options.length; k++ ) {
						var splitCopiedArr = sipMethodSelLst.options[k].value.split("/");
						if ( sipMethodLst.options[j].value == splitCopiedArr[0] ) {
							break;
						}
						if(k == sipMethodSelLst.options.length - 1) {
							var selListTxt = sipMethodLst.options[j].text;
							var selListVal = sipMethodLst.options[j].value;
							var selGrpList = document.createElement('option');
							selGrpList.text = selListTxt;
							selGrpList.value = selListVal;
							sipMethodSelLst.add(selGrpList,sipMethodSelLst.options[null]);
							try {
								sipMethodSelLst.add(selGrpList,sipMethodSelLst.options[null]);
							}
							catch(ex) {
								sipMethodSelLst.add(selGrpList,sipMethodSelLst.options[null]);
							}
						}
					}
				}
			}
		}

		function copyAllSipMethodsToSelectedList() {
			var i,j;
			var found = 0;
			var sipMethodLst =  document.getElementById('sip_methods');
			var sipMethodSelLst =  document.getElementById('sipMethodsSelected');
			if(sipMethodLst.length <= 0) {
				alert("There are no sipMethod in the list to be added.");
				return false;
			}
			removeAllSipMethodsFromSelectedList();
			for ( i = 0 ; i < sipMethodLst.options.length; i++ ) {
				for ( j = 0; j < sipMethodSelLst.options.length; j++ ) {
					if ( sipMethodLst.options[i].value == sipMethodSelLst.options[j].value ) {
						found = 1;
						return false;
					}
				}
				if ( found !=  1 ) {
					var newOption = document.createElement("option");
					newOption.text = sipMethodLst.options[i].text;
					newOption.value = sipMethodLst.options[i].value;
					sipMethodSelLst.add(newOption,sipMethodSelLst.options[null]);
				}
			}
		}

		function removeSipMethodsFromSelectedList() {
			var i,j;
			var sipMethodSelLst =  document.getElementById('sipMethodsSelected');
			var sipMethodSelLstSelcted =  document.getElementById('sipMethodsSelected').value;
			var GrpListLen = sipMethodSelLst.options.length;
			var grpSelSI = sipMethodSelLst.selectedIndex;
			for(j = 0 ; j < GrpListLen; j++) {
				if(grpSelSI == -1) {
					document.getElementById('sipMethodsSelected').focus();
					return false;
				}
			}
			if(sipMethodSelLst.options.length < 1) {
				return false;
			}
			if(sipMethodSelLstSelcted == '') {
				return false;
			}
			for (i = sipMethodSelLst.options.length - 1; i >= 0; i--) {
				if (sipMethodSelLst.options[i].selected) {
					sipMethodSelLst.remove(i);
				}
			}
			return false;
		}

		function removeAllSipMethodsFromSelectedList() {
			var i;
			var sipMethodSelLst =  document.getElementById('sipMethodsSelected');
			if(sipMethodSelLst.options.length < 1) {
				return false;
			}
			for (i = sipMethodSelLst.options.length - 1; i >= 0; i--) {
				if (sipMethodSelLst.options[i]) {
					sipMethodSelLst.remove(i);
				}
			}
			return false;
		}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php echo $res ?>');">
		<div class="content">
			<h1>SIP Protocol Compliance <img src="images/icn_help.png" style="margin-left: 10px; height: auto; cursor: pointer" onclick="parent.helpPage()" />
			<img src="images/warning.png" style="margin-left: 10px; height: 20px;vertical-align:middle"/>
			<span class="sip_comp">Please make sure to refer to the user manual before making changes in this configuration page.</span>
			</h1>
			<div id="datetimebody" style="overflow-y: hidden; width: 95%; overflow-x: hidden">
				<form id="sipSettings" method="post" action="sip_sec_settings.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table">
					<tr>
<!--start SIP Protocol Compliance-->
					<td class="system-settings-table-td">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<th>
								SIP Protocol Compliance Settings
								<img src="images/Icn_info_blue_bg.png" class="ttip" id="sip_sec_001" align="right" style="margin-right: 5px" />
							</th>
							<tr>
								<td>
									<table>
<!-- settings data -->
										<tr> <td>
												<table cellpadding="2" cellspacing="2" class="sub-container-table">
												<tr>
													<td> Max Sessions </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[4];?>" name="max_sess" id="MAX_SESS" maxlength="5" size="5" />
														<img id="sip_sec_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
													<td>&nbsp;</td>
													<td> Max From length </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[9];?>" name="max_frm_len" id="MAX_FRM_LEN" maxlength="5" size="5" />
														<img id="sip_sec_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
												</tr>
												<tr>
													<td> Max Dialogs per session </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[5];?>" name="max_dia_sess" id="MAX_DIA_SESS" maxlength="5" size="5" />
														<img id="sip_sec_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
													<td>&nbsp;</td>
													<td> Max To length
													</td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[10];?>" name="max_to_len" id="MAX_TO_LEN" maxlength="5" size="5" />
														<img id="sip_sec_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
												</tr>
												<tr>
													<td> Max URI length </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[6];?>" name="max_uri_len" id="MAX_URI_LEN" maxlength="5" size="5" />
														<img id="sip_sec_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
													<td>&nbsp;</td>
													<td> Max Via length </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[11];?>" name="max_via_len" id="MAX_VIA_LEN" maxlength="5" size="5" />
														<img id="sip_sec_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
												</tr>
												<tr>
													<td> Max Call ID length </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[7];?>" name="max_callid_len" id="MAX_CALLID_LEN" maxlength="5" size="5" />
														<img id="sip_sec_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
													<td>&nbsp;</td>
													<td> Max Contact length </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[12];?>" name="max_ctct_len" id="MAX_CTCT_LEN" maxlength="5" size="5" />
														<img id="sip_sec_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
												</tr>
												<tr>
													<td> Max Request name length </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[8];?>" name="max_req_len" id="MAX_REQ_LEN" maxlength="5" size="5" />
														<img id="sip_sec_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td>
													<td>&nbsp;</td>
													<td> Max Content length </td>
													<td>
														<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[13];?>" name="max_cnt_len" id="MAX_CNT_LEN" maxlength="5" size="5" />
														<img id="sip_sec_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
													</td> </tr>
												</table>
<!-- settings data -->
											</td> </tr>
									</table>
								</td> </tr>
						</table>
						</td>
<!--end SIP Protocol Compliance-->
<!--start SIP/MEDIA Ports Configuration-->
<!--<td class="system-settings-table-td">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<th>
								SIP/MEDIA Ports Configuration
								<img src="images/Icn_info_blue_bg.png" class="ttip" id="sip_sec_012" align="right" style="margin-right: 5px" />
							</th>
							<tr>
								<td>
								<table cellpadding="2" cellspacing="2" class="sub-container-table">
								<?php
									if($get_sip_sec_settings[0] == "1")
										$enable_auto = "checked";
									else
										$enable_auto = "";
								?>
									<tr>
										<td> <label for="enable_auto">Auto Discovery :</label> </td>
										<td>
											<input type="checkbox" id="enable_auto" <?php print $enable_auto; ?> name="auto_disc" />
										</td>
									</tr>
									<tr>
										<td> SIP Transport </td>
										<td>
											<select name="sip_transport">
											<?php
												for ( $j = 0; $j < count($transport_str_array); $j++ ) {
												$select_str = "";
												if ( $transport_str_array[$j] == $get_sip_sec_settings[14] )
													$select_str = "selected";
											?>
													<option value="<?php print $transport_str_array[$j];?>" <?php print  $select_str;?> ><?php print  $transport_str_array[$j];?></option>
													<?php } ?>
												</select>
												<img id="sip_sec_013" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td> SIP Ports </td>
										<td>
											<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[1]; ?>" id="SIP_PORTS" name="sip_ports" maxlength="128" size="20" />
											<img id="sip_sec_014" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
									<?php
										if($get_sip_sec_settings[2] == "1")
											$enable_media ="checked";
										else
											$enable_media ="";
									?>
										<td> <label for="disable_media">Dynamic Media Ports :</label> </td>
										<td>
											<input type="checkbox"  id="disable_media" <?php print $enable_media; ?> name="dyn_media" />
										</td>
									</tr>
									<tr>
										<td> Media Transport </td>
										<td>
											<select name="media_transport">
											<?php
												for ( $j = 0; $j < count($transport_str_array); $j++ ) {
												$select_str = "";
												if ( $transport_str_array[$j] == $get_sip_sec_settings[15] )
													$select_str = "selected";
											?>
													<option value="<?php print $transport_str_array[$j];?>" <?php print  $select_str;?> ><?php print  $transport_str_array[$j];?></option>
													<?php } ?>
											</select>
											<img id="sip_sec_015" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td> Media Ports </td>
										<td>
											<input type="text" class="ipBox" value="<?php print $get_sip_sec_settings[3];?>" name="media_ports" id="MEDIA_PORTS" maxlength="16" size="10" />
											<img id="sip_sec_016" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
								</table>
							</td> </tr>
					</table>
					<br />
				</td>-->
				<td class="system-settings-table-td">
					<table class="sub-container" cellpadding="0" cellspacing="0">
					<th
						SIP Methods
						<img src="images/Icn_info_blue_bg.png" class="ttip" id="sip_sec_001" align="right" style="margin-right: 5px" />
					</th>
						<tr> <td>
							<table style="margin-top: 6px; margin-bottom: 6px;">
							<tr> <td valign="top">
								<table>
									<tr> 
									<td valign="top">SIP Methods</td> <td>
										<?php
											$sip_methods = SipSecuritySettingsModel::getSIPmethods();
											$sipMethodsCount = count($sip_methods);
											$blockedSipMethodsCount = count($get_sip_methods);
										?>
										<select size="8" name="sip_methods[]" id="sip_methods" multiple="multiple">
										<?php
											for( $i =0; $i < $sipMethodsCount; $i++) { 
										?>
											<option value="<?php print $sip_methods[$i]; ?>"><?php print $sip_methods[$i]; ?></option>
										<?php
											}
										?>
										</select>
										<img id="sip_sec_017" style="vertical-align: top;" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
<!-- blocked -->
									<td>
									<table>
										<tr><td><a class="btn-usergrp-wide" onclick="copySipMethodsToSelectedList();"><span> > </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="copyAllSipMethodsToSelectedList();"><span> >> </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeSipMethodsFromSelectedList();"><span> < </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeAllSipMethodsFromSelectedList();"><span> << </span></a></td></tr>
									</table>
									</td>
									<td>
										<select name="sipMethodsSelected[]" style="width: 110px; height: 133px; margin-top: 3px;" id="sipMethodsSelected" size="8" multiple="multiple">
										<?php
											for( $k =0; $k < $blockedSipMethodsCount; $k++) {
												if ( $get_sip_methods[$k] != '') {
										?>
											<option  value="<?php print $get_sip_methods[$k]; ?>"><?php print $get_sip_methods[$k]; ?></option>
										<?php
												}
											}
										?>
										</select>
										<img id="sip_sec_018" style="vertical-align: top;" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
<!-- blocked -->
							</table>
						</td> </tr>
					</table>
				</td> </tr>
			</table>
		</td> </tr>
	</table>
<!--end SIP/MEDIA Ports Configuration-->
	</table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			</form>
			</div>
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="save_sip_settings" onclick="submitData();"><span>Save</span></a>
				<a class="btn-grey-wide-l" id="cancel" onclick="resetData();"><span>Cancel</span></a>
			</div>
		</div>
	</body>
	<?php
	include_once ("time_panel.php");
	$shell_obj = ShellCommand::getInstance();
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if(submit == 1) {
			save_Message(val);
		}
	</script>
</html>
