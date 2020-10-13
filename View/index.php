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
	include_once("Model/sys_calls.php");
	include_once("Model/db_mgr.php");
	include_once("Model/shell_command.php");

	$sessTimeOut = $_SESSION['sessTimeout'];
	$end_user = 0;
	$checkarch =SysCalls::getSysArch();
	if($checkarch == "armv7l") {
		$end_user = 1; 
	}
	$checkAutoUpg = 0;
	if($_SESSION['notify_user'] == 1) {
		$checkAutoUpg = SysCalls::checkUpgAvail();
	}
	$bloxconfig = 0;
	$dbh = DB_Handle::Connect();
	$result = $dbh->getUniqueActionListName();
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		if($row['T_NAME'] == 0) {
			$bloxconfig = 1;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=8">
		<title>:: <?php print SysCalls::getFullProductName(); ?> ::</title>
		<link rel="stylesheet" href="css/jquery.ui.all.css">
		<link rel="stylesheet" href="css/demos.css">
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="jquery.ui.base.css">
		<script src="js/jquery-1.8.2.min.js"></script>
		<script src="js/jquery.ui.core.js"></script>
		<script src="js/jquery.ui.widget.js"></script>
		<script src="js/jquery.ui.accordion.js"></script>
		<script src="js/jquery.ui.button.js"></script>
		<script src="js/jquery.ui.position.js"></script>
		<script src="js/jquery.ui.tooltip.js"></script>
		<script src="js/jquery.ui.dialog.js"></script>
		<script src="js/menu.js"></script>
		<noscript><div class="noscript">Please enable Javascript!</div></noscript>
		<style>
			.ui-menu {
				position: absolute;
				width: 100px;
			}
			.no-close .ui-dialog-titlebar-close {
				display: none;
			}
			.custom-add-edit-dialog {
				border: none;
				padding: 0px;
				color: #444444;
			}
			</style>
		<script type="text/javascript">
			var sync = true;
			var $main = $.noConflict(true);
			var sessVal = '<?php print $sessTimeOut; ?>';
			var sessTimeout = parseInt(sessVal) + parseInt(2) + parseInt(900);
			var limit ="0:"+sessTimeout;
			if (document.images) {
				var parselimit = limit.split(":");
				parselimit = parselimit[0]*60+parselimit[1]*1;
			}
			var bloxconfig = '<?php print $bloxconfig; ?>';

			function beginrefresh() {
				$main.ajax({
					type: "GET",
					cache: "false",
					data: {ping:"1"},
					url:  "is_valid_session.php",
					global: "false",
					async:"false",
					success: function (res) {
						time_to_expire = res ;
						if(time_to_expire<=0) {
							parent.window.location = "logout.php"
						}
						curtime = "session timeout in " + time_to_expire ;
						console.log( curtime );
						setTimeout(beginrefresh, 10000);
					}
				});
			}

			$main(document).ready(function() {
				beginrefresh();
			});

			var option_display = 0;
			var parent_display = 0;

			function restoreDbIgnorechanges() {
				$main.ajax({
					type: "POST",
					cache: "false",
					data: {restoredb:"1"},
					url:  "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (res) {
					}
				});
			}

			function deleteBackupDb() {
				$main.ajax({
					type: "POST",
					cache: "false",
					data: {deletedb:"1"},
					url:  "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response) {
					}
				});
			}

			function showMenu() {
				if (option_display == 0) {
					var div = document.getElementById('option-list-box');
					div.style.display = 'block';
					option_display = 1;
					parent_display = 0;
				} else {
					var div = document.getElementById('option-list-box');
					div.style.display = 'none';
					option_display = 0;
				}
			}

			function hideMenu() {
				if (option_display == 1 && parent_display == 1) {
					var div = document.getElementById('option-list-box');
					div.style.display = 'none';
					option_display = 0;
				}
				parent_display = 1;
			}

			var current_setup_tab = 0;
			var default_color = "#EEEEEE";
			var selected_color = "#999999";
			var hover_color = "#DCDCDC";

			function callEditPage(val, pageid, ht, wt) {
				$main("#dialog-popup").html("");
				$main("#dialog-popup").load(pageid + "?data=" + val).dialog({
					modal : true,
					height : ht,
					width : wt,
					closeOnEscape : false,
					dialogClass : "no-close"
				});
				$main(".ui-dialog-titlebar").hide();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog");
				$main("#dialog-popup").removeClass("ui-dialog-content");
			}

			function callAddPage(pageid, ht, wt) {
				$main("#dialog-popup").html("");
				$main("#dialog-popup").load(pageid).dialog({
					modal : true,
					height : ht,
					width : wt,
					closeOnEscape : false,
					dialogClass : "no-close"
				});
				$main(".ui-dialog-titlebar").hide();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog");
				$main("#dialog-popup").removeClass("ui-dialog-content");
			}

			function webSettings() {
				$main("#dialog-popup").html("");
				$main("#dialog-popup").load("web-settings.php").dialog({
					modal : true,
					height : 430,
					width : 600,
					closeOnEscape : false,
					dialogClass : "no-close"
				});
				$main(".ui-dialog-titlebar").hide();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog");
				$main("#dialog-popup").removeClass("ui-dialog-content");
			}

			function endUserLicence() {
				$main(document).ready(function() {
				$main("#dialog-popup").html("");
				$main("#dialog-popup").load("end-user-licence.php").dialog({
					modal : true,
					height : 430,
					width : 640,
					closeOnEscape : false,
					dialogClass : "no-close"
				});
				$main(".ui-dialog-titlebar").hide();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog");
				$main("#dialog-popup").removeClass("ui-dialog-content");
				});
			}

			var notify_user = '<?php print $_SESSION['notify_user']; ?>';
			var checkUbuntu = '<?php print $end_user; ?>';
			if ( notify_user == "1" && checkUbuntu != "1" ) {
				endUserLicence();
			}

			function updateDetails(elem) {
				if (parseInt(elem.value) > 0) {
					$main("#dialog-popup").html("");
					$main("#dialog-popup").load("update-details.php").dialog({
						modal : true,
						height : 560,
						width : 600,
						closeOnEscape : false,
						dialogClass : "no-close"
					});
					$main(".ui-dialog-titlebar").hide();
					$main(".ui-widget-content").addClass("custom-add-edit-dialog");
					$main("#dialog-popup").removeClass("ui-dialog-content");
				}
			}

			function helpPage() {
				$main("#dialog-popup").html("");
				$main("#dialog-popup").load("help.php").dialog({
					modal : true,
					height : 560,
					width : 600,
					closeOnEscape : false,
					dialogClass : "no-close"
				});
				$main(".ui-dialog-titlebar").hide();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog");
				$main("#dialog-popup").removeClass("ui-dialog-content");
			}

			function closeAddEditDialog() {
				$main("#dialog-popup").dialog("close");
			}

			function autoUpgMsg() {
				document.getElementById("autoupg").style.display = "block";
				document.getElementById("autoupg_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-autoupg").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
                                                "Download & Install Now" : function() {
							$main(this).dialog("close");
							autoUpgNow();
						},
                                                "Ignore" : function() {
                                                        $main(this).dialog("close");
							autoUpgIgnore();
						},
						"Later" : function() {
							$main(this).dialog("close");
                                                }
					}
				});
				$main(".ui-dialog-titlebar").show();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog");
				$main("#dialog-popup").removeClass("ui-dialog-content");
			}

			function callConfirmBox(val) {
				document.getElementById("confirmdata").style.display = "block";
				document.getElementById("confirmdata_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-confirm").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"YES" : function() {
							$main(this).dialog("close");
							callDelete(1, val);
						},
						"NO" : function() {
							$main(this).dialog("close");
							callDelete(0, val);
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}
/*----- Begin : Confirmation dialog for factoryreset/restart/shutdown device -----*/
			function callConfirm(val) {
				document.getElementById("admindata").style.display = "block";
				document.getElementById("admindata_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-admin").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"YES" : function() {
							$main(this).dialog("close");
							systemService(1, val);
						},
						"NO" : function() {
							$main(this).dialog("close");
							systemService(0, val);
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function callConfirmFirmUpgrade() {
				document.getElementById("firmupgdata").style.display = "block";
				document.getElementById("firmupgdata_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-firmwareUpgrade").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"YES" : function() {
							$main(this).dialog("close");
							callFirmUpg(1);
						},
						"NO" : function() {
							$main(this).dialog("close");
							callFirmUpg(0);
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function systemService(yes, no) {
				if (yes == 1) {
					document.getElementById('container').contentWindow.callsystemService(yes, no);
				}
			}
/*----- Begin : Confirmation dialog for factoryreset/restart/shutdown device -----*/
			function downloadLogs(val_name) {
				if(val_name == "alert") {
					document.getElementById("downloadalert").style.display = "block";
					document.getElementById("download_alert").style.display = "block";
					$main(".ui-dialog-titlebar").show();
					$main("#dialog-alert").dialog({
						resizable : false,
						dialogClass : "no-close",
						height : 160,
						width : 450,
						modal : true,
						closeOnEscape : false,
						buttons : {
							"OK" : function() {
								$main(this).dialog("close");
							}
						}
					});
					$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
				} else if(val_name == "diagnostics") {
					document.getElementById("downloaddiagnostics").style.display = "block";
					document.getElementById("download_diagnostics").style.display = "block";
					$main(".ui-dialog-titlebar").show();
					$main("#dialog-diagnostics").dialog({
						resizable : false,
						dialogClass : "no-close",
						height : 160,
						width : 450,
						modal : true,
						closeOnEscape : false,
						buttons : {
							"OK" : function() {
								$main(this).dialog("close");
							}
						}
					});
					$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
				} else if ( val_name == "rootca" ) {
					document.getElementById("downloadrootCA").style.display = "block";
					document.getElementById("download_rootCA").style.display = "block";
					var str = "Download Root CA";
					var resLink = str.link("download.php?id="+val_name);
					document.getElementById("rootCA").innerHTML = resLink;
					$main(".ui-dialog-titlebar").show();
					$main("#dialog-rootCA").dialog({
						resizable : false,
						dialogClass : "no-close",
						height : 160,
						width : 450,
						modal : true,
						closeOnEscape : false,
						buttons : {
							"OK" : function() {
								$main(this).dialog("close");
							}
						}
					});
					$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
				} else {
					document.getElementById("downloadcerts").style.display = "block";
					document.getElementById("download_certs").style.display = "block";
					var str = "Download Client Certificate";
					var resLink = str.link("download.php?id="+val_name);
					document.getElementById("clientCerts").innerHTML = resLink;
					$main(".ui-dialog-titlebar").show();
					$main("#dialog-certs").dialog({
						resizable : false,
						dialogClass : "no-close",
						height : 160,
						width : 450,
						modal : true,
						closeOnEscape : false,
						buttons : {
							"OK" : function() {
								$main(this).dialog("close");
							}
						}
					});
					$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
				}
			}

			function downloadDb() {
				document.getElementById("downloadconfig").style.display = "block";
				document.getElementById("download_config").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-config").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function downloadCdr() {
				document.getElementById("downloadcdr").style.display = "block";
				document.getElementById("download_cdr").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-cdr").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function dialogGenMsg(title,msg,color="black") {
				document.getElementById("dialog-genbox-msg").innerHTML = msg;
				document.getElementById("dialog-genbox-msg").style.color= color;
				document.getElementById("dialog-genbox-block").style.display = "block";
				document.getElementById("dialog-genbox-style").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				document.getElementById("dialog-genbox").title = title;
				$main("#dialog-genbox").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
						}
					}
				});
			}

			function downloadpackcap() {
				document.getElementById("downloadpackcap").style.display = "block";
				document.getElementById("download_packcap").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-packet").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
							parent.document.getElementById("container").src = "packetcap.php";
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			var def_passwd = "**********" ;

			function user_athentication(del_rec) {
				document.getElementById("pwd_error_div").innerHTML = "";
				document.getElementById("deleteauth").style.display = "block";
				document.getElementById("deleteauth_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-deleteauth").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"YES" : function() {
							pwd_authen(del_rec);
							document.getElementById("del_pwd").value = def_passwd;
						},
						"NO" : function() {
							$main(this).dialog("close");
							calldelauth(0);
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function ssh_athentication() {
				document.getElementById("pwd_error_div").innerHTML = "";
				document.getElementById("deleteauth").style.display = "block";
				document.getElementById("deleteauth_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-deleteauth").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"Connect" : function() {
							if(sync) {
								sync = false;
								ssh_pwd_authen();
								sync = true;
							}
							document.getElementById("del_pwd").value = def_passwd;
						},
						"Cancel" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function onClick_pwd() {
				document.getElementById("pwd_error_div").innerHTML = "";
				if(document.getElementById("del_pwd").value == def_passwd) {
					document.getElementById("del_pwd").type = "password";
					document.getElementById("del_pwd").value = def_passwd;
				}
			}

			function saveMessage(val) {
				document.getElementById("savedata").style.display = "block";
				document.getElementById("save_div").style.display = "block";
				document.getElementById("save_div").innerHTML = val;
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-save").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function mgmtAccessDefault(val) {
				document.getElementById("mgmtdata").style.display = "block";
				document.getElementById("mgmt_div").style.display = "block";
				document.getElementById("mgmt_div").innerHTML = val;
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-mgmt").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function sigUpdateMsg(val) {
				document.getElementById("sigupdatedata").style.display = "block";
				document.getElementById("sigupdate_div").style.display = "block";
				var error_upload = "error";
				var status = new RegExp(error_upload, "i")
				var result = val.match(status);
				if( val == "Running sigupdate task. Please wait for a moment....") {
					$main("#sigupdate_div").next(".ui-dialog-buttonpane button:contains('OK')")
						.button("disable");
					document.getElementById("sigupdate_div").className = "wait";
				} else if( result != null) {
					document.getElementById("sigupdate_div").className = "error";
				} else {
					$main("#sigupdate_div").next(".ui-dialog-buttonpane button:contains('OK')")
					.button("enable");
					document.getElementById("sigupdate_div").className = "msg";
				}
				document.getElementById("sigupdate_res").innerHTML = val;
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-sigupdate").dialog({
					resizable : false,
					dialogClass : "no-close",
					modal : true,
					closeOnEscape : false,
					buttons : {
					"OK" : function() {
						$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function geoIPMessage(val) {
				document.getElementById("geoipdata").style.display = "block";
				document.getElementById("geoip_div").style.display = "block";
				if( val == "Geo IP Update is in progress, Please wait....") {
					$main("#geoip_div").next(".ui-dialog-buttonpane button:contains('OK')") .button("disable");
					$main("#dialog-geoip").dialog({
						resizable : false,
						dialogClass : "no-close",
						modal : true,
						height : 160,
						width : 450,
						closeOnEscape : false,
						buttons : {
						"OK" : function() {
							$main(this).dialog("");
							}
						}
					});
					document.getElementById("geoip_div").className = "geowait";
				} else if( val == "Geo IP Update successfully applied.") {
					$main("#geoip_div").next(".ui-dialog-buttonpane button:contains('OK')") .button("disable");
					$main("#dialog-geoip").dialog({
						resizable : false,
						dialogClass : "no-close",
						modal : true,
						height : 160,
						width : 450,
						closeOnEscape : false,
						buttons : {
							"OK" : function() {
								$main(this).dialog("close");
							}
						}
					});
						 document.getElementById("geoip_div").className = "msg";
				}
				document.getElementById("geoip_div").innerHTML = val;
				$main(".ui-dialog-titlebar").show();
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function errorMessage(val) {
				document.getElementById("errordata").style.display = "block";
				document.getElementById("error_div").style.display = "block";
				document.getElementById("error_div").innerHTML = val;
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-error").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function callConfirmAllBox(val) {
				document.getElementById("confirmdata").style.display = "block";
				document.getElementById("confirmdata_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-confirm").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"YES" : function() {
							$main(this).dialog("close");
							callAllDelete(1, val);
						},
						"NO" : function() {
							$main(this).dialog("close");
							callAllDelete(0, val);
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function rebootDevice() {
				document.getElementById("confirmreboot").style.display = "block";
				document.getElementById("confirmreboot_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-reboot").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"YES" : function() {
							parent.window.location = 'reboot.php'
						},
						"NO" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function updateSuccess(val) {
				document.getElementById("confirmsuccess").style.display = "block";
				if( val < 1 ) {
					document.getElementById("confirmsuccess_div").innerHTML = "No configuration changes were found!";
					refreshFrame();
				} else {
					document.getElementById("confirmsuccess_div").innerHTML = "All the configuration files are successfully updated.";
					refreshFrame();
				}
				document.getElementById("confirmsuccess_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-success").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function applyError(val) {
				document.getElementById("apply_error").style.display = "block";
				document.getElementById("apply_error_div").style.display = "block";
				$main("#apply_error_div").html(val);
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-error").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"Close" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			function applyReboot() {
				document.getElementById("apply_reboot").style.display = "block";
				document.getElementById("apply_reboot_div").style.display = "block";
				$main(".ui-dialog-titlebar").show();
				$main("#dialog-apply-reboot").dialog({
					resizable : false,
					dialogClass : "no-close",
					height : 160,
					width : 450,
					modal : true,
					closeOnEscape : false,
					buttons : {
						"OK" : function() {
							callSysReboot();
							$main(this).dialog("close");
						},
						"Cancel" : function() {
							$main(this).dialog("close");
						}
					}
				});
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog");
			}

			$main(function() {
				$main("#accordion").accordion({
					heightStyle : "content"
				});
			});

			function callDelete(val, deletetoken) {
				if (val == 1) {
					document.getElementById('container').contentWindow.deleteAction(val, deletetoken);
				}
			}

			function callFirmUpg(val) {
				if(val == 1) {
					document.getElementById('container').contentWindow.firmUpgAction(val);
				}
			}

			function calldelauth(val) {
				document.getElementById('container').contentWindow.delete_auth(val);
			}

			function call_ssh_auth(val) {
				document.getElementById('container').contentWindow.sshTerminalConnect(val);
			}

			function pwd_authen(records) {
				var delpwd = document.getElementById("del_pwd").value;
				if(delpwd != "") {
					$main.ajax({
						type: "POST",
						cache: "false",
						data: {pwd_auth:"1", pwd: delpwd, del_record: records},
						url:  "show_iface_ip.php",
						global: "false",
						async:"false",
						success: function (response) {
							if (response == "2") {
								document.getElementById("pwd_error_div").innerHTML = "Invalid password";
							} else if(response == "3" ) {
								document.getElementById("pwd_error_div").innerHTML = "Delete Records failed";
							} else if(response == "1") {
								$main("#dialog-deleteauth").dialog("close");
								calldelauth(1);
							}
						}
					});
				} else {
					document.getElementById("pwd_error_div").innerHTML = "Enter password";
				}
			}

			function ssh_pwd_authen() {
				var user_pwd = document.getElementById("del_pwd").value;
				if(user_pwd != "") {
					$main.ajax({
						type: "POST",
						cache: "false",
						data: {ssh_pwd_auth:"1", pwd: user_pwd},
						url:  "show_iface_ip.php",
						global: "false",
						async:"false",
						success: function (response) {
							if (response == "0") {
								document.getElementById("pwd_error_div").innerHTML = "Invalid password";
							} else if(response == "1") {
								$main("#dialog-deleteauth").dialog("close");
								call_ssh_auth("0");
							} else {
								$main("#dialog-deleteauth").dialog("close");
								call_ssh_auth(response);
							}
						}
					});
				} else {
					document.getElementById("pwd_error_div").innerHTML = "Enter password";
				}
			}

			function autoUpgNow() {
				var upgFile = "/var/tmp/blox_update.tgz";
				$main.ajax({
					type: "POST",
					cache: "false",
					data: {autoUpgNow:"1"},
					url:  "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response) {
						if(response  == "1") {
							parent.window.location = 'wait.php?time=900&val=4&firmfile='+ upgFile;
						} else {
							alert("Auto Upgrade Download Failed");
						}
					}
				});
			}

			function autoUpgIgnore() {
				$main.ajax({
					type: "POST",
					cache: "false",
					data: {autoUpgIgnore:"1"},
					url:  "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response) {
					}
				});
			}
			function callAllDelete(val, deletetoken) {
				if (val == 1) {
					document.getElementById('container').contentWindow.deleteAllAction(val, deletetoken);
				}
			}

			function helpTooltip() {
				$main(".btn-top").tooltip({
					show : null,
					position : {
						my : "left top",
						at : "left bottom"
					},
					open : function(event, ui) {
						ui.tooltip.animate({
							top : ui.tooltip.position().top + 10
						}, "fast");
					}
				});
			}

			function refreshFrame() {
				document.getElementById("container").src = document.getElementById("container").src;
			}

			function setUpdateValue(val) {
				if (parseInt(val) == 0) {
					$main("#applychanges").attr("class", "apply-changes-disable");
					$main("#ignorechanges").attr("class", "ignore-changes-disable");
				} else {
					$main("#updates").css("display","block");
					$main("#applychanges").attr("class", "apply-changes");
					$main("#ignorechanges").attr("class", "ignore-changes");
				}
				$main("#updates").val(val);
			}

			function callSysReboot() {
				parent.window.location = "wait.php?time=2100&val=2";
			}

			function setUpdates() {
				$main("#applychanges").css("display", "none");
				$main("#ignorechanges").css("display", "none");
				$main("#updates").css("display", "none");
				$main("#applychangessaving").css("display", "block");
				if(bloxconfig == 1){
					deleteBackupDb();
				}
				$main.ajax({
					url : "../Model/apply_changes.php"
				}).done(function(output) {
					var list = output.split(",|,");
					var list_length = list.length;
					var response;
					var reboot ;
					for(var i=0; i < list.length; i++) {
						response = list[i].split(",");
						if(response[1] == "FAILURE") {
							break;
						}
						if(response[2] == "YES") {
							reboot = "YES" ;
						}
					}
					var no_of_changes = $main("#updates").val();
					if(response[1] == "FAILURE") {
						applyError(response[2]);
						$main("#updates").css("display","none");
						$main("#applychangessaving").css("display", "none");
						$main("#applychanges").css("display", "block");
						$main("#applychanges").attr("class", "apply-changes-disable");
						$main("#ignorechanges").css("display", "block");
						$main("#ignorechanges").attr("class", "ignore-changes-disable");
					} else {
						$main("#updates").css("display","none");
						$main("#applychangessaving").css("display", "none");
						$main("#applychanges").css("display", "block");
						$main("#applychanges").attr("class", "apply-changes-disable");
						$main("#ignorechanges").css("display", "block");
						$main("#ignorechanges").attr("class", "ignore-changes-disable");
						if(reboot == "YES") {
							applyReboot();
						} else {
							updateSuccess(no_of_changes);
						}
					}
				});
			}

			function ignoreChanges() {
				if(bloxconfig == 1){
					restoreDbIgnorechanges();
					$main("#updates").css("display","none");
					$main("#applychangessaving").css("display", "none");
					$main("#applychanges").css("display", "block");
					$main("#applychanges").attr("class", "apply-changes-disable");
					$main("#ignorechanges").css("display", "block");
					$main("#ignorechanges").attr("class", "ignore-changes-disable");
					parent.document.getElementById("container").src = "dashboard.php";
				} else {
					$main.ajax({
						type:"POST",
						data:{ignore_config:1},
						url : "../Model/apply_changes.php"
					}).done(function(output) {
						$main("#updates").css("display","none");
						$main("#applychangessaving").css("display", "none");
						$main("#applychanges").css("display", "block");
						$main("#applychanges").attr("class", "apply-changes-disable");
						$main("#ignorechanges").css("display", "block");
						$main("#ignorechanges").attr("class", "ignore-changes-disable");
						parent.document.getElementById("container").src = "dashboard.php";
					});
				}
			}

			var set_interval;
			var checkAutoUpg = '<?php print $checkAutoUpg; ?>';
			$main(document).ready(function() {
				document.getElementById("container").src = "dashboard.php";
                                if ( notify_user == "1" && checkUbuntu == "0" ) {
					set_interval = setInterval(isDialogOpen, 1000);
                                }
				if(notify_user == "1" && checkUbuntu == "1" ) {
					if(checkAutoUpg != "0") {
						autoUpgMsg();
					}
				}
				<?php $_SESSION['notify_user'] = "0"; ?>
			});

			function isDialogOpen() {
				var isOpen = $main( "#dialog-popup" ).dialog( "isOpen" );
				if(isOpen != 1) {
					clearInterval(set_interval);
					if(checkAutoUpg != "0") {
						autoUpgMsg();
					}
				}
			}
			function openDashboard() {
				document.getElementById("container").src = "dashboard.php";
			}

			function Log_out() {
				document.location.href = "logout.php";
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
						if(document.getElementById("enable_cookies") != null) {
							document.getElementById("enable_cookies").style.display = "none";
						}
					}
				}
				return true;
			}
		</script>
	</head>
	<body onclick="hideMenu()">
		<div id="alert-box">
		<table style="margin: 0 auto; width: 100%;" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
				<table style="width: 100%; height: 70px;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="page-header"><a href="" onclick="openDashboard();"><div class="main-logo"></div></a>
						<div class="login-details-div" >
							<div class="login-details" style="display: none">
								Last Login : Wed 10, 2013
								<br />
								Current Login : Fri 12, 2013
							</div>
						</div>
						<div class="header-right-content">
						<table>
							<tr>
								<td>
									<table style="width: 100%; height: 45px; margin-top:-16px;" cellpadding="0" cellspacing="0">
										<tr><td style="color:#cdebf0; float:right; padding-right:0px;">
										<div class="logout-panel">
											<label style="float: left; padding: 5px">Welcome <span class="username"><?php print "admin"; //$_SESSION['user']; ?></span>&nbsp;</label>
											<input type="button" class="btn-options" onclick="showMenu();" onfocus="if(this.blur)this.blur()" />
											<span id="option-list-box" class="option-list-box">
											<input type="button" class="btn-option-list" value="Web Settings" onclick="webSettings();" onfocus="if(this.blur)this.blur()" />
											<input type="button" class="btn-option-list" value="End User Licence" onclick="endUserLicence();" onfocus="if(this.blur)this.blur()" />
											<input type="button" onclick="Log_out();" class="btn-option-list" value="Logout" onfocus="if(this.blur)this.blur()" />
											</span>
										</div>
										</td></tr>
										<tr><td style="color:#cdebf0; float:right; margin-top:-3px; padding-right:10px;"> FreeBlox version : <span style="color:#cdebf0;"><b><?php print SysCalls::getAppVersion();?></b></span></td></tr>
										<tr><td style="color:#cdebf0; float:right; padding-right:10px;">
											<label style="font-size: 12px; display: block; color: #cdebf0;" id="table_time"></label>
											<label style="font-size: 12px; display: none;" id="system_time"></label>
										</td></tr>
										<tr>
											<td class="page-refresh">
												<input type="button" class="btn-refresh" title="Refresh" align="left" onclick="refreshFrame()" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
									</table>
								</td>
								<td width="30">&nbsp;</td>
								<td>
									<a id="ignorechanges" class="ignore-changes-disable" onclick="ignoreChanges()">
									<a id="applychanges" class="apply-changes-disable" onclick="setUpdates()"><a id="applychangessaving" class="apply-changes-saving"></a>
									<input id="updates" type="button" class="btn-notify" value="0" onclick="updateDetails(this);" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
						</table>
						</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td valign="top">
				<table style="width: 100%; height: 480px" cellpadding="0" cellspacing="0">
					<tr>
						<td id="main-menu" style="width: 15%;" valign="top">
							<?php include_once("menu.php"); ?>
						</td>
						<td style="width: 85%;" valign="top"><iframe src="" id="container" name="container" frameborder="0" style="height: 500px; width: 100%; border: 0px; margin: 0px;"></iframe></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td align="center" style="position: fixed; bottom: 0px; border-top: 1px solid #cccccc; width: 100%; background-color: #eeeeee; height: 30px">
				<div style="margin-top: 5px; color: #555; font-size: 11px">
					Copyright &copy; 2015-2018. <?php print SysCalls::getFullProductName(); ?> - Web Panel. All Rights Reserved.
				</div></td>
			</tr>
		</table>

		<div id="dialog-confirm" title="Confirm">
			<p id="confirmdata" style="display: none">
				<img src="images/icn-warning.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="confirmdata_div" style="font-size: 13px; margin-left: 10px; display: none">
					Are you sure you want to delete the selected items?
				</div>
			</p>
		</div>

		<div id="dialog-admin" title="Confirm">
			<p id="admindata" style="display: none">
				<img src="images/icn-warning.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="admindata_div" style="font-size: 13px; margin-left: 10px; display: none">
					Are you sure you want to continue ?
				</div>
			</p>
		</div>

		<div id="dialog-firmwareUpgrade" title="Confirm">
			<p id="firmupgdata" style="display: none">
				<img src="images/icn-warning.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="firmupgdata_div" style="font-size: 13px; margin-left: 10px; display: none">
					Changes are not applied! Do you want to ignore the changes ?
				</div>
			</p>
		</div>

		<div id="dialog-alert" title="Download Alerts">
			<p id="downloadalert" style="display: none">
				<div id="download_alert" style="font-size: 13px; margin-left: 10px; display: none">
					<div class="download">
							<a href="download.php?id=alert">Download Alerts</a>
					</div>
				</div>
			</p>
		</div>

		<div id="dialog-genbox">
			<p id="dialog-genbox-block" style="display: none">
				<div id="dialog-genbox-style" style="font-size: 13px; margin-left: 10px; display: none">
					<div id="dialog-genbox-msg" align="center"> &nbsp; </div>

				</div>
			</p>
		</div>

		<div id="dialog-cdr" title="Download CDR Reports">
			<p id="downloadcdr" style="display: none">
				<div id="download_cdr" style="font-size: 13px; margin-left: 10px; display: none">
					<div class="download">
						<a href="download.php?id=cdr">Download CDR Reports</a>
					</div>
				</div>
			</p>
		</div>

		<div id="dialog-config" title="Download Configuration">
			<p id="downloadconfig" style="display: none">
				<div id="download_config" style="font-size: 13px; margin-left: 10px; display: none">
					<div class="download">
					<table>
					<tr>
						<td>
							<a href="download.php?id=db">Download Configuration</a>
						</td>
					</tr>
					<tr>
						<td>
							Click above link to download the configuration.
						</td>
					</tr>
					</table>
					</div>
				</div>
			</p>
		</div>

		<div id="dialog-packet" title="Download packet Capture">
			<p id="downloadpackcap" style="display: none">
				<div id="download_packcap" style="font-size: 13px; margin-left: 10px; display: none">
				<div class="download">
				<table>
				<tr>
					<td>
						<a href="download.php?id=packet_cap">Download Packet Capture</a>
					</td>
				</tr>
				<tr>
					<td>
						Click above link to download the packet Capture.
					</td>
				</tr>
				</table>
				</div>
				</div>
			</p>
		</div>

		<div id="dialog-deleteauth" title="Password Confirm">
			<p id="deleteauth" style="display: none">
				<img src="images/icn-warning.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="deleteauth_div" style="font-size: 13px; margin-left: 10px; display: none">
					<table>
						<tr>
							<td> Enter Password <td>
							<td>
								<td><input maxlength=64; size=""; class="ipBox" id="del_pwd" autocomplete="off" value="**********" onclick="onClick_pwd()" /></td>
							</td>
						</tr>
					</table>
					<div class="login_error_msg_space" id="pwd_error_div" align="center" style="color:red"> &nbsp; </div>
				</div>
			</p>
		</div>

		<div id="dialog-autoupg" title="Auto Upgrade Blox">
			<p id="autoupg" style="display: none">
				<img src="images/icn-warning.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="autoupg_div" style="font-size: 13px; margin-left: 10px; display: none">
					New Version <?php print $checkAutoUpg; ?> available
				</div>
			</p>
		</div>

		<div id="dialog-diagnostics" title="Download Report">
			<p id="downloaddiagnostics" style="display: none">
				<div id="download_diagnostics" style="font-size: 13px; margin-left: 10px; display: none">
					<div class="download">
						<a href="download.php?id=diagnostics">Download Report</a>
					</div>
				</div>
			</p>
		</div>

		<div id="dialog-certs" title="Download Client Certificate">
			<p id="downloadcerts" style="display: none">
				<div id="download_certs" style="font-size: 13px; margin-left: 10px; display: none">
					<div class="download" id="clientCerts">
					</div>
				</div>
			</p>
		</div>

		<div id="dialog-rootCA" title="Download Root CA">
			<p id="downloadrootCA" style="display: none">
				<div id="download_rootCA" style="font-size: 13px; margin-left: 10px; display: none">
					<div class="download" id="rootCA">
					</div>
				</div>
			</p>
		</div>

		<div id="dialog-save" title="Message">
			<p id="savedata" style="display: none">
				<div class="msg" id="save_div">
				</div>
			</p>
		</div>

		<div id="dialog-mgmt" title="Message">
			<p id="mgmtdata" style="display: none">
				<div class="mgmt_default" id="mgmt_div">
				</div>
			</p>
		</div>

<!-- begin: GeoIPUpdate Message -->
		<div id="dialog-geoip" title="GeoIP Update">
			<p id="geoipdata" style="display: none">
				<div class="geowait" id="geoip_div">
				</div>
			</p>
		</div>
<!-- begin: GeoIPUpdate Message -->

<!-- SigUpdate -->
		<div id="dialog-sigupdate" title="Signature Update">
			<p id="sigupdatedata" style="display: none">
				<div class="wait" id="sigupdate_div">
					<textarea cols ="60" id="sigupdate_res" rows= "15" readonly ="readonly"></textarea>
				</div>
			</p>
		</div>

<!-- SigUpdate -->
		<div id="dialog-error" title="Error">
			<p id="errordata" style="display: none">
				<div class="error" id="error_div">
				</div>
			</p>
		</div>

		<div id="dialog-reboot" title="Confirm">
			<p id="confirmreboot" style="display: none">
				<img src="images/icn-reboot.png" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="confirmreboot_div" style="font-size: 13px; margin-left: 10px; display: none">
					Are you sure you want to reboot the device?
				</div>
			</p>
		</div>

		<div id="dialog-success" title="Message">
			<p id="confirmsuccess" style="display: none">
				<div id="confirmsuccess_div" style="font-size: 13px; margin-left: 10px; display: none">
					All the configuration files are successfully updated.
				</div>
			</p>
		</div>

		<div id="dialog-restore-success" title="Message">
			<p id="confirmsuccess1" style="display: none">
				<div id="confirmsuccess_div1" style="font-size: 13px; margin-left: 10px; display: none">
					Restore Config Changes Applied, Continue Rebooting !.
				</div>
			</p>
		</div>

		<div id="dialog-apply-reboot" title="Confirm">
			<p id="apply_reboot" style="display: none">
				<img src="images/icn-warning.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="apply_reboot_div" style="font-size: 13px; margin-left: 10px; display: none">
					Reboot is required. Click OK to Continue reboot.
				</div>
			</p>
		</div>

		<div id="dialog-error" title="Error">
			<p id="apply_error" style="display: none">
				<img src="images/icn-error.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" />
				<div id="apply_error_div" style="font-size: 13px; margin-left: 10px; display: none">
				</div>
			</p>
		</div>

		<div id="dialog-popup">
		</div>
		</div>
	</body>
	<script type="text/javascript">
		$main(document).ready(function() {
			refreshFrame();
			areCookiesEnabled();
		});
	</script>
	<?php
		include_once ("time_panel.php");
		$shell_obj = ShellCommand::getInstance();
		$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
		if( $shell_status == 0) {
			$currentTime =trim( $shell_obj->getLastOutput());
		}
	?>
	<script type="text/javascript">
		parent.Time_init('<?php echo "$currentTime"; ?>');
	</script>
</html>
