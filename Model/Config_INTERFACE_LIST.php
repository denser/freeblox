<?php
	include_once("Control/session_secure.php");
	include_once("shell_command.php");

	class Config_INTERFACE_LIST {
		private $INTERFACE_CONF_FILE = "/etc/sysconfig/network-scripts/ifcfg-";
		private $DNS_FILE ="/etc/resolv.conf";
		private $T_NAME = array(48=>"INTERFACE_LIST");
		private $DBH;
		private $Return_val = false;
		private $down_cmd="";
		private $up_cmd="";

		private function get_InterfaceID($macid) {
			$r_value="NOT_FOUND";
			$result = $this->DBH->query("SELECT DEV_ID FROM NETWORK_DEVICE WHERE MAC_ID='$macid'");
			if (count($result) !=0) {
				$row = $result->fetchArray();
				$r_value= $row[0];
			}
			return $r_value;
			//return "eth0";
		}
/*
		private function netmask2cidr($netmask) {
			$cidr = 0;
			foreach (explode('.', $netmask) as $number) {
				for (;$number> 0; $number = ($number <<1) % 256) {
					$cidr++;
				}
			}
			return $cidr;
		}
*/
		private function set_InterFile($row) {
			$inter_id= $this->get_InterfaceID($row['DEV_ID']);
			//echo "\n InterID :$inter_id\n";

			if ($inter_id != "NOT_FOUND" ) {
				$F_Data = array();
				$d_str="DEVICE=".$inter_id; 
				array_push($F_Data, $d_str);
				$d_str="ONBOOT=yes"; 
				array_push($F_Data, $d_str);
				$d_str="NM_CONTROLLED=yes"; 
				array_push($F_Data, $d_str);
				$d_str="TYPE=Ethernet"; 
				array_push($F_Data, $d_str);
				if ($row['IP_TYPE']==1) {
					$d_str="BOOTPROTO=dhcp"; 
					array_push($F_Data, $d_str);
				}else {
					$d_str="BOOTPROTO=static"; 
					array_push($F_Data, $d_str);
					$d_str="IPADDR=".$row['IP_ADDR'];
					array_push($F_Data, $d_str);
					$d_str="NETMASK=".$row['NETMASK'];
					array_push($F_Data, $d_str);
					if($row['GATEWAY'] !="") {
						$d_str="GATEWAY=".$row['GATEWAY']; 
						array_push($F_Data, $d_str);
					}
				}
				$d_str="VLAN=no";
				array_push($F_Data, $d_str);
				$w_content = implode("\n", array_values($F_Data));
				$file_name = $this->INTERFACE_CONF_FILE.$inter_id;
				$F = fopen($file_name, "w");
				fwrite($F, $w_content);
				fclose($F);
				// system command //
				//$cmd_1 = "ifdown ".$inter_id."&";
				//$cmd_2 = "ifup ".$inter_id."&";
				//shell_exec($cmd_1);
				//shell_exec($cmd_2);
				//echo "fname $file_name\n";
					if($this->up_cmd !="") {
						$this->up_cmd .=";";
					}
				$this->up_cmd .="ifup ".$inter_id;
			}
		}

		private function ClearInterface() {
			$result = $this -> DBH -> query("SELECT * FROM NETWORK_DEVICE");
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$d_id=$row['DEV_ID'];
				$f_name = $this->INTERFACE_CONF_FILE.$d_id;
				if(file_exists($f_name)) {
					unlink($f_name);
				}
				if ($this->down_cmd !="") {
					$this->down_cmd .=";";
				}
				$this->down_cmd .= "ifdown ".$d_id;
			}
		}

		private function setDefaultGW_CMD($deGw,$InerfaceID) {
			$shell_obj = ShellCommand::getInstance();
			$get_OldGw_cmd ="route -n | awk '{print $2}' | tail -1";
			$shell_status_route = $shell_obj->Execute($get_OldGw_cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($shell_status_route == 0) {
				$oldGW = $shell_obj->getLastOutput();
			}
			if ($deGw != $oldGW) {
				$res1 = $shell_obj->Execute("route del default gw $oldGW", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			}
			$res2 = $shell_obj->Execute("route add default gw $deGw", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		}

		private function SysCmdExe() {
			$shell_obj = ShellCommand::getInstance();
			//echo "$this->down_cmd\n";
			//echo "$this->up_cmd\n";
			$res1 = $shell_obj->Execute($this->down_cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			sleep(2);
			$res2 = $shell_obj->Execute($this->up_cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		}

		private function setDns() {
			$F_Data = array();
			$d_str="";
			array_push($F_Data, $d_str);
			$result = $this -> DBH -> query("SELECT DNS FROM INTERFACE_LIST WHERE DNS !='' ");
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$d_str="nameserver ".$row['DNS'];
				array_push($F_Data, $d_str);
			}
			$w_content = implode("\n", array_values($F_Data));
			$file_name = $this->DNS_FILE;
			$F = fopen($file_name, "w");
			fwrite($F, $w_content);
			fclose($F);
		}

		
		private function setDefaultGW() {
			$query = "SELECT count(*) FROM INTERFACE_LIST a, ";
			$query .="NETWORK_DEVICE b WHERE a.DEV_ID=b.MAC_ID and ";
			$query .=" a.IP_TYPE=0 AND b.T_CODE=0 AND a.DEF_GWT=1";
			$result = $this -> DBH -> query($query);
			$row = $result -> fetchArray(SQLITE3_ASSOC);
			if ($row[0] !=0 ) {
				$query = "SELECT a.GATEWAY,b.DEV_ID FROM INTERFACE_LIST a, ";
				$query .="NETWORK_DEVICE b WHERE a.DEV_ID=b.MAC_ID and ";
				$query .=" a.IP_TYPE=0 AND b.T_CODE=0 AND a.DEF_GWT=1";
				$result = $this -> DBH -> query($query);
				$r1 = $result -> fetchArray(SQLITE3_ASSOC);
				$deGw =$r1['GATEWAY'];
				$InerfaceID=$r1['DEV_ID'];
				$this->setDefaultGW_CMD($deGw,$InerfaceID);
			}
		}

		private function cen_Update_Interface() {
			$this-> ClearInterface();
			$result = $this -> DBH -> query("SELECT * FROM INTERFACE_LIST");
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$this->set_InterFile($row);
			}
			$this->setDns();
			$this->setDefaultGW();
			$this->SysCmdExe();
			$this -> Return_val = true;
		}


// for ubuntu os update begin
		private function ubu_Update_Interface() {
			$inface_file ="/etc/network/interfaces";
			$F_Data = array();
			$d_str="auto lo";
			array_push($F_Data, $d_str);
			$d_str="iface lo inet loopback";
			array_push($F_Data, $d_str);
			$d_str="";
			array_push($F_Data, $d_str);

			$result = $this -> DBH -> query("SELECT * FROM INTERFACE_LIST  order by DEF_GWT desc");
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				//$this->set_InterFile($row);
				$inter_id= $this->get_InterfaceID($row['DEV_ID']);
				if ($inter_id != "NOT_FOUND" ) {
					if ($row['IP_TYPE']==1) {	// dhcp
						$d_str="auto ".$inter_id;
						array_push($F_Data, $d_str);
						$d_str="iface ".$inter_id." inet dhcp";
						array_push($F_Data, $d_str);
						$d_str="";
						array_push($F_Data, $d_str);
					}else {
						$d_str="auto ".$inter_id;
						array_push($F_Data, $d_str);
						$d_str="iface ".$inter_id." inet static";
						array_push($F_Data, $d_str);

						$d_str="address ".$row['IP_ADDR'];
						array_push($F_Data, $d_str);
						$d_str="netmask ".$row['NETMASK'];
						array_push($F_Data, $d_str);

						if($row['GATEWAY'] !="") {
							$d_str="gateway ".$row['GATEWAY'];
							array_push($F_Data, $d_str);
						}
						if ($row['DNS'] !="") {
							$d_str="dns-nameservers ".$row['DNS'];
							array_push($F_Data, $d_str);
						}
						$d_str="";
						array_push($F_Data, $d_str);
					}
				}
			}	// while end

			$w_content = implode("\n", array_values($F_Data));
			$file_name = $inface_file;
			$F = fopen($file_name, "w");
			fwrite($F, $w_content);
			fclose($F);

			$in_cmd="/etc/init.d/networking restart";

			$shell_obj = ShellCommand::getInstance();
			$in_res= $shell_obj->Execute($in_cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			$this -> Return_val = true;
		}
// for ubuntu os update end

		private function Update_Interface() {
			$shell_obj = ShellCommand::getInstance();
			$com="python -mplatform | grep -i ubuntu";
			$res = $shell_obj->Execute($com, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			$out = "";
			if($res == 0) {
				$out= $shell_obj->getLastOutput();
			}
			if ($out!="") {
				//echo "Ubuntu";
				$this->ubu_Update_Interface();
			} else {
			//echo "Centos";
				$this->cen_Update_Interface();
			}
		}

		public function config_update($db, $t_name) {
			//if (array_key_exists($t_name,$this->T_NAME)) {
				$this -> DBH = $db;
				$this -> Update_Interface();
			//}
			return $this -> Return_val;
		}
	}
?>
