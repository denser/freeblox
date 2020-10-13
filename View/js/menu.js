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
function openDashboard(elem)
{
	document.getElementById("container").src = "dashboard.php";

	$main("#dashboard-arrow").css("display", "block");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_settings(elem)
{
	if (elem.id == "settings" || elem.id == "interfaces")
	{
		$main("#sub-interfaces").show();
		$main("#device_settings").attr("class", "subtabselected");
		$main("#General_settings").attr("class", "subtabsetup");
		$main("#virtual_ip").attr("class", "subtabsetup");
		$main("#vlan_config").attr("class", "subtabsetup");
		$main("#routes").attr("class", "tabsetup");
		$main("#device_access").attr("class", "tabsetup");
		$main("#logging").attr("class", "tabsetup");

		document.getElementById("container").src = "devicelist_setting.php";
	}

	if (elem.id == "device_settings")
	{
		$main("#device_settings").attr("class", "subtabselected");
		$main("#General_settings").attr("class", "subtabsetup");
		$main("#virtual_ip").attr("class", "subtabsetup");
		$main("#vlan_config").attr("class", "subtabsetup");
		$main("#routes").attr("class", "tabsetup");
		$main("#device_access").attr("class", "tabsetup");
		$main("#logging").attr("class", "tabsetup");

		document.getElementById("container").src = "devicelist_setting.php";
	}
 
	if (elem.id == "General_settings")
	{
		$main("#device_settings").attr("class", "subtabsetup");
		$main("#General_settings").attr("class", "subtabselected");
		$main("#virtual_ip").attr("class", "subtabsetup");
		$main("#vlan_config").attr("class", "subtabsetup");
		$main("#routes").attr("class", "tabsetup");
		$main("#device_access").attr("class", "tabsetup");
		$main("#logging").attr("class", "tabsetup");

		document.getElementById("container").src = "general_settings.php";
	}


	if (elem.id == "virtual_ip")
	{
		$main("#device_settings").attr("class", "subtabsetup");
		$main("#General_settings").attr("class", "subtabsetup");
		$main("#virtual_ip").attr("class", "subtabselected");
		$main("#vlan_config").attr("class", "subtabsetup");
		$main("#routes").attr("class", "tabsetup");
		$main("#device_access").attr("class", "tabsetup");
		$main("#logging").attr("class", "tabsetup");

		document.getElementById("container").src = "virtual_ip.php";
	}

	if (elem.id == "vlan_config")
	{
		$main("#device_settings").attr("class", "subtabsetup");
		$main("#General_settings").attr("class", "subtabsetup");
		$main("#virtual_ip").attr("class", "subtabsetup");
		$main("#vlan_config").attr("class", "subtabselected");
		$main("#routes").attr("class", "tabsetup");
		$main("#device_access").attr("class", "tabsetup");

		document.getElementById("container").src = "vlan_config.php";
	}

	if (elem.id == "routes")
	{
		$main("#sub-interfaces").hide();
		$main("#interfaces").attr("class", "tabsetup");
		$main("#routes").attr("class", "tabsetupselected");
		$main("#device_access").attr("class", "tabsetup");

		document.getElementById("container").src = "routes.php";
	}

	if (elem.id == "device_access")
	{
		$main("#sub-interfaces").hide();
		$main("#interfaces").attr("class", "tabsetup");
		$main("#routes").attr("class", "tabsetup");
		$main("#device_access").attr("class", "tabsetupselected");
		$main("#logging").attr("class", "tabsetup");

		document.getElementById("container").src = "device_access.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "block");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_sbc(elem)
{
	if ( elem.id == "sbc_gen_settings")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").hide();
		$main("#sbc_gen_settings").attr("class", "tabsetupselected");

		document.getElementById("container").src = "sbc_gen_settings.php";
	}

	if ( elem.id == "sbc" || elem.id == "sip_domain_profile")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetupselected");
		/*$main("#wan_profile").attr("class", "tabsetup");*/
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").hide();
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "sip_domain_profile.php";
	}

	/*if (elem.id == "wan_profile")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#wan_profile").attr("class", "tabsetupselected");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "wan_profile.php";
	}*/

	if (elem.id == "trunk_config")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		/*$main("#wan_profile").attr("class", "tabsetup");*/
		$main("#trunk_config").attr("class", "tabsetupselected");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").hide();
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "trunk_config.php";
	}

	if (elem.id == "multiple_table")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		/*$main("#wan_profile").attr("class", "tabsetup");*/
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetupselected");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").hide();
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "sipheader.php";
	}

	if (elem.id == "lcr")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		/*$main("#wan_profile").attr("class", "tabsetup");*/
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetupselected");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").hide();
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "least_call_routing.php";
	}

	if (elem.id == "roam")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#virtual_ip").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetupselected");
		$main("#sub-tls").hide();
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "roaming_users.php";
	}

	if (elem.id == "tls")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#virtual_ip").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").show();
		$main("#device_root_ca").attr("class", "tabsetupselected");
		$main("#server_certs").attr("class", "tabsetup");
		$main("#client_certs").attr("class", "tabsetup");
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "device_root_ca.php";
	}

	if (elem.id == "device_root_ca")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#virtual_ip").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").show();
		$main("#device_root_ca").attr("class", "tabsetupselected");
		$main("#server_certs").attr("class", "tabsetup");
		$main("#client_certs").attr("class", "tabsetup");
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "device_root_ca.php";
	}

	if (elem.id == "server_certs")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#virtual_ip").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").show();
		$main("#device_root_ca").attr("class", "tabsetup");
		$main("#server_certs").attr("class", "tabsetupselected");
		$main("#client_certs").attr("class", "tabsetup");
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "server_certs.php";
	}

	if (elem.id == "client_certs")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#virtual_ip").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").show();
		$main("#device_root_ca").attr("class", "tabsetup");
		$main("#server_certs").attr("class", "tabsetup");
		$main("#client_certs").attr("class", "tabsetupselected");
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "client_certs.php";
	}
		
	if (elem.id == "sip_domain_profile")
	{
		$main("#sip_profile").attr("class", "tabsetup");
		$main("#sip_domain_profile").attr("class", "tabsetupselected");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#virtual_ip").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").hide();
		$main("#device_root_ca").attr("class", "tabsetup");
		$main("#server_certs").attr("class", "tabsetup");
		$main("#client_certs").attr("class", "tabsetup");
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "sip_domain_profile.php";
	}

	if (elem.id == "sip_profile")
	{
		$main("#sip_profile").attr("class", "tabsetupselected");
		$main("#sip_domain_profile").attr("class", "tabsetup");
		//$main("#wan_profile").attr("class", "tabsetup");
		$main("#virtual_ip").attr("class", "tabsetup");
		$main("#trunk_config").attr("class", "tabsetup");
		$main("#multiple_table").attr("class", "tabsetup");
		$main("#lcr").attr("class", "tabsetup");
		$main("#roam").attr("class", "tabsetup");
		$main("#sub-tls").hide();
		$main("#device_root_ca").attr("class", "tabsetup");
		$main("#server_certs").attr("class", "tabsetup");
		$main("#client_certs").attr("class", "tabsetup");
		$main("#sbc_gen_settings").attr("class", "tabsetup");

		document.getElementById("container").src = "sip_profile.php";
	}


	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "block");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_auth(elem)
{
	if (elem.id == "auth" || elem.id == "radius")
	{
		$main("#radius").attr("class", "tabsetupselected");

		document.getElementById("container").src = "radius.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "block");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_media(elem)
{
	if (elem.id == "media" || elem.id == "sbc_media_settings")
	{
		$main("#sbc_media_settings").attr("class", "tabsetupselected");
		$main("#fax_profiles").attr("class", "tabsetup");

		document.getElementById("container").src = "media_settings.php";
	}

	if (elem.id == "fax_profiles")
	{
		$main("#sbc_media_settings").attr("class", "tabsetup");
		$main("#fax_profiles").attr("class", "tabsetupselected");

		document.getElementById("container").src = "fax_profiles.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "block");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_status(elem)
{
	if (elem.id == "status" || elem.id == "profile_status")
	{
		/*$main("#system_logs").attr("class", "tabsetupselected");
		$main("#security_alerts").attr("class", "tabsetup");
		$main("#sbc_logs").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");*/
		$main("#profile_status").attr("class", "tabsetupselected");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").hide();
		$main("#sub-reports").hide();

		document.getElementById("container").src = "profile_status.php";
	}

	if (elem.id == "lcr_status")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetupselected");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").hide();
		$main("#sub-reports").hide();

		document.getElementById("container").src = "lcr_status.php";
	}

	if (elem.id == "trunk_status")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetupselected");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").hide();
		$main("#sub-reports").hide();

		document.getElementById("container").src = "trunk_status.php";
	}

	if (elem.id == "roaming_user_status")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetupselected");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").hide();
		$main("#sub-reports").hide();

		document.getElementById("container").src = "roaming_user_status.php";
	}

	if (elem.id == "active_call")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetupselected");
		$main("#sub-logs").hide();
		$main("#sub-reports").hide();

		document.getElementById("container").src = "active_call_info.php";
	}

	if (elem.id == "logs")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabselected");
		$main("#media_logs").attr("class", "subtabsetup");
		$main("#lcr_logs").attr("class", "subtabsetup");
		$main("#system_logs").attr("class", "subtabsetup");
		$main("#security_alerts").attr("class", "subtabsetup");
		$main("#service_logs").attr("class", "subtabsetup");

		document.getElementById("container").src = "signal_logs.php";
	}

	if (elem.id == "sub-logs")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabselected");
		$main("#media_logs").attr("class", "subtabsetup");
		$main("#lcr_logs").attr("class", "subtabsetup");
		$main("#system_logs").attr("class", "subtabsetup");
		$main("#security_alerts").attr("class", "subtabsetup");
		$main("#service_logs").attr("class", "subtabsetup");

		document.getElementById("container").src = "active_call_info.php";
	}

	if (elem.id == "signal_logs")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabselected");
		$main("#media_logs").attr("class", "subtabsetup");
		$main("#lcr_logs").attr("class", "subtabsetup");
		$main("#system_logs").attr("class", "subtabsetup");
		$main("#security_alerts").attr("class", "subtabsetup");
		$main("#service_logs").attr("class", "subtabsetup");

		document.getElementById("container").src = "signal_logs.php";
	}

	if (elem.id == "media_logs")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabsetup");
		$main("#media_logs").attr("class", "subtabselected");
		$main("#lcr_logs").attr("class", "subtabsetup");
		$main("#system_logs").attr("class", "subtabsetup");
		$main("#security_alerts").attr("class", "subtabsetup");
		$main("#service_logs").attr("class", "subtabsetup");

		document.getElementById("container").src = "media_logs.php";
	}

	if (elem.id == "lcr_logs")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabsetup");
		$main("#media_logs").attr("class", "subtabsetup");
		$main("#lcr_logs").attr("class", "subtabselected");
		$main("#system_logs").attr("class", "subtabsetup");
		$main("#security_alerts").attr("class", "subtabsetup");
		$main("#service_logs").attr("class", "subtabsetup");

		document.getElementById("container").src = "lcr_logs.php";
	}

	if (elem.id == "system_logs")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabsetup");
		$main("#media_logs").attr("class", "subtabsetup");
		$main("#lcr_logs").attr("class", "subtabsetup");
		$main("#system_logs").attr("class", "subtabselected");
		$main("#security_alerts").attr("class", "subtabsetup");
		$main("#service_logs").attr("class", "subtabsetup");

		document.getElementById("container").src = "system_logs.php";
	}

	if (elem.id == "security_alerts")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabsetup");
		$main("#media_logs").attr("class", "subtabsetup");
		$main("#lcr_logs").attr("class", "subtabsetup");
		$main("#system_logs").attr("class", "subtabsetup");
		$main("#security_alerts").attr("class", "subtabselected");
		$main("#service_logs").attr("class", "subtabsetup");

		document.getElementById("container").src = "status_alerts.php";
	}
	
	if (elem.id == "service_logs")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").show();
		$main("#sub-reports").hide();
		$main("#signal_logs").attr("class", "subtabsetup");
		$main("#media_logs").attr("class", "subtabsetup");
		$main("#lcr_logs").attr("class", "subtabsetup");
		$main("#system_logs").attr("class", "subtabsetup");
		$main("#security_alerts").attr("class", "subtabsetup");
		$main("#service_logs").attr("class", "subtabselected");

		document.getElementById("container").src = "service_logs.php";
	}

	if (elem.id == "reports")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").hide();
		$main("#sub-reports").show();
		$main("#cdr_reports").attr("class", "subtabselected");

		document.getElementById("container").src = "cdr_reports.php";
	}

	if (elem.id == "sub-reports")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").hide();
		$main("#sub-reports").show();
		$main("#cdr_reports").attr("class", "subtabselected");

		document.getElementById("container").src = "cdr_reports.php";
	}

	if (elem.id == "cdr_reports")
	{
		$main("#profile_status").attr("class", "tabsetup");
		$main("#lcr_status").attr("class", "tabsetup");
		$main("#trunk_status").attr("class", "tabsetup");
		$main("#roaming_user_status").attr("class", "tabsetup");
		$main("#active_call").attr("class", "tabsetup");
		$main("#sub-logs").hide();
		$main("#sub-reports").show();
		$main("#cdr_reports").attr("class", "subtabselected");

		document.getElementById("container").src = "cdr_reports.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "block");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_logs(elem)
{
	if (elem.id == "logs" || elem.id == "signal_logs")
	{
		$main("#signal_logs").attr("class", "tabsetupselected");
		$main("#media_logs").attr("class", "tabsetup");
		$main("#lcr_logs").attr("class", "tabsetup");
		$main("#system_logs").attr("class", "tabsetup");
		$main("#security_alerts").attr("class", "tabsetup");
		/*$main("#sbc_logs").attr("class", "tabsetup");*/

		document.getElementById("container").src = "signal_logs.php";
	}

	if (elem.id == "media_logs")
	{
		$main("#signal_logs").attr("class", "tabsetup");
		$main("#media_logs").attr("class", "tabsetupselected");
		$main("#lcr_logs").attr("class", "tabsetup");
		$main("#system_logs").attr("class", "tabsetup");
		$main("#security_alerts").attr("class", "tabsetup");
		/*$main("#sbc_logs").attr("class", "tabsetup");*/

		document.getElementById("container").src = "media_logs.php";
	}

	if (elem.id == "lcr_logs")
	{
		$main("#signal_logs").attr("class", "tabsetup");
		$main("#media_logs").attr("class", "tabsetup");
		$main("#lcr_logs").attr("class", "tabsetupselected");
		$main("#system_logs").attr("class", "tabsetup");
		$main("#security_alerts").attr("class", "tabsetup");
		/*$main("#sbc_logs").attr("class", "tabsetup");*/

		document.getElementById("container").src = "lcr_logs.php";
	}

	if (elem.id == "system_logs")
	{
		$main("#signal_logs").attr("class", "tabsetup");
		$main("#media_logs").attr("class", "tabsetup");
		$main("#lcr_logs").attr("class", "tabsetup");
		$main("#system_logs").attr("class", "tabsetupselected");
		$main("#security_alerts").attr("class", "tabsetup");
		/*$main("#sbc_logs").attr("class", "tabsetup");*/

		document.getElementById("container").src = "system_logs.php";
	}

	if (elem.id == "security_alerts")
	{
		$main("#signal_logs").attr("class", "tabsetup");
		$main("#media_logs").attr("class", "tabsetup");
		$main("#lcr_logs").attr("class", "tabsetup");
		$main("#system_logs").attr("class", "tabsetup");
		$main("#security_alerts").attr("class", "tabsetupselected");
		/*$main("#sbc_logs").attr("class", "tabsetup");*/

		document.getElementById("container").src = "status_alerts.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "block");
	$main("#reports-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
}

function menu_reports(elem)
{
	if (elem.id == "reports" || elem.id == "cdr_reports")
	{
		$main("#cdr_reports").attr("class", "tabsetupselected");

		document.getElementById("container").src = "cdr_reports.php";
	}
}

function menu_tools(elem)
{
	if (elem.id == "tools" || elem.id == "administration")
	{
		$main("#administration").attr("class", "tabsetupselected");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#sub-diagnostics").hide();
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetup");		

		document.getElementById("container").src = "administration.php";
	}

	if (elem.id == "main-diagnostics")
	{
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#sub-diagnostics").show();
		$main("#diagnostics").attr("class", "subtabselected");
		$main("#ping").attr("class", "subtabsetup");
		$main("#tracert").attr("class", "subtabsetup");
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetup");	
		$main("#packet_capture").attr("class", "subtabsetup");	

		document.getElementById("container").src = "diagnostics.php";
	}

	if (elem.id == "diagnostics")
	{
		$main("#sub-diagnostics").show();
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#diagnostics").attr("class", "subtabselected");
		$main("#ping").attr("class", "subtabsetup");
		$main("#tracert").attr("class", "subtabsetup");
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetup");	
		$main("#packet_capture").attr("class", "subtabsetup");	

		document.getElementById("container").src = "diagnostics.php";
	}

	if (elem.id == "ping")
	{
		$main("#sub-diagnostics").show();
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#diagnostics").attr("class", "subtabsetup");
		$main("#ping").attr("class", "subtabselected");
		$main("#tracert").attr("class", "subtabsetup");
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetup");		
		$main("#packet_capture").attr("class", "subtabsetup");

		document.getElementById("container").src = "ping.php";
	}

	if (elem.id == "tracert")
	{
		$main("#sub-diagnostics").show();
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#diagnostics").attr("class", "subtabsetup");
		$main("#ping").attr("class", "subtabsetup");
		$main("#tracert").attr("class", "subtabselected");
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetup");		
		$main("#packet_capture").attr("class", "subtabsetup");

		document.getElementById("container").src = "traceroute.php";
	}
	
	if (elem.id == "packet_capture")
	{
		$main("#sub-diagnostics").show();
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#diagnostics").attr("class", "subtabsetup");
		$main("#ping").attr("class", "subtabsetup");
		$main("#tracert").attr("class", "subtabsetup");
		$main("#packet_capture").attr("class", "subtabselected");
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetup");

		document.getElementById("container").src = "packetcap.php";
	}

	if (elem.id == "trouble_shooting")
	{
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#sub-diagnostics").hide();
		$main("#trouble_shooting").attr("class", "tabsetupselected");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetup");		

		document.getElementById("container").src = "troubleshooting.php";
	}

	if (elem.id == "logs_archive")
	{
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#sub-diagnostics").hide();
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetupselected");
		$main("#plugins").attr("class", "tabsetup");
		document.getElementById("container").src = "logs_archive.php";
	}

	if (elem.id == "plugins")
	{
		$main("#administration").attr("class", "tabsetup");
		$main("#main-diagnostics").attr("class", "tabsetup");
		$main("#sub-diagnostics").hide();
		$main("#trouble_shooting").attr("class", "tabsetup");
		$main("#logs_archive").attr("class", "tabsetup");
		$main("#plugins").attr("class", "tabsetupselected");
		document.getElementById("container").src = "plugins.php";
	}
	
	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "block");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_security(elem)
{
	if (elem.id == "sid" || elem.id == "sip")
	{
		$main("#sub-sip").show();
		$main("#sub-firewall").hide();
		$main("#sub-vpn").hide();
		$main("#dpi_signatures").attr("class", "subtabselected");
		$main("#sip_sec_settings").attr("class", "subtabsetup");
		$main("#sig_update").attr("class", "subtabsetup");

		document.getElementById("container").src = "dpi_signatures.php";
	}

	if (elem.id == "dpi_signatures")
	{
		$main("#sub-sip").show();
		$main("#sub-firewall").hide();
		$main("#sub-vpn").hide();
		$main("#dpi_signatures").attr("class", "subtabselected");
		$main("#sip_sec_settings").attr("class", "subtabsetup");
		$main("#sig_update").attr("class", "subtabsetup");

		document.getElementById("container").src = "dpi_signatures.php";
	}

	if (elem.id == "sip_sec_settings")
	{
		$main("#sub-sip").show();
		$main("#sub-firewall").hide();
		$main("#sub-vpn").hide();
		$main("#dpi_signatures").attr("class", "subtabsetup");
		$main("#sip_sec_settings").attr("class", "subtabselected");
		$main("#sig_update").attr("class", "subtabsetup");

		document.getElementById("container").src = "sip_sec_settings.php";
	}

	if (elem.id == "sig_update")
	{
		$main("#sub-sip").show();
		$main("#sub-firewall").hide();
		$main("#sub-vpn").hide();
		$main("#dpi_signatures").attr("class", "subtabsetup");
		$main("#sip_sec_settings").attr("class", "subtabsetup");
		$main("#sig_update").attr("class", "subtabselected");

		document.getElementById("container").src = "sig_update.php";
	}

	if (elem.id == "firewall")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabselected");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#network_qos").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "firewall.php";
	}

	if (elem.id == "sub-firewall")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabselected");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#network_qos").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "firewall.php";
	}

	if (elem.id == "firewall_conf")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabselected");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#network_qos").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "firewall.php";
	}

	if (elem.id == "dos_control")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabsetup");
		$main("#dos_control").attr("class", "subtabselected");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "dos_control.php";
	}

	if (elem.id == "nat_rules")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabsetup");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabselected");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "port_forwarding.php";
	}

	if (elem.id == "whitelist_conf")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabsetup");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabselected");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "whitelist.php";
	}

	if (elem.id == "blacklist_conf")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabsetup");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabselected");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "blacklist.php";
	}

	if (elem.id == "dyn_blacklist_conf")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabsetup");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabselected");
		$main("#geo_ip_filter").attr("class", "subtabsetup");

		document.getElementById("container").src = "dynamic_blacklist.php";
	}

	if (elem.id == "geo_ip_filter")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").show();
		$main("#sub-vpn").hide();
		$main("#firewall_conf").attr("class", "subtabsetup");
		$main("#dos_control").attr("class", "subtabsetup");
		$main("#nat_rules").attr("class", "subtabsetup");
		$main("#whitelist_conf").attr("class", "subtabsetup");
		$main("#blacklist_conf").attr("class", "subtabsetup");
		$main("#dyn_blacklist_conf").attr("class", "subtabsetup");
		$main("#geo_ip_filter").attr("class", "subtabselected");

		document.getElementById("container").src = "geo_ip_filter.php";
	}

	if (elem.id == "vpn")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").hide();
		$main("#sub-vpn").show();
		$main("#ipsec_vpn").attr("class", "subtabselected");

		document.getElementById("container").src = "ipsec_vpn.php";
	}

	if (elem.id == "sub-vpn")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").hide();
		$main("#sub-vpn").show();
		$main("#ipsec_vpn").attr("class", "subtabselected");

		document.getElementById("container").src = "ipsec_vpn.php";
	}

	if (elem.id == "ipsec_vpn")
	{
		$main("#sub-sip").hide();
		$main("#sub-firewall").hide();
		$main("#sub-vpn").show();
		$main("#ipsec_vpn").attr("class", "subtabselected");

		document.getElementById("container").src = "ipsec_vpn.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "block");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_system(elem)
{
	if (elem.id == "system" || elem.id == "time_settings")
	{
		$main("#time_settings").attr("class", "tabsetupselected");
		$main("#logging").attr("class", "tabsetup");
		$main("#firmware_upgrade").attr("class", "tabsetup");
		$main("#email").attr("class", "tabsetup");

		document.getElementById("container").src = "time_settings.php";
	}

	if (elem.id == "logging")
	{
		$main("#time_settings").attr("class", "tabsetup");
		$main("#logging").attr("class", "tabsetupselected");
		$main("#firmware_upgrade").attr("class", "tabsetup");
		$main("#email").attr("class", "tabsetup");

		document.getElementById("container").src = "logging.php";
	}

	if (elem.id == "firmware_upgrade")
	{
		$main("#time_settings").attr("class", "tabsetup");
		$main("#logging").attr("class", "tabsetup");
		$main("#firmware_upgrade").attr("class", "tabsetupselected");
		$main("#email").attr("class", "tabsetup");

		document.getElementById("container").src = "firmware.php";
	}

	if (elem.id == "email")
	{
		$main("#time_settings").attr("class", "tabsetup");
		$main("#logging").attr("class", "tabsetup");
		$main("#firmware_upgrade").attr("class", "tabsetup");
		$main("#email").attr("class", "tabsetupselected");

		document.getElementById("container").src = "email.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "block");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "none");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "none");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}

function menu_vpn(elem)
{
	if (elem.id == "vpn" || elem.id == "ipsec_vpn")
	{
		$main("#ipsec_vpn").attr("class", "tabsetupselected");

		document.getElementById("container").src = "ipsec_vpn.php";
	}

	$main("#dashboard-arrow").css("display", "none");
	$main("#settings-arrow").css("display", "none");
	$main("#sbc-arrow").css("display", "none");
	$main("#media-arrow").css("display", "none");
	$main("#auth-arrow").css("display", "block");
	$main("#security-arrow").css("display", "none");
	$main("#status-arrow").css("display", "none");
	$main("#tools-arrow").css("display", "none");
	$main("#sid-arrow").css("display", "none");
	$main("#system-arrow").css("display", "none");
	$main("#vpn-arrow").css("display", "block");
	$main("#logs-arrow").css("display", "none");
	$main("#reports-arrow").css("display", "none");
}
function presence_settings(elem)
{
	if (elem.id == "psettings" || elem.id == "subscribers")
	{
		$main("#subscribers").show();
		$main("#subscribers").attr("class", "subtabselected ");
		$main("#events").attr("class", "subtabsetup");		

		document.getElementById("container").src = "subscribers.php";
	}

	if (elem.id == "subscribers")
	{
		$main("#subscribers").attr("class", "subtabselected");
		$main("#events").attr("class", "subtabsetup");	
		
		document.getElementById("container").src = "subscribers.php";
	}
		
	if (elem.id == "events")
	{
		$main("#subscribers").attr("class", "subtabsetup");
		$main("#events").attr("class", "subtabselected");	
		
		document.getElementById("container").src = "events_settings.php";
	}

	$main("#psettings").css("display", "block");
	$main("#subscribers").css("display", "block");
	$main("#events").css("display", "block");
}

function load_balance(elem) {

	if(elem.id == "loadbalance" || elem.id == "resourceprofile") {
		$main("#resourceprofile").show();
		$main("#resourceprofile").attr("class", "subtabselected ");
		$main("#groupprofile").attr("class", "subtabsetup");
		$main("#loadbalrule").attr("class", "subtabsetup");				

		document.getElementById("container").src = "resource_profile.php";
	}

	if (elem.id == "resourceprofile")
	{
		$main("#resourceprofile").attr("class", "subtabselected");
		$main("#groupprofile").attr("class", "subtabsetup");
		$main("#loadbalrule").attr("class", "subtabsetup");				
				
		document.getElementById("container").src = "resource_profile.php";
	}

	if (elem.id == "groupprofile")
	{
		$main("#resourceprofile").attr("class", "subtabsetup");
		$main("#groupprofile").attr("class", "subtabselected");
		$main("#loadbalrule").attr("class", "subtabsetup");				
		document.getElementById("container").src = "group_profile.php";
	}
	
	if (elem.id == "loadbalrule")
	{
		$main("#resourceprofile").attr("class", "subtabsetup");
		$main("#groupprofile").attr("class", "subtabsetup");
		$main("#loadbalrule").attr("class", "subtabselected");
				
		document.getElementById("container").src = "lbrule_profile.php";
	}

	$main("#loadbalance").css("display", "block");
	$main("#resourceprofile").css("display", "block");
	$main("#groupprofile").css("display", "block");
	$main("#loadbalrule").css("display", "block");
}

