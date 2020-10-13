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

	class IPSECConfigModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;
		public static $ENABLE = 'ENABLE';
		public static $NAME = 'NAME';
		public static $MODE = 'MODE';
		public static $POLICY_TYPE = 'POLICY_TYPE';
		public static $LOCAL_GATEWAY = 'LOCAL_GATEWAY';
		public static $LOCAL_NETWORK = 'LOCAL_NETWORK';
		public static $REMOTE_GATEWAY = 'REMOTE_GATEWAY';
		public static $REMOTE_NETWORK = 'REMOTE_NETWORK';
		public static $IKE_EXCHANGE_MODE = 'IKE_EXCHANGE_MODE';
		public static $LIFETTIME = 'LIFETTIME';
		public static $ENCRYPTION_ALGORITHM = 'ENCRYPTION_ALGORITHM';
		public static $HASH_ALGORITHM = 'HASH_ALGORITHM';
		public static $AUTHENTICATION_METHOD = 'AUTHENTICATION_METHOD';
		public static $PRESHARED_KEY = 'PRESHARED_KEY';
		public static $DH_GROUP = 'DH_GROUP';
		public static $TRANSPORT = 'TRANSPORT';
		public static $IPSEC_LIFETIME = 'IPSEC_LIFETIME';
		public static $PFS_GROUP = 'PFS_GROUP';
		public static $IPSEC_ENCRYPTION_ALGORITHM = 'IPSEC_ENCRYPTION_ALGORITHM';
		public static $AUTH_ALOGORITHM = 'AUTH_ALOGORITHM';
		public static $ENABLE_COMPRESSION = 'ENABLE_COMPRESSION';
		public static $NAT_TRAVERSAL = 'NAT_TRAVERSAL';
		public static $ENABLE_DPD = 'ENABLE_DPD';
		public static $DPD_DELAY = 'DPD_DELAY';
		public static $DPD_MAXFAIL = 'DPD_MAXFAIL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getIPSECRules() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT ENABLE, NAME, MODE, 
							POLICY_TYPE, LOCAL_GATEWAY, LOCAL_NETWORK, 
							REMOTE_GATEWAY, REMOTE_NETWORK, 
							IKE_EXCHANGE_MODE, LIFETTIME, 
							ENCRYPTION_ALGORITHM, HASH_ALGORITHM, 
							AUTHENTICATION_METHOD, PRESHARED_KEY, DH_GROUP, 
							TRANSPORT, IPSEC_LIFETIME, PFS_GROUP, 
							IPSEC_ENCRYPTION_ALGORITHM, AUTH_ALOGORITHM, 
							ENABLE_COMPRESSION, NAT_TRAVERSAL, ENABLE_DPD, 
							DPD_DELAY, DPD_MAXFAIL FROM IPSEC_VPN');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getIPSECRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT ENABLE, NAME, MODE, 
							POLICY_TYPE, LOCAL_GATEWAY, LOCAL_NETWORK, 
							REMOTE_GATEWAY, REMOTE_NETWORK, 
							IKE_EXCHANGE_MODE, LIFETTIME, 
							ENCRYPTION_ALGORITHM, HASH_ALGORITHM, 
							AUTHENTICATION_METHOD, PRESHARED_KEY, 
							DH_GROUP, TRANSPORT, IPSEC_LIFETIME, 
							PFS_GROUP, IPSEC_ENCRYPTION_ALGORITHM, 
							AUTH_ALOGORITHM, ENABLE_COMPRESSION, 
							NAT_TRAVERSAL, ENABLE_DPD, DPD_DELAY, 
							DPD_MAXFAIL FROM IPSEC_VPN WHERE NAME = :name;');
			}
			self::$get_stmt->reset();
			self::$get_stmt->bindValue(':name', $name);
			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function checkIfRuleExists($name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM IPSEC_VPN WHERE NAME = '$name'");
		}

		public function addIPSECRule( $enable, $name, $mode, $policy_type, $local_gw, $local_nw,
			$remote_gw, $remote_nw, $ike_exchange_mode, $lifetime, $encrypt_algorithm, 
			$hash_algorithm, $auth_method, $preshared_key, $dh_group, $transport, 
			$ipsec_lifetime, $pfs_group, $ipsec_encrypt_algorithm, $auth_algorithm, 
			$enable_compression, $nat_traversal, $enable_dpd, $dpd_delay, $dpd_maxfail ) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 0 ) {
					return FALSE;
				}
				if ( !isset(self::$add_stmt) ) {
				self::$add_stmt = $dbh->prepare('INSERT INTO IPSEC_VPN ( ENABLE, NAME, 
							MODE, POLICY_TYPE, LOCAL_GATEWAY, 
							LOCAL_NETWORK, REMOTE_GATEWAY, REMOTE_NETWORK, 
							IKE_EXCHANGE_MODE, LIFETTIME, 
							ENCRYPTION_ALGORITHM, HASH_ALGORITHM, 
							AUTHENTICATION_METHOD, PRESHARED_KEY, DH_GROUP, 
							TRANSPORT, IPSEC_LIFETIME, PFS_GROUP, 
							IPSEC_ENCRYPTION_ALGORITHM, AUTH_ALOGORITHM, 
							ENABLE_COMPRESSION, NAT_TRAVERSAL, ENABLE_DPD, 
							DPD_DELAY, DPD_MAXFAIL ) VALUES (:enable, 
							:name, :mode, :policy_type, :local_gw, 
							:local_nw, :remote_gw, :remote_nw, 
							:ike_exchange_mode, :lifetime, 
							:encrypt_algorithm, :hash_algorithm, 
							:auth_method, :preshared_key, :dh_group, 
							:transport, :ipsec_lifetime, :pfs_group, 
							:ipsec_encrypt_algorithm, :auth_algorithm, 
							:enable_compression, :nat_traversal, 
							:enable_dpd, :dpd_delay, :dpd_maxfail)');
				}
				self::$add_stmt->reset();
				self::checkIfRuleExists($name);
				self::$add_stmt->bindValue(':name',$name);
				self::$add_stmt->bindValue(':enable',$enable);
				self::$add_stmt->bindValue(':mode',$mode);
				self::$add_stmt->bindValue(':policy_type',$policy_type);
				self::$add_stmt->bindValue(':local_gw',$local_gw);
				self::$add_stmt->bindValue(':local_nw',$local_nw);
				self::$add_stmt->bindValue(':remote_gw',$remote_gw);
				self::$add_stmt->bindValue(':remote_nw',$remote_nw);
				self::$add_stmt->bindValue(':ike_exchange_mode',$ike_exchange_mode);
				self::$add_stmt->bindValue(':lifetime',$lifetime);
				self::$add_stmt->bindValue(':encrypt_algorithm',$encrypt_algorithm);
				self::$add_stmt->bindValue(':hash_algorithm',$hash_algorithm);
				self::$add_stmt->bindValue(':auth_method',$auth_method);
				self::$add_stmt->bindValue(':preshared_key',$preshared_key);
				self::$add_stmt->bindValue(':dh_group',$dh_group);
				self::$add_stmt->bindValue(':transport',$transport);
				self::$add_stmt->bindValue(':ipsec_lifetime',$ipsec_lifetime);
				self::$add_stmt->bindValue(':pfs_group',$pfs_group);
				self::$add_stmt->bindValue(':ipsec_encrypt_algorithm', 
							$ipsec_encrypt_algorithm);
				self::$add_stmt->bindValue(':auth_algorithm',$auth_algorithm);
				self::$add_stmt->bindValue(':enable_compression',$enable_compression);
				self::$add_stmt->bindValue(':nat_traversal',$nat_traversal);
				self::$add_stmt->bindValue(':enable_dpd',$enable_dpd);
				self::$add_stmt->bindValue(':dpd_delay',$dpd_delay);
				self::$add_stmt->bindValue(':dpd_maxfail',$dpd_maxfail);
				return self::$add_stmt->execute();
		}

		public function editIPSECRule( $enable, $name, $mode, $policy_type, $local_gw, 
			$local_nw, $remote_gw, $remote_nw, $ike_exchange_mode, $lifetime, 
			$encrypt_algorithm, $hash_algorithm, $auth_method, $preshared_key, $dh_group, 
			$transport, $ipsec_lifetime, $pfs_group, $ipsec_encrypt_algorithm, 
			$auth_algorithm, $enable_compression, $nat_traversal, $enable_dpd, $dpd_delay, 
			$dpd_maxfail ) {
				$dbh = DB_Handle::Connect();
				if ( self::checkIfRuleExists($name) != 1 ) {
					return FALSE;
				}
				if ( !isset(self::$edit_stmt) ) {
					self::$edit_stmt = $dbh->prepare('UPDATE IPSEC_VPN SET 
								ENABLE = :enable, MODE = :mode, 
								POLICY_TYPE = :policy_type, 
								LOCAL_GATEWAY = :local_gw, 
								LOCAL_NETWORK = :local_nw, 
								REMOTE_GATEWAY =  :remote_gw, 
								REMOTE_NETWORK = :remote_nw, 
								IKE_EXCHANGE_MODE = :ike_exchange_mode, 
								LIFETTIME = :lifetime, 
								ENCRYPTION_ALGORITHM = 
								:encrypt_algorithm, HASH_ALGORITHM = 
								:hash_algorithm, AUTHENTICATION_METHOD 
								= :auth_method, PRESHARED_KEY = 
								:preshared_key, DH_GROUP = :dh_group, 
								TRANSPORT = :transport, IPSEC_LIFETIME 
								= :ipsec_lifetime, PFS_GROUP = 
								:pfs_group, IPSEC_ENCRYPTION_ALGORITHM 
								= :ipsec_encrypt_algorithm, 
								AUTH_ALOGORITHM = :auth_algorithm, 
								ENABLE_COMPRESSION = 
								:enable_compression, NAT_TRAVERSAL = 
								:nat_traversal, ENABLE_DPD = 
								:enable_dpd, DPD_DELAY = :dpd_delay, 
								DPD_MAXFAIL = :dpd_maxfail WHERE 
								NAME = :name');
				}
				self::$edit_stmt->reset();
				self::$edit_stmt->bindValue(':name',$name);
				self::$edit_stmt->bindValue(':enable',$enable);
				self::$edit_stmt->bindValue(':mode',$mode);
				self::$edit_stmt->bindValue(':policy_type',$policy_type);
				self::$edit_stmt->bindValue(':local_gw',$local_gw);
				self::$edit_stmt->bindValue(':local_nw',$local_nw);
				self::$edit_stmt->bindValue(':remote_gw',$remote_gw);
				self::$edit_stmt->bindValue(':remote_nw',$remote_nw);
				self::$edit_stmt->bindValue(':ike_exchange_mode',$ike_exchange_mode);
				self::$edit_stmt->bindValue(':lifetime',$lifetime);
				self::$edit_stmt->bindValue(':encrypt_algorithm',$encrypt_algorithm);
				self::$edit_stmt->bindValue(':hash_algorithm',$hash_algorithm);
				self::$edit_stmt->bindValue(':auth_method',$auth_method);
				self::$edit_stmt->bindValue(':preshared_key',$preshared_key);
				self::$edit_stmt->bindValue(':dh_group',$dh_group);
				self::$edit_stmt->bindValue(':transport',$transport);
				self::$edit_stmt->bindValue(':ipsec_lifetime',$ipsec_lifetime);
				self::$edit_stmt->bindValue(':pfs_group',$pfs_group);
				self::$edit_stmt->bindValue(':ipsec_encrypt_algorithm', 
								$ipsec_encrypt_algorithm);
				self::$edit_stmt->bindValue(':auth_algorithm',$auth_algorithm);
				self::$edit_stmt->bindValue(':enable_compression',$enable_compression);
				self::$edit_stmt->bindValue(':nat_traversal',$nat_traversal);
				self::$edit_stmt->bindValue(':enable_dpd',$enable_dpd);
				self::$edit_stmt->bindValue(':dpd_delay',$dpd_delay);
				self::$edit_stmt->bindValue(':dpd_maxfail',$dpd_maxfail);
				return self::$edit_stmt->execute();
		}

		public function deleteIPSecRule($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM IPSEC_VPN WHERE 
								NAME = :name;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':name', $name);
			return self::$del_stmt->execute();
		}

		public function setVPNEnable($name,$enabled) {
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($name) != 1 ) {
				return FALSE;
			}
			if ( !isset(self::$edit_stmt) ) {
				self::$edit_stmt =
					$dbh->prepare('UPDATE IPSEC_VPN SET ENABLE = :enabled WHERE 
						NAME = :name');
			}
			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':name', $name);
			self::$edit_stmt->bindValue(':enabled', $enabled);
			return self::$edit_stmt->execute();
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

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$res = $dbh->query("SELECT NAME FROM IPSEC_VPN");
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
