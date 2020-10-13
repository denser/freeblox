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
	include_once('sbc_provisioning.php');

	class DpiConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $set_stmt = null;

		public static $ID = 'CATEGORY_ID';
		public static $CATEGORY = 'CATEGORY';
		public static $UI_NAME = 'UI_NAME';
		public static $ACTION = 'ACTION';
		public static $ENABLED = 'ENABLED';
		public static $BL_DURATION = 'BL_DURATION';
		public static $PARAMS = 'PARAMS';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getActionMappingNames() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME FROM ACTION_MAPPING');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getDpiConfigAll() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT CATEGORY_ID, CATEGORY, UI_NAME, 
							ACTION, ENABLED, BL_DURATION, PARAMS 
							FROM DPI_CONFIG');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getDpiConfig($category) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT CATEGORY, UI_NAME, ACTION, 
							ENABLED, BL_DURATION, PARAMS FROM DPI_CONFIG 
							WHERE CATEGORY = :category;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':category', $category);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function setDpiConfig($category, $action,$enabled, $block_duration, $params) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$set_stmt) ) {
				self::$set_stmt = $dbh->prepare('UPDATE DPI_CONFIG SET 
							ACTION = :action, ENABLED = :enabled, 
							BL_DURATION = :block_duration, PARAMS = :params 
							WHERE CATEGORY = :category;');
			}
			self::$set_stmt->reset();
			self::$set_stmt->bindValue(':action', $action);
			self::$set_stmt->bindValue(':enabled', $enabled);
			self::$set_stmt->bindValue(':block_duration', $block_duration);
			self::$set_stmt->bindValue(':params', $params);
			self::$set_stmt->bindValue(':category', $category);
			return self::$set_stmt->execute();
		}

		public function setDpiEnable($category,$enabled) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$set_stmt) ) {
				self::$set_stmt = $dbh->prepare('UPDATE DPI_CONFIG SET 
							ENABLED = :enabled WHERE CATEGORY = :category;');
			}
			self::$set_stmt->reset();
			self::$set_stmt->bindValue(':enabled', $enabled);
			self::$set_stmt->bindValue(':category', $category);
			return self::$set_stmt->execute();
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getCategoryAction($category_id) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT ACTION FROM DPI_CONFIG WHERE 
					CATEGORY_ID = '$category_id'");
			$i = 0;
			while ($row = $result->fetchArray()) {
				return $row[0];
			}
		}

		public function getSigsConfigAction($sig_id) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT ACTION FROM SIGS_CONFIG WHERE 
					SIGID = '$sig_id'");
			$i = 0;
			while ($row = $result->fetchArray()) {
				return $row[0];
			}
		}

		public function getPreProcessorAction($category_id) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT ACTION FROM PREPROC_CATEGORY_CONFIG 
					WHERE CATEGORY_ID = '$category_id'");
			$i = 0;
			while ($row = $result->fetchArray()) {
				return $row[0];
			}
		}

		public function getCategoryName($category_id) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT UI_NAME FROM DPI_CONFIG WHERE 
					CATEGORY_ID = '$category_id'");
			$i = 0;
			while ($row = $result->fetchArray()) {
				return $row[0];
			}
		}
	}
?>
