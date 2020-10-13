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
	include_once("Control/login_auth.php");
	include_once("Control/date_config.php");

	$obj = DateTimeConfig::getConfig();
	$update_count = $obj -> getActionListCount();
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
			var submit = 0;
		</script>
		<script type="text/javascript">
			function closeDialog() {
				parent.closeAddEditDialog();
			}

			function refresh_iframe(val) {
				if( val == 1 ) {
					parent.closeAddEditDialog();
					parent.document.getElementById("container").src = parent.document.getElementById("container").src;
				}
			}

			function initValidation() {
				$sub("#SESSION_TIMEOUT").attr("data-validation-engine","validate[required,custom[integer],min[60],max[3600]]");
				$sub("#websettings").validationEngine('attach', {promptPosition : "bottomRight"});
			}
		</script>
	</head>
	<body class="popup" onload="refresh_iframe('<?php print $res;?>');">
		<div class="mainborder">
			<div class="pgtitle">End User License</div>
			<div class="popup-content" style="height: 430px; width: 640px;">
				<form id="websettings" class="popup-form" action="end-user-licence.php" onsubmit="closeDialog();" target="container" method="POST" autocomplete="off">
					<table class="formtable" cellpadding="2px" cellspacing="2px">
						<tr>
							<td>
<div id="post-166" class="posts clearfix">
<p  style="text-align: center;"><strong>FREEWARE LICENSE<br>
END-USER LICENSE AGREEMENT<br>
For FreeBlox, A Freeware GUI Management Tool for Blox (http://www.blox.org)<br>
By www.allo.com</strong></p>
<p  style="text-align: justify;"><strong>NOTICE TO USER:</strong><br>
Please, read this carefully. By using all or any portion of the Software you accept all the terms and conditions of this Agreement. If you do not agree, do not use this Software.</p>
<p  style="text-align: justify;"><strong>1. DEFINITIONS</strong><br>
When used in this Agreement, the following terms shall have the respective meanings indicated, such meanings to be applicable to both the singular and plural forms of the terms defined:</p>
<p style="text-align: justify;">Licensor means www.allo.com.</p>
<p  style="text-align: justify;">Licensee means You or Your Company, unless otherwise indicated.</p>
<p  style="text-align: justify;">Software means (a) all of the contents of the files, disk(s), CD-ROM(s) or other media with which this Agreement is provided, including but not limited to ((i) registration information, i.e. License key which is unique for a registration name of the Licensee; (ii) related explanatory written materials or files (Documentation); and (iii) Software setup files and code samples (if any); and (b) upgrades, modified versions, updates, additions, and copies of the Software, if any, licensed to you by www.allo.com (collectively, Updates).</p>
<p style="text-align: justify;">Use or Using means to access, install, download, copy or otherwise benefit from using the functionality of the Software in accordance with the Documentation.</p>
<p style="text-align: justify;">System means Windows OS, GNU/Linux or Mac OS X, or any virtual machine.</p>
<p style="text-align: justify;"><strong>2. GENERAL USE&nbsp;</strong><br>
You are granted a non-exclusive License to Use the downloaded Software for any purposes for an unlimited period of time.</p>
<p style="text-align: justify;">The software product under this License is provided free of charge. Even though a license fee is not paid for the use of such software, it does not mean that there are no conditions for using such software.</p>
<p style="text-align: justify;">2.1. The Software may be installed and Used by the Licensee for any legal purpose.</p>
<p style="text-align: justify;">2.2. The Software may be installed and Used by the Licensee on any number of systems.</p>
<p style="text-align: justify;">2.3. The Software can be copied and distributed under the condition that original copyright notice and disclaimer of warranty will stay intact and the Licensee will not charge money or fees for the Software product, except to cover distribution costs.</p>
<p style="text-align: justify;">2.4. The Licensee will not have any proprietary rights in and to the Software. The Licensee acknowledges and agrees that the Licensor retains all copyrights and other proprietary rights in and to the Software.</p>
<p style="text-align: justify;">2.5 Use within the scope of this License is free of charge and no royalty or licensing fees shall be paid by the Licensee.</p>
<p style="text-align: justify;"><strong>3. INTELLECTUAL PROPERTY RIGHTS</strong><br>
3.1 This License does not transmit any intellectual rights on the Software. The Software and any copies that the Licensee is authorized by the Licensor to make are the intellectual property of and are owned by the Licensor.</p>
<p style="text-align: justify;">3.2 The Software is protected by copyright, including without limitation by Copyright Law and international treaty provisions.</p>
<p style="text-align: justify;">3.3 Any copies that the Licensee is permitted to make pursuant to this Agreement must contain the same copyright and other proprietary notices that appear on or in the Software.</p>
<p style="text-align: justify;">3.4 The structure, organization and code of the Software are the valuable trade secrets and confidential information of the Licensor. The Licensee agrees not to decompile, disassemble or otherwise attempt to discover the source code of the Software.</p>
<p style="text-align: justify;">3.5 Any attempts to reverse-engineer, copy, clone, modify or alter in any way the installer program without the Licensors specific approval are strictly prohibited. The Licensee is not authorized to use any plug-in or enhancement that permits to save modifications to a file with software licensed and distributed by the Licensor.</p>
<p style="text-align: justify;">3.6 Trademarks shall be used in accordance with accepted trademark practice, including identification of trademarks owners names. Trademarks can only be used to identify printed output produced by the Software and such use of any trademark does not give the Licensee any rights of ownership in that trademark.</p>
<p style="text-align: justify;"><strong>4. WARRANTY</strong><br>
4.1 The Licensor warrants that:</p>
<p style="text-align: justify;">4.1.1 The Licensor owns the Software and documentation and/or is in possession of valid and existing licenses that support the terms of this Agreement;</p>
<p style="text-align: justify;">4.1.2 the Software conforms to specifications and functionality as specified in Documentation;</p>
<p style="text-align: justify;">4.1.3 to the best of the Licensors knowledge, the Software does not infringe upon or violate any intellectual property right of any third party;</p>
<p style="text-align: justify;">4.1.4 the Software does not contain any routine, intentionally designed by the Licensor to disable a computer program, or computer instructions that may alter, destroy or inhibit the processing environment.</p>
<p style="text-align: justify;">4.2 Except those warranties specified in section 4.1 above, the Software is being delivered to the Licensee AS IS and the Licensor makes no warranty as to its use or performance.</p>
<p style="text-align: justify;">The Licensor does not and cannot warrant the performance or results the Licensee may obtain by using the Software. The entire risk arising out of use or performance of the Software remains with the Licensee.</p>
<p style="text-align: justify;">The Licensor gives no warranty, express or implied, that (i) the Software will be of satisfactory quality, suitable for any particular purpose or for any particular use under specified conditions, notwithstanding that such purpose, use, or conditions may be known to the Licensor; or (ii) that the Software will operate error free or without interruption or that any errors will be corrected.</p>
<p style="text-align: justify;"><strong>5. LIMITATION OF LIABILITY<br>
</strong>In no event will the Licensor be liable for any damages, claims or costs whatsoever or any consequential, indirect, incidental damages, or any lost profits or lost savings, even if the Licensor has been advised of the possibility of such loss, damages, claims or costs or for any claim by any third party.</p>
<p style="text-align: justify;">In no event will the Licensee be liable to the Licensor on condition that the Licensee complies with all terms and conditions stated in this License.</p>
<p  style="text-align: justify;"><strong>6. NON-WAIVER</strong><br>
If a portion of this agreement is held unenforceable, the remainder shall be valid. It means that if one section of the Agreement is not lawful, the rest of the Agreement is still in force. A partys failure to exercise any right under this Agreement will not constitute a waiver of (a) any other terms or conditions of this Agreement, or (b) a right at any time thereafter to require exact and strict compliance with the terms of this Agreement.</p>
<p  style="text-align: center;">&copy; 2015-2018 www.allo.com. All rights reserved.</p>
</div>
							</td>
						</tr>
					</table>
			</div>
			<div class="popup-footer">
				<input type="button" name="" value="Agree" class="btn-grey-input" onclick="closeDialog();" onfocus="if(this.blur)this.blur();" />
			</form>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		parent.setUpdateValue('<?php echo $update_count ?>');
		if( submit == "2" ) {
			document.location.href = 'logout.php';
		}
	</script>
</html>
