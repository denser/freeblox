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

	class GlobalSettingsConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $PROFILE = 'NAME';
		public static $INTERFACE = 'SBC_IF_TYPE';
		public static $SIGNALLING_PORT = 'SBC_PORT';
		public static $TLS = 'USR_TLS';
		public static $TLS_PORT = 'TLS_PORT';
		public static $NAT = 'ENABLE_NAT';
		public static $NAT_TYPE = 'NAT_TYPE';
		public static $NAT_IP = 'NAT_ADDRESS';
		public static $COMMENTS = 'DESCRIPTION';
		static $ip_type_options = array("ANY","HOST","NETWORK","RANGE");

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getGlobalSettingsRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, SBC_IF_TYPE, SBC_PORT, 
							USR_TLS, TLS_PORT, ENABLE_NAT, NAT_TYPE, 
							NAT_ADDRESS, DESCRIPTION FROM SBC_PROFILES');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getGlobalSettingsRule($profile) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, SBC_IF_TYPE, SBC_PORT, 
							USR_TLS, TLS_PORT, ENABLE_NAT, NAT_TYPE, 
							NAT_ADDRESS, DESCRIPTION FROM SBC_PROFILES 
							WHERE NAME = :profile;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':profile', $profile);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($profile) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM SBC_PROFILES WHERE 
						NAME = '$profile'");
		}

		public function addGlobalSettingsRule( $profile, $interface, $signal_port, $tls, $tls_port, 
			$nat, $nat_type, $nat_ip, $comments ) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($profile) != 0 ) {
					return FALSE;
				}
				if ( !isset(self::$add_stmt) ) {
					self::$add_stmt = $dbh->prepare('INSERT INTO SBC_PROFILES (NAME, 
								SBC_IF_TYPE, SBC_PORT,USR_TLS, TLS_PORT, 
								ENABLE_NAT, NAT_TYPE, NAT_ADDRESS,DESCRIPTION) 
								VALUES (:profile, :interface, :signal_port, 
								:tls, :tls_port, :nat, :nat_type, :nat_ip, 
								:comments)');
				}
				self::$add_stmt->reset();
				self::checkIfRuleExists($profile);
				self::$add_stmt->bindValue(':profile', $profile);
				self::$add_stmt->bindValue(':interface', $interface);
				self::$add_stmt->bindValue(':signal_port', $signal_port);
				self::$add_stmt->bindValue(':tls', $tls);
				self::$add_stmt->bindValue(':tls_port', $tls_port);
				self::$add_stmt->bindValue(':nat', $nat);
				self::$add_stmt->bindValue(':nat_type', $nat_type);
				self::$add_stmt->bindValue(':nat_ip', $nat_ip);
				self::$add_stmt->bindValue(':comments', $comments);
				return self::$add_stmt->execute();
		}

		public function editGlobalSettingsRule($profile, $interface, $signal_port, $tls, $tls_port, 
			$nat, $nat_type, $nat_ip, $comments ) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($profile) != 1 ) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					self::$edit_stmt = $dbh->prepare('UPDATE SBC_PROFILES SET SBC_IF_TYPE = 
								:interface, SBC_PORT= :signal_port, USR_TLS = 
								:tls, TLS_PORT = :tls_port, ENABLE_NAT = :nat, 
								NAT_TYPE = :nat_type, NAT_ADDRESS = :nat_ip, 
								DESCRIPTION = :comments WHERE NAME = :profile');
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':profile', $profile);
				self::$edit_stmt->bindValue(':interface', $interface);
				self::$edit_stmt->bindValue(':signal_port', $signal_port);
				self::$edit_stmt->bindValue(':tls', $tls);
				self::$edit_stmt->bindValue(':tls_port', $tls_port);
				self::$edit_stmt->bindValue(':nat', $nat);
				self::$edit_stmt->bindValue(':nat_type', $nat_type);
				self::$edit_stmt->bindValue(':nat_ip', $nat_ip);
				self::$edit_stmt->bindValue(':comments', $comments);
				return self::$edit_stmt->execute();
		}

		public function deleteGlobalSettingsRule($profile) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM SBC_PROFILES WHERE 
							NAME = :profile;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':profile', $profile);
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

		public function getInboundProfiles($iftype) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM SBC_PROFILES WHERE 
					SBC_IF_TYPE = '$iftype'");
			return $result;
		}

		public function getGlobalSettingsProfilesCount() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT COUNT(*) FROM SBC_PROFILES");
			while ( $row = $result->fetchArray()) {
				return $row[0];
			}
		}
	}
?>
