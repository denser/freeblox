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
	include_once('Model/db_mgr.php');

	$dbh = DB_Handle::Connect();
	$type = $dbh->getActionListType();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<script type="text/javascript">
			var $sub = $.noConflict(true);
		</script>
		<script type="text/javascript">
			function closeDialog() {
				parent.closeAddEditDialog();
			}
		</script>
	</head>
	<body class="popup" style="color: #444">
		<div class="mainborder">
			<div class="pgtitle">Updates List<input type="button" class="btn-close" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 490px">
				<div style="margin: 20px; margin-top: 0px; font-size: 13px">
					<?php
					while($row_type = $type->fetchArray(SQLITE3_ASSOC)) {
						$i = 0;
						$result = $dbh->getActionListData($row_type["KEYTYPE"]);
					?>
						<table style="width: 100%; background-color: #ccc; border: 2px solid #ccc" cellpadding="1" cellspacing="1">
							<th style="background-color: #ccc; font-size: 14px; height: 24px; color: #666"><?php echo $row_type["KEYTYPE"] ?></th>
					<?php
						while($row = $result->fetchArray(SQLITE3_ASSOC)) {
							$i++;
					?>
							<tr>
								<td style="background-color: #ffffff; color: #444; padding-left: 20px; padding-top: 3px; padding-bottom: 3px"><?php echo $i.". ".$row["ACTION"]." ".$row["KEYNAME"] ?></td>
							</tr>
					<?php
						}
					?>
					</table>
					<br />
					<?php
					}
					?>
				</div>
			</div>
			<div class="popup-footer">
				<input type="button" name="" value="" class="btn-cancel" onclick="closeDialog()" onfocus="if(this.blur)this.blur()" />
			</div>
		</div>
	</body>
</html>
