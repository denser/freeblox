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
	include_once('db_mgr.php');

	class ActionMappingModel {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getActionMapping() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM ACTION_MAPPING');
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getActionMappingVal($name) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT VAL FROM ACTION_MAPPING WHERE NAME = :name;');
			}

			self::$get_stmt->reset();

			self::$get_stmt->bindValue(':name', $name);

			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}

		public function getActionMappingName($val) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT NAME FROM ACTION_MAPPING WHERE VAL = :val;');
			}

			self::$get_stmt->reset();

			self::$get_stmt->bindValue(':val', $val);

			return self::$get_stmt->execute()->fetchArray(SQLITE3_ASSOC);
		}
	}

	class SBCInterfaceType {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getSBCInterfaceType()
		{
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) )
			{
				self::$get_stmt = $dbh->prepare('SELECT * FROM SBC_INTERFACE_TYPE');
			}

			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}
	}

	class SBCNatType {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getSBCNatType() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM SBC_NAT_TYPE');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class SBCMediaType {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getSBCMediaType() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM MEDIA_TYPE');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class SBCTransportType {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getSBCTransportType() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM SBC_TRANSPORT_PORT_TYPE');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}


	class SBCTrunkType {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getSBCTrunkType() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM SBC_TRUNK_TYPE');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class IKE_DH_GROUP {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getDHGroups() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM IKE_DH_GROUP');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class IKE_ENCRYPT_ALGORITHM {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getIKEEncryptAlgorithms() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM IKE_ENCRYPT_ALGORITHM');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class HASH_ALGORITHM {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getHashAlgorithms() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM HASH_ALGORITHM');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class IKE_EXCHANGE_MODE {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getIKEExchangeMode() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM IKE_EXCHANGE_MODE');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class AUTH_METHOD {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getAuthenticationMethods() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM AUTH_METHOD');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class IPSEC_ENCRYPT_ALGORITHM {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getIpsecEncryptAlgorithms() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM IPSEC_ENCRYPT_ALGORITHM');			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class IPSEC_AUTH_ALGORITHM {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getIpsecAuthAlgorithms() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM IPSEC_AUTH_ALGORITHM');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class ENABLE_COMPRESSION {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getEnableCompression() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM ENABLE_COMPRESSION');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class NAT_TRAVERSAL {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getNatTraversal() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM NAT_TRAVERSAL');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class IPSEC_PFS_GROUP {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getPfsGroups() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM IPSEC_PFS_GROUP');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}


	class GEN_NAT_Types {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getGen_NatTypes() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM NAT_TYPE');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}

	class LCR_Types {
		private static $instance = null;
		private static $get_stmt = null;

		public static $NAME = 'NAME';
		public static $VAL = 'VAL';

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function getLCR_Types() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt = $dbh->prepare('SELECT * FROM LCR_TYPE');
			}

			self::$get_stmt->reset();

			return self::$get_stmt->execute();
		}
	}
?>
