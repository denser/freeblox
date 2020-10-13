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

	class SipSecuritySettingsModel {
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

		public static $ENABLE_AUTO_DISCOVERY = 'EnableAutoDiscovery';
		public static $SIP_TRANSPORT = 'SipTransport';
		public static $SIP_PORTS = 'SipPorts';
		public static $DYNAMIC_MEDIA_PORTS = 'DynamicMediaPorts';
		public static $MEDIA_TRANSPORT = 'MediaTransport';
		public static $MEDIA_PORTS = 'MediaPorts';
		public static $MAX_SESSIONS = 'MaxSessions';
		public static $MAX_DIALOGS = 'MaxDialogs';
		public static $MAX_URI_LEN = 'MaxUriLen';
		public static $MAX_CALLID = 'MaxCallId';
		public static $MAX_REQ_LEN = 'MaxReqLen';
		public static $MAX_FROM_LEN = 'MaxFromLen';
		public static $MAX_TO_LEN = 'MaxToLen';
		public static $MAX_VIA_LEN = 'MaxViaLen';
		public static $MAX_CONTACT_LEN = 'MaxContactLen';
		public static $MAX_CONTENT_LEN = 'MaxContentLen';
		public static $ALLOW_HTTP = "AllowHttp";
		public static $HTTP_PORTS = "HttpPorts";
		public static $ALLOW_SSH = "AllowSSH";
		public static $SSH_PORTS = "SSHPorts";
		public static $SIP_METHODS = "SIPMethods";
		public static $ALLOW_ICMP = "AllowICMP";
		private static $sipMethods = array("invite","cancel","ack","bye","register",
						"options","refer","subscribe","update","join",
						"info","message","notify","benotify","do","qauth",
						"sprack","publish","service","unsubscribe","prack");

		public function get($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
			self::$get_stmt = $dbh->prepare('SELECT VAL FROM SIP_SECURITY_SETTINGS 
						WHERE NAME = :name;');
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
				self::$set_stmt = $dbh->prepare('UPDATE SIP_SECURITY_SETTINGS SET 
						VAL = :val WHERE NAME = :name;');
			}
			self::$set_stmt->reset();
			self::$set_stmt->bindValue(':val', $val);
			self::$set_stmt->bindValue(':name', $name);
			$result = self::$set_stmt->execute();
			return $result;
		}

		public function getSIPmethods() {
			return SipSecuritySettingsModel::$sipMethods;
		}

		public static function serializeSIPMethods($args) {
			return implode(",", $args);
		}

		public static function unSerializeSIPMethods($val) {
			return explode(",", $val);
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
