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
	include_once("Model/roaming_users_model.php");
	include_once("Model/sip_profile_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/media_settings_model.php");
	include_once("Model/fax_profiles_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/sys_calls.php");
	include_once("Model/device_settings_model.php");
	include_once("Model/devicelist_settings_model.php");
	include_once('Model/sip_domain_profile_model.php');

        $hidden = "";
        if(SysCalls::getSysArch() == "armv7l") {
                $hidden = 'style="display:none"';
        }

	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$internalinterface=$devicelist_settings_config->getsipnamesvalues(0);
	$externalinterface=$devicelist_settings_config->getsipnamesvalues(1);
	$roaming_users_config = RoamingUsersModel::getInstance();
	$media_config = MediaConfigModel::getInstance();
	$sip_config = SIPProfileConfigModel::getInstance();
	$media_config = MediaConfigModel::getInstance();
	$fax_profiles_config = FaxProfilesConfigModel::getInstance();
	$sip_lan_profiles = $sip_config->getSIPInternalProfiles();
	$wanProfiles = $sip_config->sipWanProfiles();
	$wanProfilesCount = count($wanProfiles);
	$lanProfiles = $sip_config->sipLanProfiles();
	$lanProfilesCount = count($lanProfiles);
	$sip_domainlist = array();
	$sip_domainlist = $roaming_users_config->getfrwdsipdomain();
	$transport_str_array = array("udp","tcp","tls");
	$roamNames = $roaming_users_config->getNames();
	$fax_names = $fax_profiles_config->getNames();
	$fax_count = count($fax_names);
	$media_encryption = MediaConfigModel::getMediaEncryptOptions();
	$media_encrypt_count = count($media_encryption);
	$sipLanProfilesCount = count($sip_lan_profiles);
	$sip_wan_profiles = $sip_config->getSIPExternalProfiles();
	$sipWanProfilesCount = count($sip_wan_profiles);
	$media_profiles_config = $media_config->getMediaProfileNames();
	$sipheader_config_profiles = $roaming_users_config->getAllsipheaders();
	$k = 0;
	while ($row = $sipheader_config_profiles->fetchArray()) {
		$sipheader_profiles[$k] = $row[0];
		$k++;
	}
	$l = 0;
	while ($row = $media_profiles_config->fetchArray()) {
		$media_profiles[$l] = $row[0];
		$l++;
	}
	$sipdomainarray = $roaming_users_config->getSipnamesDomains();
	$bindport = $roaming_users_config->getAllsipdomainBindport();
	$roaming_user_sip_config = $roaming_users_config->getRoamingUserSipProfiles();
	$roaming_user_lan_config = $roaming_users_config->getRoamingUserLanProfiles();
	$k = 0;
	while ( $row = $roaming_user_sip_config->fetchArray()) {
		$roaming_user_sip_profiles[$k] = $row[0];
		$k++;
	}
	$j = 0;
	while ( $row = $roaming_user_lan_config->fetchArray()) {
		$roaming_user_lan_profiles[$j] = $row[0];
		$j++;
	}
	$res = 0;
	$page_title = "Create Roaming User Profile";
	$flag = "add";
	$name = "";
	$desc = "";
	$wan_sip_profile = "";
	$lan_sip_profile = "";
	$media_profile = "";
	$forward_reg_server_port = "";
	$protocol = "";
	$force_expire = "";
	$t38_profile = "";
	$lan_media_encrypt = "";
	$wan_media_encrypt = "";
	$ip_auth = "";
	$max_inbound = $row[RoamingUsersModel::$MAX_INBOUND];
	$max_outbound = $row[RoamingUsersModel::$MAX_OUTBOUND];
	$$presence_val = 0;
	$presence_sub = "";
	$enum_check = "0";
	$enum_type = "enum";
	$enum_suffix = "e164.arpa.";
	$enum_service = "sip";
	$presence_domain = "";
	$createdDataNames = array();
	$sbc_transport_type = SBCTransportType::getInstance();
	$transport_types = $sbc_transport_type->getSBCTransportType();
	$sbc_transport = array();
	while($tr_row = $transport_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_transport[$tr_row['VAL']] = $tr_row['NAME'];
	}
	$gettransmediaprofiles = $media_config->getTransMediaProfiles();
	$dev_settings = DeviceSettingsModel::getInstance();
	$transcodingStatus = SysCalls::getTranscodingStatus();
	$enable_transcoding = $dev_settings->get(DeviceSettingsModel::$ENABLE_TRANSCODING);
	$transcodingInterface = SysCalls::getTranscodingInterface();
	$transMediaProfile = $media_config->getTranscodingMediaProfile($transcodingInterface);

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit Roaming User Profile";
		$row = $roaming_users_config->getRoamingUser($id);
		if($row) {
			$name = $row[RoamingUsersModel::$NAME];
			$desc = $row[RoamingUsersModel::$DESCRIPTION];
			$wan_sip_profile = $row[RoamingUsersModel::$WAN_SIP_PROFILE];
			$lan_sip_profile = $row[RoamingUsersModel::$LAN_SIP_PROFILE];
			$media_profile = $row[RoamingUsersModel::$MEDIA_PROFILE];
			$forward_reg_server_port = $row[RoamingUsersModel::$FORWARD_REGISTRAR_SERVER_PORT];
			$force_expire = $row[RoamingUsersModel::$FORCE_EXPIRE];
			$t38_profile = $row[RoamingUsersModel::$T38_PROFILE];
			$lan_media_encrypt = $row[RoamingUsersModel::$MEDIA_ENCRYPT_LAN];
			$wan_media_encrypt = $row[RoamingUsersModel::$MEDIA_ENCRYPT_WAN];
			$ip_auth = $row[RoamingUsersModel::$IP_AUTH];
			$max_inbound = $row[RoamingUsersModel::$MAX_INBOUND];
			$max_outbound = $row[RoamingUsersModel::$MAX_OUTBOUND];
			$presence_val =$row[RoamingUsersModel::$PRESENCE];
			$presence_sub = $row[RoamingUsersModel::$PRESENCE_SUB];
			$get_sipheader = SipSecuritySettingsModel::unSerializeSIPMethods($row[RoamingUsersModel::$SIPHEADERS]);
			$enum_check = $row[RoamingUsersModel::$ENUM_CHECK];
			$enum_type = $row[RoamingUsersModel::$ENUM_TYPE];
			$enum_suffix = $row[RoamingUsersModel::$ENUM_SUFFIX];
			$enum_service = $row[RoamingUsersModel::$ENUM_SERVICE];
			$presence_domain = $row[RoamingUsersModel::$PRESENCE_DOMAIN];
			$dbsipdomainlist = $roaming_users_config->getSipDomainMap($name,$wan_sip_profile);
			$dbsipdomain = array();
			$dbsipmethod = array();
			$dbdesuri = array();
			$dbdesdomain = array();
			foreach($dbsipdomainlist as $key => $val) {
				$strarray = explode(",",$val);
				array_push($dbsipdomain ,$strarray[1]);
				array_push($dbsipmethod,$strarray[2]);
				$desuridomain_array = explode(";domain=",$strarray[3]);
				array_push($dbdesuri,$desuridomain_array[0]);
				array_push($dbdesdomain,$desuridomain_array[1]);
			}
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enum_check'] == "on") {
			$postenumcheck = 1;
		} else {
			$postenumcheck = 0;
		}
		if ($_POST['t38_profile'] == "--No Profile--") {
			$post_t38_profile = NULL;
		} else {
			$post_t38_profile = $_POST['t38_profile'];
		}
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			if ($_POST['wan_sip_profile'] == "--No Profile--") {
				$post_wan_sip_profile = NULL;
			} else {
				$post_wan_sip_profile = $_POST['wan_sip_profile'];
			}
			if ($_POST['lan_sip_profile'] == "--No Profile--") {
				$post_lan_sip_profile = NULL;
			} else {
				$post_lan_sip_profile = $_POST['lan_sip_profile'];
			}
			if ($_POST['media_profile'] == "--No Profile--") {
				$post_media_profile = NULL;
			} else {
				$post_media_profile = $_POST['media_profile'];
			}
			if ($_POST['ip_auth'] == on) {
				$post_ip_auth = 1;
			} else {
				$post_ip_auth = 0;
			}
			if ( $_POST['sipheaderSel'] != "") {
				$post_sipheader = SipSecuritySettingsModel::serializeSIPMethods($_POST['sipheaderSel']);
			}
			$roaming_users_config->addRoamingUser ( trim($_POST['name']),
								trim($_POST['comments']),
								$post_wan_sip_profile,
								$post_lan_sip_profile,
								$post_media_profile,
								trim($_POST['fwd_reg_server_port']),
								trim($_POST['force_expire']),
								$post_t38_profile,
								$_POST['media_encrypt_lan'],
								$_POST['media_encrypt_wan'],
								$post_ip_auth,
								trim($_POST['max_inbound']),
								trim($_POST['max_outbound']),
								$_POST['PRESENCE_HIDDEN'],
								trim($_POST['PRESENCE_SUB']),
								$post_sipheader,
								$postenumcheck,
								$_POST['enum_type'],
								$_POST['enum_suffix'],
								$_POST['enum_service'],
								($_POST['PRESENCE_DOMAIN']));
			$domainnamedesuri =explode(",",$_POST['DEST_URI_HIDDEN']);
			$sipdomains =explode(",",$_POST['EXT_DOMAIN_HIDDEN']);
			for($k=0;$k<count($domainnamedesuri) && $k<count($sipdomains);$k++) {
				$method = "All";
				$sipdomain = $sipdomains[$k];
				$desuri = $domainnamedesuri[$k];
				$roaming_users_config->addRoamingSipDomain(trim($_POST['name']),
						$post_wan_sip_profile,$sipdomain,$method,$desuri);
			}
			$roaming_users_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, 
					"Roaming User Added", DEPLOY_MODULES::SBC_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $roaming_users_config->getRoamingUser($id);
			if($_POST['enum_check'] == "on") {
				$postenumcheck = 1;
			} else {
					$postenumcheck = 0;
			}
			if ($_POST['wan_sip_profile'] == "--No Profile--") {
				$post_wan_sip_profile = NULL;
			} else {
				$post_wan_sip_profile = $_POST['wan_sip_profile'];
			}
			if ($_POST['lan_sip_profile'] == "--No Profile--") {
				$post_lan_sip_profile = NULL;
			} else {
				$post_lan_sip_profile = $_POST['lan_sip_profile'];
			}
			if ($_POST['media_profile'] == "--No Profile--") {
				$post_media_profile = NULL;
			} else {
				$post_media_profile = $_POST['media_profile'];
			}
			if ($_POST['ip_auth'] == on) {
				$post_ip_auth = 1;
			} else {
				$post_ip_auth = 0;
			}
			if ( $_POST['sipheaderSel'] != "") {
				$post_sipheader = SipSecuritySettingsModel::serializeSIPMethods($_POST['sipheaderSel']);
			}
			if($row) {
				$name = $row[RoamingUsersModel::$NAME];
				$desc = $row[RoamingUsersModel::$DESCRIPTION];
				$wan_sip_profile = $row[RoamingUsersModel::$WAN_SIP_PROFILE];
				$lan_sip_profile = $row[RoamingUsersModel::$LAN_SIP_PROFILE];
				$media_profile = $row[RoamingUsersModel::$MEDIA_PROFILE];
				$forward_reg_server_port = $row[RoamingUsersModel::$FORWARD_REGISTRAR_SERVER_PORT];
				$force_expire = $row[RoamingUsersModel::$FORCE_EXPIRE];
				$t38_profile = $row[RoamingUsersModel::$T38_PROFILE];
				$lan_media_encrypt = $row[RoamingUsersModel::$MEDIA_ENCRYPT_LAN];
				$wan_media_encrypt = $row[RoamingUsersModel::$MEDIA_ENCRYPT_WAN];
				$ip_auth = $row[RoamingUsersModel::$IP_AUTH];
				$max_inbound = $row[RoamingUsersModel::$MAX_INBOUND];
				$max_outbound = $row[RoamingUsersModel::$MAX_OUTBOUND];
				$presence_val =$row[RoamingUsersModel::$PRESENCE] ;
				$presence_sub = $row[RoamingUsersModel::$PRESENCE_SUB];
				$get_sipheader = SipSecuritySettingsModel::unSerializeSIPMethods($row[RoamingUsersModel::$SIPHEADERS]);
				$enum_check = $row[RoamingUsersModel::$ENUM_CHECK];
				$enum_type = $row[RoamingUsersModel::$ENUM_TYPE];
				$enum_suffix = $row[RoamingUsersModel::$ENUM_SUFFIX];
				$enum_service = $row[RoamingUsersModel::$ENUM_SERVICE];
				$presence_domain = $row[RoamingUsersModel::$PRESENCE_DOMAIN];
				if ( $desc != trim($_POST['comments']) || 
						$wan_sip_profile != $post_wan_sip_profile || 
						$lan_sip_profile != $post_lan_sip_profile || 
						$media_profile != $post_media_profile || 
						trim($_POST['fwd_reg_server_port']) != 
						$forward_reg_server_port || 
						$_POST['access_protcol'] != $protocol || 
						trim($_POST['force_expire']) != $force_expire || 
						$post_t38_profile != $t38_profile || 
						$_POST['media_encrypt_lan'] != $lan_media_encrypt || 
						$_POST['media_encrypt_wan']!= $wan_media_encrypt || 
						$ip_auth != $post_ip_auth || $max_inbound!= 
						trim($_POST['max_inbound']) || 
						$max_outbound!= trim($_POST['max_outbound']) || 
						$presence_val!= trim($_POST['PRESENCE_HIDDEN']) || 
						$presence_sub != trim($_POST['PRESENCE_SUB']) || 
						$post_sipheader != $get_sipheader|| $_POST['enum_check'] != 
						$enum_check || $_POST['enum_type'] != $enum_type || 
						$_POST['enum_suffix'] != $enum_suffix || 
						$_POST['enum_service'] != $enum_service || 
						$presence_domain != $PRESENCE_DOMAIN ) {
							$roaming_users_config->editRoamingUser( 
									trim($_POST['name']),
									trim($_POST['comments']),
									$post_wan_sip_profile,
									$post_lan_sip_profile,
									$post_media_profile,
									trim($_POST['fwd_reg_server_port']),
									trim($_POST['force_expire']),
									$post_t38_profile,
									$_POST['media_encrypt_lan'],
									$_POST['media_encrt_wan'],
									$post_ip_auth,
									trim($_POST['max_inbound']),
									trim($_POST['max_outbound']),
									$_POST['PRESENCE_HIDDEN'],
									trim($_POST['PRESENCE_SUB']),
									$post_sipheader,
									$postenumcheck,
									$_POST['en_type'],
									$_POST['enum_suffix'],
									$_POST['enum_service'],
									trim($_POST['PRESENCE_DOMAIN']));
							$roaming_users_config->deleteRoamingSipDomain(
								trim($_POST['name']),$wan_sip_profile);
							$domainnamedesuri =explode(",",$_POST['DEST_URI_HIDDEN']);
							$sipdomains =explode(",",$_POST['EXT_DOMAIN_HIDDEN']);
							for($k=0;$k<count($domainnamedesuri) && $k<count($sipdomains);$k++) {
								$method = "All";
								$desuri = $domainnamedesuri[$k];
								$sipdomain = $sipdomains[$k];
								$roaming_users_config->addRoamingSipDomain(trim($_POST['name']),$post_wan_sip_profile,$sipdomain,$method,$desuri);
							}
								$roaming_users_config->createActionList(
									DEPLOY_MODULES::SBC_CONFIG, 
									"Roaming User edited", 
									DEPLOY_MODULES::SBC_CONFIG_STR, 
									"");
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
			var sipprofilearray = new Array();
			var domainname = new Array();
			var sip_domainlist = new Array();
			var sipdomain_bindport = new Array();
			var sipdomainlist_bndpt = new Array();
			var sipprofile_port = new Array();
			var domaincount = 0;
			<?php
				foreach($sip_domainlist as $key => $val) { ?>
					sip_domainlist.push('<?php echo $val; ?>');
			<?php
				}
			?>
			<?php
				foreach($sipdomainarray as $key => $val) {
					$siparray = explode(";",$val);
			?>
					sipprofilearray.push('<?php echo $siparray[0]; ?>');
					domainname.push('<?php echo $siparray[1]; ?>');
					sipprofile_port.push('<?php echo $siparray[2]; ?>');
			<?php
				}
			?>
			<?php
				foreach($bindport as $key => $val) { 
					$bindport_array = explode(";",$val);
			?>
					sipdomain_bindport.push('<?php echo $bindport_array[1] ; ?>');
					sipdomainlist_bndpt.push('<?php echo $bindport_array[0] ; ?>');
			<?php
				}
			?>
			$sub(document).ready(function(){
				initValidation();
				getPageTips("roaming_users_ttip");
				setTooltip();
				presence_click();
				enable_enum();
				$sub('#wan_sip_profile').trigger('change');
			});
			var transMediaProfile = new Array();
			<?php
				foreach($gettransmediaprofiles as $key => $val) {
			?>
					transMediaProfile.push('<?php echo $val; ?>');
			<?php
				}
			?>
			$sub("#wan_sip_profile").change(function() {
				var waninterface = "<?php echo $wan_sip_profile; ?>";
				var selVal = $sub(this).val();
				$sub("#textboxDiv").html('');
				$sub("#textboxDiv").append('<tr><th align="left" width="60%">External Domains</th><th align="left">Destination domains</th><th align="left">Destination URI</th></tr>');
				var i =0;
				for (i=0;i<sipprofilearray.length;i++) {
					if(selVal == sipprofilearray[i]) {
						var domainarray = domainname[i].split(",");
						var k=0
						for(k=0;k<domainarray.length;k++){
							var z=0;
							for(z=0;z<sipdomainlist_bndpt.length;z++){
								if(domainarray[k]==sipdomainlist_bndpt[z]){
									if(sipdomain_bindport[z] == "1" ){
										domainarray[k] = domainarray[k]+ ":" +sipprofile_port[i];
									}

								}
							}
						}
						if (domainarray.length != 0){
							var j =0;
							domaincount = domainarray.length;
							var str = "";
							for (j=0;j<domainarray.length;j++) {
								var desurival = "";
								var desdomainval = "";
								if(flag == "edit") {
									<?php for($i=0;$i<count($dbsipdomain);$i++) { ?>
										if(selVal == waninterface) {
											var dburi = "<?php echo $dbdesuri[$i]; ?>";
											var desdomain = "<?php echo $dbdesdomain[$i]; ?>";
											var method = "<?php echo $dbsipmethod[$i]; ?>";
											var sipdomain = "<?php echo $dbsipdomain[$i]; ?>";
											var edit_domain = domainarray[j].split(":");
											var edit_extdomain = edit_domain[0];
											if(edit_domain.length == 1) {
												 edit_extdomain = edit_domain[0]+ ":" +sipprofile_port[i]

											}
											if((sipdomain == domainarray[j]) || (edit_extdomain == sipdomain)) {
												desurival = dburi;
												desdomainval = desdomain;
											}
										}
									<?php } ?>
								}
								str = '<tr><td align="left" valign="top" width="43%" id = "sipname'+j+'">'+domainarray[j]+'</td>';
								str += '<td align="left"><select style="width: 160px;" name="DstDomain'+j+'" id="DstDomain'+j+'" class="ipDrpBox" >';
								for(k=0;k<sip_domainlist.length;k++){
									var selval = "";
									if(desdomainval == sip_domainlist[k]) { selval = "selected='selected'"; } 
										str += '<option value="'+sip_domainlist[k]+'" '+selval+' >'+sip_domainlist[k]+'</option>';
								}
								str += '</select>';
								str += '<td align="left"><input type="text" class="ipBox" size="20" maxlength="256" name="DstURI'+j+'" id="DstURI'+j+'" value="'+desurival+'" /></td></tr>';
								$sub("#textboxDiv").append(str);
							}
						}
					}
				}
			});

			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			function initValidation() {
				$sub( document ).ready(function() {
					var wan_profile = $sub("#wan_sip_profile").val();
					var lan_profile = $sub("#lan_sip_profile").val();
					var media_profile = $sub("#media_profile_list").val();
					var t38_profile = $sub("#t38_profile").val();
					var enum_val = $sub("#enum_check").val();
					if(enum_val == "on") {
						$sub("#enum_suffix").attr("data-validation-engine", "validate[required]");
						$sub("#enum_service").attr("data-validation-engine", "validate[required]");
					}
					if ( wan_profile == "--No Profile--" || 
						lan_profile == "--No Profile--" || 
						media_profile == "--No Profile--" || 
						t38_profile == "--No Profile--") {
							$sub("#wan_sip_profile").attr( "data-validation-engine", "validate[required, custom[selectProfiles]]");
							$sub("#lan_sip_profile").attr( "data-validation-engine", "validate[required, custom[selectProfiles]]");
							$sub("#media_profile_list").attr( "data-validation-engine", "validate[required, custom[selectProfiles]]");
							$sub("#t38_profile").attr( "data-validation-engine", "validate[required, custom[selectProfiles]]");
					}
					if(domaincount != 0) {
						var i =0;
						for (i =0;i<domaincount;i++) {
							$sub("#DstURI"+i).attr( "data-validation-engine", "validate[required, custom[IP_DOMAIN_PORT]]");
						}
					}
				});
				$sub("#NAME").attr("data-validation-engine", "validate[required,custom[rulename],minSize[5], maxSize[16],LEN_FIX_1007[15]]");
				//$sub("#FWD_REG_SERVER_PORT").attr("data-validation-engine", "validate[required,custom[IP_DOMAIN_PORT]]");
				$sub("#FORCE_EXPIRE").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[3600]]");
				$sub("#MAX_INBOUND").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[10000]]");
				$sub("#MAX_OUTBOUND").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[10000]]")
				$sub("#COMMENTS").attr("data-validation-engine", "validate[custom[comments],maxSize[64]]");
				$sub("#PRESENCE_SUB").attr("data-validation-engine", "validate[required,custom[USER_AT_OR_IP_DOMAIN_PORT]]");
				$sub("#addroamingusers").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			$sub(document).ready(function(){
				$sub('input[type="button"]').click(function(){
					var $op = $sub('#sipheaderSel option:selected'),
						$this = $sub(this);
					if($op.length){
						($this.val() == 'Up') ?
							$op.first().prev().before($op) :
							$op.last().next().after($op);
					}
				});
			});

			function submitData() {
				var sipcount = 0;
				if($sub("#PRESENCE_CHK").is(":checked")) {
					$sub("#PRESENCE_HIDDEN").val("1");
				}
				var flag = '<?php print $flag ?>';
				if (flag == "add") {
					var roamArr = new Array();
					var get_roam_val = document.getElementById("NAME").value;
					var post_roam_val = get_roam_val.trim();
					<?php
						foreach($roamNames as $key=>$val) {
					?>
							roamArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var roamCount = roamArr.length;
					for (var i=0; i<roamCount; i++) {
						if (post_roam_val== roamArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub('#errorText').show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				$sub('#sipheaderSel option').attr('selected', 'selected');
				sipcount = $sub('#sipheaderSel option').length;
				if(sipcount > 10) {
					document.getElementById("errorText1").innerHTML = "Invalid! SIP Headers shouldn't exceed 10.";
					document.getElementById("errorText1").className = "alert-box errorMsg";
					$sub('#errorText').show().delay(2000).fadeOut();
					return false;
				}
				if($sub("#addroamingusers").validationEngine('validate')) {
					var formObj = document.forms[0];

					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					if(domaincount != 0){
						var i =0;
						var str ="", str2="";
						for (i =0;i<domaincount;i++) {
							if( i == 0) {
								str2 = document.getElementById("sipname"+i).innerHTML;
								str  += document.getElementById("DstURI"+i).value;
								str  += ";domain="+document.getElementById("DstDomain"+i).value;
							} else {
								str2  += "," +document.getElementById("sipname"+i).innerHTML;
								str  +=  "," +document.getElementById("DstURI"+i).value;
								str  += ";domain="+document.getElementById("DstDomain"+i).value;
							}

						}
						formObj.DEST_URI_HIDDEN.value = str;
						formObj.EXT_DOMAIN_HIDDEN.value = str2;
					}
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
					parent.document.getElementById("container").src = "roaming_users.php";
				}
			}

			function enable_enum() {
				var enableenum = document.getElementById("enum_check").checked;
				if(enableenum == true) {
					document.getElementById("enum_type").disabled = false;
					document.getElementById("enum_suffix").disabled = false;
					document.getElementById("enum_service").disabled = false;
				} else {
					document.getElementById("enum_type").disabled = true;
					document.getElementById("enum_suffix").disabled = true;
					document.getElementById("enum_service").disabled = true;
					document.getElementById("enum_type").value = "enum";
					document.getElementById("enum_suffix").value = "e164.arpa";
					document.getElementById("enum_service").value = "sip";
				}
			}

			function showLanIP() {
				var iface = document.getElementById("lan_sip_profile").value;
				if(iface != '--No Profile--') {
					var post_val = "1";
					$sub.ajax({
						type: "POST",
						cache : false,
						data : {roam_lan_ip : iface, roam_lan_post:post_val},
						url : "show_iface_ip.php",
						success : function(ipaddr) {
							document.getElementById("lanipaddr").innerHTML = ipaddr;
						}
					});
				} else {
					document.getElementById("lanipaddr").innerHTML = "";
				}
			}

			function showWanIP() {
				var iface = document.getElementById("wan_sip_profile").value;
				if(iface != '--No Profile--') {
					var post_val = "1";
					$sub.ajax({
						type: "POST",
						cache : false,
						data : {roam_wan_ip : iface, roam_wan_post:post_val},
						url : "show_iface_ip.php",
						success : function(ipaddr) {
							document.getElementById('wanipaddr').innerHTML = ipaddr;
						}
					});
				} else {
					document.getElementById('wanipaddr').innerHTML = "";
				}
			}

			function presence_click() {
				var presence_chk = document.getElementById("PRESENCE_CHK").checked;
				if( presence_chk == false ) {
					document.getElementById("PRESENCE_SUB").value = "";
					document.getElementById("PRESENCE_DOMAIN").value = "";
					document.getElementById("PRESENCE_SUB").disabled = true;
					document.getElementById("PRESENCE_DOMAIN").disabled = true;
				} else {
					document.getElementById("PRESENCE_SUB").disabled = false;
					document.getElementById("PRESENCE_DOMAIN").disabled = false;
				}
			}

			/*function showServerCerts() {
				var tranportOptions = document.getElementById("access_protcol").value;
				if  (tranportOptions == "tls") {
					document.getElementById("server_certs_opt").disabled = false;
				} else {
					document.getElementById("server_certs_opt").disabled = true;
					document.getElementById("server_certs_opt").selectedIndex = 0;
				}
			}*/

			function showMediaIP() {
				var iface = document.getElementById("media_profile_list").value;
				if(iface != '--No Profile--') {
					var post_val = "1";
					$sub.ajax({
						type: "POST",
						cache : false,
						data : {roam_media_ip : iface, roam_media_post:post_val},
						url : "show_iface_ip.php",
						success : function(ipaddr) {
							document.getElementById('mediaipaddr').innerHTML = ipaddr;
						}
					});
				} else {
					document.getElementById('mediaipaddr').innerHTML = "";
				}
				enableT38Profile();
			}
			var transStatus = '<?php print $transcodingStatus; ?>';
			var transEnabled  = '<?php print $enable_transcoding; ?>';
			var transProfile  = '<?php print $transMediaProfile; ?>';

			function enableT38Profile() {
				var selectedMediaProfile = document.getElementById("media_profile_list").value;
				var i=0;
				var settrans = 0;
				for(i=0;i<transMediaProfile.length;i++) {
					if(transMediaProfile[i] == selectedMediaProfile) {
						settrans = 1;
					}
				}
				if (settrans == 1) {
					document.getElementById("t38_profile").disabled = false;
					document.getElementById("media_encrypt_wan").disabled = false;
					document.getElementById("media_encrypt_lan").disabled = false;
				} else {
					document.getElementById("t38_profile").disabled = true;
					document.getElementById("media_encrypt_wan").disabled = true;
					document.getElementById("media_encrypt_lan").disabled = true;
				}
			}

			function copySipheaderToSelectedList() {
				var i,j,k;
				var sipheaderLst = document.getElementById('sipheader');
				var sipheaderSelLst = document.getElementById('sipheaderSel');
				var grpLstSelected = document.getElementById('sipheader').value;
				if(sipheaderLst.value == '' && sipheaderLst.length > 0) {
					alert("Please select an option to add!");
					sipheaderLst.focus();
					return false;
				} else if(sipheaderLst.length <= 0) {
					alert("There are no sipheader in the list to be added.");
					return false;
				}
				for( j=0; j< sipheaderLst.options.length; j++) {
					if(sipheaderLst[j].selected == true) {
						if(sipheaderSelLst.options.length == 0) {
							var selListTxt = sipheaderLst.options[j].text;
							var selListVal = sipheaderLst.options[j].value;
							var selGrpList = document.createElement('option');
							selGrpList.text = selListTxt;
							selGrpList.value = selListVal;
							sipheaderSelLst.add(selGrpList,sipheaderSelLst.options[null]);
							try {
								sipheaderSelLst.add(selGrpList,sipheaderSelLst.options[null]);
							}
							catch(ex) {
								sipheaderSelLst.add(selGrpList,sipheaderSelLst.options[null]);
							}
						}

						for ( k = 0; k < sipheaderSelLst.options.length; k++ ) {
							var splitCopiedArr = sipheaderSelLst.options[k].value.split("/");
							if ( sipheaderLst.options[j].value == splitCopiedArr[0] ) {
								break;
							}
							if(k == sipheaderSelLst.options.length - 1) {
								var selListTxt = sipheaderLst.options[j].text;
								var selListVal = sipheaderLst.options[j].value;
								var selGrpList = document.createElement('option');
								selGrpList.text = selListTxt;
								selGrpList.value = selListVal;
								sipheaderSelLst.add(selGrpList,sipheaderSelLst.options[null]);
								try {
									sipheaderSelLst.add(selGrpList,sipheaderSelLst.options[null]);
								}
								catch(ex) {
									sipheaderSelLst.add(selGrpList,sipheaderSelLst.options[null]);
								}
							}
						}
					}
				}
			}

			function copyAllSipheaderToSelectedList() {
				var i,j;
				var found = 0;

				var sipheaderLst =  document.getElementById('sipheader');
				var sipheaderSelLst =  document.getElementById('sipheaderSel');
				if(sipheaderLst.length <= 0) {
					alert("There are no sipheader in the list to be added.");
					return false;
				}
				removeAllSipheaderFromSelectedList();
				for ( i = 0 ; i < sipheaderLst.options.length; i++ ) {
					for ( j = 0; j < sipheaderSelLst.options.length; j++ ) {
						if ( sipheaderLst.options[i].value == sipheaderSelLst.options[j].value ) {
							found = 1;
							return false;
						}
					}
					if ( found !=  1 ) {
						var newOption = document.createElement("option");
						newOption.text = sipheaderLst.options[i].text;
						newOption.value = sipheaderLst.options[i].value;
						sipheaderSelLst.add(newOption,sipheaderSelLst.options[null]);
					}
				}
			}

			function removeSipheaderFromSelectedList() {
				var i,j;
				var sipheaderSelLst =  document.getElementById('sipheaderSel');
				var sipheaderSelLstSelcted =  document.getElementById('sipheaderSel').value;
				var GrpListLen = sipheaderSelLst.options.length;
				var grpSelSI = sipheaderSelLst.selectedIndex;
				for(j = 0 ; j < GrpListLen; j++) {
					if(grpSelSI == -1) {
						document.getElementById('sipheaderSel').focus();
						return false;
					}
				}
				if(sipheaderSelLst.options.length < 1) {
					return false;
				}
				if(sipheaderSelLstSelcted == '') {
					return false;
				}
				for (i = sipheaderSelLst.options.length - 1; i >= 0; i--) {
					if (sipheaderSelLst.options[i].selected) {
						sipheaderSelLst.remove(i);
					}
				}
				return false;
			}

			function removeAllSipheaderFromSelectedList() {
				var i;
				var sipheaderSelLst =  document.getElementById('sipheaderSel');
				if(sipheaderSelLst.options.length < 1) {
					return false;
				}
				for (i = sipheaderSelLst.options.length - 1; i >= 0; i--) {
					if (sipheaderSelLst.options[i]) {
						sipheaderSelLst.remove(i);
					}
				}
				return false;
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addroamingusers" name="addroamingusers" class="popup-form" action="add_edit_roam_users.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
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
							<!--	<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="14" maxlength="16" /> 
LEN_FIX_1007		-->
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="14" maxlength="15" />
								<img id="roaming_users_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $desc; ?>"  size="48" maxlength="64" id="COMMENTS" class="ipBox" name="comments" />
								<img id="roaming_users_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Internal SIP Profile</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="lan_sip_profile" id="lan_sip_profile" class="ipDrpBox" onchange="showLanIP();" Onkeyup="showLanIP();" Onkeydown="showLanIP();">
						<?php
							$set =1;
							if(count($internalinterface) == 0) {
								$set =1;
							} else {
								for($i=0;$i<count($internalinterface);$i++) {
									$select_str = "";
									if($flag == "edit" && ($internalinterface[$i] == $lan_sip_profile)) {
										$select_str = "selected";
										$set = 0;
						?>
									<option value="<?php echo $internalinterface[$i];?>" <?php print $select_str;?> > <?php echo $internalinterface[$i];?> </option>
						<?php
									} else {
										$check = $roaming_users_config->checkinternalprofile($internalinterface[$i]);
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
								</select>
								<img id="roaming_users_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="lanipaddr"></span> 
							</td>
						</tr>
						<tr>
							<td>External SIP Profile</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="wan_sip_profile" id="wan_sip_profile" class="ipDrpBox" onchange="showWanIP();" Onkeyup="showWanIP();" Onkeydown="showWanIP();">
						<?php
						$set =1;
						if(count($externalinterface) == 0) {
							$set =1;
 						} else {
							for($i=0;$i<count($externalinterface);$i++) {
								$select_str = "";
								if($flag == "edit" && ($externalinterface[$i] == $wan_sip_profile)) {
									$select_str = "selected";
									$set = 0;
						?>
									<option value="<?php echo $externalinterface[$i];?>" <?php print $select_str;?> > <?php echo $externalinterface[$i];?> </option>
						<?php
								} else {
									$check = $roaming_users_config->checkexternalprofile($externalinterface[$i]);
									if($check == 0) {
										$set = 0
						?>
										<option value="<?php echo $externalinterface[$i];?>"> <?php echo $externalinterface[$i];?> </option>
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
								</select>
								<img id="roaming_users_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="wanipaddr"></span> 
							</td>
						</tr>
						<tr><td/><td/><td>
						<table id="textboxDiv"> </table>
						</td>
						<td>
						<img id="roaming_users_ttip_022" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
						</tr>
						<tr>
							<td>Media Profile</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="media_profile" id="media_profile_list" class="ipDrpBox" onchange="showMediaIP()" Onkeyup="showMediaIP()" Onkeydown="showMediaIP()">
								<?php 
								if (count($media_profiles) < 1) { 
								?>
									<option value="--No Profile--">--No Profile--</option>
								<?php
								}
								for ( $j = 0; $j < count($media_profiles); $j++ ) {
									$select_str = "";
									if ( $media_profiles[$j] == $media_profile ) $select_str = "selected";
								?>
									<option value="<?php print $media_profiles[$j];?>" <?php print  $select_str;?> ><?php print  $media_profiles[$j];?></option>
								<?php
								}
								?>
								</select>
								<img id="roaming_users_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="mediaipaddr"></span> 
							</td>
						</tr>
						<tr <?php echo $hidden; ?>>
							<td>Media Encryption (LAN) </td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="media_encrypt_lan" id="media_encrypt_lan" class="ipDrpBox">
								<?php
									for ( $j = 0; $j < $media_encrypt_count; $j++ ) {
										$select_str = "";
										if ( $j == $lan_media_encrypt ) $select_str = "selected";
								?>
										<option value="<?php print $j;?>" <?php print  $select_str;?> ><?php print $media_encryption[$j];?></option>
								<?php
									}
								?>
								</select>
								<img id="roaming_users_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr <?php echo $hidden; ?>>
							<td>Media Encryption (WAN) </td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="media_encrypt_wan" id="media_encrypt_wan" class="ipDrpBox">
								<?php
									for ( $j = 0; $j < $media_encrypt_count; $j++ ) {
										$select_str = "";
										if ( $j == $wan_media_encrypt )
											$select_str = "selected";
								?>
											<option value="<?php print $j;?>" <?php print  $select_str;?> ><?php print $media_encryption[$j];?></option>
								<?php
									}
								?>
								</select>
								<img id="roaming_users_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr <?php echo $hidden; ?>>
							<td>T38 Profile</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="t38_profile" id="t38_profile" class="ipDrpBox">
									<option value="None">None</option>
									<?php
										for ( $j = 0; $j < $fax_count; $j++ ) {
											$select_str = "";
											if ( $fax_names[$j] == $t38_profile )
												$select_str = "selected";
									?>
										<option value="<?php print $fax_names[$j];?>" <?php print  $select_str;?> ><?php print  $fax_names[$j];?></option>
									<?php
										}
									?>
								</select>
								<img id="roaming_users_ttip_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<?php
							if ( $ip_auth == 1 ) {
								$ip_auth_val = "checked";
							} else {
								$ip_auth_val = "";
							}
						?>
						<tr>
							<td valign="top"><label for="ip_auth">IP Auth</label></td>
							<td width="20"></td>
							<td>
								<input type="checkbox" id="ip_auth" <?php print $ip_auth_val;?> name="ip_auth" />
								<img id="roaming_users_ttip_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr style="display:none">
							<td valign="top">Fordward registrar port</td>
							<td width="20"></td>
							<td>
								<select name="fwd_reg_server_port" id="FWD_REG_SERVER_PORT" class="ipDrpBox"> 

							<?php
								$i=0; 
								for ($i=0;$i < count($sip_domainlist);$i++) {
							?>
									<option value="<?php echo $sip_domainlist[$i];?>" <?php if($forward_reg_server_port == $sip_domainlist[$i]) echo "selected" ?>><?php echo $sip_domainlist[$i];?></option>
							<?php
								}
							?>
								</select>
								<img id="roaming_users_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<!--tr>
							<td valign="top">SIP Access Protocol</td>
							<td width="20"></td>
							<td>
								<select style="width: 60px;" class="ipDrpBox" id="access_protcol" name="access_protcol" onchange="showServerCerts();">
							<?php
							for ( $j = 0; $j < count($transport_str_array); $j++ ) {
							$select_str = "";
							if ( $transport_str_array[$j] == $protocol )
								$select_str = "selected";
							?>
									<option value="<?php print $transport_str_array[$j];?>" <?php print  $select_str;?> ><?php print  $transport_str_array[$j];?></option>
							<?php
							}
							?>
							</select>
							<img id="roaming_users_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Server Certs</td>
							<td width="20"></td>
							<td colspan="2">
								<table>
									<tr>
										<td>
											<select class="ipDrpBox" id="server_certs_opt" name="server_certs">
												<option value="None">None</option>
											<?php
												for ($i=0; $i < $serverCertCount; $i++) {
													$select_str = "";
													$certArray = explode(',',$serverCertList[$i]);
													$cert_name =  $certArray[0];
													$cert_type = $certArray[1];
													$cert_expire = $certArray[2];
													$cert_regen = $certArray[3];
													if ($cert_name != "" && $cert_type == 1 ) {
														if ($cert_name == $server_certs_val)
															$select_str = "selected";
											?>
												<option value="<?php print $cert_name;?>" <?php print  $select_str;?> ><?php print $cert_name;?></option>
											<?php
													}
												}
											?>
											</select>
											<img id="sip_profile_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
								</table>
							</td>
						</tr-->
						<tr>
							<td valign="top">Force Expire</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $force_expire; ?>" class="ipBox" id="FORCE_EXPIRE" name="force_expire" size="5" maxlength="4" />
								<img id="roaming_users_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Max Inbound</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="MAX_INBOUND" name="max_inbound" value="<?php print $max_inbound; ?>" size="5" maxlength="5" />
								<img id="roaming_users_ttip_013" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Max Outbound</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="MAX_OUTBOUND" name="max_outbound" value="<?php print $max_outbound; ?>" size="5" maxlength="5" />
								<img id="roaming_users_ttip_014" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
                                                <tr style="display: none;">

						<td valign="top"><label for="presence">Presence</label></td>
						<td width="20"></td>
						<td>
							<input <?php if($presence_val == 1) echo "checked='checked'" ?> id="PRESENCE_CHK" name="PRESENCE_CHK" type="checkbox" onclick= "presence_click()"/>
							<img id="roaming_users_ttip_015" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
						</tr>
                                                <tr style="display: none;">
						<td valign="top">Presence Config</td>
							<td width="20"></td>
							<td>
							<table>
							<tr><th align="left">Presence Domain</th><th align="left">Presence Server</th></tr>
							<tr>
							<td align="left">
								<select style="width: 160px;" name="PRESENCE_DOMAIN" id="PRESENCE_DOMAIN" class="ipDrpBox" >
								<?php 
								for($k=0;$k<count($sip_domainlist);$k++){
									$selval = "";
									if($sip_domainlist[$k] == $presence_domain) { $selval = "selected"; } ?>
										<option value="<?php echo $sip_domainlist[$k];?>" <?php echo $selval;?> ><?php echo $sip_domainlist[$k];?> </option>

								<?php
								}
								?>
								</select>
							</td>
							<td align="left">
								<input type="text" value="<?php print $presence_sub; ?>" size="20" maxlength="256" id="PRESENCE_SUB" class="ipBox" name="PRESENCE_SUB" />
								</td><td>
								<img id="roaming_users_ttip_016" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
</td> </tr>
							</table>
						</td>
						</tr>
						<tr>
							<td valign="top">Enum </td>
							<td width="20"></td>
							<?php
								if ($enum_check == "1") {
									$enum_check_checked = "checked";
								} else {
									$enum_check_checked = "";
								}
							?>
							<td>
								<input type="checkbox" <?php print $enum_check_checked; ?> name="enum_check" onclick="enable_enum();" id="enum_check" />
								<img id="roaming_users_ttip_018" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
						<tr>
							<td>Enum Type</td>
							<td width="20"></td>
							<td>
							<select style="width: 160px;" name="enum_type" id="enum_type" class="ipDrpBox" >
								<option value="enum" <?php if($enum_type == "enum"){ echo "selected";}?>>enum</option>
								<option value="isn" <?php if($enum_type == "isn"){ echo "selected";}?>>isn</option>
								<option value="isn2" <?php if($enum_type == "isn2"){ echo "selected";}?>>isn2</option>
							</select>
								<img id="roaming_users_ttip_019" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
						<tr>
							<td>Enum Suffix</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="enum_suffix" name="enum_suffix" value="<?php print $enum_suffix; ?>" size="14" maxlength="16" />
								<img id="roaming_users_ttip_020" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
						<tr>
							<td>Enum Service</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="enum_service" name="enum_service" value="<?php print $enum_service; ?>" size="14" maxlength="16" />
								<img id="roaming_users_ttip_021" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
<!-- ENUM ends-->
<!-- sip header starts -->
						<tr style="display:none">
							<td valign="top">SIP Headers</td>
							<td width="20"></td>
							<td colspan="4">
							<table>
								<tr>
									<td>
										<select style="width: 160px; height: 150px;" name="sipheader[]" id="sipheader" multiple="multiple" size="9">
										<?php
											for ( $k = 0; $k < count($sipheader_profiles); $k++ ) {
										?>
												<option value="<?php print $sipheader_profiles[$k];?>"><?php print  $sipheader_profiles[$k];?></option>
										<?php
											}
										?>
										</select>

										<img style="vertical-align: top;" id="lcr_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								<td>
									<table>
										<tr><td><a class="btn-usergrp-wide" onclick="copySipheaderToSelectedList();"><span> > </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="copyAllSipheaderToSelectedList();"><span> >> </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeSipheaderFromSelectedList();"><span> < </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeAllSipheaderFromSelectedList();"><span> << </span></a></td></tr>
									</table>
								</td>
								<td>
									<select name="sipheaderSel[]" style="width: 160px; height: 150px;" id="sipheaderSel" size="9" multiple="multiple">
									<?php
									for ( $k=0; $k < count($get_sipheader); $k++) {
										if ( $get_sipheader[$k] != "" ) {
									?>
											<option value="<?php print $get_sipheader[$k]; ?>" <?php print "selected"; ?>><?php print $get_sipheader[$k]; ?></option>
									<?php
										}
									}
									?>
									</select>
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
									</table>
								</tr>
								<tr><td height="5" ></td></tr>
								 <tr align="right"> <td colspan="3" aligh="center"> <span id="errorText1"></span> </td> 
							</tr>
<!--sip header ends-->
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="ROAMING_USERS_LIST" id="ROAMING_USERS_LIST" value="" />
					<input type="hidden" id="PRESENCE_HIDDEN" name="PRESENCE_HIDDEN" value="0" />
					<input type="hidden" id="DEST_URI_HIDDEN" name="DEST_URI_HIDDEN" value="0" />
					<input type="hidden" id="EXT_DOMAIN_HIDDEN" name="EXT_DOMAIN_HIDDEN" value="0" />
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
		showWanIP();
		showMediaIP();
	</script>
</html>
