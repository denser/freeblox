<?php
	include_once("Control/session_secure.php");
	include_once("shell_command.php");
	include_once("Model/sys_calls.php");

	class Config_GENERAL_SETTINGS {
		private $T_NAME = array(49=>"GENERAL_SETTINGS");
		private $DBH;
		private $Retun_val = false;

		private function SSH_Provision($row) {
			$port=$row['SSH_PORT'];
			$shell_obj = ShellCommand::getInstance();
			if ($row['ENABLE_SSH']==0) {
				if(SysCalls::getSysArch() == "armv7l") {
					$cmd1="/etc/init.d/ssh stop; update-rc.d -f ssh remove";
				} else {
					$cmd1="/etc/init.d/sshd stop;killall sshd;chkconfig sshd off";
				}
				$cmd2="iptables -R INPUT_ACCESS_RULES 1 -p tcp --dport " + $port + " -j DROP";
				$res1 = $shell_obj->Execute($cmd1, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
				$res2 = $shell_obj->Execute($cmd2, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
			} else {
				$cmd1="sed -i.bak 's/port_val/ " + $port + " /g' /etc/ssh/sshd_config";
				if(SysCalls::getSysArch() == "armv7l") {
					$cmd2="update-rc.d ssh defaults;/etc/init.d/ssh start";
				} else {
					$cmd2="chkconfig --level 3 sshd;killall sshd;/etc/init.d/sshd start";
				}
				//$cmd3="iptables -R INPUT_ACCESS_RULES 1 -p tcp --dport " + $port + " -j ACCEPT";
				$res1 = $shell_obj->Execute($cmd1, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;			
				$res2 = $shell_obj->Execute($cmd2, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;			
				//$res3 = $shell_obj->Execute($cmd3, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE) ;			
			}
		}

		private function Host_Provision($row) {
			$host_cmd = "/usr/bin/updatehost ".$row['HOST_NAME'];
			$shell_obj = ShellCommand::getInstance();
			$res1 = $shell_obj->Execute($host_cmd, ShellCommand::FLAG_DONT_ALLOWCMD|ShellCommand::FLAG_DONT_VALIDATE);
		}

		private function Update_Config() {
			$result = $this->DBH -> query("SELECT * FROM GENERAL_SETTINGS");
			$row = $result -> fetchArray(SQLITE3_ASSOC);
			$this->Host_Provision($row);
			$this->SSH_Provision($row);
			$this -> Retun_val = true;
		}

		public function config_update($db, $t_name) {
			//if (array_key_exists($t_name,$this->T_NAME)){
				$this -> DBH = $db;
				$this -> Update_Config();
			//}
			return $this -> Retun_val;
		}
	}

?>
