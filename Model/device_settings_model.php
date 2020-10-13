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
	include_once('db_mgr.php');
	include_once ('sbc_provisioning.php');
	include_once("shell_command.php");

	class DeviceSettingsModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $set_stmt = null;
		private static $persist_set_stmt = null;

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public static $HOSTNAME = 'Hostname';
		public static $LAN_CONFIG_MODE = 'LanConfigMode';
		public static $LAN_IP_ADDR = 'LanIPAddress';
		public static $LAN_NETMASK = 'LanNetmask';
		public static $WAN_CONFIG_MODE = 'WanConfigMode';
		public static $WAN_IP_ADDR = 'WanIPAddress';
		public static $WAN_NETMASK = 'WanNetmask';
		public static $GATEWAY = 'WanGateway';
		public static $DNSSERVER = 'DnsServer';
		public static $ENABLE_SSH = 'EnableSSH';
		public static $SSH_PORT = 'SSHPort';
		public static $ENABLE_DPI_UPDATE = 'EnableDpiUpdate';
		public static $DPI_UPDATE_URL = 'DpiUpdateUrl';
		public static $DPI_UPDATE_SCHEDULE = 'DpiUpdateSchedule';
		public static $SUBSCRIPTION_KEY = 'SupscriptionKey';
		public static $ENABLE_REMOTE_LOGGING = 'EnableRemoteLogging';
		public static $REMOTE_LOG_SERVER = 'RemoteLogServer';
		public static $WHITERULES_PRECEDES = 'WhiteListRulesPrecedes';
		public static $WEBUI_PORT = 'WebuiPort';
		public static $SESSION_TIMEOUT = 'SessionTimeout';
		public static $ALLOW_ICMP = 'AllowICMP';
		public static $ENABLE_DPI = 'EnableDPI';
		public static $EnableIPSEC= 'EnableIPSEC';
		public static $ENABLE_EMAIL_SERVER = 'EnableEmailNotification';
		public static $EMAIL_SERVER = 'EmailServerAddress';
		public static $EMAIL_SERVER_PORT = 'EmailServerPort';
		public static $EMAIL_ID = 'SenderEmailID';
		public static $RECEIVER_EMAIL_ID = 'ReceiverEmailID';
		public static $AUTHENTICATION = 'Authentication';
		public static $UNAME = 'EmailUsername';
		public static $PASSWORD= 'EmailPassword';
		public static $EMAIL_FREQUENCY = 'EmailFrequency';
		public static $EMAIL_FREQUENCY_TYPE = 'EmailFrequencyType';
		public static $MGMT_VLAN_IP_ADDR = 'MgmtVlanAddress';
		public static $MGMT_VLAN_NETMASK = 'MgmtVlanNetmask';
		public static $TRANSCODING_IP_ADDR = 'TranscodingIP';
		public static $TRANSCODING_NETMASK = 'TranscodingNetmask';
		public static $LAN_INTERFACE = 'LanInterface';
		public static $WAN_INTERFACE = 'WanInterface';
		public static $ENABLE_TRANSCODING = 'EnableTranscoding';
		public static $ENABLE_FIREWALL = 'EnableFirewall';
		public static $ENABLE_EMAIL_SERVER_SIGNAL_LOGS = 'EnableEmailNotificationSignalLogs';
		public static $EMAIL_FREQUENCY_SIGNAL_LOGS = 'EmailFrequencySignalLogs';
		public static $EMAIL_FREQUENCY_TYPE_SIGNAL_LOGS = 'EmailFrequencyTypeSignalLogs';
		public static $ENABLE_EMAIL_SERVER_MEDIA_LOGS = 'EnableEmailNotificationMediaLogs';
		public static $EMAIL_FREQUENCY_MEDIA_LOGS = 'EmailFrequencyMediaLogs';
		public static $EMAIL_FREQUENCY_TYPE_MEDIA_LOGS = 'EmailFrequencyTypeMediaLogs';
		public static $ENABLE_EMAIL_SERVER_LCR_LOGS = 'EnableEmailNotificationLCRLogs';
		public static $EMAIL_FREQUENCY_LCR_LOGS = 'EmailFrequencyLCRLogs';
		public static $EMAIL_FREQUENCY_TYPE_LCR_LOGS = 'EmailFrequencyTypeLCRLogs';
		public static $ENABLE_EMAIL_SERVER_SYSTEM_LOGS = 'EnableEmailNotificationSystemLogs';
		public static $EMAIL_FREQUENCY_SYSTEM_LOGS = 'EmailFrequencySystemLogs';
		public static $EMAIL_FREQUENCY_TYPE_SYSTEM_LOGS = 'EmailFrequencyTypeSystemLogs';
		public static $ENABLE_EMAIL_SERVER_SECURITY_LOGS = 'EnableEmailNotificationSecurityLogs';
		public static $EMAIL_FREQUENCY_SECURITY_LOGS = 'EmailFrequencySecurityLogs';
		public static $EMAIL_FREQUENCY_TYPE_SECURITY_LOGS = 'EmailFrequencyTypeSecurityLogs';
		public static $AUTO_UPGRADE = 'AutoUpgrade';
		public static $AUTO_UPGRADE_URL = 'AutoUpgradeURL';

		public function get($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT VAL FROM 
							DEVICE_SETTINGS WHERE NAME = :name;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			$result = self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
			if ( $result )
				return $result['VAL'];
			else
				return null;
		}

		public function set($name, $val) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$set_stmt) ) {
				self::$set_stmt = $dbh->prepare('UPDATE DEVICE_SETTINGS SET 
							VAL = :val WHERE NAME = :name;');
			}
			self::$set_stmt->reset();
			self::$set_stmt->bindValue(':val', $val);
			self::$set_stmt->bindValue(':name', $name);
			if(($name == DeviceSettingsModel::$ENABLE_DPI_UPDATE) || ($name == DeviceSettingsModel::$SUBSCRIPTION_KEY) || ($name == DeviceSettingsModel::$DPI_UPDATE_SCHEDULE)) {
				$dbhp = DB_Handle_Persist::Connect();
				if ( !isset(self::$persist_set_stmt) ) {
					self::$persist_set_stmt = $dbhp->prepare('UPDATE DEVICE_SETTINGS SET 
								VAL = :val WHERE NAME = :name;');
				}
				self::$persist_set_stmt->reset();
				self::$persist_set_stmt->bindValue(':val', $val);
				self::$persist_set_stmt->bindValue(':name', $name);
				self::$persist_set_stmt->execute();
			}
			return self::$set_stmt->execute();
		}

		public function getIfaceName($ifaceName) {
			$dbh = DB_Handle::Connect();
			$t_type=0;
			if ($ifaceName =="WanInterface") {
				$t_type=1;
			}
			$query = "select a.DEV_ID FROM NETWORK_DEVICE a, ";
			$query .=" INTERFACE_LIST b where b.I_TYPE=$t_type and a.MAC_ID=b.DEV_ID";
			$result = $dbh->query($query);
			$row = $result -> fetchArray(SQLITE3_ASSOC);
			$result = $row['DEV_ID'];
			return $result;
		}

		public function getLANInterfaces() {
			$shell_obj = ShellCommand::getInstance();
			$lanIface = "LanInterface";
			$getlanIface = DeviceSettingsModel::getIfaceName($lanIface);
			$shell_status = $shell_obj->Execute("ifconfig -a | grep $getlanIface | awk '{ print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$lan_interfaces =trim($shell_obj->getLastOutput());
			}
			return $lan_interfaces;
		}

		public function getMediaInterfaces() {
			$shell_obj = ShellCommand::getInstance();
			$lanIface = "LanInterface";
			$transIface = SysCalls::getTranscodingInterface();
			$getlanIface = DeviceSettingsModel::getIfaceName($lanIface);
			$enableStatus = DeviceSettingsModel::getEnableTranscodingStatus();
			$transcodingStatus = SysCalls::getTranscodingStatus();
			$shell_status = $shell_obj->Execute("ifconfig -a | grep $getlanIface | awk '{ print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$ret_interfaces1 =trim($shell_obj->getLastOutput());
			}
			$ret_interfaces = explode(" ", $ret_interfaces1);
			foreach ( $ret_interfaces as $vlan ) {
				list($vlan_arr[]) = explode(".", $vlan);
			}
			$vlan_ifaces = array();
			if ( $vlan_arr != null ) {
				$vlan_ifaces = array_unique($vlan_arr);
			}
			foreach ( $vlan_ifaces as $vip ) {
				list($vip_arr[]) = explode(":", $vip);
			}
			$real_interfaces = array();
			if ( $vip_arr != null ) {
				$real_interfaces = array_unique($vip_arr);
			}
			$getmediaInt = array_diff($vlan_ifaces,$real_interfaces);
			$mediaInterfaces = array_values($getmediaInt);
			if ( $transIface != NULL && ( $enableStatus == "1" && $transcodingStatus == "1" ) ) {
				array_push($mediaInterfaces,$transIface);
			}
			return $mediaInterfaces;
		}

		public function getWANInterfaces() {
			$shell_obj = ShellCommand::getInstance();
			$wanIface = "WanInterface";
			$getwanIface = DeviceSettingsModel::getIfaceName($wanIface);
			$shell_status = $shell_obj->Execute("ifconfig -a | grep $getwanIface | awk '{ print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$wan_interfaces =trim($shell_obj->getLastOutput());
			}
			return $wan_interfaces;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getIfaceType($iface) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT NAME FROM DEVICE_SETTINGS WHERE VAL = '$iface' ");
			return $result;
		}

		public function getEnableTranscodingStatus() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT VAL FROM DEVICE_SETTINGS WHERE NAME = 'EnableTranscoding' ");
			return $result;
		}
	}
?>
