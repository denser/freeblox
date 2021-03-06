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

	$cmdline = getenv("_CMDLINE");

	if(isset($cmdline) && strlen($cmdline)>0) {
		//called from local command line
	} else {
		$failed = 1;
		if(!isset($_SESSION)) {
			session_start();
		}
		$session_file = "/var/tmp/session.txt";
		
		if(!file_exists($session_file)) { goto end;}
		
		$sf_stat = stat($session_file) ;
		if(!isset($sf_stat)) { goto end; }
		
		$valid_time = (time() - $sf_stat['mtime']);
		if($_SESSION['sessTimeout'] >= $valid_time){
			//valid session
		} else {
			goto end;
		}
		
		if (isset($_SESSION) && isset($_SESSION['AUTH'])) {
			if ($_SESSION['AUTH'] == "AUTH_USER") {
				// valid session
				$failed = 0;
			} else {
				goto end;
			}
		}
	end:
		if ($failed == 1) {
			echo "<script>parent.window.location = 'logout.php'</script>";
			exit;
		}
		touch($session_file);
	}
?>
