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

	$time=$_GET['time'];
	if($time=="") {
		$time=180;
	}
	$getval=$_GET['val'];
	$filename = "";
	if($getval == 3) {
		session_start();
		$filename =trim($_GET['filename']);
	}
	if($getval == 4){
		$filename = trim($_GET['firmfile']);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link href="css/reboot.css" rel="stylesheet" type="text/css">
		<title>:: <?php print SysCalls::getFullProductName(); ?> ::</title>
		<script type="text/javascript">
			var $main = $.noConflict(true);
			var getvalue = '<?php print $getval;?>';
			var filepath = '<?php print $filename;?>';
			var timeout4sysup = 10*1000 ; // 1000 milliseconds = 1 sec
			var rebootStChkCnt = 0;

			function refreshPage() {
				location.replace("logout.php");
			}

			function setRefreshInterval() {
				var time='<?php print $time;?>';
				setInterval(refreshPage, time*100);
			}

			function setRefreshIntervalFirmware(_tout) {
				if(_tout == null) _tout = 1000 ;
				setTimeout(checkRebootStatus, _tout);
			}

			$main(document).ready(function () {
				if(getvalue == 1) {
					$main.ajax({
						type: "POST",
						cache: "false",
						data: {factoryreset:1},
						url: "show_iface_ip.php",
						url: "admin_service.html",
						global: "false",
						async:"false",
						success: function (response) {
							checkFactoryStatus();
						}
					});
				}

				if(getvalue == 3) {
					$main.ajax({
						type: "POST",
						cache: "false",
						data: {restore_device:1, filename:filepath},
						url:  "show_iface_ip.php",
						global: "false",
						async:"false",
						success: function (response) {
							checkRestoreStatus(response);
						}
					});
				}

				if(getvalue == 4) {
					$main.ajax({
						type: "POST",
						cache: false,
						data: {firmware_upg:"1", filename:filepath},
						url:  "show_iface_ip.php",
						global: "false",
						async:"false",
						success: function (response) {
							checkStatus();
						}
					});
				}
				if(getvalue == 5) {
					$main.ajax({
						type: "POST",
						cache: false,
						data: {auto_upg:"1", upg_now: "1"},
						url:  "show_iface_ip.php",
						global: "false",
						async:"false",
						success: function (response) {
							if(response == "1") {
								checkAutoStatus();
							} else {
								alert("Auto Upgrade is failed");
							}
						}
					});
				}
				if(getvalue == 2) {
					callSysReboot();
				}
			});

			function checkRestoreStatus(statusfilepath) {
				$main.ajax({
					type: "POST",
					cache: "false",
					data: {restore_device:1, checkstatus:1},
					url:  "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (restore_status) {
						if(restore_status == "1") {
							alert("Config restored successfully! Need Apply changes");
						} else {
							alert("Restore config failed!");
						}
						document.location.href = 'index.php';
					}
				});
			}

			function checkFactoryStatus() {
				$main.ajax({
					type: "POST",
					cache: "false",
					data: {factoryreset:1, checkstatus:1},
					url:  "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (factorystatus) {
						if(factorystatus == "-1") {
							setTimeout(checkFactoryStatus, 5000);
						} else if(factorystatus == "0") {
							alert("Factory Reset Success! Need Reboot.");
							callSysReboot();
						} else {
							alert("Factory Reset Failed!");
							document.location.href = 'logout.php';
						}
					}
				});
			}

			function checkStatus() {
				$main.ajax({
					type: "POST",
					cache: false,
					data: {firmware_upg:"1", checkstatus: "status"},
					url: "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response) {
						if(response == "-1") {
							setTimeout(checkStatus, 10000);
						} else if(response == "0") {
							alert("Firmaware upgraded Successfully! Need Reboot.");
							callSysReboot();
						} else {
							alert("Firmaware Upgrade Failed!");
							document.location.href = 'logout.php';
						}
					}
				});
			}

			function checkAutoStatus() {
				$main.ajax({
					type: "POST",
					cache: false,
					data: {auto_upg:"1", checkstatus: "status"},
					url: "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response) {
						if(response == "-1") {
							setTimeout(checkAutoStatus, 5000);
						} else if(response == "0") {
							alert("Auto firmaware upgraded Successfully! Need Reboot.");
							callSysReboot();
						} else {
							alert("Auto Firmaware Upgrade Failed!");
							document.location.href = 'logout.php';
						}
					}
				});
			}

			function callSysReboot() {
				$main.ajax({
					type: "POST",
					cache: false,
					data: {restart:"1"},
					url:  "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response3) {
						if(response3 == "1") {
							setRefreshIntervalFirmware(timeout4sysup * 2);
						}
					}
				});
			}

			function checkRebootStatus() {
				rebootStChkCnt++;
				$main.ajax({
					cache : false,
					url : "show_iface_ip.php",
					global: "false",
					async:"false",
					timeout: 1000,
					success : function() {
						parent.window.location = '/';
					},
					error : function(xhr, ajaxOptions, thrownError) {
						if (rebootStChkCnt < 20) {
							setRefreshIntervalFirmware(timeout4sysup);
						} else {
							$main("#div_reboot").css("display", "none");
							$main("#div_error").css("display", "block");
						}
					}
				});
			}
		</script>
	</head>
	<body bgcolor="#333333">
		<div class="reboot_wrapper">
			<div id="div_reboot" class="reboot_main_container" style="display: block">
				<?php if( $_GET['val'] == 1 ) { ?>
				<div class="top_animation_area"><img src="images/progress.gif" style="padding-top:40px;"></div>
				<div class="main_text_content" align="center">
					Factory reset is in progress. Please wait.
				</div>
				<?php } ?>

				<?php if( $_GET['val'] == 2 ) { ?>
				<div class="top_animation_area"><img src="images/reboot-anim2.gif"></div>
				<div class="main_text_content" align="center">
					Device is restarting. Please wait.
				</div>
				<?php } ?>

				<?php if( $_GET['val'] == 3 ) { ?>
				<div class="top_animation_area"><img src="images/progress.gif" style="padding-top:40px;"></div>
				<div class="main_text_content" align="center">
					Restoring the configuration. Please wait.....
				</div>
				<?php } ?>

				<?php if(($_GET['val'] == 4 ) || ($_GET['val'] == 5 )) { ?>
				<div class="top_animation_area"><img src="images/progress.gif" style="padding-top:40px;"></div>
				<div class="main_text_content" align="center">
					Firmware upgrade is in progress. Please wait.....
				</div>
				<?php } ?>

				<div class="bottom_text_content_01" align="center">
					You will be automatically redirected to the login page.
				</div>
				<!--<div class="relogin_bar" align="center"></div>-->
				<div class="bottom_text_content_02" align="center">
					Note:&nbsp;  Please re-login manually, if IP address is changed.
				</div>
			</div>
			<div id="div_error" class="error_reboot_main_container" style="display: none">
				<div class="error_top_animation_area"><img src="images/warning_70x70.png">
				</div>
				<div class="error_main_text_content" align="center">
					<br/>
					This might be due to a change in IP address.
					<br/>
					Please check the connection and login manually.
				</div>
			</div>
		</div>
	</body>
</html>
