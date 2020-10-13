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
	include_once("Model/lan_profile_model.php");
	include_once("Model/action_mapping_model.php");

	$lan_config = LANProfileConfigModel::getInstance();
	$res = 0;
	$page_title = "Create WAN Profile";
	$flag = "add";
	$name = "";
	$enable = "";
	$desc = "";
	$uname = "";
	$passwd = "";
	$sig_port = "";
	$sig_transport = "";
	$createdDataNames = array();
	$transport_str_array = array("tcp", "udp", "any");
	$sbc_transport_type = SBCTransportType::getInstance();
	$transport_types = $sbc_transport_type->getSBCTransportType();
	$sbc_transport = array();
	while($tr_row = $transport_types->fetchArray(SQLITE3_ASSOC)) {
		$sbc_transport[$tr_row['VAL']] = $tr_row['NAME'];
	}

	if(isset($_GET['data'])) {
		$val = $_GET['data'];
		$id = str_replace('@', ' ', $val);
		$flag = "edit";
		$page_title = "Edit WAN Profile";
		$row = $lan_config->getLANProfile($id);
		if($row) {
			$name = $row[LANProfileConfigModel::$NAME];
			$enabled = $row[LANProfileConfigModel::$ENABLE];
			$desc = $row[LANProfileConfigModel::$DESCRIPTION];
			$uname = $row[LANProfileConfigModel::$USERNAME];
			$passwd = $row[LANProfileConfigModel::$PASSWORD];
			$sig_port = $row[LANProfileConfigModel::$SIGNALLING_PORT];
			$sig_transport = $row[LANProfileConfigModel::$SIGNALLING_TRANSPORT];
		}
	}

	if(isset($_POST) && isset($_POST['submit_action'])) {
		if($_POST['enable'] == "on")
			$post_enable = "1";
		else
			$post_enable = "";
		if ($_POST['submit_action'] == "2") {	// Add a new rule
			$lan_config->addLANProfile(trim($_POST['name']), $post_enable, 
					trim($_POST['comments']), trim($_POST['user_name']), 
					trim($_POST['passwd']), trim($_POST['sig_port']), 
					trim($_POST['sig_transport']));
			$res = 1;
		}

		if ($_POST['submit_action'] == "1") {	// Edit a rule
			$id = $_POST['id'];
			$row = $lan_config->getLANProfile($id);
			if($row) {
				$name = $row[LANProfileConfigModel::$NAME];
				$enabled = $row[LANProfileConfigModel::$ENABLE];
				$desc = $row[LANProfileConfigModel::$DESCRIPTION];
				$uname = $row[LANProfileConfigModel::$USERNAME];
				$passwd = $row[LANProfileConfigModel::$PASSWORD];
				$sig_port = $row[LANProfileConfigModel::$SIGNALLING_PORT];
				$sig_transport = $row[LANProfileConfigModel::$SIGNALLING_TRANSPORT];

				if ( $desc != trim($_POST['comments']) || 
					$uname != trim($_POST['user_name']) || 
					$enabled != $post_enable || trim($_POST['passwd']) != $passwd ||
					$sig_port != trim($_POST['sig_port']) || 
					$sig_transport != trim($_POST['sig_transport']) ) {
						$lan_config->editLANProfile(trim($_POST['name']),
							$post_enable, trim($_POST['comments']), 
							trim($_POST['user_name']), 
							trim($_POST['passwd']), trim($_POST['sig_port']),
							trim($_POST['sig_transport']));
				}
				$res = 1;
			}
		}
	}
	if ( isset($_POST) && $_POST['ajax_post'] == "1" ) {
		if($_POST['en_dis'] == "true")
			$post_enable_val = "1";
		else if($_POST['en_dis'] == "false")
			$post_enable_val = "";
		$lp_name = str_replace('@',' ',$_POST['lan_id']);
		$rows = $lan_config->getLANProfile($lp_name);
		$db_val = $rows[LANProfileConfigModel::$ENABLE];
		if( $post_enable_val != $db_val ) {
			$lan_config->setLANProfileEnable($lp_name,$post_enable_val);
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
				getPageTips("add_rule");
				setTooltip();
			});

			if(flag == "add") {
				var createdNames = new Array();
				<?php foreach($createdDataNames as $key => $val){ ?>
					createdNames.push('<?php echo $val; ?>');
				<?php } ?>
			}

			function initValidation() {
				$sub("#NAME").attr("data-validation-engine","validate[required,custom[rulename],minSize[5],maxSize[32]]");
				$sub('#ip_type').on('change', function() { {
					var ip_type = $sub("#ip_type option:selected").val();
					if( ip_type == "IP_HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
					if( ip_type == "IP_NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipwithCIDR]]");
					}
					if( ip_type == "IP_RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipRange]]");
					}
					if( ip_type == "MAC_ADDR" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[macaddr]]");
					}
				}
				});
				$sub(document).ready(function() {
					var ip_type = $sub("#ip_type option:selected").val();
					if( ip_type == "IP_HOST" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipv4]]");
					}
					if( ip_type == "IP_NETWORK" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipwithCIDR]]");
					}
					if( ip_type == "IP_RANGE" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[ipRange]]");
					}
					if( ip_type == "MAC_ADDR" ) {
						$sub("#IPADDR").attr("data-validation-engine","validate[required,custom[macaddr]]");
					}
				});
				$sub("#COMMENTS").attr("data-validation-engine","validate[custom[comments],maxSize[64]]");
				$sub("#adddeviceaccess").validationEngine('attach', {promptPosition : "bottomRight"});
			}

			function submitData() {
				if($sub("#adddeviceaccess").validationEngine('validate')) {
					var formObj = document.forms[0];
					if(flag == "add"){
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
					parent.document.getElementById("container").src = "wan_profile.php";
				}
			}
		</script>
	</head>
	<body class="popup" onload="f1('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 270px">
				<form id="adddeviceaccess" name="adddeviceaccess" class="popup-form" action="add_edit_lan_profile.php" target="container" onsubmit="return false" method="POST" autocomplete="off">
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
								<input type="text" class="ipBox" id="NAME" name="name" <?php if($flag == "edit") echo "readonly='readonly'" ?> value="<?php echo $name ?>" size="" maxlength="" />
								<img id="add_rule_001" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td><label for="enable">Enable</label></td>
							<td width="20"></td>
							<td>
								<input type="checkbox" name="enable" id="enable" />
								<img id="setup_3-1_002" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Description</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $desc; ?>" maxlength="32" id="COMMENTS" class="ipBox" name="comments" />
								<img id="add_rule_003" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">WAN Access Port</td>
							<td width="20"></td>
							<td>
								<input type="text" value="<?php print $sig_port; ?>" class="ipBox" name="sig_port" />
								<img id="add_rule_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">WAN Access Transport</td>
							<td width="20"></td>
							<td>
								<select class="ipDrpBox" id="sig_transport" name="sig_transport">
								<?php
								for ( $j = 0; $j < count($transport_str_array); $j++ ) {
									$select_str = "";
									if ( $transport_str_array[$j] == $get_sip_sec_settings[14] )
										$select_str = "selected";
								?>
										<option value="<?php print $transport_str_array[$j];?>" <?php print $select_str;?> ><?php print $transport_str_array[$j];?></option>
								<?php } ?>
								</select>
								<img id="add_rule_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Enable Keepalive</td>
							<td width="20"></td>
							<td>
								<input type="checkbox" name="enable_keepalive" id="ENABLE_KEEPALIVE" />
								<img id="add_rule_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
						<tr>
							<td valign="top">Keepalive Interval</td>
							<td width="20"></td>
							<td>
								<input type="text" class="ipBox" value="" name="keepalive_int" id="KEEPALIVE_INT" />
								<img id="add_rule_004" src="images/icn-info.png" class="ttip" title="" onfocus="if(this.blur)this.blur()" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="submit_action" value="0" />
					<input type="hidden" name="WHITELIST_RULE_LIST" id="WHITELIST_RULE_LIST" value="" />
				</form>
			</div>
			<div class="popup-footer">
				<input type="submit" name="" value="" class="btn-save" onclick="submitData()" onfocus="if(this.blur)this.blur()" />
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
	<script type="text/javascript">
		setIPType();
		initValidation();
	</script>
</html>
