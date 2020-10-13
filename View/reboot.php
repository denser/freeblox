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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link href="css/reboot.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
			var $main = $.noConflict(true);
		</script>
		<script>
			var inter;
			var count = 0;
			function checkRebootStatus() {
				$main.ajax({
					cache : false,
					url : "x.php",
					success : function() {
						inter = window.clearInterval(inter);
						parent.window.location = '../';
					},
					error : function(xhr, ajaxOptions, thrownError) {
						if (xhr.status == 404) {
						}
					}
				});
				count++;
				if (count >= 10) {
					inter = window.clearInterval(inter);
					$main("#div_reboot").css("display", "none");
					$main("#div_error").css("display", "block");
				}
			}

			function systemReboot() {
				$main.ajax({
					type: "POST",
					cache: false,
					data: {reboot_device:1},
					url:  "admin_service.html",
					success: function (html) {
						parent.window.location = 'reboot.php'
					}
				});
			}
		</script>
	</head>
	<body onload="systemReboot()" bgcolor="#333333">
		<div class="reboot_wrapper">
			<div id="div_reboot" class="reboot_main_container" style="display: block">

				<div class="top_animation_area"><img src="images/reboot-anim2.gif">
				</div>
				<div class="main_text_content" align="center">
					System is rebooting. Please wait...
				</div>

				<div class="bottom_text_content_01" align="center">
					You will be automatically redirected to the login page.
				</div>

				<div class="bottom_text_content_02" align="center">
					Note:&nbsp;  Please re-login manually, if IP address is changed.
				</div>
			</div>
			<div id="div_error" class="error_reboot_main_container" style="display: none">
				<div class="error_top_animation_area"><img src="images/warning_70x70.png">
				</div>
				<div class="error_main_text_content" align="center">
					Note: Reboot is unsuccessful
					<br/>
					This might be due to a change in IP address.
					<br/>
					Please check the connection and login manually.
				</div>
			</div>
		</div>
	</body>
</html>
