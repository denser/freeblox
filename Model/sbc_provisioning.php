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
	interface DEPLOY_MODULES {
		//T_NAME
		const ALL = 0;
		const HOST_CONFIG = 1;
		const NET_CONFIG = 2;
		const SSH_CONFIG = 3;
		const WEBUI_CONFIG = 4;
		const DEVICE_ACCESS_CONFIG = 5;
		const DEVICE_ACCESS_FILTER_CONFIG = 6;
		const SIP_SECURITY_CONFIG = 7;
		const FIREWALL_CONFIG = 8;
		const DPI_CONFIG = 9;
		const WHITELIST_CONFIG = 10;
		const BLACKLIST_CONFIG = 11;
		const GEOIP_CONFIG = 12;
		const SIGUPDATE_CONFIG = 13;
		const LOGGING_CONFIG = 14;
		const WHITE_LIST_RULES_PRECEDENCE_CONFIG = 15;
		const TIME_SETTINGS_CONFIG = 16;
		const EMAIL_CONFIG = 17;
		const MGMT_VLAN_CONFIG = 18;
		const GLOBAL_FW_CONFIG = 19;
		const DPI_ENABLED_FLAG_CONFIG = 20;
		const IPSEC_VPN_CONFIG = 21;
		const IPSEC_ENABLED_FLAG_CONFIG = 22;
		const SIP_PROFILE_CONFIG = 23;
		const SIP_TRUNK_CONFIG = 24;
		const LCR_CONFIG = 25;
		const ROAMING_USERS_CONFIG = 26;
		const MEDIA_PROFILE_CONFIG = 27;
		const SBC_CONFIG = 28;
		const VLAN_CONFIG = 29;
		const VIP_CONFIG = 30;
		const ROUTES_CONFIG = 31;
		const PORT_FORWARDING_CONFIG = 32;
		const TRANSCODING_CONFIG = 33;
		const SBC_INTERFACES_CONFIG = 34;
		const SBC_NAT_SETTINGS_CONFIG = 35;
		const FAX_PROFILE_CONFIG = 36;
		const PLUGIN_HUMBUG_CONFIG = 37;
		// for device list settings
		const DEVICELIST_PROFILE_CONFIG = 40;
		const GENERAL_SETTING_CONFIG = 41;
		const SUBSCRIBER_SETTINGS_CONFIG = 42;
		const EVENTS_SETTINGS_CONFIG = 43;
		const SIPHEADERS_PROFILE_CONFIG = 44;
		// for Load Balance
		const RESOURCE_PROFILE_CONFIG = 45;
		const GROUP_PROFILE_CONFIG = 46; 
		const LBRULE_PROFILE_CONFIG = 47;
		const SIPDOMAIN_PROFILE_CONFIG = 48;
		//KEYTYPE
		const HOST_CONFIG_STR = "Host Configuration";
		const NET_CONFIG_STR = "Network Configuration";
		const SSH_CONFIG_STR = "SSH Configuration";
		const WEBUI_CONFIG_STR = "WebUI Configuration";
		const DEVICE_ACCESS_CONFIG_STR = "Device Access Configuration";
		const DEVICE_ACCESS_FILTER_CONFIG_STR = "Device Access Filter Configuration";
		const SIP_SECURITY_CONFIG_STR = "SIP Security Device Settings";
		const FIREWALL_CONFIG_STR = "Firewall Rules";
		const DPI_CONFIG_STR = "DPI Configuration";
		const WHITELIST_CONFIG_STR = "Whitelist Rules";
		const BLACKLIST_CONFIG_STR = "Blacklist Rules";
		const GEOIP_CONFIG_STR = "GeoIP Filters";
		const SIGUPDATE_CONFIG_STR = "Signature Update";
		const LOGGING_CONFIG_STR = "Logging";
		const WHITE_LIST_RULES_PRECEDENCE_CONFIG_STR = "Whitelist Rules Precedence";
		const TIME_SETTINGS_CONFIG_STR = "Time Settings";
		const EMAIL_CONFIG_STR = "E-mail Server";
		const MGMT_VLAN_CONFIG_STR = "Management Vlan Settings";
		const GLOBAL_FW_CONFIG_STR = "Global Firewall Settings";
		const DPI_ENABLED_FLAG_CONFIG_STR = "DPI Enabled";
		const IPSEC_VPN_CONFIG_STR = "IPSec VPN";
		const IPSEC_ENABLED_FLAG_CONFIG_STR = "IPSec Enabled Flag";
		const SIP_PROFILE_CONFIG_STR = "SIP Profile Configuration";
		const SIP_TRUNK_CONFIG_STR = "SIP Trunk Configuration";
		const LCR_CONFIG_STR = "LCR Configuration";
		const ROAMING_USERS_CONFIG_STR = "Roaming User Configuration";
		const MEDIA_PROFILE_CONFIG_STR = "Media Profile Configuration";
		const SBC_CONFIG_STR = "FreeBlox Configuration";
		const VLAN_CONFIG_STR = "VLAN Configuration";
		const VIP_CONFIG_STR = "VIP Configuration";
		const ROUTES_CONFIG_STR = "Routes Configuration";
		const PORT_FORWARDING_CONFIG_STR = "Port Forwarding Configuration";
		const SBC_GENERAL_CONFIG_STR = "FreeBlox General Settings";
		const TRANSCODING_CONFIG_STR = "Transcoding Settings";
		const FAX_PROFILE_CONFIG_STR = "T38 Fax Profiles";
		const PLUGIN_HUMBUG_CONFIG_STR = "Plugin Humbug";
		const DEVICELIST_PROFILE_CONFIG_STR = "Devicelist_setting profiles";
		const GENERAL_SETTING_CONFIG_STR="General Settings";
		const SUBSCRIBER_SETTINGS_CONFIG_STR = "Subscribers setting profile";
		const EVENTS_SETTINGS_CONFIG_STR = "Events setting profile";
		const SIPHEADERS_PROFILE_CONFIG_STR = "Sip headers Rules";
		const RESOURCE_PROFILE_CONFIG_STR = "Resource profile";
		const GROUP_PROFILE_CONFIG_STR = "Group Profile";
		const LBRULE_PROFILE_CONFIG_STR = "LB Rule Profile";
		const SIPDOMAIN_PROFILE_CONFIG_STR = "SIP Domain Profile";
	}
?>
