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
	include_once("trunk_config_model.php");
	include_once ("sbc_log_mgr.php");
	include_once("sip_profile_model.php");
	include_once("shell_command.php");

	class AORRecord {
		public $key;
		public $state;
		public $last_reg_sent;
		public $reg_t_out;
		public $registrar;
		public $binding;
	};

	function parseAorRecords() {
		$shell_obj = ShellCommand::getInstance();
		$AORRecords = array();
		$res = $shell_obj->Execute('/usr/local/sbin/opensipsctl fifo reg_list > "/tmp/res.txt"',ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
		if(!file_exists("/tmp/res.txt"))
			return "";
		$file = fopen("/tmp/res.txt", "r");
		$i = 0;
		$aor_record = NULL;
		while (!feof($file)) {
			$line = fgets($file);
			$toks = explode(':: ', $line);
			if ( trim($toks[0]) == "AOR" ) {
				$aor_record = new AORRecord();
				$aor_record->key = trim($toks[1]);
			} else if ( trim($toks[0]) == "state" ) {
				$aor_record->state = trim($toks[1]);
			} else if ( trim($toks[0]) == "last_register_sent" ) {
				$aor_record->last_reg_sent = trim($toks[1]);
			} else if ( trim($toks[0]) == "registration_t_out" ) {
				$aor_record->reg_t_out = trim($toks[1]);
			} else if ( trim($toks[0]) == "registrar" ) {
				$aor_record->registrar = trim($toks[1]);
			} else if ( trim($toks[0]) == "binding" ) {
				$aor_record->binding = trim($toks[1]);
				$AORRecords[$i++] = $aor_record;
				$aor_record = NULL;
			}
		}
		fclose($file);
		$res = $shell_obj->Execute("rm -f /tmp/res.txt", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		return $AORRecords;
	}

	function delectUsedAorRecord($AORRecords_no_aorkey,$aor_key) {
		for ( $k = 0; $k < count($AORRecords_no_aorkey); $k++ ) {
			$aor_record = $AORRecords_no_aorkey[$k];
			if ( strncmp( $aor_key, $aor_record->binding, strlen($aor_key)) == 0) {
				//4 is binding
				unset($AORRecords_no_aorkey[$k]);
				$reset_index_aor = array_values($AORRecords_no_aorkey);
				return $reset_index_aor;
			}
		}
		return $AORRecords_no_aorkey;
	}

	function getAorRecord($AORRecords, $aor_key) {
		for ( $k = 0; $k < count($AORRecords); $k++ ) {
			$aor_record = $AORRecords[$k];
			if ( strncmp( $aor_key, $aor_record->binding, strlen($aor_key)) == 0) {
				//4 is binding
				return $AORRecords[$k];
			}
		}
		return false;
	}

	function getLocationTrunk($loc_trunk_key) {
		$location_trunks = SBCLogMgr::queryTrunkSatus();
		for ( $k = 0; $k < count($location_trunks); $k++ ) {
			if ( $location_trunks[$k][14] == $loc_trunk_key ) {
				return $location_trunks[$k];
			}
		}
		return array();
	}

	function getTrunkStatus() {
		$results = array();
		$AORRecords = parseAorRecords();
		$AORRecords_no_aorkey = array();
		$AORRecords_no_aorkey = parseAorRecords();
		$i = 0;
		$trunk_config = TrunkConfigModel::getInstance();
		$trunk_configRules = $trunk_config->getTrunkConfigRules();
		$sip_config =  SIPProfileConfigModel::getInstance();
		if ( $trunk_configRules ) {
			while($row = $trunk_configRules->fetchArray(SQLITE3_ASSOC)) {
				$internal_sip_raw = $sip_config->getSIPProfile($row[TrunkConfigModel::$INTERNAL_SIP_PROFILE]);
				$interanl_sip = $internal_sip_raw[NAME];
				$lanip = $sip_config->getSipProfileInterfaceAddress($interanl_sip);
				$lanport = $sip_config->getSipProfileInterfacePort($interanl_sip);
				$lan_proto = $sip_config->getSipProfileInterfaceProto($interanl_sip);
				$loc_trunk_key = $lan_proto.":".$lanip.":".$lanport;
				$external_sip_raw = $sip_config->getSIPProfile($row[TrunkConfigModel::$EXTERNAL_SIP_PROFILE]);
				$external_sip = $external_sip_raw[NAME];
				$wanip = $sip_config->getSipProfileInterfaceAddress($external_sip);
				$wanport = $sip_config->getSipProfileInterfacePort($external_sip);
				$aor_key = "sip:".trim($row[TrunkConfigModel::$USERNAME])."@".$wanip.":".$wanport;
				$aor_record = getAorRecord($AORRecords, $aor_key);
				$loc_trunk = getLocationTrunk($loc_trunk_key);
				$AORRecords_no_aorkey = delectUsedAorRecord($AORRecords_no_aorkey,$aor_key);
				$result= array($aor_record->state,$loc_trunk[1],$loc_trunk[2],$aor_record->registrar,$aor_record->key,$aor_record->last_reg_sent,$aor_record->reg_t_out,$loc_trunk[13],$loc_trunk[3],$loc_trunk[4],$loc_trunk[6],$loc_trunk[14]);
				$results[$i] = $result;
				$i++;
			}
			$total_unmatched = count($AORRecords_no_aorkey);
			if($total_unmatched != 0 ) {
				$loc_trunk = array();
				for($l =0; $l < $total_unmatched ; $l++) {
					$aor_record = $AORRecords_no_aorkey[$l];
					$results[]= array($aor_record->state,$loc_trunk[1],$loc_trunk[2],$aor_record->registrar,$aor_record->key,$aor_record->last_reg_sent,$aor_record->reg_t_out,$loc_trunk[13],$loc_trunk[3],$loc_trunk[4],$loc_trunk[6],$loc_trunk[14]);
				}
			}
		}
		return $results;
	}
?>
