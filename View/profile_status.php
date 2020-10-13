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
	include_once("Model/sip_profile_model.php");
	include_once("Model/trunk_config_model.php");
	include_once("Model/roaming_users_model.php");
	include_once("Model/lcr_model.php");
	include_once("Model/media_settings_model.php");
	include_once("Model/shell_command.php");

	$_SESSION['pageID'] = "#Profile_status";
	$sip_config =  SIPProfileConfigModel::getInstance();
	$media_config = MediaConfigModel::getInstance();
	$trunk_config =  TrunkConfigModel::getInstance();
	$siptrunk_config = $trunk_config->getTrunkProfileStatus();
	$i = 0;
	while ($row = $siptrunk_config->fetchArray()) {
		$siptrunk_rules[$i] = $row;
		$i++;
	}
	$roaming_users_config = RoamingUsersModel::getInstance();
	$roaming_users_status = $roaming_users_config->getRoamingUserProfileStatus();
	$i = 0;
	while ($row = $roaming_users_status->fetchArray()) {
		$roamingUsers_status[$i] = $row;
		$i++;
	}
	$lcr_config =  LCRConfigModel::getInstance();
	$lcr_status = $lcr_config->getLCR_Trunks_status();
	$i = 0;
	while ($row = $lcr_status->fetchArray()) {
		$lcrUser_status[$i] = $row;
		$i++;
	}
	$_SESSION['pageID'] = "#log";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/validationEngine.jquery.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/tooltip.css" />
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
		<script src="js/jquery.validationEngine-en.js"></script>
		<script src="js/jquery.validationEngine.js"></script>
		<script src="js/jquery.tooltip.js"></script>
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
			if ( isset($_POST['profile_status_interval']) ) {
				$page_refresh_interval = $_POST['profile_status_interval'];
			}
			else if ( isset($_GET['profile_status_interval']) ) {
				$page_refresh_interval = $_GET['profile_status_interval'];
			} else {
				$page_refresh_interval = 300;
			}
		?>
		<script type="text/javascript">
			function status_refresh() {
				location.replace("profile_status.php?profile_status_interval=" + refresh_interval_frm_srv);
			}

			$sub(document).ready(function() {
				$sub('#profile_status').dataTable( {
					"sScrollX": "1250px",
					"sScrollY": "213px",
					"aaSorting": [[ 0, "desc" ]],
					"bPaginate": false
				} );
				initValidation();
				getPageTips("tools");
				setTooltip();
			});

			function submitform() {
				if($sub("#ProfileStatus").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function initValidation() {
				$sub("#profile_status_interval").attr("data-validation-engine","validate[required,custom[integer],min[15],max[3600]]");
				$sub("#ProfileStatus").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function download_logs(val) {
				parent.downloadLogs(val);
			}

			var refresh_interval_frm_srv = "<?php echo $page_refresh_interval; ?>";
			function refreshPage() {
				location.replace("profile_status.php?profile_status_interval=" + refresh_interval_frm_srv);
			}

			function setRefreshInterval() {
				setInterval(refreshPage, refresh_interval_frm_srv*1000);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();">
		<div class="content">
			<h1>Profile Status<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="ProfileStatus" method="post" action="profile_status.php" autocomplete="off">
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
												<td><input type="text" maxlength=4; size=4; class="ipBox" id="profile_status_interval" name="profile_status_interval" value="<?php echo $page_refresh_interval; ?>" /></td>
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
<!-- Begin: Status Alerts logs Contnet -->
				<table id="profile_status" class="tablesorter" cellpadding="0" cellspacing="1px">
					<thead>
						<tr>
							<td><label>SIP Trunk</label></td>
							<td><label>Roaming User</label></td>
							<td><label>LCR</label></td>
							<td><label>LAN Profile</label></td>
							<td><label>WAN Profile</label></td>
							<td><label>Media Profile</label></td>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = count($siptrunk_rules);
						for($i = 0 ;$i < $cnt; $i++) {
						?>
							<tr id="<?php print $i; ?>" class="<?php print $odd_even;?>">
								<td><?php print $siptrunk_rules[$i][0]; print ":";print $siptrunk_rules[$i][2]; ?></td>
								<td><?php print  "-"; ?></td>
								<td><?php print  "-"; ?></td>
								<td><?php print  $siptrunk_rules[$i][10]; echo ":".$sip_config->getSipProfileInterfaceAddress($siptrunk_rules[$i][10]);
									echo ":".$sip_config->getSipProfileInterfacePort($siptrunk_rules[$i][10]);?></td>
								<td><?php print  $siptrunk_rules[$i][11]; echo ":".$sip_config->getSipProfileInterfaceAddress($siptrunk_rules[$i][11]);
									echo ":".$sip_config->getSipProfileInterfacePort($siptrunk_rules[$i][11]);?></td>
								<td><?php print"".$siptrunk_rules[$i][12]; echo ":".$media_config->getMediaProfileIpPort($siptrunk_rules[$i][12])?></td>
							</tr>
						<?php }
						$cnt = count($roamingUsers_status);
						for($i = 0 ;$i < $cnt; $i++) {
						?>
							<tr id="<?php print $i; ?>" class="<?php print $odd_even;?>">
								<td><?php print  "-"; ?></td>
								<td><?php print $roamingUsers_status[$i][0]; print ":".$roamingUsers_status[$i][5]; ?></td>

								<td><?php print  "-"; ?></td>
								<td><?php print  $roamingUsers_status[$i][3]; echo ":". $sip_config->getSipProfileInterfaceAddress($roamingUsers_status[$i][3]);
								echo ":".$sip_config->getSipProfileInterfacePort($roamingUsers_status[$i][3]);?></td>
								<td><?php print  $roamingUsers_status[$i][2]; echo ":". $sip_config->getSipProfileInterfaceAddress($roamingUsers_status[$i][2]);
								echo ":".$sip_config->getSipProfileInterfacePort($roamingUsers_status[$i][2]);?></td>
								<td><?php print"".$roamingUsers_status[$i][4]; echo ":".$media_config->getMediaProfileIpPort($roamingUsers_status[$i][4]) ?></td>
							</tr>
						<?php }
						$cnt = count($lcrUser_status);
						for($i = 0 ;$i < $cnt; $i++) {
						?>
							<tr id="<?php print $i; ?>" class="<?php print $odd_even;?>">
								<td><?php print  "-"; ?></td>
								<td><?php print  "-"; ?></td>
								<td><?php print $lcrUser_status[$i][0]; ?></td>
								<td><?php print $lcrUser_status[$i][3]; echo ":".$sip_config->getSipProfileInterfaceAddress($lcrUser_status[$i][3]);
								echo ":".$sip_config->getSipProfileInterfacePort($lcrUser_status[$i][3]);?></td>
								<td><?php print "-"; ?></td>
								<td><?php print "-"; ?></td>
							</tr>
						<?php }
						?>
					</tbody>
				</table>
			<!-- End: Status Alerts logs Contnet -->
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
			</div>
			</form>
		</div>
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
