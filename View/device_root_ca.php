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
	include_once("Control/date_config.php");
	include_once('Model/sys_calls.php');
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#tls_cert";
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
			$res = 0;
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();
			$getcaCertDetails = SysCalls::getCaCertDetails();
			$ca_config_mode = SysCalls::getCAConfigStatus();
			$serverCertList = SysCalls::getCertList();
			$serverCertCount = count($serverCertList);
			$generateServerCount = 0;
			for ($i=0; $i < $serverCertCount; $i++) {
				$certArray = explode(',',$serverCertList[$i]);
				$cert_name =  $certArray[0];
				$cert_type = $certArray[1];
				if ($cert_name != "" && $cert_type == 1 ) {
					$generateServerCount++;
				}
			}
			$serverConfigCount = trim($generateServerCount);
			$generateClientCount = 0;
			for ($i=0; $i < $serverCertCount; $i++) {
				$certArray = explode(',',$serverCertList[$i]);
				$cert_name =  $certArray[0];
				$cert_type = $certArray[1];
				if ($cert_name != "" && $cert_type == 2 ) {
					$generateClientCount++;
				}
			}
			$clientConfigCount = trim($generateClientCount);
			$caConfMode = trim($ca_config_mode);
			/*$caCertDetails = explode("/",$getcaCertDetails);
			$commonName = explode("=",$caCertDetails[1]);
			$commonNameStr = "Common Name = ";
			$state = explode("=",$caCertDetails[2]);
			$stateStr = "State = ";
			$countryName = explode("=",$caCertDetails[3]);
			$countryNameStr = "Country Name = ";
			$emailID = explode("=",$caCertDetails[4]);
			$emailStr = "E-mail ID = ";
			$orgNameStr = "Organization Name = ";
			if ( $caCertDetails[6] != "" ) {
				$orgName = explode("=",$caCertDetails[5]);
				$org_exp = explode("not",$caCertDetails[6]);
				//$orgUnit = explode("=",trim($org_exp[0]));
				//$orgUnitStr = "Organization Unit = ";
				$validFrom = explode("=",trim($org_exp[1]));
				$validFromStr = "Valid From = ";
				$validTo = explode("=",trim($org_exp[2]));
				$validToStr = "Valid To = ";
			} else {
				$org_exp = explode("not",$caCertDetails[5]);
				//$orgUnit = explode("=",trim($org_exp[0]));
				//$orgUnitStr = "Organization Unit = ";
				$orgName = explode("=",trim($org_exp[0]));
				$validFrom = explode("=",trim($org_exp[1]));
				$validFromStr = "Valid From = ";
				$validTo = explode("=",trim($org_exp[2]));
				$validToStr = "Valid To = ";
			}*/
		?>
		<script type="text/javascript">
			$sub(document).ready(function() {
				initValidation();
				getPageTips("deviceRootCA");
				setTooltip();
			});

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "device_root_ca.php";
				}
			}

			function initValidation() {
				$sub("#deviceRootCA").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var serverConfigCount = '<?php print $serverConfigCount ?>';
			var clientConfigCount = '<?php print $clientConfigCount ?>';
			function generate_upload_ca(val) {
				if ( serverConfigCount < 1 && clientConfigCount < 1) {
					if ( val == "upload") {
						parent.callEditPage(val,'generate_upload_ca.php', 100, 550);
					}
					if ( val == "generate") {
						parent.callEditPage(val,'generate_upload_ca.php', 360, 550);
					}
				} else {
					alert("Please delete the server/client certificates as those have been generated using the Device Root CA");
					return false;
				}
			}

			var caConfigMode = '<?php print $caConfMode ?>';
			function downloadCert(val) {
				if ( caConfigMode == "generate" ) {
					parent.downloadLogs(val);
				} else {
					alert("To download Device Root CA, you need to generate the certificate locally by using Generate option.");
					return false;
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<?php
				if ($update_count > 0 ) {
					include_once("notification.html");
					$class = "notify_changes";
				}
			?>
			<h1 class="<?php print $class; ?>">Device Root CA<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="deviceRootCA" method="post" action="device_root_ca.php" autocomplete="off">
			<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>
					<td>
						<table>
							<tr>
								<td>
									<a onclick="generate_upload_ca('<?php print "upload"; ?>');" class="btn-grey-wide"><span>Upload</span></a>
								</td>
								<td>
									<a onclick="generate_upload_ca('<?php print "generate"; ?>');" class="btn-grey-wide"><span>Generate</span></a>
								</td>
								<td>
									<a onclick="downloadCert('<?php print "rootca"; ?>');" class="btn-grey-wide"><span>Download Root CA</span></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<textarea readonly="readonly" class="ipTxtArea" cols="60" rows="12"><?php print $getcaCertDetails."\n"; ?></textarea>
					</td>
					<!--td>
						<textarea readonly="readonly" class="ipTxtArea" cols="60" rows="12"><?php print $commonNameStr.$commonName[1]."\n"; print $stateStr.$state[1]."\n"; print $countryNameStr.$countryName[1]."\n"; print $emailStr.$emailID[1]."\n"; print $orgNameStr.$orgName[1]."\n"; /*print $orgUnitStr.$orgUnit[1]."\n"; */ print $validFromStr.$validFrom[1]."\n"; print $validToStr.$validTo[1]."\n"; ?></textarea>
					</td-->
				</tr>
			</table>
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
			</div>
			</form>
		</div>
	</body>
	<?php
		include_once ("time_panel.php");
		$shell_obj = ShellCommand::getInstance();
		$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", 
				ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
		if( $shell_status == 0) {
			$currentTime =trim( $shell_obj->getLastOutput());
		}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if( submit == 1 ) {
			save_Message(val);
		}
	</script>
</html>
