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

(function($) {
	$.fn.validationEngineLanguage = function() {
	};
	$.validationEngineLanguage = {
		newLang : function() {
			$.validationEngineLanguage.allRules = {
				"required" : {// Add your regex rules here, you can take telephone as an example
					"regex" : "none",
					"alertText" : "* This field is required",
					"alertTextCheckboxMultiple" : "* Please select an option",
					"alertTextCheckboxe" : "* This checkbox is required",
					"alertTextDateRange" : "* Both date range fields are required"
				},

				// customized rule addition
				"nameSpChar" : {
					"regex" : /^[a-zA-Z0-9`!#\$%^&*\(\)\-_\+=\{\}\[|\'\?/<,\.\\]+$/,
					"alertText" : "* Special characters ; : @ \" > ~ ] are not allowed"
				},

				"proxySpChar" : {
					"regex" : /^[a-zA-Z0-9\-\._~!\$\'\(\)*\+,=]+$/,
					"alertText" : "* Allowed characters are A-Z a-z 0-9 - . _ ~ ! $ ' ( ) * + , ="
				},

				"pswdSpChar" : {
					"regex" : /^[a-zA-Z0-9`!#\$%^&*\(\)\-_\+=\{\}\[|\'\?/</>,\.\\"@]+$/,
					"alertText" : "* Special characters ; : ~ ] are not allowed"
				},

				"customCallid" : {
					"regex" : /^[\+]?[0-9]+$/,
					"alertText" : "* Numbers Only(<b>Note:</b> Only + is allowed at the first place)"
				},

				"customDateFormat" : {
					"regex" : /^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/,
					"alertText" : "* Date Format is incorrect"
				},

				"requiredInFunction" : {
					"func" : function(field, rules, i, options) {
						return (field.val() == "test") ? true : false;
					},
					"alertText" : "* Field must equal test"
				},
				"dateRange" : {
					"regex" : "none",
					"alertText" : "* Invalid ",
					"alertText2" : "Date Range"
				},

				"timeRange" : {
					"regex" : "none",
					"alertText" : "* Invalid ",
					"alertText2" : "Time Range"
				},
				"dateTimeRange" : {
					"regex" : "none",
					"alertText" : "* Invalid ",
					"alertText2" : "Date Time Range"
				},
				"minSize" : {
					"regex" : "none",
					"alertText" : "* Minimum ",
					"alertText2" : " characters required"
				},
				"maxSize" : {
					"regex" : "none",
					"alertText" : "* Maximum ",
					"alertText2" : " characters allowed"
				},
				"LEN_FIX_1007" : {
					"regex" : "none",
					"alertText" : "* Maximum ",
					"alertText2" : " characters allowed"
				},
				"groupRequired" : {
					"regex" : "none",
					"alertText" : "* You must fill one of the following fields"
				},
				"min" : {
					"regex" : "none",
					"alertText" : "* Minimum value is "
				},
				"max" : {
					"regex" : "none",
					"alertText" : "* Maximum value is "
				},
				"checkExists" : {
					"regex" : "none",
					"alertText" : "* Keyname already in use"
				},
				"past" : {
					"regex" : "none",
					"alertText" : "* Date prior to "
				},
				"future" : {
					"regex" : "none",
					"alertText" : "* Date past "
				},
				"maxCheckbox" : {
					"regex" : "none",
					"alertText" : "* Maximum ",
					"alertText2" : " options allowed"
				},
				"minCheckbox" : {
					"regex" : "none",
					"alertText" : "* Please select ",
					"alertText2" : " options"
				},
				"equals" : {
					"regex" : "none",
					"alertText" : "* Fields do not match"
				},
				"creditCard" : {
					"regex" : "none",
					"alertText" : "* Invalid credit card number"
				},
				"phone" : {
					// credit: jquery.h5validate.js / orefalo
					"regex" : /^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/,
					"alertText" : "* Invalid phone number"
				},
				"email" : {
					// HTML5 compatible email regex ( http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#    e-mail-state-%28type=email%29 )
					"regex" : /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
					"alertText" : "* Invalid email address"
				},
				"integer" : {
					"func" : function (field)
					{
						var pattern = new RegExp(/^[\-\+]?\d+$/);
						var match = pattern.exec(field.val());
						if ( field.val() == "none" )
							return true;
						if ( match == null )
						{
							return false;
						}
						return true;
					},
				"alertText" : " *Not a valid integer"
				},

				"serverCerts" : {
					"func" : function (field)
					{
						if ( field.val() == "None" )
						{
							return false;
						}
						return true;
					},
				"alertText" : " *Invalid! Please select a valid certificate from the list."
				},

				"hostname" : {
					"func" : function (field)
					{
						var host = new RegExp(/^(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
						var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
						var match_host = host.exec(field.val());
						var match_ipaddr = ipaddr.exec(field.val());
						if ( match_host == null &&  match_ipaddr == null )
						{
							return false;
						}
						return true;
					},
				"alertText" : " * Invalid ! Enter either a valid domain name or IP address."
				},

				"number" : {
					// Number, including positive, negative, and floating decimal. credit: orefalo
					"regex" : /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,
					"alertText" : "* Invalid floating decimal number"
				},
				"date" : {
					//	Check if date is valid by leap year
					"func" : function(field) {
						var pattern = new RegExp(/^(\d{4})[\/\-\.](0?[1-9]|1[012])[\/\-\.](0?[1-9]|[12][0-9]|3[01])$/);
						var match = pattern.exec(field.val());
						if (match == null)
							return false;

						var year = match[1];
						var month = match[2] * 1;
						var day = match[3] * 1;
						var date = new Date(year, month - 1, day);
						// because months starts from 0.

						return (date.getFullYear() == year && date.getMonth() == (month - 1) && date.getDate() == day);
					},
					"alertText" : "* Invalid date, must be in YYYY-MM-DD format"
				},
				"ipv4" : {
					"regex" : /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
					"alertText" : "* Invalid IP address"
				},
				"url" : {
					"regex" : /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
					"alertText" : "* Invalid URL"
				},
				"onlyNumberSp" : {
					"regex" : /^[0-9\ ]+$/,
					"alertText" : "* Numbers only"
				},
				"onlyNumber" : {
					"regex" : /^[0-9]+$/,
					"alertText" : "* Numbers only"
				},
				"onlyLetterSp" : {
					"regex" : /^[a-zA-Z\ \']+$/,
					"alertText" : "* Letters only"
				},
				"onlyLetter" : {
					"regex" : /^[a-zA-Z']+$/,
					"alertText" : "* Letters only"
				},
				"comments" : {
					"regex" : /\S/,
					"alertText" : "* Enter valid comments"
				},
				"icmpPorts" : {
					"func" : function(field) {
						var pattern = new RegExp(/^\d+(,\d+)*$/);
						var match = pattern.exec(field.val());
						if (match == null)
							return false;
						var portVal = field.val().split(',');
						if (parseInt(parseFloat(portVal[0])) < 0)
						{
							return false;
						}
						for (var i=0; i<portVal.length; i++)
						{
							if (parseInt(parseFloat(portVal[i])) > 65535)
							{
							   return false;
							}
						}
						return true;
					},
					"alertText" : "* Invalid! Enter a valid Port Number (0-65535)"
				},

				"IP_DOMAIN_PORT" : {
					"func" : function (field)
					{
						var getVal = field.val();
						if ( getVal.indexOf(":") != -1 )
						{
							var split_field = field.val().split(':');
							if ( split_field.length < 2 )
							{
								return false;
							}
							var ipaddressVal = split_field[0];
							var port = split_field[1];
						}

						var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
						var match_ipaddr = ipaddr.exec(ipaddressVal);

						var host = new RegExp(/^(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
						var match_host = host.exec(getVal);
						if ( match_ipaddr == null && match_host == null )
						{
							return false;
						}
						var num = new RegExp(/^[0-9\ ]+$/);
						var match_port = num.exec(port);

						if(match_port == null) {
							return false;
						}

						if ( parseInt(parseFloat(port)) < 1 || parseInt(parseFloat(port)) > 65535  )
						{
							return false;
						}
						return true;
					},
				"alertText" : " * Invalid ! Enter either a valid IP Address:Port (DOMAIN/IPADDRESS:PORT)."
				},

				"IP_OR_DOMAIN_DOT" : {
					"func" : function (field)
					{
						var field_val = field.val();
						var ip_or_domain_pattern = new RegExp("^[^._-][a-zA-Z0-9._-]+$");
						if (ip_or_domain_pattern.exec(field_val) == null) {
            	return false;
						}
            return true;
					},
				"alertText" : " * Invalid ! Enter either a valid IP Address or Domain Name with or without dot."
				},

				"USER_AT_OR_IP_DOMAIN" : {
                                        "func" : function (field)
                                        {
                                                var main_val=field.val();
                                                var getVal = field.val();
                                                if (main_val.indexOf("@") != -1 ){
                                                         var split_field = field.val().split('@');
                                                        var user_id = split_field[0];
                                                        getVal = split_field[1];

                                                        var uservalid= new RegExp(/^[0-9a-zA-Z\ \']+$/);

                                                        if(uservalid.exec(user_id) == null){
                                                                return false;
                                                        }
                                                }

                                                var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
                                                var match_ipaddr = ipaddr.exec(main_val);

                                                var host = new RegExp(/^(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
						                                                var match_host = host.exec(getVal);
                                                if ( match_ipaddr == null && match_host == null )
                                                {
                                                        return false;
                                                }
                                                return true;
                                        },
                                "alertText" : " * Invalid ! Enter either a valid IP Address (DOMAIN/IPADDRESS or user@domain/ipaddress) User should be alphanumeric."
                                },

				"USER_AT_OR_IP_DOMAIN_PORT" : {
                                        "func" : function (field)
                                        {
						var main_val=field.val();
                                                var getVal = field.val();
						if (main_val.indexOf("@") != -1 ){
							 var split_field = field.val().split('@'); 
							var user_id = split_field[0];
							getVal = split_field[1];
							
							var uservalid= new RegExp(/^[0-9a-zA-Z\ \']+$/);
							
							if(uservalid.exec(user_id) == null){
								return false;
							}			
						}
                                                if ( getVal.indexOf(":") != -1 )
                                                {
                                                        var split_field = getVal.split(':');
                                                        if ( split_field.length < 2 )
                                                        {
                                                                return false;
                                                        }
                                                        var ipaddressVal = split_field[0];
                                                        var port = split_field[1];
                                                }

                                                var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
                                                var match_ipaddr = ipaddr.exec(ipaddressVal);

                                                var host = new RegExp(/^(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
                                                var match_host = host.exec(getVal);
                                                if ( match_ipaddr == null && match_host == null )
                                                {
                                                        return false;
                                                }
                                                if ( parseInt(parseFloat(port)) < 1 || parseInt(parseFloat(port)) > 65535 || port == "" )
                                                {
                                                        return false;
                                                }
                                                return true;
                                        },
                                "alertText" : " * Invalid ! Enter either a valid IP Address:Port (DOMAIN/IPADDRESS:PORT or user@domain/ipaddress:port ) User should be alphanumeric."
                                },

				"IP_DOMAIN" : {
					"func" : function (field)
					{
						var ipaddress_domain = field.val();

						var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/);
						var match_ipaddr = ipaddr.exec(ipaddress_domain);

						var host = new RegExp(/^(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
						var match_host = host.exec(ipaddress_domain);

						if ( match_ipaddr == null && match_host == null )
						{
							return false;
						}
						return true;
					},
				"alertText" : " * Invalid ! Enter either a valid IP Address or Domain Name."
				},

				"DOMAIN" : {
					"func" : function (field)
					{
						var domain_val = field.val();

						var host = new RegExp(/^(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
						var match_host = host.exec(domain_val);

						if ( match_host == null )
						{
							return false;
						}
						return true;
					},
				"alertText" : " * Invalid ! Enter either a valid IP Address or Domain Name."
				},

				"selectProfiles" : {
					"func" : function (field)
					{
						var profile_val = field.val();

						if ( profile_val == "--No Profile--" )
						{
							return false;
						}
						return true;
					},
				"alertText" : " * Invalid ! There are no profiles to be selected."
				},

				"selectInterfaces" : {
					"func" : function (field)
					{
						var profile_val = field.val();

						if ( profile_val == "--No Interfaces--" )
						{
							return false;
						}
						return true;
					},
				"alertText" : " * Invalid ! There are no interfaces to be selected."
				},

				"IP_PORT" : {
					"func" : function (field)
					{
						var split_field = field.val().split(':');
						if ( split_field.length < 2 )
						{
							return false;
						}
						var ipaddress = split_field[0];
						var port = split_field[1];

						var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
						var match_ipaddr = ipaddr.exec(ipaddress);

						if ( match_ipaddr == null )
						{
							return false;
						}
						if ( parseInt(parseFloat(port)) < 1 || parseInt(parseFloat(port)) > 65535 )
						{
							return false;
						}
						return true;
					},
				"alertText" : " * Invalid ! Enter a valid IP Address:Port (IPADDRESS:PORT)."
				},
				"IP_PORT_ANY" : {
					"func" : function (field)
					{
						var delim_pos = field.val().indexOf(":");
						if ( delim_pos == -1 )
						{
							var any_check = field.val().match(/^any|ANY$/g);
							if ( any_check == null )
							{
								return false;
							}
						}
						else if ( delim_pos != -1 )
						{
							var split_field = field.val().split(':');
							if ( split_field.length < 2 )
							{
								return false;
							}
							var ipaddress = split_field[0];
							var port = split_field[1];

							var ipaddr  = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-4]))$/);
							var match_ipaddr = ipaddr.exec(ipaddress);

							if ( match_ipaddr == null )
							{
								return false;
							}
							if( port.toUpperCase() === "ANY" ) {
								// port matching any
							} else  if ( isNaN(parseInt(parseFloat(port))) || parseInt(parseFloat(port)) < 1 || parseInt(parseFloat(port)) > 65535 ) {
								return false;
							}
						}
						return true;
					},
				"alertText" : " * Invalid ! Enter either a valid IP Address:Port (IPADDRESS:PORT) or ANY/any ."
				},

				"macaddr" : {
					"func" : function(field) {
						var pattern = new RegExp(/^[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}$/);
						var match = pattern.exec(field.val());
						if (match == null)
						{
							return false;
						}
						else
						{
							return true;
						}
					},
					"alertText" : "* Invalid! Enter a valid MAC Address"
				},

				"commaSepPorts" : {
					"func" : function(field) {
						var pattern = new RegExp(/^\d+(,\d+)*$/);
						var match = pattern.exec(field.val());
						if (match == null)
							return false;
						var portVal = field.val().split(',');
						if (parseInt(parseFloat(portVal[0])) < 1)
						{
							return false;
						}
						for (var i=0; i<portVal.length; i++)
						{
							if (parseInt(parseFloat(portVal[i])) > 65535)
							{
							   return false;
							}
						}
						return true;
					},
					"alertText" : "* Invalid! Enter a valid Port Number (1-65535)"
				},
				"icmpportsRange" : {
					"func" : function(field) {
						var pattern = new RegExp(/^\d+(-\d+)$/);
						var match = pattern.exec(field.val());
						if (match == null)
							return false;
						var portVal = field.val().split('-');
						if (parseInt(parseFloat(portVal[0])) < 0)
						{
							return false;
						}
						if (parseInt(parseFloat(portVal[0])) >=  parseInt(parseFloat(portVal[1])))
						{
							return false;
						}
						for (var i=0; i<portVal.length; i++)
						{
							if (parseInt(parseFloat(portVal[i])) > 65535)
							{
							   return false;
							}
						}
						return true;
					},
					"alertText" : "* Invalid! Enter a valid Port Range (0-65535)"
				},
				"portsRange" : {
					"func" : function(field) {
						var pattern = new RegExp(/^\d+(-\d+)$/);
						var match = pattern.exec(field.val());
						if (match == null)
							return false;
						var portVal = field.val().split('-');
						if (parseInt(parseFloat(portVal[0])) < 1)
						{
							return false;
						}
						if (parseInt(parseFloat(portVal[0])) >=  parseInt(parseFloat(portVal[1])))
						{
							return false;
						}
						for (var i=0; i<portVal.length; i++)
						{
							if (parseInt(parseFloat(portVal[i])) > 65535)
							{
							   return false;
							}
						}
						return true;
					},
					"alertText" : "* Invalid! Enter a valid Port Range (1-65535)"
				},
				"ipRange": {
					"func" : function(field) {
						var pattern = new RegExp(/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))(-((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5])))$/);
						var match = pattern.exec(field.val());
						if (match == null)
							return false;
						var ipVal = field.val();
						var ipSplit = ipVal.split('-');
						var StartAddr = ipSplit[0];
						SAddr = StartAddr.split('.');
						var EndAddr = ipSplit[1];
						EAddr = EndAddr.split('.');
						if( SAddr.length !=  EAddr.length )
						{
							return false;
						}
						var flag =  false;
						for( var k = 0; k < SAddr.length; k++)
						{
							var POSAddr = parseInt(SAddr[0]);
							var PTSAddr = parseInt(SAddr[1]);
							var PTHSAddr = parseInt(SAddr[2]);
							var PFSAddr = parseInt(SAddr[3]);

							var POEAddr = parseInt(EAddr[0]);
							var PTEAddr = parseInt(EAddr[1]);
							var PTHEAddr = parseInt(EAddr[2]);
							var PFEAddr = parseInt(EAddr[3]);

							var ParSAddr = new Array(POSAddr,PTSAddr,PTHSAddr,PFSAddr);
							var ParEAddr = new Array(POEAddr,PTEAddr,PTHEAddr,PFEAddr);
							for(var j=0; j<SAddr.length ; j++)
							{
								if(ParSAddr[j] >= ParEAddr[j])
								{
									flag = false;
								}
								else
								{
									flag = true;
								}
							}
						}
						if(flag == false)
						{
							return false;
						}
						return true;
					},
					"alertText" : "* Invalid Please enter a valid IP Range"
				},

				"netmask":{
					"regex" : /^((128|192|224|240|248|252|254|255)\.0\.0\.0)|(255\.(((0|128|192|224|240|248|252|254|255)\.0\.0)|(255\.(((0|128|192|224|240|248|252|254|255)\.0)|255\.(0|128|192|224|240|248|252|254|255)))))$/,
					"alertText" : "* Not a valid NetMask value"
				},
				"ipwithCIDR":{
					"regex" : /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/([1-2]\d|3[0-2]|\d))$/,
					"alertText" : "* Invalid! Enter an IP Address with CIDR value"
				},

				"onlyLetterNumber" : {
					"regex" : /^[0-9a-zA-Z\ \']+$/,
					"alertText" : "* Invalid! Enter a valid name (5-32 char's)"
				},

                                "DID" : {
                                        "regex" : /^\+?[0-9]+$/,
                                        "alertText" : "* Invalid! Enter a valid DID (allowed: + followed number, ., -, _)"
                                },

                                "DIDorUSER" : {
                                        "regex" : /^\+?[0-9a-zA-Z\.\-\_]+$/,
                                        "alertText" : "* Invalid! Enter a valid DID or USER (allowed: + followed, alphanumeric, ., -, _)"
                                },

                                "AUTHUSER" : {
                                        "regex" : /^\+?[0-9a-zA-Z\.\-\_\@]+$/,
                                        "alertText" : "* Invalid! Enter a valid AUTHUSER (allowed: + followed, alphanumeric, ., -, _, @)"
                                },

                                "onlyLetterNumberDotHyphenUnderScore" : {
                                        "regex" : /^[0-9a-zA-Z\.\-\_]+$/,
                                        "alertText" : "* Invalid! Enter a valid name (allowed: alphanumeric, ., -, _)"
                                },

				"alphaNumeric" : {
					"regex" : /^[0-9a-zA-Z\']+$/,
					"alertText" : "* Invalid! Enter a valid name (5-32 char's)"
				},
				"rulename" : {
					"regex" : /^[a-zA-Z0-9\[\]\_-]*$/,
					"alertText" : "* Invalid! Enter a valid name (5-32 char's)"
				},
				"nwrulename" : {
					"regex" : /^[a-zA-Z0-9\s\[\]\_-]*$/,
					"alertText" : "* Invalid! Enter a valid name (5-16 char's)"
				},
				"userAgent" : {
					"regex" : /^[a-zA-Z0-9\s\[\]\\@\_-]*$/,
					"alertText" : "* Invalid! Enter a valid name (5-16 char's)"
				},
				// --- CUSTOM RULES -- Those are specific to the demos, they can be removed or changed to your likings
				"ajaxUserCall" : {
					"url" : "ajaxValidateFieldUser",
					// you may want to pass extra data on the ajax call
					"extraData" : "name=eric",
					"alertText" : "* This user is already taken",
					"alertTextLoad" : "* Validating, please wait"
				},
				"ajaxUserCallPhp" : {
					"url" : "phpajax/ajaxValidateFieldUser.php",
					// you may want to pass extra data on the ajax call
					"extraData" : "name=eric",
					// if you provide an "alertTextOk", it will show as a green prompt when the field validates
					"alertTextOk" : "* This username is available",
					"alertText" : "* This user is already taken",
					"alertTextLoad" : "* Validating, please wait"
				},
				"ajaxNameCall" : {
					// remote json service location
					"url" : "ajaxValidateFieldName",
					// error
					"alertText" : "* This name is already taken",
					// if you provide an "alertTextOk", it will show as a green prompt when the field validates
					"alertTextOk" : "* This name is available",
					// speaks by itself
					"alertTextLoad" : "* Validating, please wait"
				},
				"ajaxNameCallPhp" : {
					// remote json service location
					"url" : "phpajax/ajaxValidateFieldName.php",
					// error
					"alertText" : "* This name is already taken",
					// speaks by itself
					"alertTextLoad" : "* Validating, please wait"
				},
				"validate2fields" : {
					"alertText" : "* Please input HELLO"
				},
				//tls warning:homegrown not fielded
				"dateFormat" : {
					"regex" : /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/,
					"alertText" : "* Invalid Date"
				},
				//tls warning:homegrown not fielded
				"dateTimeFormat" : {
					"regex" : /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/,
					"alertText" : "* Invalid Date or Date Format",
					"alertText2" : "Expected Format: ",
					"alertText3" : "mm/dd/yyyy hh:mm:ss AM|PM or ",
					"alertText4" : "yyyy-mm-dd hh:mm:ss AM|PM"
				}
			};

		}
	};

	$.validationEngineLanguage.newLang();

})(jQuery);
