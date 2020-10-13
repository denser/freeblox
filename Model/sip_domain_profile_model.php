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

	class SipDomainSettingsModel {
		private static $instance = null;
		private static $get_stmt = null;
		private static $add_stmt = null;
		private static $edit_stmt = null;
		private static $del_stmt = null;

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public static $ID = 'ID';
		public static $SIP_DOMAIN = 'SIP_DOMAIN';
		public static $METHODS = 'METHODS';
		public static $DST_URI = 'DST_URI';
		public static $BIND_PORT = 'BIND_PORT';
		public static $BIND_HOST = 'BIND_HOST';

		public function getAllsipdomainSettings() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$query= "select ID,SIP_DOMAIN,METHODS,DST_URI,BIND_PORT,BIND_HOST ";
				$query.= "from SIP_DOMAIN_PROFILES ";
				self::$get_stmt = $dbh->prepare($query);
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getsipdomainsettings($id) {
			$dbh = DB_Handle::Connect();
			$query = " SELECT SIP_DOMAIN,METHODS,DST_URI,BIND_PORT,BIND_HOST ";
			$query .="FROM SIP_DOMAIN_PROFILES WHERE ID =".$id ;
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt =  $dbh->query($query);
			}
			$row = self::$get_stmt-> fetchArray();
			return $row;
		}

		public function checkIfRuleExists($username) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from LB_RESOURCE_PROFILE where RESOURCE_NAME = '$username'";
			return $dbh->querySingle($query);
		}

		public function addsipdomainSettings ( $sipdomain, $methods, $desuri ,$bind_port, $bind_host) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$add_stmt) ) {
				$query = "insert into SIP_DOMAIN_PROFILES ( SIP_DOMAIN, METHODS,";
				$query .= "DST_URI ,BIND_PORT,BIND_HOST) values (:sipdomain, :methods, ";
				$query .= ":desuri, :bind_port, :bind_host)"; 
				self::$add_stmt =$dbh->prepare($query);
			}
			self::$add_stmt->reset();
			self::$add_stmt->bindValue(':sipdomain', $sipdomain);
			self::$add_stmt->bindValue(':methods', $methods);
			self::$add_stmt->bindValue(':desuri', $desuri);
			self::$add_stmt->bindValue(':bind_port', $bind_port);
			self::$add_stmt->bindValue(':bind_host', $bind_host);
			return self::$add_stmt->execute();
		}

		public function editsipdomainSettings ($id,$sipdomain,$method,$dsturi,$bind_port,$bind_host) {
			$query = "update SIP_DOMAIN_PROFILES set SIP_DOMAIN = '$sipdomain' ";
			$query .= ",METHODS ='$method',DST_URI = '$dsturi',BIND_PORT = $bind_port, BIND_HOST = $bind_host ";
			$query .= "WHERE ID = $id ";
			$dbh = DB_Handle::Connect();
			$dbh->query($query);
			return 1;
		}


		public function deletesipdomainSettings ( $id ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('delete from SIP_DOMAIN_PROFILES 
							where ID = :id;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':id', $id);
			return self::$del_stmt->execute();
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($config, $config_str, $activity, $action){
			"<script type=\"text/javascript\">".
			"window.alert('$config $config_str, $activity, $action');".
			"</script>";
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

// to get usernames from subscribers profile list
		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select SIP_DOMAIN from SIP_DOMAIN_PROFILES");
			$i = 0;
			$devicenameProfilesArr = array();
			while ($row = $result->fetchArray()) {
				$devicenameProfilesArr[$i] = $row[0];
				$i++;
			}
			$count = count($devicenameProfilesArr);
			if ($count > 0 ) {
				return $devicenameProfilesArr;
			} else {
				return $devicenameProfilesArr;
			}
		}

		public function getusername($id) {
			$dbh = DB_Handle::Connect();
			$query = "select SIP_DOMAIN from SIP_DOMAIN_PROFILES where ID = $id ";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];	
		}

		public function checksipprofile($id) {
			$dbh = DB_Handle::Connect();
			$username = $this->getusername($id);
			$query = "select count(*) from SIP_PROFILES ";
			$query .= "WHERE SIPDOMAINS LIKE '%,".$username.",%' OR ";
			$query .= " SIPDOMAINS LIKE '".$username.",%' ";
			$query .= "OR SIPDOMAINS LIKE '%,".$username."'";
			$query .= " OR SIPDOMAINS LIKE '".$username."'";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function checkusernameroaming() {
			$retarray = array();
			$dbh = DB_Handle::Connect();
			$query = "select USERNAME, ROAMUSERPROFILE from SUBSCRIBERS ";	
			$result = $dbh->query($query);	
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				$str = $row['USERNAME']."|".$row['ROAMUSERPROFILE'];
				array_push($retarray,$str);
			}
			return $retarray;
		}

		public function getdeviceids() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT I_TYPE,MAC_ID,T_CODE,DEV_ID FROM NETWORK_DEVICE");
			return $result;
		}
	}
