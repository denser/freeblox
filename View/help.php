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
	session_start();
	$page_section_id = $_SESSION['pageID'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery-1.8.2.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
		<script type="text/javascript">
			var $sub = $.noConflict(true);
			var page_section_id = '<?php print $page_section_id; ?>';
		</script>
		<script type="text/javascript">
			$sub(document).ready(function() {
				parent.helpTooltip();
				window.location.href = page_section_id;
			});

			function closeDialog() {
				parent.closeAddEditDialog();
			}
		</script>
	</head>
	<body class="popup">
		<div class="mainborder">
			<div class="pgtitle">Help<input type="button" class="btn-close" onclick="closeDialog();" onfocus="if(this.blur)this.blur()" /></div>
			<div class="popup-content" style="height: 430px">
				<div style="margin: 20px; margin-top: 0px; font-size: 13px">
				<ul type="square">
					<a name="top"></a>
					<li><a href="#dashboard" onfocus="if(this.blur)this.blur()">Dashboard</a><br /></li>
					<li><a href="#gen_settings" onfocus="if(this.blur)this.blur()">General Settings</a><br /></li>
					<li><a href="#virtualip_settings" onfocus="if(this.blur)this.blur()">Virtual IP Settings</a><br /></li>
					<li><a href="#vlan_settings" onfocus="if(this.blur)this.blur()">Vlan Settings</a><br /></li>
					<li><a href="#routes_settings" onfocus="if(this.blur)this.blur()">Routes Settings</a><br /></li>
					<li><a href="#device_access_settings" onfocus="if(this.blur)this.blur()">Device Access Settings</a><br /></li>
					<li><a href="#time" onfocus="if(this.blur)this.blur()">Time Settings</a><br /></li>
					<li><a href="#logging" onfocus="if(this.blur)this.blur()">Logging</a><br /></li>
					<li><a href="#firware_up" onfocus="if(this.blur)this.blur()">Firmware Upgrade</a><br /></li>
					<li><a href="#email_server_settings" onfocus="if(this.blur)this.blur()">Email Server Settings</a><br /></li>
					<li><a href="#media_profile_settings" onfocus="if(this.blur)this.blur()">Media Profile Settings</a><br /></li>
					<li><a href="#t38_fax_settings" onfocus="if(this.blur)this.blur()">T38 Fax Profile Settings</a><br /></li>
					<li><a href="#sip_domain_settings" onfocus="if(this.blur)this.blur()">SIP Domain Profile Settings</a><br /></li>
					<li><a href="#sip_profile_settings" onfocus="if(this.blur)this.blur()">SIP Profile Settings</a><br /></li>
					<li><a href="#sip_headers_settings" onfocus="if(this.blur)this.blur()">SIP Header Settings</a><br /></li>
					<li><a href="#trunk_config_settings" onfocus="if(this.blur)this.blur()">Trunk Configuration Settings</a><br /></li>
					<li><a href="#roaming_user_settings" onfocus="if(this.blur)this.blur()">Roaming User Settings</a><br /></li>
					<li><a href="#lcr_settings" onfocus="if(this.blur)this.blur()">LCR Settings</a><br /></li>
					<li><a href="#tls_cert" onfocus="if(this.blur)this.blur()">TLS Settings</a><br /></li>
					<li><a href="#gen_settings_signal" onfocus="if(this.blur)this.blur()">General Settings</a><br /></li>
					<li><a href="#presence_settings" onfocus="if(this.blur)this.blur()">Presence subscriber and events</a><br /></li>
					<li><a href="#dpi_sigs" onfocus="if(this.blur)this.blur()">SIP Attacks Detection</a><br /></li>
					<li><a href="#sip_settings" onfocus="if(this.blur)this.blur()">SIP Protocol Compliance</a><br /></li>
					<li><a href="#signature" onfocus="if(this.blur)this.blur()">Signature Update</a><br /></li>
					<li><a href="#firewall_config" onfocus="if(this.blur)this.blur()">Firewall Configuration</a><br /></li>
					<li><a href="#firewall_rate_limiting" onfocus="if(this.blur)this.blur()">Firewall Rate Limiting</a><br /></li>
					<li><a href="#firewall_portfwd" onfocus="if(this.blur)this.blur()">Firewall Port Forwarding</a><br /></li>
					<li><a href="#whitelist" onfocus="if(this.blur)this.blur()">Whitelist IP Addresses</a><br /></li>
					<li><a href="#blacklist" onfocus="if(this.blur)this.blur()"> Blacklist IP Addresses</a><br /></li>
					<li><a href="#dyn_blacklist" onfocus="if(this.blur)this.blur()">Dynamic Blacklist IP Addresses</a><br /></li>
					<li><a href="#geo_ip" onfocus="if(this.blur)this.blur()">Geo IP Filters</a><br /></li>
					<li><a href="#Profile_status" onfocus="if(this.blur)this.blur()">Status</a><br /></li>
					<li><a href="#active_calls" onfocus="if(this.blur)this.blur()">Active Calls</a><br /></li>
					<li><a href="#all_logs" onfocus="if(this.blur)this.blur()">Logs- Signalling,Media,LCR,System and Security</a><br /></li>
					<li><a href="#cdr_reports_help" onfocus="if(this.blur)this.blur()">CDR Reports</a><br /></li>
					<li><a href="#admin" onfocus="if(this.blur)this.blur()">Administration</a><br /></li>
					<li><a href="#diagno" onfocus="if(this.blur)this.blur()">Diagnostics</a><br /></li>
					<li><a href="#ping_sbc" onfocus="if(this.blur)this.blur()">Ping</a><br /></li>
					<li><a href="#tracert_sbc" onfocus="if(this.blur)this.blur()">Trace Route</a><br /></li>
					<li><a href="#packet_cap" onfocus="if(this.blur)this.blur()">Packet Capture</a><br /></li>
					<li><a href="#trouble" onfocus="if(this.blur)this.blur()">Troubleshooting</a><br /></li>
					<li style="display:none"><a href="#logs_arch" onfocus="if(this.blur)this.blur()"> Logs Archive</a><br /></li>
					<li><a href="#plugins_help" onfocus="if(this.blur)this.blur()">Plugins</a><br /></li>
				</ul>

					<a name="dashboard">
						<h3 class="title_help">Dashboard</h3>
					</a>
						<p>Top panel of UI shows the FreeBlox icon on the left clicking on which shows the Dashboard status.</p>
						<p>The left of the top panel shows the current time of device. Top panel also shows the firmware release version
						and has an icon <img src="images/refresh_icon.png" class="help_save_del" alt="Refresh" title="Refresh" />
						which will refresh the page.</p>
						<p>On the right side of the top panel clicking on settings icon shows the menu which has <b>Web Settings </b>
						and <b>Logout</b> options.</p>
						<p>Web Settings allows user to change the Old Password &amp; Session Timeout values where as user name is Read-Only.</p>
						<p>Clicking Logout will kill the session and redirects to the Login page.</p>
						<p>System Status Panel shows Device up time, Memory Usage, Flash Usage &amp; CPU Usage.</p>
		<p>Sig Update Version Panel shows FreeBlox Signature version and Release State.</p>
						<p>Network Status Panel shows IP, LAN MAC, WAN MAC and Gateway of the device.</p>
						<p>Security Alert Summary Panel shows four links and on mouse over shows
						the details of Top 10 Signatures, Top 10 Categories, Top Source &amp; Top Destinations.</p>

					<a name="gen_settings">
						<h3 class="title_help">General Settings</h3>
					</a>
					<p>General Settings allows user to configure Host Name, IP Configuration in both Static and DHCP mode. IP Addr/Mask, Gateway &amp; DNS fields are editable only in Static IP mode.</p>
					<p>It also allows user to enable or disable SSH access to device. </p>
					<p>User can save configurations by clicking on <b>Save</b> and can ignore saving the
					configurations by clicking on <b>Cancel</b>.</p>

					<a name="virtualip_settings">
						<h3 class="title_help">Virtual IP Settings</h3>
					</a>
					<p>virtual IP address (VIP or VIPA) is an IP address that doesn't correspond to an actual physical network interface (port). Uses for VIPs include network address translation (especially, one-to-many NAT), fault-tolerance, and mobility.</p>
					<p>A Virtual IP address (VIP or VIPA) is an IP address assigned to multiple applications residing on a single server, multiple domain names, or multiple servers, rather than being assigned to a specific single server or network interface card (NIC).</p>

					<a name="vlan_settings">
						<h3 class="title_help">Vlan Settings</h3>
					</a>
					<p>VLAN is a logically separate IP sub network. It allows multiple IP networks and subnets to exist on the same-switched network. VLANs are implemented to achieve scalability, security and ease of network management and can quickly adapt to change in network requirements and relocation of workstations and server nodes.</p>

					<a name="routes_settings">
						<h3 class="title_help">Routes Settings</h3>
					</a>
					<p>Routing is the process of selecting best paths in a network FreeBlox can also be used in conjunction with SIP trunks to provide call control and make routing/policy decisions on how calls are routed through the LAN/WAN.</p>

					<a name="device_access_settings">
						<h3 class="title_help">Device Access Settings</h3>
					</a>
					<p>It is a networking solution that uses a set of protocols to define and implement a policy that describes how to secure access the devices. It allows user to create a rule for device access that allows access to the device anywhere.</p>

					<a name="time">
						<h3 class="title_help">Time Settings</h3>
					</a>
					<p>Allows user to configure Date / Time. User can select the configuration type from the configuration type  menu and also allows to select the time zones from the time zone menu.</p>
					<p>In NTP configuration mode, user can add the NTP Server to the NTP list by clicking on <b>Add</b>
					button and can also delete the NTP Servers from the list by selecting and clicking on <b>Delete</b>
					button.</p>
					<p>Clicking on <b>Apply </b> will apply the configurations and <b>Cancel</b> will ignore the configurations made.
					</p>

					<a name="logging">
						<h3 class="title_help">Logging</h3>
					</a>
					<p>The administrator can configure the FreeBlox appliance to send the security alerts generated on detecting the SIP based attacks, to the remote syslog server.</p>
					<p>The logging page will allow enable/disable the remote logging of security alerts and to which syslog server the security alerts are to be forwarded.</p>

					<a name="firware_up">
						<h3 class="title_help">Firmware Upgrade</h3>
					</a>
					<p>Firmware can upgraded by selecting a file from the system and clicking on <b>Upgrade</b> button which reboots the device on success.</p>

					<a name="email_server_settings">
						<h3 class="title_help">Email Server Settings</h3>
					</a>
					<p>All email accounts we host, regardless of the domain name, will use the server settings in the email server configuration.</p>

					<a name="media_profile_settings">
						<h3 class="title_help">Media Profile Settings</h3>
					</a>
					<p>A media profile deals with all forms of media which goes in and out of the FreeBlox. Media Profile takes care of channeling of respective media. user can configure the media port range as well as type of the media like Transcoding or general media. Media profiles are the actual DSPs that perform RTP streaming, trans-coding etc.</p>

					<a name="t38_fax_settings">
						<h3 class="title_help">T38 Fax Profile Settings</h3>
					</a>
					<p>T38 is a protocol that describes how to send a fax over a computer data network. It is needed because fax data can not be sent over a computer data network in the same way as voice communications. T38 fax is converted to an image, sent to the other T38 fax device and then converted back to an analog fax signal.</p>

					<a name="sip_domain_settings">
						<h3 class="title_help">SIP Domain Profile Settings</h3>
					</a>
					<p>The Domain-based routing for roaming users provides support for matching an outbound dial peerbased on the domain name or IP address provided in the sip domain field. </p>

					<a name="sip_profile_settings">
						<h3 class="title_help">SIP Profile Settings</h3>
					</a>
					<p> A SIP Profile is an account built on the FreeBlox which contains a set of SIP attributes that are associated to the FreeBlox itself. The SIP profile is used as a configuration for how the external endpoints may connect to the FreeBlox. You bind an IP address, port, and other SIP related parameters to a SIP profile.</p>

					<a name="sip_headers_settings">
						<h3 class="title_help">SIP Header Settings</h3>
					</a>
					<p>Header manipulation is used when specific components within SIP messages need to be modified. SIP Header Manipulation provides the flexibility to add, remove, or modify any attribute in a SIP message on the Blox. The most common reason for doing this is to fix an incompatibility problem between two SIP endpoints</p>

					<a name="trunk_config_settings">
						<h3 class="title_help">Trunk Configuration Settings</h3>
					</a>
					<p>SIP Trunks are used to connect FreeBlox to a remote SIP Providers/User Agents. SIP Trunks can be used to communicate with SIP carriers or with IP-PBXs. It is the description of how the FreeBlox will communicate with that endpoint. Example: IP address, port, etc.</p>

					<a name="roaming_user_settings">
						<h3 class="title_help">Roaming User Settings</h3>
					</a>
					<p>Roaming user or Remote user is to create a profile for Internal (LAN) PBX such that user agents can register from the External network providing the details of roaming profiles. Roaming user is a kind of user/extension which can register to the LAN side PBX by giving the FreeBlox External IP address and external roaming port during registration</p>

					<a name="lcr_settings">
						<h3 class="title_help">LCR Settings</h3>
					</a>
					<p>Least cost routing uses the graphic user interface of the FreeBlox to allow users to create routing rules. Least Cost Routing rules can be used to route calls based on route costs.</p>

					<a name="tls_cert">
						<h3 class="title_help">TLS Settings</h3>
					</a>
					<p>TLS (Transport Layer Security) greatly enhances the security. TLS will provide endpoint authentication by using Mutual or Two-Way Authentication on a hop-by-hop basis User can upload a CA file and generate the same along with the Country name, Province Name, Organization name, Email address, Encryption strength and valid days etc. User can upload or generate server certificates iin the server certificates option It provides detailed information about the client certificates with viable options which are uploaded in FreeBlox.</p>

					<a name="gen_settings_signal">
						<h3 class="title_help">General Settings</h3>
					</a>
					<p>NAT (Network Address Translation) translates the source IP address of a device on one network interface, usually the Internal, to a different IP address as it leaves another interface, usually the interface connected to the ISP and the Internet. This enables a single public address to represent a significantly larger number of private addresses. User can configure the kepp alive Method, keep alive interval, keep alive from URI and user-Agent options.</p>

					<a name="presence_settings">
						<h3 class="title_help">Presence Subscriber & Event Settings</h3>
					</a>
					<p>The SIP Presence feature allows users to view the state of other users that belong to the same organization. The feature is somewhat similar to the instant messaging application status.To benefit from this feature, you must use a telephone with SIP presence/BLF (Busy Lamp Field) support. You can monitor the state of one or several extensions by means of a series of special settings in the phone interface Configure subscribers in the presence option and select the required roaming user configuration for which the presence is enabled in the field provided User can assign one subscriber as an operator by clicking on the check box for operator Create Event for a user and select the subscribers whom should get published for the event from the list Give "Message-summary" in the events field A/c packets field give "application/pidf+xml" this will come by default Specify required expiry time eg:-3600.</p>

					<a name="dpi_sigs">
						<h3 class="title_help">SIP Attacks Detection</h3>
					</a>
					<p>The SIP Attack Detection page allows to configure the SIP Deep packet Inspection rules categories. The administrator can enable/disable the inspection against particular category of rules, action to be taken on detecting attacks matching the rules in the categories.</p>
					<p>The possible actions that the FreeBlox can execute are log the alert, block the packets containing the attack vector and blacklist the attacker ip for the given duration. The blocking duration of how long the attacker up needs to be blocked is also configure per category level.</p>
					<p>The table given below lists the SIP Deep packet Inspection rules categories supported in FreeBlox and configuration parameters in each category.</p>

					<table class="table_help" border="1">
						<tr>
							<th>Category</th>
							<th>Description</thead>
							<th>User Configurable options</th>
						</tr>
						<tbody>
							<tr align="left">
								<td>SIP Reconnaissance Attacks</td>
								<td>The intruder is trying to detect what version of Asterisk you are running. With that info, he will start exploiting the numerous vulnerabilities of that version. The FreeBlox will not respond to his query.</td>
								<td>-</td>
							</tr>
							<tr align="left">
								<td>SIP Devices Scanning</td>
								<td>The intruder will scan the PBX ports to see what devices are connected to it. With that info, he can exploit 3rd party vulnerabilities. The FreeBlox will not respond to his query.</td>
								<td>-</td>
							</tr>
							<tr align="left">
								<td>SIP Extensions Discovery</td>
								<td>The intruder will ask the PBX to divulge the range of the extension numbers. With that info, he can try different passwords to take control of these extensions. The FreeBlox will not respond to that query.</td>
								<td>Invalid SIP User Registration Attempts/Duration</td>
							</tr>
							<tr align="left">
								<td>Multiple Authentication Failures/Bruteforce password Attempt</td>
								<td>The intruder will try to log in with different user names and passwords multiple times. Once he succeeds, he will have control of that extension. The FreeBlox can block, log or blacklist the IP for a period of time if it exceeds the authorized number of trials/second.</td>
								<td>Failed Authentication Attempts/Duration</td>
							</tr>
							<tr align="left">
								<td>Ghost calls Attempt</td>
								<td>The intruder will generate calls to an extension and it will look like the calls come from that same extension. His goal is to crash the PBX resulting in disrupted communication. The FreeBlox can block, log or blacklist the IP for a period of time if it exceeds the authorized number of trials/second.</td>
								<td>No of Anonymous Invite Responses/Duration</td>
							</tr>
							<tr align="left">
								<td>SIP Dos Attacks</td>
								<td>Flooding attempts using various SIP messages.</td>
								<td>No of SIP Request Messages/Duration</td>
							</tr>
							<tr align="left">
								<td>SIP DDos Attacks</td>
								<td>Distributed flooding attempts using various SIP messages.</td>
								<td>No of SIP Response Messages/Duration</td>
							</tr>
							<tr align="left">
								<td>SIP Anomaly attacks</td>
								<td>The intruder will send abnormal SIP packets to the PBX. His goal is to crash the PBX resulting in disrupted communication. The FreeBlox can block, log or blacklist the IP for a period of time if it exceeds the authorized number of trials/second.</td>
								<td>-</td>
							</tr>
							<tr align="left">
								<td>SIP Buffer overflow attacks</td>
								<td>Buffer overflow attempts resulted from improper validation of user inputs.</td>
								<td>-</td>
							</tr>
							<tr align="left">
								<td>SIP Cross site scripting</td>
								<td>SIP is vulnerable to cross-site scripting, caused by improper validation of user-supplied input in a SIP request. A remote attacker could exploit this vulnerability to inject malicious script into a Web page which would be executed in a victim's Web browser, when the victim accessed a web page containing information taken from the SIP request.</td>
								<td>-</td>
							</tr>
							<tr align="left">
								<td>3rd Party vendor vulnerabilities</td>
								<td>Attacks targeted towards PBX/SIP Gateway appliances exploiting their vulnerabilities.</td>
								<td>-</td>
							</tr>
						</tbody>
					</table>

					<a name="sip_settings">
						<h3 class="title_help">SIP Protocol Compliance</h3>
						</a>
						<p>The SIP Deep packet inspection engine running the FreeBlox appliance has been made to inspect the SIP traffic with the SIP Security Compliance rules in built into the SIP DPI engine.</p>
						<p>The anomalies in the SIP Message headers can result to various erroneous conditions, SIP parser failures & malformed packets which will lead to SIP applications vulnerable to attacks.</p>
						<p>The following parameters will be used by the SIP deep packet engine for identifying the different protocol anomaly conditions and take the action configured by the administrator.</p>
						<p>Configuring inappropriate values for these parameters can result to disruptive impact in the VOIP deployment. Administrators with more in-depth understanding with the SIP Protocol can choose to tune these parameters for their specific deployment needs. Otherwise it is recommended to use the default settings for these parameters.</p>
						<p><b>Max_sessions</b><br/>
A SIP session is the application level connection setup created between the SIP server and SIP client for exchanging the audio/video messages with each other.</p>
						<p>The max_sessions parameter defines the maximum number session that SIP deep packet inspection engine can keep track of. The default value has been set as 4096. </p>
						<p><b>Max Dialogs per session</b><br/>
Max_Dialogs_per_session specifies the maximum number of SIP messages transaction that can happen between the SIP server and client.</p>
						<p><b>Methods</b><br/> This parameter specifies on what methods to check for SIP messages.<br/> Following are the SIP messages that SIP DPI Engine can identify: (1) invite, (2) cancel, (3) ack, (4) bye, (5) register, (6) options, (7) refer, (8) subscribe, (9) update (10) join (11) info (12) message (13) notify (14) prack.</p>
						<p><b>Max_uri_len</b><br/>
The uri identifies the user or service to which SIP request is being addressed. Max_uri_len specifies the maximum Request URI field size. Default is set to 256. The allowed range for this option is 1 - 65535.</p>
						<p><b>Max_call_id_len</b><br/> The Call-ID header field in SIP message acts as a unique identifier that relates to sequence of messages exchanged between SIP client and server. Max_call_id_len specifies the maximum Call-ID field size. Default is set to 256. The allowed range for this option is 1 - 65535.</p>
						<p><b>Max_requestName_len</b><br/> Max_requestName_len specifies the maximum request name size that is part of the CSeq ID. Default is set to 20. The allowed range for this option is 1 - 65535</p>
						<p><b>Max_from_len</b><br/> The From header field indicates the identity of the initiator of the SIP request. Max_from_len specifies the maximum From field size. The allowed range for this option is 1 - 65535.</p>
						<p><b>Max_to_len</b><br/> The To header field specifies the desired  recipient of the SIP request. Max_to_len specifies the maximum To field size. Default is set to 256. The allowed range for this option is 1 - 65535.</p>
						<p><b>Max_via_len</b><br/> The Via header field indicates the transport used for the SIP transaction &amp; identifies the location where the SIP response is to be sent.</p>
						<p>Max_via_len specifies the maximum Via field size. Default is set to 1024. The allowed range for this option is 1 - 65535.</p>
						<p><b>Max_contact_len</b><br/> Identifier used to contact that specific instance of the SIP client/server for subsequent requests. Max_contact_len specifies the maximum Contact field size. Default is set to 256. The allowed range for this option is 1 - 65535.</p>
						<p><b>Max_content_len</b><br/> Max_content_len specifies the maximum content length of the message body. Default is set to 1024. The allowed range for this option is 1 - 65535.</p>
						<a name="signature">
							<h3 class="title_help">Signature Update</h3>
						</a>
						<p>Allows user to schedule the update by configuring the time schedule fields.</p>
						<p><b>Apply</b> will cause signature to be updated according to the time scheduled by user.</p>
						<p><b>Update Signatures now</b> updates the signatures at that moment.</p>

						<a name="firewall_config">
							<h3 class="title_help">Firewall Configuration</h3>
						</a>
						<p> The firewall rules configuration will allow the administrator in configuring what traffic should be allowed to protected SIP PBX/Gateway network from untrusted wan zone, besides DPI enabled SIP traffic and RTP traffic.</p>
						<p> The administrator needs to specify the source and destination networks and port numbers and protocol that will be used as the matching criteria in the filtering rule and action to be taken on matching the filtering rule.
						</p>
						<p>
							The possible actions are to block the traffic and allow the traffic on matching the filtering rule.
						</p>
							The rules precedence will be in the order in which the rules configured on firewall rules table.
						</p>
						<p>Shows the table with columns Name, Enabled, Src Type, Src Addr, Dst Type, Dst Addr, Protocol, Port and Action.</p>
						<p>User can search the entries by entering the value in the <b>Search</b> box which appears on top right of the table.</p>
						<p>Clicking on <b>Add New</b> opens a dialog with fields Name, Enabled, Src Type, Src Addr, Dst Type, Dst Addr, Protocol, Port and Action.</p>
						<p>Single entry can be deleted by clicking on the delete button. Multiple entries can be deleted by selecting the check boxes which appears on left of each entry.<b>Delete Selected</b> will delete the entries which are selected.</p>
						<p>User can sort (Ascending / Descending) the table entries by clicking on the particular column of the table for e.g. Name.</p>
						<p>Entry can be edited by clicking on <img src="images/btn-edit.png" class="help_save_del" title="Edit Entry" alt="Edit"> button.</p>
						<p>Entry can be deleted by clicking on <img src="images/btn-delete.png" class="help_save_del" title="Delete Entry" alt="Delete"> button.</p>
						<p>Changes can be saved by clicking on <b>Save</b> button and can ignore the changes by clicking on <b>Cancel</b> button.</p>

						<a name="firewall_rate_limiting">
							<h3 class="title_help">Firewall Rate Limiting</h3>
						</a>
						<p>Add this with firewall configuration and display that page. The firewall Rate Limiting configuration will allow the administrator in configuring rate at which traffic should be allowed to protected SIP PBX/Gateway network from untrusted wan zone, besides DPI enabled SIP traffic and RTP traffic.</p>
						<a name="firewall_portfwd">
							<h3 class="title_help">Firewall Port Forwarding</h3>
							</a>
						<p>Port forwarding or port mapping is an application of network address translation (NAT) that redirects a communication request from one address and port number combination to another while the packets are traversing a network gateway, such as a router or firewall.</p>
						<a name="whitelist">
							<h3 class="title_help">Whitelist IP Addresses</h3>
						</a>
						<p> This page allows to configure the white listed ip addresses in the untrusted wan zone from which the access to communicate with the protected SIP network will be allowed by the FreeBlox firewall. </p>
						<p> This page will also allows configuring whether the white rules take precedence over the blacklist rules (both static and dynamic) configured on the device at any instant. </p>
					<p>White list Rules Precedes over Blacklist Rules can be saved by clicking on <b>Save</b> button.</p>
					<p>Shows the table with columns Name, IP Type, Address, Enabled and Comments.</p>
					<p>User can search the entries by entering the value in the <b>Search</b> box which appears on top right of the table.</p>
					<p>Clicking on <b>Add New</b> opens a dialog with fields Name, IP Type, Address, Enabled and Comments.</p>
					<p>Single entry can be deleted by clicking on the delete button. Multiple entries can be deleted by selecting the check boxes which appears on left of each entry.<b>Delete Selected</b> will delete the entries which are selected.</p>
					<p>User can sort (Ascending / Descending) the table entries by clicking on the particular column of the table for e.g. Name.</p>
					<p>Entry can be edited by clicking on <img src="images/btn-edit.png" class="help_save_del" title="Edit Entry" alt="Edit"> button.</p>
					<p>Entry can be deleted by clicking on <img src="images/btn-delete.png" class="help_save_del" title="Delete Entry" alt="Delete"> button.</p>
					<p>Changes can be saved by clicking on <b>Save</b> button and can ignore the changes by clicking on <b>Cancel</b>
					button.</p>

					<a name="blacklist">
						<h3 class="title_help">Blacklist IP Addresses</h3>
					</a>
					<p> This page allows to configure the black listed ip addresses in the untrusted wan zone from which the access to communicate with the protected SIP network will be blocked by the FreeBlox firewall. </p>
					<p>Shows the table with columns Name, IP Type, Address, Enabled and Comments.</p>
					<p>User can search the entries by entering the value in the <b>Search</b> box which appears on top right of the table.</p>
					<p>Clicking on <b>Add New</b> opens a dialog with fields Name, IP Type, Address, Enabled and Comments.</p>
					<p>Single entry can be deleted by clicking on the delete button. Multiple entries can be deleted by selecting the checkboxes which appears on left of each entry.<b>Delete Selected</b> will delete the entries which are selected.</p>
					<p>User can sort (Ascending / Descending) the table entries by clicking on the particular column of the table for e.g. Name.</p>
					<p>Entry can be edited by clicking on <img src="images/btn-edit.png" class="help_save_del" title="Edit Entry" alt="Edit"> button.</p>
					<p>Entry can be deleted by clicking on <img src="images/btn-delete.png" class="help_save_del" title="Delete Entry" alt="Delete"> button.</p>
					<p>Changes can be saved by clicking on <b>Save</b> button and can ignore the changes by clicking on <b>Cancel</b> button.</p>

					<a name="dyn_blacklist">
						<h3 class="title_help">Dynamic Blacklist IP Addresses</h3>
					</a>
					<p>The dynamic blacklist addresses are the blocking rules added by the FreeBlox SIP deep packet inspection engine to block the traffic from attacker ip addresses for the blocking duration configured in the rules category, on detecting the attack.</p>
					<p>The dynamic blacklist addresses page will allow the administrator to see the dynamic blacklist addresses currently configured on the device at any instant. In case if the administrator wants to override and allow the traffic from particular blacklisted ip, he can delete the address from the dynamic blacklist addresses page.</p>
					<p>Shows the table with columns Address and Options.</p>
					<p>User can search the entries by entering the value in the <b>Search</b> box which appears on top right of the table.</p>
					<p>Single entry can be deleted by clicking on the delete button. Multiple entries can be deleted by selecting the check boxes which appears on left of each entry.<b>Delete Selected</b> will delete the entries which are selected.</p>
					<p>User can sort (Ascending / Descending) the table entries by clicking on the particular column of the table for e.g. Name.</p>
					<p>Entry can be deleted by clicking on <img src="images/btn-delete.png" class="help_save_del" title="Delete Entry" alt="Delete"> button.</p>

					<a name="geo_ip">
						<h3 class="title_help">Geo IP Filters</h3>
					</a>
					<p>The administrator can choose to block the traffic originating from the specific countries towards the protected SIP network, by configuring the GeoIP filter rules in FreeBlox.</p>
					<p>Clicking on Allow All Countries will allow all the countries and Block All Countries will block all the countries.</p>
					<p>Clicking on Update Geo IP will download the latest database from website and replace the existing country database.</p>
					<p>Shows the table with columns Country Name and Allowed.</p>
					<p>User can search the entries by entering the value in the <b>Search</b> box which appears on top right of the table.</p>
					<p>User can sort (Ascending / Descending) the table entries by clicking on the particular column of the table for e.g. Name.</p>
					<p>Entry can be edited by clicking on <img src="images/btn-edit.png" class="help_save_del" title="Edit Entry" alt="Edit"> button.</p>
					<p>Changes can be saved by clicking on <b>Save</b> button and can ignore the changes by clicking on <b>Cancel</b>
					button.</p>

					<a name="Profile_status">
						<h3 class="title_help">Status</h3>
						</a>
					<p>This page provides information of the profiles created by the user in the profile configuration. The registered roaming user status will display in Roaming user status page If a trunk is registered with the service provided it will display in the trunk status page</p>
		
					<a name="active_calls">
						<h3 class="title_help">Active Calls</h3>
					</a>
					<p> Active calls is to quickly check and monitor the all current active call running on your server </p>

					<a name="all_logs">
						<h3 class="title_help">Logs</h3>
					</a>
					<p>Log Viewer Settings panels shows Refresh interval. User can configure Refresh interval by clicking on the <b>Update Refresh Interval</b>. For instant refresh click<b>Refresh</b> button. User can also download logs by clicking on the <b>Download Logs</b> button.</p>
					<p>The table shows Detected Time, ID, Category, Message Content, Source IP &amp; its port number, Destination IP &amp; its port and Protocol type</p>
					<p>User can search the entries by entering the value in the <b>Search</b> box which appears on top right of the table.</p>
					<p>User can sort (Ascending / Descending) the table entries by clicking on the particular column of the table for e.g. Src IP.</p>
					<p>User can also edit E-mail server settings by clicking on the <b>Edit E-mail server settings.</b></p>
					<p>Enabling the  Enable E-mail Notification check box will send notification to the specified receiver email-id. User has to specify the values for Email Server IP and Port.</p>
					<p>User has to specify the Sender email id. There are two Authentication typesAuth_login and Auth_plain, when Auth_plain is selected user doesn't need to specify any values for username and password where as for Auth_login user has to specify any values for username and password.</p>
					<p>Notice once in every field will notify the user about the notifications based on the selection i.e., day, hour or week.</p>

					<a name="cdr_reports_help">
						<h3 class="title_help">CDR Reports</h3>
					</a>
					<p>CDR reports shows all the call deetail record of calls made through Blox. User can configure Refresh interval by clicking on the Update Refresh Interval. For instant refresh clickRefresh button. User can also download logs by clicking on the Download Logs button.</p>

					<a name="admin">
						<h3 class="title_help">Administration</h3>
					</a>
					<p>User can do factory reset by clicking on <b>Factory Reset</b> button.</p>
					<p>User can restart FreeBlox services by clicking on <b>Restart FreeBlox Services</b> button.</p>
					<p>User can reboot device by clicking on <b>Reboot</b> button.</p>
					<p>User can shutdown device by clicking on <b>Shutdown</b> button.</p>
					<p>User can take back up of the configuration by clicking on <b>Config Back-Up</b> button.</p>
					<p>Restoring the configuration can be done by selecting the configuration file from the system and clicking
					on the <b>Config Restore</b> button which reboots the machine on success.</p>
					<a name="diagno">
						<h3 class="title_help">Diagnostics</h3>
					</a>
				<p>User can run diagnostics by clicking on <b>Run Diagnostics</b> button and result can be seen in the text area which appears below the buttons.</p>
				<p>Diagnostics report can be downloaded by clicking on the <b>Get Report</b> button</p>

					<a name="ping_sbc">
						<h3 class="title_help">Ping</h3>
					</a>
					<p>User can ping a host by entering values for host IP / Domain Name  and selecting the count from the list.</p>
					<p><b>Ping</b> button will send a ping request to the host and <b>Reset</b> button clears the entered values.</p>
					<p>Ping result is shown in the text area which appears below the ping and reset buttons.</p>

					<a name="tracert_sbc">
						<h3 class="title_help">Traceroute</h3>
					</a>
					<p>User can trace route a host by entering values for host IP / Domain Name , hopcount  and enabling the ICMP by clicking ICMP checkbox.</p>
					<p><b>Traceroute</b> button will send a trace route request to the host and <b>Reset</b> button clears the entered values.</p>
					<p>Traceroute result is shown in the text area which appears below the ping and reset buttons.</p>

					<a name="packet_cap">
						<h3 class="title_help">Packet Capture</h3>
					</a>
					<p>Packet Capture is a common packet analyzer that runs under the commandof selected interface with 4 ports of max and timeout of 10-300secs . It allows the user to display TCP/IP and other packets being transmitted or received over a network to which the computer is attached.The Packet Capture library provides a high level interface to packet capture systems. All packets on the network, even those destined for other hosts, are accessible through this mechanism. It also supports saving captured packets and saved as pcap file..</p>

					<a name="trouble">
						<h3 class="title_help">Troubleshooting</h3>
					</a>
					<p>Clicking on <b>Enable DPI or Disable DPI</b> button which enables or disables DPI.</p>
					<p>Clicking on <b>Enable Firewall or Disable Firewall</b> button which enables or disables Firewall.</p>

					<a style="display:none" name="logs_arch">
						<h3 class="title_help">Logs Archive</h3>
					</a>
					<p>After the device storage has reached it's limit, logs are stored in USB storage device if one is connected.</p>
				<p>Logs archive summary is listed in the text area.</p>

					<a name="plugins_help">
						<h3 class="title_help">Plugins</h3>
					</a>
					<p>Humbug is the plugin used for added security and to analyze the logs in the blox Get real-time information on your telecom traffic, including segmentation by caller, country, PBX & more Easily access the data you need by creating custom reports using drill-down filters Humbug matches your traffic against a central fraud information database, protecting you from known and emerging fraud attacks </p>
				</div>
			</div>
			<div class="popup-footer">
				<a href="#top"><input type="button" class="btn-top" title="Go to Top" onfocus="if(this.blur)this.blur()" /></a>
			</div>
		</div>
	</body>
</html>
