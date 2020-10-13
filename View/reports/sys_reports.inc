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
class UtmSysReports {
    private static function doCmd($cmd, $args)
    {
    	$sock_file = "/tmp/sysstat.dat";

    	if (!file_exists($sock_file))
        {
           // print "ERROR: -1 - Reports server not seems to be running.";
           return $res;
        }

        $sock = stream_socket_client('unix:///' . $sock_file, $errno, $errstr);

        if ($sock)
		{
			$msg = $cmd.':';
			if ( $args != NULL )
			{
				for ( $i = 0; $i < count($args); $i++ )
				{
					$msg = $msg.$args[$i].',';
				}
			}

            fwrite($sock, $msg . "\n");
            $res = fread($sock, 256);
            fclose($sock);
        } else {
           // print "ERROR: $errno - $errstr";
        }
        return $res;
    }

    public static function execReportsCmd($cmd) {
        switch($cmd) {
            case UtmReportsCmd::EXIT_CMD :
            case UtmReportsCmd::GET_CPU_STAT :
            case UtmReportsCmd::GET_MEM_STAT :
            case UtmReportsCmd::GET_CONNS_STAT :
            case UtmReportsCmd::GET_WAN_USAGE_STAT :
            case UtmReportsCmd::GET_FW_STAT :
            case UtmReportsCmd::GET_IPS_STAT :
            case UtmReportsCmd::GET_SYS_CPU_MODEL :
            case UtmReportsCmd::GET_SYS_MEM_INFO :
            case UtmReportsCmd::GET_SYS_LAN_IF_ADDR :
            case UtmReportsCmd::GET_TIME_ZONE:
            case UtmReportsCmd::GET_IF_LIST :
                return UtmSysReports::doCmd($cmd, NULL);
                break;
            default:
                print "ERROR: -1 - Unknown Command";
                break;
        }
    }

    public static function execReportsCmd2($cmd, $args) {
        switch($cmd) {
            case UtmReportsCmd::GET_IF_ADDR :
            case UtmReportsCmd::GET_IF_STATUS :
                return UtmSysReports::doCmd($cmd, $args);
                break;
            default:
                print "ERROR: -1 - Unknown Command";
                break;
        }
    }
}
?>
