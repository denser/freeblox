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

	interface SBC_CONSTANTS {
		const IP_TYPE_ANY = 1;
		const IP_TYPE_HOST = 2;
		const IP_TYPE_NETWORK = 3;
		const IP_TYPE_RANGE = 4;
		const MAC_TYPE_HOST = 5;

		const IP_TYPE_ANY_STR = "ANY";
		const IP_TYPE_HOST_STR = "IP_HOST";
		const IP_TYPE_NETWORK_STR = "IP_NETWORK";
		const IP_TYPE_RANGE_STR = "IP_RANGE";
		const MAC_TYPE_HOST_STR = "MAC_ADDR";

		const GEO_IP_ALLOW_STR = "1";
		const GEO_IP_BLOCK_STR = "";
	}

	class SBC_FWCONFIG_OPTIONS {
		static $IP_TYPE_OPTIONS = array("ANY","IP_HOST","IP_NETWORK","IP_RANGE", "MAC_ADDR");
		static $PROTOCOL_OPTIONS = array("any","tcp","udp","icmp");
	}
?>
