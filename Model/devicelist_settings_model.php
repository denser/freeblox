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

	class DevicelistSettingsModel {
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
		public static $INTER_NAME = 'INTER_NAME';
		public static $I_TYPE = 'I_TYPE';
		public static $IP_TYPE = 'IP_TYPE';
		public static $IP_ADDR = 'IP_ADDR';
		public static $NETMASK = 'NETMASK';
		public static $DEV_ID = 'DEV_ID';
		public static $GATEWAY = 'GATEWAY';
		public static $DNS = 'DNS';
		public static $SSHENABLE = 'SSHENABLE';
		public static $SSHPORT = 'SSHPORT';
		public static $ICMP = 'ICMP';
		public static $T_CODE = 'T_CODE';
		public static $MACADDRESS = 'MACADDRESS';
		public static $MAC_ID = 'MAC_ID';
		public static $DEF_GWT = 'DEF_GWT';
		public static $NAT_SETTINGS = 'NAT_SETTINGS';
		public static $STUN_STATIC_IPADDR = 'STUN_STATIC_IPADDR';
		static $nat_settings_array = array("NO NAT","STUN","STATIC");

		public function getNatSettings() {
			return DevicelistSettingsModel::$nat_settings_array;
		}


		public function getAlldevicelistSettings() {
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$get_stmt) ) {
				$query='select a.ID, a.INTER_NAME, a.DEF_GWT, a.I_TYPE, a.IP_TYPE, 
					a.DEV_ID as MACADDRESS, b.DEV_ID from INTERFACE_LIST as a, 
					NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID;';
				self::$get_stmt = $dbh->prepare($query);
			}
			self::$get_stmt->reset();
			return self::$get_stmt->execute();
		}

		public function getdevicelistSettings($id) {
			$dbh = DB_Handle::Connect();
			$query = "SELECT ID, INTER_NAME, I_TYPE, IP_TYPE, IP_ADDR, NETMASK, 
				DEV_ID, GATEWAY, DNS, SSHENABLE, SSHPORT, ICMP, DEF_GWT, NAT_SETTINGS, 
				STUN_STATIC_IPADDR FROM INTERFACE_LIST WHERE ID =".$id;
			if ( !isset(self::$get_stmt) ) {
				self::$get_stmt =  $dbh->query($query);
			}
			$row = self::$get_stmt-> fetchArray();
			return $row;
		}

		public function checkIfRuleExists($inter_name) {
			$dbh = DB_Handle::Connect();
			return $dbh->querySingle("SELECT count(*) FROM INTERFACE_LIST WHERE 
						INTER_NAME = '$inter_name'");
		}

		public function adddevicelistSettings ( $inter_name, $i_type, $ip_type, $ip_addr, $netmask, $dev_id, $gateway, $dns, $sshenable, $sshport, $icmp, $def_gwt, $nat_settings, $ip_address) {
			$wancount=$this->getWanCount();	
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($inter_name) != 0 ) {
				return FALSE;
			}
			if ( !isset(self::$add_stmt) ) {
				$query = "INSERT INTO INTERFACE_LIST ( INTER_NAME, I_TYPE,";
				$query .=" IP_TYPE, IP_ADDR, NETMASK, DEV_ID, GATEWAY, "; 
				$query .="DNS, SSHENABLE, SSHPORT, ICMP, NAT_SETTINGS, ";
				$query .= "STUN_STATIC_IPADDR ) VALUES( :inter_name, ";
				$query .= ":i_type, :ip_type, :ip_addr, :netmask, :dev_id, ";
				$query .= ":gateway,:dns,:sshenable,:sshport,";
				$query .= ":icmp, :nat_settings, :ip_address )";
				self::$add_stmt = $dbh->prepare($query);
			}
			self::$add_stmt->reset();
			self::checkIfRuleExists($name);
			self::$add_stmt->bindValue(':inter_name', $inter_name);
			self::$add_stmt->bindValue(':i_type', $i_type);
			self::$add_stmt->bindValue(':ip_type', $ip_type);
			self::$add_stmt->bindValue(':ip_addr', $ip_addr);
			self::$add_stmt->bindValue(':netmask', $netmask);
			self::$add_stmt->bindValue(':dev_id', $dev_id);
			self::$add_stmt->bindValue(':gateway', $gateway);
			self::$add_stmt->bindValue(':dns', $dns);
			self::$add_stmt->bindValue(':sshenable', $sshenable);
			self::$add_stmt->bindValue(':sshport', $sshport);
			self::$add_stmt->bindValue(':icmp', $icmp);
			self::$add_stmt->bindValue(':nat_settings', $nat_settings);
			self::$add_stmt->bindValue(':ip_address', $ip_address);
			self::$add_stmt->execute();
			if( ($wancount == 0) && ($i_type == 1)) {
				$def_gwt = 1;
				$this->defaultinterface($inter_name,$def_gwt);
			}
			//return;
	}

		public function editdevicelistSettings ( $inter_name, $i_type, $ip_type, $ip_addr, $netmask, $dev_id, $gateway, $dns, $sshenable, $sshport, $icmp, $def_gwt, $nat_settings, $ip_address) {
			if($i_type != 1) {
				$def_gwt =0;
			}
			$wancount=$this->getWanCount();
			$dbdefgateval = $this->getDbDefaultVal($inter_name);
			$dbh = DB_Handle::Connect();
			if ( self::checkIfRuleExists($inter_name) != 1 ) {
				return FALSE;
			}
			if ( !isset(self::$edit_stmt) ) {
				$query ="UPDATE INTERFACE_LIST SET I_TYPE = :i_type,";
				$query .= " IP_TYPE = :ip_type, IP_ADDR = :ip_addr, ";
				$query .="NETMASK = :netmask, DEV_ID= :dev_id, GATEWAY= :gateway,";
				$query .=" DNS= :dns,SSHENABLE= :sshenable,SSHPORT= :sshport, ";
				$query .="ICMP= :icmp, NAT_SETTINGS= :nat_settings, ";
				$query .="STUN_STATIC_IPADDR= :ip_address  WHERE ";
				$query .="INTER_NAME = :inter_name";
				self::$edit_stmt =
				$dbh->prepare($query);
				$this->defaultinterface($inter_name,$def_gwt);
			}
			self::$edit_stmt->reset();
			self::$edit_stmt->bindValue(':inter_name', $inter_name);
			self::$edit_stmt->bindValue(':i_type', $i_type);
			self::$edit_stmt->bindValue(':ip_type', $ip_type);
			self::$edit_stmt->bindValue(':ip_addr', $ip_addr);
			self::$edit_stmt->bindValue(':netmask', $netmask);
			self::$edit_stmt->bindValue(':dev_id', $dev_id);
			self::$edit_stmt->bindValue(':gateway', $gateway);
			self::$edit_stmt->bindValue(':dns', $dns);
			self::$edit_stmt->bindValue(':sshenable', $sshenable);
			self::$edit_stmt->bindValue(':sshport', $sshport);
			self::$edit_stmt->bindValue(':icmp', $icmp);
			self::$edit_stmt->bindValue(':nat_settings', $nat_settings);
			self::$edit_stmt->bindValue(':ip_address', $ip_address);
			self::$edit_stmt->execute();
			if($nat_settings == "NO NAT") {
				$str = "update MEDIA_PROFILES set NAT_ENABLE = 0 where 
					EXTERNAL_INTERFACE = '$inter_name'";
				$dbh->query($str);
				$str = "update SIP_PROFILES set NAT_ENABLE = 0 where 
					INTERFACE = '$inter_name'";
				$dbh->query($str);
			}
			if( (($wancount == 0) && ($i_type == 1)) || ($dbdefgateval == 1) ) {
				$def_gwt = 1;
				$this->defaultinterface($inter_name,$def_gwt);
			}
			if(($dbdefgateval == 1) && ($i_type != 1)){
				$this->updateDefaultGateway();
			}
		}

		public static function defaultinterface($inter_name,$def_gwt) {
			$dbh = DB_Handle::Connect();
			if($def_gwt == 1) {
				$query = "update INTERFACE_LIST set DEF_GWT = 1 where ";
				$query .= "INTER_NAME == '$inter_name'";
				$dbh->query($query);
				$query = "update INTERFACE_LIST set DEF_GWT = 0 where ";
				$query .= "INTER_NAME != '$inter_name'";
				$dbh->query($query);
			} else {
				$query = "update INTERFACE_LIST set DEF_GWT = 0 where ";
				$query .= "INTER_NAME == '$inter_name'";
				$dbh->query($query);
			}
			$query = "UPDATE FIREWALL_RULES SET DST_IFACE = (SELECT INTER_NAME FROM 
				INTERFACE_LIST WHERE DEF_GWT = 1 AND I_TYPE = 1 LIMIT 1)";
			$dbh->query($query);
		}

		public function getWanCount() {
			$dbh = DB_Handle::Connect();
			$str ="select count(*) from INTERFACE_LIST where I_TYPE = 1";
			$result = $dbh->query($str);
			$row = $result->fetchArray();
			$defaultcount = $row[0];
			return $defaultcount;
		}

		public function getDbDefaultVal($interfacename) {
			$dbh = DB_Handle::Connect();
			$str = "SELECT DEF_GWT FROM INTERFACE_LIST WHERE INTER_NAME = '$interfacename'";
			$result = $dbh->query($str);
			$row = $result->fetchArray(SQLITE3_ASSOC);
			$defaultdbval = $row['DEF_GWT'];
			return $defaultdbval;
		}

		public function updateDefaultGateway() {
			$dbh = DB_Handle::Connect();
			$str = "select ID from INTERFACE_LIST where I_TYPE = 1";
			$result = $dbh->query($str);
			$row = $result->fetchArray(SQLITE3_ASSOC);
			$id = $row['ID'];
			if($id != "") {
				$str2 = "UPDATE INTERFACE_LIST SET DEF_GWT = 1 WHERE ID = $id";
				$dbh->query($str2);
			}
		}

		public function checkDefGateway($id) {
			$dbh = DB_Handle::Connect();
			$str = "select count(*) from INTERFACE_LIST where I_TYPE = 1 ";
			$str .= "AND DEF_GWT = 1 AND ID = $id";
			$result = $dbh->query($str);
			$row = $result->fetchArray();
			return $row[0];
		}

		public function deletedevicelistSettings ( $id ) {
			$checkdefgate = $this->checkDefGateway($id);
			$dbh = DB_Handle::Connect();
			if ( !isset(self::$del_stmt) ) {
				self::$del_stmt = $dbh->prepare('DELETE FROM INTERFACE_LIST 
								WHERE ID = :id;');
			}
			self::$del_stmt->reset();
			self::$del_stmt->bindValue(':id', $id);
			self::$del_stmt->execute();
			if($checkdefgate == 1) {
				$this->updateDefaultGateway();
			}
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

		public function getNames() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT INTER_NAME FROM INTERFACE_LIST");
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



		public function getdevlist() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT DEV_ID FROM INTERFACE_LIST");
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

		public function getdeviceids() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT I_TYPE, MAC_ID, T_CODE, DEV_ID 
					FROM NETWORK_DEVICE");
			return $result ;
		}

		public function getsipvalues() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select a.INTER_NAME, a.I_TYPE, b.DEV_ID, a.NAT_SETTINGS 
					from INTERFACE_LIST as a, NETWORK_DEVICE as b where
					a.DEV_ID = b.MAC_ID AND b.T_CODE != 1");
			return $result;
		}

		public function getsipvalues1($intername) {
			$resultval = "";
			$interfacetype = "";
			$dbh = DB_Handle::Connect();
			$str = "select a.INTER_NAME, a.I_TYPE, b.DEV_ID from ";
			$str .= "INTERFACE_LIST as a, NETWORK_DEVICE as b ";
			$str .= "where a.DEV_ID = b.MAC_ID AND b.T_CODE != 1 ";
			$str .= "AND b.DEV_ID = '".$intername."'";
			$result = $dbh->query($str);
			$row1 = $result->fetchArray(SQLITE3_ASSOC);
			$resultval = $row1['INTER_NAME']." - ";
			if($row1['I_TYPE'] == 0) {
				$interfacetype = "Internal Interface";
			} else {
				$interfacetype = "External Interface";
			}
			$resultval .= $interfacetype."(".$row1['DEV_ID'].")";
			return $resultval;
		}

		public function getid($macid) {
			$query = "select a.ID from INTERFACE_LIST as a, ";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND ";
			$query .= "a.DEV_ID = '$macid';";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			return $row[0];
		}

		public function delsipcheck($id) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from SIP_PROFILES where INTERFACE IN";
			$query .= "(select b.DEV_ID from INTERFACE_LIST as a,";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID and ";
			$query .= "a.ID = '$id')";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function delsipprcheck($id) {
			$dbh = DB_Handle::Connect();
			$intername =$this-> getvalue($id);
			$query1 = "select count(*) from SIP_PROFILES ";
			$query1 .= "where INTERFACE LIKE '$intername'";
			$result1 = $dbh->query($query1);
			$row1=$result1->fetchArray();
			return $row1[0];
		}

		public function delmediacheck($id) {
			$count = 0;
			$dbh = DB_Handle::Connect();
			$intername =$this-> getvalue($id);
			$query1 = "select count(*) from MEDIA_PROFILES ";
			$query1 .= "where EXTERNAL_INTERFACE LIKE '$intername'";
			$result1 = $dbh->query($query1);
			$row1=$result1->fetchArray();

			$query2 = "select count(*) from MEDIA_PROFILES ";
			$query2 .= "where INTERNAL_INTERFACE LIKE '$intername'";
			$result2 = $dbh->query($query2);
			$row2=$result2->fetchArray();

			$query3 = "select count(*) from MEDIA_PROFILES ";
			$query3 .= "where MEDIA_INTERFACE LIKE '$intername'";
			$result3 = $dbh->query($query3);
			$row3=$result3->fetchArray();

			$count = $row1[0]  +  $row2[0] + $row3[0];
			return $count;
		}

		public function getvalue($id) {
			$dbh = DB_Handle::Connect();
			$query = "select a.INTER_NAME from INTERFACE_LIST as a, NETWORK_DEVICE as b ";
			$query .= "where a.DEV_ID = b.MAC_ID and a.ID =$id";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];	
		}

		public function delportforwcheck($id) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from PORT_FORWARDING where INTERFACE IN ";
			$query .= "(select a.INTER_NAME from INTERFACE_LIST as a,";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID and ";
			$query .= "a.ID = '$id')";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function delroutercheck($id) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from ROUTES where INTERFACE IN ";
			$query .= "(select a.INTER_NAME from INTERFACE_LIST as a,";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID and ";
			$query .= "a.ID = $id)";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function delvlancheck($id) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from VLAN_CONFIG where INTERFACE IN ";
			$query .= "(select a.INTER_NAME from INTERFACE_LIST as a,";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID and ";
			$query .= "a.ID = $id)";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}


		public function delvirtualipcheck($id) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from VIRTUAL_IP where INTERFACE IN ";
			$query .= "(select a.INTER_NAME from INTERFACE_LIST as a,";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID and ";
			$query .= "a.ID = $id)";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function delVipsMediacheck($interfacename) {
			$count = 0;
			$dbh = DB_Handle::Connect();
			$query1 = "select count(*) from MEDIA_PROFILES ";
			$query1 .= "where EXTERNAL_INTERFACE LIKE '$interfacename'";
			$result1 = $dbh->query($query1);
			$row1=$result1->fetchArray();

			$query2 = "select count(*) from MEDIA_PROFILES ";
			$query2 .= "where INTERNAL_INTERFACE LIKE '$interfacename'";
			$result2 = $dbh->query($query2);
			$row2=$result2->fetchArray();

			$query3 = "select count(*) from MEDIA_PROFILES ";
			$query3 .= "where MEDIA_INTERFACE LIKE '$interfacename'";
			$result3 = $dbh->query($query3);
			$row3=$result3->fetchArray();

			$count = $row1[0] + $row2[0] + $row3[0];
			return $count;
		}

		public function delVipsipcheck($interfacename) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from SIP_PROFILES where INTERFACE ='$interfacename'";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function getintername($id) {
			$dbh = DB_Handle::Connect();
			$query = "select INTER_NAME from INTERFACE_LIST where ID = $id";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

		public function checksipprofile($macid) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from SIP_PROFILES where INTERFACE IN 
				(select DEV_ID from NETWORK_DEVICE where MAC_ID = '$macid')";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			return $row[0];
		}

// get interface for vlan and vip.
		public function getInterfaceByName($internameval) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select a.I_TYPE, b.DEV_ID from INTERFACE_LIST as a, 
					NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND
					a.INTER_NAME = '$internameval'");
			$row1 = $result->fetchArray(SQLITE3_ASSOC);
			$resultval = $row1['DEV_ID']."|";
			if($row1['I_TYPE'] == 0) {
				$interfacetype = "LAN";
			} else if($row1['I_TYPE'] == 1) {
				$interfacetype = "WAN";
			} else if($row1['I_TYPE'] == 2) {
				$interfacetype = "Transcoding";
			}
			$resultval .= $interfacetype;
			return $resultval;
		}

//get interface for sip and media.
		public function getVirtualInterface($intername) {
			$interfacename = "";
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT COUNT(*) FROM VIRTUAL_IP WHERE 
					NAME ='$intername'");
			$row = $result->fetchArray();
			if($row[0] != 0) {
				$str = "SELECT INTERFACE FROM VIRTUAL_IP WHERE NAME = '$intername'";
				$result1 = $dbh->query($str);
				$row1 = $result1->fetchArray(SQLITE3_ASSOC);
				$interfacename = $row1['INTERFACE'];
			}
			$array1 = explode(".",$intername);
			$len = count($array1);
			if($len == 2) {
				$tagid = $array1[1];
				if($tagid != "") {
					$result2 = $dbh->query("SELECT COUNT(*) FROM 
							VLAN_CONFIG WHERE TAG_ID = $tagid");
					$row2 = $result2->fetchArray();
					if($row2[0] != 0) {
						$interfacename = $array1[0];
					}
				}
			}
			return $interfacename;
		}

		public function getVirtualip($intername) {
			$virtualip = "";
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT COUNT(*) FROM VIRTUAL_IP 
						WHERE NAME ='$intername'");
			$row = $result->fetchArray();
			if($row[0] != 0) {
				$str = "SELECT IPADDRESS FROM VIRTUAL_IP WHERE NAME = '$intername'";
				$result1 = $dbh->query($str);
				$row1 = $result1->fetchArray(SQLITE3_ASSOC);
				$virtualip = $row1['IPADDRESS'];
			}
			$array1 = explode(".",$intername);
			$len = count($array1);
			if($len  == 2) {
				$tagid = $array1[1];
				if($tagid != "") {
					$result2 = $dbh->query("SELECT COUNT(*) FROM 
								VLAN_CONFIG WHERE TAG_ID = $tagid");
					$row2 = $result2->fetchArray();
						if($row2[0] != 0) {
							$str = "SELECT IPADDRESS FROM VLAN_CONFIG 
								WHERE TAG_ID = $tagid";
							$result3 = $dbh->query($str);
							$row3 = $result3->fetchArray(SQLITE3_ASSOC);
							$virtualip = $row3['IPADDRESS'];
						}
				}
			}
			return $virtualip;
		}

		public function getInterface() {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("SELECT INTER_NAME FROM INTERFACE_LIST");
			return $result;
		}

		public function getInterfaceList($val) {
			$interfacearray1 = array();
			$dbh = DB_Handle::Connect();
			$query = "select a.INTER_NAME from INTERFACE_LIST as a, ";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID ";
			$query .= "AND a.I_TYPE = $val";
			$result = $dbh->query($query);
			while ($row = $result->fetchArray()) {
				if($row["INTER_NAME"] != "") {
					array_push($interfacearray1,$row["INTER_NAME"]);
				}
			}
			return $interfacearray1;
		}

		public function getInternalExternalList() {
			$interfacearray1 = array();
			$dbh = DB_Handle::Connect();
			$query = "select a.INTER_NAME from INTERFACE_LIST as a, ";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND b.T_CODE != 1";
			$result = $dbh->query($query);	
			while ($row = $result->fetchArray()) {
				if($row["INTER_NAME"] != "") {
					array_push($interfacearray1,$row["INTER_NAME"]);
				}
			}
			return $interfacearray1;
		}

		public function getVlanVirtualIp($transvalue) {
			$interfacearray2 = array();
			$dbh = DB_Handle::Connect();
			if($transvalue == 0) {
				$query = "SELECT NAME FROM VIRTUAL_IP WHERE INTERFACE IN ";
				$query .= "(select INTER_NAME from INTERFACE_LIST as a, ";
				$query .= "NETWORK_DEVICE as b where b.T_CODE != 1 AND ";
				$query .= "a.DEV_ID = b.MAC_ID ) ";
			} else {
				$query = "SELECT NAME FROM VIRTUAL_IP";
			}
			$result1 = $dbh->query($query);
			while ($row1 = $result1->fetchArray()) {
				if($row1["NAME"] != "") {
					array_push($interfacearray2,$row1["NAME"]);
				}
			}
			if($transvalue == 0) {
				$query = "SELECT TAG_ID, INTERFACE from VLAN_CONFIG WHERE INTERFACE IN ";
				$query .= "(select INTER_NAME from INTERFACE_LIST as a, ";
				$query .= "NETWORK_DEVICE as b where b.T_CODE != 1 AND ";
				$query .= "a.DEV_ID = b.MAC_ID ) ";
			} else {
				$query = "SELECT TAG_ID, INTERFACE from VLAN_CONFIG;";
			}
			$result2 = $dbh->query($query);	
			while ($row2 = $result2->fetchArray()) {
				if($row2["TAG_ID"] != "") {
					$str = $row2["INTERFACE"].".".$row2["TAG_ID"];
					array_push($interfacearray2,$str);
				}
			}
			return $interfacearray2;
		}

		public static function getvirtualrealnames($interfaces) {
			$val="";
			if ($interfaces != "") {
				if(strstr($interfaces,":")) {
					$d_arry = explode(":",$interfaces);
				}
				$val=$d_arry[0];
			}
			return $val;
		}

		public static function getrealnames($interfaces) {
			$val="";
			if ($interfaces != "") {
				if(strstr($interfaces,".")) {
					$d_arry = explode(".",$interfaces);
				} else {
					$d_arry = explode(":",$interfaces);
				}
				$val=$d_arry[0];
			}
			return $val;
		}

		public function getWaninterface() {
			$waninterfaces = array();
			$dbh = DB_Handle::Connect();
			$query = "select a.INTER_NAME from INTERFACE_LIST as a, NETWORK_DEVICE as b 
				where a.I_TYPE = 1 AND a.DEV_ID = b.MAC_ID";
			$result1 = $dbh->query($query);
			while ($row1 = $result1->fetchArray()) {
				array_push($waninterfaces,$row1['INTER_NAME']);
			}
			// for virtual Ip
			$query = "select a.NAME from VIRTUAL_IP as a, INTERFACE_LIST as b";
			$query .= " where a.INTERFACE = b.INTER_NAME AND  b.I_TYPE = 1";
			$result2 = $dbh->query($query);
			while ($row2 = $result2->fetchArray()) {
				array_push($waninterfaces,$row2['NAME']);
			}
			// for VLAN
			$query = "select a.NAME from VLAN_CONFIG as a, INTERFACE_LIST as b";
			$query .= " where a.INTERFACE = b.INTER_NAME AND  b.I_TYPE = 1";
			$result3 = $dbh->query($query);	
			while ($row3 = $result3->fetchArray()) {
				array_push($waninterfaces,$row3['NAME']);
			}
			return $waninterfaces;
		}

		public function getinterfacetype($interfaces,$typevalue) {
			$separray = array();
			for($i=0;$i<count($interfaces);$i++) {
				$val=$this->getrealnames($interfaces[$i]);
				$counts=$this->checktype(trim($val),$typevalue);
				if($counts !=0) {
					array_push($separray,$interfaces[$i]);
				}
			}
			return $separray;
		}

		public function gettrans($internalinterface,$systransinterfaces) {
			$separray = array();
			for($i=0;$i<count($systransinterfaces);$i++) {
				$val=$this->getrealnames($systransinterfaces[$i]);
				$counts=$this->tinterface(trim($val));
				if($counts != 0) {
					array_push($separray,$systransinterfaces[$i]);
				}
			}
			for($i=0;$i<count($internalinterface);$i++) {
				$val=$this->getvirtualrealnames($internalinterface[$i]);
				$counts=$this->tvirtualinterface(trim($val));
				if($counts != 0) {
					array_push($separray,$internalinterface[$i]);
				}
			}
			return $separray;
		}

		public function gettranscoding($interfaces) {
			$separray = array();
			for($i=0;$i<count($interfaces);$i++) {
				$val=$this->getrealnames($interfaces[$i]);
				$counts=$this->tinterface(trim($val));
				if($counts != 0) {
					array_push($separray,$interfaces[$i]);
				}
			}
			return $separray;
		}

		public function getsshdata() {
			$dbh = DB_Handle::Connect();
			$query = "select HOST_NAME,ENABLE_SSH,SSH_PORT,ALLOW_ICMP ";
			$query .= "from GENERAL_SETTINGS";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			return $row;
		} 

		public function updatessh($hostname,$enablessh,$sshport,$allow_icmp) {
			$dbh = DB_Handle::Connect();
			$query = "update GENERAL_SETTINGS set HOST_NAME = '$hostname', ";
			$query .= "ENABLE_SSH = $enablessh, SSH_PORT = '$sshport', ";	
			$query .= "ALLOW_ICMP = '$allow_icmp'";
			$dbh->query($query);
		}

		public function checktype($val,$type) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from INTERFACE_LIST as a, ";
			$query .= "NETWORK_DEVICE as b where b.T_CODE != 1 ";
			$query .= "AND a.DEV_ID = b.MAC_ID ";
			$query .= "AND b.DEV_ID ='$val' AND a.I_TYPE=$type";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			return $row[0];
		}

		public function tinterface($val) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from INTERFACE_LIST as a, ";
			$query .= "NETWORK_DEVICE as b where b.T_CODE = 1 ";
			$query .= "AND a.DEV_ID = b.MAC_ID AND b.DEV_ID = '$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			return $row[0];
		}

		public function tvirtualinterface($val) {
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from INTERFACE_LIST as a, ";
			$query .= "NETWORK_DEVICE as b where ";
			$query .= "a.DEV_ID = b.MAC_ID AND b.DEV_ID = '$val'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			return $row[0];
		}

		public function gettranceinterfacename($arrayval) {
			$dbh = DB_Handle::Connect();
			$interfacearray = array();
			for($i=0;$i<count($arrayval);$i++) {
				$val =$this->getrealnames($arrayval[$i]);
				$query = "select a.INTER_NAME, a.I_TYPE from INTERFACE_LIST as a, ";
				$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND ";
				$query .= "b.DEV_ID = '$val';";
				$result = $dbh->query($query);
				$row = $result->fetchArray();
				if($row[0] != "") {
					$str = $row[0]."- (".$arrayval[$i].")";
					array_push($interfacearray,$str);
				}
			}
			return $interfacearray;
		}

		public function getsipinterfacename($arrayval) {
			$dbh = DB_Handle::Connect();
			$interfacearray = array();
			for($i=0;$i<count($arrayval);$i++) {
				$val =$this->getrealnames($arrayval[$i]);
				$query = "select a.INTER_NAME, a.I_TYPE from INTERFACE_LIST as a, ";
				$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND ";
				$query .= "b.DEV_ID = '$val' AND b.T_CODE != 1;";
				$result = $dbh->query($query);
				$row = $result->fetchArray();
				//$str="";
				if($row[0] != "") {
					if($row[1] == 0 ) {
						$str = $row[0]."- Internal(".$arrayval[$i].")";
					} else {
						$str = $row[0]."- External(".$arrayval[$i].")";
					}
					array_push($interfacearray,$str);
				} else {
					$str = "";
					array_push($interfacearray,$str);
				}
			}
			return $interfacearray;
		}


		public function getinterfacename($arrayval) {
			$dbh = DB_Handle::Connect();
			$interfacearray = array();
			for($i=0;$i<count($arrayval);$i++) {
				$val =$this->getrealnames($arrayval[$i]);
				$query = "select a.INTER_NAME, a.I_TYPE from INTERFACE_LIST as a, ";
				$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND ";
				$query .= "b.DEV_ID = '$val';";
				$result = $dbh->query($query);
				$row = $result->fetchArray();
				if($row[0] != "")
					$str = $row[0]." - ".$arrayval[$i];
				array_push($interfacearray,$str);
			}
			return $interfacearray;
		}

		public function getsipnamesvalues($intertype) {
			$sipnamesarray = array();
			$dbh = DB_Handle::Connect();
			$query = "select NAME,INTERFACE from SIP_PROFILES";
			$result = $dbh->query($query);	
			while($row = $result->fetchArray(SQLITE3_ASSOC)) {
				$val =$this->checkInterfaceList($row['INTERFACE']);
				$query2 = "select count(*) from INTERFACE_LIST as a, ";
				$query2 .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID ";
				$query2 .= "AND a.I_TYPE = $intertype AND a.INTER_NAME = '$val'";
				$result2 = $dbh->query($query2);
				$row2 = $result2->fetchArray();
				if($row2[0] != 0) {
					$str = $row['NAME'];
					array_push($sipnamesarray,$str);
				}
			}
			return $sipnamesarray;
		}

		public function checkInterfaceList($interfacename) {
			$interfacevalue = "";
			$dbh = DB_Handle::Connect();
			$query = "select count(*) from INTERFACE_LIST where 
				INTER_NAME = '$interfacename'";
			$result = $dbh->query($query);
			$row=$result->fetchArray();
			if($row[0] == 1) {
				$interfacevalue = $interfacename;
			} else {
				$str1 = "select INTERFACE from VIRTUAL_IP where NAME = '$interfacename'";
				$result1 = $dbh->query($str1);
				$row1=$result1->fetchArray();
				$interfacevalue = $row1['INTERFACE'];
				if($interfacevalue == "") {
					$str2 = "select INTERFACE from VLAN_CONFIG where NAME ='$interfacename'";
					$result2 = $dbh->query($str2);
					$row2=$result2->fetchArray();
					$interfacevalue = $row2['INTERFACE'];
				}
			}
			return $interfacevalue;
		}

		public function getSipInterfaceValue($val) {
			$dbh = DB_Handle::Connect();
			$result = $dbh->query("select a.I_TYPE, b.DEV_ID from INTERFACE_LIST as a, 
					NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND
					a.INTER_NAME = '$val'");
			$row1 = $result->fetchArray(SQLITE3_ASSOC);
			$resultval = $row1['DEV_ID'];
			return $resultval;
		}

		public function getsipnames($intertype) {
			$i =0;
			$sipnamesarray = array();
			$dbh = DB_Handle::Connect();
			$query = "select NAME,INTERFACE from SIP_PROFILES";
			$result = $dbh->query($query);	
			while($row = $result->fetchArray()) {
				$i++;
				$val =$this->getrealnames($row[1]);
				$query2 = "select count(*) from INTERFACE_LIST as a, ";
				$query2 .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID ";
				$query2 .= "AND a.I_TYPE = $intertype AND b.DEV_ID = '$val'";
				$result2 = $dbh->query($query2);
				$row2 = $result2->fetchArray();
				if($row2[0] != 0) {
					$str = $row[0]." - ".$row[1];
					array_push($sipnamesarray,$str);
				}
			}
			return $sipnamesarray;
		}

// for Show_iface_ip.php - to get interface Type
		public function getitype($val) {
			$interfacetype1 = "";
			$val1 =$this->getrealnames($val);
			$dbh = DB_Handle::Connect();
			$query = "select a.INTER_NAME, a.I_TYPE from INTERFACE_LIST as a, ";
			$query .= "NETWORK_DEVICE as b where a.DEV_ID = b.MAC_ID AND ";
			$query .= "b.DEV_ID = '$val1'";
			$result = $dbh->query($query);
			$row = $result->fetchArray();
			if($row["I_TYPE"] == 0)	{
				$interfacetype1 = "Lan";
			} else if($row["I_TYPE"] == 1) {
				$interfacetype1 = "Wan";
			} else {
				$interfacetype1 = "Trans";
			}
			return 	$interfacetype1;
		}

		public function checkExternalInterface($interface) {
			$str="select count(*) from INTERFACE_LIST where 
				I_TYPE = 1 AND INTER_NAME = '$interface'";
			$dbh = DB_Handle::Connect();
			$result = $dbh->query($str);
			$row = $result->fetchArray();
			return $row[0];
		}

		public function checkVirtualExternalInterface($interface) {
			$natsettings = "";
			$str="select NAT_SETTINGS from VIRTUAL_IP where NAME = '$interface'";
			$dbh = DB_Handle::Connect();
			$result = $dbh->query($str);
			$row = $result->fetchArray();
			$natsettings = $row['NAT_SETTINGS'];
			if($natsettings == "") {
				$str1="select NAT_SETTINGS from VLAN_CONFIG where NAME = '$interface'";
				$result1 = $dbh->query($str1);
				$row1 = $result1->fetchArray();
				$natsettings = $row1['NAT_SETTINGS'];
			}
			return $natsettings;
		}
	}
?>
