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

	$hidden = "";
	if(SysCalls::getSysArch() == "armv7l") {
		$hidden = 'style="display:none"';
	}

?>
	<div id="accordion" style="padding: 10px; color:#DDDDDD; ">
		<h3 onclick="openDashboard()" style="font-size: 12px;">Dashboard<img id="dashboard-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: block" /></h3>
	<div>
	</div>
	<h3 style="font-size: 12px" id="settings" onclick="menu_settings(this)">Network<img id="settings-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="interfaces" onclick="menu_settings(this)">
			Interfaces
		</div>
		<div id="sub-interfaces" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="device_settings" onclick="menu_settings(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Settings
			</div>
			<div class="subtabsetup" id="General_settings" onclick="menu_settings(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;General
			</div>
			<div class="subtabsetup" id="virtual_ip" onclick="menu_settings(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Virtual IP
			</div>
			<div class="subtabsetup" id="vlan_config" onclick="menu_settings(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;VLAN
			</div>
		</div>
		<div class="tabsetup" id="routes" onclick="menu_settings(this)">
			Routes
		</div>
		<div class="tabsetup" id="device_access" onclick="menu_settings(this)">
			Device Access
		</div>
	</div>

	<h3 style="font-size: 12px" id="system" onclick="menu_system(this)">System<img id="system-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="time_settings" onclick="menu_system(this)">
			Time Settings
		</div>
		<div class="tabsetup" id="logging" onclick="menu_system(this)">
			Logging
		</div>
		<div class="tabsetup" id="firmware_upgrade" onclick="menu_system(this)">
			Package Upgrade
		</div>
		<div class="tabsetup" id="email" onclick="menu_system(this)">
			E-mail Server Settings
		</div>
	</div>

	<h3 style="font-size: 12px" id="media" onclick="menu_media(this)">Media<img id="media-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="sbc_media_settings" onclick="menu_media(this)">
			Media Profile
		</div>
		<div <?php echo $hidden; ?> class="tabsetup" id="fax_profiles" onclick="menu_media(this)">
			T38 Fax Profiles
		</div>
	</div>

	<h3 style="font-size: 12px" id="sbc" onclick="menu_sbc(this)">Signalling<img id="sbc-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="sip_domain_profile" onclick="menu_sbc(this)">
			SIP Domain
		</div>
		<div class="tabsetup" id="sip_profile" onclick="menu_sbc(this)">
			SIP Profile
		</div>
		<div class="tabsetup" id="multiple_table" onclick="menu_sbc(this)">
			SIP Headers
		</div>
		<!--<div class="tabsetup" id="wan_profile" onclick="menu_sbc(this)">
			<img src="images/arrow.png" /> &nbsp;&nbsp;WAN Profile
		</div>-->
		<!--<div class="tabsetup" id="virtual_ip" onclick="menu_sbc(this)">
			<img src="images/arrow.png" /> &nbsp;&nbsp;Virtual IP
		</div>-->
		<div class="tabsetup" id="trunk_config" onclick="menu_sbc(this)">
			Trunk Configuration
		</div>
		<div class="tabsetup" id="roam" onclick="menu_sbc(this)">
			Roaming Users
		</div>
		<div class="tabsetup" id="lcr" onclick="menu_sbc(this)">
			Least Cost Routing
		</div>
		<div class="tabsetup" id="tls" onclick="menu_sbc(this)">
			TLS Settings
		</div>
		<div id="sub-tls" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="device_root_ca" onclick="menu_sbc(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Device Root CA
			</div>
			<div class="subtabsetup" id="server_certs" onclick="menu_sbc(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Server Certs
			</div>
			<div class="subtabsetup" id="client_certs" onclick="menu_sbc(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Client Certs
			</div>
		</div>
		<div class="tabsetup" id="sbc_gen_settings" onclick="menu_sbc(this)">
		General Settings
		</div>
	</div>

	<h3 style="font-size: 12px; display: none" id="psettings" onclick="presence_settings(this)">Presence<img id="settings-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="subscribers" onclick="presence_settings(this)">
			Subscribers
		</div>
		
		<div class="tabsetup" id="events" onclick="presence_settings(this)">
			Events
		</div>		
		<!--div class="tabsetup" id="geo_ip_update" onclick="menu_settings(this)">
			<img src="images/arrow.png" /> &nbsp;&nbsp;Geo IP Update
		</div-->
	</div>

	<!--<h3 style="font-size: 14px" id="auth" onclick="menu_auth(this)">Authentication<img id="auth-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="radius" onclick="menu_auth(this)">
			<img src="images/arrow.png" /> &nbsp;&nbsp;Radius
		</div>
	</div>-->

<!-- Load balance -->
	<h3 style="font-size: 12px; display:none" id="loadbalance" onclick="load_balance(this)">Load Balance<img id="settings-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="resourceprofile" onclick="load_balance(this)">
			Resource Profile
		</div>	
		<div class="tabsetup" id="groupprofile" onclick="load_balance(this)">
			Group Profile
		</div>	
		<div class="tabsetup" id="loadbalrule" onclick="load_balance(this)">
			Rule Profile
		</div>
	</div>

	<h3 style="font-size: 12px" id="sid" onclick="menu_security(this)">Security<img id="sid-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="sip" onclick="menu_security(this)">
			SIP
		</div>
		<div id="sub-sip" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="dpi_signatures" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp; Attacks Detection
			</div>
			<div class="subtabsetup" id="sip_sec_settings" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp; Protocol Compliance
			</div>
			<div class="subtabsetup" id="sig_update" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Signature Update
			</div>
		</div>
		<div class="tabsetup" id="firewall" onclick="menu_security(this)">
			Firewall
		</div>
		<div id="sub-firewall" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="firewall_conf" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Firewall Config
			</div>
			<div class="subtabsetup" id="dos_control" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Firewall Rate Limiting
			</div>
			<div class="subtabsetup" id="nat_rules" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Port Forwarding
			</div>
			<div class="subtabsetup" id="whitelist_conf" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Whitelist IP Addresses
			</div>
			<div class="subtabsetup" id="blacklist_conf" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Blacklist IP Addresses
			</div>
			<div class="subtabsetup" id="dyn_blacklist_conf" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Dynamic Blacklist IP Addresses
			</div>
			<div class="subtabsetup" id="geo_ip_filter" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Geo IP Filters
			</div>
		</div>
		<div class="tabsetup" id="vpn" onclick="menu_security(this)">
			VPN
		</div>
		<div id="sub-vpn" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="ipsec_vpn" onclick="menu_security(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;IPSec VPN
			</div>
		</div>
	</div>

	<h3 style="font-size: 12px" id="status" onclick="menu_status(this)">Status<img id="status-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="profile_status" onclick="menu_status(this)">
			Profile Status
		</div>
		<!--div class="tabsetup" id="lcr_status" onclick="menu_status(this)">
			LCR Status
		</div-->
		<div class="tabsetup" id="trunk_status" onclick="menu_status(this)">
			Trunk Status
		</div>
		<div class="tabsetup" id="roaming_user_status" onclick="menu_status(this)">
			Roaming User Status
		</div>
		<div class="tabsetup" id="active_call" onclick="menu_status(this)">
			Active Calls
		</div>
		<div class="tabsetup" id="logs" onclick="menu_status(this)">
			Logs
		</div>
		<div id="sub-logs" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="signal_logs" onclick="menu_status(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Signalling Logs
			</div>
			<div class="subtabsetup" id="media_logs" onclick="menu_status(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Media Logs
			</div>
<!--
			<div class="subtabsetup" id="lcr_logs" onclick="menu_status(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;LCR Logs
			</div>
-->
			<div class="subtabsetup" id="system_logs" onclick="menu_status(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;System Logs
			</div>
			<div class="subtabsetup" id="security_alerts" onclick="menu_status(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Security Logs
			</div> 
			<div class="subtabsetup" id="service_logs" onclick="menu_status(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Service Logs
			</div>
		</div>
		<div class="tabsetup" id="reports" onclick="menu_status(this)">
			Reports
		</div>
		<div id="sub-reports" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="cdr_reports" onclick="menu_status(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;CDR Reports
			</div>
		</div>
	</div>

	<h3 style="font-size: 12px" id="tools" onclick="menu_tools(this)">Tools<img id="tools-arrow" src="images/selected_arrow.png" align="right" style="padding: 3px; display: none" /></h3>
	<div>
		<div class="tabsetup" id="administration" onclick="menu_tools(this)">
			Administration
		</div>
		<div class="tabsetup" id="main-diagnostics" onclick="menu_tools(this)">
			Diagnostics
		</div>
		<div id="sub-diagnostics" class="subtabsetup_bg" style="display: none">
			<div class="subtabsetup" id="diagnostics" onclick="menu_tools(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Run Diagnostics
			</div>
			<div class="subtabsetup" id="ping" onclick="menu_tools(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Ping
			</div>
			<div class="subtabsetup" id="tracert" onclick="menu_tools(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Traceroute
			</div>
			<div class="subtabsetup" id="packet_capture" onclick="menu_tools(this)">
				<img src="images/arrow.png" /> &nbsp;&nbsp;Packet Capture
			</div>
		</div>
		<div class="tabsetup" id="trouble_shooting" onclick="menu_tools(this)">
			Troubleshooting
		</div>
		<div class="tabsetup" id="logs_archive" style="display:none" onclick="menu_tools(this)">
			Logs Archive
		</div>
		<div class="tabsetup" id="plugins" onclick="menu_tools(this)">
			Plugins
		</div>
	</div>
</div>
