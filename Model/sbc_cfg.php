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

	class SBCConfig {
		private $globalSettings;
		private $extensionProfiles;
		private $trunkConfig;

		public function __construct() {
			$this->globalSettings = GlobalSettingsConfigModel::getInstance();
			$this->extensionProfiles = ExtensionProfilesConfigModel::getInstance();
			$this->trunkConfig = TrunkConfigModel::getInstance();
		}

// Sip Profile configuration begin
		public function getSipProfile_Count() {
			return $this->globalSettings->getGlobalSettingsProfilesCount();
		}

		public function getSipProfile($i) {
			return $this->globalSettings->getGlobalSettingsRule($index);
		}

		public function setSipProfile($profile, $interface, $signal_port, $tls, $tls_port, 
			$nat, $nat_type, $nat_ip, $comments ) {
				return $this->globalSettings->editGlobalSettingsRule($profile, 
					$interface, $signal_port, $tls, $tls_port, $nat, $nat_type, 
					$nat_ip, $comments );
		}

		public function addSipProfile( $profile, $interface, $signal_port, $tls, $tls_port, 
			$nat, $nat_type, $nat_ip, $comments) {
				return $this->globalSettings->addGlobalSettingsRule( $profile, 
					$interface, $signal_port, $tls, $tls_port, $nat, $nat_type, 
					$nat_ip, $comments );
		}

		public function deleteSipProfile($profile) {
			return $this->globalSettings->deleteGlobalSettingsRule($profile);
		}

		public function getTrunk_Count() {
			return $this->trunkConfig->getTrunkConfigCount();
		}

		public function getTrunk($i) {
			return $this->trunkConfig->getTrunkConfigRule($name);
		}

		public function setTrunk( $name, $desc, $sbc_trunk_type,$trunk_ip, $trunk_port, 
			$ob_sbc_prof, $ob_sbc_transport, $ob_media_type, $ob_call_id, $ob_user, 
			$ob_passwd, $prefix, $prefix_count, $og_calls, $ib_sbc_prof, $ib_media_type,
			$use_ob_cred, $ib_user, $ib_passwd) {
				return $this->trunkConfig->editTrunkConfigRule( $name, $desc, 
					$sbc_trunk_type, $trunk_ip, $trunk_port,$ob_sbc_prof, 
					$ob_sbc_transport, $ob_media_type, $ob_call_id, $ob_user, 
					$ob_passwd, $prefix, $prefix_count, $og_calls, $ib_sbc_prof, 
					$ib_media_type, $use_ob_cred, $ib_user, $ib_passwd );
		}

		public function addTrunk($name, $desc, $sbc_trunk_type, $trunk_ip, $trunk_port, 
			$ob_sbc_prof, $ob_sbc_transport, $ob_media_type, $ob_call_id, $ob_user, 
			$ob_passwd, $prefix, $prefix_count, $og_calls, $ib_sbc_prof, $ib_media_type, 
			$use_ob_cred, $ib_user, $ib_passwd){
				return $this->trunkConfig->addTrunkConfigRule( $name, $desc, 
					$sbc_trunk_type, $trunk_ip, $trunk_port,$ob_sbc_prof, 
					$ob_sbc_transport, $ob_media_type, $ob_call_id, $ob_user, 
					$ob_passwd, $prefix, $prefix_count, $og_calls, $ib_sbc_prof, 
					$ib_media_type,$use_ob_cred, $ib_user, $ib_passwd);
		}

		public function deleteTrunk($name) {
			return $this->trunkConfig->deletetrunkConfigRule($name);
		}

		public function getConfiguredProfileList($type) {
			$plist=array();
			$p=0;
			$sip_list_count=$this->getSipProfile_Count();
			for ($i=0; $i < $sip_list_count; $i++) {
				$sip_array=$this->getSipProfile($i);
				if ($type == $sip_array[1]) {
					$plist[$p]=array($sip_array[0],$sip_array[3]);
					$p++;
				}
			}
			return $plist;
		}

		public function getRoaminguser_Count(){
			return $this->extensionProfiles->getExtensionProfilesCount();
		}

		public function getRoaminguser($i){
			return $this->extensionProfiles->getExtensionProfilesRule($extension);
		}

		public function setRoaminguser( $extension, $redirect_proxy_ip, $redirect_proxy_port, 
			$user, $password, $registration_int, $inbound_sbc_prof, $inbound_sbc_transport, 
			$inbound_media_type, $outbound_sbc_prof, $outbound_media_type ) {
				return $this->extensionProfiles->editExtensionProfilesRule($extension, 
					$redirect_proxy_ip, $redirect_proxy_port, $user, $password, 
					$registration_int, $inbound_sbc_prof, $inbound_sbc_transport, 
					$inbound_media_type, $outbound_sbc_prof, $outbound_media_type);
		}

		public function addRoaminguser( $extension, $redirect_proxy_ip, $redirect_proxy_port, 
			$user, $password, $registration_int, $inbound_sbc_prof, $inbound_sbc_transport, 
			$inbound_media_type, $outbound_sbc_prof, $outbound_media_type ) {
				return $this->extensionProfiles->addExtensionProfilesRule( $extension, 
					$redirect_proxy_ip, $redirect_proxy_port, $user, $password, 
					$registration_int, $inbound_sbc_prof, $inbound_sbc_transport, 
					$inbound_media_type, $outbound_sbc_prof, $outbound_media_type );
		}

		public function deleteRoaminguser($i){
			return $this->extensionProfiles->deleteExtensionProfilesRule($name);
		}
// RoamingUser Configuration end

// for validation function begin
		public function Profile_InTrunk_Romming($P_name){
			$Trunk_count = $this->getTrunk_Count();
			for ($i=0; $i < $Trunk_count; $i++){
				$Trunk_array = $this->getTrunk($i);
				if ($Trunk_array[2] == $P_name || $Trunk_array[13] == $P_name ){
					return 1;
				}
			}

			$Roming_Count = $this->getRoaminguser_Count();
			for ($i=0; $i < $Roming_Count; $i++){
				$Roming_array =$this->getRoaminguser($i);
				if ($Roming_array[4] == $P_name || $Roming_array[9] == $P_name ){
					return 1;
				}
			}
			return -1;
		}

		public function getProfilePortList(){
			$plist=array();
			$p=0;
			$sip_list_count=$this->getSipProfile_Count();
			for ($i=0; $i < $sip_list_count; $i++){
				$sip_array=$this->getSipProfile($i);
				$plist[$p]=$sip_array[2];
				$p++;
				if ($sip_array[3]=="true"){
					$plist[$p]=$sip_array[4];
					$p++;
				}
			}
			return $plist;
		}

		public function getConfigNameList(){
			$plist=array();
			$p=0;
			$sip_list_count=$this->getSipProfile_Count();
			for ($i=0; $i < $sip_list_count; $i++){
				$sip_array=$this->getSipProfile($i);
				$plist[$p]=$sip_array[0];
				$p++;
			}

			$Trunk_count = $this->getTrunk_Count();
			for ($i=0; $i < $Trunk_count; $i++){
				$Trunk_array = $this->getTrunk($i);
				$plist[$p]=$Trunk_array[0];
				$p++;
			}

			$Roming_Count = $this->getRoaminguser_Count();
			for ($i=0; $i < $Roming_Count; $i++){
				$Roming_array = $this->getRoaminguser($i);
				$plist[$p]=$Roming_array[0];
				$p++;
			}
			return $plist;
		}
// for validation function end
	}
?>
