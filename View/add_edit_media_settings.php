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
	include_once("Model/media_settings_model.php");
	include_once("Model/action_mapping_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/device_settings_model.php");
	include_once("Model/sys_calls.php");
	include_once('Model/devicelist_settings_model.php');

	$hidden = "";
	if(SysCalls::getSysArch() == "armv7l") {
		$hidden = 'style="display:none"';
	}

	$devlist = DevicelistSettingsModel::getInstance();
	$device_settings_config = DeviceSettingsModel::getInstance();
	$internalinterface=$devlist->getInterfaceList(0);
	$externalinterface=$devlist->getInterfaceList(1);
	$transinterface=$devlist->getInterfaceList(2);
	$devinterfaces = $devlist->getsipvalues();
	$virtualtype = "";
	$vlanvirtualip = $devlist->getVlanVirtualIp(1);
	foreach($vlanvirtualip as $val) {
		$virtualinterfname = $devlist->getVirtualInterface($val);
		$virtualidtype =  $devlist -> getInterfaceByName($virtualinterfname);
		$array = explode("-",$virtualidtype);
		$virtualtype = $array[1];
		if($virtualtype == "LAN") {
			array_push($internalinterface,$val);
		} else if($virtualtype == "WAN") {
			array_push($externalinterface,$val);
		} else if($virtualtype == "Transcoding") {
			array_push($transinterface,$val);
		}
	}
	$media_settings_config = MediaConfigModel::getInstance();
	$getCodecs = MediaConfigModel::getCodecs();
	$getDTMFModes = MediaConfigModel::getDTMFModes();
	$transcodingInterface = SysCalls::getTranscodingInterface();
	$transcodingStatus = SysCalls::getTranscodingStatus();
	$transCount = $media_settings_config->getTranscodingCount($transcodingInterface);
	$res = 0;
	$page_title = "Create Media Profile";
	$flag = "add";
	$name = "";
	$desc = "";
	$external_interface = "";
	$internal_interface = "";
	$media_interface = "";
	$rtpport_start = "";
	$rtpport_end = "";
	$codecsList = 'g729,g711u,g711a';
	$codecs = SipSecuritySettingsModel::unSerializeSIPMethods($codecsList);
	$enable_silence_sup = "";
	$media_tos = "";
	$nat_enable = 0;
	$createdDataNames = array();
	$mediaNames = $media_settings_config->getNames();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit Media Profile";
		$row = $media_settings_config->getMediaSettings($id);
		if($row) {
			$name = $row[MediaConfigModel::$NAME];
			$desc = $row[MediaConfigModel::$DESCRIPTION];
			$external_interface = $row[MediaConfigModel::$EXTERNAL_INTERFACE];
			$internal_interface = $row[MediaConfigModel::$INTERNAL_INTERFACE];
			$media_interface = $row[MediaConfigModel::$MEDIA_INTERFACE];
			$rtpport_start = $row[MediaConfigModel::$RTPPORT_START];
			$rtpport_end = $row[MediaConfigModel::$RTPPORT_END];
			$get_codecs = $row[MediaConfigModel::$CODECS];
			$nat_enable = $row[MediaConfigModel::$NAT_ENABLE];
			if ( $row[MediaConfigModel::$CODECS] != "") {
				$codecs = SipSecuritySettingsModel::unSerializeSIPMethods($row[MediaConfigModel::$CODECS]);
			} else {
				$codecs = "";
			}
			$enable_silence_sup = $row[MediaConfigModel::$ENABLE_SILENCE_SUPPRESSION];
			$media_tos = $row[MediaConfigModel::$MEDIA_TOS];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ( $_POST['media_interface'] != "No Transcoding" ) {
			if( $_POST['codecsSelected'] != "") {
				$post_codecs = SipSecuritySettingsModel::serializeSIPMethods($_POST['codecsSelected']);
			} else {
				$post_codecs = "";
			}
			if ($_POST['enable_sil_supress'] == "on" )
				$post_enable_sil_supress = "1";
			else
				$post_enable_sil_supress = "";
		} else {
			$post_codecs = "";
			$post_enable_sil_supress = "";
		}
		if($_POST['media_interface'] == "No Transcoding") {
			$postmediainterface = "";
		} else {
			$postmediainterface = $_POST['media_interface'];
		}
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$media_settings_config->addMediaSettings ( trim($_POST['name']),
							trim($_POST['comments']), 
							$_POST['external_interface'], 
							$_POST['internal_interface'], 
							$postmediainterface, 
							trim($_POST['rtpport_start']), 
							trim($_POST['rtpport_end']), 
							$post_codecs, 
							$post_enable_sil_supress, 
							trim($_POST['media_tos']),
							$_POST['NAT_ENABLE_HID']);
			$media_settings_config->createActionList(DEPLOY_MODULES::MEDIA_PROFILE_CONFIG, 
							"Media Profile Added", 
							DEPLOY_MODULES::MEDIA_PROFILE_CONFIG_STR, "");
			$res = 1;
		}
		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $media_settings_config->getMediaSettings($id);
			if($row) {
				$name = $row[MediaConfigModel::$NAME];
				$desc = $row[MediaConfigModel::$DESCRIPTION];
				$external_interface = $row[MediaConfigModel::$EXTERNAL_INTERFACE];
				$internal_interface = $row[MediaConfigModel::$INTERNAL_INTERFACE];
				$media_interface = $row[MediaConfigModel::$MEDIA_INTERFACE];
				$rtpport_start = $row[MediaConfigModel::$RTPPORT_START];
				$rtpport_end = $row[MediaConfigModel::$RTPPORT_END];
				$get_codecs = $row[MediaConfigModel::$CODECS];
				$nat_enable = $row[MediaConfigModel::$NAT_ENABLE];
				if ( $row[MediaConfigModel::$CODECS] != "") {
					$codecs = SipSecuritySettingsModel::unSerializeSIPMethods($get_codecs);
				} else {
					$codecs = "";
				}
				$enable_silence_sup = $row[MediaConfigModel::$ENABLE_SILENCE_SUPPRESSION];
				$media_tos = $row[MediaConfigModel::$MEDIA_TOS];
				if ( $desc != trim($_POST['comments']) || 
					$external_interface != $_POST['external_interface'] || 
					$internal_interface != $_POST['internal_interface'] || 
					$media_interface != $_POST['media_interface'] || 
					trim($_POST['rtpport_start']) != $rtpport_start || 
					trim($_POST['rtpport_end']) != $rtpport_end || 
					$get_codecs != $post_codecs || 
					$enable_silence_sup != $post_enable_sil_supress || 
					$media_tos != trim($_POST['media_tos']) || 
					$nat_enable != $_POST['NAT_ENABLE_HID']) {
						$media_settings_config->editMediaSettings (
							trim($_POST['name']), trim($_POST['comments']),
							$_POST['external_interface'], 
							$_POST['internal_interface'], 
							$postmediainterface, 
							trim($_POST['rtpport_start']), 
							trim($_POST['rtpport_end']),
							$post_codecs,
							$post_enable_sil_supress,
							trim($_POST['media_tos']),
							$_POST['NAT_ENABLE_HID']);
						if($postmediainterface == "") {
							$media_settings_config->updTranRoamingTrunk(trim($_POST['name']));
						}
						$media_settings_config->createActionList(
							DEPLOY_MODULES::MEDIA_PROFILE_CONFIG, 
							"Media Profile edited", 
							DEPLOY_MODULES::MEDIA_PROFILE_CONFIG_STR, "");
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
			var transInterface = '<?php print $transcodingInterface; ?>';
			var nat_enable = '<?php print $nat_enable; ?>';
			var wan_interface_nat = '<?php print $external_interface; ?>';
			var devintername =  new Array();
			var devinterfacetype = new Array();
			var devnatsettings = new Array();

			<?php
				while($row = $devinterfaces->fetchArray(SQLITE3_ASSOC)) {
			?>
					devintername.push('<?php echo $row[DevicelistSettingsModel::$INTER_NAME]; ?>');
					devinterfacetype.push('<?php echo $row[DevicelistSettingsModel::$I_TYPE]; ?>');
					devnatsettings.push('<?php echo $row[DevicelistSettingsModel::$NAT_SETTINGS]; ?>');
			<?php
				}
			?>
			var devnamecount = devintername.length;

			$sub(document).ready(function() {
				initValidation();
				getPageTips("media_settings_ttip");
				setTooltip();
				showExternalIP();
			});

			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			if(flag == "edit") {
				var mediaval = document.getElementById("media_interface").value;
				if(mediaval != "No Transcoding") {
					document.getElementById("media_interface").disabled = true;
				} else {
					document.getElementById("media_interface").disabled = false;
				}
			}

			function initValidation() {
				$sub( document ).ready(function() {
					var internal_interface = $sub("#internal_interface").val();
					var external_interface = $sub("#external_interface").val();
					var media_interface = $sub("#media_interface").val();
					if ( internal_interface == "--No Interfaces--" || external_interface == "--No Interfaces--" || media_interface == "--No Interfaces--") {
						$sub("#internal_interface").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
						$sub("#external_interface").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
						$sub("#media_interface").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
					}
				});
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[alphaNumeric],minSize[5],maxSize[16]]");
				$sub("#FWD_REG_SERVER_PORT").attr("data-validation-engine","validate[required,custom[IP_PORT]]");
				$sub("#FORCE_EXPIRE").attr("data-validation-engine","validate[required,custom[integer],min[1],max[3600]]");
				$sub("#rtpport_start").attr("data-validation-engine","validate[required,custom[integer],min[1025],max[65534]]");
				$sub("#rtpport_end").attr("data-validation-engine","validate[required,custom[integer],min[1026],max[65535]]");
				$sub("#media_tos").attr("data-validation-engine","validate[custom[integer],min[0],max[63]]");
				var rtpStart = document.getElementById("rtpport_start").value;
				var rtpEnd = document.getElementById("rtpport_end").value;
				if ( parseInt(rtpStart) >= parseInt(rtpEnd) ) {
					document.getElementById("errorPortRange").innerHTML = "Invalid! RTP Start can't be greater than RTP End.";
					document.getElementById("errorPortRange").className = "alert-box errorMsg";
					$sub('#errorPortRange').show().delay(2000).fadeOut();
					return false;
				}
				$sub( document ).ready(function() {
					var mediaIfaceVal = $sub("#media_interface").val();
					if ( mediaIfaceVal == "None") {
					$sub("#media_interface").attr("data-validation-engine","validate[required,custom[selectInterfaces]]");
					}
				});
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub("#addmediasettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				var flag = "<?php echo $flag ?>";
				if ( flag == "add") {
					var mediaArr = new Array();
					var get_media_val = document.getElementById("NAME").value;
					var post_media_val = get_media_val.trim();
					<?php
						foreach($mediaNames as $key=>$val) {
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
				if($sub("#addmediasettings").validationEngine('validate')) {
					var selTransIface = $sub("#media_interface").val();
					var transCount = '<?php print $transCount ?>';
					var transIface = '<?php print $transcodingInterface?>';
					var addEditFlag  = '<?php print $flag ?>';
					var mediaIface = '<?php print $media_interface ?>';
					var formObj = document.forms[0];
					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					var rtpStart = document.getElementById("rtpport_start").value;
					var rtpEnd = document.getElementById("rtpport_end").value;
					if ( parseInt(rtpStart) >= parseInt(rtpEnd) ) {
						document.getElementById("errorPortRange").innerHTML = "Invalid! RTP Start can't be greater than RTP End.";
						document.getElementById("errorPortRange").className = "alert-box errorMsg";
						$sub('#errorPortRange').show().delay(2000).fadeOut();
						return false;
					}
					$sub('#codecsSelected option').attr('selected', 'selected');
					document.getElementById("media_interface").disabled = false;
					if($sub("#NAT_ENABLE").is(":checked")) {
						$sub("#NAT_ENABLE_HID").val("1");
					} else {
						$sub("#NAT_ENABLE_HID").val("0");
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
					parent.document.getElementById("container").src = "media_settings.php";
				}
			}

			function showExternalIP() {
				var iface = document.getElementById("external_interface").value;
				var nat = "";
				var itypeval1 = 0;
				var checknatpost_val = 1;
				var set = 0;
				var post_val = 1;
				for (i=0; i<devnamecount;i++) {
					if((devintername[i] == iface) && (devinterfacetype[i] == 1)) {
						nat = devnatsettings[i];
						itypeval1 = devinterfacetype[i];
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
							itypeval1 = 1;
						}
					}).responseText;
					nat = virtualnatvalue;
				}
				if((itypeval1 == 1) && (nat != "NO NAT") && (set == 1)) {
					if((flag == "edit") && (nat_enable == 1) && (wan_interface_nat == iface)){
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

				$sub.ajax({
					type : "POST",
					cache: false,
					data : {external_ip: iface, external_post:post_val},
					url : "show_iface_ip.php",
					success: function (ipaddr) {
						document.getElementById("external_ip").innerHTML = ipaddr;
					}
				});
			}

			function showInternalIP() {
				var iface = document.getElementById("internal_interface").value;
				var post_val = "1";
				$sub.ajax({
					type : "POST",
					cache: false,
					data : {internal_ip: iface, internal_post:post_val},
					url : "show_iface_ip.php",
					success: function (ipaddr) {
						document.getElementById("internal_ip").innerHTML = ipaddr;
					}
				});
			}

			function showMediaIP() {
				disableTranscoding();
				var iface = document.getElementById("media_interface").value;
				var post_val = "1";
				$sub.ajax({
						type : "POST",
						cache: false,
						data : {media_ip: iface, media_post:post_val},
						url : "show_iface_ip.php",
						success: function (ipaddr) {
							document.getElementById("media_ip").innerHTML = ipaddr;
						}
				});
			}

			$sub(document).ready(function(){
				$sub('input[type="button"]').click(function() {
					var $op = $sub('#codecsSelected option:selected'),
						$this = $sub(this);
					if($op.length) {
						($this.val() == 'Up') ?
							$op.first().prev().before($op) :
							$op.last().next().after($op);
					}
				});
			});

/*----- Jquery Tabs function ----*/
			$sub(document).ready(function() {
				$sub("#add_edit_media .tab_content").hide(); //Hide all content
				$sub("#add_edit_media ul.tabs li:first").addClass("active").show(); //Activate first tab
				$sub("#add_edit_media .tab_content:first").show(); //Show first tab content
//On Click Event
				$sub("#add_edit_media ul.tabs li").click(function() {
					$sub("#add_edit_media ul.tabs li").removeClass("active"); //Remove any "active" class
					$sub(this).addClass("active"); //Add "active" class to selected tab
					$sub("#add_edit_media .tab_content").hide(); //Hide all tab content
					var activeTab = $sub(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
					$sub(activeTab).show(); //Fade in the active content
					return false;
				});
			});
/*---------- Copy Codecs to another list -------*/
			function copyCodecsToSelectedList() {
				var i,j,k;
				var codeLst =  document.getElementById('codec');
				var codecsSelLst =  document.getElementById('codecsSelected');
				var grpLstSelected = document.getElementById('codec').value;
				var medIntList = document.getElementById("media_interface").value;
				if ( medIntList != "No Transcoding" ) {
					if(codeLst.value == '' && codeLst.length > 0) {
						alert("Please select an option to add!");
						codeLst.focus();
						return false;
					} else if(codeLst.length <= 0) {
						alert("There are no codecs in the list to be added.");
						return false;
					}
					for( j=0; j< codeLst.options.length; j++) {
						if(codeLst[j].selected == true) {
							if(codecsSelLst.options.length == 0) {
								var selListTxt = codeLst.options[j].text;
								var selListVal = codeLst.options[j].value;
								var selGrpList = document.createElement('option');
								selGrpList.text = selListTxt;
								selGrpList.value = selListVal;
								codecsSelLst.add(selGrpList,codecsSelLst.options[null]);
								try {
									codecsSelLst.add(selGrpList,codecsSelLst.options[null]);
								}
								catch(ex) {
									codecsSelLst.add(selGrpList,codecsSelLst.options[null]);
								}
							}
							for ( k = 0; k < codecsSelLst.options.length; k++ ) {
								var splitCopiedArr = codecsSelLst.options[k].value.split("/");
								if ( codeLst.options[j].value == splitCopiedArr[0] ) {
									break;
								}
								if(k == codecsSelLst.options.length - 1) {
									var selListTxt = codeLst.options[j].text;
									var selListVal = codeLst.options[j].value;
									var selGrpList = document.createElement('option');
									selGrpList.text = selListTxt;
									selGrpList.value = selListVal;
									codecsSelLst.add(selGrpList,codecsSelLst.options[null]);
									try {
										codecsSelLst.add(selGrpList,codecsSelLst.options[null]);
									}
									catch(ex) {
										codecsSelLst.add(selGrpList,codecsSelLst.options[null]);
									}
								}
							}
						}
					}
				}
			}

			function copyAllCodecsToSelectedList() {
				var i,j;
				var found = 0;
				var codeLst =  document.getElementById('codec');
				var codecsSelLst =  document.getElementById('codecsSelected');
				var medIntList = document.getElementById("media_interface").value;
				if ( medIntList != "No Transcoding" ) {
					if(codeLst.length <= 0) {
						alert("There are no codecs in the list to be added.");
						return false;
					}
					removeAllSipMethodsFromSelectedList();
					for ( i = 0 ; i < codeLst.options.length; i++ ) {
						for ( j = 0; j < codecsSelLst.options.length; j++ ) {
							if ( codeLst.options[i].value == codecsSelLst.options[j].value ) {
								found = 1;
								return false;
							}
						}
						if ( found != 1 ) {
							var newOption = document.createElement("option");
							newOption.text = codeLst.options[i].text;
							newOption.value = codeLst.options[i].value;
							codecsSelLst.add(newOption,codecsSelLst.options[null]);
						}
					}
				}
			}

			function removeCodecsFromSelectedList() {
				var i,j;
				var codecsSelLst =  document.getElementById('codecsSelected');
				var trunksSelLstSelcted =  document.getElementById('codecsSelected').value;
				var GrpListLen = codecsSelLst.options.length;
				var grpSelSI = codecsSelLst.selectedIndex;
				var medIntList = document.getElementById("media_interface").value;
				if ( medIntList != "No Transcoding" ) {
					for(j = 0 ; j < GrpListLen; j++) {
						if(grpSelSI == -1) {
							document.getElementById('codecsSelected').focus();
							return false;
						}
					}
					if(codecsSelLst.options.length < 1) {
						return false;
					}
					if(trunksSelLstSelcted == '') {
						return false;
					}
					for (i = codecsSelLst.options.length - 1; i >= 0; i--) {
						if (codecsSelLst.options[i].selected) {
							codecsSelLst.remove(i);
						}
					}
				}
				return false;
			}

			function removeAllSipMethodsFromSelectedList() {
				var i;
				var codecsSelLst =  document.getElementById('codecsSelected');
				var medIntList = document.getElementById("media_interface").value;
				if ( medIntList != "No Transcoding" ) {
					if(codecsSelLst.options.length < 1) {
						return false;
					}
					for (i = codecsSelLst.options.length - 1; i >= 0; i--) {
						if (codecsSelLst.options[i]) {
							codecsSelLst.remove(i);
						}
					}
				}
				return false;
			}
/*------------ Copy Codecs to another list ---------*/
			function disableTranscoding() {
				var medIntList = document.getElementById("media_interface").value;
				if ( medIntList != "No Transcoding" ) {
					document.getElementById("codec").disabled = false;
					document.getElementById("codecsSelected").disabled = false;
					document.getElementById("ENABLE_SIL_SUPRESS").disabled = false;
					document.getElementById("add").disabled = false;
					document.getElementById("addAll").disabled = false;
					document.getElementById("delete").disabled = false;
					document.getElementById("deleteAll").disabled = false;
				} else {
					document.getElementById("codecsSelected").innerHTML = "";
					document.getElementById("ENABLE_SIL_SUPRESS").checked = false;
					document.getElementById("codec").disabled = true;
					document.getElementById("codecsSelected").disabled = true;
					document.getElementById("ENABLE_SIL_SUPRESS").disabled = true;
					document.getElementById("add").disabled = true;
					document.getElementById("addAll").disabled = true;
					document.getElementById("delete").disabled = true;
					document.getElementById("deleteAll").disabled = true;
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div id="add_edit_media">
				<ul class="tabs">
					<li><a href="#tabMedia">Media Settings</a></li>
					<li <?php echo $hidden; ?>><a href="#tabTranscoding">Transcoding Settings</a></li>
				</ul>
			<div class="tab_container"> <a name="tabMedia"></a> <a name="tabTranscoding"></a>
			<div class="popup-content" style="height: 285px">
				<form id="addmediasettings" name="addmediasettings" class="popup-form" action="add_edit_media_settings.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
				<div id="tabMedia" class="tab_content">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<td>Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="15" maxlength="16" />
								<img id="media_settings_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $desc; ?>"  size="40" maxlength="64" id="COMMENTS" class="ipBox" name="comments" />
								<img id="media_settings_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">External Interface</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="external_interface" id="external_interface" class="ipDrpBox" onchange="showExternalIP();" Onkeyup="showExternalIP();" Onkeydown="showExternalIP();"> 

							<?php
								$set =1;
								if(count($externalinterface) == 0) {
									$set =1;
								} else {
									for($i=0;$i<count($externalinterface);$i++) {
										$select_str = "";
										if($flag == "edit" && ($externalinterface[$i] == $external_interface)) {
											$select_str = "selected";
											$set = 0;
							?>
											<option value="<?php echo $externalinterface[$i];?>" <?php print $select_str;?> > <?php echo $externalinterface[$i];?> </option>
							<?php
										} else {
										$set = 0;
							?>
											<option value="<?php echo $externalinterface[$i];?>"> <?php echo $externalinterface[$i];?> </option>
							<?php
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
								<img id="media_settings_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="external_ip"></span>
								<span>&nbsp;&nbsp;&nbsp;&nbsp;NAT</span>
								<input type="checkbox" id="NAT_ENABLE" name="NAT_ENABLE" />		
								<img id="media_settings_ttip_012" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />

							</td>
						</tr>
						<tr>
							<td valign="top">Internal Interface</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="internal_interface" id="internal_interface" class="ipDrpBox" Onchange="showInternalIP();" OnKeydown="showInternalIP();" OnKeyup="showInternalIP();">
							<?php
								$set =1;
								if(count($internalinterface) == 0)
								{ 
									$set =1;
								} else {
									for($i=0;$i<count($internalinterface);$i++) {
										$x = count($internalinterface);
							?>
							<!--<script>window.alert('<?php echo $x; ?>');</script> -->
							<?php
										$select_str = "";
										if($flag == "edit" && ($internalinterface[$i] == $internal_interface)) {
											$select_str = "selected";
											$set = 0;
							?>
												<option value="<?php echo $internalinterface[$i];?>" <?php print $select_str;?> > <?php echo $internalinterface[$i];?> </option>
							<?php
										} else {
											$set = 0;
							?>
												<option value="<?php echo $internalinterface[$i];?>"> <?php echo $internalinterface[$i];?> </option>
						<?php
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
								<img id="media_settings_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="internal_ip"></span>
							</td>
						</tr>
						<tr <?php echo $hidden; ?>>
							<td valign="top">transcoding Interface</td>
							<td width="20"></td>
							<td>
								<select style="width: 160px;" name="media_interface" id="media_interface" class="ipDrpBox" onchange="showMediaIP();" OnKeydown="showMediaIP();" OnKeyup="showMediaIP();">
								<?php
								$set =1;
								if(count($transinterface) == 0) {
									$set =1;
								} else {
									for($i=0;$i<count($transinterface);$i++) {
										$select_str = "";
										if($flag == "edit" && ($transinterface[$i] == $media_interface)) {
											$select_str = "selected";
											$set = 0;
								?>
											<option value="<?php echo $transinterface[$i];?>" <?php print $select_str;?>> <?php echo $transinterface[$i];?> </option>
								<?php
										} else {
											$check =$media_settings_config->checktranscoding($transinterface[$i]);	
											if($check == 0) {
												$set = 0;
									?>
												<option value="<?php echo $transinterface[$i];?>"> <?php echo $transinterface[$i];?> </option>
									<?php
											}
										}
									}
								}
								?>
								<?php
								if(($flag == "edit") && ( $media_interface == "")) {
									$select_str = "selected";
								?>
									<option value="No Transcoding" <?php print $select_str;?>> No Transcoding </option>
								<?php
								} else { ?>
									<option value="No Transcoding"> No Transcoding </option>
								<?php
								}
								if($set == 1) { 
								?>
									<!--	<option value="--No Interfaces--">--No Interfaces--</option> -->
								<?php
								}
								?>
								</select>
								<img id="media_settings_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="media_ip"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">RTP Port Start</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $rtpport_start; ?>" size="5" maxlength="5" id="rtpport_start" class="ipBox" name="rtpport_start" />
								<img id="media_settings_ttip_006" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">RTP Port End</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $rtpport_end; ?>" size="5" maxlength="5" id="rtpport_end" class="ipBox" name="rtpport_end" />
								<img id="media_settings_ttip_007" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorPortRange"></span>
							</td>
						</tr>
						<tr>
							<td valign="top">Media TOS</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $media_tos; ?>" size="3" maxlength="2" id="media_tos" class="ipBox" name="media_tos" />
								<img id="media_settings_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<div id="tabTranscoding" class="tab_content">
				<table>
					<tr>
						<td valign="top">Codec</td>
						<td width="20"></td>
						<td colspan="2">
							<table>
								<tr>
									<td>
										<table>
											<tr>
												<td>
												<select size="5" style="width: 160px; font-size: 12px" multiple="multiple" id="codec" name="codec[]">
											<?php
												$codecsCount = count($getCodecs);
												$selectedCodecsCount = count($codecs);
												for ($i = 0; $i < $codecsCount; $i++) {
											?>
													<option id="" value="<?php print $getCodecs[$i]; ?>"><?php print $getCodecs[$i]; ?></option>
											<?php
												}
											?>
												</select>
												</td>
												<td valign="top">
												<img id="media_settings_ttip_009" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
												</td>
											</tr>
										</table>
									</td>
									<td>
									<table>
										<tr><td><a class="btn-usergrp-wide" onclick="copyCodecsToSelectedList();"><span id="add"> > </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="copyAllCodecsToSelectedList();"><span id="addAll"> >> </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeCodecsFromSelectedList();"><span id="delete"> < </span></a></td></tr>
										<tr><td><a class="btn-usergrp-wide" onclick="removeAllSipMethodsFromSelectedList();"><span id="deleteAll"> << </span></a></td></tr>
									</table>
									</td>
									<td>
									<table>
										<tr>
										<td>
											<select name="codecsSelected[]" size="5" style="width: 160px; font-size: 12px" id="codecsSelected" multiple="multiple">
											<?php
											if( $flag == "add" ) {
												$codecsCount = count($getCodecs);
												$codecsCount = 0;
												for ($i = 0; $i < $codecsCount; $i++) {
											?>
													<option id="" value="<?php print $getCodecs[$i]; ?>"><?php print $getCodecs[$i]; ?></option>
											<?php
												}
											}
											?>
											<?php
											if( $flag == "edit" ) {
												for ($k = 0;  $k < $selectedCodecsCount; $k++) {
													if ( $codecs[$k] != '') {
											?>
												<option  value="<?php print $codecs[$k]; ?>"><?php print $codecs[$k]; ?></option>
											<?php
													}
												}
											}
											?>
											</select>
										</td>
										<td valign="top">
											<img id="media_settings_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									</table>
								</td>
								<td valign="center">
									<table>
										<tr>
											<td><input class="btn-up" type="button" value="Up"></td>
										</tr>
										<tr>
											<td><input class="btn-down" type="button" value="Down"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						</td>
						</tr>
						<tr>
							<td><label for="enable">Enable Silence Suppression</label></td>
							<td width="20"></td>
							<?php
								if ( $enable_silence_sup == "1") {
									$silence_sup_enabled = "checked";
								} else {
									$silence_sup_enabled = "";
								}
							?>
							<td>
								<input type="checkbox" <?php print $silence_sup_enabled; ?> name="enable_sil_supress" id="ENABLE_SIL_SUPRESS" />
								<img id="media_settings_ttip_011" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
				<div class="clear"></div>
				</div>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="MEDIA_SETTINGS_LIST" id="MEDIA_SETTINGS_LIST" value="" />
					<input type="hidden" id="NAT_ENABLE_HID" name="NAT_ENABLE_HID" value="0" /> 
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
		showExternalIP();
		showInternalIP();
		showMediaIP();
		disableTranscoding();
	</script>
</html>
