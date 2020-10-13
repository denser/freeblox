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
	include_once('Model/subscribers_model.php');
	include_once("Model/action_mapping_model.php");
	include_once("Model/sip_sec_settings_model.php");
	include_once("Model/sys_calls.php");

	$subscribers_settings_config = SubscribersSettingsModel::getInstance();
	$res = 0;
	$page_title = "Create Subscribers Profile";
	$flag = "add";
	$username = "";
	$password = "";
	$operator = "";
	$roaminguserprofile = "";
	$createdDataNames = array();
	$subusernames = $subscribers_settings_config->getNames();
	$subroamuserprofile = $subscribers_settings_config->getroamuserprofile();
	$checkusername_roaming = $subscribers_settings_config->checkusernameroaming();

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = $val;
		$postid = $val;
		$flag = "edit";
		$page_title = "Edit Subscribers Profile";
		$row1 = $subscribers_settings_config->getsubscribersSettings($id);
		if($row1) {
			$username = $row1[SubscribersSettingsModel::$USERNAME];
			$password = $row1[SubscribersSettingsModel::$PASSWORD];
			$roaminguserprofile = $row1[SubscribersSettingsModel::$ROAMUSERPROFILE];
			$operator = $row1[SubscribersSettingsModel::$OPERATOR];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])){
		if ($_POST['submit_action'] == "1"){
			$subscribers_settings_config->editsubscribersSettings ($_POST['editid'],
							trim($_POST['password']),
							trim($_POST['OPERHIDDEN']),
							$_POST['roaminguserprofile']);
			$subscribers_settings_config->createActionList(
							DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG, 
							"Subscribers Profile edited", 
							DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG_STR, 
							"");
			$res = 1;
		}

		if ($_POST['submit_action'] == "2") {
			$subscribers_settings_config->addsubscribersSettings (trim($_POST['username']),
						trim($_POST['password']),
						trim($_POST['roaminguserprofile']),
						trim($_POST['OPERHIDDEN']));
			$subscribers_settings_config->createActionList(
						DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG, 
						"Subscribers Profile Added", 
						DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG_STR, "");
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
			var roaminguser = "<?php echo $roaminguserprofile ?>";
			var roamuserArr = new Array();	
			var checkroamuserArr = new Array();
			var username = new Array();
			<?php
				$i = 0;
				for($i = 0; $i < count($subusernames); $i++) {
			?>
					username.push('<?php echo $subusernames[$i]; ?>');
			<?php
				}
			?>
			var countusername = username.length;
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
				for($i = 0; $i < count($checkusername_roaming); $i++) {
			?>
					checkroamuserArr.push('<?php echo $checkusername_roaming[$i]; ?>');
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
				getPageTips("subscribers_settings_ttip");
				setTooltip();
				enablepwd();
			});

			function initValidation(){
				$sub("#username").attr("data-validation-engine","validate[required,custom[onlyLetterNumberDotHyphenUnderScore],maxSize[256]]");
				$sub("#password").attr("data-validation-engine","validate[required]");
				$sub("#addsubscribersettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function enablepwd() {
				var oper_val = document.getElementById("OPER_CHK").checked;
				if( oper_val == false ) {
					$sub("#setpwd").hide(500);
					$sub("#password").val("");
				} else {
					$sub("#setpwd").show(500);
				}
			}

			function submitData() {
				var flag = "<?php echo $flag ?>";
				var mediaArr = new Array();
				var formObj = document.forms[0];
				var get_media_val = formObj.username.value;
				var get_media2_val = formObj.roaminguserprofile.value;
				var post_media_val = get_media_val+ "|"+ get_media2_val;
				if ( flag == "add") {
					for ( var i=0; i < countcheckroamuserArr; i++) {
						if (post_media_val == checkroamuserArr[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! User Name and Roaminguser profile is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg"
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
					for ( var i=0; i < countusername; i++) {
						if (get_media_val == username[i]) {
							document.getElementById("errorText").innerHTML = "Invalid! User Name is already in use.";
							document.getElementById("errorText").className = "alert-box errorMsg"
							$sub("#errorText").show().delay(2000).fadeOut();
							return false;
						}
					}
				}
				if(flag == "edit") {
					if(get_media2_val != roaminguser) {
						for ( var i=0; i < countcheckroamuserArr; i++)	{
							if (post_media_val == checkroamuserArr[i]) {
								document.getElementById("errorText").innerHTML = "Invalid! User Name and Roaminguser profile is already in use.";
								document.getElementById("errorText").className = "alert-box errorMsg"
								$sub("#errorText").show().delay(2000).fadeOut();
								return false;
							}
						}
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
				if($sub("#addsubscribersettings").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add"){
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
					var oper_val = document.getElementById("OPER_CHK").checked;
					if(oper_val == false) {
						$sub("#OPERHIDDEN").val("0");
					} else {
						$sub("#OPERHIDDEN").val("1");
					}

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
					parent.document.getElementById("container").src = "subscribers.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 285px">
<form id="addsubscribersettings" name="addsubscribersettings" class="popup-form" action="add_edit_subscribers.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
				<div id="tabdevicelist" class="tab_content">
					<table>
						<tr>
							<td>User name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" id="username" name="username" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $username ?>" size="" maxlength="256" />
								<img id="subscribers_settings_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText"></span>
							</td>
						</tr>
						<tr>
						<td>Operator</td>
						<td width="20"></td>
						<td>
							<input type="checkbox" <?php if($operator == 1) echo "checked='checked'" ?> id="OPER_CHK" name="OPER_CHK" <?php if( ($flag == "edit") && ($operator == 1) ) echo "disabled='disabled'" ?> onclick="enablepwd();"/>
							<img id="subscribers_settings_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
						</tr>
						<tr id="setpwd" style="display:none">
							<td valign="top">Password</td>
							<td width="20"></td>
							<td>
								<input type="password" value="<?php print $password; ?>"  size="10" maxlength="256" id="password" class="ipBox" name="password" />
								<img id="subscribers_settings_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Roaming User Profile</td>
							<td width="20"></td>
							<td>
								<select name="roaminguserprofile" id="roaminguserprofile" <?php if($flag == "edit") echo "disabled='disabled'" ?> class="ipDrpBox">
								</select>
								<img id="subscribers_settings_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								<span id="errorText1"></span>
							</td>
							</tr>
					</table>
				<div class="clear"></div>
			 </div>
				<input type="hidden" name="submit_action"  id="submit_action" value="0" />
				<input type="hidden" name="editid"  id="editid" value="<?php echo $id;?>" />
				<input type="hidden" id="OPERHIDDEN" name="OPERHIDDEN" value="0" /> 
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
