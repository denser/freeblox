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
	include_once("Model/sbc_log_mgr.php");
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#cdr_reports_help";
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
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
			var submit = 0;
		</script>
		<?php
			$res = 0;
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();
		?>
		<script type="text/javascript">
			$sub(document).ready(function() {
				$sub('#cdr_reports').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "213px",
					"aaSorting": [[ 0, "desc" ]],
					"bPaginate": false
				});
				getPageTips("tools");
				setTooltip();
				initValidation();
			});
		</script>
		<?php
			if ( isset($_POST['cdr_reports_interval']) ) {
				$page_refresh_interval = $_POST['cdr_reports_interval'];
			}
			else if ( isset($_GET['cdr_reports_interval']) ) {
				$page_refresh_interval = $_GET['cdr_reports_interval'];
			} else {
				$page_refresh_interval = 300;
			}
		?>
		<script type="text/javascript">
		function status_refresh() {
			location.replace("cdr_reports.php?cdr_reports_interval=" + refresh_interval_frm_srv);
		}

		function submitform() {
			if($sub("#CDRReports").validationEngine('validate')) {
				var formObj = document.forms[0];
				formObj.submit();
				return true;
			}
		}

		function initValidation() {
			$sub("#cdr_reports_interval").attr("data-validation-engine","validate[required,custom[integer],min[15],max[3600]]");
			$sub("#CDRReports").validationEngine('attach', {promptPosition : "bottomRight"});
		}

		function download_logs() {
			<?php 
				SBCLogMgr::downloadQueryCDReports();
			?>
			parent.downloadCdr();
		}

		var refresh_interval_frm_srv = "<?php echo $page_refresh_interval; ?>";
		function refreshPage() {
			location.replace("cdr_reports.php?cdr_reports_interval=" + refresh_interval_frm_srv);
		}

		function setRefreshInterval() {
			setInterval(refreshPage, refresh_interval_frm_srv*1000);
		}

		function delete_logs() {
			var record_val = document.getElementById("cdr_reports_delete").value;
			var pattern = new RegExp(/^[\-\+]?\d+$/);
			if(record_val == ""){
				$sub('#cdr_reports_delete').validationEngine('showPrompt', '* This field is required', 'error', 'bottomRight', true);
				return;
			} else if(!(pattern.test($sub("#cdr_reports_delete").val()))) {
				$sub('#cdr_reports_delete').validationEngine('showPrompt', '* This field accepts only integer[1-500]', 'error', 'bottomRight', true);
				return;
			} else if((record_val < 1) || (record_val > 500)) {
				$sub('#cdr_reports_delete').validationEngine('showPrompt', '* This field accepts only integer[1-500]', 'error', 'bottomRight', true);
			} else {
				parent.user_athentication(record_val);
			}
		}

		function delete_auth(val) {
			if(val == "1") {
				parent.document.getElementById("container").src = "cdr_reports.php";
			} else {
				document.getElementById("delete_log").className="btn-grey-wide-l-disabled";
				document.getElementById("cdr_reports_delete").value="";
			}
		}

		function delete_record() {
			$sub('#cdr_reports_delete').validationEngine('hideAll');
			document.getElementById("delete_log").className="btn-grey-wide-l";
		}
	</script>
	</head>
<body onclick="javascript: parent.hideMenu()">
		<div class="content" style="padding-right:20px;">
			<h1>CDR Reports<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form name="CDRReports" id="CDRReports" method="POST" action="cdr_reports.php" autocomplete="off">
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
												<td><input type="text" maxlength=4; size=4; class="ipBox" id="cdr_reports_interval" name="cdr_reports_interval" value="<?php echo $page_refresh_interval; ?>" /></td>
												<td><a class="btn-grey-wide-l" href="javascript:submitform();" id="update_ci_int"><span>Update Refresh Interval</span></a></td>
												<td><a class="btn-grey-wide-l" onclick="status_refresh();"><span>Refresh</span></a></td>
												<?php
												$cidr_reports = SBCLogMgr::queryCDReports();
												if(count($cidr_reports) == 0) {
													$classname = "btn-grey-wide-l-disabled";
												} else {
													$classname = "btn-grey-wide-l";
												}
												?>
												<td><a class="<?php echo $classname; ?>" id="update" <?php if($classname == "btn-grey-wide-l") {?> onclick="download_logs();" <?php } ?>><span>Download Logs</span></a></td>
											</tr>
											<tr>
												<td><input type="text" maxlength=3; size=4; class="ipBox" id="cdr_reports_delete" name="cdr_reports_delete" value="" onclick="delete_record();" /></td>
												<td><a class="btn-grey-wide-l-disabled"  id="delete_log" onclick="delete_logs();" ><span>Delete Logs</span></a></td>
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
				<table id="cdr_reports" class="tablesorter" cellpadding="0" cellspacing="1px" style="margin-left:1px; width: 100%">
					<thead>
						<tr>
							<th scope="col"><a href="#">ID</a></th>
							<th scope="col"><a href="#">Time</a></th>
							<th scope="col"><a href="#">Method</a></th>
							<th scope="col"><a href="#">Source</a></th>
							<th scope="col"><a href="#">Channel</a></th>
							<th scope="col"><a href="#">Destination</a></th>
							<th scope="col"><a href="#">Dest Channel</a></th>
							<th scope="col"><a href="#">SIP Code</a></th>
							<th scope="col"><a href="#">SIP Reason</a></th>
							<th scope="col"><a href="#">Duration</a></th>
							<th scope="col"><a href="#">Setup Time</a></th>
						</tr>
						</thead>
						<tbody>
						<?php
							$cidr_reports = SBCLogMgr::queryCDReports();
							$count=count($cidr_reports);
							for($j=0;$j < $count;$j++) {
						?>
								<tr id="<?php print $j; ?>" class="<?php print $odd_even;?>">
						<?php
								$time_date = explode(" ",$cidr_reports[$j][7]);
						?>
									<td><a title="<?php print $cidr_reports[$j][0]; ?>"><?php print substr($cidr_reports[$j][0],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][7]; ?>"><?php print $time_date[0]." ".$time_date[1]; ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][1]; ?>"><?php print substr($cidr_reports[$j][1],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][11]; ?>"><?php print substr($cidr_reports[$j][11],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][13]; ?>"><?php print substr($cidr_reports[$j][13],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][12]; ?>"><?php print substr($cidr_reports[$j][12],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][14]; ?>"><?php print substr($cidr_reports[$j][14],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][5]; ?>"><?php print substr($cidr_reports[$j][5],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][6]; ?>"><?php print substr($cidr_reports[$j][6],0,10); ?></a></td>
									<td><a title="<?php print $cidr_reports[$j][8]; ?>"><?php print substr($cidr_reports[$j][8],0,10); ?></a></td>
									<td><a title="<?php print  $cidr_reports[$j][9]; ?>"><?php print substr($cidr_reports[$j][9],0,10); ?></a></td>
								</tr>
						<?php } ?>
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
		initValidation();
	</script>
</html>
