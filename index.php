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
	$error_tag = 0;
	if (isset($_GET["error_type"])) {
		if ($_GET["error_type"] == 1) {
			$error_tag = 1;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
		<title>:: FreeBlox - Blox Configuration Management Tool ::</title>
		<link href="View/css/style.css" rel="stylesheet" type="text/css">
		<link href="View/css/login.css" rel="stylesheet" type="text/css">
		<script src="View/js/jquery-1.8.2.min.js"></script>
		<script src="View/js/md5.js"></script>
		<noscript><div class="noscript" id = "no_script">Please enable Javascript! </div></noscript>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#PSWD").focus();
			});

			function setPassword() {
				if ( areCookiesEnabled() != false ) {
					var frmObj = document.forms[0];
					frmObj.AUTHPSWD.value = (frmObj.PSWD.value);
					frmObj.submit_action.value = "1";
					return true;
				}
			}

			function checkError(error_tag) {
				var error_div = document.getElementById("error_div");
				if (error_tag == 1) {
					error_div.innerHTML = " Invalid Username or Password !";
				} else {
					error_div.innerHTML = "&nbsp;";
				}
				$("#UNAME").focus();
			}

			function timedOut() {
				var pageUrl = "login_timeout.html";
				self.location.href = pageUrl;
			}

			function setRefreshInterval() {
				setInterval(timedOut, 60000);
			}

/********** Check Cookies enabled function ******/
			function setCookie(c_name,value,exdays) {
				var exdate=new Date();
				exdate.setDate(exdate.getDate() + exdays);
				var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
				document.cookie=c_name + "=" + c_value;
			}

			function getCookie(c_name) {
				var i,x,y,ARRcookies=document.cookie.split(";");
				for (i=0;i<ARRcookies.length;i++) {
					x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
					y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
					x=x.replace(/^\s+|\s+$/g,"");
					if (x==c_name) {
						return unescape(y);
					}
				}
			}

			function areCookiesEnabled() {
				var cookies_disabled = 0;
				if (document.all) {
					if (!navigator.cookieEnabled) {
						cookies_disabled = 1;
						return false;
					}
				} else {
					setCookie('temp','temp');
					var temp = getCookie('temp');
					if (!temp) {
						document.getElementById("enable_cookies").innerHTML = "Please enable cookies in your browser preferences to continue.";
						document.getElementById("enable_cookies").style.display = "block";
						return false;
					} else {
						document.getElementById("enable_cookies").style.display = "none";
					}
				}
				return true;
			}
		</script>
		<base target="_top">
	</head>
	<body bgcolor="#333" onload="checkError(<?php print_r($error_tag); ?>)">
		<form name="login-form" method="post" action="View/login.php" onsubmit="return setPassword()" autocomplete="off">
			<div id = "enable_cookies" class = "noscript"></div>
			<div class="login_wrapper">
				<div class="login_main_container">
					<div class="login_top_logo_space">
						<div class="sbc-logo"></div>
					</div>

					<div class="login_input_field_main_box">
						<div class="txt-uid">
							<input type="text" id="UNAME" name="UNAME" value="" autocomplete="off" style="border: none; outline: 0" />
						</div>
					</div>

					<div class="login_input_field_main_box">
						<div class="txt-pwd">
							<input type="password" id="PSWD" name="PSWD" value="" style="border: none; outline: 0" />
							<input type="hidden" id="AUTHPSWD" name="AUTHPSWD" value="" />
							<input type="hidden" name="submit_action" value="0" />
						</div>
					</div>

					<div class="login_error_msg_space" id="error_div" align="center" style=""> &nbsp; </div>

					<div class="login_button_container">
						<input type="submit" value="" class="login_button" onfocus="if(this.blur)this.blur()" />
					</div>

				</div>
			</div>
		</form>
	</body>
	<script type="text/javascript">
		setRefreshInterval();
		areCookiesEnabled();
	</script>
</html>
