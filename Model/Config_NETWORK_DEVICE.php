<?php
	include_once("Control/session_secure.php");
	include_once("shell_command.php");
	require_once("db_mgr.php");

	class Config_NETWORK_DEVICE {

		private static $DB = null;
		private static $instance = null;
		private $TRANCODE_FILE = "/etc/.tg_if_list";
		//private $Sys_IPLINK = "ip link | egrep -A2 'state (UP|DOWN)'";
		private $Sys_IPLINK = "ip link |  sed 'N;s/\\n/ /g' | grep qlen | awk '{print $2,$13}'";
		private $Sys_IPADDR = "ip addr";
		private $data = array();

		public static function Connect() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function __construct() {
			$this->DB = &DB_Handle::Connect();
		}

		private function Sys2DB($s_cmd) {
			$shell_obj = ShellCommand::getInstance();
			$shell_status_sys = $shell_obj->Execute($s_cmd,ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if($shell_status_sys == 0) {
				$s_out = $shell_obj->getLastOutput();
			}
			$this->data = explode("\n",$s_out);
/*
			for ($i=0; $i < count($this->data)-1; $i++) {
				$str_first = explode(":",$this->data[$i]);
				if (trim($str_first[1]) != "lo"){
					$t_data = trim($this->data[$i+1]);
					$sec_str = explode(" ",$t_data);
					$db_id = $this->get_MaxNetworkID();
					$str_d=$str_first[1];
					if (strstr($str_d,".")) {
						$f_str_arr = explode(".",$str_d);
						$str_d=$f_str_arr[0];
					}
					$this->insert_Network($db_id+1,trim($str_d),trim($sec_str[1]));
				}
				$i++;
			}
*/
			for ($i=0; $i < count($this->data)-1; $i++) {
				$token = explode(" ",$this->data[$i]);
				$dev_id = trim(str_replace(":","",$token[0]));
				if ($dev_id !="") {
					$mac_id = trim($token[1]);
					$db_id = $this->get_MaxNetworkID();
					$this->insert_Network($db_id+1,trim($dev_id),trim($mac_id));
				}
			}
		}

		private function insert_Network($Id,$devid,$macid) {
			$macid = strtoupper($macid);
			if ($this->isMACExist($macid) == 0 ) {
				$query = "INSERT INTO NETWORK_DEVICE (ID,DEV_ID,MAC_ID) VALUES (";
				$query .= "$Id,'$devid','$macid')";
				$result = $this->DB->query($query);
			} else {
				$query = "UPDATE NETWORK_DEVICE SET DEV_ID='$devid' WHERE ";
				$query .= " MAC_ID='$macid'";
				$result = $this->DB->query($query);
			}
		}

		private function get_MaxNetworkID() {
			$result = $this->DB->query("SELECT IFNULL (max(ID),0) FROM NETWORK_DEVICE");
			$row = $result->fetchArray();
			return $row[0];
		}

		private function clear_Network($flg) {
			if ($flg ==0 ) {	// force to delete
				$result = $this->DB->query("DELETE FROM NETWORK_DEVICE");
			} else {
				$query = "DELETE FROM NETWORK_DEVICE  WHERE MAC_ID NOT IN ";
				$query .= " ( select DEV_ID from INTERFACE_LIST)";
				$result = $this->DB->query($query);
				$delEmptyMac = "DELETE FROM INTERFACE_LIST WHERE DEV_ID = ''";
				$result = $this->DB->query($delEmptyMac);
			}
		}

		private function isMACExist($mid) {
			$result = $this->DB->query("SELECT count(*) FROM NETWORK_DEVICE WHERE MAC_ID='$mid' ");
			$row = $result->fetchArray();
			return $row[0];
		}

		private function getTrnsIPNETMASK() {
			$query = "SELECT a.IP_ADDR, a.NETMASK from INTERFACE_LIST a,";
			$query .= " NETWORK_DEVICE b where b.T_CODE=1 and b.MAC_ID=a.DEV_ID";
			$result = $this->DB->query($query);
			$row = $result->fetchArray();
			return $row;
		}

		private function update_TRCODE2DB($macid) {
			$macid = strtoupper($macid);
			//echo "\n Rec MID:$macid";
			$query = "UPDATE NETWORK_DEVICE SET T_CODE=1 WHERE ";
			$query .= " MAC_ID='$macid'";
			//echo"\n$query\n";
			$result = $this->DB->query($query);
/*
			$trn_row = $this->getTrnsIPNETMASK();
			$T_IP= $trn_row[0];
			$T_NM= $trn_row[1];

			$query = "UPDATE DEVICE_SETTINGS set VAL=1 where NAME='EnableTranscoding'";
			$result = $this->DB->query($query);

			$query = "UPDATE DEVICE_SETTINGS set VAL='$T_IP' where NAME='TranscodingIP'";
			$result = $this->DB->query($query);

			$query = "UPDATE DEVICE_SETTINGS set VAL='$T_NM' where NAME='TranscodingNetmask'";
			$result = $this->DB->query($query);
			
*/
			//echo "\n Reee :$result";
		}

		private function reset_TRCODE2DB() {
			$query = "UPDATE NETWORK_DEVICE SET T_CODE=0 ";
			$result = $this->DB->query($query);
/*
			$query = "UPDATE DEVICE_SETTINGS set VAL=0 where NAME='EnableTranscoding'";
			$result = $this->DB->query($query);

			$query = "UPDATE DEVICE_SETTINGS set VAL='' where NAME='TranscodingIP'";
			$result = $this->DB->query($query);

			$query = "UPDATE DEVICE_SETTINGS set VAL='' where NAME='TranscodingNetmask'";
			$result = $this->DB->query($query);
*/
		}

		private function Detect_Transcode() {
			if (file_exists($this->TRANCODE_FILE)) {
				$F = fopen($this->TRANCODE_FILE, "r+");
				$content = stream_get_contents($F);
				fclose($F);
				$lines = explode(PHP_EOL, $content);
				foreach($lines as $line) {
					$t_data = trim($line);
					if ($t_data != "") {
						$d_arry = explode("HWaddr",$t_data);
						//echo "a :".$d_arry[0]." b:".$d_arry[1];
						$this->update_TRCODE2DB(trim($d_arry[1]));
					}
				}
			}
		}

///// Calling function begin////////////////

		public function init_NetWork() {
			$this->clear_Network(1);
			$this->Sys2DB($this->Sys_IPLINK);
			$this->reset_TRCODE2DB();
			$this->Detect_Transcode();
		}

		public function reset_Network() {
			$this->clear_Network(0);
			$this->init_NetWork();
		}

		public function getNetwork_List() {
			$result = $this->DB->query("SELECT * FROM NETWORK_DEVICE");
			return $result;
		}
///// Calling function end ////////////////
	}

	//$x = Config_NETWORK_DEVICE::Connect();
	//$x->init_NetWork();
	//echo "OK";
?>


