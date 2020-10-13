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
	include_once("Model/sys_calls.php");
	include_once("Model/sip_profile_model.php");
	include_once("Model/media_settings_model.php");
	include_once("Model/devicelist_settings_model.php");
	include_once("Model/db_mgr.php");
	include_once("Model/sbc_log_mgr.php");

	$sip_config = SIPProfileConfigModel::getInstance();
	$device_config = DeviceSettingsModel::getInstance();
	$media_config = MediaConfigModel::getInstance();
	$getTransInt = SysCalls::getTranscodingInterface();
	$devicelist_config = DevicelistSettingsModel::getInstance();

	if ( isset($_POST) && $_POST['sip_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$sipinterface_interface = explode("|",$ret_interface_type);
		$ifaceType = $sipinterface_interface[1];
		$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
		if($sipinterface_interface[0] == "") {
			$virtualip = $devicelist_config->getVirtualInterface($interfacename);
			if($virtualip != "") {
				$ipAddress = $devicelist_config->getVirtualip($interfacename);
				$interfacename = $virtualip;
				$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
				$sipinterface_interface = explode("|",$ret_interface_type);
				$ifaceType = $sipinterface_interface[1];
			}
		}
		$ipInfo = $ifaceType." / ". $ipAddress;
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['internal_post'] == "1" ) {
		$media_internal_interface = $_POST['internal_ip'];
		$virtualip = $devicelist_config->getVirtualInterface($media_internal_interface);
		if($virtualip != "") {
			$ipAddress = $devicelist_config->getVirtualip($media_internal_interface);
			$media_internal_interface = $virtualip;
		} else {
			$ret_interface_type = $devicelist_config->getInterfaceByName($media_internal_interface);
			$media_internal_interface1 = explode("|",$ret_interface_type);
			$ipAddress = SysCalls::getLAN_WANIPAddress($media_internal_interface1[0]);
		}
		print $ipAddress;
	}

	if ( isset($_POST) && $_POST['external_post'] == "1" ) {
		$media_external_interface = $_POST['external_ip'];
		$virtualip = $devicelist_config->getVirtualInterface($media_external_interface);
		if($virtualip != "") {
			$ipAddress = $devicelist_config->getVirtualip($media_external_interface);
			$media_external_interface = $virtualip;
		} else {
			$ret_interface_type = $devicelist_config->getInterfaceByName($media_external_interface);
			$media_external_interface1 = explode("|",$ret_interface_type);
			$ipAddress = SysCalls::getLAN_WANIPAddress($media_external_interface1[0]);
		}
		print $ipAddress;
	}

	if ( isset($_POST) && $_POST['media_post'] == "1" ) {
		$media_interface = $_POST['media_ip'];
		if ( $media_interface == "No Transcoding") {
			$ipAddress = "";
		} else {
			$virtualip = $devicelist_config->getVirtualInterface($media_interface);
			if($virtualip != "") {
				$ipAddress = $devicelist_config->getVirtualip($media_interface);
				$media_interface = $virtualip;
			} else {
				
				$ret_interface_type = $devicelist_config->getInterfaceByName($media_interface);
				$media_trans_interface1 = explode("|",$ret_interface_type);
				$ipAddress = SysCalls::getLAN_WANIPAddress($media_trans_interface1[0]);
			}
		}
		print $ipAddress;
	}

	if ( isset($_POST) && $_POST['lan_post'] == "1" ) {
		$trunk_lan_iface =  $_POST['trunk_lan_ip'];
		$interfacename = $sip_config->getSipProfileInterfaceAddress($trunk_lan_iface);
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$sipinterface_interface = explode("|",$ret_interface_type);
		$ifaceType = $sipinterface_interface[1];
		$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
		if($sipinterface_interface[0] == "") {
			$virtualip = $devicelist_config->getVirtualInterface($interfacename);
			if($virtualip != "") {
				$ipAddress = $devicelist_config->getVirtualip($interfacename);
				$interfacename = $virtualip;
				$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
				$sipinterface_interface = explode("|",$ret_interface_type);
				$ifaceType = $sipinterface_interface[1];
			}
		}
		$ipInfo = $ifaceType." / ". $ipAddress;
		print $ipInfo; 
	}

	if ( isset($_POST) && $_POST['wan_post'] == "1" ) {
		$trunk_wan_iface =  $_POST['trunk_wan_ip'];
		$interfacename = $sip_config->getSipProfileInterfaceAddress($trunk_wan_iface);
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$sipinterface_interface = explode("|",$ret_interface_type);
		$ifaceType = $sipinterface_interface[1];
		$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
		if($sipinterface_interface[0] == "") {
			$virtualip = $devicelist_config->getVirtualInterface($interfacename);
			if($virtualip != "") {
				$ipAddress = $devicelist_config->getVirtualip($interfacename);
				$interfacename = $virtualip;
				$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
				$sipinterface_interface = explode("|",$ret_interface_type);
				$ifaceType = $sipinterface_interface[1];
			}
		}
		$ipInfo = $ifaceType." / ". $ipAddress;
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['trunk_media_post'] == "1" ) {
		$trunk_media_iface =  $_POST['trunk_media_ip'];
		$interfacename = $media_config->getMediaProfileInterfaceAddress($trunk_media_iface);
		if($interfacename != "") {
			$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
			$sipinterface_interface = explode("|",$ret_interface_type);
			$ifaceType = $sipinterface_interface[1];
			$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
			if($sipinterface_interface[0] == "") {
				$virtualip = $devicelist_config->getVirtualInterface($interfacename);
				if($virtualip != "") {
					$ipAddress = $devicelist_config->getVirtualip($interfacename);
					$interfacename = $virtualip;
					$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
					$sipinterface_interface = explode("|",$ret_interface_type);
					$ifaceType = $sipinterface_interface[1];

				}
			}
			$ipInfo = $ifaceType." / ". $ipAddress;
		} else {
			$ipInfo = "No transcoding";
		}
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['roam_lan_post'] == "1" ) {
		$roam_lan_iface =  $_POST['roam_lan_ip'];
		$interfacename = $sip_config->getSipProfileInterfaceAddress($roam_lan_iface);
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$sipinterface_interface = explode("|",$ret_interface_type);
		$ifaceType = $sipinterface_interface[1];
		$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
		if($sipinterface_interface[0] == "") {
			$virtualip = $devicelist_config->getVirtualInterface($interfacename);
			if($virtualip != "") {
				$ipAddress = $devicelist_config->getVirtualip($interfacename);
				$interfacename = $virtualip;
				$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
				$sipinterface_interface = explode("|",$ret_interface_type);
				$ifaceType = $sipinterface_interface[1];
			}
		}
		$ipInfo = $ifaceType." / ". $ipAddress;
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['roam_wan_post'] == "1" ) {
		$roam_wan_iface =  $_POST['roam_wan_ip'];
		$interfacename = $sip_config->getSipProfileInterfaceAddress($roam_wan_iface);
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$sipinterface_interface = explode("|",$ret_interface_type);
		$ifaceType = $sipinterface_interface[1];
		$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
		if($sipinterface_interface[0] == "") {
			$virtualip = $devicelist_config->getVirtualInterface($interfacename);
			if($virtualip != "") {
				$ipAddress = $devicelist_config->getVirtualip($interfacename);
				$interfacename = $virtualip;
				$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
				$sipinterface_interface = explode("|",$ret_interface_type);
				$ifaceType = $sipinterface_interface[1];
			}
		}
		$ipInfo = $ifaceType." / ". $ipAddress;
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['roam_media_post'] == "1" ) {
		$roam_media_iface =  $_POST['roam_media_ip'];
		$interfacename = $media_config->getMediaProfileInterfaceAddress($roam_media_iface);
		if($interfacename != "") {
			$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
			$sipinterface_interface = explode("|",$ret_interface_type);
			$ifaceType = $sipinterface_interface[1];
			$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
			if($sipinterface_interface[0] == "") {
				$virtualip = $devicelist_config->getVirtualInterface($interfacename);
				if($virtualip != "") {
					$ipAddress = $devicelist_config->getVirtualip($interfacename);
					$interfacename = $virtualip;
					$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
					$sipinterface_interface = explode("|",$ret_interface_type);
					$ifaceType = $sipinterface_interface[1];

				}
			}
			$ipInfo = $ifaceType." / ". $ipAddress;
		} else {
			$ipInfo = "No transcoding";
		}
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['lcr_lan_post'] == "1" ) {
		$lcr_lan_iface =  $_POST['lcr_lan_ip'];
		$interfacename = $sip_config->getSipProfileInterfaceAddress($lcr_lan_iface);
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$sipinterface_interface = explode("|",$ret_interface_type);
		$ifaceType = $sipinterface_interface[1];
		$ipAddress = SysCalls::getLAN_WANIPAddress($sipinterface_interface[0]);
		if($sipinterface_interface[0] == "") {
			$virtualip = $devicelist_config->getVirtualInterface($interfacename);
			if($virtualip != "") {
				$ipAddress = $devicelist_config->getVirtualip($interfacename);
				$interfacename = $virtualip;
				$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
				$sipinterface_interface = explode("|",$ret_interface_type);
				$ifaceType = $sipinterface_interface[1];
			}
		}
		$ipInfo = $ifaceType." / ". $ipAddress;
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['vip_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$vip_interface = explode("|",$ret_interface_type);
		$ipAddress = SysCalls::getLAN_WANIPAddress($vip_interface[0]);
		$ifaceType = $vip_interface[1];
		if ( $ifaceType != "" ) {
			$ipInfo = $ifaceType." / ". $ipAddress;
		} else {
			$ipInfo = $ipAddress;
		}
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['vlan_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$vlan_interface = explode("|",$ret_interface_type);
		$ipAddress = SysCalls::getLAN_WANIPAddress($vlan_interface[0]);
		$ifaceType = $vlan_interface[1];
		if ( $ifaceType != "" ) {
			$ipInfo = $ifaceType." / ". $ipAddress;
		} else {
			$ipInfo = $ipAddress;
		}
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['route_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$route_interface = explode("|",$ret_interface_type);
		$ipAddress = SysCalls::getLAN_WANIPAddress($route_interface[0]);
		$ifaceType = $route_interface[1];
		if ( $ifaceType != "" ) {
			$ipInfo = $ifaceType." / ". $ipAddress;
		} else {
			$ipInfo = $ipAddress;
		}
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['portfwd_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$ret_interface_type = $devicelist_config->getInterfaceByName($interfacename);
		$portfwd_interface = explode("|",$ret_interface_type);
		$ipAddress = SysCalls::getLAN_WANIPAddress($portfwd_interface[0]);
		$ifaceType = $portfwd_interface[1];
		if ( $ifaceType != "" ) {
			$ipInfo = $ifaceType." / ". $ipAddress;
		} else {
			$ipInfo = $ipAddress;
		}
		print $ipInfo;
	}

	if ( isset($_POST) && $_POST['vip_ext_inter_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$externalinterface = $devicelist_config->checkExternalInterface($interfacename);
		print $externalinterface;
	}

	if ( isset($_POST) && $_POST['vlan_ext_inter_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$externalinterface1 = $devicelist_config->checkExternalInterface($interfacename);
		print $externalinterface1;
	}

	if ( isset($_POST) && $_POST['natexternal_post'] == "1" ) {
		$interfacename = $_POST['ip_iface'];
		$externalinterface1 = $devicelist_config->checkVirtualExternalInterface($interfacename);
		print $externalinterface1;
	}

	if ( isset($_POST) && $_POST['FirmUpgval'] == "1" ) {
		$dbh = DB_Handle::Connect();
		DB_Handle::ignoreConfigChanges();
		$dbh->deleteActionList();
		print "1";
	}

	if( isset($_POST['factoryreset']) && isset($_POST['checkstatus'])) {
		$ret = sysCalls::checkFileStatus("factoryreset");
		print trim($ret);
	}

	if( isset($_POST['firmware_upg'])  && isset($_POST['filename']) ) {
		$ret = SysCalls::installUploadedFirmware($_POST['filename']);
		sleep(5) ;
		print trim($ret);
	} else if( isset($_POST['firmware_upg'])  && isset($_POST['checkstatus']) ){
		$ret = sysCalls::checkFileStatus("firmware_upg");
		print trim($ret);
	} else if( isset($_POST['restart'])){
		SysCalls::sysRestart();
		print "1";
	}

//Auto Upgrade
	if( isset($_POST['autoUpgNow']) ) {
		$ret = SysCalls::autoUpgNow();
		sleep(5) ;
		print trim($ret);
	} 
	if(isset($_POST['autoUpgIgnore'])){
		$ret = sysCalls::autoUpgIgnore();
		print trim($ret);
	}
//Auto Upgrade

	if( isset($_POST['restore_device']) && isset($_POST['filename']) ) {
		$ret = SysCalls::restoreDb($_POST['filename']);
		print $ret;
	} else if( isset($_POST['restore_device']) && isset($_POST['checkstatus']) ) {
		$ret = SysCalls::checkFileStatus("restore_config");
		if($ret == "0") { // restore migrate scripts
			$ret2 = SysCalls::restoreDbMigrate(); 
		}
		if($ret2 == 0) { // Adding to Action list
			SysCalls::applyChangesRestore();
			print "1";
		} else {
			print "0";
		}
	}

	if(isset($_POST['restoredb'])) {
		$cur_dbfile = "/etc/freeblox_conf/DB/freeblox.db";
		$bk_dbfile = "/etc/freeblox_conf/DB/freeblox.db.cr";
		if (!rename($bk_dbfile, $cur_dbfile)) {
			echo "failed to copy $bk_dbfile...\n";
		} else {
			//print_r(error_get_last());
			echo "copied $bk_dbfile...\n";
		}
	}

	if(isset($_POST['deletedb'])) {
		$res = unlink("/etc/freeblox_conf/DB/freeblox.db.cr");
		echo $res;
	}

	if(isset($_POST['pwd_auth'])) {
		$sbclog_obj = SBCLogMgr::getInstance();
		$auth_pwd=$_POST['pwd'];
		$records=$_POST['del_record'];
		$res =  $sbclog_obj->deleteCDRReports($records,$auth_pwd);
		if($res == 0) {
			echo "1";
		} else if($res == 2) {
			echo "2";
		} else {
			echo "3";
		}
	}

	if(isset($_POST['ssh_pwd_auth'])) {
		$post_pwd=$_POST['pwd'];
		$obj = WebUsers::getConfig();
		if($post_pwd != "") {
			$dbpwd = $obj -> getUserPassword("admin");
			$post_md5_pwd = md5($post_pwd);
			if($dbpwd == $post_md5_pwd) {
				$ret_start = SysCalls::sshTerminal("1");
				if($ret_start == 0) {
					$shell_port = SysCalls::sshGetPort();
					if($shell_port != "") {
						echo $shell_port;
					} else {
						echo "1";
					}
				} else {
					echo "1";
				}
			} else {
				echo "0";
			}
		}
	}

	if(isset($_POST['ssh_terminal'])) {
		$ret = SysCalls::sshTerminal("0");
		if($ret == 0) {
			echo "1";
		} else {
			echo "0";
		}
	}

	if(isset($_POST['check_tcpdump'])) {
		$download_enable = SysCalls::checkPacketTrace("/var/tmp/.packet-capture.pcap.ret");
		echo $download_enable;
	}
?>
