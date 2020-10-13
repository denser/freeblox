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
*/?>
<?php
	ob_start();
	include_once("Control/session_secure.php");
	include_once("Model/sys_calls.php")
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
		<title><?php print SysCalls::getFullProductName(); ?></title>
		<link rel="stylesheet" href="css/jquery.ui.all.css">
		<link rel="stylesheet" href="css/demos.css">
		<link rel="stylesheet" href="css/style.css">
		<script src="js/jquery-1.8.2.min.js"></script>
		<script src="js/jquery.ui.core.js"></script>
		<script src="js/jquery.ui.widget.js"></script>
		<script src="js/jquery.ui.accordion.js"></script>
		<script src="js/jquery.ui.button.js"></script>
		<script src="js/jquery.ui.position.js"></script>
		<script src="js/jquery.ui.menu.js"></script>
		<script src="js/jquery.ui.tooltip.js"></script>
		<script src="js/jquery.ui.effect.js"></script>
		<script src="js/jquery.ui.effect-explode.js"></script>
		<script src="js/jquery.ui.dialog.js"></script>
		<script src="js/jquery.ui.datepicker.js"></script>
		<script src="js/menu.js"></script>
		<style>
			.ui-menu {
				position: absolute;
				width: 100px;
			}
			.no-close .ui-dialog-titlebar-close {
				display: none;
			}
			.custom-add-edit-dialog-part {
				border: none;
				padding: 0px;
				color: #444444;
			}
		</style>
		<script type="text/javascript">
			var $main = $.noConflict(true);
		</script>
		<script>
			var current_setup_tab = 0;
			var default_color = "#EEEEEE";
			var selected_color = "#999999";
			var hover_color = "#DCDCDC";

			function callEditPage(val, pageid) {
				$main("#dialog-popup").load(pageid + "?data=" + val).dialog({
					modal : true,
					height : 560,
					width : 600,
					closeOnEscape : false,
					dialogClass : "no-close"
				});
				$main(".ui-dialog-titlebar").hide();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog-part");
				$main("#dialog-popup").removeClass("ui-dialog-content");
			}

			function callAddPage(pageid) {
				$main("#dialog-popup").load(pageid).dialog({
					modal : true,
					height : 560,
					width : 600,
					closeOnEscape : false,
					dialogClass : "no-close"
				});
				$main(".ui-dialog-titlebar").hide();
				$main(".ui-widget-content").addClass("custom-add-edit-dialog-part");
				$main("#dialog-popup").removeClass("ui-dialog-content");
			}

			function closeAddEditDialog() {
				$main("#dialog-popup").dialog("close");
			}

			function callConfirmBox(val) {
				document.getElementById("confirmdata").style.display = "block";
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
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog-part");
			}

			function callConfirmAllBox(val) {
				document.getElementById("confirmdata").style.display = "block";
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
				$main(".ui-widget-content").removeClass("custom-add-edit-dialog-part");
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

			function callAllDelete(val, deletetoken) {
				if (val == 1) {
					document.getElementById('container').contentWindow.deleteAllAction(val, deletetoken);
				}
			}

			$main(function() {
				$main("#rerun").button().click(function() {
					alert("Refresh");
				}).next().button({
					text : false,
					icons : {
						primary : "ui-icon-triangle-1-s"
					}
				}).click(function() {
					$main("#ui-id-1").css("width", "88px");
					var menu = $main(this).parent().next().toggle("10", function() {
					}).position({
						my : "left top",
						at : "left bottom",
						of : this
					});
					$main("#ui-id-1").css("left", "");
					$main(document).one("click", function() {
						menu.hide();
					});
					return false;
				}).parent().buttonset().next().hide().menu();
			});

			$main(function() {
				$main("#rerun").tooltip({
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
				$main("#updates").tooltip({
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
			});

			function openDashboard() {
				document.getElementById("container").src = "dashboard.php";
			}
		</script>
	</head>
	<body>
		<table style="margin: 0 auto; width: 100%;" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
				<table style="width: 100%; height: 90px;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="page-header"><a href="" onclick="openDashboard();"><div class="main-logo"></div></a></td>
					</tr>
				</table>
				<table style="width: 100%;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="page-title-bar"></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td valign="top">
				<table style="width: 100%; height: 480px" cellpadding="0" cellspacing="0">
					<tr>
						<td id="main-menu" style="width: 15%;" valign="top"> <?php include_once ("menu.php"); ?> </td>
						<td style="width: 85%;" valign="top"><iframe src="" id="container" name="container" frameborder="0" style="height: 500px; width: 100%; border: 0px; margin: 0px;"></iframe></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td align="center" style="position: fixed; bottom: 0px; border-top: 1px solid #cccccc; width: 100%; background-color: #eeeeee; height: 30px"><div style="margin-top: 5px; color: #444444; font-size: 11px">Copyright 2015-2018 <?php print SysCalls::getFullProductName(); ?></div></td>
			</tr>
		</table>
		<div id="dialog-confirm" title="Confirm">
			<p id="confirmdata" style="display: none">
				<img src="images/icn-warning.gif" style="float:left; margin:0 7px 20px 0; height: 50px;" /><label style="font-size: 13px; margin-left: 10px">Are you sure you want to delete the selected items?</label>
			</p>
		</div>
		<div id="dialog-popup">
		</div>
	</body>
</html>
