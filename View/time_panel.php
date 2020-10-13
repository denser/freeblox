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
//putenv("TZ=$AgmtTimezoneFile[$time_zone]");
//$currentTime =date ("YmdHis");
	include_once("Control/session_secure.php");
	include_once("Model/shell_command.php");

	$shell_obj = ShellCommand::getInstance();
	$shell_status = $shell_obj->Execute("date +'%Y%m%d%H%M%S'", 
				ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
	if( $shell_status == 0) {
		$currentTime =trim( $shell_obj->getLastOutput());
	}
	//$currentTime = trim(shell_exec("date +'%Y%m%d%H%M%S'"));
?>
	<script language="JavaScript">
		var rTimer;
		var sds;
		var month_str = ["January", "February", "March", "April", "May", "June", "July", 
				"August", "September", "October", "November", "December"];

		function TimeTick() {
			sds = new Date(sds.getTime() + 1000);
			////////  time format////////////////
			var time_str = "";
			var t_type = (sds.getHours() < 12) ? "am" : "pm";
			var hour_f = (sds.getHours() > 12) ? sds.getHours() - 12 : sds.getHours();
			if (hour_f == 0) {
				hour_f = 12;
			}
			var t_hour = (hour_f < 10) ? "0" + hour_f : hour_f;
			var t_min = (sds.getMinutes() < 10) ? "0" + sds.getMinutes() : sds.getMinutes();
			var t_sec = (sds.getSeconds() < 10) ? "0" + sds.getSeconds() : sds.getSeconds();
			time_str = t_hour + ":" + t_min + ":" + t_sec + " " + t_type;
			////////  Date format////////////////
			var date_str = "";
			var p_year = sds.getFullYear().toString();
			var p_day = (sds.getDate() < 10) ? "0" + sds.getDate() : sds.getDate();
			date_str = p_day + "-" + month_str[sds.getMonth()] + "-" + p_year.substr(2, 2);
			var identity = document.getElementById('table_time');
			identity.innerHTML = date_str + "&nbsp;&nbsp;" + time_str;
			if (rTimer) {
				clearTimeout(rTimer);
			}
			rTimer = setTimeout('TimeTick()', 1000);
		}

		function Time_init(datestr) {
			t_y = datestr.substr(0, 4);
			t_mon = datestr.substr(4, 2);
			t_day = datestr.substr(6, 2);
			t_hr = datestr.substr(8, 2);
			t_min = datestr.substr(10, 2);
			t_sec = datestr.substr(12, 4);
			document.getElementById("system_time").innerHTML = t_day + "-" + t_mon + "-" + t_y + "-" + t_hr + "-" + t_min;
			sds = new Date(t_y, t_mon - 1, t_day, t_hr, t_min, t_sec);
			TimeTick();
		}
	</script>
	<script>Time_init('<?php echo "$currentTime"; ?>');</script>
