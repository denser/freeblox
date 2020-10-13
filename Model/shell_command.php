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
	class ShellCommand {
		const MAX_SHELL_CMDOUT = 8192 ;
		const FLAG_DONT_VALIDATE = 1;
		const FLAG_DONT_ALLOWCMD = 2;

		private static $last_out = null;
		private static $last_cmd = null;
		private static $last_err = null;
		private static $instance = null;
		private static $log_cmd  = false;

		public static function getLastOutput() { return ShellCommand::$last_out; }
		public static function getLastCommand() { return ShellCommand::$last_cmd; }
		public static function getLastError() { return ShellCommand::$last_err; }


		private static function is_valid_cmd($cmd) {
			if(preg_match("/[\$\;>\|]/",$cmd,$out) != 0) {
				ShellCommand::$last_err['type'] = 1;
				ShellCommand::$last_err['message'] = "'$out[0]' in '$cmd' not valid to shell";
				return false ;
			}
			return true;
		}

		private static function is_allowed_cmd($cmd) {
			$allow_not_cmd =  array("cat", "curl", "wget", "ssh", "reboot", "poweroff");
			$anc_cnt = count($allow_not_cmd);
			for($x = 0; $x < $anc_cnt; $x++) {
				if(preg_match("/\<$allow_not_cmd[$x]\>/",$cmd,$out) != 0) {
					ShellCommand::$last_err['type'] = 1;
					ShellCommand::$last_err['message'] = "'$out[0]' not allowed to shell";
					return false ;
				}
			}
			return true;
		}

		public static function IsValidCommand($cmd) {
			return ShellCommand::is_valid_cmd($cmd);
		}

		public static function IsAllowedCommand($cmd) {
			return ShellCommand::is_allowed_cmd($cmd);
		}

		public static function Execute($cmd,$flag = 0,$nowait=0,$nonblock=false) {
			if(!($flag&ShellCommand::FLAG_DONT_VALIDATE) && (!ShellCommand::is_valid_cmd($cmd))) {
				return -1;
			}
			if(!($flag&ShellCommand::FLAG_DONT_ALLOWCMD) && (!ShellCommand::is_allowed_cmd($cmd))) {
				return -1;
			}
			if(ShellCommand::$log_cmd) error_log("Executing shell command:$cmd:",0);
			if(!$handle = popen($cmd, "r")) {
				ShellCommand::$last_err['type'] = 1;
				ShellCommand::$last_err['message'] = "unknown error" ;
				return ShellCommand::$last_err['type'];
			}
			ShellCommand::$last_err = error_get_last();
			if(ShellCommand::$last_err['type'] === E_ERROR) {
				return ShellCommand::$last_err['type'];
			}

			if($nowait!=0) {
				$last_ret = sprintf("%u",pclose($handle));
				return $last_ret;
			}
			if($nonblock) stream_set_blocking($handle, false);

			ShellCommand::$last_out = "";
			$readlen = ShellCommand::MAX_SHELL_CMDOUT ; 
			while((!feof($handle)) && ($readlen>0)) {
				ShellCommand::$last_out .= fread($handle,$readlen);
				$readlen -= strlen(ShellCommand::$last_out) ;
			}
			$last_ret = sprintf("%u",pclose($handle));

			if($last_ret != 0) {
				ShellCommand::$last_err['type'] = $last_ret;
				ShellCommand::$last_err['message'] = posix_strerror($last_ret);
			}
			return $last_ret ;
		}

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public function __construct() {
		}

	///// Calling function end ////////////////
	}

	#$x = ShellCommand::getInstance();
	#echo $x->Execute("ls 123", ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
	#echo $x->getLastOutput() ;
	#print_r ($x->getLastError()) ;
?>
