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
	include_once("Model/sys_calls.php");

	$res = 0;
	if(isset($_GET['data'])) {
		$cert_type = $_GET['data'];
		if ($cert_type == "upload") {
			$page_title = "Upload Server CA";
		}
		if ($cert_type == "generate") {
			$page_title = "Generate Server CA";
		}
	}
	if (isset($_POST)) {
		$fileName = $_FILES['server_file']['name'];
		$tmpFileName = $_FILES['server_file']['tmp_name'];
		$passPhrase = trim($_POST['passphrase']);
		$status = SysCalls::uploadServerCert($fileName,$tmpFileName,$passPhrase);
		$res = 1;
	}
	if ( isset($_POST)) {
		$common_name = $_POST['common_name'];
		$country_name = $_POST['country_name'];
		$state_province_name = $_POST['state_province_name'];
		$organization_name = $_POST['organization_name'];
		$email_id = $_POST['email_id'];
		$org_unit_name = $_POST['org_unit_name'];
		if ($common_name != "" && $country_name != "" && $state_province_name != ""
			&& $organization_name != "" && $email_id != "" &&  $org_unit_name != "" ) {
				$status = SysCalls::generateServerCert($common_name,$country_name,$state_province_name,$organization_name,$email_id,$org_unit_name);
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
				getPageTips("certs_ttip");
				setTooltip();
			});

			function initValidation() {
				$sub("#COUNTRY_NAME").attr("data-validation-engine","validate[required,custom[onlyLetter],minSize[2],maxSize[2]]");
				$sub("#STATE_PROVINCE_NAME").attr("data-validation-engine","validate[required,custom[onlyLetter],minSize[2],maxSize[32]]");
				$sub("#ORGANIZATION_NAME").attr("data-validation-engine","validate[required,custom[onlyLetterSp],minSize[3],maxSize[128]]");
				$sub("#ORG_UNIT_NAME").attr("data-validation-engine","validate[required,custom[onlyLetterSp],minSize[3],maxSize[32]]");
				$sub("#COMMON_NAME").attr("data-validation-engine","validate[required,minSize[5],maxSize[256]]");
				$sub("#EMAIL_ID").attr("data-validation-engine","validate[required,custom[email],maxSize[128]]");
				$sub("#generateuploadServerCA").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				if($sub("#generateuploadServerCA").validationEngine('validate')) {
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

			function refreshIframe(val) {
				if(val == 1) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = "server_certs.php";
				}
			}
	</script>
	</head>
	<body class="popup" onload="refreshIframe('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<?php  if ($cert_type == "upload" ) { ?>
			<div class="popup-content" style="height: 90px">
			<form enctype="multipart/form-data" id="uploadCA" name="uploadCA" class="popup-form" action="generate_upload_server_ca.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr>
							<td> Passphrase: <input type="password" class="ipBox" name="passphrase" value="" /> </td>
						</tr>
						<tr>
							<td> Upload a CA file: <input type="file" name="server_file" /> </td>
							<input type="hidden" name="submit_action" value="0" />
							<td>
								<img id="certs_ttip_010" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
							<td>
								<a onclick="submitData();" class="btn-grey-wide"><span>Upload</span></a>
							</td>
						</tr>
					</table>
			</form>
			<?php } ?>
			<?php  if ($cert_type == "generate" ) { ?>
				<div class="popup-content" style="height: 270px">
				<form id="generateuploadServerCA" name="generateuploadServerCA" class="popup-form" action="generate_upload_server_ca.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
				<table class="formtable" cellpadding="2px" cellspacing="2px">
					<tr>
						<td>Common Name</td>
						<td width="20"></td>
						<td>
							<input type="text" class="ipBox" id="COMMON_NAME" name="common_name" value="<?php echo $name; ?>" size="" maxlength="256" />
							<img id="certs_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
					</tr>
					<tr>
						<td>Country Name</td>
						<td width="20"></td>
						<td>
							<input type="text" class="ipBox" id="COUNTRY_NAME" name="country_name" value="<?php echo $name; ?>" size="" maxlength="16" />
							<img id="certs_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
					</tr>
					<tr>
						<td>State or Province Name</td>
						<td width="20"></td>
						<td>
							<input type="text" class="ipBox" id="STATE_PROVINCE_NAME" name="state_province_name" value="<?php echo $name; ?>" size="" maxlength="16" />
							<img id="certs_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
					</tr>
					<tr>
						<td>Organization Name</td>
						<td width="20"></td>
						<td>
							<input type="text" class="ipBox" id="ORGANIZATION_NAME" name="organization_name" value="<?php echo $name; ?>" size="" maxlength="16" />
							<img id="certs_ttip_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
					</tr>
					<tr>
						<td>Email Address</td>
						<td width="20"></td>
						<td>
							<input type="text" class="ipBox" id="EMAIL_ID" name="email_id" value="<?php echo $name; ?>" size="" maxlength="128" />
							<img id="certs_ttip_005" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
					</tr>
					<tr>
						<td>Organization Unit Name</td>
						<td width="20"></td>
						<td>
							<input type="text" class="ipBox" id="ORG_UNIT_NAME" name="org_unit_name" value="<?php echo $name; ?>" size="" maxlength="16" />
							<img id="certs_ttip_008" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
						</td>
					</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="WHITELIST_RULE_LIST" id="WHITELIST_RULE_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="downloadsslvpcert" value="" class="btn-save" onclick="submitData();" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
		<?php } ?>
	</body>
	<script type="text/javascript">
		initValidation();
	</script>
</html>
