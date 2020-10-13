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

	$res = 0;
	if(isset($_GET['data'])) {
		$page_title = "Server Certificate Details";
		$commonName = $_GET['data'];
		$getCertDetails = SysCalls::getServerCertDetails($commonName);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/validationEngine.jquery.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/tooltip.css" />
		<script src="js/jquery.validationEngine-en.js"></script>
		<script src="js/jquery.validationEngine.js"></script>
		<script src="js/jquery.tooltip.js"></script>
		<script src="js/set_help.js"></script>
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script type="text/javascript">
			function closeDialog() {
				parent.closeAddEditDialog();
			}
		</script>
	</head>
	<body class="popup" onload="refreshIframe('<?php echo $res ?>')">
		<div class="mainborder">
			<div class="pgtitle"><label id="pagetitle"><?php echo $page_title ?></label><input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height:250px;width:750px;">
				<table class="formtable" cellpadding="2px" cellspacing="2px">
					<!--<tr>
						<td>
							<?php $commonName = explode("=",$showServerCertDetails[5]);
								print "Common Name = ".$commonName[1];
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php $cn = explode("=",$showServerCertDetails[1]);
								print "Country Name = ".$cn[1];
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php $state = explode("=",$showServerCertDetails[2]);
								print "State = ".$state[1];
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php $orgName = explode("=",$showServerCertDetails[3]);
								print "Organization Name = ".$orgName[1];
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php $orgUnit = explode("=",$showServerCertDetails[4]);
								print "Organization Unit = ".$orgUnit[1];
							?>
						</td>
					</tr>
						<?php
							$email_exp =  explode("not",$showServerCertDetails[6]);
							$validFromStr = "Valid From ";
							$validToStr = "Valid To ";
							$emailStr = "E-mail ID ";
						?>
					<tr>
						<td>
							<?php $emailId = explode("=",$email_exp[0]);
								print $emailStr."= ".$emailId[1];
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php $validFrom = explode("=",$email_exp[1]);
								print $validFromStr."= ".$validFrom[1];
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php $validTo = explode("=",$email_exp[2]);
								print $validToStr."= ".$validTo[1];
							?>
						</td>
					</tr>-->
					<tr>
						<td style="padding:5px;">
							<pre><?php print_r(trim($getCertDetails)); ?></pre>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
