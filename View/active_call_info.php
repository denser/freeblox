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
	include_once("Model/sbc_log_mgr.php");
	include_once("Model/sys_calls.php");
	include_once("Model/shell_command.php");

	$_SESSION['pageID'] = "#active_calls";
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
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script type="text/javascript" charset="utf-8">
			$sub(document).ready(function() {
				$sub('#status_alerts').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "213px",
					"bPaginate": false
				} );
				getPageTips("tools");
				initValidation();
				setTooltip();
			});
		</script>
<!-- Sort function -->
		<?php
			if ( isset($_POST['active_call_interval']) )
			{
				$page_refresh_interval = $_POST['active_call_interval'];
			}
			else if ( isset($_GET['active_call_interval']) )
			{
				$page_refresh_interval = $_GET['active_call_interval'];
			}
			else
			{
				$page_refresh_interval = 300;
			}
		?>
		<script type="text/javascript">
			function status_refresh() {
				location.replace("active_call_info.php?active_call_interval=" + refresh_interval_frm_srv);
			}

			function submitform() {
				if($sub("#ActiveCallInfo").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function initValidation() {
				$sub( document ).ready(function() {
					$sub("#active_call_interval").attr("data-validation-engine","validate[required,custom[integer],min[15],max[3600]]");
					$sub("#ActiveCallInfo").validationEngine('attach', {promptPosition : "bottomRight"});
				});
			}

			function download_logs(val) {
				parent.downloadLogs(val);
			}
		</script>
		<script type="text/javascript">
			var refresh_interval_frm_srv = "<?php echo $page_refresh_interval; ?>";
			function refreshPage() {
				location.replace("active_call_info.php?active_call_interval=" + refresh_interval_frm_srv);
			}
			function setRefreshInterval() {
				setInterval(refreshPage, refresh_interval_frm_srv*1000);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()">
		<div class="content" style="padding-right:20px;">
			<h1>Active Calls Info<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form name="ActiveCallInfo" id="ActiveCallInfo" method="POST" action="active_call_info.php" autocomplete="off">
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
												<td><input type="text" maxlength=4; size=4; class="ipBox" id="active_call_interval" name="active_call_interval" value="<?php echo $page_refresh_interval; ?>" /></td>
												<td><a class="btn-grey-wide-l" href="javascript:submitform();" id="update_ci_int"><span>Update Refresh Interval</span></a></td>
												<td><a class="btn-grey-wide-l" onclick="status_refresh();"><span>Refresh</span></a></td>
											</tr>
			</form>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
<!--End: Log Viewer Settings Content -->

<!--<div style="height: 25px;">&nbsp;</div>-->

<!-- Begin: Status Alerts logs Contnet -->
				<table id="status_alerts" class="tablesorter" cellpadding="0" cellspacing="1px">
					<thead>
						<tr>
							<th scope="col"><a href="#">Dialling ID</a></th>
							<th scope="col"><a href="#">Call ID</a></th>
							<th scope="col"><a href="#">From URI</a></th>
							<th scope="col"><a href="#">Caller Contact</a></th>
							<th scope="col"><a href="#">Caller Sock</a></th>
							<th scope="col"><a href="#">To URI</a></th>
							<th scope="col"><a href="#">Callee Contact</a></th>
							<th scope="col"><a href="#">Callee Sock</a></th>
							<th scope="col"><a href="#">State</a></th>
							<th scope="col"><a href="#">Start Time</a></th>
							<th scope="col"><a href="#">Timeout</a></th>
							<th scope="col"><a href="#">Profiles</a></th>
						</tr>
					</thead>

					<tbody>
						<?php
							$status = SBCLogMgr::queryActiveCalls();
							$count =count($status);
							for($j=0;$j < $count;$j++) {
						?>
								<tr id="<?php print $j; ?>" class="<?php print $odd_even;?>">
									<td><?php print  $status[$j][0]; ?></td>
									<td><?php print  $status[$j][1]; ?></td>
									<td><?php print  $status[$j][2]; ?></td>
									<td><?php print  $status[$j][14]; ?></td>
									<td><?php print  $status[$j][16]; ?></td>
									<td><?php print  $status[$j][4]; ?></td>
									<td><?php print  $status[$j][15]; ?></td>
									<td><?php print  $status[$j][17]; ?></td>
									<td><?php print  $status[$j][18]; ?></td>
									<td><?php print  $status[$j][19]; ?></td>
									<td><?php print  $status[$j][20]; ?></td>
									<td><?php print  $status[$j][22]; ?></td>
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
		initValidation();
	</script>
</html>
