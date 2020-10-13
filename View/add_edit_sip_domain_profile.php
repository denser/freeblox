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
	include_once('Model/sip_domain_profile_model.php');

	$sipdomain_settings_config = SipDomainSettingsModel::getInstance();
	$res = 0;
	$page_title = "Create SIP Domain Profile";
	$flag = "add";
	$sipdomain = "";
	$methods = "";
	$dsturi = "";
	$bind_port = 1;
	$bind_host = 0;
	$createdDataNames = array();
	$sipdomains = $sipdomain_settings_config->getNames();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = $val;
		$postid = $val;
		$flag = "edit";
		$page_title = "Edit SIP Domain Profile";
		$row1 = $sipdomain_settings_config->getsipdomainsettings($id);
		if($row1) {
			$sipdomain = $row1[SipDomainSettingsModel::$SIP_DOMAIN];
			$methods = $row1[SipDomainSettingsModel::$METHODS];
			$dsturi = $row1[SipDomainSettingsModel::$DST_URI];
			$bind_port = $row1[SipDomainSettingsModel::$BIND_PORT];
			$bind_host = $row1[SipDomainSettingsModel::$BIND_HOST];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ($_POST['submit_action'] == "1"){
			$sipdomain_settings_config->editsipdomainSettings ($_POST['editid'],
						trim($_POST['sipdomain']),$_POST['methods'],
						trim($_POST['desuri']),
						$_POST['BIND_PORT_HIDDEN'],
						$_POST['BIND_HOST_HIDDEN']);
			$sipdomain_settings_config->createActionList(
						DEPLOY_MODULES::SIPDOMAIN_PROFILE_CONFIG, 
						"sip domain Profile edited", 
						DEPLOY_MODULES::SIPDOMAIN_PROFILE_CONFIG_STR, "");
			$res = 1;
		}
		if ($_POST['submit_action'] == "2") {
			$sipdomain_settings_config->addsipdomainSettings (trim($_POST['sipdomain']),
						$_POST['methods'],trim($_POST['desuri']),
						$_POST['BIND_PORT_HIDDEN'],
						$_POST['BIND_HOST_HIDDEN']);
			$sipdomain_settings_config->createActionList(
						DEPLOY_MODULES::SIPDOMAIN_PROFILE_CONFIG, 
						"SIP domain Profile Added", 
						DEPLOY_MODULES::SIPDOMAIN_PROFILE_CONFIG_STR, "");
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
			var checksipdomainArr = new Array();
			<?php
			$i = 0;
				for($i = 0; $i < count($sipdomains); $i++) {
			?>
					checksipdomainArr.push('<?php echo $sipdomains[$i]; ?>');
			<?php
				}
			?>
			var countchecksipdomainArr = checksipdomainArr.length;

			$sub(document).ready(function() {
				initValidation();
				getPageTips("sipdomain_settings_ttip");
				setTooltip();
			});

			function initValidation() {
				$sub("#sipdomain").attr("data-validation-engine", "validate[required,custom[IP_OR_DOMAIN_DOT]]");
				$sub("#desuri").attr("data-validation-engine", "validate[custom[IP_DOMAIN_PORT]]");
				$sub("#addresourcesettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function setroamuser() {
				window.alert("roaming user alert");
			}

			function submitData() {
				var flag = "<?php echo $flag ?>";
				if ( flag == "add") {
					var mediaArr = new Array();
					var formObj = document.forms[0];
					/*var get_resource_val = formObj.sipdomain.value;
					for ( var i=0; i < countchecksipdomainArr; i++) {
						if (get_resource_val == checksipdomainArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! SIP Domain name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg"
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}*/
				}
				if($sub("#addresourcesettings").validationEngine('validate')) {
					var formObj = document.forms[0];
					var oper_val = document.getElementById("BIND_OPR_CHK").checked;
					var bind_host_val = document.getElementById("BIND_HOST_CHK").checked;
                                        if(oper_val == false) {
                                                $sub("#BIND_PORT_HIDDEN").val("0");
                                        } else {
                                                $sub("#BIND_PORT_HIDDEN").val("1");
                                        }
                                        if(bind_host_val == false) {
                                                $sub("#BIND_HOST_HIDDEN").val("0");
                                        } else {
                                                $sub("#BIND_HOST_HIDDEN").val("1");
                                        }
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
					parent.document.getElementById("container").src = "sip_domain_profile.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 285px">
<form id="addresourcesettings" name="addresourcesettings" class="popup-form" action="add_edit_sip_domain_profile.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
				<div id="tabdevicelist" class="tab_content">
					<table>
						<tr>
							<td>SIP Domain</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="sipdomain" name="sipdomain" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $sipdomain ?>" size="10" maxlength="256" />
								<img id="sipdomain_settings_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							<span id="errorText"></span>
							</td>
						</tr>
						<tr>
							<td> Bind Port</td>
							<td width="20"></td>
							<td>
								<input type="checkbox" <?php if($bind_port == 1) echo "checked='checked'" ?> id="BIND_OPR_CHK" name="BIND_OPR_CHK" />
								<img id="sipdomain_settings_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td> Bind Host</td>
							<td width="20"></td>
							<td>
								<input type="checkbox" <?php if($bind_host == 1) echo "checked='checked'" ?> id="BIND_HOST_CHK" name="BIND_HOST_CHK" />
								<img id="sipdomain_settings_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr style="display:none">
							<td valign="top">Methods</td>
							<td width="20"></td>
							<td>
								<select name="methods" id="methods" class="ipDrpBox">
									<option value="Default" <?php if($methods == "Default") echo "selected" ?>>Default</option>
									<option value="Register" <?php if($methods == "Register") echo "selected" ?>>Register</option>
									<option value="Subscribe" <?php if($methods == "Subscribe") echo "selected" ?>>Subscribe</option>

								</select>
								<img id="sipdomain_settings_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr style="display:none">
							<td valign="top">Destination URI</td>
							<td width="20"></td>
							<td>
								<input type="text" size="48" maxlength="64" value="<?php print $dsturi; ?>" id="desuri" class="ipBox" name="desuri" />
								<img id="sipdomain_settings_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
				<div class="clear"></div>
			</div>
			<input type="hidden" name="submit_action"  id="submit_action" value="0" />
			<input type="hidden" name="editid"  id="editid" value="<?php echo $id;?>" />
			<input type="hidden" id="BIND_PORT_HIDDEN" name="BIND_PORT_HIDDEN" value="0" />
			<input type="hidden" id="BIND_HOST_HIDDEN" name="BIND_HOST_HIDDEN" value="0" />
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
