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
	include_once("Model/db_mgr.php");
	include_once("Model/shell_command.php");
	include_once("Model/device_settings_model.php");

	$_SESSION['pageID'] = "#firware_up";
	$obj = DateTimeConfig::getConfig();
	$shell_obj = ShellCommand::getInstance();
	$result = $obj -> getDTConfig();
	$row = $result -> fetchArray();
	$update_count = $obj -> getActionListCount();
	$dbh = DB_Handle::Connect();
	$applychangecount = $dbh->getActionListCount();
	$dev_set_obj = DeviceSettingsModel::getInstance();
	$auto_upg = $dev_set_obj->get(DeviceSettingsModel::$AUTO_UPGRADE);
	$auto_upg_url = $dev_set_obj->get(DeviceSettingsModel::$AUTO_UPGRADE_URL);
	$res  = "0";

	if(isset($_POST) && $_POST['AUTO_UPG_SUBMIT'] == '1') {
		$dev_set_obj->set(DeviceSettingsModel::$AUTO_UPGRADE,$_POST['AUTO_UPG_HIDDEN']);
		$dev_set_obj->set(DeviceSettingsModel::$AUTO_UPGRADE_URL,$_POST['AUTO_UPG_URL']);
		$res = "1";
	}
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
				var set =0;
				var applychangecount = "<?php echo $applychangecount; ?>";
			</script>
			<script type="text/javascript">
				$sub(document).ready(function() {
					initValidation();
					OnClickAutoUpg();
					getPageTips("tools");
					setTooltip();
				});

				function submitData() {
					var formObj = document.forms[0];
					if(applychangecount != 0) {
						firmConfirmMsg();
						if(set == 1) {
							formObj.submit();
							return true;
						} else {
							return false;
						}
					} else {
					formObj.upload_file.value = "1";
					formObj.submit();
					return true;
					}
				}

				function initValidation() {
					if($sub("#AUTO_UPG_CHK").is(":checked")) {
						$sub("#AUTO_UPG_URL").attr("data-validation-engine","validate[required,custom[url]]");
						$sub("#UPGRADE").validationEngine('attach', {promptPosition : "bottomRight"});
					}
				}

				function OnClickAutoUpg() {
					if($sub("#AUTO_UPG_CHK").is(":checked")) {
						$sub("#AUTO_UPG_HIDDEN").val("1");
						document.getElementById("AUTO_UPG_URL").disabled = false;
					} else {
						$sub("#AUTO_UPG_HIDDEN").val("0");
						document.getElementById("AUTO_UPG_URL").disabled = true;
						document.getElementById("AUTO_UPG_URL").value = "";
					}
				}

				function autoUpgSub() {
					var formObj = document.forms[0];
					if($sub("#UPGRADE").validationEngine('validate')) {
						if($sub("#AUTO_UPG_CHK").is(":checked")) {
							$sub("#AUTO_UPG_HIDDEN").val("1");
						} else {
							$sub("#AUTO_UPG_HIDDEN").val("0");
						}
						document.getElementById("AUTO_UPG_URL").disabled = false;
						formObj.AUTO_UPG_SUBMIT.value = "1";
					formObj.submit();
					return true;
					}
				}

				function firmConfirmMsg() {
					parent.callConfirmFirmUpgrade();
				}

				function firmUpgAction(val) {
					var firmupg =val;
					if(firmupg == 1) {
						var firmdelconfm= $sub.ajax({
							type : "POST",
							cache: false,
							data : {FirmUpgval:firmupg},
							url : "show_iface_ip.php",
							global: false,
							async:false,
							success: function (data) {}
						}).responseText;
							set = firmdelconfm;
							parent.document.getElementById("container").src = "firmware.php";
					} else {
						set = 0;
					}
				}

				function refresh_iframe(val) {
					if(val == 1) {
						parent.document.getElementById("container").src = "firmware.php";
					}
				}

				function systemRestart() {
					$sub.ajax({
						type: "POST",
						cache: false,
						data: {reboot_device:1},
						url:  "admin_service.html",
						success: function (html) {
							parent.window.location = 'wait.php?time=900&val=4';
						}
					});
				}
				var failure = "Error encountered during firmware update. Please contact support team.";
				function error_Message(failure) {
					parent.errorMessage(failure);
				}
			</script>

<?php
			if(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && 
					strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
						$postMax = ini_get('post_max_size');
?>
			<script type = "text/javascript">
				var postMaxSize = '<?php print $postMax; ?>';
				error_Message("Invalid! Please upload a file of size less than " +postMaxSize);
			</script>
<?php
			}
			if (isset($_POST) && ($_POST['upload_file'] == "1")) {
				$tmp_name = $_FILES["upload_firmware"]["tmp_name"];
				$file_name = $_FILES["upload_firmware"]["name"];
				$finfo = new finfo(FILEINFO_MIME);
				if($tmp_name != NULL)
					$type = $finfo->file($tmp_name);
				$mime = substr($type, 0, strpos($type, ';'));
				//if(strcasecmp($mime,"application/x-gzip") != 0 )
				if(strcasecmp($mime,"application/x-gzip") != 0 ) {
?>
					<script type="text/javascript">
						submit = 1;
					</script>
			<?php
				} else {
					if(!move_uploaded_file($_FILES['upload_firmware']['tmp_name'], "/var/tmp/blox_update.tgz")) {
						die('Error uploading file - check destination is writeable.');
					} else {
						$des_path = "/var/tmp/blox_update.tgz";
					}

			?>
			<script type="text/javascript">
				var tmpfile = '<?php print($des_path);?>';
				parent.window.location = 'wait.php?time=900&val=4&firmfile='+ tmpfile;
			</script>
			<?php
				}
			}
			 ?>
		</head>
		<body onclick="javascript: parent.hideMenu()" onload="refresh_iframe('<?php print $res;?>');">
			<div class="content">
				<form enctype="multipart/form-data" method="post" action="firmware.php" name="UPGRADE" id="UPGRADE">
				<h1>Package Upgrade<img onclick="parent.helpPage()" style="margin-left: 10px; cursor: pointer" src="images/icn_help.png"></h1>
					<table border="0" cellpadding="0" cellspacing="0" style="margin: 0px" class="table-config no-border">
						<tbody>
							<tr>
							<td width="450">
								<table width="760" height="150" cellspacing="0" cellpadding="0" style="width:100%;" class="sub-container" border="0">
									<tbody>
										<thead>
											<th colspan="3">FreeBlox Version: <label id="current_version"><?php print SysCalls::getAppVersion();?></label><img src="images/Icn_info_blue_bg.png" class="ttip" id="tools_002" align="right" style="margin-right: 5px"></th>
										</thead>
										<tr style="height: 30px;">
										<td colspan="3">
											<label>(Choose file path of the upgrade file)</label>
										</td>
										</tr>
										<tr style="height: 30px;"><td> <label for="file">Filename:</label> </td>
										<td>
											<input type="file" id="file" name="upload_firmware" style="font-size: 12px">
											<input type="hidden" name="hid" value="1">
										</td>
										<td>
											<label>(Need Reboot)</label>
										</td>
										<tr style="height: 30px;">&nbsp;</tr>
										<tr style="height: 10px;">&nbsp;</tr>
										</tbody>
								</table>
							</td>
							<td width="20"></td>
<!-- Auto Upgrade -->
							<td width="450">
								<table width="760" height="150" cellspacing="0" cellpadding="0" style="width:100%;" class="sub-container" border="0">
									<tbody>
										<thead>
											<th colspan="3">Auto Upgrade Blox: <img align="right" style="margin-right: 5px" id="tools_003" class="ttip" src="images/Icn_info_blue_bg.png"></th>
										</thead>
										<tr style="height: 30px;">
											<td>Auto Upgrade</td>
											<td width="2px"></td>
											<td>
												<input type="checkbox" <?php if($auto_upg == 1) echo "checked='checked'" ?> id="AUTO_UPG_CHK" name="AUTO_UPG_CHK" onclick="OnClickAutoUpg();" />
											</td>
										</tr>
										<tr style="height: 30px;">
											<td>Auto Upgrade URL</td>
											<td width="2px"></td>
											<td>
												<input type="text" class="ipBox" id="AUTO_UPG_URL" name="AUTO_UPG_URL" value="<?php echo $auto_upg_url ?>" size="27" maxlength="256" />
												<img id="tools_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr style="height: 30px;">
											<td colspan="3">
												<a input name="auto_update" type="submit" onclick="autoUpgSub();" class="btn-grey-wide-l" style="float:right; margin-right: 20px;" id="btn-autoupgrade"><span>Save</span></a>
											</td>
										</tr>
										<input type="hidden" id="AUTO_UPG_HIDDEN" name="AUTO_UPG_HIDDEN" value="0" />
										<input type="hidden" id="AUTO_UPG_SUBMIT" name="AUTO_UPG_SUBMIT" value="0" />
										<tr style="height: 10px;">&nbsp;</tr>
										</tbody>
								</table>
							</td>
							</tr>
<!-- Auto Upgrade -->
						</tbody>
					</table>
			<div>
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
			</div>
				<input type="hidden" name = "upload_file" value="0" />
				<div class="page-btns">
					<a input name="update" type="submit" onclick="submitData();" class="btn-grey-wide-l" id="btn-upgrade"><span>Upgrade</span></a>
				</div>
				</form>
			</div>
<!-- Package version,name details -->
				<div>
				<table>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				<div style="width:60%">
					<table border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px" class="table-config no-border">
						<table id="package_data" class="tablesorter" cellpadding="0" cellspacing="1px">
							<thead>
								<tr>
									<th><a href="#">Package Name</a></th>
									<th><a href="#">Version Installed</a></th>
									<th><a href="#">Platform</a></th>
								</tr>
							</thead>
							<tbody>
							<?php
								$output_array = array();
								$checkUbuntu = SysCalls::checkUbuntuCentos();
								if($checkUbuntu != "") {
									$shell_status_archnam = $shell_obj->Execute('arch', ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
									if($shell_status_archnam == 0) {
										$platform = trim($shell_obj->getLastOutput());
									}
									$cmds = array("dpkg --list | grep 'allo'","dpkg --list | grep 'blox'");
									for($c=0;$c<count($cmds);$c++) {
										$shell_status_dpkg = $shell_obj->Execute($cmds[$c], ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
										if($shell_status_dpkg == 0) {
											$dpkg_data = $shell_obj->getLastOutput();
										}
										$line_data = explode("\n",trim($dpkg_data));
										$count = count($line_data);
										for($i=0;$i<$count;$i++) {
											if($line_data[$i] != "" ) {
												$tab_data = preg_split('/\s+/',$line_data[$i] );
												$str = $tab_data[1].",".$tab_data[2].",".$platform;
												array_push($output_array,$str);
											}
										}
									}
								} else {
									 $rpmcmds = array("rpm -qa | grep 'allo'","rpm -qa | grep 'blox'");
									for($c=0;$c<count($rpmcmds);$c++) {
									$shell_status_rpm = $shell_obj->Execute($rpmcmds[$c], ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
									if(  $shell_status_rpm == 0) {
										$rpm_data = $shell_obj->getLastOutput();
									}
									$trim_nl = explode("\n",trim($rpm_data));
									$pack_count = count($trim_nl);
									for($i=0;$i<$pack_count;$i++) {
										if (($trim_nl[$i] != "") && (strpos($trim_nl[$i], '.') !== FALSE)) {
											$output_data = explode(".",$trim_nl[$i]);
											$platform = array_pop($output_data);
											$str = implode(".",$output_data );
											$packet_ver = explode("-",$str);
											$count = count($packet_ver);
											$ver = $packet_ver[$count-2]."-".$packet_ver[$count-1];
											array_pop($packet_ver);
											array_pop($packet_ver);
											$package_name = "";
											for($p =0;$p <count($packet_ver);$p++) {
												$package_name .=  $packet_ver[$p];
											}
											$arr_data = $package_name.",".$ver.",".$platform;
											array_push($output_array,$arr_data);
										}
									}
									} }
								?>
								<?php
									for($i = 0; $i < count($output_array);$i++) {
										$data = explode(",",$output_array[$i]);
								?>
								<tr>
									<td><?php print $data[0]; ?></td>
									<td><?php print $data[1]; ?></td>
									<td><?php print $data[2]; ?></td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</table>
				</div>
				</div>
<!-- Package version,name details -->
		</body>
		<?php
			include_once ("time_panel.php");
			$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$currentTime =trim( $shell_obj->getLastOutput());
			}
		?>
		<script type="text/javascript">
			initValidation();
			parent.setUpdateValue('<?php echo $update_count ?>');
			parent.Time_init('<?php echo "$currentTime"; ?>');
			if( submit == 1) {
				error_Message(failure);
			}
		</script>
		<script src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.dataTables.js"></script> 
		<script type="text/javascript">
			var $sub1 = $.noConflict(true);
		</script>
		<script>
			$sub1(document).ready(function(){
				$sub1('#package_data').dataTable( {
					"sScrollY": "200px",
					"aaSorting": [[ 0, "asc" ]],
					"aoColumns":[
						{"bSortable": true},
						{"bSortable": true},
						{"bSortable": true}
					],
					"bPaginate": false
				});
			});
		</script>
	</html>
