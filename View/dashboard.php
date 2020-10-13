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
	include_once("Model/sip_profile_model.php");
	include_once('Model/device_settings_model.php');
	include_once("Control/date_config.php");
	include_once("Model/Config_NETWORK_DEVICE.php");
	include_once("Model/devicelist_settings_model.php");
	include_once("reports/sys_reports.inc");
	include_once("reports/sys_report_consts.inc");

	$getDashboardInfo = SysCalls::getDashboardInfo();
	$cputemp = SysCalls::getCPUTemperature();
	$maxcore = SysCalls::getNumOfCores();
	$servicestatus = SysCalls::serviceRunStat();
	$obj = DateTimeConfig::getConfig();
	$result = $obj -> getDTConfig();
	$row = $result -> fetchArray();
	$update_count = $obj -> getActionListCount();
	$_SESSION['pageID'] = "#dashboard";
	$ips_data = UtmSysReports::execReportsCmd(UtmReportsCmd::GET_IPS_STAT);
	$dev_settings = DeviceSettingsModel::getInstance();
	$dpi_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_DPI);
	$firewall_status = $dev_settings->get(DeviceSettingsModel::$ENABLE_FIREWALL);
	$devidobj = DevicelistSettingsModel::getInstance();
	$networkid = Config_NETWORK_DEVICE::Connect();
	$sys_arch = "0";
	if(SysCalls::getSysArch() == "armv7l") {
		$sys_arch = "1";
	}

	if(isset($_POST["submit_action"])) {
		if($_POST["submit_action"] == 1) {
			SysCalls::detectInterfaces();
			SysCalls::setDefaultEntry();
		if(SysCalls::getSysArch() == "armv7l") {
			$networkid->reset_Network();
			SysCalls::ResetNetworkInterface();
		} else {
			$networkid->init_NetWork();
		}
		$ipAddress = SysCalls::getLAN_WANIPAddress($media_internal_interface);
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/validationEngine.jquery.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.tooltip.css" />
		<script src="js/jquery.tooltip.js"></script>
		<script src="js/ss-tooltip.js"></script>
		<script type="text/javascript">

		function refresh_corecpu() {
			$('#corecpu').toggle('500');
			var core_sym = document.getElementById("refresh_corecpu").innerHTML;
			if(core_sym == "+") {
				document.getElementById("refresh_corecpu").innerHTML = "-";
			} else {
				document.getElementById("refresh_corecpu").innerHTML = "+";
			}
		}

		function refresh() {
			var formObj = document.forms["refreshform"];
			formObj.submit_action.value = "1";
			formObj.submit();
			return true;
		}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()">
		<div class="content">
			<h1>Dashboard<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<div id="dashboardbody" style="height: ; overflow-y: hidden; width: 95%; overflow-x: hidden">
				<table border="0" style="width: 98%; margin-top: 0px" cellpadding="0" cellspacing="0" class="table-config no-border">
					<tr>
					<td valign="top" width="47%">
					<table>
					<tr><td>
						<table class="dashboard-container" cellpadding="0" cellspacing="0" style="width:415px;">
						<thead>
							<tr> <th>System Status</th> </tr>
						</thead>
<!-- Up Time -->
						<tr>
							<td class="dashboard-container-td">
								<table class="status-tabs" cellpadding="0" cellspacing="0">
									<tr>
										<td class="status-tabs-left">
											<div class="status-heading">
												<img src="images/icn-clock.png" height="35px;" style="float: left; margin-right: 10px;margin-left:-8px;" />
												<label style="line-height: 34px">Up-Time</label>
											</div>
											<div class="sys-status-uptime-details" style="margin-left:-8px; font-weight: bold;"><?php print trim($getDashboardInfo[5]); ?></div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
<!--Up Time -->
<!-- Memory Usage -->
						<tr><td align="center" style="background-color:#fbfbfb"><div class="seperator-line"></div></td></tr>
						<tr>
							<td class="status-tabs-left">
								<div class="status-heading">
									<img src="images/icn-internal-memory.png" height="35px;" style="float: left; margin-right: 10px" />
									<label style="line-height: 34px">Memory Usage <?php  print "(Total Memory : ".SysCalls::getTotalMem().")"; ?></label>
								</div>
								<div class="sys-status-cpu-usage-display-container">
									<div class="sys-status-cpu-usage-display">
										<div class="sys-status-cpu-usage-display-highlighted">
										<div class="cpu-load-bar-green" style="width: <?php print $getDashboardInfo[0]; ?>;"></div>
										</div>
									</div>
								</div>
								<div class="sys-status-memory-usage-view">&nbsp;<?php print $getDashboardInfo[0]; ?></div>
							</td>
						</tr>
<!--Memory Usage -->
						<tr><td align="center" style="background-color:#fbfbfb"><div class="seperator-line"></div></td></tr>
<!-- Flash Usage -->
						<?php
							$getFlashVal = SysCalls::getTotalFlash();
							$flash_arr = explode("|",$getFlashVal);
							$flash_size = trim($flash_arr[0])."B";
						?>
						<tr>
							<td class="status-tabs-left">
								<div class="status-heading">
									<img src="images/icn-disk-usage.png" height="35px;" style="float: left; margin-right: 10px" />
									<label style="line-height: 34px"> Flash Usage <?php  print "(Flash Size : ".$flash_arr[0].")"; ?></label>
								</div>
								<div class="sys-status-cpu-usage-display-container">
									<div class="sys-status-cpu-usage-display">
										<div class="sys-status-cpu-usage-display-highlighted">
										<div class="cpu-load-bar-green" style="width: <?php print $flash_arr[1]; ?>;"></div>
										</div>
									</div>
								</div>
								<div class="sys-status-memory-usage-view">&nbsp;<?php print $flash_arr[1]; ?></div>
							</td>
						</tr>
<!--Flash Usage -->
						<tr><td align="center" style="background-color:#fbfbfb"><div class="seperator-line"></div></td></tr>
<!-- CPU Usage -->
						<tr>
							<td class="status-tabs-left">
								<div class="status-heading">
									<img src="images/icn-cpu-usage.png" height="35px;" style="float: left; margin-right: 10px" />
									<label style="line-height: 34px"> CPU Usage ( No. of. Cores: <?php print $maxcore; ?> <?php if($sys_arch == "1") {?> Temperature: <?php print $cputemp; ?>% <?php }?>) &nbsp;</label>
									<?php
										if($sys_arch == "1") {
									?>
									<a href="#" name= "refresh_corecpu" id= "refresh_corecpu" onclick="refresh_corecpu()">+</a>  <!-- + will expand core cpu info -->
									<?php
										}
									?>
								</div>
								<div class="sys-status-cpu-usage-display-container">
									<div class="sys-status-cpu-usage-display">
										<div class="sys-status-cpu-usage-display-highlighted">
										<div class="cpu-load-bar-green" style="width: <?php print $getDashboardInfo[6]; ?>;"></div>
									</div>
									</div>
								</div>
								<div class="sys-status-memory-usage-view">&nbsp;<?php print $getDashboardInfo[6]; ?></div>
							</td>
						</tr>
						<tr><td>
							<table id="corecpu" style="display:none">
							<div name='cpucore' id='cpucore'>
							<?php
								for ($core=0; $core<$maxcore; $core++) {
							?>
							<tr>
								<td>
									<img src="images/icn-cpu-usage.png" height="8px;" style="float: right; margin-left: 30px" />
								</td>
								<td class="status-tabs-left">
									<div class="sys-status-cpu-usage-display-container">
										<div class="sys-status-cpu-usage-display">
											<div class="sys-status-cpu-usage-display-highlighted">
											<div class="cpu-load-bar-green" style="width: <?php print SysCalls::getCpuCoreUsage($core); ?>"></div>
											</div>
										</div>
									</div>
									<div class="sys-status-memory-usage-view">&nbsp;<?php print SysCalls::getCpuCoreUsage($core); ?></div>
								</td>
							</tr>
							<?php } ?>
							</div>
							</table>
						</td> </tr>
<!--CPU Usage -->
						</table>
					</td>
					</tr>
					<tr>
						<td style="height: 3px; width: 400px">&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table class="dashboard-container" cellpadding="0" cellspacing="0" style="height:70px; width:415px;">
							<thead>
								<tr>
									<th>
										Network Status
									</th>
								</tr>
							</thead>
							<tr>
								<td class="auto-discovery-status-body-container" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
									<table class="dashboard-alerts" cellpadding="0" cellspacing="1px">
										<tr>
										<td>
											<div class="sys-status-top-left-container">
											<div class="sys-status-heading">
												<img src="images/icn-network-info.png" style="float: left; margin-right: 10px" />
												<label style="line-height: 34px; font-weight: bold;"> Network Info</label>
											</div>
											</div>
										</td>
										<td>
											<form id="refreshform" method="post" action="dashboard.php">
											<table width="100%">
											<tr><td align="center">
												<a class="btn-grey-wide-l" style="float:right;" name= "refresh" id= "refresh" onclick="refresh()"><span>Detect</span></a>
											</td></tr></table>
											<input type="hidden" name="submit_action" id="submit_action"  value="0" />
											</form>
										</td>
										</tr>
							<tr>
								<td class="auto-discovery-status-body-container" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;" colspan="2">
									<table class="dashboard-alerts" cellpadding="0" cellspacing="1px">
										<thead>
										<tr>
											<th scope="col" style="text-align: center; width: 15px;">DEV</th>
											<th scope="col" style="text-align: center; width: 5px;">MAC ADDR</th>
											<th scope="col" style="text-align: center; width: 10px;">IP</th>
											<th scope="col" style="text-align: center; width: 10px;">GATEWAY</th>
											<th scope="col" style="text-align: center; width: 10px;">TYPE</th>
										</tr>
										</thead>
										<tbody>
											<?php
											$i = 0;
	$devicelistids = $devidobj->getdeviceids();
											if ( isset($devicelistids)) {
												while($row = $devicelistids->fetchArray(SQLITE3_ASSOC)) {
													$i++;
											?>
											<tr>
												<td><?php echo $row[DevicelistSettingsModel::$DEV_ID]; ?></td>
												<td>
													<?php echo $row[DevicelistSettingsModel::$MAC_ID]; ?>
												</td>
												<td>
													<?php $ipAddress = SysCalls::getLAN_WANIPAddress($row[DevicelistSettingsModel::$DEV_ID]); 
													echo $ipAddress;
												?>
												</td>
												<td>
													<?php $gateway = SysCalls::getgateway($row[DevicelistSettingsModel::$DEV_ID]);
														echo $gateway;
													 ?>
												</td>
												<td>
													<?php
													if($row[DevicelistSettingsModel::$T_CODE] == 1) {
														echo "Transcoding"; 
													} else {
														echo "Interface";
													}
													?>
												</td>
											</tr>
											<?php
												}
												if ($i==0) {
											?>
											<tr>
												<td colspan= "4">
												<center>NO INTERFACE</center>
												</td>
											</tr>
											<?php
												}
											} else {
											?>
											<tr>
												<td colspan= "4">
												<center>NO INTERFACE</center>
												</td>
											</tr>
											<?php
												}
											?>
										</tbody>
									</table>
									</td>
								</tr>
								</table>
							</td> </tr>
							</table>
							</td> </tr>
						</table>
						</td>
						<td valign="top" width="53%">
							<table>
<!--System Information-->
								<tr>
									<td>
										<table class="dashboard-container" cellpadding="0" cellspacing="0" style="height: 95px; width: 100%;">
										<thead> <tr> <th>System Information</th> </tr> </thead>
										<tr>
										<td class="auto-discovery-status-body-container" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
										<table>
										<?php 
											$out = SysCalls::getSysInfo();
											for($i=0;$i<count($out);$i++) {
										?>
										<tr>
										<?php
											$data=explode("|",$out[$i]);
										?>
											<td align="left" valign="top"><?php echo $data[0];?> </td>
											<td width="50px">&nbsp;</td>
											<td align="left"><?php echo $data[1];?> </td>
										</tr>
										<?php
											}
										?>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr> 
<!--System Information-->
					<tr>
						<td style="height: 1px;">&nbsp;</td>
					</tr>
<!-- All status -->
					<tr><td>
						<table class="dashboard-container" cellpadding="0" cellspacing="0" style="height: 70px; width: 100%;" border="0">
                                                        <thead>
                                                                <tr>
                                                                        <th colspan="4">Status</th>
                                                                </tr>
                                                        </thead>
                                                        <tr>
<!-- Proxy(signalling) Status -->
                                                                <td width="70px" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 30px; padding-right: 0px;">
									<span style="vertical-align:top;"><?php print "Proxy"; ?></span>
								</td>
                                                                <td width="70px" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
                                                                <?php if ( $servicestatus['opensips'] == "1" ) { ?>
									<image style="margin-top:-5px;" src="images/green.gif" valign="top" />
                                                                <?php } else { ?>
									<image style="margin-top:-5px;" src="images/red.png" valign="top" />
                                                                <?php } ?>
                                                                </td>
<!-- Proxy Status -->

<!-- Media Status -->

                                                                <td width="70px" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 30px; padding-right: 0px;">
									<span style="vertical-align:top;"><?php print "Media"; ?></span>
								</td>
                                                                <td width="70px" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
                                                                <?php if ( $servicestatus['conf'] == "1" ) { ?>
									<image style="margin-top:-5px;" src="images/green.gif" valign="top" />
                                                                <?php } else { ?>
									<image style="margin-top:-5px;" src="images/red.png" valign="top" />
                                                                <?php } ?>
                                                                </td>
<!-- Media Status -->
                                                        </tr>
							<tr>
<!-- DPI Status -->
                                                                <td style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 30px; padding-right: 0px;">
									<span style="vertical-align:top;"><?php print "DPI"; ?></span>
								</td>
                                                                <td style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
                                                                <?php if ( ($servicestatus['snort_'] == "1") && ( $servicestatus['snortsam'] == "1" ) ) { ?>
									<image style="margin-top:-5px;" src="images/green.gif" valign="top" />
                                                                <?php } else { ?>
									<image style="margin-top:-5px;" src="images/red.png" valign="top" />
                                                                <?php } ?>
                                                                </td>
<!-- DPI Status -->

<!-- Firewall Status -->
                                                                <td style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 30px; padding-right: 0px;">
									<span style="vertical-align:top;"><?php print "Firewall"; ?></span>
								</td>
                                                                <td  style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
                                                                <?php  if ( $servicestatus['firewall'] == 1 ) { ?>
									<image style="margin-top:-5px;" src="images/green.gif" valign="top" />
                                                                <?php } else { ?>
									<image style="margin-top:-5px;" src="images/red.png" valign="top" />
								<?php } ?>
								</td>
<!-- Firewall Status -->
							</tr>
                                                        <tr>
<!-- Database Status -->
                                                                <td style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 30px; padding-right: 0px;">
									<span style="vertical-align:top;"><?php print "Database"; ?></span>
								</td>
                                                                <td style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
                                                                <?php if ($servicestatus['mysqld/mysqld'] == "1" ) { ?>
									<image style="margin-top:-5px;" src="images/green.gif" valign="top" />
                                                                <?php } else { ?>
									<image style="margin-top:-5px;" src="images/red.png" valign="top" />
                                                                <?php } ?>
                                                                </td>
<!-- Database Status -->

<!-- ssh Status -->
                                                                <td style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 30px; padding-right: 0px;">
									<span style="vertical-align:top;"><?php print "SSH"; ?></span>
								</td>
                                                                <td style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
                                                                <?php if ( $servicestatus['sshd'] == "1" ) { ?>
									<image style="margin-top:-5px;" src="images/green.gif" valign="top" />
                                                                <?php } else { ?>
									<image style="margin-top:-5px;" src="images/red.png" valign="top" />
                                                                <?php } ?>
                                                                </td>
<!-- ssh Status -->
                                                        </tr>
							<tr><td>&nbsp;</td></tr>
<!-- All status -->
                                                        <tr>
                                                        </tr>
                                                </table>
					</tr></td> 
<!-- DPI and Firewall status -->
					<tr>
						<td style="height: 1px;">&nbsp;</td>
					</tr>
<!-- Last 10 alerts -->
					<tr>
						<td>
							<table class="dashboard-container" cellpadding="0" cellspacing="0" style="height: 70px; width: 475px;">
									<thead>
										<tr>
											<th>Last 10 Alerts</th>
										</tr>
									</thead>
									<tr>
										<td class="auto-discovery-status-body-container" style="font-weight:bold;padding-top: 10px; vertical-align: top; padding-left: 6px;">
										<!-- Alerts Table -->
										<table class="dashboard-alerts" cellpadding="0" cellspacing="1px">
									<thead>
									<tr>
										<th scope="col" style="text-align: center; width: 150px;">Time</th>
										<th scope="col" style="text-align: center; width: 50px;">ID</th>
										<th scope="col" style="text-align: center; width: 100px;">Category</th>
										<th scope="col" style="text-align: center; width: 100px;">Message</th>
										<th scope="col" style="text-align: center; width: 100px;">Src IP</th>
									</tr>
									</thead>
									<tbody>
									<?php $ips_alert_log = SysCalls::getLast10IpsAlerts();
										for($i = 0; $i <= count($ips_alert_log); $i++) {
											if($i % 2 == 0) {
												$odd_even = "";
											} else {
												$odd_even="odd";
											}
									?>
									<tr id="<?php print $i; ?>" class="<?php print $odd_even;?>">
										<td style="text-align: center; width: 150px;"><?php print  $ips_alert_log[$i][0]; ?></td>
										<td style="text-align: center; width: 50px;"><?php print  $ips_alert_log[$i][1]; ?></td>
										<td style="text-align: center; width: 100px;"><?php print  $ips_alert_log[$i][2]; ?></td>
										<td nowrap="nowrap" style="text-align: center; width: 100px;"><?php print  substr($ips_alert_log[$i][3],0,24); ?></td>
										<td style="text-align: center; width: 100px;"><?php print  $ips_alert_log[$i][4]; ?></td>
									</tr>
									<?php
										}
									?>
									</tbody>
							</table>
<!-- Alerts table-->
							</td> </tr>
						</table>
					</td> </tr>
<!-- last 10 alerts -->
				</table>
				</td>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count; ?>');
	</script>
</html>
