<?php
	include_once("Control/session_secure.php");
	include_once("shell_command.php");
	include_once("Model/sys_calls.php");

	class Config_PROV_SUBSCRIBER {
		private $MYSQL_PROV_FILE="/tmp/prov_event.sql";
		private $T_NAME = array(50=>"PROV_SUBSCRIBER");
		private $DBH;
		private $Retun_val = false;
		private $F_Data;

		private function getIP($iface) {
			$shell_obj = ShellCommand::getInstance();
			$R_v="";
			if(strlen($iface) == 0) return $R_v;
			$shell_status = $shell_obj->Execute("ifconfig $iface | grep 'inet addr:' | cut -d: -f2 | awk '{print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$ipaddress =trim($shell_obj->getLastOutput());
			}
			$R_v=$ipaddress;
			return $R_v;
		}

		private function getDomain($r_profile,$type) {
			$R_v="";
			if ($type ==1 ) {	// WAN
				$query="SELECT PRESENCE_DOMAIN FROM ROAMING_USERS ";
				$query .= "WHERE NAME = '$r_profile'";
				$result = $this -> DBH -> query($query);
				$row = $result -> fetchArray(SQLITE3_ASSOC);
				$R_v = $row['PRESENCE_DOMAIN'];
			} else if ($type == 2) {	// LAN
				$query="select c.DEV_ID,a.ACCESS_PORT from SIP_PROFILES a, ROAMING_USERS b, NETWORK_DEVICE as c, ";
				$query .= " INTERFACE_LIST as d where a.INTERFACE = d.INTER_NAME and d.DEV_ID = c.MAC_ID ";
				$query .= "and a.NAME=b.LAN_SIP_PROFILE and b.NAME='$r_profile'";
				$result = $this -> DBH -> query($query);
				$row = $result -> fetchArray(SQLITE3_ASSOC);
				$IP_v=$this-> getIP($row['DEV_ID']);
				$R_v = $IP_v.":".$row['ACCESS_PORT'];
			}
			return $R_v;
		}

		private function getRomingPreSvr($ro_pro_name) {
			$R_v="";
			$query="SELECT PRESENCE_SUB FROM ROAMING_USERS ";
			$query .= " WHERE NAME='$ro_pro_name'";
			$result = $this -> DBH -> query($query);
			$row = $result -> fetchArray(SQLITE3_ASSOC);
			$R_v = $row['PRESENCE_SUB'];
			return $R_v;
		}

		private function blox_subscribe_sql() {
			$opensips_version = SysCalls::getOpensipsVersion();
			if(preg_match("/^1.11/",$opensips_version)) {
				$result = $this -> DBH -> query("SELECT * FROM PRESENCE_EVENTS");
				$d_str ="LOCK TABLES blox_subscribe WRITE;";
				array_push($this->F_Data, $d_str);
				while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
					$pbx_dom_port = $this-> getRomingPreSvr($row['RUSERPROFILE']);
					$lan_domin	=$this->getDomain($row['RUSERPROFILE'],2);
					$pbx_dom_port_l = explode(":",$pbx_dom_port);
					$t_usr_list = explode(",",$row['SUB_LIST']);
					for ($i=0; $i < count($t_usr_list);$i++) {
						$d_str ="INSERT INTO blox_subscribe ";
						array_push($this->F_Data, $d_str);
						$d_str =" (from_uri,to_uri,event,socket,extra_hdr,expiry) VALUES (";
						array_push($this->F_Data, $d_str);
						$f_uri_v="sip:".$row['FUSER']."@".$pbx_dom_port_l[0];
						$t_uri_v="sip:".$t_usr_list[$i]."@".$pbx_dom_port;
						$event_v=$row['EVENTS'];
						$scket_v="udp:".$lan_domin;
						$ex_hdr_v="Accept:".$row['AC_PKT'];
						$expiry=$row['EXPIRE'];
						$d_str="'$f_uri_v','$t_uri_v','$event_v','$scket_v','$ex_hdr_v',$expiry";
						array_push($this->F_Data, $d_str);
						$d_str =");";
						array_push($this->F_Data, $d_str);
					}
				}
				$d_str = "UNLOCK TABLES;";
				array_push($this->F_Data, $d_str);
			} else {
				$result = $this -> DBH -> query("SELECT * FROM PRESENCE_EVENTS");
				$d_str ="LOCK TABLES subclient WRITE;";
				$d_str .="DELETE FROM subclient;";
				array_push($this->F_Data, $d_str);
				while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
					$pbx_dom_port = $this-> getRomingPreSvr($row['RUSERPROFILE']);
					$lan_domin = $this->getDomain($row['RUSERPROFILE'],2);
					$pbx_dom_port_l = explode(":",$pbx_dom_port);
					$t_usr_list = explode(",",$row['SUB_LIST']);
					for ($i=0; $i < count($t_usr_list);$i++) {
						$subresult = $this -> DBH -> query("SELECT PASSWORD FROM SUBSCRIBERS WHERE OPERATOR=1 and USERNAME = '" . $row['FUSER'] . "' and ROAMUSERPROFILE = '" . $row['RUSERPROFILE'] . "' LIMIT 1");
						$subrow = $subresult -> fetchArray(SQLITE3_ASSOC);
						if(!isset($subrow)) {
							$subrow['PASSWORD']="";
						}
						$d_str ="INSERT INTO subclient";
						array_push($this->F_Data, $d_str);
						$d_str =" (subserver,proxy,aor,third_party_subclient,username,password,binding_URI,expiry,forced_socket,extra_headers) VALUES (";
						array_push($this->F_Data, $d_str);
						$subserver_v = "sip:".$pbx_dom_port_l[0];
						$proxy_v = "sip:".$pbx_dom_port_l[0];
						$aor_v = "sip:".$t_usr_list[$i]."@".$pbx_dom_port_l[0];
						$tparty_v = "sip:".$row['FUSER']."@".$pbx_dom_port_l[0];
						$user_v = $row['FUSER'] ;
						$password_v = $subrow['PASSWORD'] ;
						$binding_URI_v = "sip:".$row['FUSER']."@".$pbx_dom_port_l[0];
						$expiry_v = $row['EXPIRE'];
						$socket_v = "udp:".$lan_domin;
						$extra_headers_v = "Event: " .$row['EVENTS']."\\r\\nAccept:".$row['AC_PKT']."\\r\\n";
						$d_str = "'$subserver_v', '$proxy_v', '$aor_v', '$tparty_v', '$user_v', '$password_v', '$binding_URI_v', '$expiry_v', '$socket_v', '$extra_headers_v'";
						array_push($this->F_Data, $d_str);
						$d_str =");";
						array_push($this->F_Data, $d_str);
					}
				}
				$d_str = "UNLOCK TABLES;";
				array_push($this->F_Data, $d_str);
			}
		}

		private function blox_presence_subscriber_sql() {
			$result = $this -> DBH -> query("SELECT * FROM SUBSCRIBERS WHERE OPERATOR=1 ");
			$id_v=1;
			$d_str ="LOCK TABLES blox_presence_subscriber WRITE;";
			array_push($this->F_Data, $d_str);
			while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
				$d_str ="INSERT INTO blox_presence_subscriber ";
				array_push($this->F_Data, $d_str);
				$d_str =" (id,username,domain,password,ha1,ha1b) VALUES (";
				array_push($this->F_Data, $d_str);
				$u_name_v= $row['USERNAME'];
				$d_name_v=$this->getDomain($row['ROAMUSERPROFILE'],1);
				$pwd_v=$row['PASSWORD'];
				$ha1_v="md5(concat(username, ':', domain, ':', password)) ";
				$ha1b_v="md5(concat(username, '@', domain, ':', domain, ':', password))";
				$d_str = "$id_v,'$u_name_v','$d_name_v','$pwd_v',$ha1_v,$ha1b_v";
				array_push($this->F_Data, $d_str);
				$d_str =");";
				array_push($this->F_Data, $d_str);
				$id_v++;
			}
			$d_str = "UNLOCK TABLES;";
			array_push($this->F_Data, $d_str);
		}

		private function Update_Prov_subscriber() {
			$this->F_Data = array();
			$this->blox_subscribe_sql();
			$this->blox_presence_subscriber_sql();
			$w_content = implode("\n", array_values($this->F_Data));
			$F = fopen($this -> MYSQL_PROV_FILE, "w");
			fwrite($F, $w_content);
			fclose($F);
			$this -> Retun_val = true;
		}

		public function config_update($db, $t_name) {
			//if (array_key_exists($t_name,$this->T_NAME)){
				$this -> DBH = $db;
				$this -> Update_Prov_subscriber();
			//}
			return $this -> Retun_val;
		}
	}
?>
