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

	class VIP_ConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $IPADDRESS = 'IPADDRESS';
		public static $INTERFACE = 'INTERFACE';
		public static $NETMASK = 'NETMASK';
		public static $DESCRIPTION = 'DESCRIPTION';
		public static $NAME = 'NAME';
		public static $NAT_SETTINGS = 'NAT_SETTINGS';
		public static $STUN_STATIC_IPADDR = 'STUN_STATIC_IPADDR';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getVIPRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT IPADDRESS, INTERFACE, NETMASK, 
							DESCRIPTION, NAME, NAT_SETTINGS, 
							STUN_STATIC_IPADDR FROM VIRTUAL_IP');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getVIPRule ( $name ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT IPADDRESS, INTERFACE, NETMASK, 
							DESCRIPTION, NAME, NAT_SETTINGS, 
							STUN_STATIC_IPADDR FROM VIRTUAL_IP 
							WHERE NAME = :name;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM VIRTUAL_IP WHERE NAME = '$name'");
		}

		public function addVIPEntry ( $ip_address, $interface, $netmask, $desc, $name, $nat_settings, $natip_address) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 0 ) {
				return FALSE;
			}
			if ( !isset(self::$add_stmt) ) {
				self::$add_stmt = $dbh->prepare('INSERT INTO VIRTUAL_IP ( IPADDRESS, 
							INTERFACE, NETMASK, DESCRIPTION, NAME, 
							NAT_SETTINGS, STUN_STATIC_IPADDR ) VALUES 
							( :ip_address, :interface, :netmask, :desc, 
							:name, :nat_settings, :natip_address)');
			}
			self::$add_stmt->reset();
			self::checkIfRuleExists($name);
			self::$add_stmt->bindValue(':name', $name);
			self::$add_stmt->bindValue(':ip_address', $ip_address);
			self::$add_stmt->bindValue(':interface', $interface);
			self::$add_stmt->bindValue(':netmask', $netmask);
			self::$add_stmt->bindValue(':desc', $desc);
			self::$add_stmt->bindValue(':nat_settings', $nat_settings);
			self::$add_stmt->bindValue(':natip_address', $natip_address);
			return self::$add_stmt->execute();
		}

		public function editVIPRule ( $ip_address, $interface, $netmask, $desc, $name, $nat_settings, $natip_address ) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 1 ) {
				return FALSE;
			}
			if ( !isset(self::$edit_stmt) ) {
				self::$edit_stmt = $dbh->prepare('UPDATE VIRTUAL_IP SET IPADDRESS = 
							:ip_address, INTERFACE = :interface, 
							NETMASK = :netmask, DESCRIPTION = :desc, 
							NAME =:name, NAT_SETTINGS= :nat_settings, 
							STUN_STATIC_IPADDR= :natip_address 
							WHERE NAME = :name');
			}
			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':name', $name);
			self::$edit_stmt->bindValue(':ip_address', $ip_address);
			self::$edit_stmt->bindValue(':interface', $interface);
			self::$edit_stmt->bindValue(':netmask', $netmask);
			self::$edit_stmt->bindValue(':desc', $desc);
			self::$edit_stmt->bindValue(':nat_settings', $nat_settings);
			self::$edit_stmt->bindValue(':natip_address', $natip_address);
			self::$edit_stmt->execute();
			if($nat_settings == "NO NAT") {
				$str = "update MEDIA_PROFILES set NAT_ENABLE = 0 
					where EXTERNAL_INTERFACE = '$name'";
				$dbh->query($str);
				$str = "update SIP_PROFILES set NAT_ENABLE = 0 
					where INTERFACE = '$name'";
				$dbh->query($str);
			}
		}

		public function deleteVIPRule ( $name ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM VIRTUAL_IP 
							WHERE NAME = :name;');
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

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM VIRTUAL_IP");
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
