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
	include_once("Model/devicelist_settings_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/sys_calls.php");

	$devicelist_settings_config = DevicelistSettingsModel::getInstance();
	$get_nat_settings = $devicelist_settings_config->getNatSettings();
	$count_nat_settings = count($get_nat_settings);
	$checkarch =SysCalls::getSysArch();
	$res = 0;
	$page_title = "Create Network Interface Profile";
	$flag = "add";
	$inter_name = "";
	$i_type = "";
	$ip_type = "";
	$ip_addr = "";
	$netmask = "";
	$dev_id = "";
	$gateway ="";
	$dns= "";
	$sshenable="";
	$sshport="";
	$icmp= "";
	$postiptype="";
	$nat_settings="";
	$ip_address="";
	$def_gwt=0;
	$createdDataNames = array();
	$devicelistNames = $devicelist_settings_config->getNames();
	$usedinterfacecount = 0;
	$devicelistids = $devicelist_settings_config->getdeviceids();
	$devlist = $devicelist_settings_config->getdevlist();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = $val;
		$flag = "edit";
		$page_title = "Edit Network Interface Profile";
		$row1 = $devicelist_settings_config->getdevicelistSettings($id);
		$delvirtipcheck = $devicelist_settings_config->delvirtualipcheck($id);
		$delvlancheck = $devicelist_settings_config->delvlancheck($id);
		$delroutercheck = $devicelist_settings_config->delroutercheck($id);
		$delportforwcheck = $devicelist_settings_config->delportforwcheck($id);
		$delmediacheck = $devicelist_settings_config->delmediacheck($id);
		$delsipprcheck = $devicelist_settings_config->delsipprcheck($id);
		if($row1) {
			$inter_name = $row1[DevicelistSettingsModel::$INTER_NAME];
			$i_type = $row1[DevicelistSettingsModel::$I_TYPE];
			$ip_type = $row1[DevicelistSettingsModel::$IP_TYPE];
			$ip_addr = $row1[DevicelistSettingsModel::$IP_ADDR];
			$netmask = $row1[DevicelistSettingsModel::$NETMASK];
			$dev_id = $row1[DevicelistSettingsModel::$DEV_ID];
			$gateway = $row1[DevicelistSettingsModel::$GATEWAY];
			$dns= $row1[DevicelistSettingsModel::$DNS];
			$sshenable= $row1[DevicelistSettingsModel::$SSHENABLE];
			$sshport= $row1[DevicelistSettingsModel::$SSHPORT];
			$icmp= $row1[DevicelistSettingsModel::$ICMP];
			$def_gwt =$row1[DevicelistSettingsModel::$DEF_GWT];
			$nat_settings=$row1[DevicelistSettingsModel::$NAT_SETTINGS];
			$ip_address=$row1[DevicelistSettingsModel::$STUN_STATIC_IPADDR];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['sshenable'] == "on")
			$sshenable = "1";
		else
			$sshenable = "0";
		if($_POST['icmp'] == "on")
			$posticmp = "1";
		else
			$posticmp = "0";
		if($_POST['ip_type'] == "")
			$postiptype = 0;
		else
			$postiptype = $_POST['ip_type'];
		if ($_POST['submit_action'] == "2") {
			$devicelist_settings_config->adddevicelistSettings(trim($_POST['inter_name']), 
							trim($_POST['i_type']), $postiptype, 
							$_POST['ip_addr'], $_POST['netmask'], 
							trim($_POST['dev_id']), trim($_POST['gateway']), 
							trim($_POST['dns']), $sshenable,
							trim($_POST['sshport']), $posticmp, 
							$_POST['DEF_GWT_HID'], $_POST['nat_settings'], 
							$_POST['ip_address']);
			$devicelist_settings_config->createActionList(
							DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG, 
							"devicelist Profile Added", 
							DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG_STR, 
							"");
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {
			$natsettingsval = $_POST['nat_settings'];
			$ipaddress = $_POST['ip_address'];
			if($_POST['i_type'] != 1) {
				$natsettingsval="NO NAT";
				$ipaddress= "";
			}
			$devicelist_settings_config->editdevicelistSettings(trim($_POST['inter_name']), 
							trim($_POST['i_type']), $postiptype, 
							$_POST['ip_addr'], $_POST['netmask'], 
							trim($_POST['dev_id']), trim($_POST['gateway']),
							trim($_POST['dns']), $sshenable, 
							trim($_POST['sshport']), 
							$posticmp, $_POST['DEF_GWT_HID'], 
							$natsettingsval, $ipaddress);
			$devicelist_settings_config->createActionList(
							DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG, 
							"Devicelist Profile edited", 
							DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG_STR, 
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
			var defvalue = "<?php echo $def_gwt ?>";
			var cdevlistArr = new Array();
			var devidArr = new Array();
			var macidArr = new Array();
			var devtypeArr = new Array();
			var tcodearray = new Array();
			var dumyArrintextinterface = new Array();
			var dumyArrtransinterface = new Array();
			var dumyArrintextmacid = new Array();
			var dumyArrtransmacid = new Array();
			var cdevlistCount = 0;
			var devidCount = 0;
			var devtypeCount = 0;
			var tcodecount = 0;
			var checkeditcount = 0;
			<?php
				foreach($devlist as $key=>$val) {
			?>
					cdevlistArr.push('<?php print $val ?>');
			<?php
				}
			?>
			var cdevlistCount = cdevlistArr.length;
			<?php
				if($devicelistids) {
					$i = 0;
					while($row = $devicelistids->fetchArray(SQLITE3_ASSOC)) { 
					$i++;
			?>
						devidArr.push('<?php echo $row[DevicelistSettingsModel::$DEV_ID]; ?>');
						devtypeArr.push('<?php echo $row[DevicelistSettingsModel::$I_TYPE]; ?>');
						macidArr.push('<?php echo $row[DevicelistSettingsModel::$MAC_ID]; ?>');
						tcodearray.push('<?php echo $row[DevicelistSettingsModel::$T_CODE]; ?>');
			<?php
					}
				}
			?>
			var devidCount = macidArr.length;
			var devtypeCount = devtypeArr.length;
			var tcodecount = tcodearray.length;

			for(var i=0; i < tcodecount; i++) {
				if((tcodearray[i] != 1)) {
					dumyArrintextinterface.push(devidArr[i]);
					dumyArrintextmacid.push(macidArr[i]);
				} else {
					dumyArrtransinterface.push(devidArr[i]);
					dumyArrtransmacid.push(macidArr[i]);
				}
			}
			var countintext = dumyArrintextinterface.length;
			var counttrans = dumyArrtransinterface.length;

			$sub(document).ready(function() {
				initValidation();
				getPageTips("devicelist_settings_ttip");
				setTooltip();
				setIType();
				showStunStatic();
				if(flag == "edit") {
					if(defvalue == 1) {
						document.getElementById("DEF_GWT_CHK").disabled = true;
						document.getElementById("DEF_GWT_CHK").checked = true;
						document.getElementById("DEF_GWT_HID").value = 1;
					}
				}
			});

			function initValidation() {
				$sub("#inter_name").attr("data-validation-engine", "validate[required,custom[alphaNumeric], minSize[3], maxSize[16]]");
				$sub("#ip_addr").attr("data-validation-engine", "validate[required,custom[ipv4]]");
				$sub("#netmask").attr("data-validation-engine", "validate[required,custom[netmask]]");
				$sub("#gateway").attr("data-validation-engine", "validate[custom[ipv4]]");
				$sub("#dns").attr("data-validation-engine","validate[custom[ipv4]]"); 
				$sub("#sshport").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[65535]]");

				$sub('#nat_settings').on('change', function() {{
					var nat_settings  = $sub("#nat_settings option:selected").val();
					if( nat_settings == "STUN" ) {
						$sub("#ip_address").attr("data-validation-engine", "validate[required,custom[IP_DOMAIN]]");
					}
					if( nat_settings == "STATIC" ) {
						$sub("#ip_address").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
				}});
				$sub(document).ready(function() {{
					var nat_settings  = $sub("#nat_settings option:selected").val();
					if( nat_settings == "STUN" ) {
						$sub("#ip_address").attr("data-validation-engine", "validate[required,custom[IP_DOMAIN]]");
					}
					if( nat_settings == "STATIC" ) {
						$sub("#ip_address").attr("data-validation-engine", "validate[required,custom[ipv4]]");
					}
				}});
				$sub("#adddevicelistsettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function checkinlist(valmac, valdev) {
				var flag = "<?php echo $flag ?>";
				for(j=0;j<cdevlistCount;j++) {
					if(cdevlistArr[j] == valmac) {
						if((flag == "edit") && (valmac == "<?php echo $dev_id ?>")) {
							return 1;
						} else {
							return 0;
						}
					}
				}
				return 1;
			}

			function setIType() {
				var formObj = document.forms[0];
				var itypevalue = formObj.i_type.value;
				var setdev1 = 0;
				var setdev2 = 0;
				var setdev3 = 0;
				var setdev4 = 0;
				var flag = "<?php echo $flag ?>";
				var usedid = "<?php echo $usedinterfacecount; ?>";

				if(itypevalue == 1) {
					$sub("#natsettings").show(500);
				} else {
					$sub("#natsettings").hide();
				}
				document.getElementById('dev_id').innerHTML = "";
				if(itypevalue != 2) {
					document.getElementById("DEF_GWT_CHK").disabled = false;
					document.getElementById("DEF_GWT_CHK").checked = false;
					document.getElementById("DEF_GWT_HID").value = 0;
				} else {
					document.getElementById("DEF_GWT_CHK").disabled = true;
					document.getElementById("DEF_GWT_CHK").checked = false;
					document.getElementById("DEF_GWT_HID").value = 0;
				}
				if((itypevalue != 2)) {
					if(countintext == 0 ) {
						setdev1 = 1;
					} else {
						for(var i=0; i < countintext; i++) {
							if(checkinlist(dumyArrintextmacid[i], dumyArrintextinterface[i]) == 1) {
								var x = document.getElementById("dev_id");
								var option = document.createElement("option");
								option.text = dumyArrintextinterface[i] +" ("+ dumyArrintextmacid[i] +")";
								option.value = dumyArrintextmacid[i];
								x.add(option);
								setdev2 = 1;
							}
						}
					}
					if((setdev1 == 1) || (setdev2 == 0)) {
						var x = document.getElementById("dev_id");
						var option = document.createElement("option");
						option.text = option.value = "NONE";
						x.add(option);
					}
				} else {
					if(counttrans == 0 ) {
						setdev3 = 1;
					} else {
						for(var i=0; i < counttrans; i++) {
							if(checkinlist(dumyArrtransmacid[i], dumyArrtransinterface[i]) == 1) {
								var x = document.getElementById("dev_id");
								var option = document.createElement("option");
								option.text = dumyArrtransinterface[i] +" ("+ dumyArrtransmacid[i] +")";
								option.value = dumyArrtransmacid[i];
								x.add(option);
								setdev4 = 1;
							
							}
						}
					}
					if((setdev3 == 1) || (setdev4 == 0)) {
						var x = document.getElementById("dev_id");
						var option = document.createElement("option");
						option.text = option.value = "NONE";
						x.add(option);
					}
				}
				if((flag == "edit")) {
					formObj.i_type.disabled = true;
				}
				setdevid();
			}

			function setIType1() {
				var flag = "<?php echo $flag ?>";
				var formObj = document.forms[0];
				var itypevalue = formObj.i_type.value;
				var dumyArr0 = new Array();
				var selectvalue = "";
				document.getElementById('dev_id').innerHTML = "";
				if(devidCount == 0) {
					var x = document.getElementById("dev_id");
					var option = document.createElement("option");
					option.text = option.value = "NONE";
					x.add(option);
				} else {
					var eset = 0;
					for(var i=0; i < devtypeCount; i++) {
						if(devtypeArr[i] == itypevalue) {
							var set = 0;
							for(j=0;j<cdevlistCount;j++) {
								if(cdevlistArr[j] == macidArr[i]) {
									set = 1;
									if(flag == "edit") {
										if(macidArr[i] == "<?php echo $dev_id ?>" ) {
											eset = 1;
											selectvalue = macidArr[i];
											selecttext = devidArr[i] +" ("+ macidArr[i] +")";
										}
									}
								}
							}
							if(set == 0) {
								eset = 1;
								dumyArr0.push(macidArr[i]);
								var x = document.getElementById("dev_id");
								var option = document.createElement("option");
								option.text = devidArr[i] +" ("+ macidArr[i] +")";
								option.value = macidArr[i];
								x.add(option);
							}
						}
					}
					if(eset == 0) {
						var x = document.getElementById("dev_id");
						var option = document.createElement("option");
						option.text = option.value = "NONE" ;
						x.add(option);
					}
					if(flag == "edit") {
						var x = document.getElementById("dev_id");
						var option = document.createElement("option");
						option.text = selecttext;
						option.value = selectvalue;
						x.add(option,0);
						x.options[0].selected = true;
						formObj.i_type.disabled = true;
					}
					setdevid();
				}
			}

			function setdevid() {
				var formObj = document.forms[0];
				var devidselected = formObj.dev_id.value;
				var macidselecarray = new Array();
				for(var i =0; i<tcodecount;i++) {
					if(tcodearray[i] == "1") {
						macidselecarray.push (macidArr[i]);
					}
					if(tcodearray[i] == "0") {
						formObj.ip_type.disabled = false;
						formObj.gateway.disabled = false;
					}
				}
				if (flag == "add")
				formObj.ip_type.value = 0;
				setIPType();
				var macidseleccount = macidselecarray.length;
				for(var i =0;i<macidseleccount;i++) {
					if(macidselecarray[i] == devidselected ) {
						formObj.ip_type.value = "0";
						formObj.ip_addr.disabled = false;
						formObj.netmask.disabled = false;
						formObj.dns.disabled = false;
						formObj.ip_type.disabled = true;
						formObj.gateway.disabled = true;
						formObj.gateway.value = "";
					}
				}
			}

			function setIPType() {
				var formObj = document.forms[0];
				var tmp = formObj.ip_type.value;
				var itypevalue = formObj.i_type.value;
				if (tmp == "0" ) {
					formObj.ip_addr.disabled = false;
					formObj.netmask.disabled = false;
					formObj.gateway.disabled = false;
					formObj.dns.disabled = false;
				} else {
					formObj.ip_addr.disabled = true;
					formObj.ip_addr.value = "";
					formObj.netmask.disabled = true;
					formObj.netmask.value = "";
					formObj.gateway.disabled = true;
					formObj.gateway.value = "";
					formObj.dns.disabled = true;
					formObj.dns.value = "";	
				}
				if((tmp == "1") || (itypevalue == 2)) {
					document.getElementById("DEF_GWT_CHK").disabled = true;
					document.getElementById("DEF_GWT_CHK").checked = false;	
					document.getElementById("DEF_GWT_HID").value = 0; 
				} else {
					document.getElementById("DEF_GWT_CHK").disabled = false;
					document.getElementById("DEF_GWT_CHK").checked = false;	
					document.getElementById("DEF_GWT_HID").value = 0; 
				}
			}

			function enableSSH() {
				var ssh_enable = document.getElementById("sshenable").checked;
				if( ssh_enable == false ) {
					document.getElementById("sshport").disabled = true;
					document.getElementById("sshport").value = "";
				} else {
					document.getElementById("sshport").disabled = false;
				}
			}

			function gatewaychange() {
				var formObj = document.forms[0];
				var gatewayval = formObj.gateway.value;
				var gatewaycount = gatewayval.length;
				if(gatewaycount != 0 || gatewayval != "") {
					formObj.DEF_GWT_CHK.disabled = false;
					formObj.DEF_GWT_CHK.checked = false;
					formObj.DEF_GWT_HID.value = 0;
				} else {
					formObj.DEF_GWT_CHK.disabled = true;
					formObj.DEF_GWT_CHK.checked = false;
					formObj.DEF_GWT_HID.value = 0;
				}
			}

			function setdefgateway() {
				var def_enable = document.getElementById("DEF_GWT_CHK").checked;
				var gatewayvalue = document.getElementById("gateway").value;
				if(gatewayvalue != "") {
					if( def_enable == true ) {
						document.getElementById("DEF_GWT_CHK").disabled = true;
						document.getElementById("DEF_GWT_HID").value = 1;
					} else {
						document.getElementById("DEF_GWT_CHK").disabled = false;
						document.getElementById("DEF_GWT_HID").value = 0;
					}
				} else {
					document.getElementById("DEF_GWT_CHK").checked = false;
				}
			}

			function showStunStatic() {
				var natSettings = document.getElementById("nat_settings").value;
				if ( natSettings== "NO NAT") {
					document.getElementById("ip_address").disabled = true;
					document.getElementById("ip_address").value = "";
				} else {
					document.getElementById("ip_address").disabled = false;
				}
			}

			function submitData() {
				var gatewayvalue = document.getElementById("gateway").value;
				var flag = "<?php echo $flag ?>";
				if ( flag == "add") {
					var mediaArr = new Array();
					var get_media_val = document.getElementById("inter_name").value;
					var post_media_val = get_media_val.trim();
					<?php
						foreach($devicelistNames as $key=>$val) {
					?>
							mediaArr.push('<?php print $val ?>');
					<?php
						}
					?>
					var mediaCount = mediaArr.length;
					for ( var i=0; i < mediaCount; i++) {
						if (post_media_val == mediaArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg"
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}

				if(flag == "add" || flag == "edit") {
					var formObj = document.forms[0];
					var val = formObj.dev_id.value;
						if(val == "NONE") {
							document.getElementById("errorText1").innerHTML = "Invalid! NO INTERFACE.";
							document.getElementById("errorText1").className = "alert-box errorMsg"
							$sub("#errorText1").show().delay(2000).fadeOut();
							return false;
						}
				}

				if($sub("#DEF_GWT_CHK").is(":checked")) {
					if(gatewayvalue != "") {
						$sub("#DEF_GWT_HID").val("1");
					} else {
						document.getElementById("errorText2").innerHTML = "Gateway is required to set as default.";
						document.getElementById("errorText2").className = "alert-box errorMsg";
						$sub("#errorText2").show().delay(2000).fadeOut();
						return false;
					}
				} else {
					$sub("#DEF_GWT_HID").val("0");
				}

				if($sub("#adddevicelistsettings").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add") {
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					formObj.i_type.disabled=false;
					formObj.dev_id.disabled=false;
					formObj.ip_type.disabled=false;
					formObj.ip_address.disabled=false;
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
					parent.document.getElementById("container").src = "devicelist_setting.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 285px">
			<form id="adddevicelistsettings" name="adddevicelistsettings" class="popup-form" action="add_edit_devicelist_settings.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
				<div id="tabdevicelist" class="tab_content">
					<table>
						<tr>
							<td>Interface name</td>
							<td width="20"></td>
							<td colspan="4">
								<input type="text" class="ipBox" id="inter_name" name="inter_name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $inter_name ?>" size="10" maxlength="16" />
								<img id="devicelist_settings_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Interface type</td>
							<td width="20"></td>
							<td colspan="4">
								<select name="i_type" id="i_type" class="ipDrpBox" onchange="setIType()" onkeyup="setIType() " onkeydown="setIType()">
									<option value="0" <?php if($i_type == 0) echo "selected" ?>>Internal Interface</option>
									<option value="1" <?php if($i_type == 1) echo "selected" ?>>External Interface</option>
									<option value="2" <?php if($i_type == 2) echo "selected" ?>>Transcoding Interface</option>
								</select>
								<img id="devicelist_settings_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Device Id</td>
							<td width="20"></td>
							<td colspan="4">
								<select name="dev_id" id="dev_id" class="ipDrpBox" <?php if($flag == "edit" && $checkarch == "armv7l"){ echo "disabled"; }?> onchange="setdevid()" onkeyup="setdevid() " onkeydown="setdevid()">
								</select>
								<img id="devicelist_settings_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText1"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">IP type</td>
							<td width="20"></td>
							<td colspan="4">
								<select name="ip_type" id="ip_type" class="ipDrpBox" onchange="setIPType()" onkeyup="setIPType()" onkeydown="setIPType()">
									<option value="0" <?php if($ip_type == 0) echo "selected" ?>>STATIC</option>
									<option value="1" <?php if($ip_type == 1) echo "selected" ?>>DHCP</option>
								</select>
								<img id="devicelist_settings_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">IP address</td>
							<td width="20"></td>
							<td colspan="4">
								<input type="text" value="<?php print $ip_addr; ?>"  size="15" maxlength="32" id="ip_addr" class="ipBox" name="ip_addr" />
								<img id="devicelist_settings_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Netmask</td>
							<td width="20"></td>
							<td colspan="4">
								<input type="text" value="<?php print $netmask; ?>" size="15" maxlength="32" id="netmask" class="ipBox" name="netmask" />
								<img id="devicelist_settings_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Gateway </td>
							<td width="20"></td>
							<td colspan="4">
								<input type="text" value="<?php print $gateway; ?>" size="15" maxlength="32" id="gateway" class="ipBox" name="gateway"  onclick = "gatewaychange()" onchange = "gatewaychange()" />
								<img id="devicelist_settings_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
							<td style="display:none">
								<label for="req_tls">Default Gatway</label>
							</td>
							<td style="display:none">
								<input type="checkbox" id="DEF_GWT_CHK" name="DEF_GWT_CHK" <?php if($def_gwt == 1) echo "checked='checked' disabled"; ?> onclick="setdefgateway()"/>
								<span id="errorText2"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">DNS Server </td>
							<td width="20"></td>
							<td colspan="4">
								<input type="text" value="<?php print $dns; ?>" size="15" maxlength="32" id="dns" class="ipBox" name="dns" />
								<img id="devicelist_settings_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr style="display:none;">
							<td colspan="3">
							<table>
								<tr>
									<td>
										<label for="sshenable">Enable SSH</label>
										<?php
											if($flag == "add")
												$sshEnable = "";
											if ($sshenable == "1")
												$sshEnable = "checked";
											else
												$sshEnable = "";
										?>
									</td>
									<td width="75"></td>
									<td>
										<input type="checkbox" <?php print $sshEnable; ?> name="sshenable" onclick="enableSSH();" id="sshenable" />
										<img id="devicelist_settings_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
									<td width="30";"> &nbsp;</td>
									<td>SSH Port</td>
									<td>
										<?php 
											$disable = "";
											if($flag == "add") {
												$disable = "disabled";
												$sshport = "";
											}
											if($flag == "edit" && $sshenable == "0") {
												$disable = "disabled";
												$sshport = "";
											}
										?>
										<input type="text" name="sshport" id="sshport" class="ipBox" value="<?php print $sshport; ?>" <?php echo $disable;?> size="4" maxlength="5" />
										<img id="devicelist_settings_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr style="display:none;">
							<td>
								<label for="icmp">Allow ICMP </label>
							</td>
							<td width="45"></td>
							<td>
								<?php
									$icmpchecked = "";
									if( $icmp == "1" )
										$icmpchecked = "checked";
										else
										$icmpchecked = "";
								?>
								<input type="checkbox" name="icmp" id="icmp" <?php print $icmpchecked; ?> />
								<img id="devicelist_settings_ttip_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
<!-- NAT settings -->
						<tr id="natsettings" style="display:none">
							<td valign="top">NAT Settings</td>
							<td width="20"></td>
							<td width="150px">
								<select class="ipDrpBox" id="nat_settings" name="nat_settings" onchange="showStunStatic();" onkeydown="showStunStatic();" onkeyup="showStunStatic();">
								<?php
									for($i = 0; $i < $count_nat_settings; $i++ ) {
										$select_str = "";
										if ( $get_nat_settings[$i] == $nat_settings )
											$select_str = "selected";
								?>
									<option value="<?php print $get_nat_settings[$i];?>" <?php print  $select_str;?> ><?php print $get_nat_settings[$i];?></option>
								<?php
									}
								?>
								</select>
								<img id="devicelist_settings_ttip_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
							<td>
								<label for="ipaddress">	IP Address</label>
							</td>
							<td width="5"></td>
							<td>
								<input type="text" size="14" maxlength="256" value="<?php print $ip_address; ?>" class="ipBox" id="ip_address" name="ip_address" />
								<img id="devicelist_settings_ttip_013" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
				<div class="clear"></div>
			</div>
				<input type="hidden" name="submit_action"  id="submit_action" value="0" />
				<input type="hidden" id="DEF_GWT_HID" name="DEF_GWT_HID" value="0" />
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
