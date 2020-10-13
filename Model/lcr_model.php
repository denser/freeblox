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

	class LCRConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $DESCRIPTION = 'DESCRIPTION';
		public static $MATCH_DIGITS = 'MATCH_DIGITS';
		public static $SIP_PROFILE = 'SIP_PROFILE';
		public static $TRUNKS = 'TRUNKS';
		public static $EMAIL_ID = 'EMAIL_ID';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getLCRRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, DESCRIPTION, 
							MATCH_DIGITS, SIP_PROFILE, TRUNKS FROM 
							LCR_CONFIG');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getLCRRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, DESCRIPTION, 
							MATCH_DIGITS, SIP_PROFILE, TRUNKS FROM 
							LCR_CONFIG WHERE NAME = :name;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM LCR_CONFIG WHERE 
						NAME = '$name'");
		}

		public function addLCRRule ( $name, $desc, $match_digits, $sip_profile, $trunks, $email_id ) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 0 ) {
				return FALSE;
			}
			if ( !isset(self::$add_stmt) ) {
				self::$add_stmt = $dbh->prepare('INSERT INTO LCR_CONFIG (NAME, 
							DESCRIPTION, MATCH_DIGITS, SIP_PROFILE, TRUNKS, 
							EMAIL_ID ) VALUES (:name, :desc, :match_digits, 
							:sip_profile, :trunks, :email_id)');
			}
			self::$add_stmt->reset();
			self::checkIfRuleExists($name);
			self::$add_stmt->bindValue(':name', $name);
			self::$add_stmt->bindValue(':desc', $desc);
			self::$add_stmt->bindValue(':match_digits', $match_digits);
			self::$add_stmt->bindValue(':sip_profile', $sip_profile);
			self::$add_stmt->bindValue(':trunks', $trunks);
			self::$add_stmt->bindValue(':email_id', $email_id);
			return self::$add_stmt->execute();
		}

		public function editLCRRule($name, $desc, $match_digits, $sip_profile, $trunks, $email_id) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 1 ) {
				return FALSE;
			}
			if ( !isset(self::$edit_stmt) ) {
				self::$edit_stmt = $dbh->prepare('UPDATE LCR_CONFIG SET NAME = :name, 
								DESCRIPTION= :desc,MATCH_DIGITS = 
								:match_digits, SIP_PROFILE = 
								:sip_profile, TRUNKS =:trunks, 
								EMAIL_ID = :email_id WHERE 
								NAME = :name');
			}
			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':name', $name);
			self::$edit_stmt->bindValue(':desc', $desc);
			self::$edit_stmt->bindValue(':match_digits', $match_digits);
			self::$edit_stmt->bindValue(':sip_profile', $sip_profile);
			self::$edit_stmt->bindValue(':trunks', $trunks);
			self::$edit_stmt->bindValue(':email_id', $email_id);
			return self::$edit_stmt->execute();
		}

		public function deleteLCRRule ( $name ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM LCR_CONFIG WHERE 
							NAME = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			return self::$del_stmt->execute();
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getLCR_SIP_Profiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT SIP_PROFILE FROM LCR_CONFIG");
			return $result;
		}

		public function getLCR_Trunks_Profiles() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT TRUNKS FROM LCR_CONFIG");
			return $result;
		}

		public function getLCR_Trunks_status() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT * FROM LCR_CONFIG");
			return $result;
		}

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT NAME FROM LCR_CONFIG");
			$i=0;
			while($row = $result->fetchArray()) {
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

// = 'SIPPROFILE3';
		public function checksipprofiles($val) {
			$count = 0;
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from LCR_CONFIG where ";
			$query .= "SIP_PROFILE ='$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			$count = $count + $row[0];

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

			return $count;
		}
	}
?>
