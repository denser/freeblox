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
	include_once("shell_command.php");

	class FaxProfilesConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $DESCRIPTION = 'DESCRIPTION';
		public static $VERSION = 'VERSION';
		public static $MAX_BIT_RATE = 'MAX_BIT_RATE';
		public static $FAX_RATE_MANAGEMENT = 'FAX_RATE_MANAGEMENT';
		public static $FAX_ECM_ENABLE = 'FAX_ECM_ENABLE';
		public static $FAX_UDP_EC = 'FAX_UDP_EC';
		public static $MEDIA_PROFILE = 'MEDIA_PROFILE';
		static $versions_array = array("0","1","2","3");
		static $max_bit_rate_array = array("4800","9600","14400");
		static $fax_rate_mgmt_array = array("1"=>"localTCF","2"=>"transferredTCF");
		static $fax_udp_ec_array = array("1"=>"t38UDPRedundancy","2"=>"t38UDPFEC");

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getAllFaxProfileSettings() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, DESCRIPTION, VERSION, 
							MAX_BIT_RATE, FAX_RATE_MANAGEMENT, 
							FAX_ECM_ENABLE, FAX_UDP_EC, MEDIA_PROFILE 
							FROM T38_FAX_PROFILES');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getFaxProfileSettings($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, DESCRIPTION, VERSION, 
							MAX_BIT_RATE, FAX_RATE_MANAGEMENT, 
							FAX_ECM_ENABLE, FAX_UDP_EC, MEDIA_PROFILE 
							FROM T38_FAX_PROFILES WHERE NAME = :name;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM T38_FAX_PROFILES 
						WHERE NAME = '$name'");
		}

		public function addFaxProfile ( $name, $desc, $version, $max_bit_rate, $fax_rate_mgmt, 
			$fax_ecm_enable, $fax_udp_ec, $media_profile ) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 0 ) {
					return FALSE;
				}
				if ( !isset(self::$add_stmt) ) {
					self::$add_stmt = $dbh->prepare('INSERT INTO T38_FAX_PROFILES (NAME, 
								DESCRIPTION, VERSION, MAX_BIT_RATE, 
								FAX_RATE_MANAGEMENT, FAX_ECM_ENABLE, 
								FAX_UDP_EC, MEDIA_PROFILE ) VALUES ( 
								:name, :desc, :version, :max_bit_rate, 
								:fax_rate_mgmt, :fax_ecm_enable, 
								:fax_udp_ec, :media_profile)');
				}
				self::$add_stmt->reset();
				self::checkIfRuleExists($name);
				self::$add_stmt->bindValue(':name', $name);
				self::$add_stmt->bindValue(':desc', $desc);
				self::$add_stmt->bindValue(':version', $version);
				self::$add_stmt->bindValue(':max_bit_rate', $max_bit_rate);
				self::$add_stmt->bindValue(':fax_rate_mgmt', $fax_rate_mgmt);
				self::$add_stmt->bindValue(':fax_ecm_enable', $fax_ecm_enable);
				self::$add_stmt->bindValue(':fax_udp_ec', $fax_udp_ec);
				self::$add_stmt->bindValue(':media_profile', $media_profile);
				return self::$add_stmt->execute();
		}

		public function editFaxProfiles ( $name, $desc, $version, $max_bit_rate, $fax_rate_mgmt, 
			$fax_ecm_enable, $fax_udp_ec, $media_profile ) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 1 ) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					self::$edit_stmt = $dbh->prepare('UPDATE T38_FAX_PROFILES SET 
								DESCRIPTION= :desc, VERSION = :version, 
								MAX_BIT_RATE = :max_bit_rate, 
								FAX_RATE_MANAGEMENT = :fax_rate_mgmt, 
								FAX_ECM_ENABLE = :fax_ecm_enable, 
								FAX_UDP_EC = :fax_udp_ec, 
								MEDIA_PROFILE =:media_profile 
								WHERE NAME = :name');
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':name', $name);
				self::$edit_stmt->bindValue(':desc', $desc);
				self::$edit_stmt->bindValue(':version', $version);
				self::$edit_stmt->bindValue(':max_bit_rate', $max_bit_rate);
				self::$edit_stmt->bindValue(':fax_rate_mgmt', $fax_rate_mgmt);
				self::$edit_stmt->bindValue(':fax_ecm_enable', $fax_ecm_enable);
				self::$edit_stmt->bindValue(':fax_udp_ec', $fax_udp_ec);
				self::$edit_stmt->bindValue(':media_profile', $media_profile);
				return self::$edit_stmt->execute();
		}

		public function getTrunkFaxProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT T38_PROFILE FROM TRUNKS_CONFIGURATION");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$trunkFaxArr[$i] = $row[0];
				$i++;
			}
			return $trunkFaxArr;
		}

		public function getRoamingFaxProfiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT T38_PROFILE FROM ROAMING_USERS");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$roamingFaxArr[$i] = $row[0];
				$i++;
			}
			return $roamingFaxArr;
		}

		public function getRoamTrunkFaxProfiles() {
			$trunkFax = FaxProfilesConfigModel::getTrunkFaxProfiles();
			$roamingFax = FaxProfilesConfigModel::getRoamingFaxProfiles();
			$faxProfiles = FaxProfilesConfigModel::getNames();
			$trunkFaxCount = count($trunkFax);
			$roamingFaxCount = count($roamingFax);
			if ( $trunkFaxCount < 1 && $roamingFaxCount < 1 ) {
				$delFaxProfiles = $faxProfiles;
			}
			if ( $trunkFaxCount > 0 && $roamingFaxCount > 0 ) {
				$trunk_roam_fax = array_intersect($roamingFax,$trunkFax);
				$delFaxProfiles = array_diff($faxProfiles,$trunk_roam_fax);
			}
			if ( $trunkFaxCount > 0 && $roamingFaxCount < 1 ) {
				$delFaxProfiles = array_diff($faxProfiles,$trunkFax);
			}
			if ( $roamingFaxCount > 0 && $trunkFaxCount < 1 ) {
				$delFaxProfiles = array_diff($faxProfiles,$roamingFax);
			}
			return $delFaxProfiles;
		}

		public function deleteFaxProfiles ( $name ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM T38_FAX_PROFILES 
							WHERE NAME = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			return self::$del_stmt->execute();
		}

		public function getMediaProfileNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM MEDIA_PROFILES");
			return $result;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getFaxVersion() {
			return FaxProfilesConfigModel::$versions_array;
		}

		public function getMaxBitRate() {
			return FaxProfilesConfigModel::$max_bit_rate_array;
		}

		public function getFaxMgmt() {
			return FaxProfilesConfigModel::$fax_rate_mgmt_array;
		}

		public function getFaxUdpEC() {
			return FaxProfilesConfigModel::$fax_udp_ec_array;
		}

		public function getMediaProfileIpPort($name) {
			$shell_obj = ShellCommand::getInstance();
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT * FROM MEDIA_PROFILES WHERE NAME='$name'");
			while ( $row = $res->fetchArray()) {
				$iface_name = $row[4];
				$shell_status = $shell_obj->Execute("ifconfig $iface_name| grep 'inet addr:' | cut -d: -f2 | awk '{print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if( $shell_status == 0) {
					 $ipaddress =$shell_obj->getLastOutput();
				}
				return $ipaddress.":".$row[5]."-". $row[6];
			}
			return $result;
		}

		public function getTranscodingCount($iface) {
			$dbh = DB_Handle::Connect();
			$count = $dbh->querySingle("SELECT count(*) FROM MEDIA_PROFILES WHERE 
					MEDIA_INTERFACE = '$iface'");
			return $count;
		}

		public function getMediaProfileNamesList() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM MEDIA_PROFILES");
			$i = 0;
			while ($row = $result->fetchArray()) {
				$mediaProfilesArr[$i] = $row[0];
				$i++;
			}
			return $mediaProfilesArr;
		}

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM T38_FAX_PROFILES");
			$i = 0;
			while ($row = $result->fetchArray()) {
				$faxProfilesArr[$i] = $row[0];
				$i++;
			}
			$count = count($faxProfilesArr);
			if ($count > 0 ) {
				return $faxProfilesArr;
			} else {
				$faxProfilesArr = array();
				return $faxProfilesArr;
			}
		}

		public function getT38ProfilesCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->querySingle("SELECT COUNT(*) FROM T38_FAX_PROFILES");
			return $result;
		}
	}
?>
