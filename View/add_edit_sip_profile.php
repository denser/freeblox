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
	include_once("Model/sip_profile_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/sys_calls.php");
	include_once("Model/devicelist_settings_model.php");

	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$devinterfaces = $devicelist_settings_config->getsipvalues();
	$interfaces=$devicelist_settings_config->getInternalExternalList();
	$vlanvirtualip = $devicelist_settings_config->getVlanVirtualIp(0);
	foreach($vlanvirtualip as $val) {
		array_push($interfaces,$val);
	}
	$sip_config = SIPProfileConfigModel::getInstance();
	$serverCertList = SysCalls::getCertList();
	$serverCertCount =  count($serverCertList);
	$get_nat_settings = $sip_config->getNatSettings();
	$count_nat_settings = count($get_nat_settings);
	$ifaceName = "WanInterface";
	$wanIface = DeviceSettingsModel::getIfaceName($ifaceName);
	$res = 0;
	$page_title = "Create SIP Profile";
	$flag = "add";
	$name = "";
	$desc = "";
	$interface = "";
	$access_protocol = "";
	$access_port = "";
	$enable_keepalive = "";
	$keepalive_int = "";
	$sip_tos = "";
	$allowed_ip_ports = array("any");
	$domain = "";
	$nat_settings = "";
	$ip_address = "";
	$req_tls = 0;
	$ver_tls = 0;
	$get_sipheader = "";
	$get_sip_domain = "";
	$nat_enable = 0;
	$interface_sipport = "";
	$createdDataNames = array();
	$transport_str_array = array("udp","tcp","tls");
	$sbc_transport_type = SBCTransportType::getInstance();
	$transport_types = $sbc_transport_type->getSBCTransportType();
	$sipheader_config_profiles = $sip_config->getAllsipheaders();
	$k = 0;
	while ($row = $sipheader_config_profiles->fetchArray()) {
		$sipheader_profiles[$k] = $row[0];
		$k++;
	}
	$sipdomain_profiles = array();
	$sipdomain_profiles = $sip_config->getsipdomain();
	$sbc_transport = array();
	$sipNames = $sip_config->getNames();
	$sipaccessport = $sip_config->getSipInterfaceAccessPorts();
	while($tr_row = $transport_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_transport[$tr_row['VAL']] = $tr_row['NAME'];
	}
	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit SIP Profile";
		$row = $sip_config->getSIPProfile($id);
		if($row) {
			$name = $row[SIPProfileConfigModel::$NAME];
			$desc = $row[SIPProfileConfigModel::$DESCRIPTION];
			$interface = $row[SIPProfileConfigModel::$INTERFACE];
			$access_protocol = $row[SIPProfileConfigModel::$ACCESS_PROTOCOL];
			$access_port = $row[SIPProfileConfigModel::$ACCESS_PORT];
			$enable_keepalive = $row[SIPProfileConfigModel::$ENABLE_KEEPALIVE];
			$keepalive_int = $row[SIPProfileConfigModel::$KEEPALIVE_INTERVAL];
			$sip_tos = $row[SIPProfileConfigModel::$SIP_TOS];
			$get_allowed_ip_ports = $row[SIPProfileConfigModel::$ALLOWED_IP_PORTS];
			$domain = $row[SIPProfileConfigModel::$DOMAIN];
			$server_certs_val = $row[SIPProfileConfigModel::$SERVER_CERTS];
			$nat_settings = $row[SIPProfileConfigModel::$NAT_SETTINGS];
			$ip_address = $row[SIPProfileConfigModel::$IP_ADDRESS];
			$req_tls = $row[SIPProfileConfigModel::$REQ_TLS];
			$ver_tls = $row[SIPProfileConfigModel::$VER_TLS];
			$nat_enable = $row[SIPProfileConfigModel::$NAT_ENABLE];
			$get_sipheader = SipSecuritySettingsModel::unSerializeSIPMethods($row[SIPProfileConfigModel::$SIPHEADERS]);
			$get_sip_domain = SipSecuritySettingsModel::unSerializeSIPMethods($row[SIPProfileConfigModel::$SIPDOMAINS]);
			$allowed_ip_ports = SipSecuritySettingsModel::unSerializeSIPMethods($get_allowed_ip_ports);
			$interface_sipport = $interface."-".$access_port;
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enable'] == "on")
			$post_enable = "1";
		else
			$post_enable = "";
		if($_POST['enable_keepalive'] == "on")
			$post_enable_keepalive = "1";
		else
			$post_enable_keepalive = "";
		if ($_POST['ip_port_arr'] != "") {
			$allowed_ip = SipSecuritySettingsModel::serializeSIPMethods($_POST['ip_port_arr']);
		}
//added tls bug fixes on 28-jan-16 begin
		$REQ_V="0";
		$VER_V="0";
		if ( $_POST['sip_protocol'] =="tls"){
			$REQ_V = $_POST['REQ_TLS_HID'];
			$VER_V = $_POST['VER_TLS_HID'];
		}
//added tls bug fixes on 28-jan-16 end
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			if ( $_POST['sipheaderSel'] != "") {
				$post_sipheader = SipSecuritySettingsModel::serializeSIPMethods($_POST['sipheaderSel']);
			}
			if ( $_POST['sip_domain_sel'] != "") {
				$post_sip_domain = SipSecuritySettingsModel::serializeSIPMethods($_POST['sip_domain_sel']);
			}
			$sip_config->addSIPProfile(trim($_POST['name']),
						$_POST['comments'],
						$_POST['interfaces'],
						$_POST['sip_protocol'],
						trim($_POST['sip_port']),
						$post_enable_keepalive,
						trim($_POST['keepalive_int']),
						trim($_POST['sip_tos']),
						$allowed_ip,
						trim($_POST['domain']),
						$_POST['server_certs'],
						$_POST['nat_settings'],
						$_POST['ip_address'],
						$REQ_V,
						$VER_V,
						$post_sipheader,
						$post_sip_domain,
						$_POST['NAT_ENABLE_HID']);
			$sip_config->createActionList(DEPLOY_MODULES::SBC_CONFIG, 
						"SIP Profile added", 
						DEPLOY_MODULES::SBC_CONFIG_STR, "");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $sip_config->getSIPProfile($id);
			if($row) {
				$name = $row[SIPProfileConfigModel::$NAME];
				$desc = $row[SIPProfileConfigModel::$DESCRIPTION];
				$interface = $row[SIPProfileConfigModel::$INTERFACE];
				$access_protocol = $row[SIPProfileConfigModel::$ACCESS_PROTOCOL];
				$access_port = $row[SIPProfileConfigModel::$ACCESS_PORT];
				$enable_keepalive = $row[SIPProfileConfigModel::$ENABLE_KEEPALIVE];
				$keepalive_int = $row[SIPProfileConfigModel::$KEEPALIVE_INTERVAL];
				$sip_tos = $row[SIPProfileConfigModel::$SIP_TOS];
				$get_allowed_ip_ports = $row[SIPProfileConfigModel::$ALLOWED_IP_PORTS];
				$domain = $row[SIPProfileConfigModel::$DOMAIN];
				$server_certs_val = $row[SIPProfileConfigModel::$SERVER_CERTS];
				$nat_settings = $row[SIPProfileConfigModel::$NAT_SETTINGS];
				$ip_address = $row[SIPProfileConfigModel::$IP_ADDRESS];
				$req_tls = $row[SIPProfileConfigModel::$REQ_TLS];
				$ver_tls = $row[SIPProfileConfigModel::$VER_TLS];
				$nat_enable = $row[SIPProfileConfigModel::$NAT_ENABLE];
				$get_sipheader = SipSecuritySettingsModel::unSerializeSIPMethods($row[SIPProfileConfigModel::$SIPHEADERS]);
				$get_sip_domain = SipSecuritySettingsModel::unSerializeSIPMethods($row[SIPProfileConfigModel::$SIPDOMAINS]);
				if ( $_POST['sipheaderSel'] != "")	{
					$post_sipheader = SipSecuritySettingsModel::serializeSIPMethods($_POST['sipheaderSel']);
				}
				if ( $_POST['sip_domain_sel'] != "") {
					$post_sip_domain = SipSecuritySettingsModel::serializeSIPMethods($_POST['sip_domain_sel']);
				}
				$allowed_ip_ports = SipSecuritySettingsModel::unSerializeSIPMethods($get_allowed_ip_ports);
				$postdumyarray = $_POST['sip_domain_sel'];
				$rmsipdomain = array();
				foreach($get_sip_domain as $key => $val) {
					$returnval = array_search( $val,$postdumyarray,true);
					if($returnval === false) {
						array_push($rmsipdomain,$val);
					}
				}
				if ( $desc != trim($_POST['comments']) || 
					$interface != $_POST['interfaces'] || 
					$access_protocol != $_POST['sip_protocol'] 
					|| $access_port != trim($_POST['sip_port']) || 
					$enable_keepalive != $post_enable_keepalive
					|| $keepalive_int != trim($_POST['keepalive_int']) || 
					$sip_tos != trim($_POST['sip_tos']) || $allowed_ip != 
					$get_allowed_ip_ports || trim($_POST['domain']) != 
					$domain || $server_certs_val != $_POST['server_certs'] 
					||$_POST['nat_settings'] != $nat_settings || 
					trim($_POST['ip_address']) != $ip_address || 
					$_POST['REQ_TLS_HID'] != $req_tls || 
					$_POST['VER_TLS_HID'] != $ver_tls || 
					$post_sipheader != $get_sipheader || 
					$post_sip_domain != $get_sip_domain || 
					$nat_enable != $_POST['NAT_ENABLE_HID']) {
						$sip_config->editSIPProfile ( trim($_POST['name']),
							$_POST['comments'],
							$_POST['interfaces'],
							$_POST['sip_protocol'],
							trim($_POST['sip_port']),
							$post_enable_keepalive,
							trim($_POST['keepalive_int']),
							trim($_POST['sip_tos']),
							$allowed_ip,
							trim($_POST['domain']),
							$_POST['server_certs'],
							$_POST['nat_settings'],
							$_POST['ip_address'],
							$REQ_V,
							$VER_V,
							$post_sipheader,
							$post_sip_domain,
							$_POST['NAT_ENABLE_HID']);
						if(!empty($rmsipdomain)) {
							foreach($rmsipdomain as $key => $val1) {
								$sip_config->rmSipDomainRoaming($val1,$name);
							}
						}
						$sip_config->createActionList(
							DEPLOY_MODULES::SBC_CONFIG, 
							"SIP Profile edited", 
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
			$sub('#copyAllowedIPPorts').click(function() {
				$sub('#ip_port_list option').attr('selected', 'selected');
			});
			var flag = "<?php echo $flag ?>";
			var wan_iface = '<?php print $wanIface ?>';
			var nat_enable = '<?php print $nat_enable ?>';
			var interface_nat = '<?php print $interface; ?>';
			var interface_sipport = '<?php print $interface_sipport; ?>';
			var devarray =  new Array();
			var devintername =  new Array();
			var devinterfacetype = new Array();
			var devnatsettings = new Array();
			<?php
				while($row = $devinterfaces->fetchArray(SQLITE3_ASSOC)) {
			?>
					devarray.push('<?php echo $row[DevicelistSettingsModel::$DEV_ID]; ?>');
					devintername.push('<?php echo $row[DevicelistSettingsModel::$INTER_NAME]; ?>');
					devinterfacetype.push('<?php echo $row[DevicelistSettingsModel::$I_TYPE]; ?>');
					devnatsettings.push('<?php echo $row[DevicelistSettingsModel::$NAT_SETTINGS]; ?>');
			<?php 
				}
			?>
			var devnamecount = devintername.length;
			$sub(document).ready(function(){
				initValidation();
				tlsload();
				getPageTips("sip_profile_ttip");
				setTooltip();
				showIP();
			});

			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			function initValidation() {
				$sub( document ).ready(function() {
					var sel_interface= $sub("#interfaces_list").val();
						$sub("#sip_domain_sel").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
					if ( sel_interface == "--No Interfaces--") {
						$sub("#interfaces_list").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
					}
				});
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[rulename],minSize[5],maxSize[16],LEN_FIX_1007[11]]");
				$sub("#sip_port").attr("data-validation-engine","validate[required,custom[integer],min[1],max[65535]]");
				$sub("#KEEPALIVE_INT").attr("data-validation-engine","validate[required,custom[integer],min[60],max[360]]");
				$sub("#ip_port_field").attr("data-validation-engine","validate[custom[IP_PORT_ANY]]");
				$sub("#SIP_TOS").attr("data-validation-engine","validate[custom[integer],min[0],max[63]]");
				$sub("#DOMAIN").attr("data-validation-engine","validate[required,custom[IP_DOMAIN]]");
				$sub("#server_certs_opt").attr("data-validation-engine","validate[required,custom[serverCerts]]");
				$sub('#nat_settings').on('change', function() { {
					var nat_settings  = $sub("#nat_settings option:selected").val();
					if( nat_settings == "STUN" ) {
						$sub("#IP_ADDRESS").attr("data-validation-engine","validate[required,custom[IP_DOMAIN]]");
					}
					if( nat_settings == "STATIC" ) {
						$sub("#IP_ADDRESS").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
				}});

				$sub(document).ready(function(){ {
					var nat_settings  = $sub("#nat_settings option:selected").val();
					if( nat_settings == "STUN" ) {
						$sub("#IP_ADDRESS").attr("data-validation-engine","validate[required,custom[IP_DOMAIN]]");
					}
					if( nat_settings == "STATIC" ) {
						$sub("#IP_ADDRESS").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
				}});
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub("#addsipprofile").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			$sub(document).ready(function(){
				$sub('input[type="button"]').click(function(){
					var $op = $sub('#sipheaderSel option:selected'),
					$this = $sub(this);
					if($op.length){
						if($this.val() == 'Up') {
							$op.first().prev().before($op);
						} if($this.val() == 'Down') {
							$op.last().next().after($op);
						}
					}
				});
				$sub('input[type="button"]').click(function(){
					var $op1 = $sub('#sip_domain_sel option:selected'),
						$this = $sub(this);
					if($op1.length){
						if($this.val() == 'Up1') {
							$op1.first().prev().before($op1);
						} if($this.val() == 'Down1') {
							$op1.last().next().after($op1);
						}
					}
				});
			});

			function submitData() {
				var flag = '<?php print $flag ?>';
				if ( flag == "add") {
					var sipArr =  new Array();
					var get_sip_val = document.getElementById("NAME").value;
					var post_sip_val = get_sip_val.trim();
					<?php
						foreach ($sipNames as $key=>$val) {
					?>
							sipArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var sipCount = sipArr.length;
					for ( var i=0; i < sipCount; i++) {
						if (post_sip_val== sipArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use";
							document.getElementById("errorText").className = "alert-box errorMsg";
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if(flag == "add" || flag == "edit") {
					var formObj = document.forms[0];
					var val = formObj.interfaces_list.value;
						if(val == "NONE") {
							document.getElementById("errorText1").innerHTML = "Invalid! NO INTERFACE.";
							document.getElementById("errorText1").className = "alert-box errorMsg"
							$sub("#errorText1").show().delay(2000).fadeOut();
							return false;
						}
				}
				// For Access port error msg
				var accessports = new Array();
				var get_access_val = document.getElementById("sip_port").value;
				var get_interface_val = document.getElementById("interfaces_list").value;
				var interface_access_val = get_interface_val+ "-" +get_access_val;
				<?php
					foreach ($sipaccessport as $key=>$val) {
				?>
						accessports.push('<?php print $val ?>');
				<?php
					}
				?>
				var accesscount = accessports.length;
				if(flag == "add") {
					for ( var i=0; i < accesscount; i++) {
						if (interface_access_val == accessports[i]) {
							document.getElementById("errorText2").innerHTML = "Invalid! Access port is already in use of "+ get_interface_val +" Interface";
							document.getElementById("errorText2").className = "alert-box errorMsg";
							$sub("#errorText2").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if(flag == "edit") {
					if(interface_sipport != interface_access_val) {
						for ( var i=0; i < accesscount; i++) {
							if (interface_access_val == accessports[i]) {
								document.getElementById("errorText2").innerHTML = "Invalid! Access port is already in use of "+ get_interface_val +" Interface";
								document.getElementById("errorText2").className = "alert-box errorMsg";
								$sub("#errorText2").show().delay(2000).fadeOut();
								return false;
							}
						}
					}
				}
				if($sub("#REQ_TLS_CHK").is(":checked")) {
					$sub("#REQ_TLS_HID").val("1");
				} else {
					$sub("#REQ_TLS_HID").val("0");
				}
				if($sub("#VER_TLS_CHK").is(":checked")) {
					$sub("#VER_TLS_HID").val("1");
				} else {
					$sub("#VER_TLS_HID").val("0");
				}
				if($sub("#NAT_ENABLE").is(":checked")) {
					$sub("#NAT_ENABLE_HID").val("1");
				} else {
					$sub("#NAT_ENABLE_HID").val("0");
				}
				$sub('#sipheaderSel option').attr('selected', 'selected');
				sipcount = $sub('#sipheaderSel option').length;
				if(sipcount > 10) {
						document.getElementById("errorText5").innerHTML = "Invalid! SIP Headers shouldn't exceed 10.";
						document.getElementById("errorText5").className = "alert-box errorMsg";
						$sub('#errorText5').show().delay(2000).fadeOut();
						return false;
				}
				$sub('#sip_domain_sel option').attr('selected', 'selected');
				sipcount = $sub('#sip_domain_sel option').length;
				if(sipcount > 16) {
						document.getElementById("errorText6").innerHTML = "Invalid! SIP Domain shouldn't exceed 10.";
						document.getElementById("errorText6").className = "alert-box errorMsg";
						$sub('#errorText6').show().delay(2000).fadeOut();
						return false;
				}

				if($sub("#addsipprofile").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					$sub('#ip_port_list option').attr('selected', 'selected');
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
					parent.document.getElementById("container").src = "sip_profile.php";
				}
			}

			function setIPList() {
				var count = $sub('#ip_port_list option').length;
				if(count > 10) {
					document.getElementById("allowip_error").innerHTML = "Invalid! Allow IP:Port shouldn't exceed 10.";
					document.getElementById("allowip_error").className = "alert-box errorMsg";
					$sub('#allowip_error').show().delay(2000).fadeOut();
					return false;
                                }
				var ip_val = $sub("#ip_port_field").val();
				var delim_pos = ip_val.indexOf(":");
				if ( delim_pos == -1 ) {
					var any_check = ip_val.match(/^any|ANY$/g);
					if ( any_check == null ) {
						return false;
					}
				} else if ( delim_pos != -1 ) {
					var split_field = ip_val.split(':');
					if ( split_field.length < 2 ) {
						return false;
					}
					var ipaddress = split_field[0];
					var port = split_field[1];
					var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
					var match_ipaddr = ipaddr.exec(ipaddress);
					if ( match_ipaddr == null ) {
						return false;
					}
					if ( parseInt(parseFloat(port)) < 1 || parseInt(parseFloat(port)) > 65535 ) {
						return false;
					}
				}
				var allowedipports = ip_val;
				var select = document.getElementById('ip_port_list');
				var selectLen = select.options.length;
				var allowedipVal = allowedipports;
				var i = 0;
				var allowedip = document.createElement('option');
				allowedip.text = allowedipVal;
				allowedip.value = allowedipVal;
				for(i =0; i < selectLen; i++) {
					allowedipList = select.options[i].value;
					if(allowedipVal == allowedipList) {
						document.getElementById('ip_port_arr').focus();
						document.getElementById('ip_port').value = '';
						return false;
					}
				}
				if($sub.browser.msie) {
					select.add(allowedip);
					document.getElementById('ip_port_arr').value = '';
					document.getElementById('ip_port').value = '';
				} else {
					select.add(allowedip, null);
					document.getElementById('ip_port_arr').value = '';
					document.getElementById('ip_port').value = '';
				}
				return false;
			}

			function deleteIPList() {
				$sub("#ip_port_list option:selected").remove();
				$sub("#reset_default").attr("class", "btn-grey-wide-l");
			}

			function showIP() {
				var checknatpost_val = 1;
				var set = 0;
				var post_ipvalue;
				var iface = document.getElementById("interfaces_list").value;
				var nat = "";
				var itypeval = 0;
				for (i=0; i<devnamecount;i++) {
					if((devintername[i] == iface) && (devinterfacetype[i] == 1)) {
							nat = devnatsettings[i];
							itypeval = devinterfacetype[i];
							set =1;
					}
					if((devintername[i] == iface) && (devinterfacetype[i] == 0)) {
						itypeval = devinterfacetype[i];
						set =1;
					}
				}
				if(set == 0) {
					var virtualnatvalue= $sub.ajax({
						type : "POST",
						cache: false,
						data : {ip_iface : iface, natexternal_post:checknatpost_val},
						url : "show_iface_ip.php",
						global: false,
						async:false,
						success: function (natsettingsdata) {
								set = 1;
								itypeval = 1;
						}
					}).responseText;
					nat = virtualnatvalue;
				}
				if((itypeval == 1) && (nat != "NO NAT") && (set == 1)) { 
					if((flag == "edit") && (nat_enable == 1) && (interface_nat == iface)){
						document.getElementById("NAT_ENABLE").disabled = false;
						document.getElementById("NAT_ENABLE").checked = true;
						document.getElementById("NAT_ENABLE_HID").value = 1;
					} else {
						document.getElementById("NAT_ENABLE").disabled = false;
						document.getElementById("NAT_ENABLE").checked = false;
						document.getElementById("NAT_ENABLE_HID").value = 0;
					}
				} else {
					document.getElementById("NAT_ENABLE").disabled = true;
					document.getElementById("NAT_ENABLE").checked = false;
					document.getElementById("NAT_ENABLE_HID").value = 0;
				}
				var i;
				for (i=0; i<devnamecount;i++) {
					if(devintername[i] == iface) {
						post_ipvalue = devarray[i];
					}
				}
				var post_val = "1";
				$sub.ajax({
					type : "POST",
					cache: false,
					data : {ip_iface:iface, sip_post:post_val},
					url : "show_iface_ip.php",
					success: function (ipaddr) {
						document.getElementById("iface_addr").innerHTML = ipaddr;
					}
				});
			}

			function tlsload() {
				var flag = '<?php print $flag ?>';
				var proval = '<?php print $access_protocol ?>';	
				var req_tlsval = '<?php print $req_tls ?>';
				var ver_tlsval = '<?php print $ver_tls ?>';
				if(flag == "edit") {
					if(proval == "tls") {
						if(req_tlsval == 1) {
							document.getElementById("REQ_TLS_CHK").disabled = false;
							document.getElementById("VER_TLS_CHK").disabled = false;
							document.getElementById("REQ_TLS_CHK").checked = true;
							document.getElementById("REQ_TLS_HID").value = 1;
						} else {
							document.getElementById("REQ_TLS_CHK").disabled = false;
							document.getElementById("VER_TLS_CHK").disabled = true;
							document.getElementById("REQ_TLS_CHK").checked = false;
							document.getElementById("REQ_TLS_HID").value = 0;
							document.getElementById("VER_TLS_CHK").checked = false;
							document.getElementById("VER_TLS_HID").value = 0;
						}
						if(ver_tlsval == 1) {
							document.getElementById("VER_TLS_CHK").checked = true;
							document.getElementById("VER_TLS_HID").value = 1;
						} else {
							document.getElementById("VER_TLS_CHK").checked = false;
							document.getElementById("VER_TLS_HID").value = 0;
						}
					} else {
						document.getElementById("REQ_TLS_CHK").disabled = true;
						document.getElementById("VER_TLS_CHK").disabled = true;
						document.getElementById("REQ_TLS_CHK").checked = false;
						document.getElementById("VER_TLS_CHK").checked = false;
						document.getElementById("REQ_TLS_HID").value = 0;
						document.getElementById("VER_TLS_HID").value = 0;
					}
				}
				if(flag == "add") {
					document.getElementById("REQ_TLS_CHK").disabled = true;
					document.getElementById("VER_TLS_CHK").disabled = true;
					document.getElementById("REQ_TLS_CHK").checked = false;
					document.getElementById("VER_TLS_CHK").checked = false;
					document.getElementById("REQ_TLS_HID").value = 0;
					document.getElementById("VER_TLS_HID").value = 0;
				}
			}

			function showServerCerts() {
				var tranportOptions = document.getElementById("sip_protocol").value;
				if (tranportOptions == "tls") {
					document.getElementById("server_certs_opt").disabled = false;
				} else {
					document.getElementById("server_certs_opt").disabled = true;
					document.getElementById("server_certs_opt").selectedIndex = 0;
				}
				if (tranportOptions == "tls") {
					document.getElementById("REQ_TLS_CHK").disabled = false;
					document.getElementById("VER_TLS_CHK").disabled = true;	
					document.getElementById("REQ_TLS_CHK").checked = false;
					document.getElementById("VER_TLS_CHK").checked = false;
					document.getElementById("REQ_TLS_HID").value = 0;
					document.getElementById("VER_TLS_HID").value = 0;
				} else {
					document.getElementById("REQ_TLS_CHK").disabled = true;
					document.getElementById("VER_TLS_CHK").disabled = true;
					document.getElementById("REQ_TLS_CHK").checked = false;
					document.getElementById("VER_TLS_CHK").checked = false;
					document.getElementById("REQ_TLS_HID").value = 0;
					document.getElementById("VER_TLS_HID").value = 0;
				} 
			}

			function setreqtls() {
				if($sub("#REQ_TLS_CHK").is(":checked")) {
					document.getElementById("REQ_TLS_HID").value = 1;
					document.getElementById("VER_TLS_CHK").disabled = false;
					document.getElementById("VER_TLS_CHK").checked = false;
					document.getElementById("VER_TLS_HID").value = 0;
				} else {
					document.getElementById("REQ_TLS_HID").value = 0;
					document.getElementById("VER_TLS_CHK").disabled = true;
					document.getElementById("VER_TLS_CHK").checked = false;
					document.getElementById("VER_TLS_HID").value = 0;
				}
			}

			function setvertls() {
				if($sub("#REQ_TLS_CHK").is(":checked")) {
					document.getElementById("VER_TLS_HID").value = 1;
				} else {
					document.getElementById("VER_TLS_HID").value = 0;
				}
			}

			function copySipheaderToSelectedList() { 
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

			function copyAllSipheaderToSelectedList() {
				var i,j;
				var found = 0;
				var sipheaderLst =  document.getElementById('sipheader');
				var sipheaderSelLst =  document.getElementById('sipheaderSel');
				if(sipheaderLst.length <= 0) {
					alert("There are no sipheader in the list to be added.");
					return false;
				}
				removeAllSipheadersFromSelectedList();
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

			function removeAllSipheadersFromSelectedList() {
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
// for sip domain
			function copySipdomainToSelectedList() {
				var i,j,k;
				var sipdomainLst =  document.getElementById('sip_domain');
				var sipdomainSelLst =  document.getElementById('sip_domain_sel');
				var grpLstSelected = document.getElementById('sip_domain').value;
				if(sipdomainLst.value == '' && sipdomainLst.length > 0) {
					alert("Please select an option to add!");
					sipdomainLst.focus();
					return false;
				} else if(sipdomainLst.length <= 0) {
					alert("There are no sipheader in the list to be added.");
					return false;
				}
				for( j=0; j< sipdomainLst.options.length; j++) {
					if(sipdomainLst[j].selected == true) {
						if(sipdomainSelLst.options.length == 0) {
							var selListTxt = sipdomainLst.options[j].text;
							var selListVal = sipdomainLst.options[j].value;
							var selGrpList = document.createElement('option');
							selGrpList.text = selListTxt;
							selGrpList.value = selListVal;
							sipdomainSelLst.add(selGrpList,sipdomainSelLst.options[null]);
							try {
								sipdomainSelLst.add(selGrpList,sipdomainSelLst.options[null]);
							}
							catch(ex) {
								sipdomainSelLst.add(selGrpList,sipdomainSelLst.options[null]);
							}
						}
						for ( k = 0; k < sipdomainSelLst.options.length; k++ ) {
							var splitCopiedArr = sipdomainSelLst.options[k].value.split("/");
							if ( sipdomainLst.options[j].value == splitCopiedArr[0] ) {
								break;
							}
							if(k == sipdomainSelLst.options.length - 1) {
								var selListTxt = sipdomainLst.options[j].text;
								var selListVal = sipdomainLst.options[j].value;
								var selGrpList = document.createElement('option');			
								selGrpList.text = selListTxt;
								selGrpList.value = selListVal;
								sipdomainSelLst.add(selGrpList,sipdomainSelLst.options[null]);
								try {
									sipdomainSelLst.add(selGrpList,sipdomainSelLst.options[null]);
								}
								catch(ex) {
									sipdomainSelLst.add(selGrpList,sipdomainSelLst.options[null]);
								}
							}
						}
					}
				}
			}

			function copyAllSipdomainToSelectedList() {
				var i,j;
				var found = 0;
				var sipdomainLst =  document.getElementById('sip_domain');
				var sipdomainSelLst =  document.getElementById('sip_domain_sel');
				if(sipdomainLst.length <= 0) {
					alert("There are no sipheader in the list to be added.");
					return false;
				}
				removeAllSipdomainFromSelectedList();
				for ( i = 0 ; i < sipdomainLst.options.length; i++ ) {
					for ( j = 0; j < sipdomainSelLst.options.length; j++ ) {
						if ( sipdomainLst.options[i].value == sipdomainSelLst.options[j].value ) {
							found = 1;
							return false;
						}
					}
					if ( found !=  1 ) {
						var newOption = document.createElement("option");
						newOption.text = sipdomainLst.options[i].text;
						newOption.value = sipdomainLst.options[i].value;
						sipdomainSelLst.add(newOption,sipdomainSelLst.options[null]);
					}
				}
			}

			function removeSipdomainFromSelectedList() {
				var i,j;
				var sipdomainSelLst =  document.getElementById('sip_domain_sel');
				var sipDomainSelLstSelcted =  document.getElementById('sip_domain_sel').value;
				var GrpListLen = sipdomainSelLst.options.length;
				var grpSelSI = sipdomainSelLst.selectedIndex;
				for(j = 0 ; j < GrpListLen; j++) {
					if(grpSelSI == -1) {
						document.getElementById('sip_domain_sel').focus();
						return false;
					}
				}
				if(sipdomainSelLst.options.length < 1) {
					return false;
				}
				if(sipDomainSelLstSelcted == '') {
					return false;
				}
				for (i = sipdomainSelLst.options.length - 1; i >= 0; i--) {
					if (sipdomainSelLst.options[i].selected) {
						sipdomainSelLst.remove(i);
					}
				}
				return false;
			}

			function removeAllSipdomainFromSelectedList() {
				var i;
				var sipdomainSelLst =  document.getElementById('sip_domain_sel');
				if(sipdomainSelLst.options.length < 1) {
					return false;
				}
				for (i = sipdomainSelLst.options.length - 1; i >= 0; i--) {
					if (sipdomainSelLst.options[i]) {
						sipdomainSelLst.remove(i);
					}
				}
				return false;
			}
// for sip domain end
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="addsipprofile" name="addsipprofile" class="popup-form" action="add_edit_sip_profile.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
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
					<!--			<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="15" maxlength="16" /> 
LEN_FIX_1007 -->
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="15" maxlength="11" />
								<img id="sip_profile_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<input type="text" size="48" maxlength="64" value="<?php print $desc; ?>" id="COMMENTS" class="ipBox" name="comments" />
								<img id="sip_profile_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Interfaces</td>
							<td width="20"></td>
							<td>
								<select class="ipDrpBox" id="interfaces_list" name="interfaces" onkeyup="showIP();" onkeydown="showIP();" onchange="showIP();" > 
				
							<!--	<?php
									for ($j=0; $j < count($interfaces); $j++) {
										$select_str = "";
										if ( $interfaces[$j] == $interface )
											$select_str = "selected";
										if($interfacenames[$j] != "") {
								?>
									<option value="<?php print $interfaces[$j]; ?>" <?php print $select_str;?>><?php print $interfacenames[$j]; ?></option>
								<?php }} ?> -->
						<?php
							$set =1;
							if(count($interfaces) == 0) {
								$set =1;
							} else {
								for($i=0;$i<count($interfaces);$i++) {
									$select_str = "";
									if(($flag == "edit") && ( $interfaces[$i] == $interface)) {
										$select_str = "selected";
										$set = 0;

						?>
								<option value="<?php echo $interfaces[$i];?>" <?php print $select_str;?> > <?php echo $interfaces[$i];?> </option>
						<?php
									} else {
										if($interfaces[$i] != "") {
											$set = 0;
						?>
											<option value="<?php echo $interfaces[$i];?>"> <?php echo $interfaces[$i];?> </option>
						<?php
										}
									}
								}
							}

							if($set == 1) {
						?>
								<option value="--No Interfaces--">--No Interfaces--</option>
						<?php
							}
						?>
								</select>
								<img id="sip_profile_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="iface_addr" style="color:#000"></span>
								<span id="errorText1"></span>
								<span>&nbsp;&nbsp;&nbsp;&nbsp;NAT</span>
								<input type="checkbox" id="NAT_ENABLE" name="NAT_ENABLE" />				
								<img id="sip_profile_ttip_013" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">SIP Protocol/Port</td>
							<td width="20"></td>
							<td colspan="2">
								<table>
									<tr>
										<td>
											<select class="ipDrpBox" id="sip_protocol" name="sip_protocol" onchange="showServerCerts();" onkeyup="showServerCerts();" onkeydown="showServerCerts();">
								<?php
								for ( $j = 0; $j < count($transport_str_array); $j++ ) {
									$select_str = "";
									if ( $transport_str_array[$j] == $access_protocol )
										$select_str = "selected";
								?>
										<option value="<?php print $transport_str_array[$j];?>" <?php print  $select_str;?> ><?php print  $transport_str_array[$j];?></option>
								<?php } ?>
											</select>
											<img id="sip_profile_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										<td>
											<input type="text" size="4" maxlength="5" value="<?php print $access_port; ?>" class="ipBox" id="sip_port" name="sip_port" />
											<img id="sip_profile_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											<span id="errorText2"></span>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr> <td colspan ="3">
							<table> <tr>
								<td width ="125px"><label for="req_tls">Required TLS</label> </td>
								<td width ="80px">
								<input type="checkbox" id="REQ_TLS_CHK" name="REQ_TLS_CHK" onclick="setreqtls()"/>
								<img id="sip_profile_ttip_014" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
	
								</td>
								<td width ="100px"><label for="ver_tls">Verified TLS</label>
								</td>
								<td width ="80px">
									<input type="checkbox" id="VER_TLS_CHK" name="VER_TLS_CHK" onclick="setvertls()"/>
									<img id="sip_profile_ttip_015" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td valign="top">Server Certs</td>
							<td width="20"></td>
							<td colspan="2">
								<table>
									<tr>
										<td>
											<select class="ipDrpBox" id="server_certs_opt" name="server_certs" style="width: 140px;">
												<option value="None">None</option>
											<?php
											for ($i=0; $i < $serverCertCount; $i++) {
												$select_str = "";
												$certArray = explode(',',$serverCertList[$i]);
												$cert_name =  $certArray[0];
												$cert_type = $certArray[1];
												$cert_expire = $certArray[2];
												$cert_regen = $certArray[3];
												if ($cert_name != "" && ( $cert_type == 1 || $cert_type == 4) ) {
													if ($cert_name == $server_certs_val)
														$select_str = "selected";
											?>
												<option value="<?php print $cert_name;?>" <?php print  $select_str;?> ><?php print $cert_name;?></option>
											<?php
												}
											}
											?>
											</select>
											<img id="sip_profile_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr style="display:none">
							<td valign="top">NAT Settings/IP Address</td>
							<td width="20"></td>
							<td colspan="2">
								<table>
									<tr>
										<td>
											<select class="ipDrpBox" id="nat_settings" name="nat_settings" onchange="showStunStatic();" onkeyup="showStunStatic();" onkeydown="showStunStatic();">
											<?php
											for ( $i = 0; $i < $count_nat_settings; $i++ ) {
												$select_str = "";
												if ( $get_nat_settings[$i] == $nat_settings )
													$select_str = "selected";
											?>
												<option value="<?php print $get_nat_settings[$i];?>" <?php print  $select_str;?> ><?php print $get_nat_settings[$i];?></option>
											<?php
											}
											?>
											</select>
											<img id="sip_profile_ttip_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
										<td>
											<input type="text" size="15"  maxsize="256" value="<?php print $ip_address; ?>" class="ipBox" id="IP_ADDRESS" name="ip_address" />
											<img id="sip_profile_ttip_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr style="display:none">
							<td valign="top">Domain</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" maxlength="256" size="" value="<?php print $domain; ?>" name="domain" id="DOMAIN" />
								<img id="sip_profile_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<!--tr>
							<td valign="top">Enable Keepalive</td>
							<td width="20"></td>
							<?php
								if ($enable_keepalive == "1") {
									$keepalive_checked = "checked";
								} else {
									$keepalive_checked = "";
								}
							?>
							<td>
								<input onchange="enable_keep_alive();" type="checkbox" <?php print $keepalive_checked; ?> name="enable_keepalive" id="ENABLE_KEEPALIVE" />
								<img id="sip_profile_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Keepalive Interval</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" maxlength="3" size="2" value="<?php print $keepalive_int; ?>" name="keepalive_int" id="KEEPALIVE_INT" />
								<img id="sip_profile_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr-->
						<tr>
							<td valign="top">SIP TOS</td>
							<td width="20"></td>
							<td>
								<input type="text" maxlength="2" size="2" value="<?php print $sip_tos; ?>" class="ipBox" name="sip_tos" id="SIP_TOS" />
								<img id="sip_profile_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
<!-- sip Domain Profiles -->
						<tr id="sipdomain">
						<td valign="center">Sip Domain</td>
						<td width="20"></td>
						<td colspan="4">
						<table>
							<tr>
								<td>
									<select size="5" style="width: 160px; font-size: 12px" name="sip_domain[]" id="sip_domain" multiple="multiple">
									<?php
										for ( $k = 0; $k < count($sipdomain_profiles); $k++ ) {
									?>
											<option value="<?php print $sipdomain_profiles[$k];?>"><?php print  $sipdomain_profiles[$k];?></option>
									<?php
										}
									?>
									</select>
									<img id="sip_profile_ttip_016" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
									<td>
									<table style="height:auto; width:auto;">
										<tr><td><a class="btn-usergrp-wide" onclick="copySipdomainToSelectedList();"><span> > </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="copyAllSipdomainToSelectedList();"><span> >> </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeSipdomainFromSelectedList();"><span> < </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeAllSipdomainFromSelectedList();"><span> << </span></a></td></tr>
									</table>
									</td>
									<td>
									<select name="sip_domain_sel[]" size="5" style="width: 160px; font-size: 12px" id="sip_domain_sel" multiple="multiple">
									<?php
									for ( $k=0; $k < count($get_sip_domain); $k++) {
										if ( $get_sip_domain[$k] != "" ) {
									?>
										<option value="<?php print $get_sip_domain[$k]; ?>" <?php print "selected"; ?>>
										<?php print $get_sip_domain[$k]; ?></option>
									<?php
										}
									}
									?>
									</select>
									<img id="sip_profile_ttip_017" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
									<td valign="top" style="display:none">
											<table>
												<tr>
													<td style="padding-top: 43px;"><input class="btn-up" type="button" value="Up1"></td>
												</tr>
												<tr>
													<td style="padding-top: 5px;"><input class="btn-down" type="button" value="Down1"></td>
												</tr>
											</table>
									</td></tr>
									</table></td>
								</tr>
								<tr><td height="5" ></td></tr>
								<tr align="right"> <td colspan="3" aligh="center"> <span id="errorText6"></span> </td> </tr>
 <!-- SIP Domain Profiles ends-->
						<tr>
							<td valign="center">Allow (IP:PORT)</td>
							<td width="20"></td>
							<td colspan="2">
								<table>
									<tr>
										<td><input type="text" class="ipBox" maxlength="21" value="" name="ip_port" id="ip_port_field"  size="14" /></td>
										<td><a class="btn-grey-wide-l-2" onclick="setIPList();" ><span>Add</span></a></td>
										<td><span id="allowip_error"></span></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
									<td>
										<table>
											<tr>
												<td>
													<select id="ip_port_list" size="5" style="width: 160px; font-size: 12px" name="ip_port_arr[]" multiple="multiple" >
										<?php
										for ($k = 0; $k < count($allowed_ip_ports); $k++) {
											if ( $allowed_ip_ports[$k] != NULL) {
										?>
											<option value="<?php print $allowed_ip_ports[$k]; ?>" <?php print "selected"; ?>><?php print $allowed_ip_ports[$k]; ?></option>
										<?php
											}
										}
										?>
												</select>
												</td>
												<input type="hidden" id="NTPSERVER" name="NTPSERVER" value="" />
												<td style="vertical-align: top;margin-left: 2px;"><a class="btn-grey-wide-l-2" onclick="deleteIPList()"><span>Delete</span></a></td>
											</tr>
										</table>
									</td>
						</tr>
<!-- sip headers -->
						<tr>
						<td valign="center">SIP Headers</td>
						<td width="20"></td>
						<td colspan="4">
						<table>
							<tr>
								<td>
									<select size="5" style="width: 160px; font-size: 12px" name="sipheader[]" id="sipheader" multiple="multiple">
									<?php
									for ( $k = 0; $k < count($sipheader_profiles); $k++ ) {
									?>
										<option value="<?php print $sipheader_profiles[$k];?>"><?php print  $sipheader_profiles[$k];?></option>
									<?php
									}
									?>
									</select>
									<img id="sip_profile_ttip_018" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								<td>
									<table>
										<tr><td><a class="btn-usergrp-wide" onclick="copySipheaderToSelectedList();"><span> > </span></a></td></tr>
										<tr style="display:none"><td><a class="btn-usergrp-wide" onclick="copyAllSipheaderToSelectedList();"><span> >> </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeSipheaderFromSelectedList();"><span> < </span></a></td></tr>
										<tr style="display:none"><td><a class="btn-usergrp-wide" onclick="removeAllSipheadersFromSelectedList();"><span> << </span></a></td></tr>
									</table>
								</td>
								<td>
									<select name="sipheaderSel[]" size="5" style="width: 160px; font-size: 12px" id="sipheaderSel" multiple="multiple">
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
									<img id="sip_profile_ttip_019" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
								</table>
								</tr>
								<tr><td height="5" ></td></tr>
								<tr align="right"> <td colspan="3" aligh="center"> <span id="errorText5"></span> </td> </tr>
<!-- sip headers ends-->
<!-- LB Group profile -->
							<tr style="display:none">
							<td valign="center">LB Rules</td>
							<td width="20"></td>
							<td colspan="4">
							<table>
								<tr>
									<td>
										<select size="5" style="width: 160px; font-size: 12px" name="lbrule[]" id="lbrule" multiple="multiple">
										<?php
											for ( $k = 0; $k < count($lbrule_profiles); $k++ ) {
										?>
												<option value="<?php print $lbrule_profiles[$k];?>"><?php print  $lbrule_profiles[$k];?></option>
										<?php
											}
										?>
										</select>
										<img style="vertical-align: top;" id="lcr_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								<td>
									<table>
										<tr><td><a class="btn-usergrp-wide" onclick="copyLbruleToSelectedList();"><span> > </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="copyAllLbruleToSelectedList();"><span> >> </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeLbruleFromSelectedList();"><span> < </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeAllLbruleFromSelectedList();"><span> << </span></a></td></tr>
									</table>
								</td>
								<td>
									<select name="lbruleSel[]" size="5" style="width: 160px; font-size: 12px" id="lbruleSel" multiple="multiple">
									<?php
									for ( $k=0; $k < count($get_lbrule); $k++) {
										if ( $get_lbrule[$k] != "" ) {
									?>
											<option value="<?php print $get_lbrule[$k]; ?>" <?php print "selected"; ?>> <?php print $get_lbrule[$k]; ?></option>
									<?php
										}
									}
									?>
									</select>
								</td>
									</table>
								</tr>
								<tr><td height="5" ></td></tr>
								<tr align="right"> <td colspan="3" aligh="center"> <span id="errorText5"></span> </td> </tr>
<!-- LB Group profile -->
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="WHITELIST_RULE_LIST" id="WHITELIST_RULE_LIST" value="" />
					<input type="hidden" id="REQ_TLS_HID" name="REQ_TLS_HID" value="0" />
					<input type="hidden" id="NAT_ENABLE_HID" name="NAT_ENABLE_HID" value="0" />
					<input type="hidden" id="VER_TLS_HID" name="VER_TLS_HID" value="0" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" id="copyAllowedIPPorts" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
	<script type="text/javascript">
		showIP();
		initValidation();
		showServerCerts();
	</script>
</html>
