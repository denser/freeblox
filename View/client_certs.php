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
	include_once("Model/sys_calls.php");
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#tls_cert";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.dataTables.js"></script>
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
			$delete = 0;
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();
			$clientCertList = SysCalls::getCertList();
			unset($clientCertList[count($clientCertList)-1]);
			$clientCertCount =  count($clientCertList);
			$ca_config_mode = SysCalls::getCAConfigStatus();
			if(isset($_POST) && isset($_POST['submit_action'])) {
				if($_POST['submit_action'] == "3" && $_POST['CLIENT_CERT'] != "") {
					SysCalls::deleteClientCert($_POST['CLIENT_CERT']);
					$delete = 1;
				}
			}
		?>
		<script type="text/javascript">
			$sub(document).ready(function() {
				$sub('#clientCertsTable').dataTable( {
					"sScrollX": "1100px",
					"sScrollY": "200px",
					"aaSorting": [[ 0, "asc" ]],
					"aoColumns":[
						{"bSortable": true},
						{"bSortable": false}
					],
					"bPaginate": false
				} );
				getPageTips("clientCerts");
				setTooltip();
			});

			function submitData() { 
				if($sub("#clientCerts").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "client_certs.php";
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "client_certs.php";
				}
			}

			function generateClientCerts(val) {
				parent.callEditPage(val,'generate_client_ca.php', 360, 550);
			}

			function delete_data(val) {
				parent.callConfirmBox(val);
			}

			function deleteAction(val, deletetoken) {
				var formObj = document.forms[0];
				if(val == 1) {
					formObj.submit_action.value = "3";
					formObj.CLIENT_CERT.value = deletetoken;
					formObj.submit();
				}
			}

			function showClientCertDetails(val) {
				parent.callEditPage(val,'show_client_cert_details.php',200,750);
			}

			function downloadClientCert(val) {
				parent.downloadLogs(val);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $delete;?>');">
		<div class="content">
			<?php
				$class = "";
				if ($update_count > 0 ) {
					include_once("notification.html");
					$class = "notify_changes";
				}
			?>
			<h1 class="<?php print $class; ?>">Client Certificates<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="clientCerts" method="post" action="client_certs.php" autocomplete="off">
				<table>
					<tr>
					<?php
						$generate = "generate";
						if (strcmp(trim($ca_config_mode),$generate) == 0 ) {
					?>
						<td>
							<a onclick="generateClientCerts('<?php print "generate"; ?>');" class="btn-grey-wide"><span>Generate Client Certificates</span></a>
						</td>
					<?php } ?>
					</tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px; margin-top: 10px;" class="table-config no-border">
					<table id="clientCertsTable" class="tablesorter" cellpadding="0" cellspacing="1px">
						<thead>
							<tr>
								<th><a href="#">Certificates</a></th>
								<td width="100px"><label>Options</label></td>
							</tr>
						</thead>
						<tbody>
							<?php
								for ($i=0; $i < $clientCertCount; $i++) {
									$certArray = explode(',',$clientCertList[$i]);
									$cert_name =  $certArray[0];
									$cert_type = $certArray[1];
									$cert_expire = $certArray[2];
									$cert_regen = $certArray[3];
									if ($cert_name != "" && $cert_type == 2) {
							?>
							<tr>
								<td><?php print $cert_name; ?></td>
								<td><input type="button" title="Delete" alt="Delete" class="btn-delete" onclick="delete_data('<?php print $cert_name; ?>');" onfocus="if(this.blur)this.blur()" />
								<a title="Client Cert Details" class="btn-certificate-small" onclick="showClientCertDetails('<?php print $cert_name; ?>')"></a>
								<a title="Download Certificate" class="btn-certificate-small" onclick="downloadClientCert('<?php print $cert_name; ?>')"></a>
								</td>
							</tr>
							<?php
									}
								}
							?>
						</tbody>
					</table>
				</table>
				<input type="hidden" name="CLIENT_CERT" id="CLIENT_CERT" value="" />
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
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
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
	if( $shell_status == 0) {
		$currentTime =trim($shell_obj->getLastOutput());
	}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
	</script>
</html>
