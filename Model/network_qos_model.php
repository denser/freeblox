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

	class NetworkConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $set_stmt = null;

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public static $PRIORITY0 = 'Priority0';
		public static $PRIORITY1 = 'Priority1';
		public static $PRIORITY2 = 'Priority2';
		public static $PRIORITY3 = 'Priority3';
		public static $PRIORITY4 = 'Priority4';
		public static $PRIORITY5 = 'Priority5';
		public static $PRIORITY6 = 'Priority6';
		public static $PRIORITY7 = 'Priority7';

		public function get($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT VAL FROM NETWORK_QOS WHERE 
							NAME = :name;');
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
				self::$set_stmt = $dbh->prepare('UPDATE NETWORK_QOS SET 
							VAL = :val WHERE NAME = :name;');
			}
			self::$set_stmt->reset();
			self::$set_stmt->bindValue(':val', $val);
			self::$set_stmt->bindValue(':name', $name);
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
	}
?>
