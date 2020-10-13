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

function getTipText(id)
{
	var tip_text=[];
//	tip_text[id] = "Tool Tip Text goes here.";

// General Settings tooltips
	tip_text[0] = "Allows user to configure General Settings.";
	tip_text[1] = "Specify Host Name ";
	tip_text[2] = "User can configure LAN to be Static or DHCP";
	tip_text[3] = "Specify IP Address / Netmask.";
	tip_text[4] = "Specify gateway.";
	tip_text[5] = "Specify SSH Port (1-65535).";
	tip_text[51] = "Specify the value for DNS .";
	tip_text[67] = "Specify Management VLAN IP Address / Netmask.";
	tip_text[170] = "Select lan interface from the list";
	tip_text[171] = "Select wan interface from the list";
	tip_text[172] = "User can configure WAN to be Static or DHCP";
	tip_text[173] = "Specify IP Address / Netmask.";
	tip_text[174] = "Specify IP Address / Netmask.";
	tip_text[191] = "User can Enable/Disable SSH.";
	tip_text[192] = "User can Enable/Disable ICMP.";
	tip_text[193] = "Shows the status of transcoding card.";
	tip_text[194] = "Shows the interface name of transcoding card";
	tip_text[195] = "User can Enable/Disable transcoding.";

// Date/Time Settings tooltips
	tip_text[6] = "Allows user to configure Date / Time Settings.";
	tip_text[7] = "User can configure either Manual or NTP.";
	tip_text[8] = "User can configure Date / Time in format hh:mm-DD/MM/YYYY.";
	tip_text[9] = "User can select timezones from the list.";
	tip_text[10] = "Allows user to configure Date / Time Settings.";
	tip_text[11] = "User can add NTP servers to the NTP Server list.";

// DeviceAccess/BlackList/Whitelist rules tooltips
	tip_text[12] = "Specify a name with 5-16 char's.";
	tip_text[13] = "Select an option from the list ";
	tip_text[14] = "Specify IP Address/Netmask or IP Range or MAC address.";
	tip_text[15] = "Comments can be of 64 char's length.";
	tip_text[16] = "Select an option from the list ";
	tip_text[17] = "Select an option from the list ";
	tip_text[18] = "Select an option from the list ";
	tip_text[19] = "Specify IP Address/Netmask or IP Range.";
	tip_text[20] = "Specify Port Number 1-65535.";
	tip_text[199] = "Click checkbox to enable the rule.";
	tip_text[208] = "Select an option from the list .";

// Logging tooltips
	tip_text[21] = "Allows user to configure Remote Log Server Settings.";
	tip_text[22] = "Specify the ip address for Syslog Server";
	tip_text[436] = "Enable/Disable Remote logging";

// Signature update tooltips
	tip_text[23] = "Allows user to configure Signature Update.";
	tip_text[24] = "Specify the Subscription key";
	tip_text[25] = "Specify the DPI rules udpate URL";
	tip_text[26] = "Specify the time schedule";

// SIP security settings tooltips
	tip_text[27] = "Allows user to configure SIP compliance settings.";
	tip_text[28] = "Specify a value for Max Sessions.";
	tip_text[29] = "Specify a value for Max Dialogs per session.";
	tip_text[30] = "Specify a value for Max URI length.";
	tip_text[31] = "Specify a value for Max Call ID length.";
	tip_text[32] = "Specify a value for Max Request name length.";
	tip_text[33] = "Specify a value for Max From length.";
	tip_text[34] = "Specify a value for Max To length.";
	tip_text[35] = "Specify a value for Max Via length.";
	tip_text[36] = "Specify a value for Max Contact length.";
	tip_text[37] = "Specify a value for Max Content length.";
	tip_text[38] = "Allows user to configure SIP/MEDIA Ports Configuration.";
	tip_text[39] = "Select an option from SIP Transport list.";
	tip_text[40] = "Specify a value for SIP Ports.";
	tip_text[41] = "Select an option from Media Transport list.";
	tip_text[42] = "Specify a value for Media Ports.";
	tip_text[43] = "User can select options from the SIP Methods list.";
	tip_text[190] = "List shows currently selected SIP Methods.";

// SIP DPI Signature tooltips
	tip_text[44] = "Shows the category name.";
	tip_text[45] = "Select an option from the action list.";
	tip_text[46] = "Specify a value for blocking duration (1-86400).";
	tip_text[63] = "Specify a value in between 30 to 10000.";
	tip_text[64] = "Time interval where counting starts. Specify a value for time interval in secs (3-3600).";
	tip_text[65] = "Specify a value in between 10 to 10000.";
	tip_text[66] = "Time interval where counting starts. Specify a value for time interval in secs (3-3600).";
	tip_text[225] = "Specify a value in between 100 to 1000.";
	tip_text[226] = "Specify a values in between 10 to 100";

// SIP DPI Signature tooltips
	tip_text[47] = "Shows the country name.";
	tip_text[48] = "Configure the users to be allowed or not allowed for this country.";

// StatusAlerts and Firmware upgrade tooltips
	tip_text[49] = "Allows user to configure log viewer settings.";
	tip_text[50] = "Allows user to upload firmware.";

// Auto Upgrade
	tip_text[460] = "Allow user to set Auto Upgrade options";
	tip_text[461] = "Auto upgrade URL is required if auto upgrade is enabled.";

// StatusAlerts and Firmware upgrade tooltips
	tip_text[52] = "Specify the value for Web Session Timeout.";
	tip_text[53] = "Shows the user name (READ-ONLY).";
	tip_text[54] = "Enter the old password to change it.";
	tip_text[55] = "Choose a new password.";
	tip_text[56] = "Re-enter the new password to confirm.";

// Ping settings tooltips
	tip_text[57] = "Specify the value of host.";
	tip_text[58] = "Select the interface from the list.";
	tip_text[59] = "Select the count from the list.";

// Traceroute settings tooltips
	tip_text[60] = "Specify the value of host.";
	tip_text[61] = "Specify the value of hopcount.";
	tip_text[62] = "Enable/Disable ICMP.";

// Traceroute settings tooltips
	tip_text[68] = "Specify the value of TCP Syn flood rate. (0-100000)";
	tip_text[69] = "Specify the value of TCP Syn flood burst. (0-100000)";
	tip_text[70] = "Specify the value of TCP flood rate. (0-100000)";
	tip_text[71] = "Specify the value of TCP flood burst. (0-100000)";
	tip_text[72] = "Specify the value of UDP flood rate. (0-100000)";
	tip_text[73] = "Specify the value of UDP flood burst. (0-100000)";
	tip_text[74] = "Specify the value of ICMP flood rate. (0-100000)";
	tip_text[75] = "Specify the value of ICMP flood burst. (0-100000)";
	tip_text[76] = "Allows the user to configure global firewall settings.";

// Signalling Settings tooltips
	tip_text[77] = "Allows user to configure NAT settings";
	tip_text[78] = "User can select Keepalive Type as NOTIFY or OPTIONS";
	tip_text[79] = "Specify the value of Keepalive Interval. (5-86400)";
	tip_text[197] = "Specify the valid keepalive From-URI.";
	tip_text[206] = "Specify the user agent value(Alphnameric(special chars (-_@))";
	tip_text[207] = "Allows user to configure Global settings";
	tip_text[230] = "Specify the Maximum CDR Record (default :10000 Max : 999999)";
	tip_text[231] = "Enable / Disable Ping NAT Only.";

//  SIP Profile tooltips
	tip_text[80] = "Specify a name with 5-16 char's";
	tip_text[81] = "Specify comments of length 64 char's";
	tip_text[82] = "User can select interface from the interfaces list.";
	tip_text[83] = "User can select SIP protocol from the SIP protocol list.";
	tip_text[84] = "Specify SIP port in the range 1-65535.";
	tip_text[85] = "Enable keep alive.";
	tip_text[86] = "Specify keep alive interval in the range 60-360.";
	tip_text[87] = "Specify SIP TOS in the range 0-63.";
	tip_text[167] = "Select Server Certificates from the list.";
	tip_text[168] = "Specify domain name.";
	tip_text[185] = "Select NAT Settings from the list.";
	tip_text[186] = "Specify the value for IP address.";
	tip_text[438] = "Enable / Disable Required TLS.";
	tip_text[439] = "Enable / Disable Verified TLS.";
	tip_text[440] = "List of available Sip domains.";
	tip_text[441] = "List of active Sip domains.";
	tip_text[442] = "List of available Sip Headers.";
        tip_text[443] = "List of active Sip Headers.";


// trunks config tooltips
	tip_text[88] = "Specify a name with 5-16 char's";
	tip_text[89] = "Specify comments of length 64 char's";
	tip_text[90] = "Specify either IP Address:Port or Domain Name.";
	tip_text[91] = "User can select protocol from protocol list.";
	tip_text[92] = "Specify username  maxlength-64 (alpha-numeric).";
	tip_text[93] = "Specify password in the range of 1-64.";
	tip_text[94] = "Specify either IP Address:Port or Domain Name.";
	tip_text[95] = "Specify Registrar expire in the range 360-3600.";
	tip_text[96] = "Specify outbound add prefix / caller id name";
	tip_text[97] = "Specify Outbound Proxy URI in the format either IP Address:Port or Domain Name.";
	tip_text[98] = "Specify User Agent in the range of 1-16 (alpha-numeric).";
	tip_text[99] = "Select Internal SIP Profile from the list.";
	tip_text[100] = "Select External SIP Profile from the list.";
	tip_text[101] = "Select Media Profile from the list.";
	tip_text[102] = "Specify Add Prefix in the range of 1-16 (numbers only).";
	tip_text[103] = "Specify Strip digits in the range 0-9.";
	tip_text[460] = "Specify outbound add prefix / caller id number";
	tip_text[153] = "Enable Allow Inbound";
	tip_text[154] = "Specify max inbound in the range 1-10000.";
	tip_text[155] = "Enable Allow Outbound";
	tip_text[156] = "Specify max outbound in the range 1-10000.";
	tip_text[176] = "Specify Inbound URI in the format either user@IP/Domain:port or IP/Domain:port";
	tip_text[198] = "Specify SIP Domain Name.";
	tip_text[200] = "Select Media Encryption(LAN) from the list.";
	tip_text[201] = "Select Media Encryption(WAN) from the list.";
	tip_text[202] = "Specify T38 Profiles from the list.";
	tip_text[417] = "Enable Enum.";
	tip_text[418] = "Select Enum type from the list.";
	tip_text[419] = "Specify Enum suffix.";
	tip_text[420] = "Specify Enum service.";
	tip_text[454] = "Select the SIP domain from the list";
	tip_text[459] = "Select SIP domain from list and specify destination URI with port"; 

// least cost rounting tooltips
	tip_text[104] = "Specify a name with 5-16 char's";
	tip_text[105] = "Specify comments of length 64 char's";
	tip_text[106] = "Specify Prefix to match in the range of 1-16 (numbers only).";
	tip_text[107] = "Specify either IP Address or Domain.";
	tip_text[108] = "Specify password in the range of 1-64.";
	tip_text[109] = "Select SIP Profile from the list.";
	tip_text[110] = "List of avilable trunks.";
	tip_text[196] = "Specify Redirect Prefix to match selected trunk.";
	tip_text[433] = "List of active trunks.";

// least cost routing tooltips
	tip_text[111] = "Specify a name with 5-16 char's";
	tip_text[112] = "Specify comments of length 64 char's";
	tip_text[113] = "Select LAN SIP Profile from the list.";
	tip_text[114] = "Select WAN SIP Profile from the list.";
	tip_text[115] = "Select Media Profile from the list.";
	tip_text[116] = "Specify forward sip registrar in the format IP Address/Domain:Port.";
	tip_text[117] = "Select protocol from the list.";
	tip_text[118] = "Specify force expire in the range of 1-3600.";
	tip_text[203] = "Select Media Encryption(LAN) from the list.";
	tip_text[204] = "Select Media Encryption(WAN) from the list.";
	tip_text[205] = "Specify T38 Profiles from the list.";
	tip_text[209] = "Enable IP Authentication.";
	tip_text[210] = "Specify max inbound in the range 1-10000.";
	tip_text[211] = "Specify max outbound in the range 1-10000.";

// media tooltips
	tip_text[119] = "Allows user to configure RTP Port Settings";
	tip_text[120] = "Specify RTP ports range 1-65535";
	tip_text[121] = "Specify a name with 5-32 char's";
	tip_text[122] = "Specify comments of length 64 char's";
	tip_text[123] = "Select Codecs from the list.";
	tip_text[124] = "Specify Max media session value (1-90).";
	tip_text[125] = "Specify Session Time value (3600-432000).";
	tip_text[126] = "Enable SRTP";
	tip_text[127] = "Enable T.38 fax";
	tip_text[128] = "Enable Bypass Video";
	tip_text[129] = "Enable Silence Suppression";
	tip_text[130] = "Select DTMF Mode from the list";
	tip_text[131] = "Specify Media TOS value (0-63).";

// IPSec VPN tooltips
	tip_text[132] = "Select interfaces from the list";
	tip_text[133] = "Specify External Address";
	tip_text[134] = "Specify External Port (1-65535)";
	tip_text[135] = "Specify Internal Address";
	tip_text[136] = "Specify Internal Port (1-65535)";
	tip_text[137] = "Specify comments of length 64 char's";

// Network tool tips
	tip_text[138] = "Select interfaces from the list";
	tip_text[139] = "Specify IP address";
	tip_text[140] = "Specify Netmask";
	tip_text[141] = "Specify comments of length 64 char's";
	tip_text[142] = "Specify Tag ID in the range of 1-4092";
	tip_text[143] = "Specify Destination Address";
	tip_text[144] = "Specify Gateway";
	tip_text[145] = "Specify Metric Value in the range of 0-31";
	tip_text[169] = "Specify a name with 5-16 char's";


// Media Profile tool tips
	tip_text[146] = "Specify a name with 5-16 char's";
	tip_text[147] = "Specify comments of length 64 char's";
	tip_text[148] = "Select External interfaces from the list";
	tip_text[149] = "Select Internal interfaces from the list";
	tip_text[150] = "Select Transcoding interfaces from the list";
	tip_text[151] = "Specify RTP Port Start (1024-65534)";
	tip_text[152] = "Specify RTP Port End (1025-65535)";
	tip_text[152] = "Specify RTP Port End (1025-65535)";
	tip_text[175] = "Specify Media TOS value (0-63)";
	tip_text[187] = "List of available codecs";
	tip_text[188] = "List of active codecs";
	tip_text[189] = "Enable Silence Suppression.";
	tip_text[437] = "Enable / Disbale NAT";

// E-Mail server settings tooltips
	tip_text[157] = "Allows user to configure E-mail server settings";
	tip_text[158] = "Specify Server IP Address/Domain Name";
	tip_text[159] = "Specify Server Port in the range of 1-65535";
	tip_text[160] = "Specify Sender E-mail ID";
	tip_text[161] = "Specify Receiver E-mail ID";
	tip_text[162] = "Select Authentication from the list";
	tip_text[163] = "Specify Username in the range of 5-64 (alpha-numeric)";
	tip_text[164] = "Specify Password in the range of 5-64";

//port fwd tooltips
	tip_text[165] = "Select protocol from the list";
	tip_text[166] = "Specify a name with 5-16 char's";

//certificate tooltips
	tip_text[177] = "Specify Common Name ( 5-32 alphanumeric chars)";
	tip_text[178] = "Specify Country Name (Max 2 chars)";
	tip_text[179] = "Specify State or Province Name( 2-32 alphanumeric chars)";
	tip_text[180] = "Specify Organization Name ( 3-128 alphnanumeric (with space) chars)";
	tip_text[181] = "Specify E-mail ID";
	tip_text[182] = "Select Encryption Strength from the list";
	tip_text[183] = "Specify Valid Days value.";
	tip_text[184] = "Specify Organization Unit Name ( 3-32 alphnanumeric chars)";
	tip_text[215] = "upload format as 'PEM'";	
	tip_text[216] = "upload format as 'PKCS12 includes CA'";	

//fax profiles tooltips
	tip_text[300] = "Specify a name with 5-16 char's";
	tip_text[301] = "Specify comments of length 64 char's";
	tip_text[302] = "Select Fax Version from the list";
	tip_text[303] = "Select Max Bit Rate from the list";
	tip_text[304] = "Select Fax Rate Management from the list";
	tip_text[305] = "Enable Fax ECM";
	tip_text[306] = "Select Fax Udp EC from the list";

//devicelist settings tooltips
	tip_text[307] = "Specify the alphanumeric name";
	tip_text[308] = "Select interface type.";
	tip_text[309] = "select IP type.";
	tip_text[310] = "Specify IP Address.";
	tip_text[311] = "Specify IP Netmask.";
	tip_text[312] = "Specify deviceID.";
	tip_text[313] = "Specify Gateway.";
	tip_text[314] = "Specify the value for DNS.";
	tip_text[315] = "User can Enable/Disable SSH.";
	tip_text[316] = "Specify SSH Port (1-65535).";
	tip_text[317] = "User can Enable/Disable ICMP.";
	tip_text[434] = "Select NAT Type.";
	tip_text[435] = "Specify NAT IP Address .";

//General settings tooltips
	tip_text[318] = "Allow user to set SSH and ICMP ";
	tip_text[319] = "Specify the alphanumeric name";
	tip_text[320] = "User can Enable/Disable SSH.";
	tip_text[321] = "Specify SSH Port (1-65535).";
	tip_text[322] = "User can Enable/Disable ICMP.";

// Subscriber setting tooltip
	tip_text[401] = "Enter username maxlenth-64 char's (alpha-numeric)";
	tip_text[402] = "Enter Password";
	tip_text[403] = "Select Roaming User profiles";

// events setting
	tip_text[404] = "Enter from Username maxlenth-64 char's (alpha-numeric)";
	tip_text[405] = "Select Roaming User profiles";
	tip_text[406] = "Select subscriber lists";
	tip_text[407] = "Enter event";
	tip_text[408] = "Enter AC packets";
	tip_text[409] = "Enter expire";
	tip_text[431] = "List of available subscribers";
	tip_text[432] = "List of active subscribers";
	tip_text[455] = "Enable / Disable Operator";

// device access 
	tip_text[410] = "Select the protocol";
	tip_text[411] = "Specify the port";
	

// resource profile
	tip_text[415] = "Enter resource name";
	tip_text[416] = "Enter name";

//Sip domain settings
	
	tip_text[412] = "Specify the Sip domain (IP address or Domain Name)";
	tip_text[413] = "Enable / Disable Bind Port";
	tip_text[414] = "Enable / Disable Bind Host";

//Roaming user

	tip_text[421] = "Enable Presence";
	tip_text[422] = "Specify Ip address for presence server";
	tip_text[423] = "Specify Ip address for presence domain";

//plugins

	tip_text[424] = "Specify the name for creating Plugins";
	tip_text[425] = "To enable or disable the humbug Plugin";
	tip_text[426] = "Enter API key for authentication";
	tip_text[427] = "Enter encryption key for authentication";
	tip_text[428] = "Specify the CDR Export URI";
	tip_text[429] = "Set the duration how frequently the call logs send";
	tip_text[430] = "Specify the Fraud Detection URI";

// Sip Headers 

	tip_text[444] = "Selete the conditions";
	tip_text[445] = "Specify the header";
	tip_text[446] = "Select the headers from the list";
	tip_text[447] = "Specify headers name";
	tip_text[448] = "Select the index (0-9)";
	tip_text[449] = "Specify the params for header";
	tip_text[450] = "Specify the pattern";
	tip_text[451] = "Select the Action from the list";
	tip_text[452] = "Specify param-1";
	tip_text[453] = "Specify param-2";
	
// Packet Capture

	tip_text[456] = "Select Interface from the list";
	tip_text[457] = "Specify ports if required (1-65535)";
	tip_text[458] = "Select Timeout(10 sec - 5 mins)";
	tip_text[462] = "Specify digest authentication username (alpha-numeric).";


	if (id == -1) return "";

	if ( id < tip_text.length)
	{
		return tip_text[id];
	}
	return "";
}

//this function contains the no of tooltips required by a particular page with a reference to the tooltip array

function getDataOfIndex(page_id)
{
	switch(page_id)
	{
	    // case "page_id" : return new Array (["tooltip index", "respective array index"])

		/*------------- Device Settings --*/
			case "gen_settings":
			return (new Array(
				["001", "0"],
				["002", "1"],
				["003", "2"],
				["004", "3"],
				["005", "4"],
				["006", "5"],
				["007", "51"],
				["008", "67"],
				["009", "170"],
				["010", "171"],
				["011", "172"],
				["012", "173"],
				["013", "174"],
				["014", "191"],
				["015", "192"],
				["016", "193"],
				["017", "194"],
				["018", "195"]
			));
			break;

			case "time_settings":
			return (new Array(
				["001", "6"],
				["002", "7"],
				["003", "8"],
				["004", "9"],
				["005", "10"],
				["006", "11"]
			));
			break;

			case "add_rule":
			return (new Array(
				["001", "12"],
				["002", "13"],
				["003", "14"],
				["004", "15"],
				["005", "16"],
				["006", "17"],
				["007", "18"],
				["008", "19"],
				["009", "20"],
				["010","199"],
				["011","208"],
				["012","410"],
				["013","411"]	
			));
			break;

			case "logging":
			return (new Array(
				["001", "21"],
				["002", "22"],
				["003", "436"]
			));
			break;

			case "sig_update":
			return (new Array(
				["001", "23"],
				["002", "24"],
				["003", "25"],
				["004", "26"]
			));
			break;

			case "sip_sec":
			return (new Array(
				["001", "27"],
				["002", "28"],
				["003", "29"],
				["004", "30"],
				["005", "31"],
				["006", "32"],
				["007", "33"],
				["008", "34"],
				["009", "35"],
				["010", "36"],
				["011", "37"],
				["012", "38"],
				["013", "39"],
				["014", "40"],
				["015", "41"],
				["016", "42"],
				["017", "43"],
				["018", "190"]
			));
			break;

			case "dpi_sigs":
			return (new Array(
				["001", "44"],
				["002", "45"],
				["003", "46"],
				["004", "63"],
				["005", "64"],
				["006", "65"],
				["007", "66"]
			));
			break;
		/*------------- Device Settings --*/
			case "geo_ips":
			return (new Array(
				["001", "47"],
				["002", "48"]
			));
			break;

			case "tools":
			return (new Array(
				["001", "49"],
				["002", "50"],
				["003", "460"],
				["004", "461"]
			));
			break;
			/*------------- Device Settings --*/
			case "web_settings":
			return (new Array(
				["001", "52"],
				["002", "53"],
				["003", "54"],
				["004", "55"],
				["005", "56"]
			));
			break;
			/*------------- Device Settings --*/
			case "ping":
			return (new Array(
				["001", "57"],
				["002", "58"],
				["003", "59"]
			));
			break;

			case "tracert":
			return (new Array(
				["001", "60"],
				["002", "61"],
				["003", "62"]
			));
			break;

			case "dos_control":
			return (new Array(
				["001", "68"],
				["002", "69"],
				["003", "70"],
				["004", "71"],
				["005", "72"],
				["006", "73"],
				["007", "74"],
				["008", "75"],
				["009", "76"]
			));
			break;

			case "sig_settings":
			return (new Array(
				["001", "77"],
				["002", "78"],
				["003", "79"],
				["004","197"],
				["005","206"],
				["006","207"],
				["007","230"],
				["008","231"]
			));
			break;

			case "sip_profile_ttip":
			return (new Array(
				["001", "80"],
				["002", "81"],
				["003", "82"],
				["004", "83"],
				["005", "84"],
				["006", "85"],
				["007", "86"],
				["008", "87"],
				["009", "167"],
				["010", "168"],
				["011", "185"],
				["012", "186"],
				["013", "437"],
				["014", "438"],
				["015", "439"],
				["016", "440"],
				["017", "441"],
				["018", "442"],
				["019", "443"]
			));
			break;

			case "trunk_config_ttip":
			return (new Array(
				["001", "88"],
				["002", "89"],
				["003", "90"],
				["004", "91"],
				["005", "92"],
				["006", "93"],
				["007", "94"],
				["008", "95"],
				["009", "96"],
				["010", "97"],
				["011", "98"],
				["012", "99"],
				["013", "100"],
				["014", "101"],
				["015", "102"],
				["016", "103"],
				["017", "153"],
				["018", "154"],
				["019", "155"],
				["020", "156"],
				["021", "176"],
				["022","198"],
				["023","200"],
				["024","201"],
				["025","202"],
				["026","417"],
				["027","418"],
				["028","419"],
				["029","420"],
				["030","454"],
				["031","454"],
				["032","460"]
				["033","462"]

			));
			break;

			case "lcr_ttip":
			return (new Array(
				["001", "104"],
				["002", "105"],
				["003", "106"],
				["004", "107"],
				["005", "108"],
				["006", "109"],
				["007", "110"],
				["008", "196"],
				["009", "433"]
			));
			break;

			case "roaming_users_ttip":
			return (new Array(
				["001", "111"],
				["002", "112"],
				["003", "113"],
				["004", "114"],
				["005", "115"],
				["006", "116"],
				["007", "117"],
				["008", "118"],
				["009", "203"],
				["010", "204"],
				["011", "205"],
				["012", "209"],
				["013", "210"],
				["014", "211"],
				["015", "421"],
				["016", "422"],
				["017", "423"],
				["018", "417"],
				["019", "418"],
				["020", "419"],
				["021", "420"],
				["022", "459"]

			));
			break;

			case "media_ttip":
			return (new Array(
				["001", "119"],
				["002", "120"],
				["003", "121"],
				["004", "122"],
				["005", "123"],
				["006", "124"],
				["007", "125"],
				["008", "126"],
				["009", "127"],
				["010", "128"],
				["011", "129"],
				["012", "130"],
				["013", "131"]
			));
			break;

		case "port_fwd_ttip" :
			return (new Array(
				["001","132"],
				["002","133"],
				["003","134"],
				["004","135"],
				["005","136"],
				["006","137"],
				["007","165"],
				["008","166"]
			));
			break;

		case "network_ttip" :
			return (new Array (
				["001","138"],
				["002","139"],
				["003","140"],
				["004","141"],
				["005","142"],
				["006","143"],
				["007","144"],
				["008","145"],
				["009","169"],
				["010","434"],
				["011","435"]
			));
			break;

		case "media_settings_ttip" :
			return (new Array(
				["001","146"],
				["002","147"],
				["003","148"],
				["004","149"],
				["005","150"],
				["006","151"],
				["007","152"],
				["008","175"],
				["009", "187"],
				["010", "188"],
				["011", "189"],
				["012", "437"]
			));
			break;

		case "email_ttip" :
			return (new Array(
				["001","157"],
				["002","158"],
				["003","159"],
				["004","160"],
				["005","161"],
				["006","162"],
				["007","163"],
				["008","164"]
			));
			break;

		case "certs_ttip" :
			return (new Array(
				["001","177"],
				["002","178"],
				["003","179"],
				["004","180"],
				["005","181"],
				["006","182"],
				["007","183"],
				["008","184"],
				["009","215"],
				["010","216"]
			));
			break;

		case "fax_profiles_ttip" : //300-350 reserved for fax profiles
			return (new Array(
				["001","300"],
				["002","301"],
				["003","302"],
				["004","303"],
				["005","304"],
				["006","305"],
				["007","306"]
			));
			break;


		case "devicelist_settings_ttip" :
			return (new Array(
				["001","307"],
				["002","308"],
				["003","309"],
				["004","310"],
				["005","311"],
				["006","312"],
				["007","313"],
				["008","314"],
				["009","315"],
				["010","316"],
				["011","317"],
				["012","434"],
				["013","435"]				
								
			));
			break;

		case "general_settings_ttip" :
			return (new Array(
				["001","318"],
				["002","319"],
				["003","320"],
				["004","321"],
				["005","322"]								
			));
			break;
// subscribers setting profile 
		case "subscribers_settings_ttip" : 
			return (new Array(
				["001","401"],
				["002","402"],
				["003","403"],
				["004","455"]
			));
			break;
		
		case "events_settings_ttip" : 
			return (new Array(
				["001","404"],
				["002","405"],
				["003","406"],
				["004","407"],
				["005","408"],
				["006","409"],
				["007","431"],
				["008","432"]

			));
			break;
		
		case "resource_settings_ttip" : 
			return (new Array(
				["001","12"],
				["002","89"]

			));
			break;

		case "sipdomain_settings_ttip" :
			return (new Array(
				["001","412"],
				["002","413"],
				["003","414"]
			));
			break;	
			
		case "plugin_ttip" :
			return (new Array(
				["001","424"],
				["002","425"],
				["010","426"],
				["011","427"],
				["012","428"],
				["013","429"],
				["014","430"]
			));
			break;	

		case "sipheaders_ttip" :
			return (new Array(
                                ["001","121"],
                                ["002","81"],
                                ["003","444"],
                                ["004","445"],
                                ["005","446"],
                                ["006","447"],
                                ["007","448"],
				["008","449"],
				["009","450"],
				["010","451"],
				["011","452"],
				["012","453"]
                        ));
			break;

		case "packetcap_ttip" :
			return (new Array(
                                ["001","456"],
                                ["002","457"],
                                ["003","458"]
                        ));
                        break;

		}
	return -1;
}

function getPageTips(page_id)
{
	var checkPageId = getDataOfIndex(page_id);
	if (checkPageId != -1)
	{
		for (var i = 0; i < checkPageId.length; i++)
		{
			var tipId = "#" + page_id + "_" + checkPageId[i][0];
			var tipText = getTipText(parseInt(checkPageId[i][1], 10));
			$sub(tipId).attr("title", tipText);
		}
	}
}
