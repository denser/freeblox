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

	class MediaConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $DESCRIPTION = 'DESCRIPTION';
		public static $EXTERNAL_INTERFACE = 'EXTERNAL_INTERFACE';
		public static $INTERNAL_INTERFACE = 'INTERNAL_INTERFACE';
		public static $MEDIA_INTERFACE = 'MEDIA_INTERFACE';
		public static $RTPPORT_START = 'RTPPORT_START';
		public static $RTPPORT_END = 'RTPPORT_END';
		public static $CODECS = 'CODECS';
		public static $ENABLE_SILENCE_SUPPRESSION = 'ENABLE_SILENCE_SUPPRESSION';
		public static $MEDIA_TOS = 'MEDIA_TOS';
		public static $NAT_ENABLE = 'NAT_ENABLE';
		static $codecs_array = array("g729","g711u","g711a");
		static $dtmf_mode_array = array("Passthrough","RFC2833","Inband","Auto");
		static $media_encrypt = array("None","Optional","Compulsory");

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getAllMediaSettings() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, DESCRIPTION, 
							EXTERNAL_INTERFACE, INTERNAL_INTERFACE, 
							MEDIA_INTERFACE, RTPPORT_START, RTPPORT_END, 
							CODECS, ENABLE_SILENCE_SUPPRESSION, MEDIA_TOS , 
							NAT_ENABLE FROM MEDIA_PROFILES');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getMediaSettings($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, DESCRIPTION, 
							EXTERNAL_INTERFACE, INTERNAL_INTERFACE, 
							MEDIA_INTERFACE, RTPPORT_START, RTPPORT_END, 
							CODECS, ENABLE_SILENCE_SUPPRESSION, MEDIA_TOS, 
							NAT_ENABLE FROM MEDIA_PROFILES WHERE 
							NAME = :name;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM MEDIA_PROFILES WHERE 
						NAME = '$name'");
		}

		public function addMediaSettings($name, $desc, $external_interface, $internal_interface,
			$media_interface, $rtpport_start, $rtpport_end, $codecs, $enable_silence_sup, 
			$media_tos, $nat_enable) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 0 ) {
					return FALSE;
				}
				if ( !isset(self::$add_stmt) ) {
					self::$add_stmt = $dbh->prepare('INSERT INTO MEDIA_PROFILES ( 
							NAME, DESCRIPTION, EXTERNAL_INTERFACE, 
							INTERNAL_INTERFACE, MEDIA_INTERFACE, 
							RTPPORT_START, RTPPORT_END, CODECS, 
							ENABLE_SILENCE_SUPPRESSION, MEDIA_TOS, 
							NAT_ENABLE) VALUES ( :name, :desc, 
							:external_interface, :internal_interface, 
							:media_interface, :rtpport_start, :rtpport_end, 
							:codecs, :enable_silence_sup, :media_tos, 
							:nat_enable)');
				}
				self::$add_stmt->reset();
				self::checkIfRuleExists($name);
				self::$add_stmt->bindValue(':name', $name);
				self::$add_stmt->bindValue(':desc', $desc);
				self::$add_stmt->bindValue(':external_interface', $external_interface);
				self::$add_stmt->bindValue(':internal_interface', $internal_interface);
				self::$add_stmt->bindValue(':media_interface', $media_interface);
				self::$add_stmt->bindValue(':rtpport_start', $rtpport_start);
				self::$add_stmt->bindValue(':rtpport_end', $rtpport_end);
				self::$add_stmt->bindValue(':codecs', $codecs);
				self::$add_stmt->bindValue(':enable_silence_sup', $enable_silence_sup);
				self::$add_stmt->bindValue(':media_tos', $media_tos);
				self::$add_stmt->bindValue(':nat_enable', $nat_enable);
				return self::$add_stmt->execute();
		}

		public function editMediaSettings ( $name, $desc, $external_interface, 
			$internal_interface, $media_interface, $rtpport_start, $rtpport_end, $codecs, 
			$enable_silence_sup, $media_tos, $nat_enable){
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 1 ) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					self::$edit_stmt = $dbh->prepare('UPDATE MEDIA_PROFILES SET 
							DESCRIPTION= :desc, EXTERNAL_INTERFACE = 
							:external_interface, INTERNAL_INTERFACE = 
							:internal_interface,  MEDIA_INTERFACE = 
							:media_interface, RTPPORT_START= :rtpport_start, 
							RTPPORT_END = :rtpport_end, CODECS = :codecs, 
							ENABLE_SILENCE_SUPPRESSION = 
							:enable_silence_sup, MEDIA_TOS = :media_tos, 
							NAT_ENABLE =:nat_enable WHERE NAME = :name');
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':name', $name);
				self::$edit_stmt->bindValue(':desc', $desc);
				self::$edit_stmt->bindValue(':external_interface', $external_interface);
				self::$edit_stmt->bindValue(':internal_interface', $internal_interface);
				self::$edit_stmt->bindValue(':media_interface', $media_interface);
				self::$edit_stmt->bindValue(':rtpport_start', $rtpport_start);
				self::$edit_stmt->bindValue(':rtpport_end', $rtpport_end);
				self::$edit_stmt->bindValue(':codecs', $codecs);
				self::$edit_stmt->bindValue(':enable_silence_sup', $enable_silence_sup);
				self::$edit_stmt->bindValue(':media_tos', $media_tos);
				self::$edit_stmt->bindValue(':nat_enable', $nat_enable);
				return self::$edit_stmt->execute();
		}

		public function deleteMediaSettings ( $name ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM MEDIA_PROFILES 
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

		public function getCodecs() {
			return MediaConfigModel::$codecs_array;
		}

		public function getDTMFModes() {
			return MediaConfigModel::$dtmf_mode_array;
		}

		public function getMediaEncryptOptions() {
			return MediaConfigModel::$media_encrypt;
		}

		public function getMediaProfileIpPort($name) {
			$shell_obj = ShellCommand::getInstance();
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT * FROM MEDIA_PROFILES WHERE NAME='$name'");
			while ( $row = $res->fetchArray()) {
				$iface_name = $row[4];
				$shell_status = $shell_obj->Execute("ifconfig $iface_name| grep 'inet addr:' | cut -d: -f2 | awk '{print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				if( $shell_status == 0) {
					 $ipaddress =$shell_obj->getLastOutput();
				}
				return $ipaddress.":".$row[5]."-". $row[6];
			}
			return $result;
		}

		public function getTranscodingCount($iface) {
			$dbh = DB_Handle::Connect();
			$count = $dbh->querySingle("SELECT count(*) FROM MEDIA_PROFILES 
						WHERE MEDIA_INTERFACE = '$iface'");
			return $count;
		}

		public function getTranscodingMediaProfile($iface) {
			$dbh = DB_Handle::Connect();
			$res = $dbh->querySingle("SELECT NAME FROM MEDIA_PROFILES 
						WHERE MEDIA_INTERFACE = '$iface'");
			return $res;
		}

		public function getMediaProfileInterfaceAddress($media_profile) {
			$shell_obj = ShellCommand::getInstance();
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT MEDIA_INTERFACE FROM MEDIA_PROFILES 
					WHERE NAME ='$media_profile'");
			while ( $row = $res->fetchArray()) {
				$iface_name = $row[0];
			}
			return $iface_name;
		}

		public function updTranRoamingTrunk($mediaprofilename) {
			$query = "UPDATE TRUNKS_CONFIGURATION SET MEDIA_ENCRYPT_LAN = '', 
				MEDIA_ENCRYPT_WAN = '', T38_PROFILE='' 
				WHERE MEDIA_PROFILE = '$mediaprofilename'";
			$dbh = DB_Handle::Connect();
			$dbh->query($query);
			$query = "UPDATE ROAMING_USERS SET MEDIA_ENCRYPT_LAN = '', 
				MEDIA_ENCRYPT_WAN = '', T38_PROFILE='' 
				WHERE MEDIA_PROFILE = '$mediaprofilename'";
			$dbh->query($query);
		}

		public function getTransMediaProfiles() {
			$transmediaprofiles = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM MEDIA_PROFILES 
					WHERE MEDIA_INTERFACE != ''");
			while ($row = $result->fetchArray()) {
				array_push($transmediaprofiles,$row['NAME']);
			}
			return $transmediaprofiles;
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

		public function getTrunkMediaProfiles() {
			$trunkMeidaArr = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT MEDIA_PROFILE FROM TRUNKS_CONFIGURATION");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$trunkMeidaArr[$i] = $row[0];
				$i++;
			}
			return $trunkMeidaArr;
		}

		public function getRoamingMediaProfiles() {
			$roamingMeidaArr = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT MEDIA_PROFILE FROM ROAMING_USERS");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$roamingMeidaArr[$i] = $row[0];
				$i++;
			}
			return $roamingMeidaArr;
		}

		public function getFaxProfiles() {
			$faxNamesArr = array();
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT MEDIA_PROFILE FROM T38_FAX_PROFILES");
			$i = 0;
			while ( $row = $result->fetchArray()) {
				$faxNamesArr[$i] = $row[0];
				$i++;
			}
			return $faxNamesArr;
		}

		public function delMediaProfiles() {
			$trunkMedia = MediaConfigModel::getTrunkMediaProfiles();
			$roamingMedia = MediaConfigModel::getRoamingMediaProfiles();
			$mediaProfiles = MediaConfigModel::getMediaProfileNamesList();
			$faxProfiles = MediaConfigModel::getFaxProfiles();
			$trunkMediaCount = count($trunkMedia);
			$roamingMediaCount = count($roamingMedia);
			$faxCount = count($faxProfiles);
			if ( $trunkMediaCount < 1 && ($roamingMediaCount < 1 && $faxCount < 1)) {
				$delMediaProfiles = $mediaProfiles;
			}
			if ( $trunkMediaCount > 0 && ($roamingMediaCount > 0 && $faxCount > 0)) {
				$trunk_roam = array_merge($trunkMedia,$roamingMedia);
				$trunk_roam_arr = array_merge($trunk_roam,$faxProfiles);
				$delMediaProfiles = array_diff($mediaProfiles,$trunk_roam_arr);
			}
			if ( $faxCount > 0 && ($trunkMediaCount < 1 && $roamingMediaCount < 1)) {
				$delMediaProfiles = array_diff($mediaProfiles,$faxProfiles);
			}
			if ( $trunkMediaCount > 0 && ($faxCount < 1 && $roamingMediaCount < 1)) {
				$delMediaProfiles = array_diff($mediaProfiles,$trunkMedia);
			}
			if ( $roamingMediaCount > 0  && ($faxCount < 1 && trunkMediaCount < 1)) {
				$delMediaProfiles = array_diff($mediaProfiles,$roamingMedia);
			}
			if ( $faxCount > 0 && ($trunkMediaCount < 1 && $roamingMediaCount > 0)) {
				$roam_fax = array_merge($roamingMedia,$faxProfiles);
				$roam_fax_arr = (array_unique($roam_fax));
				$delMediaProfiles = array_diff($mediaProfiles,$roam_fax_arr);
			}
			if ( $faxCount > 0 && ($trunkMediaCount > 0 && $roamingMediaCount < 1)) {
				$trunk_fax = array_merge($trunkMedia,$faxProfiles);
				$trunk_fax_arr = (array_unique($trunk_fax));
				$delMediaProfiles = array_diff($mediaProfiles,$trunk_fax_arr);
			}
			return $delMediaProfiles;
		}

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM MEDIA_PROFILES");
			$i = 0;
			while ($row = $result->fetchArray()) {
				$mediaProfilesArr[$i] = $row[0];
				$i++;
			}
			$count = count($mediaProfilesArr);
			if ($count > 0 ) {
				return $mediaProfilesArr;
			} else {
				$mediaProfilesArr = array();
				return $mediaProfilesArr;
			}
		}

		public function checktranscoding($val) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from MEDIA_PROFILES where ";
			$query .= "MEDIA_INTERFACE = '$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			return $row[0];
		} 
	}
?>
