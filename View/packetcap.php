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
	include_once ("Control/session_secure.php");
	include_once ("Control/login_auth.php");
	include_once ("Control/date_config.php");
	include_once("Model/sys_calls.php");
	include_once ("Model/shell_command.php");
	include_once("Model/devicelist_settings_model.php");

	$_SESSION['pageID'] = "#packet_cap";
	$shell_obj = ShellCommand::getInstance();
	$obj = DateTimeConfig::getConfig();
	$result = $obj -> getDTConfig();
	$row = $result -> fetchArray();
	$deviceid_list = array();
	$deviceobj = DevicelistSettingsModel::getInstance();
	$devicelistids = $deviceobj->getdeviceids();
	while($row = $devicelistids->fetchArray(SQLITE3_ASSOC)) {
		array_push($deviceid_list,$row[DevicelistSettingsModel::$DEV_ID]);
	}
	SysCalls::deletePacketCapture();
	$filename = "/var/tmp/.packet-capture.pcap.ret";
	$download_enable = SysCalls::checkPacketTrace($filename);
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
			var download_enable = '<?php echo $download_enable; ?>';
			var checkdownTime;

                        if(localStorage.getItem("packetcap_status") == null ) {
                                localStorage.setItem("packetcap_status", "Run");
                        }
 
			$sub(document).ready(function() {
				initValidation();
				getPageTips("packetcap_ttip");
				setTooltip();
				runStopLocalStorage();
			});

			function runStopLocalStorage() {
				document.getElementById("packet_capspan").innerHTML = localStorage.getItem("packetcap_status");
				if(localStorage.getItem("packetcap_status") == "Stop") {
					$sub("#reset").attr('class','btn-grey-wide-l-disabled');
					checkDownload();
				}
			}

			function checkDownload() {
				$sub.ajax({
					type: "POST",
					dataType:"text",
					cache: "false",
					data: {check_tcpdump:1},
					url: "show_iface_ip.php",
					global: "false",
					async:"false",
					success: function (response) {
						if(response == "0") {
							checkdownTime = setInterval(checkDownload, 9000);
						} else {
							localStorage.setItem("packetcap_status", "Run");
							clearTimeout(checkdownTime);
							refresh_iframe(1);
						}
					}
				});
			}

			function submitData() {
				var packet_capspan = document.getElementById("packet_capspan").innerHTML;
				var formObj = document.forms[0];
				$sub("#reset").attr('class','btn-grey-wide-l-disabled');
				if(packet_capspan == "Run" ) {
					if($sub("#packetcap").validationEngine('validate')) {
						formObj.submit_action.value = "1";
						document.getElementById("packet_capspan").innerHTML = "Stop";
						localStorage.setItem("packetcap_status", "Stop");
						formObj.submit();
						return true;
					}
				} else {
					localStorage.setItem("packetcap_status", "Run");
					$sub("#packet_capid").attr('class','btn-grey-wide-l-disabled');
					$sub.ajax({
						type: "POST",
						cache: "false",
						data: {stop_tcpdump:1},
						url: "packetcap.php",
						global: "false",
						async:"false",
						success: function (response) {
							localStorage.setItem("packetcap_status", "Run");
							clearTimeout(checkdownTime);
							refresh_iframe(1);
						}
					});
				}
			}

			function download_packcap() {
				parent.downloadpackcap();
				refresh_iframe(1);
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "packetcap.php";
				}
			}

			function reset_packet() {
				document.packetcap.deviceid.selectedIndex = 0;
				document.packetcap.deviceid.options[0].selected = true;
				document.packetcap.port1.value = '';
				document.packetcap.port2.value = '';
				document.packetcap.port3.value = '';
				document.packetcap.port4.value = '';
				document.packetcap.timeout.selectedIndex = 0;
				document.packetcap.timeout.options[0].selected = true;
			}

			function initValidation() {
				$sub("#port1").attr("data-validation-engine","validate[custom[integer],min[1],max[65535]]");
				$sub("#port2").attr("data-validation-engine","validate[custom[integer],min[1],max[65535]]");
				$sub("#port3").attr("data-validation-engine","validate[custom[integer],min[1],max[65535]]");
				$sub("#port4").attr("data-validation-engine","validate[custom[integer],min[1],max[65535]]");
				$sub("#packetcap").validationEngine('attach', {promptPosition : "bottomRight"});
			}
		</script>
		<?php
			$deviceid ="";
			$ports = "";
			$timeout = "";

			if(isset($_POST) && ($_POST['submit_action'] == "1")) {
				$deviceid = $_POST['deviceid'];
				$ports = "";
				for($i=1;$i<5;$i++) {
					$portval = $_POST["port$i"];
					if( $portval != "") {
						if($i == 1) {
							$ports .= " port ".$portval;
						} else {
							$ports .= " or port ".$portval; 
						}
					}
				}
				$timeout = $_POST['timeout'];
				if($timeout > 300) {
					$timeout = 300;
				}
				if($timeout < 10) {
					$timeout = 10;
				}
				$str = "timeout ".$timeout." tcpdump -i ".$deviceid.$ports." -w /var/tmp/packet-capture.pcap ";
				$file = "packet-capture.pcap.ret";
				$result = SysCalls::executeShellscript($str,$file,1);
				echo "<script>refresh_iframe(1);</script>";
			}

			if(isset($_POST['stop_tcpdump'])) {
				$ret = SysCalls::stopPacketTrace();
				echo $ret; 
			}
		?>
	</head>
	<body onclick="javascript: parent.hideMenu()">
		<div class="content">
			<h1>Packet Capture<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form name="packetcap" id="packetcap" method="post" action="packetcap.php" autocomplete="off">
			<table border="0" cellpadding="0" cellspacing="0" class="table-config no-border">
				<tr>
					<td>
						<table cellpadding="2" ciellspacing="2">
							<tr>
								<td> Interfaces </td>
								<td>
									<select class="ipDrpBox" name="deviceid" id="deviceid">
										<?php
											for($x=0;$x < count($deviceid_list); $x++) {
										?>
												<option value="<?php print $deviceid_list[$x]; ?>"><?php print $deviceid_list[$x]; ?></option>
										<?php
											}
										?>
												<option value="<?php print "any"; ?>"><?php print "any"; ?></option>
									</select>
									<img id="packetcap_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							<tr>
								<td>Ports </td>
								<td>
								<table>
								<body>
									<tr>
									<td>
										<input type="text" id="port1" name="port1" size="1" maxlength="5" class="ipBox" value="" />
									</td>
									<td>OR</td>
									<td>
										<input type="text" id="port2" name="port2" size="1" maxlength="5" class="ipBox" value="" />
									</td>
									<td>OR</td>
									<td>
										<input type="text" id="port3" name="port3" size="1" maxlength="5" class="ipBox" value="" />
									</td>
									<td>OR</td>
									<td>
										<input type="text" id="port4" name="port4" size="1" maxlength="5" class="ipBox" value="" />
										<img id="packetcap_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
									</td>
									</tr>
								</body>
								</table>
								</td>
							</tr>
							<tr>
								<td>Timeout (sec) </td>
								<td>
									<select class="ipDrpBox" name="timeout" id="timeout">
									<?php
										for($x=10;$x <= 300; $x=$x+10) {
									?>
											<option value="<?php print $x; ?>"><?php print $x; ?></option>
									<?php
										}
									?>
									</select>
									<img id="packetcap_ttip_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
								</td>
							</tr>
							<tr>
								<input type="hidden" name="submit_action" value="0" />
								<td colspan="2">
								<?php
									if($download_enable == 0) {
										$downloadclass = "btn-grey-wide-l-disabled";
										$runclass = "btn-grey-wide-l";
									} else {
										$downloadclass = "btn-grey-wide-l";
										 $runclass = "btn-grey-wide-l-disabled";
									}
								?>

									<a class="<?php echo $runclass; ?>" id="packet_capid"  <?php if($runclass == "btn-grey-wide-l") {?> onclick="submitData();" <?php } ?>><span id="packet_capspan">Run</span></a>
									<a class="<?php echo $runclass; ?>" id="reset" <?php if($runclass == "btn-grey-wide-l") {?> onclick="reset_packet();" <?php } ?>><span>Reset</span></a>
									<a class="<?php echo $downloadclass; ?>" id="packet_download" <?php if($downloadclass == "btn-grey-wide-l") {?> onclick="download_packcap();" <?php } ?> ><span>Download</span></a>

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
			</table>
			</form>
		</div>
		<div>
			<label style="font-size: 12px; display: none" id="system_time"></label>
			<label style="font-size: 12px; display: none" id="table_time"></label>
		</div>
	</body>
	<?php
		include ("time_panel.php");
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
