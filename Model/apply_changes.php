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
	include_once('Model/sbc_provisioning.php');
	include_once('db_mgr.php');
	include_once("shell_command.php");
	$ERR_LOG="/tmp/deploy.log";

	function __autoload($class_name) {
		require_once $class_name . '.php';
	}

	class apply_changes {
		private $RETURN_ARRAY=array();
		private static $instance = null;
		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function __construct() {
		}

		public function getReturnArray() {
			return implode(",", $this->RETURN_ARRAY);
		}

		public function php_provision_status($id,$mod_name,$status){
			$count=count($this->RETURN_ARRAY);
			if($count != 0)
				$this->RETURN_ARRAY[$count]="|";
			else
				$count=-1;
			$this->RETURN_ARRAY[$count+1]=$mod_name;
			$this->RETURN_ARRAY[$count+2]=$status;
			$this->RETURN_ARRAY[$count+3]=$status;
		}

		public function update_status($id,$mod_name) {
			$shell_obj = ShellCommand::getInstance();
			$dep_log="/tmp/deploy.log";
			$statOutFile = "/tmp/.bloxconfig.$id.ret";
			$ret = $shell_obj->Execute("/usr/bin/execute_bg.sh $statOutFile bloxconfig $id", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if($ret == 0)
				$status= "SUCCESS";
			else
				$status= "FAILURE";
			if($id=="0") {
				$content="YES" ;
			} else if(file_exists($dep_log)) {
				$content = file_get_contents($dep_log);
			}
			$count=count($this->RETURN_ARRAY);
			if($count != 0)
				$this->RETURN_ARRAY[$count]="|";
			else
				$count=-1;
			$this->RETURN_ARRAY[$count+1]=$mod_name;
			$this->RETURN_ARRAY[$count+2]=$status;
			$this->RETURN_ARRAY[$count+3]=$content;
		}

		public function doAction() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->getUniqueActionListName();
			$r_val = true;
			$report=array();
			while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
				switch ( $row['T_NAME'] ) {
					case DEPLOY_MODULES::ALL:
						$id=$row['T_NAME'];
						$mod_name="Config Restore";
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::HOST_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::HOST_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::NET_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::NET_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SSH_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SSH_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::WEBUI_CONFIG:
						//$id=$row['T_NAME'];
						//$mod_name=DEPLOY_MODULES::WEBUI_CONFIG_STR;
						//self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::DEVICE_ACCESS_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::DEVICE_ACCESS_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::DEVICE_ACCESS_FILTER_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SIP_SECURITY_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SIP_SECURITY_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::FIREWALL_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::FIREWALL_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::DPI_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::DPI_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::WHITELIST_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::WHITELIST_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::BLACKLIST_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::BLACKLIST_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::GEOIP_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::GEOIP_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SIGUPDATE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SIGUPDATE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::LOGGING_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::LOGGING_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::WHITE_LIST_RULES_PRECEDENCE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::WHITE_LIST_RULES_PRECEDENCE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::TIME_SETTINGS_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::TIME_SETTINGS_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::EMAIL_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::EMAIL_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::MGMT_VLAN_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::MGMT_VLAN_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::GLOBAL_FW_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::GLOBAL_FW_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SIP_PROFILE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SIP_PROFILE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SIP_TRUNK_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SIP_TRUNK_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::LCR_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::LCR_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::ROAMING_USERS_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::ROAMING_USERS_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::MEDIA_PROFILE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::MEDIA_PROFILE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SBC_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SBC_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::VIP_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::VIP_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::VLAN_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::VLAN_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::ROUTES_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::ROUTES_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::IPSEC_VPN_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::IPSEC_VPN_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::IPSEC_ENABLED_FLAG_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::IPSEC_ENABLED_FLAG_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::PORT_FORWARDING_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::PORT_FORWARDING_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::TRANSCODING_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::TRANSCODING_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::FAX_PROFILE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::FAX_PROFILE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::PLUGIN_HUMBUG_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::PLUGIN_HUMBUG_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SUBSCRIBER_SETTINGS_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					
					case DEPLOY_MODULES::EVENTS_SETTINGS_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::EVENTS_SETTINGS_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::GENERAL_SETTING_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::GENERAL_SETTING_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SIPHEADERS_PROFILE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					case DEPLOY_MODULES::SIPDOMAIN_PROFILE_CONFIG:
						$id=$row['T_NAME'];
						$mod_name=DEPLOY_MODULES::SIPDOMAIN_PROFILE_CONFIG_STR;
						self::update_status($id,$mod_name);
						break;
					// New Feature added begin
					/*
					case DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG:
						$id=$row['T_NAME'];
						$c_Name = "Config_INTERFACE_LIST";
						$c_obj = new $c_Name();
						$r_val = $c_obj -> config_update($dbh,$id);
						
						$mod_name=DEPLOY_MODULES::DEVICELIST_PROFILE_CONFIG_STR;
						$status= "FAILURE";
						if($r_val == true) {
							$status= "SUCCESS";
						}

						$this->php_provision_status($id,$mod_name,$status);
						
						break;
					case DEPLOY_MODULES::GENERAL_SETTING_CONFIG:
						$id=$row['T_NAME'];
						$c_Name = "Config_GENERAL_SETTINGS";
						$c_obj = new $c_Name();
						$r_val = $c_obj -> config_update($dbh,$id);
						
						$mod_name=DEPLOY_MODULES::GENERAL_SETTING_CONFIG_STR;
						$status= "FAILURE";
						if($r_val == true) {
							$status= "SUCCESS";
						}

						$this->php_provision_status($id,$mod_name,$status);

						break;
					*/
					// New Feature addded end
					default:
						break;
				}
			}
			DB_Handle::deleteActionList();
			DB_Handle::persistDB();
			return $this->getReturnArray();
		}
	}
	if( isset($_POST['ignore_config'])) {
		$dbh = DB_Handle::Connect();
		DB_Handle::ignoreConfigChanges();
		$dbh->deleteActionList();
	} else {
		$ac = apply_changes::getInstance();
		$r = $ac->doAction();
		echo $r;
	}
?>
