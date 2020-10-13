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

	class DeviceAccessConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $IP_TYPE = 'IP_TYPE';
		public static $NET_ADDRESS = 'NET_ADDRESS';
		public static $ENABLED = 'ENABLED';
		public static $COMMENTS = 'COMMENTS';
		public static $PROTOCOL = 'PROTOCOL';
		public static $PORT = 'PORT';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getDeviceAccessRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, IP_TYPE, NET_ADDRESS,
						 ENABLED, COMMENTS, PROTOCOL, PORT FROM DEVICE_ACCESS_FILTER');
			}
			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}

		public function getDeviceAccessRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME, IP_TYPE, NET_ADDRESS,
						 ENABLED, COMMENTS, PROTOCOL, PORT FROM
						 DEVICE_ACCESS_FILTER WHERE NAME = :name;');
			}
			self::$get_stmt->reset();

			self::$get_stmt->bindValue(':name', $name);

			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM DEVICE_ACCESS_FILTER
						 WHERE NAME = '$name'");
		}

		public function addDeviceAccessRule($name, $ip_type, $netaddr, $enabled, $comments, $protocol, $port) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 0 ) {
				return FALSE;
			}

			if ( !isset(self::$add_stmt) ) {
				self::$add_stmt = $dbh->prepare('INSERT INTO DEVICE_ACCESS_FILTER
						 (NAME,IP_TYPE,NET_ADDRESS, ENABLED, COMMENTS, PROTOCOL,
						 PORT) VALUES (:name, :ip_type, :netaddr, :enabled,
						 :comments, :protocol, :port)');
			}

			self::$add_stmt->reset();

			self::checkIfRuleExists($netaddr);
			self::$add_stmt->bindValue(':name', $name);
			self::$add_stmt->bindValue(':ip_type', $ip_type);
			self::$add_stmt->bindValue(':netaddr', $netaddr);
			self::$add_stmt->bindValue(':enabled', $enabled);
			self::$add_stmt->bindValue(':comments', $comments);
			self::$add_stmt->bindValue(':protocol', $protocol);
			self::$add_stmt->bindValue(':port', $port);

			return self::$add_stmt->execute();
		}

		public function editDeviceAccessRule($name, $ip_type, $netaddr, $enabled, $comments, $protocol, $port) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 1 ) {
				return FALSE;
			}

			if ( !isset(self::$edit_stmt) ) {
				self::$edit_stmt = $dbh->prepare('UPDATE DEVICE_ACCESS_FILTER SET
						 NET_ADDRESS = :netaddr,IP_TYPE= :ip_type,
						 ENABLED = :enabled, COMMENTS = :comments, PROTOCOL =
						 :protocol, PORT = :port WHERE NAME = :name');
			}

			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':name', $name);
			self::$edit_stmt->bindValue(':ip_type', $ip_type);
			self::$edit_stmt->bindValue(':netaddr', $netaddr);
			self::$edit_stmt->bindValue(':enabled', $enabled);
			self::$edit_stmt->bindValue(':comments', $comments);
			self::$edit_stmt->bindValue(':protocol', $protocol);
			self::$edit_stmt->bindValue(':port', $port);

			return self::$edit_stmt->execute();
		}

		public function setDeviceAccessEnable($name, $enabled ) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 1 ) {
				return FALSE;
			}

			if ( !isset(self::$edit_stmt) ) {
				self::$edit_stmt = $dbh->prepare('UPDATE DEVICE_ACCESS_FILTER SET
						 ENABLED = :enabled WHERE NAME = :name');
			}

			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':name', $name);
			self::$edit_stmt->bindValue(':enabled', $enabled);

			return self::$edit_stmt->execute();
		}

		public function deleteDeviceAccessRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM DEVICE_ACCESS_FILTER
						 WHERE NAME = :name;');
			}

			self::$del_stmt->reset();

			self::$del_stmt->bindValue(':name', $name);

			return self::$del_stmt->execute();
		}

		public function getIPTypeOptions() {
			return SBC_FWCONFIG_OPTIONS::$IP_TYPE_OPTIONS;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function updateMgmtVlanAccessRule($addr, $mask) {
			$name = "MgmtVlanAccess";

			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 1 ) {
				return FALSE;
			}

			$bits = strpos(decbin(ip2long($mask)), "0");
			$network = long2ip(bindec(decbin(ip2long($addr)) & decbin(ip2long($mask))));
			$netaddr = $network."/".$bits;

			if ( !isset(self::$edit_stmt) ) {
				self::$edit_stmt = $dbh->prepare('UPDATE DEVICE_ACCESS_FILTER SET
						 NET_ADDRESS = :netaddr WHERE NAME = :name');
			}

			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':name', $name);
			self::$edit_stmt->bindValue(':netaddr', $netaddr);

			return self::$edit_stmt->execute();
		}

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM DEVICE_ACCESS_FILTER");
			$i=0;
			while ($row = $res->fetchArray()) {
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
	}
?>
