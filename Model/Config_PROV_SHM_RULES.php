<?php
	include_once("Control/session_secure.php");
	include_once("shell_command.php");

	class Config_PROV_SHM_RULES {
		//private $MYSQL_PROV_FILE="/var/tmp/siphm.cfg";
		//private $MYSQL_PROV_FILE="/etc/blox/siphm.cfg";
		private $MYSQL_PROV_FILE="/tmp/siphm.cfg";
		private $TRUNK_ID_LIST_FILE= "/tmp/trunk_list";
		private $ROMING_ID_LIST_FILE= "/tmp/roming_list";
		private $SHM_SQL_FILE="/tmp/prov_shm.sql";
		private $T_NAME = array(50=>"PROV_SUBSCRIBER");
		private $DBH;
		private $Retun_val = false;
		private $F_Data;
		private $SHM_RULE = array();

		private $ACTION_a = array(
					"append_time0" => "append_time",
					"remove_hf1" => "remove_hf",
					"append_hf1or2" =>"append_hf",
					"insert_hf1or2" => "insert_hf",
					"append_urihf2" => "append_urihf",
					"append_cturi1" => "append_cturi",
					"append_ctparam1" => "append_ctparam"
					);

		private function run_script() {
			$shell_obj = ShellCommand::getInstance();
			$res1 = $shell_obj->Execute("/usr/bin/generate_siphm_routes.sh", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		}

//RULEID|C_ID|CON_1|CON_12_OTHER1|CON_11_HD|CON_11_HD_PARAMS|CON_11_PATTERN|ACTION|ACT_P1|ACT_P1
//RULEID|Seq|Condition|([HEADER|SIP METHOD|MIME|])|Header|[$ct.fields()]|[PATTERN]|ACTION|PARAM1|PARAM2
//RULEID|Seq|Condition|([HEADER|SIP METHOD|MIME|])|[PATTERN]|ACTION|PARAM1|PARAM2|...

		private function Gen_rules($rid,$rname) {
			$query = "SELECT * FROM SHM_CONDITIONS ";
			$query .= " WHERE NAME='$rname' ORDER BY C_ID asc";
			$result = $this -> DBH -> query($query);
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$con_1 = ($row['CON_1'] == "none") ? "":$row['CON_1'];
				// for condition ([HEADER|SIP METHOD|MIME|]
				if(($row['CON_1'] == "none")) {
					$headercon = "";
				} else if(($row['CON_1'] == "eq") || ($row['CON_1'] == "ne")) {
					$headercon = $row['CON_11_HD'];
				} else {
					$headercon =  $row['CON_12_OTHER1'];
				}
				$con_11_hd_params = ($row['CON_11_HD_PARAMS'] == "none")? "":$row['CON_11_HD_PARAMS'];
				$d_str = $rid."|".$row['C_ID']."|".$con_1;
				$d_str .= "|".$headercon."|".$row['CON_11_PATTERN'];
				$d_str .= "|".$this->ACTION_a[$row['ACTION']]."|";
				$d_str .= $row['ACT_P1'];
				$d_str .= "|".$row['ACT_P2'];
				array_push($this->F_Data, $d_str);
			}
		}

		private function Update_Prov_SHM() {
			$this->F_Data = array();
			$query = "SELECT * FROM SHM_RULES ORDER BY ID asc";
			$result = $this -> DBH -> query($query);
			$r_id=1;
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$this->Gen_rules($r_id,$row['NAME']);
				$r_id++;
			}
			array_push($this->F_Data, "");
			$w_content = implode("\n", array_values($this->F_Data));
			$F = fopen($this -> MYSQL_PROV_FILE, "w");
			fwrite($F, $w_content);
			fclose($F);
			//$this->run_script();
			$this -> Retun_val = true;
		}

		private function getshmIndex($val) {
			$R_val = -1;
			$S_ID = 500;
			$key = array_search($val, $this->SHM_RULE);
			if ($key != false ) {
				$R_val = $S_ID+$key;
			}
			return  $R_val;
		}

		private function getTRKorROMID($farr,$token) {
			$R_VAL="NOT";
			for ($i = 0; $i < count($farr); $i++) {
				$x_data = $farr[$i];
				$f_a_data = explode("|",$x_data);
				if (trim($f_a_data[0]) == $token) {
					return $f_a_data[1];
				}
			}
			return $R_VAL;
		}

		private function assgin_shmrule() {
			$query = "SELECT * FROM SHM_RULES ORDER BY ID";
			$result = $this -> DBH -> query($query);
			$index=1;
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$this->SHM_RULE[$index] = $row['NAME'];
				$index++;
			}
		}

		private function getSHMP_list($d_list) {
			$d_array = explode(",",$d_list);
			$r_value="";
			for ($i=0; $i < count($d_array);$i++) {
				if ($i != 0) {
					$r_value .=":";
				}
				$in_id = $this->getshmIndex($d_array[$i]);
				$r_value .=$in_id;
			}
			return $r_value;
		}

		private function gen_trunk_sql() {
			$query = "SELECT * from TRUNKS_CONFIGURATION where SIPHEADERS !=''";
			if (file_exists($this->TRUNK_ID_LIST_FILE)) {
				$F = fopen($this->TRUNK_ID_LIST_FILE, "r+");
				$f_content = stream_get_contents($F);
				fclose($F);
				$trk_ids = explode("\n",$f_content);
				$result = $this -> DBH -> query($query);
				while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
					$d_str ="INSERT INTO blox_config_ext ";
					array_push($this->F_Data, $d_str);
					$d_str =" (uuid,SHMP) VALUES (";
					array_push($this->F_Data, $d_str);
					$uuid = $this->getTRKorROMID($trk_ids,$row['NAME']);
					$shmp = $this->getSHMP_list($row['SIPHEADERS']);
					$d_str = "'".$uuid."','".$shmp."'";
					array_push($this->F_Data, $d_str);
					$d_str =");";
					array_push($this->F_Data, $d_str);
				}
			}
		}

		private function gen_rom_sql() {
			$query = "SELECT * from ROAMING_USERS where SIPHEADERS !=''";
			if (file_exists($this->ROMING_ID_LIST_FILE)) {
				$F = fopen($this->ROMING_ID_LIST_FILE, "r+");
				$f_content = stream_get_contents($F);
				fclose($F);
				$trk_ids = explode("\n",$f_content);
				$result = $this -> DBH -> query($query);
				while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
					$d_str ="INSERT INTO blox_config_ext ";
					array_push($this->F_Data, $d_str);
					$d_str =" (uuid,SHMP) VALUES (";
					array_push($this->F_Data, $d_str);
					$uuid = $this->getTRKorROMID($trk_ids,$row['NAME']);
					$shmp = $this->getSHMP_list($row['SIPHEADERS']);
					$d_str = "'".$uuid."','".$shmp."'";
					array_push($this->F_Data, $d_str);
					$d_str =");";
					array_push($this->F_Data, $d_str);
				}
			}
		}

		private function gen_provSQL() {
/*
			$this->F_Data = array();
			$d_str ="LOCK TABLES blox_config_ext WRITE;";
			array_push($this->F_Data, $d_str);
			$this->assgin_shmrule();
			//$this->gen_trunk_sql();
			//$this->gen_rom_sql();
			$d_str = "UNLOCK TABLES;";
			array_push($this->F_Data, $d_str);
			$w_content = implode("\n", array_values($this->F_Data));
			$F = fopen($this -> SHM_SQL_FILE, "w");
			fwrite($F, $w_content);
			fclose($F);
*/
			$this -> Retun_val = true;
		}

		public function gen_SQL($db, $t_name) {
			$this -> DBH = $db;
			$this ->gen_provSQL();
			return $this -> Retun_val;
		}

		public function config_update($db, $t_name) {
			//if (array_key_exists($t_name,$this->T_NAME)){
				$this -> DBH = $db;
				$this -> Update_Prov_SHM();
			//}
			return $this -> Retun_val;
		}
	}

?>
