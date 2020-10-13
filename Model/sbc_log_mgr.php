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
	include_once('mysql_db_mgr.php');
	include_once("shell_command.php");
	include_once ('sys_calls.php');
	include_once("Control/web_users.php");

	class SBCLogMgr {
		private static $instance = null;
		private static $CDR_REPORTS_FILE="/tmp/cdr_reports.txt";
		private static $CDR_REPORTS_CSV_FILE="/var/tmp/cdr.csv";
		private static $TRUNK_STATUS_FILE="/tmp/trunk_status.txt";
		private static $ROAMING_STATUS_FILE="/tmp/roaming_status.txt";
		private static $ACTIVE_CALLS_STATUS_FILE="/tmp/active_calls_status.txt";
		private static $NO_OF_ENTRY=500;

		public static function getInstance() {
			if (!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}

		public static function getStatus($status_file,$no_entry) {
			$line = array();
			if ( file_exists ( $status_file ) == FALSE ) {
				return $line;
			}
			$line = SysCalls::read_lastlines($status_file,$no_entry);
			$temp = explode("\n",$line);
			$count = count($temp);
			$status = array();
			for($i=0; $i < $count; $i++) {
				if ( strlen(trim($temp[$i])) == 0 )
					continue;
				$status[$i] = explode("|",$temp[$i]);
			}
			return $status;
		}

		public static function getMysqlShellCommand($root="user") {
			if($root=="root") {
				$exe = 'mysql -u ' . "'" . MysqlDBMgr::$ROOTUSERNAME. "'" . ' --password=' . "'".MysqlDBMgr::$ROOTPASSWORD."'" . " " .MysqlDBMgr::$DBNAME . " ";
			} else {
				$exe = 'mysql -u ' . "'" . MysqlDBMgr::$USERNAME. "'" . ' --password=' . "'".MysqlDBMgr::$PASSWORD."'" . " " .MysqlDBMgr::$DBNAME . " ";
			}
			return $exe ;
		}

		public function queryTrunkSatus() {
			$shell_obj = ShellCommand::getInstance();
			$exe = self::getMysqlShellCommand();
			$exe .= '-e "select * from locationtrunk" |  sed -n "1!p" | sed -e "s/\t/|/g" > /tmp/trunk_status.txt';
			$res = $shell_obj->Execute($exe, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			return self::getStatus(self::$TRUNK_STATUS_FILE,self::$NO_OF_ENTRY);
		}

		public function  queryRoamingUsers() {
			$shell_obj = ShellCommand::getInstance();
			$exe = self::getMysqlShellCommand();
			$exe .= '-e "select * from locationpbx" | sed -n "1!p" | sed -e "s/\t/|/g"  > /tmp/roaming_status.txt';
			$res = $shell_obj->Execute($exe, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			return self::getStatus(self::$ROAMING_STATUS_FILE,self::$NO_OF_ENTRY);
		}

		public function  queryCDReports() {
			$shell_obj = ShellCommand::getInstance();
			$exe = self::getMysqlShellCommand();
			$exe .= '-e "select * from acc where sip_code != 401 && sip_code != 183" | sed -n "1!p" | sed -e "s/\t/|/g"  > ' . self::$CDR_REPORTS_FILE ;
			$res = $shell_obj->Execute($exe, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			return self::getStatus(self::$CDR_REPORTS_FILE,self::$NO_OF_ENTRY);
		}

		public function deleteCDRReports($records,$pwd) {
			$obj = WebUsers::getConfig();
			if($pwd != "") {
				$dbpwd = $obj -> getUserPassword("admin");
				$post_md5_pwd = md5($pwd);
				if($dbpwd == $post_md5_pwd) {
					$shell_obj = ShellCommand::getInstance();
					$exe = self::getMysqlShellCommand();
					$exe .= '-e "delete from acc where sip_code != 401 && sip_code != 183 ORDER BY time ASC LIMIT '.$records.'";';
					$res = $shell_obj->Execute($exe, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
					return $res;
				} else {
					return "2";
				}
			}
		}

		public function  downloadQueryCDReports() {
			$shell_obj = ShellCommand::getInstance();
			$get_mysql_file_dir= self::getMysqlShellCommand();
			$get_mysql_file_dir .= "-e 'SHOW VARIABLES LIKE \"secure_file_priv\"' | tail -1 | awk '{print $2}'" ;
			$shell_obj->Execute($get_mysql_file_dir, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			$CDR_REPORTS_CSV_FILE=trim($shell_obj->getLastOutput());
			if(strlen($CDR_REPORTS_CSV_FILE)) {
				$CDR_REPORTS_CSV_FILE.="cdr.csv";
			} else {
				$CDR_REPORTS_CSV_FILE="/var/tmp/cdr.csv";
			}
			if ( file_exists($CDR_REPORTS_CSV_FILE) == TRUE ) {
				$rmfile = $shell_obj->Execute("rm -f " . $CDR_REPORTS_CSV_FILE, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			}
			$exe = self::getMysqlShellCommand("root");
			#Below Query should be executed as only root user, it requires access to write into file 
			$exe .= '-e "SELECT * from ( SELECT "\'"ID"\'","\'"TIME"\'","\'"METHOD"\'","\'"SOURCE"\'","\'"CHANNEL"\'","\'"DESTINATION"\'","\'"DEST_CHANNEL"\'","\'"SIP_CODE"\'","\'"SIP_REASON"\'","\'"DURATION"\'","\'"SETUP_TIME"\'"  UNION ALL ( SELECT id,time,method,src,channel,dst,dstchannel,sip_code,sip_reason,duration,setuptime from acc where sip_code != 401 && sip_code != 183)) result_set INTO OUTFILE "\'"'.$CDR_REPORTS_CSV_FILE.'"\'" FIELDS TERMINATED BY "\'","\'" ENCLOSED BY "\'"\""\'" LINES TERMINATED BY "\'"\\n"\'";"';
			$res = $shell_obj->Execute($exe, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			$cmd = "mv $CDR_REPORTS_CSV_FILE " . self::$CDR_REPORTS_CSV_FILE;
			$res = $shell_obj->Execute($cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			$rmnul = $shell_obj->Execute("sed -i 's|\\\N||g' ".self::$CDR_REPORTS_CSV_FILE, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
		}

		public static function  queryActiveCalls() {
			$shell_obj = ShellCommand::getInstance();
			$exe = self::getMysqlShellCommand();
			$exe .= '-e "select * from dialog" | sed -n "1!p" | sed -e "s/\t/|/g" > /tmp/active_calls_status.txt';
			$res = $shell_obj->Execute($exe, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			return self::getStatus(self::$ACTIVE_CALLS_STATUS_FILE,self::$NO_OF_ENTRY);
		}

		public function  queryProfileCalls() {
			$shell_obj = ShellCommand::getInstance();
			$exe = self::getMysqlShellCommand();
			$exe .= '-e "select * from sip_profile" | sed -n "1!p"  | sed -e "s/\t/|/g" > /tmp/active_calls_status.txt';
			$res = $shell_obj->Execute($exe, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;
			return self::getStatus(self::$ACTIVE_CALLS_STATUS_FILE,self::$NO_OF_ENTRY);
		}
	}
?>
