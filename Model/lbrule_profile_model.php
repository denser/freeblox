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

		public function getMT($name) {
			$result = $this -> DBH -> query("SELECT * FROM LB_RULES WHERE NAME='$name'");
			return $result;
		}

		public function getCOUNT() {
			$result = $this -> DBH -> query("SELECT count(*) FROM LB_RULES");
			$row = $result->fetchArray();
			return $row[0];
		}

		public function getlbruleList() {
			$qStr = "SELECT * from LB_RULES";
			$result = $this -> DBH -> query($qStr);
			return $result;
		}

		public function getconditions($name) {
			$str = "SELECT * FROM LB_RULES_CONDITIONS";
			$str .= " WHERE NAME= '$name'";
			$result = $this -> DBH -> query($str);
			return $result;
		}

		public function getrulenamelist() {
			$sipnames = array();
			$str= "SELECT NAME FROM LB_RULES ";
			$result = $this -> DBH -> query($str);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($sipnames, $row["NAME"]);
			}
			return $sipnames;
		}

		private function isIntField($field_name) {
			foreach ($this->Int_List as $key => $value) {
				if ($key == strtoupper($field_name)) {
					return 1;
				}
			}
			return 0;
		}

		public function addrules($data_array) {
			$query = "INSERT INTO LB_RULES ";
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

		public function addrulecon($data_array) {
			$query = "INSERT INTO LB_RULES_CONDITIONS ";
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

		public function setrules($name, $data_array) {
			$res_val = $data_array["RESOURCE_NAMES"];
			$group_val = $data_array["LB_GRP_NAME"];
			$query = "UPDATE LB_RULES SET RESOURCE_NAMES = '$res_val',";
			$query .= " LB_GRP_NAME = '$group_val' WHERE NAME = '$name'";
			$this -> DBH -> query($query);	
		}

		public function deletecondition($data_array) {
			$name = $data_array["NAME"];
			$query = "DELETE FROM LB_RULES_CONDITIONS WHERE NAME = '$name'";
			$this -> DBH -> query($query);
		}

		public function getgroupnames() {
			$groupnames = array();
			$str= "select LB_GRP_NAME from LB_GROUP_PROFILE";
			$result = $this -> DBH -> query($str);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($groupnames, $row["LB_GRP_NAME"]);
			}
			return $groupnames;
		}

		public function getresourcenames() {
			$resourcenames = array();
			$str = "select RESOURCE_NAME from LB_RESOURCE_PROFILE";
			$result = $this -> DBH -> query($str);
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				array_push($resourcenames, $row["RESOURCE_NAME"]);
			}
			return $resourcenames;
		}

		public function delRules($name) {
			$result = $this -> DBH -> query("DELETE FROM LB_RULES WHERE NAME='$name'");
			$result = $this -> DBH -> query("DELETE FROM LB_RULES_CONDITIONS 
							WHERE NAME='$name'");
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

		public function createActionList($config, $config_str, $activity, $action) {
			"<script type=\"text/javascript\">".
			"window.alert('$config $config_str, $activity, $action');".
			"</script>"; 
			$dbh = DB_Handle::Connect();
			$dbh->createActionList($config, $config_str, $activity, $action);
		}
	}
?>
