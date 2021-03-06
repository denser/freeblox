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

	if(!isset($_SESSION)) {
		session_start();
	}

	if($_SESSION['user'] == "" || (!isset($_SESSION))) {
		header('location:login.php');
	}
	else if(isset($_SESSION)) {
		if ( isset($_SESSION['sessTimeout']) && $_SESSION['sessTimeout'] != 0 ) {
			$inactive = $_SESSION['sessTimeout'];
		} else {
			$inactive = 900;
		}

		if (!isset($_SESSION['timeout_idle'])) {
			$_SESSION['timeout_idle'] = time() + $inactive;
		} else {
			if ($_SESSION['timeout_idle'] < time()) {
				?>
				<script type="text/javascript">
					document.location.href='logout.php';
				</script>
			<?php
			} else {
				$_SESSION['timeout_idle'] = time() + $inactive;
			}
		}
	}
?>
