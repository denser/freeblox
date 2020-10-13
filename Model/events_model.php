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

	class EventsSettingsModel {
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

		public static $ID= 'ID';
		public static $FUSER= 'FUSER';
		//public static $TUSER = 'TUSER';
		public static $RUSERPROFILE = 'RUSERPROFILE';
		public static $SUB_LIST = 'SUB_LIST';
		public static $EVENTS = 'EVENTS';
		public static $AC_PKT = 'AC_PKT';
		public static $EXPIRE = 'EXPIRE';
		

// network device columns
		public function getAlleventsSettings() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$query="select ID, FUSER, RUSERPROFILE, SUB_LIST, EVENTS, ";
				$query.="AC_PKT, EXPIRE from PRESENCE_EVENTS";
				self::$get_stmt = $dbh->prepare($query);
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function geteventsSettings($id) {
			$dbh = DB_Handle::Connect();
			$query = "select FUSER, RUSERPROFILE, SUB_LIST, EVENTS, AC_PKT, EXPIRE 
				from PRESENCE_EVENTS WHERE ID =".$id;
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt =  $dbh->query($query);
			}
			$row = self::$get_stmt-> fetchArray();
			return  $row;
		}

		public function checkIfRuleExists($fuser) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from PRESENCE_EVENTS where FUSER = '$fuser'";
			return $dbh->querySingle($query);
		}

		public function addeventsSettings ($fuser, $ruserprofile, $sub_list, $events, $ac_pkt,$expire) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$add_stmt) ) {
				$query = "insert into PRESENCE_EVENTS (FUSER,RUSERPROFILE, ";
				$query .= "SUB_LIST,EVENTS,AC_PKT,EXPIRE) ";
				$query .= "values( :fuser, :ruserprofile, ";
				$query .= ":sub_list, :events, :ac_pkt, :expire)";
				self::$add_stmt =$dbh->prepare($query);
			}
			self::$add_stmt->reset();
			self::$add_stmt->bindValue(':fuser', $fuser);
			self::$add_stmt->bindValue(':ruserprofile', $ruserprofile);
			self::$add_stmt->bindValue(':sub_list', $sub_list);
			self::$add_stmt->bindValue(':events', $events);
			self::$add_stmt->bindValue(':ac_pkt', $ac_pkt);
			self::$add_stmt->bindValue(':expire', $expire);
			return self::$add_stmt->execute();
		}

//fusername,roaminguserprofile,subscriberlist,events,acpackets,expire
		public function editeventsSettings ($id,$fuser, $ruserprofile, $sub_list, $events, $ac_pkt,$expire){
			$query = "update PRESENCE_EVENTS set FUSER = '$fuser', 
				RUSERPROFILE = '$ruserprofile', SUB_LIST= '$sub_list', 
				EVENTS= '$events', AC_PKT= '$ac_pkt', EXPIRE= '$expire' 
				where ID = $id";
			$dbh = DB_Handle::Connect();
			$dbh->query($query);
			return 1;
		}

		public function deleteeventsSettings ( $id ) {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('delete from PRESENCE_EVENTS 
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
			$result = $dbh->query("select FUSER from PRESENCE_EVENTS");
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

		public function getroamuserprofile() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select NAME from ROAMING_USERS where PRESENCE = 1;");
			$i = 0;
			$devidsProfilesArr = array();
			while ($row = $result->fetchArray()) {
				$devidsProfilesArr[$i] = $row[0];
				$i++;
			}
			$count = count($devidsProfilesArr);
			if ($count > 0 ) {
				return $devidsProfilesArr;
			} else {
				return $devidsProfilesArr;
			}
		}

		public function getroaminguserslist($roaminguser) {
			$retarray = array();
			$dbh = DB_Handle::Connect();
			$query = "select USERNAME from SUBSCRIBERS ";
			$query .="where ROAMUSERPROFILE = '$roaminguser'";
			$result = $dbh->query($query);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($retarray,$row['USERNAME']);
			}
			return $retarray;
		}

		public static function getFromUsers() {
			$retarray = array();
			$dbh = DB_Handle::Connect();
			$query = "select USERNAME from SUBSCRIBERS where OPERATOR = 1";
			$result = $dbh->query($query);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($retarray,$row['USERNAME']);
			}
			return $retarray;
		}

		public static function getRomaingUser() {
			$retarray = array();
			$dbh = DB_Handle::Connect();
			$query = "select USERNAME, ROAMUSERPROFILE from SUBSCRIBERS;";
			$result = $dbh->query($query);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				$res = $row['USERNAME']."|".$row['ROAMUSERPROFILE'];
				array_push($retarray,$res);
			}
			return $retarray;
		}

		public static function serializeSIPMethods($args) {
			return implode(",", $args);
		}

		public static function unSerializeSIPMethods($val) {
			return explode(",", $val);
		}

		public function getdeviceids() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT I_TYPE,MAC_ID,T_CODE,DEV_ID FROM NETWORK_DEVICE");
			return $result;
		}
}
