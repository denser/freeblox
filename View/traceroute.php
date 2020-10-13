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

	$_SESSION['pageID'] = "#tracert_sbc";
	$obj = DateTimeConfig::getConfig();
	$result = $obj -> getDTConfig();
	$row = $result -> fetchArray();
	$update_count = $obj -> getActionListCount();
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
			$sub(document).ready(function() {
				initValidation();
				getPageTips("tracert");
				setTooltip();
			});

			function submitData() {
				if($sub("#tracert").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function reset_tracert() {
				document.tracert.hopcount.value = "";
				document.tracert.tracert_result.value = "";
				document.tracert.tracert_host.value = "";
				document.tracert.icmp.checked = false;
			}

			function initValidation() {
				$sub("#TRACERT_HOST").attr("data-validation-engine","validate[required,custom[hostname]]");
				$sub("#HOP_COUNT").attr("data-validation-engine","validate[required,custom[integer]]");

				$sub("#tracert").validationEngine('attach', {promptPosition : "bottomRight"});
			}
		</script>
		<?php
			$tracert_result = "";
			$icmp = "";
			$host = "";
			$hopcount = "";

			if(isset($_POST) && isset($_POST['submit_action'])) {
				$icmp = $_POST['icmp'];
				$hopcount = trim($_POST['hopcount']);
				$host = trim($_POST['tracert_host']);
				$tracert_result = SysCalls::getTracert( $icmp, $hopcount, $host );
				if ( $tracert_result == NULL ) {
					$tracert_result = "Invalid Host";
				}
			}
		?>
	</head>
	<body onclick="javascript: parent.hideMenu()">
		<div class="content">
			<h1>Traceroute<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form name="tracert" id="tracert" action="traceroute.php" method="post" autocomplete="off">
			<table border="0" cellpadding="0" cellspacing="0" class="table-config no-border">
				<tr>
					<td>
						<table cellpadding="2" cellspacing="2">
							<tr>
								<td>Host </td>
								<td>
									<input type="text" name="tracert_host" id="TRACERT_HOST" value="<?php print $host; ?>" class="ipBox" maxlength="256" size="45" />
									<img id="tracert_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							<tr>
								<td>Hop Count </td>
								<td>
									<input type="text" name="hopcount" id="HOP_COUNT" value="<?php print $hopcount; ?>" class="ipBox" maxlength="2" size="2"/>
									<img id="tracert_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							<tr>
								<td>ICMP </td>
								<td>
									<input type="checkbox" name="icmp" id="icmp" <?php if($icmp=="on") {?> checked <?php } ?> />
									 <img id="tracert_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />

								</td>
							</tr>
							<tr>
								<input type="hidden" name="submit_action" value="0" />
								<td colspan="2">
									<a class="btn-grey-wide-l" id="ping" onclick="submitData();"><span>Traceroute</span></a>
									<a class="btn-grey-wide-l" id="reset" onclick="reset_tracert();"><span>Reset</span></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<div style="height:8px;" class="vmargin12"></div>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<textarea name="tracert_result" id="tracert_result" readonly="readonly" class="ipTxtArea" rows="8" cols="100"><?php print $tracert_result; ?></textarea>
					</td>
				</tr>
			</table>
			</form>
		</div>
		<div>
			<label style="font-size: 12px; display: none" id="system_time"></label>
			<label style="font-size: 12px; display: none" id="table_time"></label>
		</div>
	<div id="DownloadConfiguration" class="jqmDialog"></div>
	</body>
	<?php
		include_once ("time_panel.php");
		$shell_obj = ShellCommand::getInstance();
		$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", 
				ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		if( $shell_status == 0) {
			$currentTime =trim( $shell_obj->getLastOutput());
		}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
	</script>
</html>
