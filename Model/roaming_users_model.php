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

	class RoamingUsersModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $DESCRIPTION = 'DESCRIPTION';
		public static $WAN_SIP_PROFILE = 'WAN_SIP_PROFILE';
		public static $LAN_SIP_PROFILE = 'LAN_SIP_PROFILE';
		public static $MEDIA_PROFILE = 'MEDIA_PROFILE';
		public static $FORWARD_REGISTRAR_SERVER_PORT = 'FORWARD_REGISTRAR_SERVER_PORT';
		public static $FORCE_EXPIRE = 'FORCE_EXPIRE';
		/*public static $PROTOCOL = 'PROTOCOL';
		public static $SERVER_CERTS = 'SERVER_CERTS';*/
		public static $T38_PROFILE = 'T38_PROFILE';
		public static $MEDIA_ENCRYPT_LAN = 'MEDIA_ENCRYPT_LAN';
		public static $MEDIA_ENCRYPT_WAN = 'MEDIA_ENCRYPT_WAN';
		public static $IP_AUTH = 'IP_AUTH';
		public static $MAX_INBOUND = 'MAX_INBOUND';
		public static $MAX_OUTBOUND = 'MAX_OUTBOUND';
		public static $PRESENCE = 'PRESENCE';
		public static $PRESENCE_SUB = 'PRESENCE_SUB';
		public static $SIPHEADERS = 'SIPHEADERS';
		public static $ENUM_CHECK = 'ENUM_CHECK';
		public static $ENUM_TYPE = 'ENUM_TYPE';
		public static $ENUM_SUFFIX = 'ENUM_SUFFIX';
		public static $ENUM_SERVICE = 'ENUM_SERVICE';
		public static $PRESENCE_DOMAIN = 'PRESENCE_DOMAIN';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getRoamingUsers() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$qry = "SELECT NAME, DESCRIPTION, WAN_SIP_PROFILE,";
				$qry .= "LAN_SIP_PROFILE, MEDIA_PROFILE,";
				$qry .= "FORWARD_REGISTRAR_SERVER_PORT, FORCE_EXPIRE,";
				$qry .= "T38_PROFILE, MEDIA_ENCRYPT_LAN, MEDIA_ENCRYPT_WAN,";
				$qry .= "IP_AUTH, MAX_INBOUND, MAX_OUTBOUND, PRESENCE,";
				$qry .= "PRESENCE_SUB, SIPHEADERS, ENUM_CHECK, ENUM_TYPE, ";
				$qry .= "ENUM_SUFFIX, ENUM_SERVICE, PRESENCE_DOMAIN FROM ROAMING_USERS";
				self::$get_stmt = $dbh->prepare($qry);
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getRoamingUser($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$qry = "SELECT NAME, DESCRIPTION, WAN_SIP_PROFILE,";
				$qry .= "LAN_SIP_PROFILE, MEDIA_PROFILE,";
				$qry .= "FORWARD_REGISTRAR_SERVER_PORT, FORCE_EXPIRE,";
				$qry .= "T38_PROFILE, MEDIA_ENCRYPT_LAN, MEDIA_ENCRYPT_WAN,";
				$qry .= "IP_AUTH,MAX_INBOUND, MAX_OUTBOUND,PRESENCE,";
				$qry .= "PRESENCE_SUB, SIPHEADERS,ENUM_CHECK,";
				$qry .= "ENUM_TYPE,ENUM_SUFFIX,ENUM_SERVICE, PRESENCE_DOMAIN ";
				$qry .= "FROM ROAMING_USERS WHERE NAME = :name;";
				self::$get_stmt = $dbh->prepare($qry);
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM ROAMING_USERS 
						WHERE NAME = '$name'");
		}

		public function addRoamingUser($name, $desc, $wan_sip_profile, $lan_sip_profile, $media_profile, $forward_reg_server_port, $force_expire, $t38_profile, $media_encrypt_lan, $media_encrypt_wan, $ip_auth, $max_inbound, $max_outbound,$presence,$presence_sub, $sipheaders, $enum_check,$enum_type, $enum_suffix, $enum_service, $presence_domain ) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 0 ) {
				return FALSE;
			}
			if ( !isset(self::$add_stmt) ) {
				$qry = "INSERT INTO ROAMING_USERS (NAME, ";
				$qry .= "DESCRIPTION, WAN_SIP_PROFILE, LAN_SIP_PROFILE,";
				$qry .= "MEDIA_PROFILE, FORWARD_REGISTRAR_SERVER_PORT,";
				$qry .= "FORCE_EXPIRE, T38_PROFILE, MEDIA_ENCRYPT_LAN,";
				$qry .= "MEDIA_ENCRYPT_WAN, IP_AUTH, MAX_INBOUND,";
				$qry .= "MAX_OUTBOUND, PRESENCE, PRESENCE_SUB, SIPHEADERS,";
				$qry .= "ENUM_CHECK, ENUM_TYPE, ENUM_SUFFIX, ENUM_SERVICE, 
					PRESENCE_DOMAIN) ";
				$qry .= " VALUES(:name, :desc, :wan_sip_profile,";
				$qry .=" :lan_sip_profile, :media_profile,";
				$qry .=" :forward_reg_server_port, :force_expire, :t38_profile,";
				$qry .=" :media_encrypt_lan, :media_encrypt_wan,";
				$qry .=" :ip_auth, :max_inbound, :max_outbound,";
				$qry .=" :presence,:presence_sub,:sipheaders,";
				$qry .= " :enum_check, :enum_type, :enum_suffix, ";
				$qry .= ":enum_service, :presence_domain)";
				self::$add_stmt = $dbh->prepare($qry);
			}
			self::$add_stmt->reset();
			self::checkIfRuleExists($name);
			self::$add_stmt->bindValue(':name', $name);
			self::$add_stmt->bindValue(':desc', $desc);
			self::$add_stmt->bindValue(':wan_sip_profile', $wan_sip_profile);
			self::$add_stmt->bindValue(':lan_sip_profile', $lan_sip_profile);
			self::$add_stmt->bindValue(':media_profile', $media_profile);
			self::$add_stmt->bindValue(':forward_reg_server_port', $forward_reg_server_port);
			self::$add_stmt->bindValue(':force_expire', $force_expire);
			self::$add_stmt->bindValue(':t38_profile', $t38_profile);
			self::$add_stmt->bindValue(':media_encrypt_lan', $media_encrypt_lan);
			self::$add_stmt->bindValue(':media_encrypt_wan', $media_encrypt_wan);
			self::$add_stmt->bindValue(':ip_auth', $ip_auth);
			self::$add_stmt->bindValue(':max_inbound', $max_inbound);
			self::$add_stmt->bindValue(':max_outbound', $max_outbound);
			self::$add_stmt->bindValue(':presence', $presence);
			self::$add_stmt->bindValue(':presence_sub', $presence_sub);
			self::$add_stmt->bindValue(':sipheaders', $sipheaders);
			self::$add_stmt->bindValue(':enum_check', $enum_check);
			self::$add_stmt->bindValue(':enum_type', $enum_type);
			self::$add_stmt->bindValue(':enum_suffix', $enum_suffix);
			self::$add_stmt->bindValue(':enum_service', $enum_service);
			self::$add_stmt->bindValue(':presence_domain', $presence_domain);
			return self::$add_stmt->execute();
		}

		public function getSipDomainMap($roamingname,$wanprofile) {
			$str = "select * from SIP_DOMAIN_MAP where ROAMING_USER_NAME = '$roamingname'";
			$str .= " AND SIP_PROFILE_NAME = '$wanprofile'";
			$sipdomainlist = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query($str);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				$str1 = $row['ID'].",".$row['SIP_DOMAIN'].",".$row['SIP_METHOD'].",".$row['DST_URI'];
				array_push($sipdomainlist, $str1);
			}
			return $sipdomainlist;
		}

		public function addRoamingSipDomain($romaingname,$sipprofilename,$sipdomain,$method,$desuri) {
			$str = "INSERT INTO SIP_DOMAIN_MAP (ROAMING_USER_NAME, SIP_PROFILE_NAME, 
				SIP_DOMAIN, SIP_METHOD, DST_URI) VALUES ";
			$str .= "('$romaingname','$sipprofilename','$sipdomain','$method','$desuri')";
			$dbh = DB_Handle::Connect();
			$dbh->query($str);
			return;
		}

		public function deleteRoamingSipDomain ($romaingname,$sipprofilename) {
			$str = "delete from SIP_DOMAIN_MAP where ROAMING_USER_NAME 
				= '$romaingname' AND  SIP_PROFILE_NAME = '$sipprofilename'";
			$dbh = DB_Handle::Connect();
			$dbh->query($str);
			return;
		}

		public function deleteAllRaomingSipDomain($romaingname) {
			$str = "delete from SIP_DOMAIN_MAP where ROAMING_USER_NAME = '$romaingname'";
			$dbh = DB_Handle::Connect();
			$dbh->query($str);
			return;
		}

		public function editRoamingUser($name, $desc, $wan_sip_profile, $lan_sip_profile, 
			$media_profile, $forward_reg_server_port, $force_expire, $t38_profile, 
			$media_encrypt_lan, $media_encrypt_wan, $ip_auth, $max_inbound, $max_outbound, 
			$presence,$presence_sub, $sipheaders, $enum_check, $enum_type, $enum_suffix, 
			$enum_service, $presence_domain) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 1 ) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					$query = "UPDATE ROAMING_USERS SET DESCRIPTION= :desc, ";
					$query .= "WAN_SIP_PROFILE = :wan_sip_profile, ";
					$query .= "LAN_SIP_PROFILE = :lan_sip_profile, ";
					$query .= "MEDIA_PROFILE = :media_profile, ";
					$query .= "FORWARD_REGISTRAR_SERVER_PORT = ";
					$query .= ":forward_reg_server_port, ";
					$query .= "FORCE_EXPIRE = :force_expire, ";
					$query .= "T38_PROFILE =:t38_profile, ";
					$query .= "MEDIA_ENCRYPT_LAN =:media_encrypt_lan, ";
					$query .= "MEDIA_ENCRYPT_WAN =:media_encrypt_wan, ";
					$query .= "IP_AUTH =:ip_auth, MAX_INBOUND = :max_inbound, ";
					$query .= "MAX_OUTBOUND =:max_outbound,PRESENCE =:presence, ";
					$query .= "SIPHEADERS =:sipheaders, ";
					$query .= "PRESENCE_SUB =:presence_sub,";
					$query .= "SIPHEADERS =:sipheaders, ENUM_CHECK =:enum_check,";
					$query .= " ENUM_TYPE =:enum_type, ENUM_SUFFIX =:enum_suffix,";
					$query .= " ENUM_SERVICE =:enum_service, PRESENCE_DOMAIN ";
					$query .= "=:presence_domain WHERE NAME = :name";
					self::$edit_stmt =$dbh->prepare($query);
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':name', $name);
				self::$edit_stmt->bindValue(':desc', $desc);
				self::$edit_stmt->bindValue(':wan_sip_profile', $wan_sip_profile);
				self::$edit_stmt->bindValue(':lan_sip_profile', $lan_sip_profile);
				self::$edit_stmt->bindValue(':media_profile', $media_profile);
				self::$edit_stmt->bindValue(':forward_reg_server_port',
							$forward_reg_server_port);
				self::$edit_stmt->bindValue(':force_expire', $force_expire);
				self::$edit_stmt->bindValue(':t38_profile', $t38_profile);
				self::$edit_stmt->bindValue(':media_encrypt_lan', $media_encrypt_lan);
				self::$edit_stmt->bindValue(':media_encrypt_wan', $media_encrypt_wan);
				self::$edit_stmt->bindValue(':ip_auth', $ip_auth);
				self::$edit_stmt->bindValue(':max_inbound', $max_inbound);
				self::$edit_stmt->bindValue(':max_outbound', $max_outbound);
				self::$edit_stmt->bindValue(':presence', $presence);
				self::$edit_stmt->bindValue(':presence_sub', $presence_sub);
				self::$edit_stmt->bindValue(':sipheaders', $sipheaders);
				self::$edit_stmt->bindValue(':enum_check', $enum_check);
				self::$edit_stmt->bindValue(':enum_type', $enum_type);
				self::$edit_stmt->bindValue(':enum_suffix', $enum_suffix);
				self::$edit_stmt->bindValue(':enum_service', $enum_service);
				self::$edit_stmt->bindValue(':presence_domain', $presence_domain);
				return self::$edit_stmt->execute();
		}

		public function deleteRoamingUser ( $name ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM ROAMING_USERS 
							WHERE NAME = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			self::$del_stmt->execute();
			$this->deleteAllRaomingSipDomain($name);
			return ;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getRoamingUserSipProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT WAN_SIP_PROFILE FROM ROAMING_USERS 
					WHERE WAN_SIP_PROFILE != 'NULL'");
			return $result;
		}

		public function getfrwdsipdomain() {
			$sipdomainlist = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select SIP_DOMAIN from SIP_DOMAIN_PROFILES");
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($sipdomainlist, $row["SIP_DOMAIN"]);
			}
			return $sipdomainlist;
		}

		public function getAllsipheaders() {
			$dbh = DB_Handle::Connect();
			$qry = "SELECT NAME FROM SHM_RULES";
			$result =  $dbh->query ($qry);
			return $result;	
		}

		public function getRoamingUserLanProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT LAN_SIP_PROFILE FROM ROAMING_USERS 
					WHERE LAN_SIP_PROFILE != 'NULL'");
			return $result;
		}

		public function getRoamingUserCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT COUNT(*) FROM ROAMING_USERS");
			return $result;
		}


		public function getRoamingUserProfileStatus() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT * FROM ROAMING_USERS");
			return $result;
		}

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM ROAMING_USERS");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$names[$i] = $row[0];
				$i++;
			}
			$count = count($names);
			if ($count > 0) {
				return $names;
			} else {
				$names = array();
				return $names;
			}
		}

		public function checkexternalprofile($val) {
			$count = 0;
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from ROAMING_USERS where ";
			$query .= "WAN_SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];
			$query = "select count(*) from TRUNKS_CONFIGURATION where ";
			$query .= "EXTERNAL_SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];
			return $count;	
		}

		public function checkinternalprofile($val) {
			$count = 0;
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from ROAMING_USERS where ";
			$query .= "LAN_SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];

			$query = "select count(*) from TRUNKS_CONFIGURATION where ";
			$query .= "INTERNAL_SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];

			$query = "select count(*) from LCR_CONFIG where ";
			$query .= "SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];
			return $count;
		}

		public function checkroamingname($val) {
			$query = "select count(*) from SUBSCRIBERS where ";
			$query .= "ROAMUSERPROFILE = '$val'";
			$dbh = DB_Handle::Connect();
			$row = $dbh->query($query);
			$result = $row->fetchArray();
			return $result[0];
		}

		public function checkroamingevents($val) {
			$query = "select count(*) from PRESENCE_EVENTS where ";
			$query .= "RUSERPROFILE = '$val'";
			$dbh = DB_Handle::Connect();
			$row= $dbh->query($query);
			$result = $row->fetchArray();
			return $result[0];
		}

		public function getSipnamesDomains() {
			$sipdomain= array();
			$str = "select NAME,SIPDOMAINS,ACCESS_PORT from SIP_PROFILES";
			$dbh = DB_Handle::Connect();
			$result= $dbh->query($str);
			while ($row = $result->fetchArray()) {
				$domainval = $row['NAME'].";".$row['SIPDOMAINS'].";".$row['ACCESS_PORT'];
				array_push($sipdomain,$domainval);
			}
			return $sipdomain;
		}

		public function getAllsipdomainBindport() {
			$sipdomain_bindport= array();
			$query= "select ID,SIP_DOMAIN,BIND_PORT ";
			$query.= "from SIP_DOMAIN_PROFILES ";
			$dbh = DB_Handle::Connect();
			$result= $dbh->query($query);
			while ($row = $result->fetchArray()) {
				$val = $row['SIP_DOMAIN'].";".$row['BIND_PORT'];
				array_push($sipdomain_bindport,$val);
			}
			return $sipdomain_bindport;
		}
	}
?>
