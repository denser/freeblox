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
	include_once('Model/network_qos_model.php');
	include_once("Model/shell_command.php");
	$_SESSION['pageID'] = "#log";
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
			var submit = 0;
		</script>
		<?php
			$network_config = NetworkConfigModel::getInstance();
			$priority0 = $network_config->get(NetworkConfigModel::$PRIORITY0);
			$priority1 = $network_config->get(NetworkConfigModel::$PRIORITY1);
			$priority2 = $network_config->get(NetworkConfigModel::$PRIORITY2);
			$priority3 = $network_config->get(NetworkConfigModel::$PRIORITY3);
			$priority4 = $network_config->get(NetworkConfigModel::$PRIORITY4);
			$priority5 = $network_config->get(NetworkConfigModel::$PRIORITY5);
			$priority6 = $network_config->get(NetworkConfigModel::$PRIORITY6);
			$priority7 = $network_config->get(NetworkConfigModel::$PRIORITY7);
			$res = 0;
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();

		if(isset($_POST) && isset($_POST['submit_action'])) {
			if( trim($_POST['priority0']) != $priority0 || 
					trim($_POST['priority1']) != $priority1 || 
					trim($_POST['priority2']) != $priority2
					|| trim($_POST['priority3']) != $priority3 || 
					trim($_POST['priority4']) != $priority4 || 
					trim($_POST['priority5']) != $priority6
					|| trim($_POST['priority7']) != $priority7 ) {
						$network_config->set(NetworkConfigModel::$PRIORITY0,trim($_POST['priority0']));
						$network_config->set(NetworkConfigModel::$PRIORITY1,trim($_POST['priority1']));
						$network_config->set(NetworkConfigModel::$PRIORITY2,trim($_POST['priority2']));
						$network_config->set(NetworkConfigModel::$PRIORITY3,trim($_POST['priority3']));
						$network_config->set(NetworkConfigModel::$PRIORITY4,trim($_POST['priority4']));
						$network_config->set(NetworkConfigModel::$PRIORITY5,trim($_POST['priority5']));
						$network_config->set(NetworkConfigModel::$PRIORITY6,trim($_POST['priority6']));
						$network_config->set(NetworkConfigModel::$PRIORITY7,trim($_POST['priority7']));
						$network_config->createActionList(
							DEPLOY_MODULES::NETWORK_QOS_CONFIG,
							"Network QoS settings updated", 
							DEPLOY_MODULES::NETWORK_QOS_CONFIG_STR,  "");
			}
			$res = 1;
		?>
			<script type="text/javascript">
				submit = 1;
			</script>
		<?php
		}
		?>
		<script type="text/javascript">
			$sub(document).ready(function() {
				initValidation();
				getPageTips("logging");
				setTooltip();
			});

			function submitData() {
				if($sub("#qos").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit();
					return true;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "qos.php";
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "qos.php";
				}
			}

			function initValidation() {
				$sub("#PRIORITY0").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#PRIORITY1").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#PRIORITY2").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#PRIORITY3").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#PRIORITY4").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#PRIORITY5").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#PRIORITY6").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#PRIORITY7").attr("data-validation-engine","validate[required,custom[integer],min[0],max[63]]");
				$sub("#qos").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var val = "Network QoS settings saved successfully";
			function save_Message(val) {
				parent.saveMessage(val);
			}

			var sysLogServer = '<?php print $remote_login_server; ?>';
			function enableRemoteLog() {
				var enable =  document.getElementById("enable_remote_login").checked;
				if( enable == false ) {
					document.getElementById("IPADDR").value = "";
					document.getElementById("IPADDR").disabled = true;
				} else {
					document.getElementById("IPADDR").value = sysLogServer;
					document.getElementById("IPADDR").disabled = false;
				}
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu();" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<h1>Network QoS<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="qos" method="post" action="qos.php" autocomplete="off">
				<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>
					<td style="width: 25%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									QoS Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="logging_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tbody>
							<tr><td>
								<table cellpadding="2" cellspacing="2" class="sub-container-table">
									<tr>
										<td>Priority 0 </td>
										<td>
											<input type="text" value="<?php  print $priority0; ?>" id="PRIORITY0" size="4" maxlength="2" class="ipBox" name="priority0" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td>Priority 1 </td>
										<td>
											<input type="text" value="<?php print $priority1; ?>" id="PRIORITY1" size="4" maxlength="2" class="ipBox" name="priority1" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td>Priority 2 </td>
										<td>
											<input type="text" value="<?php print $priority2; ?>" id="PRIORITY2" size="4" maxlength="2" class="ipBox" name="priority2" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td>Priority 3 </td>
										<td>
											<input type="text" value="<?php print $priority3; ?>" id="PRIORITY3" size="4" maxlength="2" class="ipBox" name="priority3" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td>Priority 4 </td>
										<td>
											<input type="text" value="<?php print $priority4; ?>" id="PRIORITY4" size="4" maxlength="2" class="ipBox" name="priority4" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td>Priority 5 </td>
										<td>
											<input type="text" value="<?php print $priority5; ?>" id="PRIORITY5" size="4" maxlength="2" class="ipBox" name="priority5" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td>Priority 6 </td>
										<td>
											<input type="text" value="<?php print $priority6; ?>" id="PRIORITY6" size="4" maxlength="2" class="ipBox" name="priority6" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
									<tr>
										<td>Priority 7 </td>
										<td>
											<input type="text" value="<?php print $priority7;  ?>" id="PRIORITY7" size="4" maxlength="2" class="ipBox" name="priority7" />
											<img id="logging_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										</td>
									</tr>
								</table>
							</td>
							</tr>
						</tbody>
						</table>
					</td>
					<td style="width: 50%; vertical-align: top;"> &nbsp; </td>
				</tr>
				</table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_logging" onclick="submitData();"><span>Save</span></a>
				<a class="btn-grey-wide-l" id="cancel" onclick="resetData();"><span>Cancel</span></a>
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
		enableRemoteLog();
	</script>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		parent.Time_init('<?php echo "$currentTime"; ?>');
		if( submit == 1 ) {
			save_Message(val);
		}
	</script>
</html>
