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

	class GeoIpSettingsModel {
		private static $instance = null;
		private static $get_allowed_stmt = null;
		private static $get_stmt = null;
		private static $get_stmt2 = null;
		private static $set_allowed_stmt = null;
		private static $set_allowed_all_stmt = null;
		public static $COUNTRY_NAME = 'COUNTRY_NAME';
		public static $KEYVAL = 'KEYVAL';
		public static $ALLOWED = 'ALLOWED';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getGeoIpFilters() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT COUNTRY_NAME, ALLOWED 
							FROM GEO_BLOCKING_MAP');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getGeoIPFilter($country) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT COUNTRY_NAME, ALLOWED FROM 
							GEO_BLOCKING_MAP WHERE 
							COUNTRY_NAME = :country;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':country', $country);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function getGeoIpFilters2() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt2) ) {
				self::$get_stmt2 = $dbh->prepare('SELECT COUNTRY_NAME, KEYVAL, 
							ALLOWED FROM GEO_BLOCKING_MAP');
			}
			self::$get_stmt2->reset();
			return self::$get_stmt2->execute();
		}

		public function getAllowed($cn_name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_allowed_stmt) ) {
				self::$get_allowed_stmt = $dbh->prepare('SELECT ALLOWED FROM 
								GEO_BLOCKING_MAP WHERE COUNTRY_NAME = 
								:cn_name;');
			}
			self::$get_allowed_stmt->reset();
			self::$get_allowed_stmt->bindValue(':cn_name', $cn_name);
			$result = self::$get_allowed_stmt->execute()->fetchArray(SQLITE3_ASSOC);
			if ( $result ) 
				return $result['ALLOWED'];
			else
				return null;
		}

		public function setAllowed($cn_name, $allowed) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$set_allowed_stmt) ) {
				self::$set_allowed_stmt = $dbh->prepare('UPDATE GEO_BLOCKING_MAP SET 
								ALLOWED = :allowed WHERE COUNTRY_NAME 
								= :cn_name;');
			}
			self::$set_allowed_stmt->reset();
			self::$set_allowed_stmt->bindValue(':allowed', $allowed);
			self::$set_allowed_stmt->bindValue(':cn_name', $cn_name);
			return self::$set_allowed_stmt->execute();
		}

		public function setAllowedAll($allowed) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$set_allowed_all_stmt) ) {
				self::$set_allowed_all_stmt = $dbh->prepare('UPDATE GEO_BLOCKING_MAP 
								SET ALLOWED = :allowed');
			}
			self::$set_allowed_all_stmt->reset();
			self::$set_allowed_all_stmt->bindValue(':allowed', $allowed);
			return self::$set_allowed_all_stmt->execute();
		}

		public function setBlockedAll($allowed) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$set_allowed_all_stmt) ) {
				self::$set_allowed_all_stmt = $dbh->prepare('UPDATE GEO_BLOCKING_MAP 
								SET ALLOWED = :allowed');
			}
			self::$set_allowed_all_stmt->reset();
			self::$set_allowed_all_stmt->bindValue(':allowed', $allowed);
			return self::$set_allowed_all_stmt->execute();
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getGeoIpFiltersCount() {
			$dbh = DB_Handle::Connect();
			$count = $dbh->query("SELECT COUNT(*) FROM GEO_BLOCKING_MAP");
			while ($row = $count->fetchArray()) {
				return $row[0];
			}
		}

		public function getGeoIpFiltersActionCount($action) {
			$dbh = DB_Handle::Connect();
			$action_count = $dbh->query("SELECT COUNT(*) FROM GEO_BLOCKING_MAP 
					WHERE ALLOWED = '$action'");
			while ($action_row = $action_count->fetchArray()) {
				return $action_row[0];
			}
		}
	}
?>
