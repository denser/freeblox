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

	$_SESSION['pageID'] = "#ping_sbc";
	$shell_obj = ShellCommand::getInstance();
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
				getPageTips("ping");
				setTooltip();
			});

			function submitData() {
				if($sub("#ping").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function reset_ping() {
				document.ping.count.selectedIndex = 0;
				document.ping.count.options[0].selected = true;
				document.ping.host.value = '';
				document.ping.ping_result.value = '';
			}

			function initValidation() {
				$sub("#HOST").attr("data-validation-engine","validate[required,custom[hostname]]");
				$sub("#ping").validationEngine('attach', {promptPosition : "bottomRight"});
			}
		</script>
		<?php
			$ping_result ="";
			$host = "";
			$ping_count = "";
			$interface_lst = SysCalls::getInterfaces();
			$interface_count = count($interface_lst);

			if(isset($_POST) && isset($_POST['submit_action'])) {
				$ping_count = $_POST['count'];
				$host = trim($_POST['host']);
				$ping_result = SysCalls::getPing($ping_count, $host );
				if( $ping_result == NULL )
				{
					$ping_result = "Invalid Host";
				}
			}
		?>
	</head>
	<body onclick="javascript: parent.hideMenu()">
		<div class="content">
			<h1>Ping<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form name="ping" id="ping" method="post" action="ping.php" autocomplete="off">
			<table border="0" cellpadding="0" cellspacing="0" class="table-config no-border">
				<tr>
					<td>
						<table cellpadding="2" cellspacing="2">
							<tr>
								<td>Host </td>
								<td>

									<input type="text" class="ipBox" value="<?php print $host; ?>" id="HOST" name="host" size="" maxlength="256" />
									<img id="ping_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							<tr>
								<!--td>Interface </td>
								<td>
									<select class="ipDrpBox" name="interfaces">
										<?php
										for($i=0; $i < $interface_count; $i++) {
										?>
											<option <?php if(isset($_POST['interfaces']) && $_POST['interfaces']== $interface_lst[$i]) echo 'selected="selected"'; ?> value="<?php print $interface_lst[$i];?>"><?php print $interface_lst[$i] ?></option>
										<?php
										}
										?>
									</select>
									<img id="ping_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td-->
							</tr>
							</tr>
							<tr>
								<td> Count </td>
								<td>
									<select class="ipDrpBox" name="count">
									<?php
										for($x=1;$x <= 5; $x++) {
									?>
											 <option <?php if(isset($_POST['count']) && $_POST['count'] == $x) echo 'selected="selected"'; ?> value="<?php print $x; ?>"><?php print $x; ?></option>
									<?php
										}
									?>
									</select>
									<img id="ping_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							<tr>
								<input type="hidden" name="submit_action" value="0" />
								<td colspan="2">
									<a class="btn-grey-wide-l" id="ping" onclick="submitData();"><span>Ping</span></a>
									<a class="btn-grey-wide-l" id="reset" onclick="reset_ping();"><span>Reset</span></a>
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
						<textarea name="ping_result" id="ping_result" readonly="readonly" class="ipTxtArea" rows="8" cols="100"><?php print $ping_result; ?></textarea>
					</td>
				</tr>
			</table>
			</form>
		</div>
		<div>
			<label style="font-size: 12px; display: none" id="system_time"></label>
			<label style="font-size: 12px; display: none" id="table_time"></label>
		</div>
	</body>
	<?php
	include_once ("time_panel.php");
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
	</script>
</html>
