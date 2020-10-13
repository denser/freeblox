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
	include_once("Model/plugins_model.php");

	$plugin_config =  Plugins_ConfigModel::getInstance();
	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@',' ', $val);
		$flag = "edit";
		$page_title = "Edit Plugin";
		$row = $plugin_config->getPlugin($id);
		if($row) {
			$name = $row[Plugins_ConfigModel::$NAME];
			$enabled = $row[Plugins_ConfigModel::$ENABLE];
			$params = $row[Plugins_ConfigModel::$PARAMS];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $plugin_config->getPlugin($id);
			$dynparams_count = $_POST['dynparams_count'];
			$new_params = "";
			if ($_POST['name'] == "humbug") {
				if ($_POST['freq'] == "minutes") {
					$post_value = $_POST['dyn_param_val3'].":"."*".":"."*";
				}
				if ($_POST['freq'] == "hours") {
					$post_value = "*".":".$_POST['dyn_param_val3'].":"."*";
				}
				if ($_POST['freq'] == "days") {
					$post_value = "*".":"."*".":".$_POST['dyn_param_val3'];
				}
				$new_params =
				$_POST[dyn_param_key0]."=".$_POST['dyn_param_val0']."|".
				$_POST[dyn_param_key1]."=".$_POST['dyn_param_val1']."|".
				$_POST[dyn_param_key2]."=".$_POST['dyn_param_val2']."|".
				$_POST[dyn_param_key3]."=".$post_value."|".
				$_POST[dyn_param_key4]."=".$_POST['dyn_param_val4'];
			}
			if ($_POST['name'] != "humbug") {
				for ( $i = 0; $i < $dynparams_count; $i++ ) {
					$key = "dyn_param_key".$i;
					$value = "dyn_param_val".$i;
					if ( isset($_POST[$key]) &&  isset($_POST[$value] ) ) {
						if (trim($_POST[$key]) != "" && trim($_POST[$value]) != "") {
							$new_params = $new_params.trim($_POST[$key])."=".trim($_POST[$value])."|";
						}
					}
				}
			}
			if($_POST['enable'] == "on")
				$post_enable = "1";
			else
				$post_enable = "0";
			if($row) {
				$name = $row[Plugins_ConfigModel::$NAME];
				$enabled = $row[Plugins_ConfigModel::$ENABLE];
				$params = $row[Plugins_ConfigModel::$PARAMS];
				if ( $enabled != $post_enable || $params !=  $new_params ) {
					$plugin_config->editPlugin(trim($_POST['name']), $post_enable,
								$new_params);
				}
				$res = 1;
				$plugin_config->createActionList(DEPLOY_MODULES::PLUGIN_HUMBUG_CONFIG, 
					"Plugin edited", DEPLOY_MODULES::PLUGIN_HUMBUG_CONFIG_STR, "");
			}
		}
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
		</script>
		<script type="text/javascript">
			var flag = "<?php echo $flag ?>";
			$sub(document).ready(function(){
				initValidation();
				getPageTips("plugin_ttip");
				setTooltip();
			});

			function initValidation() {
				$sub("#plugin_0010").attr("data-validation-engine","validate[required]");
				$sub("#plugin_0012").attr("data-validation-engine","validate[required]");
				$sub("#plugin_0014").attr("data-validation-engine","validate[required]");
				$sub("#editplugin").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				if($sub("#editplugin").validationEngine('validate')) {
					var formObj = document.forms[0];
					formObj.submit_action.value = "1";
					formObj.submit();
					return true;
				}
			}

			function closeDialog() {
				parent.closeAddEditDialog();
			}

			function f1(val) {
				if(val == 1) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = "plugins.php";
				}
			}

			function selectFrequency() {
				var freq_val = document.getElementById("freq").value;
				if( freq_val == "minutes" ) {
					document.getElementById("min").disabled = false;
					document.getElementById("hrs").disabled = true;
					document.getElementById("day").disabled = true;
				}
				if( freq_val == "hours" ) {
					document.getElementById("min").disabled = true;
					document.getElementById("hrs").disabled = false;
					document.getElementById("day").disabled = true;
				}
				if( freq_val == "days" ) {
					document.getElementById("min").disabled = true;
					document.getElementById("hrs").disabled = true;
					document.getElementById("day").disabled = false;
				}
			}
		</script>
	</head>
	<body class="popup">
	<body class="popup" onload="f1('<?php echo $res ?>')">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 260px">
				<form id="editplugin" name="editplugin" class="popup-form" action="edit_plugins.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display: none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $name ?>" />
							</td>
						</tr>
						<tr>
							<td>Name</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" maxlength="16" name="name" id="NAME" <?php if ($flag == "edit") print "readonly='readonly'"; ?>  value="<?php print $name; ?>" />
								<img id="plugin_ttip_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 

							</td>
						</tr>
						<tr>
							<td><label for="enable">Enabled</label></td>
							<td width="20"></td>
							<td>
							<?php
								if( $enabled == "1" )
									$enabled = "checked";
								else
									$enabled = "";
							?>
								<input type="checkbox" name="enable" <?php print $enabled; ?> id="enable" />
								<img id="plugin_ttip_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" /> 
							</td>
						</tr>
						<?php
							$params_array = explode("|",$params);
							$params_count = count($params_array);
							for ($j=0;$j<$params_count;$j++) {
								$x = $j+10;
								if( $params_array[$j] != "" ) {
									$key_val =  explode("=",$params_array[$j]);
						?>
						<tr>
							<td><?php print $plugin_config->getPluginNames($key_val[0]); ?></td>
							<td width="20"></td>
							<td>
								<input type="hidden" name='<?php print "dyn_param_key".$j;?>' value="<?php echo $key_val[0]; ?>"  />
								<?php
									if ($key_val[0] == "CDR_UPD_FREQ") {
										$cdr_upd_freq = explode("*",$key_val[1]);
										$minutes = $cdr_upd_freq[0];
										$hours = $cdr_upd_freq[1];
										$days = $cdr_upd_freq[2];
										$min_val = explode(":",$minutes);
										$hour_val =  explode(":",$hours);
										$days_val =  explode(":",$days);
								?>
								<select  name="freq" id="freq" class="ipDrpBox" onchange="selectFrequency();">
									<option <?php if( $min_val[0] != "" ) print "selected=true"; ?> value="minutes">minutes</option>
									<option <?php if( $hour_val[1] != "" ) print "selected=true"; ?> value="hours">hours</option>
									<option <?php if( $days_val[1] != "" ) print "selected=true"; ?> value="days">days</option>
								</select>

								<select name='<?php print "dyn_param_val".$j;?>' name="min" id="min" class="ipDrpBox">
									<option <?php if ( $min_val[0] == "15" ) print "selected=true" ?>>15</option>
									<option <?php if ( $min_val[0] == "30" ) print "selected=true" ?>>30</option>
									<option <?php if ( $min_val[0] == "45" ) print "selected=true" ?>>45</option>
									<option <?php if ( $min_val[0] == "60" ) print "selected=true" ?>>60</option>
								</select>

								<select name='<?php print "dyn_param_val".$j;?>' name="hrs" id="hrs" class="ipDrpBox">
								<?php for( $h = 1; $h < 25; $h++) {
									if ($hour_val[1] == $h)
										$selected = "selected";
									else
										$selected = "";
								?>
									<option <?php print $selected; ?> value="<?php print $h; ?>"><?php print $h; ?></option>
								<?php } ?>
								</select>

								<select name='<?php print "dyn_param_val".$j;?>' name="day" id="day" class="ipDrpBox">
								<?php for( $d = 1; $d < 8; $d++) {
										if ($days_val[1] == $d)
											$selected = "selected";
										else
											$selected = "";
								?>
									<option <?php print $selected; ?> value="<?php print $d; ?>"><?php print $d; ?></option>
								<?php } ?>
								</select>
								<?php } else {?>
									<input type="text" size="64" id="<?php print "plugin_00".$x; ?>" class="ipBox" name='<?php print "dyn_param_val".$j;?>' value="<?php echo $key_val[1]; ?>" maxlength="" />
								<?php } ?>
									<img id="<?php print 'plugin_ttip_0'.$x; ?>" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<?php
								}
							}
						?>
						<tr style="display:none;">
							<td>DynParams</td>
							<td width="20"></td>
							<td><input type="text" class="ipBox" name="dynparams_count" value="<?php echo $params_count; ?>" /></td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="PLUGIN_LIST" id="PLUGIN_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
	<script type="text/javascript">
		initValidation();
		selectFrequency();
	</script>
</html>
