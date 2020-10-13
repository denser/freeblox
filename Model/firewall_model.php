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
	include_once('sbc_constants.php');

	class FirewallConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $NAME = 'NAME';
		public static $ENABLED = 'ENABLED';
		public static $SRC_TYPE = 'SRC_TYPE';
		public static $SRC_ADDRESS = 'SRC_ADDRESS';
		public static $DST_TYPE = 'DST_TYPE';
		public static $DST_ADDRESS = 'DST_ADDRESS';
		public static $PORT = 'PORT';
		public static $PROTOCOL = 'PROTOCOL';
		public static $ACTION = 'ACTION';
		public static $DST_IFACE = 'DST_IFACE';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getFirewallRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM FIREWALL_RULES');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getFirewallRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM FIREWALL_RULES 
							WHERE NAME = :name;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle( "SELECT count(*) FROM FIREWALL_RULES 
						WHERE NAME = '$name'");
		}

		public function addFirewallRule($name, $enabled, $srctype, $srcaddr, $dsttype, $dstaddr, 
			$protocol, $port, $action, $dst_iface) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 0 ) {
					return FALSE;
				}
				if ( !isset(self::$add_stmt) ) {
					self::$add_stmt = $dbh->prepare ('INSERT INTO FIREWALL_RULES (NAME, 
								ENABLED, SRC_TYPE, SRC_ADDRESS, DST_TYPE, 
								DST_ADDRESS, PROTOCOL, PORT, ACTION, DST_IFACE) 
								VALUES (:name, :enabled, :srctype, :srcaddr, 
								:dsttype, :dstaddr, :protocol, :port, :action, 
								:dst_iface)');
				}
				self::$add_stmt->reset();
				self::checkIfRuleExists($netaddr);
				self::$add_stmt->bindValue(':name', $name);
				self::$add_stmt->bindValue(':enabled', $enabled);
				self::$add_stmt->bindValue(':srctype', $srctype);
				self::$add_stmt->bindValue(':srcaddr', $srcaddr);
				self::$add_stmt->bindValue(':dsttype', $dsttype);
				self::$add_stmt->bindValue(':dstaddr', $dstaddr);
				self::$add_stmt->bindValue(':protocol', $protocol);
				self::$add_stmt->bindValue(':port', $port);
				self::$add_stmt->bindValue(':action', $action);
				self::$add_stmt->bindValue(':dst_iface', $dst_iface);
				return self::$add_stmt->execute();
		}

		public function editFirewallRule($name, $enabled, $srctype, $srcaddr, $dsttype, $dstaddr, 
			$protocol, $port, $action, $dst_iface) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 1) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					self::$edit_stmt = $dbh->prepare('UPDATE FIREWALL_RULES SET 
								NAME = :name, ENABLED = :enabled, 
								SRC_TYPE = :srctype, SRC_ADDRESS = :srcaddr, 
								DST_TYPE = :dsttype, DST_ADDRESS = :dstaddr, 
								PROTOCOL = :protocol, PORT = :port, 
								ACTION = :action, DST_IFACE = :dst_iface WHERE 
								NAME = :name');
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':name', $name);
				self::$edit_stmt->bindValue(':enabled', $enabled);
				self::$edit_stmt->bindValue(':srctype', $srctype);
				self::$edit_stmt->bindValue(':srcaddr', $srcaddr);
				self::$edit_stmt->bindValue(':dsttype', $dsttype);
				self::$edit_stmt->bindValue(':dstaddr', $dstaddr);
				self::$edit_stmt->bindValue(':protocol', $protocol);
				self::$edit_stmt->bindValue(':port', $port);
				self::$edit_stmt->bindValue(':action', $action);
				self::$edit_stmt->bindValue(':dst_iface', $dst_iface);
				return self::$edit_stmt->execute();
		}

		public function setFirewallEnable($name, $enabled) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 1 ) {
				return FALSE;
			}
			if ( !isset(self::$edit_stmt) ) {
				self::$edit_stmt = $dbh->prepare('UPDATE FIREWALL_RULES SET NAME = 
							:name, ENABLED = :enabled WHERE NAME = :name');
			}
			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':name', $name);
			self::$edit_stmt->bindValue(':enabled', $enabled);
			return self::$edit_stmt->execute();
		}

		public function deleteFirewallRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM FIREWALL_RULES 
							WHERE NAME = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			return self::$del_stmt->execute();
		}

		public function getIPTypeOptions() {
			return SBC_FWCONFIG_OPTIONS::$IP_TYPE_OPTIONS;
		}

		public function getProtocolOptions() {
			return SBC_FWCONFIG_OPTIONS::$PROTOCOL_OPTIONS;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM FIREWALL_RULES");
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
