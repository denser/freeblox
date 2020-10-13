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

/*
Action : INTER_PROV/INIT_NETWORK/RESET_NETWORK
//PROV_SUBSCRIBER//
//ALL_CONF//
//GEN_SETTING//
//SHM_PROV//
//SHM_SQL//
*/
	include_once("Control/session_secure.php");
	include_once('db_mgr.php');
	//include_once('Config_INTERFACE_LIST.php');
	include_once('Config_NETWORK_DEVICE.php');
	include_once('Config_GENERAL_SETTINGS.php');
	include_once('Config_PROV_SUBSCRIBER.php');
	include_once('Config_PROV_SHM_RULES.php');

	$c = count($argv);
	$Action="";
	if ($c >1 ) {
		$Action = $argv[1];
	}

	switch ($Action) {
		case "PROV_SUBSCRIBER":
				$dbh = DB_Handle::Connect();
				$c_Obj = new Config_PROV_SUBSCRIBER();
				$table_id=50;
				$c_Obj-> config_update($dbh,$table_id);
				break;

		case "INTER_PROV":
				//$dbh = DB_Handle::Connect();
				//$c_Obj = new Config_INTERFACE_LIST();
				//$table_id=48;
				//$c_Obj-> config_update($dbh,$table_id);
				break;

		case "GEN_SETTING":
				//$dbh = DB_Handle::Connect();
				//$c_Obj = new Config_GENERAL_SETTINGS();
				//$table_id=49;
				//$c_Obj-> config_update($dbh,$table_id);
				break;

		case "INIT_NETWORK":
				$c_Obj = Config_NETWORK_DEVICE::Connect();
				$c_Obj->init_NetWork();
				break;

		case "RESET_NETWORK":
				$c_Obj = Config_NETWORK_DEVICE::Connect();
				$c_Obj->reset_Network();
				break;

		case "SHM_PROV":
				$dbh = DB_Handle::Connect();
				$c_Obj = new Config_PROV_SHM_RULES();
				$table_id=50;
				$c_Obj->config_update($dbh, $table_id);
				break;

		case "SHM_SQL":
				$dbh = DB_Handle::Connect();
				$c_Obj = new Config_PROV_SHM_RULES();
				$table_id=50;
				$c_Obj->gen_SQL($dbh, $table_id);
				break;

		case "ALL_CONF":
				// Interface 
				//$dbh = DB_Handle::Connect();
				//$c_Obj = new Config_INTERFACE_LIST();
				//$table_id=48;
				//$c_Obj-> config_update($dbh,$table_id);
				// General setting 
				//$c_Obj = new Config_GENERAL_SETTINGS();
				//$table_id=49;
				//$c_Obj-> config_update($dbh,$table_id);
				break;
	}
	exit(0);
?>
