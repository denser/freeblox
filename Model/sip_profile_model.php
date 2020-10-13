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
	include_once('db_mgr.php');
	include_once ('sbc_provisioning.php');
	include_once('sbc_constants.php');
	include_once('device_settings_model.php');
	include_once("shell_command.php");
	include_once("devicelist_settings_model.php");

	class SIPProfileConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $DESCRIPTION = 'DESCRIPTION';
		public static $INTERFACE = 'INTERFACE';
		public static $ACCESS_PROTOCOL = 'ACCESS_PROTOCOL';
		public static $ACCESS_PORT = 'ACCESS_PORT';
		public static $ENABLE_KEEPALIVE = 'ENABLE_KEEPALIVE';
		public static $KEEPALIVE_INTERVAL = 'KEEPALIVE_INTERVAL';
		public static $SIP_TOS = 'SIP_TOS';
		public static $ALLOWED_IP_PORTS = 'ALLOWED_IP_PORTS';
		public static $DOMAIN = 'DOMAIN';
		public static $SERVER_CERTS = 'SERVER_CERTS';
		public static $NAT_SETTINGS = 'NAT_SETTINGS';
		public static $IP_ADDRESS = 'IP_ADDRESS';
		public static $REQ_TLS = 'REQ_TLS';
		public static $VER_TLS = 'VER_TLS';
		public static $SIPHEADERS = 'SIPHEADERS';
		public static $SIPDOMAINS = 'SIPDOMAINS';
		public static $NAT_ENABLE = 'NAT_ENABLE';
		static $nat_settings_array = array("NO NAT","STUN","STATIC");
		private $SHM_RULE = array();

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getSIPProfiles() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, DESCRIPTION, INTERFACE, 
							ACCESS_PROTOCOL, ACCESS_PORT, ENABLE_KEEPALIVE, 
							KEEPALIVE_INTERVAL, SIP_TOS, ALLOWED_IP_PORTS, 
							SERVER_CERTS,DOMAIN, NAT_SETTINGS, IP_ADDRESS, 
							SIPDOMAINS,NAT_ENABLE FROM SIP_PROFILES');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getInterfaceName($internameval) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select a.I_TYPE, b.DEV_ID from INTERFACE_LIST as a, 
					NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND 
					a.INTER_NAME = '$internameval'");
			$row1 = $result->fetchArray(SQLITE3_ASSOC);
			$resultval = $row1['DEV_ID'];
			return $resultval;
		}

		public function getInterfaceType($Iface) {
			$devicelist_config = DevicelistSettingsModel::getInstance();
			$interfacename = $Iface;
			error_log("$interfacename: save\n");
			$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
			$sipinterface_interface = explode("|",$ret_interface_type);
			$inter_type = $sipinterface_interface[1];
			if($sipinterface_interface[0] == "") {
				$virtualip = $devicelist_config->getVirtualInterface($interfacename);
				if($virtualip != "") {
					$interfacename = $virtualip;
					$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
					$sipinterface_interface = explode("|",$ret_interface_type);
					$inter_type = $sipinterface_interface[1];
				}
			}
			return $inter_type ;
		}

		public function getAllsipheaders() {
			$dbh = DB_Handle::Connect();
			$qry = "SELECT NAME FROM SHM_RULES";
			$result =  $dbh->query ($qry);
			return $result;	
		}

		private function assgin_shmrule() {
			$dbh = DB_Handle::Connect();
			$query = "SELECT * FROM SHM_RULES ORDER BY ID";
			$result =  $dbh->query ($query);
			$index=1;
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$this->SHM_RULE[$index] = $row['NAME'];
				$index++;
			}
		}

		private function getshmIndex($val) {
			$R_val = -1;
			$S_ID=500;
			$key = array_search($val, $this->SHM_RULE);
			if ($key != false ) {
				$R_val = $S_ID+$key;
			}
			return  $R_val;
		}

		public function getSIPHrsId($sip_hdr_list) {
			$this->assgin_shmrule();
			$d_array = explode(",",$sip_hdr_list);
			$r_value="";
			for ($i=0; $i < count($d_array);$i++) {
				if ($i != 0) {
					$r_value .=":";
				}
				$in_id = $this->getshmIndex($d_array[$i]);
				$r_value .=$in_id;
			}
			return $r_value;
		}

		public function getSIPProfile($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$query = " SELECT NAME, DESCRIPTION, INTERFACE, ACCESS_PROTOCOL,";
				$query .= "	ACCESS_PORT, ENABLE_KEEPALIVE,KEEPALIVE_INTERVAL,";
				$query .= " SIP_TOS, ALLOWED_IP_PORTS, DOMAIN, SERVER_CERTS,";
				$query .= " NAT_SETTINGS, IP_ADDRESS, REQ_TLS,VER_TLS,SIPHEADERS,";
				$query .= "SIPDOMAINS, NAT_ENABLE FROM SIP_PROFILES WHERE NAME = :name";
				self::$get_stmt = $dbh->prepare($query);
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle( "SELECT count(*) FROM SIP_PROFILES 
						 WHERE NAME = '$name'");
		}

		public function getsipdomain() {
			$sipdomainlist = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select SIP_DOMAIN from SIP_DOMAIN_PROFILES");
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($sipdomainlist, $row["SIP_DOMAIN"]);
			}
			return $sipdomainlist;
		}

		public function addSIPProfile ( $name, $desc, $interface, $access_protocol, $access_port, $enable_keepalive, $keepalive_int, $sip_tos, $allowed_ip_ports, $domain, $server_certs, $nat_settings, $ip_address,$req_tls,$ver_tls, $sipheaders,$sipdomains, $nat_enable ) {
			$dbh = DB_Handle::Connect();
			$inter_type = $this->getInterfaceType($interface);

			if ( self::checkIfRuleExists($name) != 0 ) {
				return FALSE;
			}
			$sipheaders_id = $this->getSIPHrsId($sipheaders);
			if ( !isset(self::$add_stmt) ) {
				$query = "INSERT INTO SIP_PROFILES ( NAME, DESCRIPTION,";
				$query .= "INTERFACE, ACCESS_PROTOCOL,ACCESS_PORT,"; 
				$query .= "ENABLE_KEEPALIVE,KEEPALIVE_INTERVAL, SIP_TOS,";
				$query .= "ALLOWED_IP_PORTS, DOMAIN, SERVER_CERTS, NAT_SETTINGS,";
				$query .= "IP_ADDRESS, REQ_TLS, VER_TLS,";
				$query .="INTER_TYPE,SIPHEADERS,SIPHEADERS_ID, SIPDOMAINS,NAT_ENABLE )";
				$query .= "VALUES (:name, :desc, :interface, :access_protocol,";
				$query .= ":access_port, :enable_keepalive, :keepalive_int,"; 
				$query .= ":sip_tos, :allowed_ip_ports, :domain, :server_certs,";
				$query .= ":nat_settings, :ip_address, :req_tls, :ver_tls ,";
				$query .= ":inter_type, :sipheaders, :sipheadersid,";
				$query .= " :sipdomains, :nat_enable)";
				self::$add_stmt =$dbh->prepare($query);
			}
			self::$add_stmt->reset();
			self::checkIfRuleExists($name);
			self::$add_stmt->bindValue(':name', $name);
			self::$add_stmt->bindValue(':desc', $desc);
			self::$add_stmt->bindValue(':interface', $interface);
			self::$add_stmt->bindValue(':access_protocol', $access_protocol);
			self::$add_stmt->bindValue(':access_port', $access_port);
			self::$add_stmt->bindValue(':enable_keepalive', $enable_keepalive);
			self::$add_stmt->bindValue(':keepalive_int', $keepalive_int);
			self::$add_stmt->bindValue(':sip_tos', $sip_tos);
			self::$add_stmt->bindValue(':allowed_ip_ports', $allowed_ip_ports);
			self::$add_stmt->bindValue(':domain', $domain);
			self::$add_stmt->bindValue(':server_certs', $server_certs);
			self::$add_stmt->bindValue(':nat_settings', $nat_settings);
			self::$add_stmt->bindValue(':ip_address', $ip_address);
			self::$add_stmt->bindValue(':req_tls', $req_tls);
			self::$add_stmt->bindValue(':ver_tls', $ver_tls);
			self::$add_stmt->bindValue(':inter_type', $inter_type);
			self::$add_stmt->bindValue(':sipheaders', $sipheaders);
			self::$add_stmt->bindValue(':sipheadersid', $sipheaders_id);
			self::$add_stmt->bindValue(':sipdomains', $sipdomains);
			self::$add_stmt->bindValue(':nat_enable', $nat_enable);
			return self::$add_stmt->execute();
		}

		public function editSIPProfile ( $name, $desc, $interface, $access_protocol, 
			$access_port, $enable_keepalive, $keepalive_int, $sip_tos, $allowed_ip_ports, 
			$domain, $server_certs, $nat_settings, $ip_address,$req_tls,$ver_tls, 
			$sipheaders, $sipdomains, $nat_enable ) {
				$inter_type = $this->getInterfaceType($interface);
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 1 ) {
					return FALSE;
				}
				$sipheaders_id = $this->getSIPHrsId($sipheaders);
				if ( !isset(self::$edit_stmt) ) {
					$query ="UPDATE SIP_PROFILES SET DESCRIPTION = :desc, 
					INTERFACE = :interface, ACCESS_PROTOCOL = :access_protocol, 
					ACCESS_PORT = :access_port, ENABLE_KEEPALIVE  
					= :enable_keepalive ,KEEPALIVE_INTERVAL = :keepalive_int, 
					SIP_TOS = :sip_tos, ALLOWED_IP_PORTS = :allowed_ip_ports, 
					DOMAIN =:domain, SERVER_CERTS =:server_certs, 
					NAT_SETTINGS = :nat_settings, IP_ADDRESS = :ip_address, 
					REQ_TLS = :req_tls, VER_TLS = :ver_tls,INTER_TYPE= 
					:inter_type,SIPHEADERS =:sipheaders,SIPHEADERS_ID 
					=:sipheadersid, SIPDOMAINS = :sipdomains, 
					NAT_ENABLE =:nat_enable WHERE NAME = :name ";
					self::$edit_stmt =$dbh->prepare($query);
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':name', $name);
				self::$edit_stmt->bindValue(':desc', $desc);
				self::$edit_stmt->bindValue(':interface', $interface);
				self::$edit_stmt->bindValue(':access_protocol', $access_protocol);
				self::$edit_stmt->bindValue(':access_port', $access_port);
				self::$edit_stmt->bindValue(':enable_keepalive', $enable_keepalive);
				self::$edit_stmt->bindValue(':keepalive_int', $keepalive_int);
				self::$edit_stmt->bindValue(':sip_tos', $sip_tos);
				self::$edit_stmt->bindValue(':allowed_ip_ports', $allowed_ip_ports);
				self::$edit_stmt->bindValue(':domain', $domain);
				self::$edit_stmt->bindValue(':server_certs', $server_certs);
				self::$edit_stmt->bindValue(':nat_settings', $nat_settings);
				self::$edit_stmt->bindValue(':ip_address', $ip_address);
				self::$edit_stmt->bindValue(':req_tls', $req_tls);
				self::$edit_stmt->bindValue(':ver_tls', $ver_tls);
				self::$edit_stmt->bindValue(':inter_type', $inter_type);
				self::$edit_stmt->bindValue(':sipheaders', $sipheaders);
				self::$edit_stmt->bindValue(':sipheadersid', $sipheaders_id);
				self::$edit_stmt->bindValue(':sipdomains', $sipdomains);
				self::$edit_stmt->bindValue(':nat_enable', $nat_enable);
				return self::$edit_stmt->execute();
		}

		public function rmSipDomainRoaming($sipdomain,$sipprofilename) {
			$dbh = DB_Handle::Connect();
			$query = "DELETE FROM SIP_DOMAIN_MAP WHERE SIP_DOMAIN = '$sipdomain'";
			$query .= " AND SIP_PROFILE_NAME = '$sipprofilename'"; 
			$dbh->query($query);
		}

		public function deleteSIPProfile($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM SIP_PROFILES 
							WHERE NAME = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			return self::$del_stmt->execute();
		}

		public function getSIPProfilesName() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM SIP_PROFILES");
			return $result;
		}

		public function getIfaceName($ifaceName) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT VAL FROM DEVICE_SETTINGS 
					WHERE NAME = '$ifaceName' ");
			return $result;
		}

		public function getSIPInternalProfiles() {	// sip lan profiles
			$lanIface = "LanInterface";
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM SIP_PROFILES WHERE INTER_TYPE='LAN'");
			$i=0;
			while($row= $result->fetchArray()) {
				$sipLanarr[$i] = $row[0];
				$i++;
			}
			return $sipLanarr;
		}

		public function getSIPExternalProfiles() {	// sip wan profiles
			$sipWanArr = array();
			$wanIface = "WanInterface";
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM SIP_PROFILES WHERE INTER_TYPE='WAN'");
			$i = 0;
			while($row = $result->fetchArray()) {
				$sipWanArr[$i] = $row[0];
				$i++;
			}
			return $sipWanArr;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getSIPWanProfiles() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM SIP_PROFILES 
				WHERE SIP_PROFILES.INTERFACE LIKE '%eth0%' 
				AND SIP_PROFILES.NAME NOT IN( SELECT 
				ROAMING_USERS.WAN_SIP_PROFILE FROM ROAMING_USERS)");
			return $res;
		}

		public function getRoamingUserLanProfiles() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM SIP_PROFILES WHERE 
				SIP_PROFILES.INTERFACE LIKE '%eth1%' AND SIP_PROFILES.NAME 
				NOT IN( SELECT ROAMING_USERS.LAN_SIP_PROFILE FROM ROAMING_USERS)");
			return $res;
		}

		public function getRoamingUserWanProfiles() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM SIP_PROFILES 
				WHERE SIP_PROFILES.INTERFACE LIKE '%eth0%' AND SIP_PROFILES.NAME 
				NOT IN( SELECT ROAMING_USERS.WAN_SIP_PROFILE FROM ROAMING_USERS)");
			return $res;
		}

		public function getSIPLanProfiles() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM SIP_PROFILES WHERE 
				SIP_PROFILES.INTERFACE LIKE '%eth1%' AND SIP_PROFILES.NAME 
				NOT IN( SELECT LCR_CONFIG.SIP_PROFILE FROM LCR_CONFIG)");
			return $res;
		}

		public function deleteSipProfiles($row_name) {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("DELETE FROM SIP_PROFILES where NAME = '$row_name'");
			return $res;
		}

		public function checktrunk($row_name) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from TRUNKS_CONFIGURATION ";
			$query .= "where INTERNAL_SIP_PROFILE = '$row_name' ";
			$query .= "OR EXTERNAL_SIP_PROFILE ='$row_name'";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function checkroaminguser($row_name) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from ROAMING_USERS ";
			$query .= "where WAN_SIP_PROFILE = '$row_name' ";
			$query .= "OR LAN_SIP_PROFILE = '$row_name'";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function checklcr($row_name) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from LCR_CONFIG where SIP_PROFILE = '$row_name'";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function deleteSipLanProfiles($row_name) {
			$dbh = DB_Handle::Connect();
			$ifaceName = "LanInterface";
			$lanIface = DeviceSettingsModel::getIfaceName($ifaceName);
			$res = $dbh->query("DELETE FROM SIP_PROFILES where NAME = '$row_name' 
				AND INTERFACE = '$lanIface'");
			return $res;
		}

		public function deleteSipWanProfiles($row_name) {
			$dbh = DB_Handle::Connect();
			$ifaceName = "WanInterface";
			$wanIface = DeviceSettingsModel::getIfaceName($ifaceName);
			$res = $dbh->query("DELETE FROM SIP_PROFILES where 
					NAME = '$row_name' AND INTERFACE = '$wanIface'");
			return $res;
		}

		public function getSIPTrunkLanProfiles() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM SIP_PROFILES WHERE SIP_PROFILES.INTERFACE 
				LIKE '%eth1' AND SIP_PROFILES.NAME NOT IN (SELECT INTERNAL_SIP_PROFILE 
				FROM TRUNKS_CONFIGURATION)");
			return $res;
		}

		public function getSIPTrunkWanProfiles() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM SIP_PROFILES WHERE SIP_PROFILES.INTERFACE 
				LIKE '%eth0' AND SIP_PROFILES.NAME NOT IN (SELECT 
				EXTERNAL_SIP_PROFILE FROM TRUNKS_CONFIGURATION)");
			return $res;
		}

		public function getSipProfileInterfaceAddress($sip_profile) {
			$shell_obj = ShellCommand::getInstance();
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT INTERFACE FROM SIP_PROFILES 
				WHERE NAME ='$sip_profile'");
			while ( $row = $res->fetchArray()) {
				$iface_name = $row[0];
			}
			return $iface_name;
		}

		public function getSipProfileInterfacePort($sip_profile) {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT ACCESS_PORT FROM SIP_PROFILES 
				WHERE NAME = '$sip_profile'");
			while ( $row = $res->fetchArray()) {
				return $row[0];
			}
		}

		public function getTrunkCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT COUNT(*) FROM TRUNKS_CONFIGURATION");
			return $result;
		}

		public function getRoamingUsersCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT COUNT(*) FROM ROAMING_USERS");
			return $result;
		}

		public function getLCRCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT COUNT(*) FROM LCR_CONFIG");
			return $result;
		}

		public function getTrunkSipLanProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT INTERNAL_SIP_PROFILE FROM TRUNKS_CONFIGURATION");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$sipLanArr[$i] = $row[0];
				$i++;
			}
			return $sipLanArr;
		}

		public function getLcrProfiles() {	// sip lan profiles
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT SIP_PROFILE FROM LCR_CONFIG");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$lcrArr[$i] = $row[0];
				$i++;
			}
			return $lcrArr;
		}

		public function getTrunkSipWanProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT EXTERNAL_SIP_PROFILE FROM TRUNKS_CONFIGURATION");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$sipWanArr[$i] = $row[0];
				$i++;
			}
			return $sipWanArr;
		}

		public function getRoamingWanProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT WAN_SIP_PROFILE FROM ROAMING_USERS");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$roamWanArr[$i] = $row[0];
				$i++;
			}
			return $roamWanArr;
		}

		public function getRoamingLanProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT LAN_SIP_PROFILE FROM ROAMING_USERS");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$roamLanArr[$i] = $row[0];
				$i++;
			}
			return $roamLanArr;
		}

		public function sipLanProfiles() {
			$trunkCount = SIPProfileConfigModel::getTrunkCount();
			$roamingUsersCount = SIPProfileConfigModel::getRoamingUsersCount();
			$lcrCount = SIPProfileConfigModel::getLCRCount();
			if ( $trunkCount < 1 && ($roamingUsersCount < 1 && $lcrCount < 1 )) {
				$sipLanProfiles = SIPProfileConfigModel::getSIPInternalProfiles();
				return $sipLanProfiles;
			}

			if ( $trunkCount > 0 && ($roamingUsersCount < 1 && $lcrCount < 1 ) ) {
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$sipLanArray = SIPProfileConfigModel::getSIPInternalProfiles();
				$sipLanProfiles = array_diff($sipLanArray,$trunkLanProfilesArr);
			}

			if ( $roamingUsersCount > 0 && ($trunkCount < 1 && $lcrCount < 1 ) ) {
				$sipLanArray = SIPProfileConfigModel::getSIPInternalProfiles();
				$roamLanArr = SIPProfileConfigModel::getRoamingLanProfiles();
				$sipLanProfiles = array_diff($sipLanArray,$roamLanArr);
			}

			if ( $lcrCount > 0 && ( $trunkCount < 1 && $roamingUsersCount  < 1 )) {
				$sipLanArray = SIPProfileConfigModel::getSIPInternalProfiles();
				$lcrArr = SIPProfileConfigModel::getLcrProfiles();
				$sipLanProfiles = array_diff($sipLanArray,$lcrArr);
			}

			if ( $trunkCount > 0 && ( $lcrCount > 0 && $roamingUsersCount < 1 ) ) {
				$sipLanArray = SIPProfileConfigModel::getSIPInternalProfiles();
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$lcrArr = SIPProfileConfigModel::getLcrProfiles();
				$lcr_trunk_arr = array_merge($lcrArr,$trunkLanProfilesArr);
				$sipLanProfiles = array_diff($sipLanArray,$lcr_trunk_arr);
			}

			if ( $trunkCount > 0 && ( $roamingUsersCount > 0 && $lcrCount < 1 ) ) {
				$sipLanArray = SIPProfileConfigModel::getSIPInternalProfiles();
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$lcrArr = SIPProfileConfigModel::getLcrProfiles();
				$roamLanArr = SIPProfileConfigModel::getRoamingLanProfiles();
				$roam_trunk_arr = array_merge($roamLanArr,$trunkLanProfilesArr);
				$sipLanProfiles = array_diff($sipLanArray,$roam_trunk_arr);
			}

			if ( $lcrCount > 0 && ( $roamingUsersCount > 0 && $trunkCount > 0 ) ) {
				$sipLanArray = SIPProfileConfigModel::getSIPInternalProfiles();
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$lcrArr = SIPProfileConfigModel::getLcrProfiles();
				$roamLanArr = SIPProfileConfigModel::getRoamingLanProfiles();
				$trunk_lcr = array_merge($trunkLanProfilesArr,$roamLanArr);
				$trunk_lcr_roam_arr = array_merge($trunk_lcr,$lcrArr);
				$sipLanProfiles = array_diff($sipLanArray,$trunk_lcr_roam_arr);
			}
			return $sipLanProfiles;
		}

		public function sipWanProfiles() {
/*
			$trunkCount = SIPProfileConfigModel::getTrunkCount();
			$roamingUsersCount = SIPProfileConfigModel::getRoamingUsersCount();
			$lcrCount = SIPProfileConfigModel::getLCRCount();

			if ( $trunkCount < 1 && $roamingUsersCount < 1) {
				$sipWanProfiles = SIPProfileConfigModel::getSIPExternalProfiles();
				return $sipWanProfiles;
			}

			if ($trunkCount > 0 && $roamingUsersCount < 1 ) {
				$trunkWanProfiles = SIPProfileConfigModel::getTrunkSipWanProfiles();
				$sipWanArray = SIPProfileConfigModel::getSIPExternalProfiles();
				$sipWanProfiles = array_diff($sipWanArray,$trunkWanProfiles);
			}

			if ($trunkCount > 0 && $roamingUsersCount > 0) {
				$trunkWanProfiles = SIPProfileConfigModel::getTrunkSipWanProfiles();
				$sipWanArray = SIPProfileConfigModel::getSIPExternalProfiles();
				$roamingWanProfiles = SIPProfileConfigModel::getRoamingWanProfiles();
				$trunk_roam = array_merge($roamingWanProfiles,$trunkWanProfiles);
				$sipWanProfiles = array_diff($sipWanArray,$trunk_roam);
			}

			if ( $roamingUsersCount > 0 && $trunkCount < 1 ) {
				$sipWanArray = SIPProfileConfigModel::getSIPExternalProfiles();
				$roamingWanProfiles = SIPProfileConfigModel::getRoamingWanProfiles();
				$sipWanProfiles = array_diff($sipWanArray,$roamingWanProfiles);
			}
			return $sipWanProfiles; 
*/
		}

		public function sipWanDelProfiles() {
/*
			$trunkCount = SIPProfileConfigModel::getTrunkCount();
			$roamingUsersCount = SIPProfileConfigModel::getRoamingUsersCount();
			$lcrCount = SIPProfileConfigModel::getLCRCount();

			if ($trunkCount < 1 && $roamingUsersCount < 1 ) {
				$sipWanProfiles = SIPProfileConfigModel::getSIPExternalProfiles();
			}

			if ($trunkCount > 0 && $roamingUsersCount < 1 ) {
				$trunkWanProfiles = SIPProfileConfigModel::getTrunkSipWanProfiles();
				$sipWanArray = SIPProfileConfigModel::getSIPExternalProfiles();
				$sipWanProfiles = array_diff($sipWanArray,$trunkWanProfiles);
			}

			if ($trunkCount > 0 && $roamingUsersCount > 0) {
				$trunkWanProfiles = SIPProfileConfigModel::getTrunkSipWanProfiles();
				$sipWanArray = SIPProfileConfigModel::getSIPExternalProfiles();
				$roamingWanProfiles = SIPProfileConfigModel::getRoamingWanProfiles();
				$trunk_roam = array_merge($roamingWanProfiles,$trunkWanProfiles);
				$sipWanProfiles = array_diff($sipWanArray,$trunk_roam);
			}

			if ( $roamingUsersCount > 0 && $trunkCount < 1) {
				$sipWanArray = SIPProfileConfigModel::getSIPExternalProfiles();
				$roamingWanProfiles = SIPProfileConfigModel::getRoamingWanProfiles();
				$sipWanProfiles = array_diff($sipWanArray,$roamingWanProfiles);
			}
			return $sipWanProfiles; 
*/
		}

		public function sipLanDelProfiles() {
			$trunkCount = SIPProfileConfigModel::getTrunkCount();
			$roamingUsersCount = SIPProfileConfigModel::getRoamingUsersCount();
			$lcrCount = SIPProfileConfigModel::getLCRCount();

			if ( $trunkCount < 1 && ($roamingUsersCount < 1 && $lcrCount < 1 ) ) {
				$sipLanProfiles = SIPProfileConfigModel::getSIPInternalProfiles();
			}

			if ( $trunkCount > 0 && ($roamingUsersCount < 1 && $lcrCount < 1 ) ) {
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$sipLanProfilesArr = SIPProfileConfigModel::getSIPInternalProfiles();
				$sipLanProfiles = array_diff($sipLanProfilesArr,$trunkLanProfilesArr);
			}

			if ( $roamingUsersCount > 0 && ($trunkCount < 1 && $lcrCount < 1 ) ) {
				$sipLanProfilesArr = SIPProfileConfigModel::getSIPInternalProfiles();
				$roamLanArr = SIPProfileConfigModel::getRoamingLanProfiles();
				$sipLanProfiles = array_diff($sipLanProfilesArr,$roamLanArr);
			}

			if ( $trunkCount > 0 && ( $lcrCount > 0 && $roamingUsersCount < 1 ) ) {
				$sipLanProfilesArr = SIPProfileConfigModel::getSIPInternalProfiles();
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$lcrArr = SIPProfileConfigModel::getLcrProfiles();
				$lcr_trunk_arr = array_merge($lcrArr,$trunkLanProfilesArr);
				$sipLanProfiles = array_diff($sipLanProfilesArr,$lcr_trunk_arr);
			}

			if ( $trunkCount > 0 && ( $roamingUsersCount > 0 && $lcrCount < 1 ) ) {
				$sipLanProfilesArr = SIPProfileConfigModel::getSIPInternalProfiles();
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$lcrArr = SIPProfileConfigModel::getLcrProfiles();
				$roamLanArr = SIPProfileConfigModel::getRoamingLanProfiles();
				$roam_trunk_arr = array_merge($roamLanArr,$trunkLanProfilesArr);
				$sipLanProfiles = array_diff($sipLanProfilesArr,$roam_trunk_arr);
			}

			if ( $lcrCount > 0 && ( $roamingUsersCount > 0 && $trunkCount > 0 ) ) {
				$sipLanProfilesArr = SIPProfileConfigModel::getSIPInternalProfiles();
				$trunkLanProfilesArr = SIPProfileConfigModel::getTrunkSipLanProfiles();
				$lcrArr = SIPProfileConfigModel::getLcrProfiles();
				$roamLanArr = SIPProfileConfigModel::getRoamingLanProfiles();
				$trunk_lcr = array_merge($trunkLanProfilesArr,$roamLanArr);
				$trunk_lcr_roam_arr = array_merge($trunk_lcr,$lcrArr);
				$sipLanProfiles = array_diff($sipLanProfilesArr,$trunk_lcr_roam_arr);
			}
			return $sipLanProfiles;
		}

		public function getSipInterfaceAccessPorts() {
			$accessport = array();
			$dbh = DB_Handle::Connect();
			$query = "SELECT INTERFACE,ACCESS_PORT FROM SIP_PROFILES";
			$res = $dbh->query($query);
			while ( $row = $res->fetchArray()) {
				$str = $row['INTERFACE']."-".$row['ACCESS_PORT'];
				array_push($accessport,$str);
			}
			return $accessport;
		}

		public function getSipProfileInterfaceProto($sip_profile) {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT ACCESS_PROTOCOL FROM SIP_PROFILES 
				WHERE NAME = '$sip_profile'");
			while ( $row = $res->fetchArray()) {
				return $row[0];
			}
		}

		public function getServerCertsList() {
			$sipServerCerts = array();
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT SERVER_CERTS FROM SIP_PROFILES 
				WHERE SERVER_CERTS != 'NULL'");
			$i = 0;
			while ( $row = $res->fetchArray()) {
				$sipServerCerts[$i] = $row[0];
				$i++;
			}
			return $sipServerCerts;
		}

		public function getNatSettings() {
			return SIPProfileConfigModel::$nat_settings_array;
		}

		public function getNames() {
			$names = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM SIP_PROFILES");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$names[$i] = $row[0];
				$i++;
			}
			$count = count($names);
			if ( $count > 0) {
				return $names;
			} else {
				$names = array();
				return $names;
			}
		}

		public function checkinterfacenames($devid) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select count(*) from SIP_PROFILES where 
					INTERFACE ='".$devid."'");
			$row = $result->fetchArray(); 
			return $row[0];
		}

		public function getallsiprecords() {
			$dbh = DB_Handle::Connect();
			$query = "";
			$result = $dbh->query($query);
			return $result;
		}
	}
?>
