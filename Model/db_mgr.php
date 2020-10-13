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
	class DB_Handle_Persist extends SQLite3 {
		// Database Name
		private static $DB_FILE_PERSIST = "/etc/freeblox_conf/DB/freeblox.db.persist";
		private static $instance = null;

		public static function Connect() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function __construct() {
			if (!file_exists(self::$DB_FILE_PERSIST)) {
				return null;
			}
			$this->open(DB_Handle_Persist::$DB_FILE_PERSIST);
		}
	};
	class DB_Handle extends SQLite3 {
		// Database Name
		private static $DB_FILE_ACTIVE = "/etc/freeblox_conf/DB/freeblox.db";
		private static $DB_FILE_PERSIST = "/etc/freeblox_conf/DB/freeblox.db.persist";
		private static $instance = null;

		public static function &Connect() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public static function DisConnect() {
			self::$instance->close() ;
			self::$instance = null ;
		}

		public function __construct() {
			if (!file_exists(self::$DB_FILE_ACTIVE)) {
				if(file_exists(self::$DB_FILE_PERSIST))
					copy(self::$DB_FILE_PERSIST, self::$DB_FILE_ACTIVE);
			}
			$this->open(DB_Handle::$DB_FILE_ACTIVE);
		}

		public static function persistDB() {
			copy(self::$DB_FILE_ACTIVE, self::$DB_FILE_PERSIST);
		}

		public static function ignoreConfigChanges() {
			//$this->close();
			copy(self::$DB_FILE_PERSIST, self::$DB_FILE_ACTIVE);
			//$this->open(DB_Handle::$DB_FILE_ACTIVE);
		}

		public function getActionListCount() {
			$result = DB_Handle::$instance->query("SELECT count(*) FROM UPDATE_LIST");
			$row = $result->fetchArray();
			return $row[0];
		}

		public function getActionList() {
			$result = DB_Handle::$instance->query("SELECT * FROM UPDATE_LIST");
			return $result;
		}

		public function getActionListData($type) {
			$result = DB_Handle::$instance->query("SELECT * FROM UPDATE_LIST WHERE KEYTYPE = '$type'");
			return $result;
		}

		public function getActionListType() {
			$result = DB_Handle::$instance->query("SELECT distinct(KEYTYPE) FROM UPDATE_LIST ORDER BY KEYTYPE ASC");
			return $result;
		}

		public function getUniqueActionListName() {
			$result = DB_Handle::$instance->query("SELECT distinct(T_NAME) FROM UPDATE_LIST ORDER BY T_NAME ASC");
			return $result;
		}

		public function deleteActionList() {
			DB_Handle::$instance->query("DELETE FROM UPDATE_LIST");
		}

		public function deleteAction($keyname, $keytype) {
			DB_Handle::$instance->query("DELETE FROM UPDATE_LIST WHERE KEYNAME = '$keyname' AND KEYTYPE = '$keytype'");
		}

		public function updateActionList($tname, $keyname, $keytype, $action) {
			$result = DB_Handle::$instance->query("SELECT count(*) FROM UPDATE_LIST WHERE KEYNAME = '$keyname' AND KEYTYPE = '$keytype'");
			$row = $result->fetchArray();
			if($row[0]==0) {
				$this->createActionList($tname, $keyname, $keytype, $action);
			} else {
				$query = "UPDATE UPDATE_LIST SET ACTION = '$action' WHERE KEYNAME = '$keyname' AND KEYTYPE = '$keytype'";
				DB_Handle::$instance->query($query);
			}
		}

		public function createActionList($tname, $keyname, $keytype, $action) {
			$query = "INSERT INTO UPDATE_LIST VALUES('$tname', '$keytype', '$keyname', '$action')";
			DB_Handle::$instance->query($query);
		}

		public function getFullProductName() {
			$result = DB_Handle::$instance -> query("SELECT * from SBC_SCHEMA_INFO");
			$row = $result->fetchArray();
			return $row;
		}

		public function getApplicationVer() {
			$result = DB_Handle::$instance -> query("SELECT * from SBC_SCHEMA_INFO");
			$row = $result->fetchArray();
			return $row;
		}

		public function getSigUpdateVer() {
			$result = DB_Handle::$instance -> query("SELECT * from SBC_SIGS_SCHEMA_INFO");
			$row = $result->fetchArray();
			return $row;
		}
	}
?>
