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

	class TrunkConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $DESCRIPTION = 'DESCRIPTION';
		public static $DOMAIN_IP_URI = 'DOMAIN_IP_URI';
		public static $USERNAME = 'USERNAME';
		public static $AUTHUSER = 'AUTHUSER';
		public static $PASSWORD = 'PASSWORD';
		public static $SIP_REGISTRAR = 'SIP_REGISTRAR';
		public static $REGISTRAR_EXPIRE = 'REGISTRAR_EXPIRE';
		public static $OUTBOUND_CALLER_ID = 'OUTBOUND_CALLER_ID';
		public static $OUTBOUND_PROXY_URI = 'OUTBOUND_PROXY_URI';
		public static $USERAGENT = 'USERAGENT';
		public static $INTERNAL_SIP_PROFILE = 'INTERNAL_SIP_PROFILE';
		public static $EXTERNAL_SIP_PROFILE = 'EXTERNAL_SIP_PROFILE';
		public static $MEDIA_PROFILE = 'MEDIA_PROFILE';
		public static $ADD_PREFIX = 'ADD_PREFIX';
		public static $STRIP_DIGITS = 'STRIP_DIGITS';
		public static $ALLOW_INBOUND = 'ALLOW_INBOUND';
		public static $INBOUND_URI = 'INBOUND_URI';
		public static $MAX_INBOUND = 'MAX_INBOUND';
		public static $ALLOW_OUTBOUND = 'ALLOW_OUTBOUND';
		public static $MAX_OUTBOUND = 'MAX_OUTBOUND';
		public static $SIP_DOMAIN = 'SIP_DOMAIN';
		public static $T38_PROFILE = 'T38_PROFILE';
		public static $MEDIA_ENCRYPT_LAN = 'MEDIA_ENCRYPT_LAN';
		public static $MEDIA_ENCRYPT_WAN = 'MEDIA_ENCRYPT_WAN';
		public static $SIPHEADERS = 'SIPHEADERS';
		public static $ENUM_CHECK = 'ENUM_CHECK';
		public static $ENUM_TYPE = 'ENUM_TYPE';
		public static $ENUM_SUFFIX = 'ENUM_SUFFIX';
		public static $ENUM_SERVICE = 'ENUM_SERVICE';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getTrunkConfigRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$qry = "SELECT NAME, DESCRIPTION, DOMAIN_IP_URI, USERNAME,";
				$qry .= "PASSWORD,SIP_REGISTRAR,REGISTRAR_EXPIRE,";
				$qry .= "OUTBOUND_CALLER_ID,OUTBOUND_PROXY_URI, USERAGENT,";
				$qry .= "INTERNAL_SIP_PROFILE,EXTERNAL_SIP_PROFILE,MEDIA_PROFILE,";
				$qry .= "ADD_PREFIX,STRIP_DIGITS, ALLOW_INBOUND,INBOUND_URI,";
				$qry .= "MAX_INBOUND,ALLOW_OUTBOUND, MAX_OUTBOUND,";
				$qry .= "SIP_DOMAIN, T38_PROFILE,MEDIA_ENCRYPT_LAN,";
				$qry .= "MEDIA_ENCRYPT_WAN,SIPHEADERS,ENUM_CHECK, ";
				$qry .= "ENUM_TYPE,ENUM_SUFFIX,ENUM_SERVICE, AUTHUSER FROM TRUNKS_CONFIGURATION";
				self::$get_stmt = $dbh->prepare($qry);
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getTrunkConfigRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$qry = "SELECT NAME, DESCRIPTION, DOMAIN_IP_URI, USERNAME,";
				$qry .= "PASSWORD,SIP_REGISTRAR,REGISTRAR_EXPIRE,";
				$qry .= "OUTBOUND_CALLER_ID,OUTBOUND_PROXY_URI, USERAGENT,";
				$qry .= "INTERNAL_SIP_PROFILE,EXTERNAL_SIP_PROFILE,MEDIA_PROFILE,";
				$qry .= "ADD_PREFIX,STRIP_DIGITS, ALLOW_INBOUND,INBOUND_URI,";
				$qry .= "MAX_INBOUND,ALLOW_OUTBOUND, MAX_OUTBOUND,";
				$qry .= "SIP_DOMAIN, T38_PROFILE,MEDIA_ENCRYPT_LAN,";
				$qry .= "MEDIA_ENCRYPT_WAN,SIPHEADERS,ENUM_CHECK,";
				$qry .= "ENUM_TYPE,ENUM_SUFFIX,ENUM_SERVICE, AUTHUSER ";
				$qry .= "FROM TRUNKS_CONFIGURATION WHERE NAME = :name;";
				self::$get_stmt = $dbh->prepare($qry);
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function getInboundFromDomain($DOMAIN_IP_URI) {
			$dbh = DB_Handle::Connect();
			$qry = "SELECT INBOUND_URI FROM TRUNKS_CONFIGURATION WHERE ";
			$qry .= "DOMAIN_IP_URI ='$DOMAIN_IP_URI'";
			$result =  $dbh->query ($qry);
			$row = $result -> fetchArray();
			return $row[0];
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM TRUNKS_CONFIGURATION 
						WHERE NAME = '$name'");
		}

		public function addTrunkConfigRule( $name, $desc, $domain_ip_uri, $username, $passwd, 
			$sip_registrar, $registrar_expire, $ob_call_id, $ob_proxy_uri,$user_agent, 
			$internal_sip_profile, $external_sip_profile, $media_profile, $add_prefix, 
			$strip_digits, $allow_inbound , $inbound_uri, $max_inbound, $allow_outbound, 
			$max_outbound, $sip_domain, $t38_profile, $media_encrypt_lan, 
			$media_encrypt_wan, $sipheaders, $enum_check,$enum_type, $enum_suffix, 
			$enum_service,$authuser) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 0 ) {
					return FALSE;
				}
				if ( !isset(self::$add_stmt) ) {
					$qry = "INSERT INTO TRUNKS_CONFIGURATION ( NAME,";
					$qry .= "DESCRIPTION, DOMAIN_IP_URI, USERNAME, PASSWORD, ";
					$qry .= "SIP_REGISTRAR, REGISTRAR_EXPIRE, OUTBOUND_CALLER_ID,";
					$qry .= "OUTBOUND_PROXY_URI, USERAGENT,INTERNAL_SIP_PROFILE,";
					$qry .= "EXTERNAL_SIP_PROFILE, MEDIA_PROFILE, ADD_PREFIX,";
					$qry .= "STRIP_DIGITS, ALLOW_INBOUND, INBOUND_URI, MAX_INBOUND,";
					$qry .= "ALLOW_OUTBOUND, MAX_OUTBOUND, SIP_DOMAIN, T38_PROFILE,";
					$qry .= "MEDIA_ENCRYPT_LAN,MEDIA_ENCRYPT_WAN,SIPHEADERS,";
					$qry .= "ENUM_CHECK,ENUM_TYPE,ENUM_SUFFIX,ENUM_SERVICE,AUTHUSER ) ";
					$qry .= "VALUES ( :name, :desc, :domain_ip_uri, :username,";
					$qry .= ":passwd, :sip_registrar, :registrar_expire,";
					$qry .= " :ob_call_id, :ob_proxy_uri, :user_agent,";
					$qry .= " :internal_sip_profile, :external_sip_profile,";
					$qry .= " :media_profile, :add_prefix, :strip_digits,";
					$qry .= " :allow_inbound, :inbound_uri, :max_inbound,";
					$qry .= " :allow_outbound, :max_outbound, :sip_domain,";
					$qry .= " :t38_profile, :media_encrypt_lan,";
					$qry .= " :media_encrypt_wan, :sipheaders,";
					$qry .= " :enum_check, :enum_type, :enum_suffix, :enum_service,";
					$qry .= " :authuser);";
					self::$add_stmt = $dbh->prepare($qry);
				}
				self::$add_stmt->reset();
				self::checkIfRuleExists($name);
				self::$add_stmt->bindValue(':name', $name);
				self::$add_stmt->bindValue(':desc', $desc);
				self::$add_stmt->bindValue(':domain_ip_uri', $domain_ip_uri);
				self::$add_stmt->bindValue(':username', $username);
				self::$add_stmt->bindValue(':passwd', $passwd);
				self::$add_stmt->bindValue(':sip_registrar', $sip_registrar);
				self::$add_stmt->bindValue(':registrar_expire', $registrar_expire);
				self::$add_stmt->bindValue(':ob_call_id', $ob_call_id);
				self::$add_stmt->bindValue(':ob_proxy_uri', $ob_proxy_uri);
				self::$add_stmt->bindValue(':user_agent', $user_agent);
				self::$add_stmt->bindValue(':internal_sip_profile', 
						$internal_sip_profile);
				self::$add_stmt->bindValue(':external_sip_profile', 
						$external_sip_profile);
				self::$add_stmt->bindValue(':media_profile', $media_profile);
				self::$add_stmt->bindValue(':add_prefix', $add_prefix);
				self::$add_stmt->bindValue(':strip_digits', $strip_digits);
				self::$add_stmt->bindValue(':allow_inbound', $allow_inbound);
				self::$add_stmt->bindValue(':max_inbound', $max_inbound);
				self::$add_stmt->bindValue(':allow_outbound', $allow_outbound);
				self::$add_stmt->bindValue(':max_outbound', $max_outbound);
				self::$add_stmt->bindValue(':inbound_uri', $inbound_uri);
				self::$add_stmt->bindValue(':sip_domain', $sip_domain);
				self::$add_stmt->bindValue(':t38_profile', $t38_profile);
				self::$add_stmt->bindValue(':media_encrypt_lan', $media_encrypt_lan);
				self::$add_stmt->bindValue(':media_encrypt_wan', $media_encrypt_wan);
				self::$add_stmt->bindValue(':sipheaders', $sipheaders);
				self::$add_stmt->bindValue(':enum_check', $enum_check);
				self::$add_stmt->bindValue(':enum_type', $enum_type);
				self::$add_stmt->bindValue(':enum_suffix', $enum_suffix);
				self::$add_stmt->bindValue(':enum_service', $enum_service);
				self::$add_stmt->bindValue(':authuser', $authuser);
				return self::$add_stmt->execute();
		}

		public function editTrunkConfigRule ( $name, $desc, $domain_ip_uri, $username, $passwd, 
			$sip_registrar, $registrar_expire, $ob_call_id, $ob_proxy_uri, $user_agent, 
			$internal_sip_profile, $external_sip_profile, $media_profile, $add_prefix, 
			$strip_digits, $allow_inbound, $inbound_uri, $max_inbound, $allow_outbound, 
			$max_outbound ,$sip_domain, $t38_profile, $media_encrypt_lan, 
			$media_encrypt_wan, $sipheaders,  $enum_check,$enum_type, $enum_suffix, 
			$enum_service,$authuser) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 1 ) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					$qry = "UPDATE TRUNKS_CONFIGURATION SET ";
					$qry .= "NAME = :name , DESCRIPTION = :desc,";
					$qry .= "DOMAIN_IP_URI = :domain_ip_uri,";
					$qry .= "USERNAME = :username, PASSWORD = :passwd,";
					$qry .= "SIP_REGISTRAR = :sip_registrar,";
					$qry .= "REGISTRAR_EXPIRE = :registrar_expire,";
					$qry .= "OUTBOUND_CALLER_ID = :ob_call_id,";
					$qry .= "OUTBOUND_PROXY_URI = :ob_proxy_uri,";
					$qry .= "USERAGENT = :user_agent,";
					$qry .= "INTERNAL_SIP_PROFILE = :internal_sip_profile,";
					$qry .= "EXTERNAL_SIP_PROFILE = :external_sip_profile,";
					$qry .= "MEDIA_PROFILE = :media_profile,";
					$qry .= "ADD_PREFIX = :add_prefix, ";
					$qry .= "STRIP_DIGITS = :strip_digits, ";
					$qry .= "ALLOW_INBOUND =:allow_inbound,  ";
					$qry .= "INBOUND_URI =:inbound_uri, ";
					$qry .= "MAX_INBOUND =:max_inbound, ";
					$qry .= "ALLOW_OUTBOUND =:allow_outbound,";
					$qry .= "MAX_OUTBOUND =:max_outbound, ";
					$qry .= "SIP_DOMAIN =:sip_domain, ";
					$qry .= "T38_PROFILE = :t38_profile,";
					$qry .= "MEDIA_ENCRYPT_LAN =:media_encrypt_lan,";
					$qry .= "MEDIA_ENCRYPT_WAN =:media_encrypt_wan,";
					$qry .= "SIPHEADERS =:sipheaders, ENUM_CHECK =:enum_check,";
					$qry .= " ENUM_TYPE =:enum_type, ENUM_SUFFIX =:enum_suffix,";
					$qry .= " ENUM_SERVICE =:enum_service, ";
					$qry .= " AUTHUSER =:authuser ";
					$qry .= "WHERE NAME = :name";
					self::$edit_stmt = $dbh->prepare($qry);
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':name', $name);
				self::$edit_stmt->bindValue(':desc', $desc);
				self::$edit_stmt->bindValue(':domain_ip_uri', $domain_ip_uri);
				self::$edit_stmt->bindValue(':username', $username);
				self::$edit_stmt->bindValue(':passwd', $passwd);
				self::$edit_stmt->bindValue(':sip_registrar', $sip_registrar);
				self::$edit_stmt->bindValue(':registrar_expire', $registrar_expire);
				self::$edit_stmt->bindValue(':ob_call_id', $ob_call_id);
				self::$edit_stmt->bindValue(':ob_proxy_uri', $ob_proxy_uri);
				self::$edit_stmt->bindValue(':user_agent', $user_agent);
				self::$edit_stmt->bindValue(':internal_sip_profile', 
								$internal_sip_profile);
				self::$edit_stmt->bindValue(':external_sip_profile', 
								$external_sip_profile);
				self::$edit_stmt->bindValue(':media_profile', $media_profile);
				self::$edit_stmt->bindValue(':add_prefix', $add_prefix);
				self::$edit_stmt->bindValue(':strip_digits', $strip_digits);
				self::$edit_stmt->bindValue(':allow_inbound', $allow_inbound);
				self::$edit_stmt->bindValue(':max_inbound', $max_inbound);
				self::$edit_stmt->bindValue(':allow_outbound', $allow_outbound);
				self::$edit_stmt->bindValue(':max_outbound', $max_outbound);
				self::$edit_stmt->bindValue(':inbound_uri', $inbound_uri);
				self::$edit_stmt->bindValue(':sip_domain', $sip_domain);
				self::$edit_stmt->bindValue(':t38_profile', $t38_profile);
				self::$edit_stmt->bindValue(':media_encrypt_lan', $media_encrypt_lan);
				self::$edit_stmt->bindValue(':media_encrypt_wan', $media_encrypt_wan);
				self::$edit_stmt->bindValue(':sipheaders', $sipheaders);
				self::$edit_stmt->bindValue(':enum_check', $enum_check);
				self::$edit_stmt->bindValue(':enum_type', $enum_type);
				self::$edit_stmt->bindValue(':enum_suffix', $enum_suffix);
				self::$edit_stmt->bindValue(':enum_service', $enum_service);
				self::$edit_stmt->bindValue(':authuser', $authuser);
				return self::$edit_stmt->execute();
		}

		public function deletetrunkConfigRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM TRUNKS_CONFIGURATION 
						WHERE NAME = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			return self::$del_stmt->execute();
		}

		public function getAllsipheaders() {
			$dbh = DB_Handle::Connect();
			$qry = "SELECT NAME FROM SHM_RULES";
			$result =  $dbh->query ($qry);
			return $result;
		}

		public function getIPTypeOptions() {
			return DeviceAccessConfigModel::$ip_type_options;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getTrunkConfigCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT COUNT(*) FROM TRUNKS_CONFIGURATION");
			while ( $row = $result->fetchArray()) {
				return $row[0];
			}
		}

		public function getAllTrunkConfigNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM TRUNKS_CONFIGURATION");
			return $result;
		}

		public function getTrunksSipProfileNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select NAME from TRUNKS_CONFIGURATION where 
				TRUNKS_CONFIGURATION.name not in(select trunks from lcr_config)");
			return $result;
		}

		public function getTrunkSipLanProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT INTERNAL_SIP_PROFILE FROM TRUNKS_CONFIGURATION");
			return $result;
		}

		public function getTrunkSipWanProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT EXTERNAL_SIP_PROFILE FROM TRUNKS_CONFIGURATION");
			return $result;
		}

		public function getTrunkProfileStatus() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT * FROM TRUNKS_CONFIGURATION");
			return $result;
		}

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM TRUNKS_CONFIGURATION");
			$i=0;
			while ($row = $result->fetchArray()) {
				$names[$i] = $row[0];
				$i++;
			}
			$count = count($names);
			if ( $count > 0 ) {
				return $names;
			} else {
				$names = array();
				return $names;
			}
		}

		public function checkinternalinterface($val) {
			$count = 0;
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from TRUNKS_CONFIGURATION where ";
			$query .= "INTERNAL_SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];
			$query = "select count(*) from ROAMING_USERS where ";
			$query .= "LAN_SIP_PROFILE ='$val'";
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

		public function checkexternalinterface($val) {
			$count = 0;
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from TRUNKS_CONFIGURATION where ";
			$query .= "EXTERNAL_SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];
			$query = "select count(*) from ROAMING_USERS where ";
			$query .= "WAN_SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];
			return $count;
		}
	}
?>
