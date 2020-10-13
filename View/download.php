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
	include_once("Model/shell_command.php");

	$shell_obj = ShellCommand::getInstance();
	$shell_status = $shell_obj->Execute("date +'%m%d%Y-%s'", 
			ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	$serial = trim(SysCalls::getSerialno());
	if($serial != "") { 
		$serialno = "_".$serial;
	}
	$ALERT="alert";
	$DIAGNOSTIC="diagnostics";
	$BACK_UP_CONFIG="db";
	$ROOT_CA = "rootca";
	$PACKET_CAP = "packet_cap";
	$CDR_REPORTS = "cdr";
	$filename = $_GET['id'];
	$clientCertList = SysCalls::getCertList();
	$clientCertCount =  count($clientCertList);
	for ($i=0; $i < $clientCertCount; $i++) {
		$certArray = explode(',',$clientCertList[$i]);
		$certName = $certArray[0];
		if ( strcmp($filename,$certName)==0) {
			$file = "/usr/local/etc/opensips/tls/user/${certName}".".".pem;
			$file_name = ${certName}.".".pem;
		}
	}
	if($filename == $ALERT) {
		header('Content-type: text/csv');
		$file_name="alerts.csv";
		$file="/tmp/alerts";
	} elseif($filename == $BACK_UP_CONFIG) {
		header('Content-type: application/octet-stream');
		$bkup_file="/tmp/bloxcfg.bin";
		$file_name="bloxcfg".$serialno."_".$currentTime.".bin";
		$file="/tmp/bloxcfg".$serialno."_".$currentTime.".bin";
		$cmd = "rm /tmp/bloxcfg_*.bin; backup_config && mv $bkup_file $file";
		$shell_status = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	}
	if($filename == $ROOT_CA ) {
		header('Content-type: application/octet-stream');
		$file_name="cacert.pem";
		$file="/usr/local/etc/opensips/tls/rootCA/cacert.pem";
	}
	if($filename == $PACKET_CAP ) {
		header('Content-type: application/octet-stream');
		$file_name="packet-capture".$serialno."_".$currentTime.".pcap";
		$file="/var/tmp/packet-capture".$serialno."_".$currentTime.".pcap";
		$pcap_file="/var/tmp/packet-capture.pcap" ;
		$cmd = "mv $pcap_file $file";
		$shell_status = $shell_obj->Execute($cmd, 
			ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	}
	if($filename == $CDR_REPORTS ) {
		header('Content-type: text/csv');
		#$file_name="cdr.csv";
		#$file="/var/tmp/cdr.csv";
		$file_name="cdr".$serialno."_".$currentTime.".csv";
		$file="/var/tmp/cdr".$serialno."_".$currentTime.".csv";
		$cdr_file="/var/tmp/cdr.csv" ;
		$cmd = "mv $cdr_file $file";
		$shell_status = $shell_obj->Execute($cmd, 
			ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	}
	if ( $filename == $DIAGNOSTIC ) {
		header('Content-type: application/octet-stream');
		$file_name="diagnostics";
		$file="/tmp/diag.out";
	}
	header('Content-Disposition: attachment; filename='.$file_name);
	if (file_exists($file)) {
		readfile($file);
		if($file_name == "bloxcfg.bin")
		unlink($file);
	} else {
		print "file not found";
	}
?>
