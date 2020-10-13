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

	class ExtensionProfilesConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;

		public static $EXTENSION = 'EXTENSION';
		public static $REDIRECT_PROXY_IP = 'REDIRECT_PROXY_IP';
		public static $REDIRECT_PROXY_PORT = 'REDIRECT_PROXY_PORT';
		public static $USER = 'USER';
		public static $PASSWD = 'PASSWD';
		public static $REGISTRATION_INTERVAL = 'REGISTRATION_INTERVAL';
		public static $INBOUND_SBC_PROFILE = 'INBOUND_SBC_PROFILE';
		public static $INBOUND_SBC_TRANSPORT = 'INBOUND_SBC_TRANSPORT';
		public static $INBOUND_MEDIA_TYPE = 'INBOUND_MEDIA_TYPE';
		public static $OUTBOUND_SBC_PROFILE = 'OUTBOUND_SBC_PROFILE';
		public static $OUTBOUND_MEDIA_TYPE = 'OUTBOUND_MEDIA_TYPE';
		static $ip_type_options = array("ANY","HOST","NETWORK","RANGE");

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getExtensionProfilesRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT EXTENSION, REDIRECT_PROXY_IP, 
							REDIRECT_PROXY_PORT, USER, PASSWD, 
							REGISTRATION_INTERVAL, INBOUND_SBC_PROFILE, 
							INBOUND_SBC_TRANSPORT, INBOUND_MEDIA_TYPE, 
							OUTBOUND_SBC_PROFILE, OUTBOUND_MEDIA_TYPE FROM 
							EXTENSION_PROFILES');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getExtensionProfilesRule($extension) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT EXTENSION, REDIRECT_PROXY_IP, 
							REDIRECT_PROXY_PORT, USER, PASSWD, 
							REGISTRATION_INTERVAL, INBOUND_SBC_PROFILE, 
							INBOUND_SBC_TRANSPORT, INBOUND_MEDIA_TYPE, 
							OUTBOUND_SBC_PROFILE, OUTBOUND_MEDIA_TYPE FROM 
							EXTENSION_PROFILES WHERE 
							EXTENSION = :extension;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':extension', $extension);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($extension) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM EXTENSION_PROFILES 
						WHERE EXTENSION = '$extension'");
		}

		public function addExtensionProfilesRule( $extension, $redirect_proxy_ip, $redirect_proxy_port, 
			$user, $password, $registration_int, $inbound_sbc_prof, $inbound_sbc_transport, 
			$inbound_media_type, $outbound_sbc_prof, $outbound_media_type) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($extension) != 0 ) {
					return FALSE;
				}
				if ( !isset(self::$add_stmt) ) {
					self::$add_stmt = $dbh->prepare ('INSERT INTO EXTENSION_PROFILES 
								(EXTENSION, REDIRECT_PROXY_IP, 
								REDIRECT_PROXY_PORT, USER, PASSWD, 
								REGISTRATION_INTERVAL, INBOUND_SBC_PROFILE, 
								INBOUND_SBC_TRANSPORT, INBOUND_MEDIA_TYPE, 
								OUTBOUND_SBC_PROFILE, OUTBOUND_MEDIA_TYPE) 
								VALUES (:extension, :redirect_proxy_ip, 
								:redirect_proxy_port, :user, :password,
								:registration_int, :inbound_sbc_prof, 
								:inbound_sbc_transport, :inbound_media_type, 
								:outbound_sbc_prof, :outbound_media_type)');
				}
				self::$add_stmt->reset();
				self::checkIfRuleExists($extension);
				self::$add_stmt->bindValue(':extension', $extension);
				self::$add_stmt->bindValue(':redirect_proxy_ip', $redirect_proxy_ip);
				self::$add_stmt->bindValue(':redirect_proxy_port', $redirect_proxy_port);
				self::$add_stmt->bindValue(':user', $user);
				self::$add_stmt->bindValue(':password', $password);
				self::$add_stmt->bindValue(':registration_int', $registration_int);
				self::$add_stmt->bindValue(':inbound_sbc_prof', $inbound_sbc_prof);
				self::$add_stmt->bindValue(':inbound_sbc_transport', $inbound_sbc_transport);
				self::$add_stmt->bindValue(':inbound_media_type', $inbound_media_type);
				self::$add_stmt->bindValue(':outbound_sbc_prof', $outbound_sbc_prof);
				self::$add_stmt->bindValue(':outbound_media_type', $outbound_media_type);
				return self::$add_stmt->execute();
		}

		public function editExtensionProfilesRule($extension, $redirect_proxy_ip, 
			$redirect_proxy_port, $user, $password, $registration_int, $inbound_sbc_prof, 
			$inbound_sbc_transport, $inbound_media_type, $outbound_sbc_prof, $outbound_media_type) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($extension) != 1 ) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					self::$edit_stmt = $dbh->prepare('UPDATE EXTENSION_PROFILES SET 
								REDIRECT_PROXY_IP = :redirect_proxy_ip, 
								REDIRECT_PROXY_PORT= :redirect_proxy_port, 
								USER = :user, PASSWD = :password , 
								REGISTRATION_INTERVAL = :registration_int, 
								INBOUND_SBC_PROFILE = :inbound_sbc_prof, 
								INBOUND_SBC_TRANSPORT = :inbound_sbc_transport, 
								INBOUND_MEDIA_TYPE = :inbound_media_type, 
								OUTBOUND_SBC_PROFILE = :outbound_sbc_prof, 
								OUTBOUND_MEDIA_TYPE = :outbound_media_type 
								WHERE EXTENSION = :extension');
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':extension', $extension);
				self::$edit_stmt->bindValue(':redirect_proxy_ip', $redirect_proxy_ip);
				self::$edit_stmt->bindValue(':redirect_proxy_port', $redirect_proxy_port);
				self::$edit_stmt->bindValue(':user', $user);
				self::$edit_stmt->bindValue(':password', $password);
				self::$edit_stmt->bindValue(':registration_int', $registration_int);
				self::$edit_stmt->bindValue(':inbound_sbc_prof', $inbound_sbc_prof);
				self::$edit_stmt->bindValue(':inbound_sbc_transport', $inbound_sbc_transport);
				self::$edit_stmt->bindValue(':inbound_media_type', $inbound_media_type);
				self::$edit_stmt->bindValue(':outbound_sbc_prof', $outbound_sbc_prof);
				self::$edit_stmt->bindValue(':outbound_media_type', $outbound_media_type);
				return self::$edit_stmt->execute();
		}

		public function deleteExtensionProfilesRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM EXTENSION_PROFILES 
							WHERE EXTENSION = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			return self::$del_stmt->execute();
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

		public function getExtensionProfilesCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT COUNT(*) FROM EXTENSION_PROFILES");
			while ( $row = $result->fetchArray()) {
				return $row[0];
			}
		}
	}
?>
