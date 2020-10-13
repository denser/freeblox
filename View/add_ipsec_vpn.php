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
	include_once("Model/ipsec_vpn_model.php");
	include_once("Model/action_mapping_model.php");
	include_once('Model/sip_sec_settings_model.php');

	$ipsec = IPSECConfigModel::getInstance();
	$sip_settings = SipSecuritySettingsModel::getInstance();
	$dh_config = IKE_DH_GROUP::getInstance();
	$get_dh_groups = $dh_config->getDHGroups();
	$ike_encrypt_config = IKE_ENCRYPT_ALGORITHM::getInstance();
	$get_ike_encrypt_alg = $ike_encrypt_config->getIKEEncryptAlgorithms();
	$hash_confg = HASH_ALGORITHM::getInstance();
	$get_hash_alg = $hash_confg->getHashAlgorithms();
	$ike_exchange_confg = IKE_EXCHANGE_MODE::getInstance();
	$get_ike_exchange_mode = $ike_exchange_confg->getIKEExchangeMode();
	$ipsec_encrypt_config = IPSEC_ENCRYPT_ALGORITHM::getInstance();
	$get_ipsec_encrypt_alg = $ipsec_encrypt_config->getIpsecEncryptAlgorithms();
	$auth_method_confg = AUTH_METHOD::getInstance();
	$get_auth_methods = $auth_method_confg->getAuthenticationMethods();
	$ipsec_auth_confg = IPSEC_AUTH_ALGORITHM::getInstance();
	$get_ipsec_alg = $ipsec_auth_confg->getIpsecAuthAlgorithms();
	$enable_cfg = ENABLE_COMPRESSION::getInstance();
	$get_enable_comp = $enable_cfg->getEnableCompression();
	$nat_cfg = NAT_TRAVERSAL::getInstance();
	$get_nat_traversal = $nat_cfg->getNatTraversal();
	$pfs_cfg = IPSEC_PFS_GROUP::getInstance();
	$get_pfs_groups = $pfs_cfg->getPfsGroups();
	$dh_groups = array();
	while($row = $get_dh_groups->fetchArray(SQLITE3_ASSOC)) {
		$dh_groups[$row['VAL']] = $row['NAME'];
	}
	$ike_encrypt_alg = array();
	while($row = $get_ike_encrypt_alg->fetchArray(SQLITE3_ASSOC)) {
		$ike_encrypt_alg[$row['VAL']] = $row['NAME'];
	}
	$hash_algorithms = array();
	while($row = $get_hash_alg->fetchArray(SQLITE3_ASSOC)) {
		$hash_algorithms[$row['VAL']] = $row['NAME'];
	}
	$ike_exchange_modes =  array();
	while($row = $get_ike_exchange_mode->fetchArray(SQLITE3_ASSOC)) {
		$ike_exchange_modes[$row['VAL']] = $row['NAME'];
	}
	$auth_methods =  array();
	while($row = $get_auth_methods->fetchArray(SQLITE3_ASSOC)) {
		$auth_methods[$row['VAL']] = $row['NAME'];
	}
	$ipsec_encrypt_algorithms =  array();
	while($row = $get_ipsec_encrypt_alg->fetchArray(SQLITE3_ASSOC)) {
		$ipsec_encrypt_algorithms[$row['VAL']] = $row['NAME'];
	}
	$ipsec_auth_algorithms =  array();
	while($row = $get_ipsec_alg->fetchArray(SQLITE3_ASSOC)) {
		$ipsec_auth_algorithms[$row['VAL']] = $row['NAME'];
	}
	$enable_compressions =  array();
	while($row = $get_enable_comp->fetchArray(SQLITE3_ASSOC)) {
		$enable_compressions[$row['VAL']] = $row['NAME'];
	}
	$nat_traversals =  array();
	while($row = $get_nat_traversal->fetchArray(SQLITE3_ASSOC)) {
		$nat_traversals[$row['VAL']] = $row['NAME'];
	}
	$pfs_groups =  array();
	while($row = $get_pfs_groups->fetchArray(SQLITE3_ASSOC)) {
		$pfs_groups[$row['VAL']] = $row['NAME'];
	}
	$res = 0;
	$page_title = "Create IPSec VPN Rule";
	$flag = "add";
	$name = "";
	$enable = "";
	$mode = "";
	$tunnel_mode = "checked";
	$transport_mode = "";
	$p2p = "checked";
	$ah = "checked";
	$esp = "";
	$ike_enable = "checked";
	$ike_disable = "";
	$road_warrior = "";
	$policy_type = "";
	$local_gw = "";
	$local_nw = "";
	$remote_gw = "";
	$remote_nw = "";
	$ike_exchange_mode = "";
	$lifetime = "";
	$encrypt_algorithm = "";
	$hash_algorithm = "";
	$auth_method = "";
	$preshared_key = "";
	$transport = "";
	$ipsec_lifetime = "";
	$pfs_group = "";
	$ipsec_encrypt_algorithm = "";
	$auth_algorithm = "";
	$enable_compression = "";
	$nat_traversal = "";
	$enable_dpd = "";
	$dpd_delay = "";
	$dpd_maxfail = "";
	$createdDataNames = array();
	$vpnNames = $ipsec->getNames();
	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$flag = "edit";
		$id = str_replace('@',' ',$val);
		$page_title = "Edit IPSec VPN Rule";
		$row = $ipsec->getIPSECRule($id);
		if($row) {
			$name = $row[IPSECConfigModel::$NAME];
			$enable =  $row[IPSECConfigModel::$ENABLE];
			if ( $enable == 1 ) {
				$enabled = "checked";
			} else {
				$enabled = "";
			}
			$mode = $row[IPSECConfigModel::$MODE];
			if ($mode == "true") {
				$tunnel_mode = "checked";
				$transport_mode = "";
			} else {
				$transport_mode = "checked";
				$tunnel_mode = "";
			}
			$policy_type = $row[IPSECConfigModel::$POLICY_TYPE];
			if ($policy_type == "true") {
				$p2p = "checked";
				$road_warrior = "";
			} else {
				$road_warrior = "checked";
				$p2p = "";
			}
			$local_gw = $row[IPSECConfigModel::$LOCAL_GATEWAY];
			$local_nw = $row[IPSECConfigModel::$LOCAL_NETWORK];
			$remote_gw = $row[IPSECConfigModel::$REMOTE_GATEWAY];
			$remote_nw = $row[IPSECConfigModel::$REMOTE_NETWORK];
			$ike_exchange_mode = $row[IPSECConfigModel::$IKE_EXCHANGE_MODE];
			$lifetime = $row[IPSECConfigModel::$LIFETTIME];
			$encrypt_algorithm = $row[IPSECConfigModel::$ENCRYPTION_ALGORITHM];
			$hash_algorithm = $row[IPSECConfigModel::$HASH_ALGORITHM];
			$auth_method = $row[IPSECConfigModel::$AUTHENTICATION_METHOD];
			$preshared_key = $row[IPSECConfigModel::$PRESHARED_KEY];
			$dh_group = $row[IPSECConfigModel::$DH_GROUP];
			$transport = $row[IPSECConfigModel::$TRANSPORT];
			if ( $transport == "true") {
				$ah = "checked";
				$esp = "";
			} else {
				$ah = "";
				$esp = "checked";
			}
			$ipsec_lifetime = $row[IPSECConfigModel::$IPSEC_LIFETIME];
			$pfs_group = $row[IPSECConfigModel::$PFS_GROUP];
			$ipsec_encrypt_algorithm = $row[IPSECConfigModel::$IPSEC_ENCRYPTION_ALGORITHM];
			$auth_algorithm = $row[IPSECConfigModel::$AUTH_ALOGORITHM];
			$enable_compression = $row[IPSECConfigModel::$ENABLE_COMPRESSION];
			$nat_traversal = $row[IPSECConfigModel::$NAT_TRAVERSAL];
			$enable_dpd = $row[IPSECConfigModel::$ENABLE_DPD];
			$dpd_delay = $row[IPSECConfigModel::$DPD_DELAY];
			$dpd_maxfail = $row[IPSECConfigModel::$DPD_MAXFAIL];
			$unserial_ipsec_encrypt = SipSecuritySettingsModel::unSerializeSIPMethods($ipsec_encrypt_algorithm);
			$unserial_ipsec_auth = SipSecuritySettingsModel::unSerializeSIPMethods($auth_algorithm);
			if ( $enable_dpd == "1") {
				$dpd_enable = "checked";
			} else {
				$dpd_enable = "";
			}
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enable'] == "on")
			$post_enable = "1";
		else
			$post_enable = "";
		if ( $_POST['advance_dpd_en'] == "on" )
			$post_dpd = "1";
		else
			$post_dpd = "";
		$post_encrypt_alg = SipSecuritySettingsModel::serializeSIPMethods($_POST['ipsec_encrypt_algo']);
		$post_auth_alg = SipSecuritySettingsModel::serializeSIPMethods($_POST['ipsec_auth_algo']);
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$ipsec->addIPSECRule( $post_enable,trim($_POST['name']),$_POST['mode'], 
				$_POST['policy_type'],trim($_POST['local_gw']), trim($_POST['local_nw']),
				trim($_POST['remote_gw']),trim($_POST['remote_nw']),
				$_POST['ike_exchange_mode'], trim($_POST['ike_life_time']), 
				$_POST['ike_enc_alg'], $_POST['ike_hash_alg'], 
				$_POST['ike_auth_method'], trim($_POST['ike_preshared_key']), 
				$_POST['ike_dh_group'], $_POST['ipsec_sainfo'], 
				trim($_POST['ipsec_lifetime']), $_POST['ipsec_pfs_grp'], 
				$post_encrypt_alg, $post_auth_alg,$_POST['advance_compr'], 
				$_POST['advance_nat'], $post_dpd, trim($_POST['advance_dpd_delay']), 
				trim($_POST['advance_dpd_maxfail']));
			$ipsec->createActionList(DEPLOY_MODULES::IPSEC_VPN_CONFIG, 
					"IPsec VPN rule added", 
					DEPLOY_MODULES::IPSEC_VPN_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $ipsec->getIPSECRule($id);
			if($row) {
				$name = $row[IPSECConfigModel::$NAME];
				$enable =  $row[IPSECConfigModel::$ENABLE];
				if ( $_POST['enable'] == "on" ) {
					$post_enable = "1";
				} else {
					$post_enable = "";
				}
				if ($_POST['advance_dpd_en'] == "on") {
					$post_en_dpd = "1";
				} else {
					$post_en_dpd = "";
				}
				$mode = $row[IPSECConfigModel::$MODE];
				if ($mode == "true") {
					$tunnel_mode = "checked";
					$transport_mode = "";
				} else {
					$transport_mode = "checked";
					$tunnel_mode = "";
				}
				$policy_type = $row[IPSECConfigModel::$POLICY_TYPE];
				if ($policy_type == "true") {
					$p2p = "checked";
					$road_warrior = "";
				} else {
					$road_warrior = "checked";
					$p2p = "";
				}
				$local_gw = $row[IPSECConfigModel::$LOCAL_GATEWAY];
				$local_nw = $row[IPSECConfigModel::$LOCAL_NETWORK];
				$remote_gw = $row[IPSECConfigModel::$REMOTE_GATEWAY];
				$remote_nw = $row[IPSECConfigModel::$REMOTE_NETWORK];
				$ike_exchange_mode = $row[IPSECConfigModel::$IKE_EXCHANGE_MODE];
				$lifetime = $row[IPSECConfigModel::$LIFETTIME];
				$encrypt_algorithm = $row[IPSECConfigModel::$ENCRYPTION_ALGORITHM];
				$hash_algorithm = $row[IPSECConfigModel::$HASH_ALGORITHM];
				$auth_method = $row[IPSECConfigModel::$AUTHENTICATION_METHOD];
				$preshared_key = $row[IPSECConfigModel::$PRESHARED_KEY];
				$dh_group = $row[IPSECConfigModel::$DH_GROUP];
				$transport = $row[IPSECConfigModel::$TRANSPORT];
				if ( $transport == "true") {
					$ah = "checked";
					$esp = "";
				} else {
					$ah = "";
					$esp = "checked";
				}
				$ipsec_lifetime = $row[IPSECConfigModel::$IPSEC_LIFETIME];
				$pfs_group = $row[IPSECConfigModel::$PFS_GROUP];
				$ipsec_encrypt_algorithm = $row[IPSECConfigModel::$IPSEC_ENCRYPTION_ALGORITHM];
				$auth_algorithm = $row[IPSECConfigModel::$AUTH_ALOGORITHM];
				$enable_compression = $row[IPSECConfigModel::$ENABLE_COMPRESSION];
				$nat_traversal = $row[IPSECConfigModel::$NAT_TRAVERSAL];
				$enable_dpd = $row[IPSECConfigModel::$ENABLE_DPD];
				$dpd_delay = $row[IPSECConfigModel::$DPD_DELAY];
				$dpd_maxfail = $row[IPSECConfigModel::$DPD_MAXFAIL];
				$post_encrypt_alg = SipSecuritySettingsModel::serializeSIPMethods($_POST['ipsec_encrypt_algo']);
				$post_auth_alg = SipSecuritySettingsModel::serializeSIPMethods($_POST['ipsec_auth_algo']);
				$unserial_ipsec_encrypt = SipSecuritySettingsModel::unSerializeSIPMethods($ipsec_encrypt_algorithm);
				$unserial_ipsec_auth = SipSecuritySettingsModel::unSerializeSIPMethods($auth_algorithm);
				if ( $enable != $post_enable || $mode != $_POST['mode'] || 
					$policy_type != $_POST['policy_type'] || 
					trim($_POST['local_gw']) != $local_gw || 
					trim($_POST['local_nw']) != $local_nw || 
					trim($_POST['remote_gw']) != $remote_gw || 
					trim($_POST['remote_nw']) != $remote_nw || 
					$_POST['ike_exchange_mode'] != $ike_exchange_mode ||
					trim($_POST['ike_life_time']) != $lifetime || 
					$_POST['ike_enc_alg'] != $encrypt_algorithm || 
					$_POST['ike_hash_alg'] != $hash_algorithm || 
					$_POST['ike_auth_method'] != $auth_method ||
					trim($_POST['ike_preshared_key']) != $preshared_key || 
					$_POST['ike_dh_group'] != $dh_group || 
					$_POST['ipsec_sainfo'] != $transport || 
					trim($_POST['ipsec_lifetime']) != $ipsec_lifetime || 
					$_POST['ipsec_pfs_grp'] != $pfs_group || 
					$post_encrypt_alg != $unserial_ipsec_encrypt ||
					$post_auth_alg !=  $unserial_ipsec_auth || 
					$_POST['advance_compr'] != $enable_compression || 
					$_POST['advance_nat'] != $nat_traversal ||
					$post_en_dpd != $enable_dpd || 
					trim($_POST['advance_dpd_delay']) != $dpd_delay || 
					trim($_POST['advance_dpd_maxfail']) != $dpd_maxfail ) {
						$ipsec->editIPSECRule ( $post_enable, 
							trim($_POST['name']), $_POST['mode'], 
							$_POST['policy_type'], trim($_POST['local_gw']),
							trim($_POST['local_nw']), 
							trim($_POST['remote_gw']), 
							trim($_POST['remote_nw']), 
							$_POST['ike_exchange_mode'], 
							trim($_POST['ike_life_time']),
							$_POST['ike_enc_alg'], $_POST['ike_hash_alg'], 
							$_POST['ike_auth_method'], 
							trim($_POST['ike_preshared_key']), 
							$_POST['ike_dh_group'], $_POST['ipsec_sainfo'],
							trim($_POST['ipsec_lifetime']), 
							$_POST['ipsec_pfs_grp'], $post_encrypt_alg, 
							$post_auth_alg, $_POST['advance_compr'], 
							$_POST['advance_nat'], $post_en_dpd, 
							trim($_POST['advance_dpd_delay']), 
							trim($_POST['advance_dpd_maxfail']) );
						$ipsec->createActionList(
							DEPLOY_MODULES::IPSEC_VPN_CONFIG, 
							"IPsec VPN rule edited", 
							DEPLOY_MODULES::IPSEC_VPN_CONFIG_STR, "");
				}
				$res = 1;
			}
		}
	}
// Set Enable / Disable parameter via Ajax Post request
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['enable_vpn'] == "true")
			$post_enable_val = "1";
		else if($_POST['enable_vpn'] == "false")
			$post_enable_val = "";
		$vpn_name = str_replace('@',' ',$_POST['vpn_name']);
		$rows = $ipsec->getIPSECRule($vpn_name);
		$db_val = $rows[IPSECConfigModel::$ENABLE];
		if( $post_enable_val != $db_val ) {
			$ipsec->setVPNEnable($vpn_name,$post_enable_val);
			$ipsec->createActionList(DEPLOY_MODULES::IPSEC_VPN_CONFIG, 
				"IPsec VPN rule edited", DEPLOY_MODULES::IPSEC_VPN_CONFIG_STR, "");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/validationEngine.jquery.css" />
		<link rel="stylesheet" type="text/css" href="css/tabs.css" media="screen" />
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
				getPageTips("add_rule");
				setTooltip();
			});
			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}
			$sub(document).ready(function() {
				//Default Action
				$sub("#add_edit_ipsec_vpn .tab_content").hide(); //Hide all content
				$sub("#add_edit_ipsec_vpn ul.tabs li:first").addClass("active").show(); //Activate first tab
				$sub("#add_edit_ipsec_vpn .tab_content:first").show(); //Show first tab content
				$sub("#add_edit_ipsec_vpn .tab_content").hide(); //Hide all content
				$sub("#add_edit_ipsec_vpn ul.tabs li:first").addClass("active").show(); //Activate first tab
				$sub("#add_edit_ipsec_vpn .tab_content:first").show(); //Show first tab content
				//On Click Event
				$sub("#add_edit_ipsec_vpn ul.tabs li").click(function() {
					$sub("#add_edit_ipsec_vpn ul.tabs li").removeClass("active"); //Remove any "active" class
					$sub(this).addClass("active"); //Add "active" class to selected tab
					$sub("#add_edit_ipsec_vpn .tab_content").hide(); //Hide all tab content
					var activeTab = $sub(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
					$sub(activeTab).show(); //Fade in the active content
					return false;
				});
			});
/*----- Jquery Tabs function ----*/
			function initValidation() {
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[onlyLetterNumber],minSize[5],maxSize[32]]");
				$sub("#LOCAL_GW").attr("data-validation-engine","validate[required,custom[ipv4]]");
				$sub("#LOCAL_NW").attr("data-validation-engine","validate[required,custom[ipwithCIDR]]");
				$sub("#REMOTE_GW").attr("data-validation-engine","validate[required,custom[ipv4]]");
				$sub("#REMOTE_NW").attr("data-validation-engine","validate[required,custom[ipwithCIDR]]");
				$sub("#ike_life_time").attr("data-validation-engine","validate[custom[integer],min[0],max[3600]]");
				$sub("#ipsec_lifetime").attr("data-validation-engine","validate[custom[integer],min[0],max[3600]]");
				$sub("#add_edit_ipsec_vpn ul.tabs li").click(function() {
				$sub("#add_edit_ipsec_vpn ul.tabs li").removeClass("active"); //Remove any "active" class
				$sub(this).addClass("active"); //Add "active" class to selected tab
				$sub("#add_edit_ipsec_vpn .tab_content").hide(); //Hide all tab content
				var activeTab = $sub(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
				$sub(activeTab).show(); //Fade in the active content
				if ( activeTab == "#tabAdv") {
					var advDPDEnable = $sub("input:checkbox[name='advance_dpd_en']").val();
					if(advDPDEnable == "on") {
						$sub("#advance_dpd_delay").attr("data-validation-engine","validate[required,custom[integer],min[6],max[36]]");
						$sub("#advance_dpd_maxfail").attr("data-validation-engine","validate[required,custom[integer],min[6],max[36]]");
					}
					$sub("input:checkbox[name='advance_dpd_en']").change(function() {
						if(this.checked) {
							$sub("#advance_dpd_delay").attr("data-validation-engine","validate[required,custom[integer],min[6],max[36]]");
							$sub("#advance_dpd_maxfail").attr("data-validation-engine","validate[required,custom[integer],min[6],max[36]]");
						}
					});
				}
				if ( activeTab == "#tabIPSec") {
					$sub("#ipsec_lifetime").attr("data-validation-engine","validate[required,custom[integer],min[0],max[3600]]");
				}
				if ( activeTab == "#tabIKE") {
					$sub("#ike_life_time").attr("data-validation-engine","validate[required,custom[integer],min[0],max[3600]]");
				}
				});
				$sub("#advance_dpd_delay").attr("data-validation-engine","validate[custom[integer],min[6],max[36]]");
				$sub("#advance_dpd_maxfail").attr("data-validation-engine","validate[custom[integer],min[6],max[36]]");
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub("#ipsecVPN").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				var flag = '<?php print $flag ?>';
				if ( flag == "add" ) {
					var vpnArr = new Array();
					var get_vpn_val = document.getElementById("NAME").value;
					var post_vpn_val = get_vpn_val.trim();
					<?php
						foreach($vpnNames as $key=>$val) {
					?>
							vpnArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var vpnCount = vpnArr.length;
					for (var i=0; i< vpnCount; i++) {
						if (post_vpn_val == vpnArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub('#errorText').show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if($sub("#ipsecVPN").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
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
					parent.document.getElementById("container").src = "ipsec_vpn.php";
				}
			}

			function enableDPD() {
				var enable_pfs = document.getElementById('advance_dpd_en').checked;
				if(!enable_pfs) {
					document.getElementById('advance_dpd_delay').disabled =true;
					document.getElementById('advance_dpd_maxfail').disabled =true;
				} else {
					document.getElementById('advance_dpd_delay').disabled =false;
					document.getElementById('advance_dpd_maxfail').disabled =false;
				}
			}
//P2P/Road Warrior function
			function p2p_roadwarr() {
				if( document.getElementById('p2p').checked == true && document.getElementById("trans_ipsec").checked == true ) {
					document.ipsecVPN.local_gw.disabled = false;
					document.ipsecVPN.local_nw.disabled = true;
					document.ipsecVPN.remote_gw.disabled = false;
					document.ipsecVPN.remote_nw.disabled = true;
				} else if( document.getElementById('p2p').checked == true && document.getElementById("tunnel_ipsec").checked == true ) {
					document.ipsecVPN.local_gw.disabled = false;
					document.ipsecVPN.local_nw.disabled = false;
					document.ipsecVPN.remote_gw.disabled = false;
					document.ipsecVPN.remote_nw.disabled = false;
				} else if( document.getElementById('road_warr').checked == true && document.getElementById("trans_ipsec").checked == true ) {
					document.ipsecVPN.local_gw.disabled = true;
					document.ipsecVPN.local_nw.disabled = true;
					document.ipsecVPN.remote_gw.disabled = true;
					document.ipsecVPN.remote_nw.disabled = true;
				} else if( document.getElementById('road_warr').checked == true && document.getElementById("tunnel_ipsec").checked == true ) {
					document.ipsecVPN.local_gw.disabled = true;
					document.ipsecVPN.local_nw.disabled = true;
					document.ipsecVPN.remote_gw.disabled = true;
					document.ipsecVPN.remote_nw.disabled = true;
				}
				if( document.getElementById('road_warr').checked == true ) {
					document.ipsecVPN.mode[1].disabled = true;
					document.ipsecVPN.mode[0].checked = true;
					document.getElementById('sainfo_false').checked = true;
					document.getElementById('sainfo_true').disabled = true;
				} else if( document.getElementById('p2p').checked == true ) {
					document.ipsecVPN.mode[1].disabled = false;
					document.getElementById('sainfo_false').checked = true;
					document.getElementById('sainfo_true').disabled = false;
				}
			}
// Tunnel/Transport function
			function policyMode_Change() {
				var ipsecMode = document.getElementsByName("mode");
				if(document.getElementById("tunnel_ipsec").checked == true &&
					document.getElementById("p2p").checked == true) {
						document.ipsecVPN.local_nw.disabled = false;
						document.ipsecVPN.remote_nw.disabled = false;
						document.ipsecVPN.local_gw.disabled = false;
						document.ipsecVPN.remote_gw.disabled = false;
				} else if(document.getElementById("tunnel_ipsec").checked == true &&
					document.getElementById("road_warr").checked == true) {
						document.ipsecVPN.local_nw.disabled = true;
						document.ipsecVPN.remote_nw.disabled = true;
						document.ipsecVPN.local_gw.disabled = true;
						document.ipsecVPN.remote_gw.disabled = true;
				} else if(document.getElementById("trans_ipsec").checked == true &&
					document.getElementById("p2p").checked == true) {
						document.ipsecVPN.local_nw.disabled = true;
						document.ipsecVPN.remote_nw.disabled = true;
						document.ipsecVPN.local_gw.disabled = false;
						document.ipsecVPN.remote_gw.disabled = false;
				}
				else if(document.getElementById("trans_ipsec").checked == true &&
					document.getElementById("road_warr").checked == true) {
						document.ipsecVPN.local_nw.disabled = true;
						document.ipsecVPN.remote_nw.disabled = true;
						document.ipsecVPN.local_gw.disabled = true;
						document.ipsecVPN.remote_gw.disabled = true;
				}
			}

// Certificate/Preshared Selection function
			function ipsec_cert_preshared() {
				var selIndex = document.getElementById('ike_auth_method').selectedIndex;
				if(selIndex == 0) {
					document.getElementById('preshared_key').style.display='';
					document.getElementById('certificate').style.display='none';
				} else {
					document.getElementById('preshared_key').style.display='none';
					document.getElementById('certificate').style.display='';
				}
			}

//Onclick show password in plain text function
			$sub(function(){
				$sub(".showpassword").each(function(index,input) {
					var $input = $sub(input);
					$sub('<label class="showpasswordlabel"/>').append(
						$sub("<input type='checkbox' class='showpasswordcheckbox' />").click(function() {
							var change = $sub(this).is(":checked") ? "text" : "password";
							var rep = $sub("<input type='" + change + "' />")
								.attr("id", $input.attr("id"))
								.attr("name", $input.attr("name"))
								.attr('class', $input.attr('class'))
								.val($input.val())
								.insertBefore($input);
								$input.remove();
								$input = rep;
						 })
					).append($sub("<span/>").text("Show password")).insertAfter($input);
				});
			});
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div id="add_edit_ipsec_vpn">
			<ul class="tabs">
				<li><a href="#tabPS">Policy Settings</a></li>
				<li><a href="#tabIKE">IKE</a></li>
				<li><a href="#tabIPSec">IPSec</a></li>
				<li><a href="#tabAdv">Advanced</a></li>
			</ul>
			<div class="tab_container"> <a name="tabPS"></a> <a name="tabIKE"></a> <a name="tabIPSec"></a>
			<a name="tabAdv"></a>
			<div class="popup-content" style="height: 300px">
				<form id="ipsecVPN" name="ipsecVPN" class="popup-form" action="add_ipsec_vpn.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
				<!-- Policy Settings tab end -->
				<div id="tabPS" class="tab_content">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<td width="100"><label for="enable">Enable</label></td>
							<td width="5"></td>
							<td><input type="checkbox" name="enable" <?php print $enabled; ?> id="enable" /></td>
						</tr>
						<tr>
							<td>Policy Name</td>
							<td width="5"></td>
							<td>
									<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="" maxlength="16" />
									<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									<span id="errorText"></span>
								</td>
						</tr>
						<tr>
							<td>Mode</td>
							<td width="5"></td>
							<td>
								<input type="radio" <?php print $tunnel_mode; ?> value="true" id="tunnel_ipsec" name="mode" onclick="policyMode_Change();" /> Tunnel
								<input type="radio" <?php print $transport_mode; ?> value="false" id="trans_ipsec" name="mode" onclick="policyMode_Change();" /> Transport
							</td>
						</tr>
						<tr>
							<td>Policy Type</td>
							<td width="5"></td>
							<td>
								<input type="radio" <?php print $p2p; ?> value="true" id="p2p" name="policy_type" onclick="p2p_roadwarr();"/> P2P
								<input type="radio" <?php print $road_warrior; ?> value="false" id="road_warr" name="policy_type" onclick="p2p_roadwarr();" /> Road Warrior
							</td>
						</tr>
						<tr>
							<td width="165">Local Gateway</td>
							<td width="5"></td>
							<td><input type="text" name="local_gw" value="<?php print $local_gw; ?>" maxlength="18" size="" id="LOCAL_GW" class="ipBox" /></td>
						</tr>
						<tr>
							<td width="165">Local Network</td>
							<td width="5"></td>
							<td><input type="text" name="local_nw" value="<?php print $local_nw; ?>" maxlength="18" size="" id="LOCAL_NW" class="ipBox" /></td>
						</tr>
						<tr>
							<td width="165">Remote Gateway</td>
							<td width="5"></td>
							<td><input type="text" name="remote_gw" value="<?php print $remote_gw; ?>" maxlength="18" size="" id="REMOTE_GW" class="ipBox" /></td>
						</tr>
						<tr>
							<td width="165">Remote Network</td>
							<td width="5"></td>
							<td><input type="text" name="remote_nw" value="<?php print $remote_nw; ?>" maxlength="18" size="" id="REMOTE_NW" class="ipBox" /></td>
						</tr>
					</table>
			<div class="clear"></div>
		</div>
		<!-- Policy Settings tab end -->
		<!-- Start : IKE Content -->
		<div id="tabIKE" class="tab_content">
		<table>
			<tr>
				<td>IKE Exchange Mode</td>
				<td width="5"></td>
				<td><select name="ike_exchange_mode" class="ipDrpBox">
					<?php
					for( $i = 1; $i <= count($ike_exchange_modes); $i++ ) {
						$select_str = "";
						if ( $ike_exchange_modes[$i] == $ike_exchange_mode)
							$select_str = "selected";
					?>
						<option value="<?php print $ike_exchange_modes[$i]; ?>" <?php print $select_str; ?>> <?php print $ike_exchange_modes[$i]; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td>Lifetime</td>
				<td width="5"></td>
				<td><input type="text" size="4" maxlength="4" class="ipBox" value="<?php print $lifetime; ?>" name="ike_life_time" id="ike_life_time"> &nbsp; in minutes</td>
			</tr>
			<tr>
				<td valign="top">Encryption Algorithm</td>
				<td width="5"></td>
				<td>
					<select class="ipDrpBox" id="ike_enc_alg" name="ike_enc_alg">
						<?php
							foreach($ike_encrypt_alg as $ike_enc_key=>$ike_enc_val ) {
								$select_str = "";
								if ( $ike_enc_key == $encrypt_algorithm)
								$select_str = "selected";
						?>
							<option value="<?php print "$ike_enc_key";?>" <?php print $select_str; ?>><?php print "$ike_enc_val";?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Hash Algorithm</td>
				<td width="5"></td>
				<td>
					<select class="ipDrpBox" id="ike_hash_alg" name="ike_hash_alg">
						<?php
						foreach($hash_algorithms as $hash_alg_key=>$hash_alg_val ) {
							$select_str = "";
							if ( $hash_alg_key == $hash_algorithm)
								$select_str = "selected";
						?>
								<option value="<?php print "$hash_alg_key"; ?>" <?php print $select_str;?> ><?php print "$hash_alg_val"; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Authentication method</td>
				<td width="5"></td>
				<td>
					<select class="ipDrpBox" id="ike_auth_method" name="ike_auth_method" onchange="ipsec_cert_preshared();" onkeyup="ipsec_cert_preshared();" onkeydown="ipsec_cert_preshared();">
					<?php
						foreach($auth_methods as $auth_method_key=>$auth_method_val ) {
							$select_str = "";
							if ( $auth_method_key == $auth_method )
								$select_str = "selected";
					?>
								<option value="<?php print "$auth_method_key"; ?>" <?php print $select_str; ?>><?php print "$auth_method_val"; ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr id="preshared_key">
				<td>Preshared key</td>
				<td width="5"></td>
				<td><input type="password" maxlength="128" id="ike_preshared_key" class="showpassword" value="<?php print $preshared_key; ?>" autocomplete="off" name="ike_preshared_key"></td>
			</tr>
			<tr id="certificate">
				<td>Server Certificate</td>
				<td></td>
				<td>
					<select name="ipsec_certificate" class="ipDrpBox">
						<option value="Certificates">Certificates</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>DH Group</td>
				<td width="5"></td>
				<td>
					<select class="ipDrpBox" id="ike_dh_group" name="ike_dh_group">
						<?php
						for($i = 1; $i <= count($dh_groups); $i++) {
							$select_str = "";
							if ($dh_groups[$i] == "")
								continue;
							if ( $dh_groups[$i] ==  $dh_group)
								$select_str = "selected";
						?>
							<option value="<?php print  $dh_groups[$i]; ?>" <?php print $select_str; ?>><?php print $dh_groups[$i]; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="clear"></div>
		</div>
<!-- Tab IKE content end -->
		 <div id="tabIPSec" class="tab_content">
		 <table>
			<tr>
				<td>Transport</td>
				<td width="5"></td>
				<td>
					<input type="radio" onchange="ipsecSainfo_Change();" <?php print $ah; ?> value="true" id="sainfo_true" name="ipsec_sainfo"> <label for="sainfo_true">AH</label>
					<input type="radio" onchange="ipsecSainfo_Change();" <?php print $esp; ?> value="false" id="sainfo_false" name="ipsec_sainfo"> <label for="sainfo_false">ESP</label>
				</td>
			</tr>
			<tr>
				<td>Lifetime</td>
				<td width="5"></td>
				<td><input type="text" size="4" maxlength="4" class="ipBox" value="<?php print $ipsec_lifetime; ?>" name="ipsec_lifetime" id="ipsec_lifetime"> &nbsp; in minutes</td>
			</tr>
			<tr>
				<td>PFS Group</td>
				<td width="5"></td>
				<td>
					<select class="ipDrpBox" id="ipsec_pfs_grp" name="ipsec_pfs_grp">
						<?php
						for( $i = 1; $i <= count($pfs_groups); $i++) {
							$select_str = "";
							if ($pfs_groups[$i] == "")
								continue;
							if ( $pfs_groups[$i] == $pfs_group )
								$select_str = "selected";
						?>
						<option value="<?php print $pfs_groups[$i] ?>" <?php print $select_str; ?>><?php print $pfs_groups[$i] ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">Encryption Algorithm</td>
				<td width="5"></td>
				<td>
					<select id="ipsec_encrypt_algo" size="7" multiple="multiple" class="ipTxtArea mw230px" name="ipsec_encrypt_algo[]">
						<?php
						if ($flag == "add" ) {
							foreach ($ipsec_encrypt_algorithms as $ipsec_encrypt_algorithms_key=>$ipsec_encrypt_algorithms_val) {
						?>
							<option value="<?php print "$ipsec_encrypt_algorithms_key"; ?>" <?php if ( $ipsec_encrypt_algorithms_key == "2")  print "selected";?>><?php print "$ipsec_encrypt_algorithms_val"; ?></option>
						<?php
							}
						}
						foreach ($ipsec_encrypt_algorithms as $ipsec_encrypt_algorithms_key=>$ipsec_encrypt_algorithms_val) {
							foreach($unserial_ipsec_encrypt as $unserial_ipsec_encrypt_key=>$unserial_ipsec_encrypt_val) {
								if ( $unserial_ipsec_encrypt_val == $ipsec_encrypt_algorithms_key )
									$select_str = "selected"."@".$ipsec_encrypt_algorithms_key;
								$ipsecEncSelected = explode("@",$select_str);
							}
						?>
							<option value="<?php print "$ipsec_encrypt_algorithms_key"; ?>" <?php if ( $ipsecEncSelected[1] == $ipsec_encrypt_algorithms_key)  print $ipsecEncSelected[0];?>><?php print "$ipsec_encrypt_algorithms_val"; ?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">Authentication Algorithm</td>
				<td width="5"></td>
				<td valign="top">
					<select id="ipsec_auth_algo" size="3" multiple="multiple" class="ipTxtArea mw230px" name="ipsec_auth_algo[]">
					<?php
					if ($flag == "add" ) {
						foreach ($ipsec_auth_algorithms as $ipsec_auth_algorithms_key=>$ipsec_auth_algorithms_val) {
					?>
							<option value="<?php print "$ipsec_auth_algorithms_key"; ?>" <?php if ( $ipsec_auth_algorithms_key == "1")  print "selected";?>><?php print "$ipsec_auth_algorithms_val"; ?></option>
					<?php
						}
					}
					foreach ( $ipsec_auth_algorithms as $ipsec_auth_algorithms_key=> $ipsec_auth_algorithms_val) {
						foreach ( $unserial_ipsec_auth as $unserial_ipsec_auth_key=>$unserial_ipsec_auth_val ) {
							if ( $unserial_ipsec_auth_val == $ipsec_auth_algorithms_key)
								$select_str = "selected"."@".$ipsec_auth_algorithms_key;
							$hashAlgSelected = explode("@",$select_str);
						}
					?>
							<option value="<?php print $ipsec_auth_algorithms_key; ?>" <?php if ($hashAlgSelected[1] == $ipsec_auth_algorithms_key )  print $hashAlgSelected[0]; ?>><?php print $ipsec_auth_algorithms_val; ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="clear"></div>
		</div>
<!-- Start: Network Tab Content -->
		<div id="tabAdv" class="tab_content">
		<table>
			<tr>
				<td>Enable compression</td>
				<td width="5"></td>
				<td>
					<select class="ipDrpBox" id="advance_compr" name="advance_compr">
				<?php
					foreach ( $enable_compressions as $enable_compressions_key=>$enable_compressions_val ) {
						$select_str = "";
						if ( $enable_compressions_key == $enable_compression )
							$select_str = "selected";
				?>
							<option value="<?php print $enable_compressions_key; ?>" <?php print $select_str; ?>><?php print $enable_compressions_val; ?></option>
				<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>NAT Traversal</td>
				<td width="5"></td>
				<td>
					<select class="ipDrpBox" id="advance_nat" name="advance_nat">
					<?php
						foreach ( $nat_traversals as $nat_traversals_key=>$nat_traversals_val ) {
							$select_str = "";
							if ( $nat_traversals_key == $nat_traversal )
								$select_str = "selected";
					?>
						<option value="<?php print $nat_traversals_key; ?>" <?php print $select_str; ?>><?php print $nat_traversals_val; ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="advance_dpd_en">Enable Dead Peer Detection</label></td>
				<td width="5"></td>
				<td><input type="checkbox" onclick="enableDPD()" id="advance_dpd_en" <?php print $dpd_enable; ?> name="advance_dpd_en"></td>
			</tr>
			<tr>
				<td>DPD Delay</td>
				<td width="5"></td>
				<td><input type="text" size="2" maxlength="2" class="ipBox" value="<?php print $dpd_delay; ?>" id="advance_dpd_delay" name="advance_dpd_delay" disabled="">&nbsp; in seconds</td>
			</tr>
			<tr>
				<td>DPD Maxfail</td>
				<td width="5"></td>
				<td><input type="text" size="2" maxlength="2" class="ipBox" value="<?php print $dpd_maxfail; ?>" id="advance_dpd_maxfail" name="advance_dpd_maxfail" disabled="">&nbsp; in seconds</td>
			</tr>
		</table>
		<div class="clear"></div>
		</div>
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
	</div>
	</body>
	<script type="text/javascript">
		initValidation();
		enableDPD();
		p2p_roadwarr();
		policyMode_Change();
		ipsec_cert_preshared();
	</script>
</html>
