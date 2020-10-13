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

	class ManipulationTable {
		private static $instance = null;
		private $DBH;
		private $Int_List = array("SEQ_ID" => "INT", 
					"INPUT_TYPE" => "INT", 
					"OUTPUT_TYPE" => "INT");
		private $createdDataNames = array();
		private $usedMT = array();

		public static function getConfig() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function __construct() {
			$this -> DBH = DB_Handle::Connect();
		}

		public function get_group_profile($name) {
			$result = $this -> DBH -> query("SELECT * FROM LB_GROUP_PROFILE WHERE 
							LB_GRP_NAME='$name'");
			return $result;
		}

		public function getCOUNT() {
			$result = $this -> DBH -> query("SELECT count(*) FROM LB_GROUP_PROFILE ");
			$row = $result->fetchArray();
			return $row[0];
		}

		public function getgroupList() {
			$qStr = "SELECT * from LB_GROUP_PROFILE";
			$result = $this -> DBH -> query($qStr);
			return $result;
		}

		public function getgroupdesurilist($groupid) {
			$str= "SELECT * FROM LB_GROUP_DSTURI ";
			$str .= " WHERE G_ID = $groupid";
			$result = $this -> DBH -> query($str);
			return $result;
		}

		public function getgroup_resourcelist($resourceid) {
			$str= "SELECT * FROM LB_GROUP_RESOURCE ";
			$str .= " WHERE R_ID = $resourceid";
			$result = $this -> DBH -> query($str);
			
			return $result;
		}

		public function getgroupnamelist() {
			$sipnames = array();
			$str= "SELECT LB_GRP_NAME FROM LB_GROUP_PROFILE ";
			$result = $this -> DBH -> query($str);	
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($sipnames, $row["LB_GRP_NAME"]);
			}
			return $sipnames;
		}

		public function getresourcenames() {
			$resnames = array();
			$str= "SELECT RESOURCE_NAME FROM LB_RESOURCE_PROFILE ";	
			$result = $this -> DBH -> query($str);	
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($resnames, $row["RESOURCE_NAME"]);
			}
			return $resnames;
		}

		private function isIntField($field_name) {
			foreach ($this->Int_List as $key => $value) {
				if ($key == strtoupper($field_name)) {
					return 1;
				}
			}
			return 0;
		}

		public function add_group_profile($data_array) {
			$query = "INSERT INTO LB_GROUP_PROFILE ";
			$q_key = "";
			$q_val = "";
			$flag = 0;
			foreach ($data_array as $key => $value) {
				$separator = ",";
				$str_tag = "'";
				if ($flag == 0) {
					$separator = " ";
				}
				if ($this -> isIntField($key) == 1) {
					$str_tag = "";
				}
				$q_key = $q_key . $separator . $key;
				$q_val = $q_val . $separator . $str_tag . $value . $str_tag;
				$flag = 1;
			}
			$query = $query . " (" . $q_key . " ) VALUES (" . $q_val . " )";
			$this -> DBH -> query($query);
			$groupid = $this->getgid($data_array["LB_GRP_NAME"]);
			return $groupid;
		}

		public function add_group_dsturi($data_array) {	
			$query = "INSERT INTO LB_GROUP_DSTURI ";
			$q_key = "";
			$q_val = "";
			$flag = 0;
			foreach ($data_array as $key => $value) {
				$separator = ",";
				$str_tag = "'";
				if ($flag == 0) {
					$separator = " ";
				}
				if ($this -> isIntField($key) == 1) {
					$str_tag = "";
				}
				$q_key = $q_key . $separator . $key;
				$q_val = $q_val . $separator . $str_tag . $value . $str_tag;
				$flag = 1;
			}
			$query = $query . " (" . $q_key . " ) VALUES (" . $q_val . " )";
			$this -> DBH -> query($query);
			$resource_id = $this->getmaxrid();
			return $resource_id;
		}

		public function add_group_resource($data_array) {
			$query = "INSERT INTO LB_GROUP_RESOURCE ";
			$q_key = "";
			$q_val = "";
			$flag = 0;
			foreach ($data_array as $key => $value) {
				$separator = ",";
				$str_tag = "'";
				if ($flag == 0) {
					$separator = " ";
				}
				if ($this -> isIntField($key) == 1) {
					$str_tag = "";
				}
				$q_key = $q_key . $separator . $key;
				$q_val = $q_val . $separator . $str_tag . $value . $str_tag;
				$flag = 1;
			}
			$query = $query . " (" . $q_key . " ) VALUES (" . $q_val . " )";
			$this -> DBH -> query($query);
		}

		private function getmaxrid() {
			$query= "select max(R_ID) from LB_GROUP_DSTURI";
			$result = $this -> DBH -> query($query);
			$row = $result->fetchArray();
			return $row[0];
		}

		public function set_group_profile($name, $data_array) {
			$desc = $data_array["LB_GRP_DESC"];
			$query = "UPDATE LB_GROUP_PROFILE SET LB_GRP_DESC = '$desc'";
			$query .=" WHERE LB_GRP_NAME = '$name'";
			$this -> DBH -> query($query);
			$groupid = $this->getgid($data_array["LB_GRP_NAME"]);
			return $groupid;
		}

		public function delete_DESURI_RES($data_array) {
			$name = $data_array["LB_GRP_NAME"];
			$this -> DBH -> query($query); */
			$gid=$this->getgid($name);
			$rid=$this->getrid($gid);
			$result = $this -> DBH -> query("DELETE FROM LB_GROUP_DSTURI WHERE G_ID=$gid");
			foreach ($rid as $key => $value) {
				$result = $this -> DBH -> query("DELETE FROM LB_GROUP_RESOURCE 
								WHERE R_ID=$value");
			}
		}

		public function delGroup($name) {
			$gid=$this->getgid($name);
			$rid=$this->getrid($gid);
			$result = $this -> DBH -> query("DELETE FROM LB_GROUP_PROFILE 
							WHERE LB_GRP_NAME='$name'");
			$result = $this -> DBH -> query("DELETE FROM LB_GROUP_DSTURI 
							WHERE G_ID=$gid");
			foreach ($rid as $key => $value) {
			$result = $this -> DBH -> query("DELETE FROM LB_GROUP_RESOURCE 
							WHERE R_ID=$value");
			}
		}

		private function getgid($name) {
			$query= "select G_ID from LB_GROUP_PROFILE where LB_GRP_NAME = '$name'";
			$result = $this -> DBH -> query($query);
			$row = $result->fetchArray();
			return $row[0];
		}

		private function getrid($id) {
			$resid = array();
			$query= "select R_ID from LB_GROUP_DSTURI where G_ID='$id'";
			$result = $this -> DBH -> query($query);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($resid, $row["R_ID"]);
			}
			return $resid;
		}

		public function gettrunkroamdepdency($sipname) {
			$dpcount = 0;
			$query = "select count(*) from ROAMING_USERS ";
			$query .= "WHERE SIPHEADERS LIKE '%,".$sipname.",%' OR ";
			$query .= " SIPHEADERS LIKE '".$sipname.",%' ";
			$query .= "OR SIPHEADERS LIKE '%,".$sipname."'";
			$query .= " OR SIPHEADERS LIKE '".$sipname."'";
			$result = $this -> DBH -> query($query);
			$row = $result->fetchArray();
			$dpcount = $dpcount + $row[0];
			$query = "select count(*) from TRUNKS_CONFIGURATION ";
			$query .= "WHERE SIPHEADERS LIKE '%,".$sipname.",%' OR ";
			$query .= " SIPHEADERS LIKE '".$sipname.",%' ";
			$query .= "OR SIPHEADERS LIKE '%,".$sipname."'";
			$query .= " OR SIPHEADERS LIKE '".$sipname."'";
			$result = $this -> DBH -> query($query);
			$row1 = $result->fetchArray();
			$dpcount = $dpcount + $row1[0];
			return $dpcount;
		}

		public function updateActionList($config, $config_str, $activity, $action) {
			$dbh = DB_Handle::Connect();
			$dbh->updateActionList($config, $config_str, $activity, $action);
		}

		public function createActionList($configID, $config_str, $activity, $action) {
			"<script type=\"text/javascript\">".
			"window.alert('$config $config_str, $activity, $action');".
			"</script>"; 
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}
	}
?>
