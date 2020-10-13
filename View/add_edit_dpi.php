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
	include_once("Model/dpi_config_model.php");
	include_once("Model/sbc_dpi_dyn_params.php");
	include_once("Model/action_mapping_model.php");

	$dpi_config_model = DpiConfigModel::getInstance();
	$action_mapping_model = ActionMappingModel::getInstance();
	$action_names = $action_mapping_model->getActionMapping();
	$action_str_array = array();
	while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
		$action_str_array[$row['VAL']] = $row['NAME'];
	}
	$res = 0;
	$page_title = "Create DPI Configuration";
	$flag = "add";
	$category = "";
	$ui_name = "";
	$action = "";
	$enabled = "";
	$blocking_duration = "";
	$params = "";
	$params_array = null;

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$flag = "edit";
		$page_title = "Edit DPI Configuration";
		$row = $dpi_config_model->getDpiConfig($val);
		if ( $row ) {
			$category = $row[DpiConfigModel::$CATEGORY];
			$ui_name = $row[DpiConfigModel::$UI_NAME];
			$action = $row[DpiConfigModel::$ACTION];
			$enabled = $row[DpiConfigModel::$ENABLED];
			$blocking_duration = $row[DpiConfigModel::$BL_DURATION];
			$params = $row[DpiConfigModel::$PARAMS];
			$params_array = explode(";",$params);
			$action_names = $action_mapping_model->getActionMapping();
			while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
				if ( $action == $row['VAL'] ) {
					$selected_action = $row['NAME'];
					break;
				}
			}
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enable'] == "on")
			$post_enable = "1";
		else
			$post_enable = "";
		if ($_POST['submit_action'] == "1") {	// Edit
			$dynparams_count = $_POST['dynparams_count'];
			$new_params = "";
			$action_names = $action_mapping_model->getActionMapping();
			for ( $i = 0; $i < $dynparams_count && $i < 16 ; $i++ ) {
				$key = "dyn_param_key".$i;
				$value = "dyn_param_val".$i;
				if ( isset($_POST[$key]) &&  isset($_POST[$value] ) ) {
					$new_params = $new_params . trim($_POST[$key]).":" .trim($_POST[$value]) . ";";
				}
			}
			$selected_action = "";
			while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
				if ( $action == $row['VAL'] ) {
					$selected_action = $row['NAME'];
					break;
				}
			}
			$id = $_POST['id'];
			$rows = $dpi_config_model->getDpiConfig($id);
			if ( $rows ) {
				$action = $rows[DpiConfigModel::$ACTION];
				$enabled = $rows[DpiConfigModel::$ENABLED];
				$blocking_duration = $rows[DpiConfigModel::$BL_DURATION];
				$action_names = $action_mapping_model->getActionMapping();
				while($row = $action_names->fetchArray(SQLITE3_ASSOC)) {
					if ( $action == $row['VAL'] ) {
						$selected_action = $row['NAME'];
						break;
					}
				}
				if( $_POST['action'] == "Block" ) {
					if ( trim($_POST['block_dur']) != "" && trim($_POST['block_dur']) != 0 )
						$post_action = "4";
					else
						$post_action = "1";
				}
				if( $_POST['action'] == "Allow")
					$post_action = "2";
				if( $_POST['action'] == "Log")
					$post_action = "3";
				$new_bl_duration = trim($_POST['block_dur']);
				if ( $new_bl_duration == "none" )
					$new_bl_duration = "0";
				if( $post_action != $action || $post_enable != $enabled 
							|| $new_bl_duration != $blocking_duration 
							|| $params != $new_params) {
					$dpi_config_model->setDpiConfig($_POST['category'], 
									$post_action, $post_enable, 
									$new_bl_duration, $new_params);
					$dpi_config_model->createActionList(
								DEPLOY_MODULES::DPI_CONFIG, 
								"DPI Settings edited", 
								DEPLOY_MODULES::DPI_CONFIG_STR, 
								"");
				}
				$res = 1;
			}
		}
	}
// Set Enable / Disable parameter via Ajax Post Request
	if($_POST['en_dis'] == "true")
		$post_enable_val = "1";
	else if($_POST['en_dis'] == "false")
		$post_enable_val = "";
	$cat_name = $_POST['cat_name'];
	$row = $dpi_config_model->getDpiConfig($cat_name);
	$db_val = $row[DpiConfigModel::$ENABLED];
	if( $post_enable_val != $db_val ) {
		$dpi_config_model->setDpiEnable ( $cat_name,$post_enable_val );
		$dpi_config_model->createActionList(DEPLOY_MODULES::DPI_CONFIG, 
					"DPI Settings edited", DEPLOY_MODULES::DPI_CONFIG_STR, "");
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
			var flag = "<?php echo $flag; ?>";
			var params = "<?php echo $params; ?>";
			$sub(document).ready(function() {
				initValidation();
				getPageTips("dpi_sigs");
				setTooltip();
			});

			function initValidation() {
				$sub("#BLOCK_DUR").attr("data-validation-engine", "validate[required,custom[integer],min[0],max[86400]]");
				$sub("#dpi_sigs_0010").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[10000]]");
				$sub("#dpi_sigs_0011").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[3600]]");
				$sub("#dpi_sigs_0020").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[10000]]");
				$sub("#dpi_sigs_0021").attr("data-validation-engine", "validate[required,custom[integer],min[1],max[3600]]");
				$sub("#adddpisigs").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				if($sub("#adddpisigs").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add") {
						formObj.submit_action.value = "2";
					} else {
						formObj.submit_action.value = "1";
					}
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
					parent.document.getElementById("container").src = "dpi_signatures.php";
				}
			}

			function disable_duration() {
				var action = document.getElementById("ACTION").value;
				if ( action == "Block" ) {
					document.getElementById("BLOCK_DUR").disabled = false;
				} else {
					document.getElementById("BLOCK_DUR").disabled = true;
					document.getElementById("BLOCK_DUR").value = "none";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="adddpisigs" name="adddpisigs" class="popup-form" action="add_edit_dpi.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr style="display:none;">
							<td>
								<input type="hidden" name="id" value="<?php echo $category; ?>" />
							</td>
						</tr>
						<tr style="display:none;">
							<td>Category</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" name="category" value="<?php echo $category; ?>" />
							</td>
						</tr>
						<tr>
							<td><label for="enable">Enabled</label></td>
							<td width="20"></td>
							<?php
								if( $enabled == "1")
									$enabled = "checked";
								else
									$enabled = "";
							?>
							<td>
								<input type="checkbox" name="enable" <?php print $enabled;?> id="enable" />
							</td>
						</tr>
						<tr>
							<td>Category</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" name="ui_name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $ui_name; ?>" size="40" maxlength="32" />
								<img id="dpi_sigs_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<?php
							$sbc_dyn_params_mapping = SbcDpiDynamicParameters::getInstance();
							for ( $i = 0; $i < count($params_array); $i++ ) {
								$j = $i+4;
								$k=$i+6;
								$x = $i+10;
								$y = $i+20;
								$keyval =  explode(':', $params_array[$i]);
								if ( count($keyval) == 2 ) {
						?>
									<tr>
										<td><?php echo $sbc_dyn_params_mapping->getUiString($category, $keyval[0]); ?></td>
										<td width="20"></td>
										<td>
										<?php 
											if ( $ui_name == "SIP Extensions Discovery") {
										?>
												<input type="hidden" name='<?php print "dyn_param_key".$i;?>' value="<?php echo $keyval[0]; ?>" />
												<input type="text" id="<?php print "dpi_sigs_00".$x; ?>" class="ipBox" name='<?php print "dyn_param_val".$i;?>' value="<?php echo $keyval[1]; ?>" maxlength="" />
												<img id="<?php print "dpi_sigs_00".$k; ?>" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										<?php
											} else {
										?>
												<input type="hidden" name='<?php print "dyn_param_key".$i;?>' value="<?php echo $keyval[0]; ?>"  />
												<input type="text" id="<?php print "dpi_sigs_00".$y; ?>" class="ipBox" name='<?php print "dyn_param_val".$i;?>' value="<?php echo $keyval[1]; ?>" maxlength="" />
												<img id="<?php print "dpi_sigs_00".$j;?>" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
										<?php
											}
										?>
										</td>
									</tr>
						<?php
								}
							}
							$j++;
							$k++;
						?>
						<tr style="display:none;">
							<td>DynParams</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" name="dynparams_count" value="<?php echo count($params_array); ?>" />
							</td>
						</tr>
						<tr>
							<td>Action</td>
							<td width="20"></td>
							<td>
								<select name="action" id="ACTION" onchange="disable_duration();" onkeydown="disable_duration();" onkeyup="disable_duration();">
							<?php
								for ( $j = 1; $j <= count($action_str_array); $j++ ) {
									$select_str = "";

										if ( $action_str_array[$j] != "Blacklist" ) {
											if ( $action_str_array[$j] == $selected_action ) {
												$select_str = "selected";
											}
							?>
											<option value="<?php print $action_str_array[$j];?>" <?php print  $select_str;?> ><?php print  $action_str_array[$j];?></option>
							<?php
										}
								}
							?>
								</select>
								<img id="dpi_sigs_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<?php
							if ( $category == "sip_preproc" ) {
								$class = "display:none";
							} else {
								$class = "";
							}
						?>
						<tr style="<?php print $class; ?>">
							<?php
								if ( $blocking_duration == "0" || $blocking_duration == "" )
									$blocking_duration = "none";
							?>
							<td>Blacklist Duration in seconds</td>
							<td width="20"></td>
							<td>
								<input type="text" id="BLOCK_DUR" class="ipBox" name="block_dur" value="<?php echo $blocking_duration; ?>" maxlength="5" />
								<img id="dpi_sigs_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="DPICONFIG_LIST" id="DPICONFIG_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
		<script type="text/javascript">
			disable_duration();
		</script>
	</body>
</html>
