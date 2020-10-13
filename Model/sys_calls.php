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
	include_once('db_mgr.php');
	include_once ('sbc_provisioning.php');
	include_once("shell_command.php");
	include_once('device_settings_model.php');

	$UPLOAD_CERT_DIR="/usr/local/etc/opensips/tls/";
	$FILE_PRESENT=1;
	$FILE_NOT_PRESENT=0;

	class SysCalls {
		public static $cert_list_file = "/etc/blox_certstore.ini";
		public function getVersionInfo() {
			$shell_obj = ShellCommand::getInstance();
			$cmd="cat /etc/version.xml";
			$r_str = "" ;
			$ret = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($ret == 0) {
				$r_str = $shell_obj->getLastOutput();
			}
			return $r_str;
		}

		public static function getDashboardInfo() {
			$shell_obj = ShellCommand::getInstance();
			$lan_iface = 'LanInterface';
			$wan_iface = 'WanInterface';
			$LanInterface = SIPProfileConfigModel::getIfaceName($lan_iface );
			$WanInterface = SIPProfileConfigModel::getIfaceName($wan_iface);
			$lan = trim($LanInterface);
			$wan = trim($WanInterface);
			$dashboard_arr = array();
			$shell_status = $shell_obj->Execute('free | grep "Mem" | awk \'{printf "%d", $3*100/$2}\'', ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$meminfo =$shell_obj->getLastOutput()."%";
			}
			$cpuusage = self::getCpuUsage();
			$shell_status = $shell_obj->Execute("ifconfig $lan | grep 'inet addr:' |cut -d: -f2 | awk '{print $1}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$lanip =$shell_obj->getLastOutput();
			}
			$shell_status = $shell_obj->Execute("ifconfig $wan | grep 'inet addr:' |cut -d: -f2 | awk '{print $1}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$wanip =$shell_obj->getLastOutput();
			}
			$shell_status = $shell_obj->Execute("ip route show default | grep default | awk '{print $3}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$defgw =$shell_obj->getLastOutput();
			}
			$shell_status = $shell_obj->Execute("ifconfig $lan | grep  'HWaddr' | awk '{print $5}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$lanmac =$shell_obj->getLastOutput();
			}
			$shell_status = $shell_obj->Execute("ifconfig $wan | grep  'HWaddr' | awk '{print $5}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$wanmac =$shell_obj->getLastOutput();
			}
			$shell_status = $shell_obj->Execute("uptime | awk -F'up' '{print $2}' |  awk -F',' '{print $1}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$uptime =$shell_obj->getLastOutput();
			}
			$dashboard_arr = array( $meminfo, $lanip, $defgw, $lanmac, $wanmac, $uptime,$cpuusage,$wanip);
			return $dashboard_arr;
		}

		public static function getCPUTemperature() {
			$ret = 1 ;	// default min 1 core
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute(
				'echo $(($(cat /sys/class/thermal/thermal_zone1/temp)/1000))', 
				ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$ret = $shell_obj->getLastOutput();
			}
			return $ret ;
		}

		public static function getNumOfCores() {
			$ret = 1 ; // default min 1 core
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute('egrep "cpu[0-9]"  /proc/stat | wc -l', ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$ret = $shell_obj->getLastOutput();
			}
			return $ret ;
		}

		public static function getCpuCoreUsage($id) {
			if(SysCalls::getSysArch() == "armv7l") {
				$shell_obj = ShellCommand::getInstance();
				$shell_status = $shell_obj->Execute("/usr/bin/mpstat -u -P $id | tail -1 | sed 's/.* //'", ShellCommand::FLAG_DONT_VALIDATE|ShellCommand::FLAG_DONT_ALLOWCMD) ;
				if( $shell_status == 0) {
					$ret = $shell_obj->getLastOutput();
				}
				$ret-=100;
				return abs($ret)."%";
			} else {
			}
		}

		public static function getCpuUsage() {
			if(SysCalls::getSysArch() == "armv7l") {
				$shell_obj = ShellCommand::getInstance();
				$shell_status = $shell_obj->Execute("/usr/bin/mpstat -u | tail -1 | sed 's/.* //'", ShellCommand::FLAG_DONT_VALIDATE|ShellCommand::FLAG_DONT_ALLOWCMD) ;
				if( $shell_status == 0) {
					$ret = $shell_obj->getLastOutput();
				}
				$ret-=100;
				return abs($ret)."%";
			} else {
				$shell_obj = ShellCommand::getInstance();
				$shell_status = $shell_obj->Execute('cat /proc/stat | grep "cpu "', ShellCommand::FLAG_DONT_VALIDATE|ShellCommand::FLAG_DONT_ALLOWCMD) ;
				if( $shell_status == 0) {
					$ret =$shell_obj->getLastOutput();
				}
				$val_tmp = explode(" ", $ret);
				unset($val_tmp[1]);
				unset($val_tmp[9]);
				unset($val_tmp[10]);
				$val = array_values($val_tmp);
				$diff = array('cpu','user','nice','sys','idle','io','ir','soft');
				if ( count($val) == count($diff)) {
					$cupTicks = array_combine($diff, $val);
					sleep (1);
					$shell_status = $shell_obj->Execute('cat /proc/stat | grep "cpu "', ShellCommand::FLAG_DONT_VALIDATE|ShellCommand::FLAG_DONT_ALLOWCMD) ;
					if( $shell_status == 0) {
						$ret1 =$shell_obj->getLastOutput();
					}
					$val1_tmp = explode(" ", $ret1);
					unset($val1_tmp[1]);
					unset($val1_tmp[9]);
					unset($val1_tmp[10]);
					$val1 = array_values($val1_tmp);
					$cupTicks2 = array_combine($diff, $val1);
					unset($cupTicks['cpu']);
					unset($cupTicks2['cpu']);
					$totalStart= array_sum($cupTicks);
					$totalEnd= array_sum($cupTicks2);
					if($totalEnd <= $totalStart)
						return 0;
					$tot = $totalEnd - $totalStart;
					$ide = $cupTicks2['idle']- $cupTicks['idle'];
					$io = $cupTicks2['io'] - $cupTicks['io'];
					$cpu = $ide + $io;
					$tot_cpu = $tot - $cpu;
					$shell_status = $shell_obj->Execute("echo $((100*$tot_cpu/$tot))", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
					if( $shell_status == 0) {
						$cpuUsage =$shell_obj->getLastOutput();
					}
				}
				$rnd= trim($cpuUsage);
				return abs($rnd)."%";
			}
		}

		public static function getAutoDiscoveryStatus() {
			return "discovery status.";
		}

		public static function getSecurityAlertsSummary() {
			return "security alerts summary";
		}

		public static function getIpsAlerts() {
			$diaglogfile ="/tmp/alerts";
			$contents_array;
			if ( file_exists($diaglogfile) == TRUE ) {
				$fh = fopen($diaglogfile, "r");
				$i=0;
				while( !feof($fh)) {
				$contents = fgets($fh);
				$split = explode(",", $contents);
				$contents_array[$i]=$split;
				$i=$i+1;
				}
				fclose($fh);
			} else {
				return array();
			}
			return $contents_array;
		}

               public static function read_lastlines($file, $num_to_get) {
                        $file_size = filesize($file);
                        $max_lines = 100000 ;
                        $new_file = "/tmp/.read_log" ;
                        if ( file_exists ( $file ) == TRUE && $file_size > 0 ) {
                                $shell_obj = ShellCommand::getInstance();
                                $shell_obj->Execute("tail -$max_lines $file > $new_file",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE,1);
                                $file = file($new_file);
                                $file = array_reverse($file);
                                foreach($file as $line){
                                        if (substr_count($data, "\n") >= $num_to_get ) {
                                                preg_match("!(.*?\n){".($num_to_get-1)."}$!", $data, $match);
                                                return $match[0];
                                        } else if ( $line != "" ) {
                                                $data .= $line;
                                        }
                                }
                                return $data;
                        } else {
                                return FALSE;
                        }
                }


		public static function read_logs($filename, $num_to_get, $is_sys_log) {
			$line = array();
			if ( file_exists ( $filename ) == FALSE )
				return $line;
			$line=self::read_lastlines($filename, $num_to_get);
			$temp = explode("\n",$line);
			$count = count($temp);
			$logs = array();
			for($i=0; $i < $count; $i++) {
				if ( strlen(trim($temp[$i])) == 0 )
					continue;
				$temp1 = explode(" ",$temp[$i],4);
				$logs[$i][0] = "$temp1[0]"." "."$temp1[1]"." ".$temp1[2];
				$logs[$i][1] = implode(" ",array_slice($temp1,3));
			}
				return $logs;
		}

		public static function getLast10IpsAlerts() {
			$diaglogfile ="/tmp/alerts";
			$num = 11;
			if ( file_exists($diaglogfile) == TRUE ) {
				$last_10_alerts = SysCalls::read_lastlines($diaglogfile,$num);
				$alerts = explode("\n",$last_10_alerts);
				$i=0;
				while( $i <= count($alerts)) {
					$split = explode(",", $alerts[$i]);
					$contents_array[$i]=$split;
					$i=$i+1;
				}
			}
			return $contents_array;
		}

		public static function sysRestart() {
			$shell_obj = ShellCommand::getInstance();
			if(SysCalls::getSysArch() == "armv7l") {
				$shell_obj->Execute("/usr/bin/reboot_s500.sh > /dev/null &",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE,1);
			} else {
				$shell_obj->Execute("reboot > /dev/null &",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE,1);
			}
		}

		public static function sysShutDown() {
			$shell_obj = ShellCommand::getInstance();
			$shell_obj->Execute("poweroff -f -d 2 > /dev/null &",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE,1);
		}

		public static function sysServicesRestart() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("nohup /usr/bin/restartserv 2>/dev/null 1>&2",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			return $shell_status;
		}

		public static function factoryReset() {
			$filename = "factoryreset";
			$res = self::executeShellscript("factoryreset 2>/dev/null &",$filename,1);
			return $res;
		}

		public static function runDiagnostics() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("rundiag",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			$shell_output =$shell_obj->getLastOutput();
			return $shell_output;
		}

		public static function updateSignatures() {
			$shell_obj = ShellCommand::getInstance();
			$shell_obj->Execute("updatesigs",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		}

		public static function installUploadedFirmware($file_name) {
			if(file_exists($file_name)) {
				$filename = "firmware_upg";
				$res = self::executeShellscript("nohup run_update",$filename,1);
				return $res;
			}
		}

		public static function checkFileStatus($filename) {
			$file = "/var/tmp/." . $filename ;
			$fileStatus = "-1" ;
			if (file_exists($file) == TRUE) {
				$handle = fopen($file,'r');
				if($handle) {
					$fileStatus = "" ;
					while(($ret=fgetc($handle)) !== false) {
						$fileStatus .= $ret ;
					}
					fclose($handle);
				}
			}
			return $fileStatus;
		}

		public static function serviceLogInfo() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("/usr/bin/check-service.sh", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			$file = "/var/tmp/services-status.log" ;
			if (file_exists($file) == TRUE) {
				$fh = fopen($file,'r');
				$contents = file_get_contents($file);
				fclose($fh);
			} else {
				return;
			}
			return $contents;
		}

		public static function serviceRunStat() {
			$status_array = array("opensips" => "0", "conf" => "0",
					"snort_" => "0", "snortsam" => "0", "firewall" => "0",
					"mysqld/mysqld" => "0", "sshd" => "0");
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("/usr/bin/check-service.sh", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			$file = "/var/tmp/services-status.log" ;
			if (file_exists($file) == TRUE) {
				$file = file($file);
				foreach($file as $line){
					if(!empty($line)) {
						foreach( $status_array as $key => $val) {
							if( (strpos($line, $key) !== false) ){
								if((preg_match('~[0-9]~', $line) === 1) && (strpos($line, 'not') === false)) {
									$status_array[$key] = "1";
								} else {
									$status_array[$key] = "0";
								}
							}
						}
					}
				}
			}
			$status_array["firewall"] = self::is_firewall_up();
			return $status_array;
		}
		
		public static function checkPacketTrace($file) {
			if((file_exists($file) == TRUE)) {
				if((file_exists("/var/tmp/packet-capture.pcap") == TRUE)) {
					return 1;
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		}

		public static function stopPacketTrace() {
			$cmd = "killall tcpdump";
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE,1);
			return $shell_status;
		}

		public static function getAppVersion() {
			$db = DB_Handle::Connect();
			$update = $db->getApplicationVer();
			echo $update[VERSION].".".$update[SUBVERSION]." ".$update[BUILD_INFO];
		}

		public static function getFullProductName() {
			$db = DB_Handle::Connect();
			$product = $db->getFullProductName();
			echo $product[FULL_PRODUCT_NAME];
		}

		public static function getSigUpdateVersion() {
			$db = DB_Handle::Connect();
			$update = $db->getSigUpdateVer();
			echo $update[PRODUCT_NAME]." " .$update[VERSION].".".$update[SUBVERSION]." ".$update[BUILD_INFO];
		}

		public static function setTZ($zone) {
			$zone_file="/etc/tzlist";
			if(!file_exists($zone_file))
				 return array();
			$handle = fopen($zone_file,"r");
			if($handle) {
				while(!feof($handle)) {
					$temp=explode("|",fgets($handle, 4096));
					$zone_arr[$temp[0]]=$temp[1];
				}
				fclose($handle);
			}
			$tz_file="/etc/TZ";
			$fh = fopen($tz_file,"w");
			fwrite($fh,trim($zone_arr[$zone]));
			fwrite($fh,PHP_EOL);
			fclose($fh);
		}

		public static function setTime($time) {
			$shell_obj = ShellCommand::getInstance();
			$month = ltrim($time[0],'0');
			$day = ltrim($time[1],'0');
			$time_hour = ltrim($time[2],'0');
			$minutes = ltrim($time[3],'0');
			$year = $time[4];
			$zone = trim($time[5]);
			if( $day >= 1 && $day <= 9) {
				$arrg_day = 0;
				$day = $arrg_day."".$day;
			}
			if( $month >= 1 && $month <= 9) {
				$arrg_month = 0;
				$month = $arrg_month."".$month;
			}
			if( $minutes >= 1 && $minutes <= 9) {
				$arrg_mnt = 0;
				$minutes = $arrg_mnt."".$minutes;
			}
			if( $time_hour >= 1 && $time_hour <= 9) {
				$arrg_hr = 0;
				$time_hour = $arrg_hr."".$time_hour;
			}
			SysCalls::setTZ($zone);
			$shell_status = $shell_obj->Execute("date $month$day$time_hour$minutes$year; hwclock -w",ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$time_ret =$shell_obj->getLastOutput();
			}
			return $time_ret;
		}

		public static function deleteDynBlRule($addr) {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("/usr/bin/dynbl2 '$addr'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
		}

		public static function deleteAllDynBlRule() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("/usr/bin/dynbl del", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
		}

		public static function getDynBlRules() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("/usr/bin/dynbl get", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			$shell_status = $shell_obj->Execute("sort -u -k 4 -o /tmp/dynbl.out.tmp /tmp/dynbl.out; mv -f /tmp/dynbl.out.tmp /tmp/dynbl.out",ShellCommand::FLAG_DONT_VALIDATE);
		}

		public static function deletePacketCapture() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("rm -f /var/tmp/packet-capture_*.pcap", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
		}

		public static function restoreDb($tmp_name) {
			if (!file_exists($tmp_name)) {
				return 1;
			}
			$filename = "restore_config";
			$cmd = 'restore_config "'.$tmp_name.'"';
			$res = self::executeShellscript($cmd,$filename);
			return $res;
		}

		public static function restoreDbMigrate() {
			$res2='/usr/bin/config_restore_migrate_mac';
			if($res2 == 0) {
				return 0;
			} else {
				return 1;
			}
		}

		public static function applyChangesRestore() {
			$dbh = DB_Handle::Connect();
			$dbh->createActionList(DEPLOY_MODULES::ALL, "Config has been Restored", "Config Restore", "");
			$dbh->createActionList(DEPLOY_MODULES::HOST_CONFIG, "Hostname has been Restored", HOST_CONFIG_STR , "");
		}

		public static function executeShellscript ($cmd,$filename,$nowait=0) {
			$path = "/var/tmp/.$filename";
			$shell_obj = ShellCommand::getInstance();
			$shell_obj->Execute("/usr/bin/execute_bg.sh ".$path." ".$cmd." 2>/dev/null &", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE,$nowait) ;
			return $path;
		}

		public static function serializeVal($args) {
			return implode(",", $args);
		}

		public static function unSerializeVal($val) {
			return explode(",", $val);
		}

		public static function getTotalMem() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("free -m | grep Mem | awk '{print $2}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if($shell_status == 0) {
				$res = $shell_obj->getLastOutput()."MB";
			}
			return $res;
		}

		public static function getTotalFlash() {
			$shell_obj = ShellCommand::getInstance();
			if(SysCalls::getSysArch() == "armv7l") {
				$shell_status = $shell_obj->Execute("df -h / | tail -1 | awk '{print $2,\"|\",$5}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			} else {
				$shell_status = $shell_obj->Execute("df -h / | tail -1 | awk '{print $1,\"|\",$4}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			}
			if($shell_status == 0) {
				$total_flash = $shell_obj->getLastOutput();
			}
			$return_flash_val = $total_flash."|".$used_flash;
			return $return_flash_val;
		}

		public static function getPing($count, $host) {
			$value;
			$ip = ip2long($host) !== false;
			if($ip == 1) {
				$ipaddr = $host;
			} else {
				$site = strtolower($host);
			}
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("ping -c '$count' '$host'");
			if($shell_status == 0) {
				$value = $shell_obj->getLastOutput();
			}
			return $value;
		}

		public static function getInterfaces() {
			$value;
			$result;
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("cat /proc/net/dev | grep ':' | cut -d ':' -f 1",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$result =$shell_obj->getLastOutput();
			}
			return $result;
		}

		public static function getRealInterfaces() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("ifconfig -a | grep Ethernet | awk '{ print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$ret_interfaces1 = $shell_obj->getLastOutput();
			}
			$ret_interfaces = explode(" ", $ret_interfaces1);
			foreach ( $ret_interfaces as $vlan ) {
				list($vlan_arr[]) = explode(".", $vlan);
			}
			$vlan_ifaces = array_unique($vlan_arr);
			foreach ( $vlan_ifaces as $vip ) {
				list($vip_arr[]) = explode(":", $vip);
			}
			$transInt = SysCalls::getTranscodingInterface();
			$intArr = array($transInt);
			$real_interfaces = array_unique($vip_arr);
			$realintArr = array_diff($real_interfaces,$intArr);
			return array_values($realintArr);
		}

		public static function getSIPInterfaces() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("ifconfig -a | grep Ethernet | awk '{ print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$ret_interfaces1 =$shell_obj->getLastOutput();
			}
			$ret_interfaces = explode(" ", $ret_interfaces1);
			$transInt = SysCalls::getTranscodingInterface();
			if ( $transInt != null ) {
				$intArr = array($transInt);
			}
			if ( $intArr != null ) {
				$sipintArr = array_diff($ret_interfaces,$intArr);
			} else {
				$sipintArr = $ret_interfaces;
			}
			if ( $sipintArr != null ) {
				$sipInterfaces = array_values($sipintArr);
			}
			return $sipInterfaces; 
		}

		public static function getrealnames($interfaces) {
			$rintername =array();
			for($i=0;$i<count($interfaces);$i++) {
				if ($interfaces[$i] != "") {
					if(strstr($interfaces[$i],".")) {
						$d_arry = explode(".",$interfaces[$i]);
					} else {
						$d_arry = explode(":",$interfaces[$i]);
					}
					array_push($rintername,$d_arry[0]);
				}
			}
			return $rintername;
		}

		public static function getTracert($icmp, $hop, $host) {
			$value;
			$shell_obj = ShellCommand::getInstance();
			if($icmp == "on") {
				$shell_status = $shell_obj->Execute("traceroute -m '$hop' -I '$host'");
				if($shell_status == 0) {
					$value = $shell_obj->getLastOutput();
				}
			} else {
				$shell_status = $shell_obj->Execute("traceroute -m '$hop' '$host'") ;
				if($shell_status == 0) {
					$value = $shell_obj->getLastOutput();
				}
			}
			return $value;
		}

		public static function restart_dpi() {
			$statOutFile = "/tmp/.bloxconfig.$id.ret";
			$shell_obj = ShellCommand::getInstance();
			$id=DEPLOY_MODULES::SIP_SECURITY_CONFIG;
			$ret = $shell_obj->Execute("/usr/bin/execute_bg.sh $statOutFile bloxconfig $id", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			return $ret;
		}

		public static function is_firewall_up() {
			$shell_obj = ShellCommand::getInstance();
			$cmd="[ $(service iptables status | wc -l) -gt 60 ] && echo 1" ;
			return !($shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE));
		}

		public static function is_dpi_up() {
			$shell_obj = ShellCommand::getInstance();
			$cmd="service snort status && service snortsam status" ;
			return !($shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE));
		}

		public static function getSaltVal() {
			return "utmshield";
		}

		public static function computeHash($password) {
			$salt = SysCalls::getSaltVal();
			$hash = sha1($password.$salt, false);
			return $hash;
		}

		public static function logArchiveInfo() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("/usr/bin/logs_usage", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$result =$shell_obj->getLastOutput();
			}
			$file = '/tmp/logs_archive.info';
			if (file_exists($file) == TRUE) {
				$fh = fopen($file,'r');
				$contents = file_get_contents($file,4096);
				fclose($fh);
			} else {
				return;
			}
			return $contents;
		}

		public static function geoIPUpdate () {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("/usr/bin/geoip_update_wrapper", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				$run_geo_ip =$shell_obj->getLastOutput();
			}
			return $run_geo_ip;
		}

		public static function getInterfacesList() {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("ifconfig -a | grep Ethernet | awk '{ print $1 }'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if( $shell_status == 0) {
				 $ret_interfaces =$shell_obj->getLastOutput();
			}
			return $ret_interfaces;
		}

		public static function getLAN_WANIPAddress($iface) {
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("ifconfig $iface| grep 'inet addr:' | cut -d: -f2 | awk '{print $1 }'",ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE ) ;
			if( $shell_status == 0) {
				$ipaddress =$shell_obj->getLastOutput();
			}
			return $ipaddress;
		}

		public static function ResetNetworkInterface() {
			if(SysCalls::getSysArch() == "armv7l") {
				$shell_obj = ShellCommand::getInstance();
				$shell_status = $shell_obj->Execute("/usr/bin/reset_network.sh 1", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			}
		}

		public static function setDefaultEntry() {
			DB_Handle::DisConnect();//close the sqlite3 DB
			$shell_obj = ShellCommand::getInstance();
			$cmd = "/usr/bin/freeblox_default_enter.sh /etc/freeblox_conf/DB/freeblox.db.default /etc/freeblox_conf/DB/freeblox.db";
			$shell_status = $shell_obj->Execute("$cmd", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			DB_Handle::Connect();//close the sqlite3 DB
		}

		public static function detectInterfaces() {
			$cmd3 = "/usr/local/ngx-allomts-server/sbin/detect_transcoding_card";
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("$cmd3", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			sleep(1);
		}

		public static function checkUbuntuCentos() {
			$shell_obj = ShellCommand::getInstance();
			$com="python -mplatform | grep -i ubuntu";
			$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			$out = "";
			if($ret == 0) {
				$out=$shell_obj->getLastOutput();
			}
			return $out;
		}

		public static function getSysArch() {
			$shell_obj = ShellCommand::getInstance();
			$com = "arch";
			$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if($ret == 0) {
				$out=trim($shell_obj->getLastOutput());
			}
			return $out;
		}

		public static function getSysInfo() {
			$sys_info = array();
			$getarch = SysCalls::getSysArch();
			$str_arch = "CPU Architecture |".$getarch;
			$shell_obj = ShellCommand::getInstance();
			$ret_kernel = $shell_obj->Execute("uname -r", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($ret_kernel == 0) {
				$out_kernel=trim($shell_obj->getLastOutput());
				$str_kernel = "Linux Kernel Version |".$out_kernel;
			}
			if($getarch == 'armv7l') {
				$com = "egrep -i '(model name)|(BogoMIPS).*:' /proc/cpuinfo  | sort | uniq";
				$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($ret == 0) {
					$out=trim($shell_obj->getLastOutput());
					$linetext = explode("\n",$out); 
					$data=explode(":",$linetext[1]);
					$str = "CPU Model |".$data[1];
					array_push($sys_info, $str);
					array_push($sys_info, $str_arch);
					$data=explode(":",$linetext[0]);
					$str = "CPU Speed |".$data[1]." MHz";
					array_push($sys_info, $str);
					array_push($sys_info, $str_kernel);
					
				}
				$com = "tail -3 /proc/cpuinfo";
				$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($ret == 0) {
					$out=trim($shell_obj->getLastOutput());
					$linetext = explode("\n",$out); 
					$data=explode(":",$linetext[0]);
					$str = "Hardware |".$data[1];
					array_push($sys_info, $str);
					$data=explode(":",$linetext[2]);
					$str = "Serial |".$data[1];
					array_push($sys_info, $str);
					
				}
			} else {
				$com = "egrep -i '(model name)|(cpu MHz).*:' /proc/cpuinfo  | sort | uniq";
				$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($ret == 0) {
					$out=trim($shell_obj->getLastOutput());
					$linetext = explode("\n",$out); 
					$data=explode(":",$linetext[1]);
					$str = "CPU Model |".$data[1];
					array_push($sys_info, $str);
					array_push($sys_info, $str_arch);
					$data=explode(":",$linetext[0]);
					$str = "CPU Speed |".$data[1]." MHz";
					array_push($sys_info, $str);
					array_push($sys_info, $str_kernel);
				}
			}
			return $sys_info;
		}

		public static function getSerialno() {
			$serialno ="";
			$getarch = SysCalls::getSysArch();
			if($getarch == 'armv7l') {
				$serial = array();
				$shell_obj = ShellCommand::getInstance();
				$com = "grep -i serial /proc/cpuinfo";
				$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				if($ret == 0) {
					$out=trim($shell_obj->getLastOutput());
				}
				$serial = explode(":",$out);
				$serialno = $serial[1];
			}
			return $serialno;
		}

		public static function getOpensipsVersion() {
			$shell_obj = ShellCommand::getInstance();
			$com="/usr/local/sbin/opensips -V | grep version |awk '{print $3}'";
			$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			$out = "" ;
			if($ret == 0) {
				$out=$shell_obj->getLastOutput();
			}
			return $out;
		}

		public static function setDPI($status) {
			$shell_obj = ShellCommand::getInstance();
			if($status == 1) {
				$shell_obj->Execute("service snortsam start;service snort start", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			} else {
				$shell_obj->Execute("service snort stop;service snortsam stop", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			}
		}

		public static function setFirewall($status) {
			$shell_obj = ShellCommand::getInstance();
			$com="python -mplatform | grep -i ubuntu";
			$ret = $shell_obj->Execute("$com", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			$out = "" ;
			if($ret == 0) {
				$out=$shell_obj->getLastOutput();
			}
			if($status == 1) {
				$shell_obj->Execute("service iptables start", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				if ($out!=="") {
					$shell_obj->Execute("update-rc.d iptables defaults", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				} else {
					$shell_obj->Execute("/sbin/chkconfig iptables --add", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
					$shell_obj->Execute("/sbin/chkconfig --level 2345 iptables on", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				}
			} else {
				$shell_obj->Execute("service iptables stop", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				//if ($out!==""){
				//	$shell_obj->Execute("update-rc.d -f iptables remove", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				//} else {
				//	$shell_obj->Execute("/sbin/chkconfig ipsec --del", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				//}
			}
		}

		public static function getgateway($devid) {
			$rval = "&nbsp;";
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute("ip route show default | grep default | awk '{print $5\":\"$3}'", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				$gateway =trim($shell_obj->getLastOutput());
			}
			$str_first = explode(":",$gateway);
			if(trim($str_first[0]) == $devid) {
				$rval = $str_first[1];
			}
			return $rval;
		}

		public static function execCmd2($cmd) {
			$values;
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if( $shell_status == 0) {
				 $values =trim($shell_obj->getLastOutput());
			} 
			return $values;
		}

		public static function getTranscodingStatus() {
			$readFile = "/etc/.use_primary_tgcard";
			if (file_exists($readFile) == TRUE) {
				$handle = fopen($readFile, "r");
				if ($handle) {
					$getTransCodingStatus = fgetc($handle);
					fclose($handle);
					return $getTransCodingStatus;
				}
			} else {
				return FALSE;
			}
		}

		public static function getCAConfigStatus() {
			$readFile = "/etc/.ca_config_mode";
			if (file_exists($readFile) == TRUE) {
				$handle = fopen($readFile, "r");
				if ($handle) {
					while (!feof($handle)) {
						$get_ca_status = file_get_contents($readFile,true);
						return $get_ca_status;
					}
				}
				fclose($handle);
			} else {
				return FALSE;
			}
		}

		public static function getTranscodingInterface() {
			$file = "/etc/.tg_if_list";
			if (file_exists($file) == TRUE ) {
				$handle = fopen($file,"r");
				if ($handle) {
					while (!feof($handle)) {
						$get_tg_if_list = file_get_contents($file,true);
						$transcodingInterface = explode(" ",trim($get_tg_if_list));
						return $transcodingInterface[0];
					}
					fclose($handle);
				}
			} else {
				return FALSE;
			}
		}

		public static function array_getTranscodingInterface() {
			$r_arr =array();
			$file = "/etc/.tg_if_list";
			if (file_exists($file) == TRUE ) {
				$F = fopen($file,"r");
				$content = stream_get_contents($F);
				fclose($F);
				$lines = explode(PHP_EOL, $content);
				foreach($lines as $line) {
					$tr_id_a=explode(" ",trim($line));
					array_push($r_arr,$tr_id_a[0]);
				}
			}
			return $r_arr;
		}


		public static function generateCaCert($cn, $co, $state, $org, $email, $keylen, $validity) {
			$shell_obj = ShellCommand::getInstance();
			$org=str_replace(" ","_",$org);
			$cmd="conf_ca_cert GENERATE $cn $co $state $org $email $keylen $validity";
			$ret = "";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($shell_status == 0) {
				$ret = $shell_obj->getLastOutput();
			}
			return $ret;
		}

		public static function uploadCaCert($name, $tmp_name) {
			$shell_obj = ShellCommand::getInstance();
			$path = "/tmp/";
			$filepath = "$path/$name";
			move_uploaded_file($tmp_name, $filepath);
			$cmd = "conf_ca_cert UPLOAD $filepath";
			$ret = "";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($shell_status == 0) {
				$ret = $shell_obj->getLastOutput();
			}
			return $ret ;
		}

		public static function getCaCertDetails() {
			$shell_obj = ShellCommand::getInstance();
			$cmd="conf_ca_cert GET_CERT_DETAILS";
			$ret = "";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($shell_status == 0) {
				$ret = $shell_obj->getLastOutput();
			}
			return $ret ;
		}

		public static function generateServerCert($cn, $co, $state, $org, $email, $orgunit) {
			$shell_obj = ShellCommand::getInstance();
			$org=str_replace(" ","_",$org);
			$orgunit=str_replace(" ","_",$orgunit);
			if( self::isFilePresentInDir($cn,"/usr/local/etc/opensips/tls/") == 1 ) {
				return 1;
			} else {
				$cmd = "conf_server_cert GENERATE $cn $co $state $org $email $orgunit";
				$ret = "";
				$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				if($shell_status == 0) {
					$ret = $shell_obj->getLastOutput();
				}
				return $ret ;
			}
		}

		public static function deleteServerCert($cn) {
			$shell_obj = ShellCommand::getInstance();
			$cmd = "conf_server_cert DELETE $cn";
			$ret = "";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($shell_status == 0) {
				$ret = $shell_obj->getLastOutput();
			}
			return $ret ;
		}

		public static function deleteServerCertCfg($cn) {
			$shell_obj = ShellCommand::getInstance();
			$cmd = "conf_server_cert DELETE_CFG";
			$ret = "";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($shell_status == 0) {
				$ret = $shell_obj->getLastOutput();
			}
			return $ret ;
		}

		public static function uploadServerCert($name,$tmp_name,$passphrase) {
			$shell_obj = ShellCommand::getInstance();
			$path = "/tmp/";
			$filepath = "$path/$name";
			move_uploaded_file($tmp_name,$filepath);
			$cn=explode(".",$name);
			if( self::isFilePresentInDir($cn[0],"/usr/local/etc/opensips/tls/") == 1 )
				return 1;
			else {
				$cmd = "conf_server_cert UPLOAD \"$filepath\" \"$cn[0]\" \"$passphrase\"";
				$ret = "";
				$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($shell_status == 0) {
					$ret = $shell_obj->getLastOutput();
				}
				return $ret ;
			}
		}

		public static function getServerCertDetails($cn) {
			$shell_obj = ShellCommand::getInstance();
			$cmd = "conf_server_cert GET_CERT_DETAILS $cn";
			$ret = "";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				$ret = $shell_obj->getLastOutput();
			return $ret ;
		}

		public static function generateClientCert($cn, $co, $state, $org, $email, $keylen, $validity, $ou) {
			$shell_obj = ShellCommand::getInstance();
			$org=str_replace(" ","_",$org);
			$ou=str_replace(" ","_",$ou);
			if( self::isFilePresentInDir($cn,"/usr/local/etc/opensips/tls/") == 1 ) {
				return 1;
			}
			else {
				$cmd = "conf_client_cert GENERATE $cn $co $state $org $email $keylen $validity $ou";
				$ret = "";
				$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
				if($shell_status == 0) {
					$ret = $shell_obj->getLastOutput();
				}
				return $ret ;
			}
		}

		public static function deleteClientCert($cn) {
				$shell_obj = ShellCommand::getInstance();
				$cmd = "conf_client_cert DELETE $cn";
				$ret = "";
				$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($shell_status == 0) {
					$ret = $shell_obj->getLastOutput();
				}
				return $ret;
		}

		public static function getCertList() {
			$readFile = SysCalls::$cert_list_file;
			if (file_exists($readFile)) {
				$handle = fopen($readFile, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = file_get_contents($readFile, true);
						$getCertDetails = explode("\n",$buffer);
						return $getCertDetails;
					}
					fclose($handle);
				}
			} else {
				return FALSE;
			}
		}

		public static function getClientCertDetails($cn) {
			$shell_obj = ShellCommand::getInstance();
			$cmd = "conf_client_cert GET_CERT_DETAILS $cn";
			$ret = "";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			$ret = $shell_obj->getLastOutput();
			return $ret ;
		}

		public static function getEncryptionStrength() {
			$shell_obj = ShellCommand::getInstance();
			$encStrength = array("1024","2048","3072");
			return $encStrength;
		}

		public static function isFilePresentInDir($file,$dir) {
			if($file== NULL || $dir == NULL) {
				return 0;
			}
			$dir_list = scandir($dir);
			if (in_array( $file , $dir_list)) {
				return 1;
			} else {
				return 0;
			}
		}

		public static function sshGetPort() {
			$shell_obj = ShellCommand::getInstance();
			$cmd="cat /var/tmp/.siab_port";
			$r_str = "" ;
			$ret = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($ret == 0) {
				$r_str = $shell_obj->getLastOutput();
			}
			return $r_str;
		}

		public static function sshTerminal($shellinbox_status) {
			$shell_obj = ShellCommand::getInstance();
			if($shellinbox_status == "1") {
				$cmd = '/usr/bin/ShellInABoxCtrl.sh stop';
				$discon_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				if($discon_status == 0) {
					$cmd = '/usr/bin/ShellInABoxCtrl.sh start '.$_SERVER['REMOTE_ADDR'];
					$con_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE,0,true); //nowait=0, nonblock=true
					if($con_status == 0) {
						return 0;
					} else {
						return 1;
					}
				} else {
					return 1;
				}
			} else {
				$cmd = '/usr/bin/ShellInABoxCtrl.sh stop';
				$discon_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				return $discon_status;
			}
		}

		public static function checkSshConnection() {
			$cmd = '[ -f /var/run/shellinaboxd.pid ] && ps -ax | grep -w "$(cat /var/run/shellinaboxd.pid)"';
			$shell_obj = ShellCommand::getInstance();
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			if($shell_status == 0) {
				return 1;
			} else {
				return 0;
			}
		}

		public static function checkUpgAvail() {
			$ignore_version_file = "/etc/freeblox_conf/.ignore_version" ;
			$db = DB_Handle::Connect();
			$update = $db->getApplicationVer(); //Current version
			$cur_version = $update[VERSION]."-".$update[BUILD_INFO];
			$latest_version = "" ;
			$dev_settings = DeviceSettingsModel::getInstance();
			$autoupg_status = $dev_settings->get(DeviceSettingsModel::$AUTO_UPGRADE); 
			$autoUpgURL = $dev_settings->get(DeviceSettingsModel::$AUTO_UPGRADE_URL); 
			$getArch = self::getSysArch();
			if($autoupg_status == "1") {
				$ret = @file_get_contents("$autoUpgURL?version=latest&arch=$getArch");
                                if ($ret ==! false ) {
					$ver_j = json_decode($ret,true);
					if(isset($ver_j["res"][0])) {
						$latest_version_str = $ver_j["res"][0] ;
						$latest_version_array = explode("/",$latest_version_str);
						$latest_version = $latest_version_array[1];
					}
				} else {
					return 0;
				}
				$ignore_version = @file_get_contents($ignore_version_file) ;
				if($latest_version != $cur_version && 
						$latest_version != $ignore_version) {
					return $latest_version;
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		}

		public static function autoUpgIgnore() {
			$ignore_version_file = "/etc/freeblox_conf/.ignore_version" ;
			$db = DB_Handle::Connect();
			$update = $db->getApplicationVer(); //Current version
			$cur_version = $update[VERSION].".".$update[SUBVERSION]."-".$update[BUILD_INFO];
			$latest_version = "" ;
			$dev_settings = DeviceSettingsModel::getInstance();
			$autoupg_status = $dev_settings->get(DeviceSettingsModel::$AUTO_UPGRADE); 
			$autoUpgURL = $dev_settings->get(DeviceSettingsModel::$AUTO_UPGRADE_URL); 
			$getArch = self::getSysArch();
			if($autoupg_status == "1") {
				$ret = file_get_contents("$autoUpgURL?version=latest&arch=$getArch");
				$ver_j = json_decode($ret,true);
				if(isset($ver_j["res"][0])) {
					$latest_version_str = $ver_j["res"][0] ;
					$latest_version_array = explode("/",$latest_version_str);
					$latest_version = $latest_version_array[1];
					if($latest_version != $cur_version) {
						$fh = fopen($ignore_version_file,'w');
						fwrite($fh,trim($latest_version));
						fclose($fh);
					}
				}
			}
			return 0;
		}

		public static function autoUpgNow() {
			$shell_obj = ShellCommand::getInstance();
			$upgfile = "/var/tmp/blox_update.tgz";
			if (file_exists($upgfile)) {
				$cmd = "rm  -f $upgfile";
				$shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			}
			$getArch = self::getSysArch();
			$dev_settings = DeviceSettingsModel::getInstance();
			$autoUpgURL = $dev_settings->get(DeviceSettingsModel::$AUTO_UPGRADE_URL);
			$cmd = "curl '$autoUpgURL?download=latest&arch=$getArch' -o $upgfile";
			$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			if($shell_status == 0) {
				return 1;
			} else {
				return 0;
			}
		}
	}
?>
