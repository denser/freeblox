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
?>

<?php
	include_once('Model/db_mgr.php');

	class DateTimeConfig {
		private static $instance = null;
		private static $zone_file = "/etc/tzlist";

		public static function getConfig() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function __construct() {
		}

		public function getDTConfig() {
			$dbh = DB_Handle::Connect();

			$result = $dbh->query("SELECT * FROM DATETIME_SETTINGS");
			return $result;
		}

		public function setDTConfig($data_array) {
			$dbh = DB_Handle::Connect();
			$dbhp = DB_Handle_Persist::Connect();
			$old_data = $this->getDTConfig();
			$r = $old_data->fetchArray(SQLITE3_ASSOC);
			$old_compare = 0;
			foreach ($r as $old_key => $old_value) {
				if(array_key_exists($old_key, $data_array)) {
					if($r[$old_key] != $data_array[$old_key]) {
						$old_compare = 1;
						break;
					}
				}
			}
			$date_change = "";
			if($old_compare == 0 && $date_change == "false") {
				return;
			}
			$timezone = $data_array["TIMEZONE"];
			$configtype = $data_array["CONFIGTYPE"];
			$ntpserver = $data_array["NTPSERVER"];
			$query = "UPDATE DATETIME_SETTINGS SET TIMEZONE = '$timezone', CONFIGTYPE = '$configtype', NTPSERVER = '$ntpserver'";
			$dbh->query($query);
	                if($dbhp) $dbhp->query($query);
		}

		public function getActionListCount() {
			$dbh = DB_Handle::Connect();
			return $dbh->getActionListCount();
		}

		public function getTimeZoneList() {
			if(!file_exists(self::$zone_file))
				return array();
			$handle = fopen(self::$zone_file,"r");
			if($handle) {
				$i=0;
				while(!feof($handle)) {
					$temp=explode("|",fgets($handle, 4096));
					$zone_arr[$i]=$temp[0];
					$i++;
				}
				fclose($handle);
			}
			return $zone_arr;
		}
	}
?>
