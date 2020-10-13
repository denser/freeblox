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

	class ResourceSettingsModel {
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
		public static $RESOURCE_NAME = 'RESOURCE_NAME';
		public static $DESCRIPTION = 'DESCRIPTION';

		public function getAllsubscribersSettings() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$query= "select ID,RESOURCE_NAME,DESCRIPTION ";
				$query.= "from LB_RESOURCE_PROFILE ";
				self::$get_stmt = $dbh->prepare($query);
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getsubscribersSettings($id) {
			$dbh = DB_Handle::Connect();
			$query = "select ID,RESOURCE_NAME,DESCRIPTION ";
			$query .="from LB_RESOURCE_PROFILE WHERE ID =".$id;
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt =  $dbh->query($query);
			}
			$row = self::$get_stmt-> fetchArray();
			return  $row;
		}

		public function checkIfRuleExists($username) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from LB_RESOURCE_PROFILE where 
				RESOURCE_NAME = '$username'";
			return $dbh->querySingle($query);
		}

		public function addsubscribersSettings ( $resourcename, $desc ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$add_stmt) ) {
				$query = "insert into LB_RESOURCE_PROFILE (RESOURCE_NAME,DESCRIPTION";
				$query .= ") values (:resourcename, :desc)"; 
				self::$add_stmt =$dbh->prepare($query);
			}
			self::$add_stmt->reset();
			self::$add_stmt->bindValue(':resourcename', $resourcename);
			self::$add_stmt->bindValue(':desc', $desc);
			return self::$add_stmt->execute();
		}

		public function editsubscribersSettings ($id, $resource,$desc) {
			$query = "update LB_RESOURCE_PROFILE set RESOURCE_NAME = '$resource',";
			$query .= " DESCRIPTION = '$desc' where ID = $id";
			$dbh = DB_Handle::Connect();
			$dbh->query($query);
			return 1;
		}

		public function deletesubscribersSettings ( $id ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('delete from LB_RESOURCE_PROFILE 
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

		public function createActionList($config, $config_str, $activity, $action) {
			"<script type=\"text/javascript\">".
			"window.alert('$config $config_str, $activity, $action');".
			"</script>";
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}

// to get usernames from subscribers profile list
		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select RESOURCE_NAME from LB_RESOURCE_PROFILE");
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

		public function checksubscriber($id) {
			$dbh = DB_Handle::Connect();
			$username = $this->getusername($id);
			$query = "select count(*) from PRESENCE_EVENTS ";
			$query .= "where SUB_LIST LIKE '%$username%'";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function getusername($id) {
			$dbh = DB_Handle::Connect();
			$query = "select RESOURCE_NAME from LB_RESOURCE_PROFILE where ID = $id ";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];	
		}

		public function getdeviceids() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT I_TYPE,MAC_ID,T_CODE,DEV_ID FROM NETWORK_DEVICE");
			return $result;
		}

}
