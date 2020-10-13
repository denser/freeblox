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
	include_once("Control/date_config.php");
	include_once("Model/sys_calls.php");
	include_once("Model/shell_command.php");

	$_SESSION['pageID'] = "#sec_alerts";
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
		<!-- CSS,JQuery & Table Sorter Plug-Ins -->
		<link rel="stylesheet" type="text/css" href="css/sort.css" media="screen" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/tooltip.css" />
		<!--script type="text/javascript" language="javascript" src="js/jquery.js"></script-->
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
		<script src="js/jquery.tooltip.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script type="text/javascript" charset="utf-8">
			$sub(document).ready(function() {
				$sub('#status_alerts').dataTable( {
					"sScrollY": "213px",
					"bPaginate": false
				} );
				getPageTips("tools");
				setTooltip();
			} );
		</script>
		<!-- Sort function -->
		<?php
		if ( isset($_POST['status_alert_interval']) ) {
			$page_refresh_interval = $_POST['status_alert_interval'];
		} else if ( isset($_GET['status_alert_interval']) ) {
			$page_refresh_interval = $_GET['status_alert_interval'];
		} else {
			$page_refresh_interval = 15;
		}
		?>
		<script type="text/javascript">
			function status_refresh() {
				location.replace("status_alerts.php?status_alert_interval=" + refresh_interval_frm_srv);
			}

			function submitform() {
				if( document.getElementById('status_alert_interval').value < 15 ) {
					alert("Invalid! Please enter a value (min 15)")
				} else {
					document.StatusAlerts.submit();
				}
			}

			function download_logs(val) {
				parent.downloadLogs(val);
			}

			var refresh_interval_frm_srv = "<?php echo $page_refresh_interval; ?>";
			function refreshPage() {
				location.replace("status_alerts.php?status_alert_interval=" + refresh_interval_frm_srv);
			}

			function setRefreshInterval() {
				setInterval(refreshPage, refresh_interval_frm_srv*1000);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()">
		<div class="content" style="padding-right:20px;">
			<h1>System Logs<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
	<form name="StatusAlerts" method="POST" action="status_alerts.php" autocomplete="off">
<!-- Begin: Log Viewer Settings Content -->
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
					<tr>
						<td style="width: 85%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<th>Log Viewer Settings<img src="images/Icn_info_blue_bg.png" class="ttip" id="tools_001" align="right" style="margin-right: 5px" /></th>
								<tr>
									<td>
										<table cellpadding="2" cellspacing="2"  class="sub-container-table">
											<tr>
												<!--<td>Refresh Interval :</td>-->
												<td><input type="text" maxlength=4; size=4; class="ipBox" id="status_alert_interval" name="status_alert_interval" value="<?php echo $page_refresh_interval; ?>" /></td>
												<td><a class="btn-grey-wide-l" href="javascript:submitform();" id="update_ci_int"><span>Update Refresh Interval</span></a></td>
												<td><a class="btn-grey-wide-l" onclick="status_refresh();"><span>Refresh</span></a></td>
												<td><a class="btn-grey-wide-l" id="update" onclick="download_logs('<?php print alert;?>')"><span>Download Logs</span></a></td>
											</tr>
											</form>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
<!--End: Log Viewer Settings Content -->
				<!--<div style="height: 25px;">&nbsp;</div>-->
<!-- Begin: Status Alerts logs Contnet -->
				<table id="status_alerts" class="tablesorter" cellpadding="0" cellspacing="1px">
					<thead>
					<tr>
						<th scope="col"><a href="#">Time</a></th>
						<th scope="col" style="width:144px;"><a href="#">ID</a></th>
						<th scope="col"><a href="#">Category</a></th>
						<th scope="col"><a href="#">Message</a></th>
						<th scope="col"><a href="#">Src IP</a></th>
						<th scope="col"><a href="#">Src Port</a></th>
						<th scope="col"><a href="#">Dst IP</a></th>
						<th scope="col"><a href="#">Dst Port</a></th>
						<th scope="col"><a href="#">Protocol</a></th>
					</tr>
					</thead>
					<tbody>
						<?php $ips_alert_log = SysCalls::getIpsAlerts();
						for($i = count($ips_alert_log) - 1; $i >= 0; $i--) {
						?>
						<?php if($i % 2 == 0) {
								$odd_even = "";
							} else {
								$odd_even="odd";
							}
						?>
						<tr id="<?php print $i; ?>" class="<?php print $odd_even;?>">
							<td><?php print  $ips_alert_log[$i][0]; ?></td>
							<td style="width: 144px;"><?php print  $ips_alert_log[$i][1]; ?></td>
							<td><?php print  $ips_alert_log[$i][2]; ?></td>
							<td><?php print  $ips_alert_log[$i][3]; ?></td>
							<td><?php print  $ips_alert_log[$i][4]; ?></td>
							<td><?php print  $ips_alert_log[$i][5]; ?></td>
							<td><?php print  $ips_alert_log[$i][6]; ?></td>
							<td><?php print  $ips_alert_log[$i][7]; ?></td>
							<td><?php print  $ips_alert_log[$i][8]; ?></td>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
<!-- End: Status Alerts logs Contnet -->
				</div>
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
	</body>
	<?php
	include_once ("time_panel.php");
	$shell_obj = ShellCommand::getInstance();
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	?>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		setRefreshInterval();
	</script>
</html>
