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
	include_once("Model/action_mapping_model.php");
	include_once("Model/global_settings_model.php");
	include_once("Model/sip_profile_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/media_settings_model.php");
	include_once("Model/fax_profiles_model.php");
	include_once("Model/sys_calls.php");
	include_once("Model/device_settings_model.php");
	include_once("Model/devicelist_settings_model.php");
	include_once("Model/roaming_users_model.php");

        $hidden = "";
        if(SysCalls::getSysArch() == "armv7l") {
                $hidden = 'style="display:none"';
        }


	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$internalinterface=$devicelist_settings_config->getsipnamesvalues(0);
	$externalinterface=$devicelist_settings_config->getsipnamesvalues(1);
	$roaming_users_config = RoamingUsersModel::getInstance();
	$trunk_config = TrunkConfigModel::getInstance();
	$sip_config = SIPProfileConfigModel::getInstance();
	$media_config = MediaConfigModel::getInstance();
	$fax_profiles_config = FaxProfilesConfigModel::getInstance();
	$transport_str_array = array("udp","tcp");
	$sip_lan_profiles = $sip_config->getSIPInternalProfiles();
	$wanProfiles = $sip_config->sipWanProfiles();
	$wanProfilesCount = count($wanProfiles);
	$lanProfiles = $sip_config->sipLanProfiles();
	$lanProfilesCount = count($lanProfiles);
	$sipLanProfilesCount = count($sip_lan_profiles);
	$sip_wan_profiles = $sip_config->getSIPExternalProfiles();
	$sipWanProfilesCount = count($sip_wan_profiles);
	$media_profiles_config = $media_config->getMediaProfileNames();
	$sipheader_config_profiles = $trunk_config->getAllsipheaders();
	$k = 0;
	while ($row = $sipheader_config_profiles->fetchArray()) {
		$trunk_profiles[$k] = $row[0];
		$k++;
	}
	$l = 0;
	while ($row = $media_profiles_config->fetchArray()) {
		$media_profiles[$l] = $row[0];
		$l++;
	}
	$trunk_lan_config = $trunk_config->getTrunkSipLanProfiles();
	$a = 0;
	while ($row = $trunk_lan_config->fetchArray()) {
		$trunk_lan_profiles[$a] = $row[0];
		$a++;
	}
	$trunk_wan_profiles = $trunk_config->getTrunkSipWanProfiles();
	$sbc_transport_type = SBCTransportType::getInstance();
	$transport_types = $sbc_transport_type->getSBCTransportType();
	$sbc_transport = array();
	while($tr_row = $transport_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_transport[$tr_row['VAL']] = $tr_row['NAME'];
	}
	$fax_names = $fax_profiles_config->getNames();
	$fax_count = count($fax_names);
	$sipdomainlist = array();
	$sipdomainlist = $roaming_users_config->getfrwdsipdomain();
	$media_encryption = MediaConfigModel::getMediaEncryptOptions();
	$media_encrypt_count = count($media_encryption);
	$res = 0;
	$page_title = "Create Trunk Configuration";
	$flag = "add";
	$name = "";
	$desc= "";
	$domain_ip_uri = "";
	$username = "";
	$authuser = "";
	$passwd = "";
	$sip_registrar = "";
	$registrar_expire = "";
	$ob_caller_id = "";
	$ob_proxy_uri = "";
	$user_agent = "";
	$internal_sip_profile = "";
	$external_sip_profile = "";
	$media_profile = "";
	$add_prefix = "";
	$strip_digits = "";
	$allow_inbound = "0";
	$inbound_uri = "";
	$max_inbound = "";
	$allow_outbound = "1";
	$enum_check = "0";
	$enum_type = "enum";
	$enum_suffix = "e164.arpa.";
	$enum_service = "sip";	
	$max_outbound = "";
	$sip_domain = "";
	$t38_profile = "";
	$lan_media_encrypt = "";
	$wan_media_encrypt = "";
	$get_sipheader = "";
	$gettransmediaprofiles = $media_config->getTransMediaProfiles();
	$dev_settings = DeviceSettingsModel::getInstance();
	$transcodingStatus = SysCalls::getTranscodingStatus();
	$enable_transcoding = $dev_settings->get(DeviceSettingsModel::$ENABLE_TRANSCODING);
	$transcodingInterface = SysCalls::getTranscodingInterface();
	$transMediaProfile = $media_config->getTranscodingMediaProfile($transcodingInterface);
	$createdDataNames = array();
	$trunkNames = $trunk_config->getNames();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit Trunk Configuration";
		$row = $trunk_config->getTrunkConfigRule($id);
		if($row) {
			$name = $row[TrunkConfigModel::$NAME];
			$desc= $row[TrunkConfigModel::$DESCRIPTION];
			$domain_ip_uri = $row[TrunkConfigModel::$DOMAIN_IP_URI];
			$username = $row[TrunkConfigModel::$USERNAME];
			$authuser = $row[TrunkConfigModel::$AUTHUSER];
			//if($username == "") {
			//		$username = $row[TrunkConfigModel::$DOMAIN_IP_URI];
			//}
			$passwd = $row[TrunkConfigModel::$PASSWORD];
			$sip_registrar = $row[TrunkConfigModel::$SIP_REGISTRAR];
			$registrar_expire = $row[TrunkConfigModel::$REGISTRAR_EXPIRE];
			$ob_caller_id = $row[TrunkConfigModel::$OUTBOUND_CALLER_ID];
			$ob_caller_id_arr = explode(";",$ob_caller_id);
			$ob_caller_id_name_prefix = "" ;
			$ob_caller_id_name = "" ;
			$ob_caller_id_num_prefix  = "";
			$ob_caller_id_num = "";
			foreach($ob_caller_id_arr as $ob_param) {
				if(preg_match('/CNAP=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
					$ob_caller_id_name_prefix = $match[1][0] ;
				}
				else if(preg_match('/CNA=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
					$ob_caller_id_name        = $match[1][0] ;
				}
				else if(preg_match('/CNUP=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
					$ob_caller_id_num_prefix  = $match[1][0] ;
				}
				else if(preg_match('/CNU=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
					$ob_caller_id_num         = $match[1][0] ;
				}
			}
			$ob_proxy_uri = $row[TrunkConfigModel::$OUTBOUND_PROXY_URI];
			$user_agent = $row[TrunkConfigModel::$USERAGENT];
			$internal_sip_profile = $row[TrunkConfigModel::$INTERNAL_SIP_PROFILE];
			$external_sip_profile = $row[TrunkConfigModel::$EXTERNAL_SIP_PROFILE];
			$media_profile = $row[TrunkConfigModel::$MEDIA_PROFILE];
			$add_prefix = $row[TrunkConfigModel::$ADD_PREFIX];
			$strip_digits = $row[TrunkConfigModel::$STRIP_DIGITS];
			$allow_inbound = $row[TrunkConfigModel::$ALLOW_INBOUND];
			$inbound_uri = $row[TrunkConfigModel::$INBOUND_URI];
			$max_inbound = $row[TrunkConfigModel::$MAX_INBOUND];
			$allow_outbound = $row[TrunkConfigModel::$ALLOW_OUTBOUND];
			$enum_check = $row[TrunkConfigModel::$ENUM_CHECK];
			$enum_type = $row[TrunkConfigModel::$ENUM_TYPE];
			$enum_suffix = $row[TrunkConfigModel::$ENUM_SUFFIX];
			$enum_service = $row[TrunkConfigModel::$ENUM_SERVICE];
			$max_outbound = $row[TrunkConfigModel::$MAX_OUTBOUND];
			$sip_domain = $row[TrunkConfigModel::$SIP_DOMAIN];
			$t38_profile = $row[TrunkConfigModel::$T38_PROFILE];
			$lan_media_encrypt = $row[TrunkConfigModel::$MEDIA_ENCRYPT_LAN];
			$wan_media_encrypt = $row[TrunkConfigModel::$MEDIA_ENCRYPT_WAN];
			$get_sipheader = SipSecuritySettingsModel::unSerializeSIPMethods($row[TrunkConfigModel::$SIPHEADERS]);
		}
	}

	if ( $_POST['internal_sip_profile'] == "--No Profile--" ) {
		$post_internal_sip_prof = NULL;
	} else {
		$post_internal_sip_prof = $_POST['internal_sip_profile'];
	}

	if ( $_POST['external_sip_profile'] == "--No Profile--" ) {
		$post_external_sip_prof = NULL;
	} else {
		$post_external_sip_prof = $_POST['external_sip_profile'];
	}

	if ( $_POST['media_profile'] == "--No Profile--" ) {
		$post_media_profile = NULL;
	} else {
		$post_media_profile = $_POST['media_profile'];
	}

	if ( $_POST['t38_profile'] == "--No Profile--" ) {
		$post_t38_profile = NULL;
	} else {
		$post_t38_profile = $_POST['t38_profile'];
	}

	if($_POST['allow_inbound'] == "on") {
		$post_allow_inbound = "1";
	} else {
		$post_allow_inbound = "";
	}

	if($_POST['allow_outbound'] == "on") {
		$post_allow_outbound = "1";
	} else {
		$post_allow_outbound = "";
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enum_check'] == "on") {
				$postenumcheck = 1;
		} else {
				$postenumcheck = 0;
		}
		if ( $_POST['t38_profile'] == "--No Profile--" ) {
			$post_t38_profile = NULL;
		} else {
			$post_t38_profile = $_POST['t38_profile'];
		}
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			if($_POST['allow_inbound'] == "on") {
				$post_allow_inbound = "1";
				$post_max_ibound = trim($_POST['max_inbound']);
			} else {
				$post_allow_inbound = "";
				$post_max_ibound = "0";
			}
			if($_POST['allow_outbound'] == "on") {
				$post_allow_outbound = "1";
				$post_max_obound = trim($_POST['max_outbound']);
			} else {
				$post_allow_outbound = "";
				$post_max_obound = "0";
			}
			$sip_reg_val = explode(":",trim($_POST['sip_registrar']));
			if ($sip_reg_val[1] == "" && $sip_reg_val[0] != "") {
				$sip_reg_val = trim($_POST['sip_registrar']).":"."5060";
			} else {
				$sip_reg_val = trim($_POST['sip_registrar']);
			}
			if ( $_POST['sipheaderSel'] != "") {
				$post_sipheader = SipSecuritySettingsModel::serializeSIPMethods($_POST['sipheaderSel']);
			}
			$trunk_config->addTrunkConfigRule(trim($_POST['name']),
							trim($_POST['trunk_desc']),
							trim($_POST['domain_ip_uri']),
							trim($_POST['user']),
							trim($_POST['passwd']),
							$sip_reg_val,
							trim($_POST['registrar_expire']),
							trim($_POST['outbound_caller_id']),
							trim($_POST['outbound_proxy_uri']),
							trim($_POST['user_agent']),
							$post_internal_sip_prof,
							$post_external_sip_prof,
							$post_media_profile,
							trim($_POST['add_prefix']),
							trim($_POST['strip_digits']),
							$post_allow_inbound,
							trim($_POST['INBOUND_URI_HIDDEN']),
							$post_max_ibound,
							$post_allow_outbound,
							$post_max_obound,
							trim($_POST['sip_domain']),
							$post_t38_profile,
							$_POST['media_encrypt_lan'],
							$_POST['media_encrypt_wan'],
							$post_sipheader,
							$postenumcheck,
							$_POST['enum_type'],
							$_POST['enum_suffix'],
							$_POST['enum_service'],
							trim($_POST['authuser']));

			$trunk_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, 
							"Trunk Config rule added", 
							DEPLOY_MODULES::SBC_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $trunk_config->getTrunkConfigRule($id);
			if($_POST['enum_check'] == "on") {
				$postenumcheck = 1;
			} else {
				$postenumcheck = 0;
			}
			if ( $_POST['internal_sip_profile'] == "--No Profile--" ) {
				$post_internal_sip_prof = NULL;
			} else {
				$post_internal_sip_prof = $_POST['internal_sip_profile'];
			}
			if ( $_POST['external_sip_profile'] == "--No Profile--" ) {
				$post_external_sip_prof = NULL;
			} else {
				$post_external_sip_prof = $_POST['external_sip_profile'];
			}
			if ( $_POST['media_profile'] == "--No Profile--" ) {
				$post_media_profile = NULL;
			} else {
				$post_media_profile = $_POST['media_profile'];
			}
			if($_POST['allow_inbound'] == "on") {
				$post_allow_inbound = "1";
				$post_max_ibound = trim($_POST['max_inbound']);
			} else {
				$post_allow_inbound = "";
				$post_max_ibound = "0";
			}
			if($_POST['allow_outbound'] == "on") {
				$post_allow_outbound = "1";
				$post_max_obound = trim($_POST['max_outbound']);
			} else {
				$post_allow_outbound = "";
				$post_max_obound = "0";
			}
			$sip_reg_val = explode(":",trim($_POST['sip_registrar']));
			if ($sip_reg_val[1] == "" && $sip_reg_val[0] != "" ) {
				$sip_reg_val = trim($_POST['sip_registrar']).":"."5060";
			} else {
				$sip_reg_val = trim($_POST['sip_registrar']);
			}
			if ( $_POST['sipheaderSel'] != "") {
				$post_sipheader = SipSecuritySettingsModel::serializeSIPMethods($_POST['sipheaderSel']);
			}
			if($row) {
				$name = $row[TrunkConfigModel::$NAME];
				$desc= $row[TrunkConfigModel::$DESCRIPTION];
				$domain_ip_uri = $row[TrunkConfigModel::$DOMAIN_IP_URI];
				$username = $row[TrunkConfigModel::$USERNAME];
				$authuser = $row[TrunkConfigModel::$AUTHUSER];
				$passwd = $row[TrunkConfigModel::$PASSWORD];
				$sip_registrar = $row[TrunkConfigModel::$SIP_REGISTRAR];
				$registrar_expire = $row[TrunkConfigModel::$REGISTRAR_EXPIRE];
				$ob_caller_id = $row[TrunkConfigModel::$OUTBOUND_CALLER_ID];
				$ob_caller_id_arr = explode(";",$ob_caller_id);
				$ob_caller_id_name_prefix = "" ;
				$ob_caller_id_name = "" ;
				$ob_caller_id_num_prefix  = "";
				$ob_caller_id_num = "";
				foreach($ob_caller_id_arr as $ob_param) {
					if(preg_match('/CNAP=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
						$ob_caller_id_name_prefix = $match[1][0] ;
					}
					else if(preg_match('/CNA=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
						$ob_caller_id_name        = $match[1][0] ;
					}
					else if(preg_match('/CNUP=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
						$ob_caller_id_num_prefix  = $match[1][0] ;
					}
					else if(preg_match('/CNU=(.*)/', $ob_param, $match, PREG_OFFSET_CAPTURE)) {
						$ob_caller_id_num         = $match[1][0] ;
					}
				}
				$ob_proxy_uri = $row[TrunkConfigModel::$OUTBOUND_PROXY_URI];
				$user_agent = $row[TrunkConfigModel::$USERAGENT];
				$internal_sip_profile = $row[TrunkConfigModel::$INTERNAL_SIP_PROFILE];
				$external_sip_profile = $row[TrunkConfigModel::$EXTERNAL_SIP_PROFILE];
				$media_profile = $row[TrunkConfigModel::$MEDIA_PROFILE];
				$add_prefix = $row[TrunkConfigModel::$ADD_PREFIX];
				$strip_digits = $row[TrunkConfigModel::$STRIP_DIGITS];
				$allow_inbound = $row[TrunkConfigModel::$ALLOW_INBOUND];
				$inbound_uri = $row[TrunkConfigModel::$INBOUND_URI];
				$max_inbound = $row[TrunkConfigModel::$MAX_INBOUND];
				$allow_outbound = $row[TrunkConfigModel::$ALLOW_OUTBOUND];
				$enum_check = $row[TrunkConfigModel::$ENUM_CHECK];
				$enum_type = $row[TrunkConfigModel::$ENUM_TYPE];
				$enum_suffix = $row[TrunkConfigModel::$ENUM_SUFFIX];
				$enum_service = $row[TrunkConfigModel::$ENUM_SERVICE];
				$max_outbound = $row[TrunkConfigModel::$MAX_OUTBOUND];
				$sip_domain = $row[TrunkConfigModel::$SIP_DOMAIN];
				$t38_profile = $row[TrunkConfigModel::$T38_PROFILE];
				$lan_media_encrypt = $row[TrunkConfigModel::$MEDIA_ENCRYPT_LAN];
				$wan_media_encrypt = $row[TrunkConfigModel::$MEDIA_ENCRYPT_WAN];
				$get_sipheader = SipSecuritySettingsModel::unSerializeSIPMethods($row[TrunkConfigModel::$SIPHEADERS]);
				if( trim($_POST['name']) != $name || trim($_POST['trunk_desc']) != $desc
					|| $_POST['domain_ip_uri'] != $domain_ip_uri
					|| trim($_POST['user']) != $username || 
					trim($_POST['authuser']) != $authuser || 
					trim($_POST['passwd']) != $passwd || 
					$sip_reg_val != $sip_registrar || 
					trim($_POST['registrar_expire']) != $registrar_expire
					|| trim($_POST['outbound_caller_id']) != $ob_caller_id || 
					trim($_POST['outbound_proxy_uri']) != $ob_proxy_uri
					|| trim($_POST['user_agent']) != $user_agent || 
					$post_internal_sip_prof != $internal_sip_profile
					|| $post_external_sip_prof != $external_sip_profile || 
					$post_media_profile != $media_profile
					|| (strcmp(trim($_POST['add_prefix']),$add_prefix) != 0 ) || 
					trim($_POST['strip_digits']) != $strip_digits || 
					$post_allow_inbound !=  $allow_inbound || 
					$post_max_ibound !=  $max_inbound || 
					$post_allow_outbound !=  $allow_outbound || $post_max_obound != 
					$max_outbound || $inbound_uri != 
					trim($_POST['INBOUND_URI_HIDDEN']) || 
					$sip_domain != trim($_POST['sip_domain']) || 
					$post_t38_profile != $t38_profile || 
					$_POST['media_encrypt_lan'] != $lan_media_encrypt || 
					$_POST['media_encrypt_wan'] != $wan_media_encrypt || 
					$post_sipheader != $get_sipheader || 
					$_POST['enum_check'] != $enum_check || 
					$_POST['enum_type'] != $enum_type || 
					$_POST['enum_suffix'] != $enum_suffix || 
					$_POST['enum_service'] != $enum_service ) {
						$trunk_config->editTrunkConfigRule ( 
							trim($_POST['name']), trim($_POST['trunk_desc']),
							$_POST['domain_ip_uri'], trim($_POST['user']),
							trim($_POST['passwd']), $sip_reg_val,
							trim($_POST['registrar_expire']),
							trim($_POST['outbound_caller_id']),
							trim($_POST['outbound_proxy_uri']),
							trim($_POST['user_agent']),
							$post_internal_sip_prof,
							$post_external_sip_prof,
							$post_media_profile,
							trim($_POST['add_prefix']),
							trim($_POST['strip_digits']),
							$post_allow_inbound,
							trim($_POST['INBOUND_URI_HIDDEN']),
							$post_max_ibound,
							$post_allow_outbound,
							$post_max_obound,
							trim($_POST['sip_domain']),
							$post_t38_profile,
							$_POST['media_encrypt_lan'],
							$_POST['media_encrypt_wan'],
							$post_sipheader,
							$postenumcheck,
							$_POST['enum_type'],
							$_POST['enum_suffix'],
							$_POST['enum_service'],
							trim($_POST['authuser']));
						$trunk_config->createActionList(
							DEPLOY_MODULES::SBC_CONFIG, 
							"Trunk Config rule edited", 
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
				getPageTips("trunk_config_ttip");
				setTooltip();
				enable_enum();
			});
			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}
			var transMediaProfile = new Array();
			<?php foreach($gettransmediaprofiles as $key => $val) {?>
					transMediaProfile.push('<?php echo $val; ?>');
			<?php } ?>

			function initValidation() {
				$sub( document ).ready(function() {
					var wan_profile = $sub("#internal_sip_profile").val();
					var lan_profile = $sub("#external_sip_profile").val();
					var media_profile = $sub("#media_profile_list").val();
					var t38_profile = $sub("#t38_profile").val();
					var enum_val = $sub("#enum_check").val();
					if(enum_val == "on") {
						$sub("#enum_suffix").attr("data-validation-engine","validate[required]");
						$sub("#enum_service").attr("data-validation-engine","validate[required]");
					}
					if ( wan_profile == "--No Profile--" || lan_profile == "--No Profile--" || media_profile == "--No Profile--") {
						$sub("#internal_sip_profile").attr("data-validation-engine","validate[required,custom[selectProfiles]]");
						$sub("#external_sip_profile").attr("data-validation-engine","validate[required,custom[selectProfiles]]");
						$sub("#media_profile_list").attr("data-validation-engine","validate[required,custom[selectProfiles]]");
					}
				});
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[rulename],minSize[5],maxSize[16]]");
				$sub("#DOMAIN_IP_URI").attr("data-validation-engine","validate[required,custom[IP_DOMAIN_PORT]]");
				$sub("#SIP_DOMAIN").attr("data-validation-engine","validate[required,custom[IP_DOMAIN]]");
				$sub("#SIP_REGISTRAR").attr("data-validation-engine","validate[custom[IP_DOMAIN_PORT]]");
				$sub('#SIP_REGISTRAR').on('change', function() { {
					var SIP_REGISTRAR_VAL = $sub("#SIP_REGISTRAR").val();
					if (SIP_REGISTRAR_VAL != "") {
						$sub("#REGISTRAR_EXPIRE").attr("data-validation-engine","validate[required,custom[integer],min[360],max[3600]]");
					} else {
						$sub("#REGISTRAR_EXPIRE").attr("data-validation-engine","validate[custom[integer],min[360],max[3600]]");
					}
				}
				});
				$sub("#REGISTRAR_EXPIRE").attr("data-validation-engine","validate[custom[integer],min[360],max[3600]]");
				$sub("#USER").attr("data-validation-engine","validate[custom[DIDorUSER],maxSize[256]]");
				$sub("#AUTHUSER").attr("data-validation-engine","validate[custom[AUTHUSER],maxSize[256]]");
				$sub("#PASSWD").attr("data-validation-engine","validate[custom[comments],minSize[1],maxSize[256]]");
				$sub("#OUTBOUND_CALL_ID_NUM").attr("data-validation-engine","validate[custom[DIDorUSER],maxSize[256]]");
				$sub("#OUTBOUND_CALL_ID_NAME").attr("data-validation-engine","validate[custom[DIDorUSER],maxSize[256]]");
				$sub("#OUTBOUND_PROXY_URI").attr("data-validation-engine","validate[custom[IP_DOMAIN_PORT]]");
				$sub("#USER_AGENT").attr("data-validation-engine","validate[custom[onlyLetterNumber],minSize[1],maxSize[16]]");
				$sub("#ADD_PREFIX").attr("data-validation-engine","validate[custom[DIDorUSER],minSize[1],maxSize[16]]");
				$sub("#STRIP_DIGITS").attr("data-validation-engine","validate[required,custom[integer],min[0],max[9]]");
				$sub("#MAX_INBOUND").attr("data-validation-engine","validate[required,custom[integer],min[1],max[10000]]");
				$sub("#MAX_OUTBOUND").attr("data-validation-engine","validate[required,custom[integer],min[1],max[10000]]")
				$sub("#INBOUND_URI").attr("data-validation-engine","validate[custom[IP_DOMAIN_PORT]]");
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub("#adddeviceaccess").validationEngine('attach', {promptPosition : "bottomRight"});
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
				var flag = '<?php print $flag ?>';
				var sipcount = 0;
				if ( flag == "add") {
					var trunkArr = new Array();
					var get_trunk_val = document.getElementById("NAME").value;
					var post_trunk_val = get_trunk_val.trim();
					<?php
						foreach($trunkNames as $key=>$val) {
					?>
							trunkArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var trunk_count = trunkArr.length;
					for (var i=0; i < trunk_count; i++) {
						if (post_trunk_val == trunkArr[i]) {
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
				if($sub("#adddeviceaccess").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					var str = document.getElementById("INBOUND_URI").value;
					str += ";domain="+document.getElementById("INBOUND_DOMAIN").value;
					formObj.INBOUND_URI_HIDDEN.value = str ;
					cid  = document.getElementById("USER").value.trim() ;
					cnap = document.getElementById("OUTBOUND_CALLER_ID_NAME_PREFIX").value.trim() ;
					cna  = document.getElementById("OUTBOUND_CALLER_ID_NAME").value.trim() ;
					cnup = document.getElementById("OUTBOUND_CALLER_ID_NUM_PREFIX").value.trim() ;
					cnu  = document.getElementById("OUTBOUND_CALLER_ID_NUM").value.trim() ;
					if(cid.length>0) str = cid ;
					else             str = "null" ; //this cid unused
					if(cnap.length) str += ";CNAP=" + cnap;
					if(cna.length)  str += ";CNA="  + cna ;
					if(cnup.length) str += ";CNUP=" + cnup;
					if(cnu.length)  str += ";CNU="  + cnu ;
					str += ";CPASS=1" ;
					formObj.outbound_caller_id.value = str ;
					//alert($sub('#adddeviceaccess').serialize());
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
					parent.document.getElementById("container").src = "trunk_config.php";
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

			function enable_inbound_outbound() {
				var enableInbound = document.getElementById("allow_inbound").checked;
				if ( enableInbound == true ) {
					document.getElementById("MAX_INBOUND").disabled = false;
					document.getElementById("INBOUND_URI").disabled = false;
					document.getElementById("INBOUND_DOMAIN").disabled = false;
				} else {
					document.getElementById("MAX_INBOUND").disabled = true;
					document.getElementById("INBOUND_URI").disabled = true;
					document.getElementById("INBOUND_DOMAIN").disabled = true;
				}
				var enableOutbound = document.getElementById("allow_outbound").checked;
				if ( enableOutbound == true ) {
					document.getElementById("MAX_OUTBOUND").disabled = false;
				} else {
					document.getElementById("MAX_OUTBOUND").disabled = true;
				}
			}

			function showLanIP() {
				var iface = document.getElementById("internal_sip_profile").value;
				if( iface != '--No Profile--' ) {
					var post_val = "1";
					$sub.ajax({
						type: "POST",
						cache : false,
						data : {trunk_lan_ip : iface, lan_post:post_val},
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
				var iface = document.getElementById("external_sip_profile").value;
				if( iface != '--No Profile--' ) {
					var post_val = "1";
					$sub.ajax({
						type: "POST",
						cache : false,
						data : {trunk_wan_ip : iface, wan_post:post_val},
						url : "show_iface_ip.php",
						success : function(ipaddr) {
							document.getElementById('wanipaddr').innerHTML = ipaddr;
						}
					});
				} else {
					document.getElementById('wanipaddr').innerHTML = "";
				}
			}

			function showMediaIP() {
				var iface = document.getElementById("media_profile_list").value;
				if(iface != '--No Profile--') {
					var post_val = "1";
					$sub.ajax({
						type: "POST",
						cache : false,
						data : {trunk_media_ip : iface, trunk_media_post:post_val},
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
				if(transMediaProfile.length != 0) {
					for(i=0;i<transMediaProfile.length;i++) {
						if(transMediaProfile[i] == selectedMediaProfile) {
							settrans = 1;
						}
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

			function copyTrunksToSelectedList() {
				var i,j,k;
				var sipheaderLst =  document.getElementById('sipheader');
				var sipheaderSelLst =  document.getElementById('sipheaderSel');
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
							} catch(ex) {
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

			function copyAllTrunksToSelectedList() {
				var i,j;
				var found = 0;
				var sipheaderLst =  document.getElementById('sipheader');
				var sipheaderSelLst =  document.getElementById('sipheaderSel');
				if(sipheaderLst.length <= 0) {
					alert("There are no sipheader in the list to be added.");
					return false;
				}
				removeAllTrunksFromSelectedList();
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

			function removeTrunksFromSelectedList() {
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

			function removeAllTrunksFromSelectedList() {
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
				<form id="adddeviceaccess" name="adddeviceaccess" class="popup-form" action="add_edit_trunk_config.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>

						<tr>
							<td>Trunk Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="15" maxlength="16" />
								<img id="trunk_config_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="trunk_desc" name="trunk_desc" value="<?php echo $desc; ?>"  size="48" maxlength="64" />
								<img id="trunk_config_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Server </td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="DOMAIN_IP_URI" name="domain_ip_uri" value="<?php print $domain_ip_uri; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
						<td>Trunk Domain </td>
						<td width="20"></td>
						<td>
							<select style="width: 160px;" name="sip_domain" id="SIP_DOMAIN" class="ipDrpBox" >
						<?php
						for($k=0;$k<count($sipdomainlist);$k++) {
							$select_str = "";
							if($sipdomainlist[$k] == $sip_domain) { $select_str = "selected";}
						?>
								<option value="<?php print $sipdomainlist[$k];?>" <?php print $select_str;?> ><?php print $sipdomainlist[$k];?></option>
<?php } ?>
							</select>
							<img id="trunk_config_ttip_030" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Trunk Username</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="USER" name="user" value="<?php echo $username; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Trunk AuthUser</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="AUTHUSER" name="authuser" value="<?php echo $authuser; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_033" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Outbound Prefix/CID Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="OUTBOUND_CALLER_ID_NAME_PREFIX" name="outbound_caller_id_name_prefix" value="<?php echo $ob_caller_id_name_prefix; ?>" size="4" maxlength="12" />
								<input type="text" class="ipBox" id="OUTBOUND_CALLER_ID_NAME" name="outbound_caller_id_name" value="<?php echo $ob_caller_id_name; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Outbound Prefix/CID</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="OUTBOUND_CALLER_ID_NUM_PREFIX" name="outbound_caller_id_num_prefix" value="<?php echo $ob_caller_id_num_prefix; ?>" size="4" maxlength="12" />
								<input type="text" class="ipBox" id="OUTBOUND_CALLER_ID_NUM" name="outbound_caller_id_num" value="<?php echo $ob_caller_id_num; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_032" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Password</td>
							<td width="20"></td>
							<td>
								<input type="password" class="ipBox" id="PASSWD" name="passwd" value="<?php echo $passwd; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>SIP Registrar </td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="SIP_REGISTRAR" name="sip_registrar" value="<?php print $sip_registrar; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Registrar Expire</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="REGISTRAR_EXPIRE" name="registrar_expire" value="<?php print $registrar_expire; ?>" size="4" maxlength="4" />
								<img id="trunk_config_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Outbound Proxy URI </td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="OUTBOUND_PROXY_URI" name="outbound_proxy_uri" value="<?php echo $ob_proxy_uri; ?>" size="" maxlength="256" />
								<img id="trunk_config_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Internal SIP Profile</td>
							<td width="20"></td>
						<!--	<?php
								if ( $lanProfiles != null ){
									$lanProfilesArray = array_values($lanProfiles);  }
							?>
								<?php
									$selectedLanProfile = array($internal_sip_profile);
									if ( count($lanProfilesArray) > 0 ) {
										$lan_profiles_avail = array_merge($lanProfilesArray,$selectedLanProfile);
										$count_lan_avail = count($lan_profiles_avail);
									} else {
										$lan_profiles_avail = $selectedLanProfile;
										$count_lan_avail = count($lan_profiles_avail);
									}
								?> -->
							<td>
								<select style="width: 160px;" name="internal_sip_profile" id="internal_sip_profile" class="ipDrpBox" onchange="showLanIP();">
						<?php
						$set =1;
						if(count($internalinterface) == 0) { 
							$set =1;
						} else {
							for($i=0;$i<count($internalinterface);$i++) {
								$select_str = "";
								if($flag == "edit" && ($internalinterface[$i] == $internal_sip_profile)) {
									$select_str = "selected";
									$set = 0;
						?>
									<option value="<?php echo $internalinterface[$i];?>" <?php print $select_str;?> > <?php echo $internalinterface[$i];?> </option>
						<?php
								} else {
									$check = $trunk_config->checkinternalinterface($internalinterface[$i]);
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
							<?php
									}
								}
							} else if ( $flag == "edit" ) {
								for ( $i = 0; $i < $count_lan_avail; $i++) {
									$select_str = "";
									if ( $lan_profiles_avail[$i] == $internal_sip_profile )
										$select_str = "selected";
							?>
											<option value="<?php print $lan_profiles_avail[$i];?>" <?php print  $select_str;?> ><?php print $lan_profiles_avail[$i];?></option>
							<?php
								}
							}
							?> -->
								</select>
								<img id="trunk_config_ttip_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="lanipaddr"></span>
							</td>
						</tr>
						<tr>
							<td>External SIP Profile</td>
							<td width="20"></td>
							<!--<?php
								if ( $wanProfiles != null ){
									$wanProfilesArray = array_values($wanProfiles);  }
								?>
								<?php
									$selectedWanProfile = array($external_sip_profile);
									if ( count($wanProfilesArray) > 0 ) {
										$wan_profiles_avail = array_merge($wanProfilesArray,$selectedWanProfile);
										$count_wan_avail = count($wan_profiles_avail);
									} else {
										$wan_profiles_avail = $selectedWanProfile;
										$count_wan_avail = count($wan_profiles_avail);
									}
								?>-->
							<td>
								<select style="width: 160px;" name="external_sip_profile" id="external_sip_profile" class="ipDrpBox" onchange="showWanIP();">
							<?php
							$set =1;
							if(count($externalinterface) == 0) {
								$set =1;
							} else {
								for($i=0;$i<count($externalinterface);$i++) {
									$select_str = "";
									if($flag == "edit" && ($externalinterface[$i] == $external_sip_profile)) {
										$select_str = "selected";
										$set = 0;
							?>
										<option value="<?php echo $externalinterface[$i];?>" <?php print $select_str;?> > <?php echo $externalinterface[$i];?> </option>
						<?php
									} else {
										$check = $trunk_config->checkexternalinterface($externalinterface[$i]);
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
								<img id="trunk_config_ttip_013" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="wanipaddr"></span>
							</td>
						</tr>
						<tr>
							<td>Media Profile</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="media_profile" id="media_profile_list" class="ipDrpBox" onchange="showMediaIP();" onkeydown="showMediaIP();" onkeyup="showMediaIP();">
									<?php if (count($media_profiles) < 1) { ?>
										<option value="--No Profile--">--No Profile--</option>
									<?php } ?>
									<?php
										for ( $j = 0; $j < count($media_profiles); $j++ ) {
											$select_str = "";
											if ( $media_profiles[$j] == $media_profile )
												$select_str = "selected";
									?>
											<option value="<?php print $media_profiles[$j];?>" <?php print  $select_str;?> ><?php print  $media_profiles[$j];?></option>
									<?php } ?>
								</select>
								<img id="trunk_config_ttip_014" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
										if ( $j == $lan_media_encrypt )
											$select_str = "selected";
									?>
											<option value="<?php print $j;?>" <?php print  $select_str;?> ><?php print $media_encryption[$j];?></option>
									<?php
									}
									?>
										</select>
										<img id="trunk_config_ttip_023" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
								<?php } ?>
								</select>
								<img id="trunk_config_ttip_024" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
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
									<?php } ?>
								</select>
								<img id="trunk_config_ttip_025" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Add Prefix</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="ADD_PREFIX" name="add_prefix" value="<?php print $add_prefix; ?>" size="18" maxlength="2" />
								<img id="trunk_config_ttip_015" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Strip Digits</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="STRIP_DIGITS" name="strip_digits" value="<?php print $strip_digits; ?>" size="1" maxlength="1" />
								<img id="trunk_config_ttip_016" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Allow Inbound</td>
							<td width="20"></td>
							<?php
								if ($allow_inbound == "1") {
									$allow_inbound_checked = "checked";
								} else {
									$allow_inbound_checked = "";
								}
							?>
							<td>
								<input type="checkbox" <?php print $allow_inbound_checked; ?> name="allow_inbound" onclick="enable_inbound_outbound();" id="allow_inbound" />
								<img id="trunk_config_ttip_017" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Inbound Config</td>
							<td width="20"></td>
							<td>
							<table>
							<tr><th align="left"  width="50%">Inbound Domains</th><th align="center">Inbound URI</th></tr>
							<tr>
							<td align="left">
							<select style="width: 160px;" name="inbound_domain" id="INBOUND_DOMAIN" class="ipDrpBox" >
								<?php for($k=0;$k<count($sipdomainlist);$k++){
									$selval = ""; 
									$inbnd_arr = explode(";domain=",$inbound_uri);
									$inbnd_domain = $inbnd_arr[1] ;
									if($inbnd_domain == $sipdomainlist[$k]) { $selval = "selected"; } ?>
										<option value="<?php echo $sipdomainlist[$k]; ?>" <?php echo $selval?> ><?php echo $sipdomainlist[$k];?> </option>

								<?php
								} ?>
							</select>
							<img id="trunk_config_ttip_031" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
							<td align="left"><input type="text" class="ipBox" size="14" maxlength="256" name="inbound_uri" id="INBOUND_URI" value="<?php echo $inbnd_arr[0]?>" />
								<img id="trunk_config_ttip_021" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td>Max Inbound</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="MAX_INBOUND" name="max_inbound" value="<?php print $max_inbound; ?>" size="5" maxlength="5" />
								<img id="trunk_config_ttip_018" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Allow Outbound</td>
							<td width="20"></td>
							<?php
							if ($allow_outbound == "1") {
								$allow_outbound_checked = "checked";
							} else {
								$allow_outbound_checked = "";
							}
							?>
							<td>
								<input type="checkbox" <?php print $allow_outbound_checked; ?> name="allow_outbound" onclick="enable_inbound_outbound();" id="allow_outbound" />
								<img id="trunk_config_ttip_019" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td>Max Outbound</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="MAX_OUTBOUND" name="max_outbound" value="<?php print $max_outbound; ?>" size="5" maxlength="5" />
								<img id="trunk_config_ttip_020" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
<!-- ENUM starts-->
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
								<img id="trunk_config_ttip_026" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
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
							<img id="trunk_config_ttip_027" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
						<tr>
							<td>Enum Suffix</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="enum_suffix" name="enum_suffix" value="<?php print $enum_suffix; ?>" size="10" maxlength="16" />
								<img id="trunk_config_ttip_028" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
						<tr>
							<td>Enum Service</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="enum_service" name="enum_service" value="<?php print $enum_service; ?>" size="10" maxlength="16" />
								<img id="trunk_config_ttip_029" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
<!-- ENUM ends-->
<!-- sip headers -->
						<tr style="display:none;">
						<td valign="top">SIP Headers</td>
						<td width="20"></td>
						<td colspan="4">
						<table>
							<tr>
								<td>
									<select style="width: 160px; height: 150px;" name="sipheader[]" id="sipheader" multiple="multiple" size="9">
									<?php
										for ( $k = 0; $k < count($trunk_profiles); $k++ ) {
									?>
											<option value="<?php print $trunk_profiles[$k];?>"><?php print  $trunk_profiles[$k];?></option>
									<?php
										}
									?>
									</select>
									<img style="vertical-align: top;" id="lcr_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							<td>
								<table>
									<tr><td><a class="btn-usergrp-wide" onclick="copyTrunksToSelectedList();"><span> &gt; </span></a></td></tr>
									<tr><td><a class="btn-usergrp-wide" onclick="copyAllTrunksToSelectedList();"><span> &gt;&gt; </span></a></td></tr>
									<tr><td><a class="btn-usergrp-wide" onclick="removeTrunksFromSelectedList();"><span> &lt; </span></a></td></tr>
									<tr><td><a class="btn-usergrp-wide" onclick="removeAllTrunksFromSelectedList();"><span> &lt;&lt; </span></a></td></tr>
								</table>
							</td>
							<td>
								<select name="sipheaderSel[]" style="width: 160px; height: 150px;" id="sipheaderSel" size="9" multiple="multiple">
							<?php
							for ( $k=0; $k < count($get_sipheader); $k++) {
								if ( $get_sipheader[$k] != "" ) {
							?>
									<option value="<?php print $get_sipheader[$k]; ?>" <?php print "selected"; ?>> <?php print $get_sipheader[$k]; ?></option>
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
								<tr align="right"> <td colspan="3" aligh="center"> <span id="errorText1"></span> </td> </tr>
 <!-- sip headers ends-->
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="TRUNK_CONFIG_LIST" id="TRUNK_CONFIG_LIST" value="" />
					<input type="hidden" name="outbound_caller_id" id="outbound_caller_id" value="" />
					<input type="hidden" id="INBOUND_URI_HIDDEN" name="INBOUND_URI_HIDDEN" value="<?php print $inbound_uri; ?>" />
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
		enable_inbound_outbound();
		showLanIP();
		showWanIP();
		showMediaIP();
	</script>
</html>
