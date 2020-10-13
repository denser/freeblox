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
	include_once('Model/sbc_provisioning.php');
	include_once('Model/shell_command.php');

	$shell_obj = ShellCommand::getInstance();
	$_SESSION['pageID'] = "#time";
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
			var submit;
		</script>
		<?php
			$res = 0;
			$obj = DateTimeConfig::getConfig();
			$result = $obj -> getDTConfig();
			$row = $result -> fetchArray();
			$update_count = $obj -> getActionListCount();
			$obj = DateTimeConfig::getConfig();

			if(isset($_POST) && isset($_POST['submit_action'])) {
				if ( $_POST['CONFIGTYPE'] == "Manual" ) {
					$time_arr = array($_POST['month'],$_POST['date'],$_POST['hour'],
						$_POST['minute'],$_POST['year'],$_POST['TIMEZONE']);
					SysCalls::setTime($time_arr);
					$ntp_val = array(
						"TIMEZONE" => $_POST['TIMEZONE'],
						"NTPSERVER" => $_POST['ntp_arr'],
						"CONFIGTYPE" => $_POST['CONFIGTYPE'],
						"DATE" => $_POST['date'],
						"MONTH" => $_POST["month"],
						"YEAR" => $_POST["year"],
						"HOUR" => $_POST["hour"],
						"MINUTE" => $_POST["minute"],
						"DATECHANGE" => $_POST["DATECHANGE"]
					);
					$obj -> setDTConfig($ntp_val);
					$cmd = "bloxconfig"." ".DEPLOY_MODULES::TIME_SETTINGS_CONFIG;
					$cmdres = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
					$res = 1;
				} else if ( $_POST['CONFIGTYPE'] == "NTP" ) {
					$ntp_arr = SysCalls::serializeVal($_POST['ntp_arr']);
					$ntp_val = array(
						"TIMEZONE" => $_POST['TIMEZONE'],
						"NTPSERVER" => $ntp_arr,
						"CONFIGTYPE" => $_POST['CONFIGTYPE'],
						"DATE" => $_POST['date'],
						"MONTH" => $_POST["month"],
						"YEAR" => $_POST["year"],
						"HOUR" => $_POST["hour"],
						"MINUTE" => $_POST["minute"],
						"DATECHANGE" => $_POST["DATECHANGE"]
					);
					SysCalls::setTZ($_POST['TIMEZONE']);
					$obj -> setDTConfig($ntp_val);
					$cmd = "bloxconfig"." ".DEPLOY_MODULES::TIME_SETTINGS_CONFIG;
					$cmdres = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
					$res = 1;
				}
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
				getPageTips("time_settings");
				setTooltip();
				$sub("#CONFIGTYPE").change();
			});

			function trim(inputStr) {
				return inputStr.toString().replace(/^\s+|\s+$/g,"");
			}

			function wspace_validate(info) {
				var white_space = /\S/;
				if (white_space.test(info)) {	// Check for whitespaces
					return true;
				} else {
					return false;
				}
			}

			function setNTPList() {
				var val = $sub("#ntp_server").val();
				var ntp_server = trim(val);
				var select = document.getElementById('ntp_list');
				var selectLen = select.options.length;
				var ntpVal = ntp_server;
				var i = 0;
				var ntpOption = document.createElement('option');

				var count = $sub('#ntp_list option').length;
				if(count > 3) {
					document.getElementById("ntp_error").innerHTML = "Invalid! NTP server shouldn't exceed 4.";
					document.getElementById("ntp_error").className = "alert-box errorMsg";
					$sub('#ntp_error').show().delay(2000).fadeOut();
					return false;
                                }
				ntpOption.text = ntpVal;
				ntpOption.value = ntpVal;
				if(wspace_validate(ntp_server) == false) {
					$sub("#ntp_server").val("");
					$sub("#ntp_server").focus();
					return false;
				}
				for(i =0; i < selectLen; i++) {
					ntpServList = select.options[i].value;
					if(ntpVal == ntpServList) {
						document.getElementById('ntp_server').focus();
						return false;
					}
				}
				if($sub.browser.msie) {
					select.add(ntpOption);
					document.getElementById('ntp_server').value = '';
				} else {
					select.add(ntpOption, null);
					document.getElementById('ntp_server').value = '';
				}
				return false;
			}

			var ntplist;
			function submitData() {
				if (document.getElementById("CONFIGTYPE").value == "NTP") {
					if( document.getElementById("ntp_list").length < 1) {
						document.getElementById("ntp_server").focus();
						return false;
					}
				}
				if($sub("#timesettings").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit_action.value = "1";
					$sub('#ntp_list option').attr('selected', 'selected');
					formObj.submit();
					return true;
				}
			}

			function resetData() {
				parent.document.getElementById("container").src = "time_settings.php";
			}

			function refresh_iframe(val) {
				if(val == 1) {
					parent.document.getElementById("container").src = "time_settings.php";
				}
			}

			function setConfigType(elem) {
				if(elem.value == "Manual") {
					$sub("#date_day").removeAttr("disabled");
					$sub("#date_month").removeAttr("disabled");
					$sub("#date_year").removeAttr("disabled");
					$sub("#time_hour").removeAttr("disabled");
					$sub("#time_minute").removeAttr("disabled");
					$sub("#ntp_server").attr("disabled", "disabled");
					$sub("#ntp_list").attr("disabled", "disabled");
				} else {
					$sub("#ntp_server").removeAttr("disabled");
					$sub("#ntp_list").removeAttr("disabled");
					$sub("#date_day").attr("disabled", "disabled");
					$sub("#date_month").attr("disabled", "disabled");
					$sub("#date_year").attr("disabled", "disabled");
					$sub("#time_hour").attr("disabled", "disabled");
					$sub("#time_minute").attr("disabled", "disabled");
				}
			}

			function deleteNTPList() {
				$sub("#ntp_list option:selected").remove();
				$sub("#reset_default").attr("class", "btn-grey-wide-l");
			}

			function initValidation() {
				$sub("#time_hour").attr("data-validation-engine","validate[required,custom[integer],min[00],max[23]]");
				$sub("#time_minute").attr("data-validation-engine","validate[required,custom[integer],min[00],max[59]]");
				$sub("#date_day").attr("data-validation-engine","validate[required,custom[integer],min[1],max[31]]");
				$sub("#date_month").attr("data-validation-engine","validate[required,custom[integer],min[1],max[12]]");
				$sub("#date_year").attr("data-validation-engine","validate[required,custom[integer],min[2013]]");
				$sub("#timesettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			var success = "Date/Time settings saved successfully";

			function redirect_logout() {
				parent.window.location = 'logout.php';
			}

			function save_Message(success) {
				setTimeout(redirect_logout,100);
				parent.saveMessage(success);
			}

			var failure = "Error while applying Date/Time settings";
			function error_Message(failure) {
				parent.errorMessage(failure);
			}
		</script>
	</head>
	<body onclick="javascript: parent.hideMenu()" onload="refresh_iframe('<?php print $res;?>');">
		<div class="content">
			<?php
				if ($update_count > 0 ) {
					include_once("notification.html");
					$class = "notify_changes";
				}
			?>
			<h1 class="<?php print $class; ?>">Date / Time Settings<img src="images/icn_help.png" style="margin-left: 10px; cursor: pointer" onclick="parent.helpPage()" /></h1>
			<form id="timesettings" name="datetime" method="post" action="time_settings.php" autocomplete="off">
			<table cellpadding="5" cellspacing="5" class="system-settings-table" >
				<tr>
					<td style="width: 50%; vertical-align: top;">
						<table class="sub-container" cellpadding="0" cellspacing="0">
							<thead>
								<th>
									Date / Time Settings
									<img src="images/Icn_info_blue_bg.png" class="ttip" id="time_settings_001" align="right" style="margin-right: 5px" />
								</th>
							</thead>
							<tr>
								<td>
									<table cellpadding="2" cellspacing="2" class="sub-container-table">
										<tr>
											<td>Configuration Type</td>
											<td>
												<select id="CONFIGTYPE" name="CONFIGTYPE" class="ipDrpBox" onchange="setConfigType(this);" onkeyup="setConfigType(this);">
													<option value="Manual" <?php if($row["CONFIGTYPE"] == "Manual") echo "selected='selected'" ?>>Manual</option>
													<option value="NTP" <?php if($row["CONFIGTYPE"] == "NTP") echo "selected='selected'" ?>>NTP</option>
												</select>
												<img id="time_settings_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr>
											<td>Date/Time</td>
											<?php
												$shell_status_date = $shell_obj->Execute("date +'%H|%M|%d|%m|%Y'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
												if( $shell_status_date == 0) {
													$today =trim( $shell_obj->getLastOutput());
												}
												//$today = shell_exec("date +'%H|%M|%d|%m|%Y'");
												$date_time = explode("|",$today);
											?>
											<td>
												<input id="time_hour" name="hour" value="<?php print $date_time[0]; ?>" maxlength="2" type="text" class="ipBox" style="width: 30px; text-align: center" />&nbsp;:
												<input id="time_minute" name="minute" value="<?php print $date_time[1]; ?>" maxlength="2" type="text" class="ipBox" style="width: 30px; text-align: center" />&nbsp;-
												<input id="date_day" name="date" maxlength="2" value="<?php print $date_time[2]; ?>" type="text" class="ipBox" style="width: 30px; text-align: center" />&nbsp;/
												<input id="date_month" name="month" maxlength="2" value="<?php print $date_time[3]; ?>" type="text" class="ipBox" style="width: 30px; text-align: center" />&nbsp;/
												<input id="date_year" name="year" maxlength="4" value="<?php print $date_time[4]; ?>" type="text" class="ipBox" style="width: 40px; text-align: center" />
												<img id="time_settings_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr>
											<td>Time Zone</td>
											<td>
												<select class="ipDrpBox" id="TIMEZONE" name="TIMEZONE" >
												<?php $zone_list = DateTimeConfig::getTimeZoneList();
													$zone_selected = $row[TIMEZONE];
													$count = count($zone_list);
													for($i=0; $i < $count; $i++) {
														if($zone_list[$i] == "")
															continue;
												?>
													<option <?php if(!strcmp(trim($zone_list[$i]),trim($zone_selected))) print "selected"; ?>><?php echo $zone_list[$i];?></option> <?php } ?> 
												</select>
												<img id="time_settings_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
											</td>
										</tr>
										<tr>
											<td>NTP Server </td>
											<td colspan="2">
												<table>
													<tr>
														<td><input type="text" id="ntp_server" class="ipBox" size="21" maxlength="256" value="" name="ntp_Server" /></td>
														<td><a class="btn-grey-wide-l-2" onclick="setNTPList();" ><span>Add</span></a></td>
</tr>
<tr>
	<td colspan="2" id="ntplisterror" ><span id="ntp_error"></span></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td>&nbsp;</td>
											<td colspan="2">
												<table>
													<tr>
														<td>
															<select id="ntp_list" name="ntp_arr[]" multiple="multiple"  size="5" style="width: 160px; font-size: 12px">
															<?php
																$ntplist = $row["NTPSERVER"];
																$ntpdata = explode(",",$ntplist);
																for($i=0; $i<count($ntpdata); $i++) {
																	if($ntpdata[$i] != "") {
															?>
																		<option value="<?php echo $ntpdata[$i] ?>"><?php echo $ntpdata[$i] ?></option>
															<?php
																	}
																}
															?>
															</select>
														</td>
														<input type="hidden" id="NTPSERVER" name="NTPSERVER" value="" />
														<td style="vertical-align: top;margin-left: 2px;"><a class="btn-grey-wide-l-2" onclick="deleteNTPList()"><span>Delete</span></a></td>
													</tr>
												</table>
											</td>
										</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td style="width: 50%; vertical-align: top;">
							&nbsp;
						</td>
					</tr>
				</table>
				<input type="hidden" name="submit_action" value="0" />
				<input type="hidden" name="DATECHANGE" value="false" />
			<div class="page-btns">
				<label style="font-size: 12px; display: none" id="system_time"></label>
				<label style="font-size: 12px; display: none" id="table_time"></label>
				<a class="btn-grey-wide-l" id="apply_datetime" name="submittime" onclick="submitData();"><span>Apply</span></a>
				<a class="btn-grey-wide-l" id="reset_default" onclick="resetData();"><span>Cancel</span></a>
			</div>
			</form>
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
		initValidation();
		if( submit == 1 ) {
			save_Message(success);
		}
	</script>
</html>
