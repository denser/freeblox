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

	$sip_config = SIPProfileConfigModel::getInstance();
	$device_config = DeviceSettingsModel::getInstance();
	$media_config = MediaConfigModel::getInstance();
	$getTransInt = SysCalls::getTranscodingInterface();
	$devicelist_config = DevicelistSettingsModel::getInstance();
/*
// This is for processing ajax request to get data from DB
	if ( isset($_POST) && $_POST['action'] == "domainlist" ) {
		$sip_profile =  $_POST['sip_profile'];
		$sip_config = SIPProfileConfigModel::getInstance();
		$row = $sip_config->getSIPProfile($sip_profile);
		$domain = $row[SIPProfileConfigModel::$SIPDOMAINS];
		print $domain;
	}
*/
?>
