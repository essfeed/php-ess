<?php
/**
 * Universal ESS EventFeed Entry Writer
 * FeedValidator class - contain static method to validate specific ESS Fields
 * 
 * The feed can be validate in : http://essfeed.org/index.php/ESS:Validator
 * 
 *
 * @package FeedValidator
 * @author  Brice Pissard
 * @link	http://essfeed.org/index.php/ESS:Validator
 */
final class FeedValidator
{
	function __construct(){}
	

	/**
	 * Control the content of the data is null
	 * 
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static function isNull( $str )
	{
		$str = trim( str_replace( array( '\n', '\r', '	', ' ' ), '', $str ) );
		
		return ( $str == '' || $str == null )? true : false;
	}
	
	/**
	 * Control the correct syntax of the date in UTC format (ISO 8601)
	 * to check if the string formated UTC date is valid (e.g. 2013-10-31T15:30:59Z)
	 * 
	 * @access	public
	 * @param	String	stringDate date string content (e.g. 2013-10-31T15:30:59Z)
	 * @return	Boolean
	 */
	public static function isValidDate( $stringDate )
	{
		$ereg = "/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|(\+|-)\d{2}(:?\d{2})?)$/";
		$matcher = preg_match( $ereg, $stringDate );
		
		return ( $matcher == 1 )? true : false;			
	}
	
	
	/**
	 * Control if the URL is correctly formated (RFC 3986)
	 * An IP can also be submited as a URL.
	 * 
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static function isValidURL( $url )
	{
		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		
		return ( @eregi( $urlregex, $url ) == true && strlen( $url ) > 10 )? true : FeedValidator::isValidIP( $url );  
	}
	
	public static function isValidIP( $ip )
	{
		if ( !eregi("^[0-9]+(\.[0-9]+)+(\.[0-9]+)+(\.[0-9]+)$", $ip ) )
		{
			return false;
		}
		else
		{
			$Array = explode(".", $ip);
			
			if ( $Array[0] > 255) { return false; }
			if ( $Array[1] > 255) { return false; }
			if ( $Array[2] > 255) {	return false; }
			if ( $Array[3] > 255) { return false; }
			
			return true;
		}
	}
	
	
	/**
	 * Control if the Email submited is correctly formated (RFC 5321)
	 * 
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static  function isValidEmail( $email ) 
	{
		if ( preg_match( '/^\w[-.\w]*@(\w[-._\w]*\.[a-zA-Z]{2,}.*)$/', $email, $matches ) )
        {
        	$hostName = $matches[ 1 ];
			
			if ( @strlen( $hostName ) > 5 )
			{
	         	if ( function_exists('checkdnsrr') )
				{
					if ( checkdnsrr( $hostName . '.', 'MX' ) ) return true;
					if ( checkdnsrr( $hostName . '.', 'A'  ) ) return true;
				}
				else
				{
					@exec( "nslookup -type=MX ".$hostName, $r );
					
					if ( @count( $r ) > 0 )
					{
						foreach ( $r as $line )
						{
							if ( @eregi( "^$hostName", $line ) ) return true;
						}
						return false;
					}
					else return true; // if a problem occured while resolving the MX consider the email as valid
				}
			}
        }
		else 
		{
			if ( eregi( "^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$", $email, $check ) )
				return true; 
		}
		return false;
	}
	
	
	/**
	 * Control if the Country Code submited is correctly formated (ISO 3166-1)
	 * It Must be a 2 chars Country Code (US, FR, ES)
	 * 
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static function isValidCountryCode( $countryCode )
	{
		$countries = array(
		  "AU" => "Australia",
		  "AF" => "Afghanistan",
		  "AL" => "Albania",
		  "DZ" => "Algeria",
		  "AS" => "American Samoa",
		  "AD" => "Andorra",
		  "AO" => "Angola",
		  "AI" => "Anguilla",
		  "AQ" => "Antarctica",
		  "AG" => "Antigua & Barbuda",
		  "AR" => "Argentina",
		  "AM" => "Armenia",
		  "AW" => "Aruba",
		  "AT" => "Austria",
		  "AZ" => "Azerbaijan",
		  "BS" => "Bahamas",
		  "BH" => "Bahrain",
		  "BD" => "Bangladesh",
		  "BB" => "Barbados",
		  "BY" => "Belarus",
		  "BE" => "Belgium",
		  "BZ" => "Belize",
		  "BJ" => "Benin",
		  "BM" => "Bermuda",
		  "BT" => "Bhutan",
		  "BO" => "Bolivia",
		  "BA" => "Bosnia/Hercegovina",
		  "BW" => "Botswana",
		  "BV" => "Bouvet Island",
		  "BR" => "Brazil",
		  "IO" => "British Indian Ocean Territory",
		  "BN" => "Brunei Darussalam",
		  "BG" => "Bulgaria",
		  "BF" => "Burkina Faso",
		  "BI" => "Burundi",
		  "KH" => "Cambodia",
		  "CM" => "Cameroon",
		  "CA" => "Canada",
		  "CV" => "Cape Verde",
		  "KY" => "Cayman Is",
		  "CF" => "Central African Republic",
		  "TD" => "Chad",
		  "CL" => "Chile",
		  "CN" => "China, People's Republic of",
		  "CX" => "Christmas Island",
		  "CC" => "Cocos Islands",
		  "CO" => "Colombia",
		  "KM" => "Comoros",
		  "CG" => "Congo",
		  "CD" => "Congo, Democratic Republic",
		  "CK" => "Cook Islands",
		  "CR" => "Costa Rica",
		  "CI" => "Cote d'Ivoire",
		  "HR" => "Croatia",
		  "CU" => "Cuba",
		  "CY" => "Cyprus",
		  "CZ" => "Czech Republic",
		  "DK" => "Denmark",
		  "DJ" => "Djibouti",
		  "DM" => "Dominica",
		  "DO" => "Dominican Republic",
		  "TP" => "East Timor",
		  "EC" => "Ecuador",
		  "EG" => "Egypt",
		  "SV" => "El Salvador",
		  "GQ" => "Equatorial Guinea",
		  "ER" => "Eritrea",
		  "EE" => "Estonia",
		  "ET" => "Ethiopia",
		  "FK" => "Falkland Islands",
		  "FO" => "Faroe Islands",
		  "FJ" => "Fiji",
		  "FI" => "Finland",
		  "FR" => "France",
		  "FX" => "France, Metropolitan",
		  "GF" => "French Guiana",
		  "PF" => "French Polynesia",
		  "TF" => "French South Territories",
		  "GA" => "Gabon",
		  "GM" => "Gambia",
		  "GE" => "Georgia",
		  "DE" => "Germany",
		  "GH" => "Ghana",
		  "GI" => "Gibraltar",
		  "GR" => "Greece",
		  "GL" => "Greenland",
		  "GD" => "Grenada",
		  "GP" => "Guadeloupe",
		  "GU" => "Guam",
		  "GT" => "Guatemala",
		  "GN" => "Guinea",
		  "GW" => "Guinea-Bissau",
		  "GY" => "Guyana",
		  "HT" => "Haiti",
		  "HM" => "Heard Island And Mcdonald Island",
		  "HN" => "Honduras",
		  "HK" => "Hong Kong",
		  "HU" => "Hungary",
		  "IS" => "Iceland",
		  "IN" => "India",
		  "ID" => "Indonesia",
		  "IR" => "Iran",
		  "IQ" => "Iraq",
		  "IE" => "Ireland",
		  "IL" => "Israel",
		  "IT" => "Italy",
		  "JM" => "Jamaica",
		  "JP" => "Japan",
		  "JT" => "Johnston Island",
		  "JO" => "Jordan",
		  "KZ" => "Kazakhstan",
		  "KE" => "Kenya",
		  "KI" => "Kiribati",
		  "KP" => "Korea, Democratic Peoples Republic",
		  "KR" => "Korea, Republic of",
		  "KW" => "Kuwait",
		  "KG" => "Kyrgyzstan",
		  "LA" => "Lao People's Democratic Republic",
		  "LV" => "Latvia",
		  "LB" => "Lebanon",
		  "LS" => "Lesotho",
		  "LR" => "Liberia",
		  "LY" => "Libyan Arab Jamahiriya",
		  "LI" => "Liechtenstein",
		  "LT" => "Lithuania",
		  "LU" => "Luxembourg",
		  "MO" => "Macau",
		  "MK" => "Macedonia",
		  "MG" => "Madagascar",
		  "MW" => "Malawi",
		  "MY" => "Malaysia",
		  "MV" => "Maldives",
		  "ML" => "Mali",
		  "MT" => "Malta",
		  "MH" => "Marshall Islands",
		  "MQ" => "Martinique",
		  "MR" => "Mauritania",
		  "MU" => "Mauritius",
		  "YT" => "Mayotte",
		  "MX" => "Mexico",
		  "FM" => "Micronesia",
		  "MD" => "Moldavia",
		  "MC" => "Monaco",
		  "MN" => "Mongolia",
		  "MS" => "Montserrat",
		  "MA" => "Morocco",
		  "MZ" => "Mozambique",
		  "MM" => "Union Of Myanmar",
		  "NA" => "Namibia",
		  "NR" => "Nauru Island",
		  "NP" => "Nepal",
		  "NL" => "Netherlands",
		  "AN" => "Netherlands Antilles",
		  "NC" => "New Caledonia",
		  "NZ" => "New Zealand",
		  "NI" => "Nicaragua",
		  "NE" => "Niger",
		  "NG" => "Nigeria",
		  "NU" => "Niue",
		  "NF" => "Norfolk Island",
		  "MP" => "Mariana Islands, Northern",
		  "NO" => "Norway",
		  "OM" => "Oman",
		  "PK" => "Pakistan",
		  "PW" => "Palau Islands",
		  "PS" => "Palestine",
		  "PA" => "Panama",
		  "PG" => "Papua New Guinea",
		  "PY" => "Paraguay",
		  "PE" => "Peru",
		  "PH" => "Philippines",
		  "PN" => "Pitcairn",
		  "PL" => "Poland",
		  "PT" => "Portugal",
		  "PR" => "Puerto Rico",
		  "QA" => "Qatar",
		  "RE" => "Reunion Island",
		  "RO" => "Romania",
		  "RU" => "Russian Federation",
		  "RW" => "Rwanda",
		  "WS" => "Samoa",
		  "SH" => "St Helena",
		  "KN" => "St Kitts & Nevis",
		  "LC" => "St Lucia",
		  "PM" => "St Pierre & Miquelon",
		  "VC" => "St Vincent",
		  "SM" => "San Marino",
		  "ST" => "Sao Tome & Principe",
		  "SA" => "Saudi Arabia",
		  "SN" => "Senegal",
		  "SC" => "Seychelles",
		  "SL" => "Sierra Leone",
		  "SG" => "Singapore",
		  "SK" => "Slovakia",
		  "SI" => "Slovenia",
		  "SB" => "Solomon Islands",
		  "SO" => "Somalia",
		  "ZA" => "South Africa",
		  "GS" => "South Georgia and South Sandwich",
		  "ES" => "Spain",
		  "LK" => "Sri Lanka",
		  "XX" => "Stateless Persons",
		  "SD" => "Sudan",
		  "SR" => "Suriname",
		  "SJ" => "Svalbard and Jan Mayen",
		  "SZ" => "Swaziland",
		  "SE" => "Sweden",
		  "CH" => "Switzerland",
		  "SY" => "Syrian Arab Republic",
		  "TW" => "Taiwan, Republic of China",
		  "TJ" => "Tajikistan",
		  "TZ" => "Tanzania",
		  "TH" => "Thailand",
		  "TL" => "Timor Leste",
		  "TG" => "Togo",
		  "TK" => "Tokelau",
		  "TO" => "Tonga",
		  "TT" => "Trinidad & Tobago",
		  "TN" => "Tunisia",
		  "TR" => "Turkey",
		  "TM" => "Turkmenistan",
		  "TC" => "Turks And Caicos Islands",
		  "TV" => "Tuvalu",
		  "UG" => "Uganda",
		  "UA" => "Ukraine",
		  "AE" => "United Arab Emirates",
		  "GB" => "United Kingdom",
		  "UM" => "US Minor Outlying Islands",
		  "US" => "USA",
		  "HV" => "Upper Volta",
		  "UY" => "Uruguay",
		  "UZ" => "Uzbekistan",
		  "VU" => "Vanuatu",
		  "VA" => "Vatican City State",
		  "VE" => "Venezuela",
		  "VN" => "Vietnam",
		  "VG" => "Virgin Islands (British)",
		  "VI" => "Virgin Islands (US)",
		  "WF" => "Wallis And Futuna Islands",
		  "EH" => "Western Sahara",
		  "YE" => "Yemen Arab Rep.",
		  "YD" => "Yemen Democratic",
		  "YU" => "Yugoslavia",
		  "ZR" => "Zaire",
		  "ZM" => "Zambia",
		  "ZW" => "Zimbabwe"
		);
		
		$countryCode = strtoupper( $countryCode );
		
		foreach ( $countries as $countryC => $countryN ) 
		{
			if ( $countryCode == $countryC ) return true;
		}
		return false;
	}
	
	
	/**
	 * Control if the Language Code submited is correctly formated (ISO 4217)
	 * It Must be a 2 chars Language Code (EN, FR, ES,..)
	 * 
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */	
	public static  function isValidLanguageCode( $languageCode )
	{
		$languages = array(
		    'aa' => 'Afar',
		    'ab' => 'Abkhaz',
		    'ae' => 'Avestan',
		    'af' => 'Afrikaans',
		    'ak' => 'Akan',
		    'am' => 'Amharic',
		    'an' => 'Aragonese',
		    'ar' => 'Arabic',
		    'as' => 'Assamese',
		    'av' => 'Avaric',
		    'ay' => 'Aymara',
		    'az' => 'Azerbaijani',
		    'ba' => 'Bashkir',
		    'be' => 'Belarusian',
		    'bg' => 'Bulgarian',
		    'bh' => 'Bihari',
		    'bi' => 'Bislama',
		    'bm' => 'Bambara',
		    'bn' => 'Bengali',
		    'bo' => 'Tibetan Standard, Tibetan, Central',
		    'br' => 'Breton',
		    'bs' => 'Bosnian',
		    'ca' => 'Catalan; Valencian',
		    'ce' => 'Chechen',
		    'ch' => 'Chamorro',
		    'co' => 'Corsican',
		    'cr' => 'Cree',
		    'cs' => 'Czech',
		    'cu' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
		    'cv' => 'Chuvash',
		    'cy' => 'Welsh',
		    'da' => 'Danish',
		    'de' => 'German',
		    'dv' => 'Divehi; Dhivehi; Maldivian;',
		    'dz' => 'Dzongkha',
		    'ee' => 'Ewe',
		    'el' => 'Greek, Modern',
		    'en' => 'English',
		    'eo' => 'Esperanto',
		    'es' => 'Spanish; Castilian',
		    'et' => 'Estonian',
		    'eu' => 'Basque',
		    'fa' => 'Persian',
		    'ff' => 'Fula; Fulah; Pulaar; Pular',
		    'fi' => 'Finnish',
		    'fj' => 'Fijian',
		    'fo' => 'Faroese',
		    'fr' => 'French',
		    'fy' => 'Western Frisian',
		    'ga' => 'Irish',
		    'gd' => 'Scottish Gaelic; Gaelic',
		    'gl' => 'Galician',
		    'gn' => 'GuaranÃ­',
		    'gu' => 'Gujarati',
		    'gv' => 'Manx',
		    'ha' => 'Hausa',
		    'he' => 'Hebrew (modern)',
		    'hi' => 'Hindi',
		    'ho' => 'Hiri Motu',
		    'hr' => 'Croatian',
		    'ht' => 'Haitian; Haitian Creole',
		    'hu' => 'Hungarian',
		    'hy' => 'Armenian',
		    'hz' => 'Herero',
		    'ia' => 'Interlingua',
		    'id' => 'Indonesian',
		    'ie' => 'Interlingue',
		    'ig' => 'Igbo',
		    'ii' => 'Nuosu',
		    'ik' => 'Inupiaq',
		    'io' => 'Ido',
		    'is' => 'Icelandic',
		    'it' => 'Italian',
		    'iu' => 'Inuktitut',
		    'ja' => 'Japanese (ja)',
		    'jv' => 'Javanese (jv)',
		    'ka' => 'Georgian',
		    'kg' => 'Kongo',
		    'ki' => 'Kikuyu, Gikuyu',
		    'kj' => 'Kwanyama, Kuanyama',
		    'kk' => 'Kazakh',
		    'kl' => 'Kalaallisut, Greenlandic',
		    'km' => 'Khmer',
		    'kn' => 'Kannada',
		    'ko' => 'Korean',
		    'kr' => 'Kanuri',
		    'ks' => 'Kashmiri',
		    'ku' => 'Kurdish',
		    'kv' => 'Komi',
		    'kw' => 'Cornish',
		    'ky' => 'Kirghiz, Kyrgyz',
		    'la' => 'Latin',
		    'lb' => 'Luxembourgish, Letzeburgesch',
		    'lg' => 'Luganda',
		    'li' => 'Limburgish, Limburgan, Limburger',
		    'ln' => 'Lingala',
		    'lo' => 'Lao',
		    'lt' => 'Lithuanian',
		    'lu' => 'Luba-Katanga',
		    'lv' => 'Latvian',
		    'mg' => 'Malagasy',
		    'mh' => 'Marshallese',
		    'mi' => 'Maori',
		    'mk' => 'Macedonian',
		    'ml' => 'Malayalam',
		    'mn' => 'Mongolian',
		    'mr' => 'Marathi (Mara?hi)',
		    'ms' => 'Malay',
		    'mt' => 'Maltese',
		    'my' => 'Burmese',
		    'na' => 'Nauru',
		    'nb' => 'Norwegian BokmÃ¥l',
		    'nd' => 'North Ndebele',
		    'ne' => 'Nepali',
		    'ng' => 'Ndonga',
		    'nl' => 'Dutch',
		    'nn' => 'Norwegian Nynorsk',
		    'no' => 'Norwegian',
		    'nr' => 'South Ndebele',
		    'nv' => 'Navajo, Navaho',
		    'ny' => 'Chichewa; Chewa; Nyanja',
		    'oc' => 'Occitan',
		    'oj' => 'Ojibwe, Ojibwa',
		    'om' => 'Oromo',
		    'or' => 'Oriya',
		    'os' => 'Ossetian, Ossetic',
		    'pa' => 'Panjabi, Punjabi',
		    'pi' => 'Pali',
		    'pl' => 'Polish',
		    'ps' => 'Pashto, Pushto',
		    'pt' => 'Portuguese',
		    'qu' => 'Quechua',
		    'rm' => 'Romansh',
		    'rn' => 'Kirundi',
		    'ro' => 'Romanian, Moldavian, Moldovan',
		    'ru' => 'Russian',
		    'rw' => 'Kinyarwanda',
		    'sa' => 'Sanskrit (Sa?sk?ta)',
		    'sc' => 'Sardinian',
		    'sd' => 'Sindhi',
		    'se' => 'Northern Sami',
		    'sg' => 'Sango',
		    'si' => 'Sinhala, Sinhalese',
		    'sk' => 'Slovak',
		    'sl' => 'Slovene',
		    'sm' => 'Samoan',
		    'sn' => 'Shona',
		    'so' => 'Somali',
		    'sq' => 'Albanian',
		    'sr' => 'Serbian',
		    'ss' => 'Swati',
		    'st' => 'Southern Sotho',
		    'su' => 'Sundanese',
		    'sv' => 'Swedish',
		    'sw' => 'Swahili',
		    'ta' => 'Tamil',
		    'te' => 'Telugu',
		    'tg' => 'Tajik',
		    'th' => 'Thai',
		    'ti' => 'Tigrinya',
		    'tk' => 'Turkmen',
		    'tl' => 'Tagalog',
		    'tn' => 'Tswana',
		    'to' => 'Tonga (Tonga Islands)',
		    'tr' => 'Turkish',
		    'ts' => 'Tsonga',
		    'tt' => 'Tatar',
		    'tw' => 'Twi',
		    'ty' => 'Tahitian',
		    'ug' => 'Uighur, Uyghur',
		    'uk' => 'Ukrainian',
		    'ur' => 'Urdu',
		    'uz' => 'Uzbek',
		    've' => 'Venda',
		    'vi' => 'Vietnamese',
		    'vo' => 'VolapÃ¼k',
		    'wa' => 'Walloon',
		    'wo' => 'Wolof',
		    'xh' => 'Xhosa',
		    'yi' => 'Yiddish',
		    'yo' => 'Yoruba',
		    'za' => 'Zhuang, Chuang',
		    'zh' => 'Chinese',
		    'zu' => 'Zulu',
		);
		
		$languageCode = strtolower( $languageCode );
		
		foreach ( $languages as $langC => $langN ) 
		{
			if ( $languageCode == $langC ) return true;
		}
		return false;
	}
	
	
	
	public static function isValidLatitude( $latitude )
	{
		$regexp = "/^-?([0-8]?[0-9]|90)\.[0-9]{1,6}$/";
		$match_latitude = @preg_match( $regexp, $latitude );
		
		return ( $match_latitude == 1 )? true : false;
	}
	
	
	
	public static function isValidLongitude( $longitude )
	{
		$regexp = "/^-?((1?[0-7]?|[0-9]?)[0-9]|180)\.[0-9]{1,6}$/";
		$match_longitude = @preg_match( $regexp, $longitude );
		
		return ( $match_longitude == 1 )? true : false;
	}
	
	
	
	public static function isValidCurrency( $currency )
	{
		$currencies = array(
			'AF' => 'AFA',
			'AL' => 'ALL',
			'DZ' => 'DZD',
			'AS' => 'USD',
			'AD' => 'EUR',
			'AO' => 'AOA',
			'AI' => 'XCD',
			'AQ' => 'NOK',
			'AG' => 'XCD',
			'AR' => 'ARA',
			'AM' => 'AMD',
			'AW' => 'AWG',
			'AU' => 'AUD',
			'AT' => 'EUR',
			'AZ' => 'AZM',
			'BS' => 'BSD',
			'BH' => 'BHD',
			'BD' => 'BDT',
			'BB' => 'BBD',
			'BY' => 'BYR',
			'BE' => 'EUR',
			'BZ' => 'BZD',
			'BJ' => 'XAF',
			'BM' => 'BMD',
			'BT' => 'BTN',
			'BO' => 'BOB',
			'BA' => 'BAM',
			'BW' => 'BWP',
			'BV' => 'NOK',
			'BR' => 'BRL',
			'IO' => 'GBP',
			'BN' => 'BND',
			'BG' => 'BGN',
			'BF' => 'XAF',
			'BI' => 'BIF',
			'KH' => 'KHR',
			'CM' => 'XAF',
			'CA' => 'CAD',
			'CV' => 'CVE',
			'KY' => 'KYD',
			'CF' => 'XAF',
			'TD' => 'XAF',
			'CL' => 'CLF',
			'CN' => 'CNY',
			'CX' => 'AUD',
			'CC' => 'AUD',
			'CO' => 'COP',
			'KM' => 'KMF',
			'CD' => 'CDZ',
			'CG' => 'XAF',
			'CK' => 'NZD',
			'CR' => 'CRC',
			'HR' => 'HRK',
			'CU' => 'CUP',
			'CY' => 'EUR',
			'CZ' => 'CZK',
			'DK' => 'DKK',
			'DJ' => 'DJF',
			'DM' => 'XCD',
			'DO' => 'DOP',
			'TP' => 'TPE',
			'EC' => 'USD',
			'EG' => 'EGP',
			'SV' => 'USD',
			'GQ' => 'XAF',
			'ER' => 'ERN',
			'EE' => 'EEK',
			'ET' => 'ETB',
			'FK' => 'FKP',
			'FO' => 'DKK',
			'FJ' => 'FJD',
			'FI' => 'EUR',
			'FR' => 'EUR',
			'FX' => 'EUR',
			'GF' => 'EUR',
			'PF' => 'XPF',
			'TF' => 'EUR',
			'GA' => 'XAF',
			'GM' => 'GMD',
			'GE' => 'GEL',
			'DE' => 'EUR',
			'GH' => 'GHC',
			'GI' => 'GIP',
			'GR' => 'EUR',
			'GL' => 'DKK',
			'GD' => 'XCD',
			'GP' => 'EUR',
			'GU' => 'USD',
			'GT' => 'GTQ',
			'GN' => 'GNS',
			'GW' => 'GWP',
			'GY' => 'GYD',
			'HT' => 'HTG',
			'HM' => 'AUD',
			'VA' => 'EUR',
			'HN' => 'HNL',
			'HK' => 'HKD',
			'HU' => 'HUF',
			'IS' => 'ISK',
			'IN' => 'INR',
			'ID' => 'IDR',
			'IR' => 'IRR',
			'IQ' => 'IQD',
			'IE' => 'EUR',
			'IL' => 'ILS',
			'IT' => 'EUR',
			'CI' => 'XAF',
			'JM' => 'JMD',
			'JP' => 'JPY',
			'JO' => 'JOD',
			'KZ' => 'KZT',
			'KE' => 'KES',
			'KI' => 'AUD',
			'KP' => 'KPW',
			'KR' => 'KRW',
			'KW' => 'KWD',
			'KG' => 'KGS',
			'LA' => 'LAK',
			'LV' => 'LVL',
			'LB' => 'LBP',
			'LS' => 'LSL',
			'LR' => 'LRD',
			'LY' => 'LYD',
			'LI' => 'CHF',
			'LT' => 'LTL',
			'LU' => 'EUR',
			'MO' => 'MOP',
			'MK' => 'MKD',
			'MG' => 'MGF',
			'MW' => 'MWK',
			'MY' => 'MYR',
			'MV' => 'MVR',
			'ML' => 'XAF',
			'MT' => 'EUR',
			'MH' => 'USD',
			'MQ' => 'EUR',
			'MR' => 'MRO',
			'MU' => 'MUR',
			'YT' => 'EUR',
			'MX' => 'MXN',
			'FM' => 'USD',
			'MD' => 'MDL',
			'MC' => 'EUR',
			'MN' => 'MNT',
			'MS' => 'XCD',
			'MA' => 'MAD',
			'MZ' => 'MZM',
			'MM' => 'MMK',
			'NA' => 'NAD',
			'NR' => 'AUD',
			'NP' => 'NPR',
			'NL' => 'EUR',
			'AN' => 'ANG',
			'NC' => 'XPF',
			'NZ' => 'NZD',
			'NI' => 'NIC',
			'NE' => 'XOF',
			'NG' => 'NGN',
			'NU' => 'NZD',
			'NF' => 'AUD',
			'MP' => 'USD',
			'NO' => 'NOK',
			'OM' => 'OMR',
			'PK' => 'PKR',
			'PW' => 'USD',
			'PA' => 'PAB',
			'PG' => 'PGK',
			'PY' => 'PYG',
			'PE' => 'PEI',
			'PH' => 'PHP',
			'PN' => 'NZD',
			'PL' => 'PLN',
			'PT' => 'EUR',
			'PR' => 'USD',
			'QA' => 'QAR',
			'RE' => 'EUR',
			'RO' => 'ROL',
			'RU' => 'RUB',
			'RW' => 'RWF',
			'KN' => 'XCD',
			'LC' => 'XCD',
			'VC' => 'XCD',
			'WS' => 'WST',
			'SM' => 'EUR',
			'ST' => 'STD',
			'SA' => 'SAR',
			'SN' => 'XOF',
			'CS' => 'EUR',
			'SC' => 'SCR',
			'SL' => 'SLL',
			'SG' => 'SGD',
			'SK' => 'EUR',
			'SI' => 'EUR',
			'SB' => 'SBD',
			'SO' => 'SOS',
			'ZA' => 'ZAR',
			'GS' => 'GBP',
			'ES' => 'EUR',
			'LK' => 'LKR',
			'SH' => 'SHP',
			'PM' => 'EUR',
			'SD' => 'SDG',
			'SR' => 'SRG',
			'SJ' => 'NOK',
			'SZ' => 'SZL',
			'SE' => 'SEK',
			'CH' => 'CHF',
			'SY' => 'SYP',
			'TW' => 'TWD',
			'TJ' => 'TJR',
			'TZ' => 'TZS',
			'TH' => 'THB',
			'TG' => 'XAF',
			'TK' => 'NZD',
			'TO' => 'TOP',
			'TT' => 'TTD',
			'TN' => 'TND',
			'TR' => 'TRY',
			'TM' => 'TMM',
			'TC' => 'USD',
			'TV' => 'AUD',
			'UG' => 'UGS',
			'UA' => 'UAH',
			'SU' => 'SUR',
			'AE' => 'AED',
			'GB' => 'GBP',
			'US' => 'USD',
			'UM' => 'USD',
			'UY' => 'UYU',
			'UZ' => 'UZS',
			'VU' => 'VUV',
			'VE' => 'VEF',
			'VN' => 'VND',
			'VG' => 'USD',
			'VI' => 'USD',
			'WF' => 'XPF',
			'XO' => 'XOF',
			'EH' => 'MAD',
			'ZM' => 'ZMK',
			'ZW' => 'USD'
		);
		
		$currency = strtoupper( $currency );
		
		foreach( $currencies as $country => $cur )
		{
			if ( $currency == $cur ) return true;
		}
		return false;
	}
	
	
	
	private static function resolveUnicode( $text )
	{
		$special_chars = array(
			'&Agrave;' 	=> 'À', 
			'&agrave;' 	=> 'à', 
			'&Aacute;' 	=> 'Á', 
			'&aacute;' 	=> 'á', 
			'&Acirc;' 	=> 'Â', 
			'&acirc;' 	=> 'â', 
			'&Atilde;' 	=> 'Ã', 
			'&atilde;' 	=> 'ã', 
			'&Auml;' 	=> 'Ä', 
			'&auml;' 	=> 'ä', 
			'&Aring;'	=> 'Å', 
			'&aring;' 	=> 'å', 
			'&AElig;' 	=> 'Æ', 
			'&aelig;' 	=> 'æ', 
			'&Ccedil;' 	=> 'Ç', 
			'&ccedil;' 	=> 'ç',
			'&ETH;' 	=> 'Ð', 
			'&eth;' 	=> 'ð', 
			'&Egrave;' 	=> 'È', 
			'&egrave;' 	=> 'è', 
			'&Eacute;' 	=> 'É', 
			'&eacute;' 	=> 'é', 
			'&Ecirc;' 	=> 'Ê', 
			'&ecirc;' 	=> 'ê', 
			'&Euml;' 	=> 'Ë', 
			'&euml;' 	=> 'ë', 
			'&Igrave;' 	=> 'Ì', 
			'&igrave;' 	=> 'ì', 
			'&Iacute;' 	=> 'Í', 
			'&iacute;' 	=> 'í', 
			'&Icirc;' 	=> 'Î', 
			'&icirc;' 	=> 'î', 
			'&Iuml;' 	=> 'Ï', 
			'&iuml;' 	=> 'ï', 
			'&Ntilde;' 	=> 'Ñ',
			'&ntilde;' 	=> 'ñ',
			'&Ograve;' 	=> 'Ò', 
			'&ograve;' 	=> 'ò', 
			'&Oacute;' 	=> 'Ó', 
			'&oacute;' 	=> 'ó', 
			'&Ocirc;' 	=> 'Ô', 
			'&ocirc;' 	=> 'ô', 
			'&Otilde;' 	=> 'Õ', 
			'&otilde;' 	=> 'õ', 
			'&Ouml;' 	=> 'Ö', 
			'&ouml;' 	=> 'ö', 
			'&Oslash;' 	=> 'Ø', 
			'&oslash;' 	=> 'ø',
			'&OElig;' 	=> 'Œ', 
			'&oelig;' 	=> 'œ', 
			'&szlig;' 	=> 'ß', 
			'&THORN;' 	=> 'Þ', 
			'&thorn;' 	=> 'þ', 
			'&Ugrave;'	=> 'Ù', 
			'&ugrave;' 	=> 'ù', 
			'&Uacute;' 	=> 'Ú', 
			'&uacute;' 	=> 'ú', 
			'&Ucirc;' 	=> 'Û', 
			'&ucirc;' 	=> 'û', 
			'&Uuml;' 	=> 'Ü', 
			'&uuml;' 	=> 'ü',
			'&Yacute;' 	=> 'Ý',
			'&yacute;' 	=> 'ý', 
			'&Yuml;' 	=> 'Ÿ', 
			'&yuml;' 	=> 'ÿ',
			'&euro;'	=> '€',
			'&plusmn;'	=> '±',
			
			'&sbquo;' 	=> chr(130), // Single Low-9 Quotation Mark 
        	'&fnof;' 	=> chr(131), // Latin Small Letter F With Hook 
			'&bdquo;' 	=> chr(132), // Double Low-9 Quotation Mark 
			'&hellip;' 	=> chr(133), // Horizontal Ellipsis 
			'&dagger;' 	=> chr(134), // Dagger 
			'&Dagger;' 	=> chr(135), // Double Dagger 
			'&circ;' 	=> chr(136), // Modifier Letter Circumflex Accent 
			'&permil;' 	=> chr(137), // Per Mille Sign 
			'&Scaron;' 	=> chr(138), // Latin Capital Letter S With Caron 
			'&lsaquo;' 	=> chr(139), // Single Left-Pointing Angle Quotation Mark 
			'&OElig;' 	=> chr(140), // Latin Capital Ligature OE 
			'&lsquo;' 	=> chr(145), // Left Single Quotation Mark 
			'&rsquo;' 	=> chr(146), // Right Single Quotation Mark 
			'&ldquo;' 	=> chr(147), // Left Double Quotation Mark 
			'&rdquo;' 	=> chr(148), // Right Double Quotation Mark 
			'&bull;' 	=> chr(149), // Bullet 
			'&ndash;' 	=> chr(150), // En Dash 
			'&mdash;' 	=> chr(151), // Em Dash 
			'&tilde;' 	=> chr(152), // Small Tilde 
			'&trade;' 	=> chr(153), // Trade Mark Sign 
			'&scaron;' 	=> chr(154), // Latin Small Letter S With Caron 
			'&rsaquo;' 	=> chr(155), // Single Right-Pointing Angle Quotation Mark 
			'&oelig;' 	=> chr(156), // Latin Small Ligature OE 
			'&Yuml;' 	=> chr(159)  // Latin Capital Letter Y With Diaeresis 
       	);
		
		foreach( $special_chars as $el => &$char )
		{
			$text = preg_replace( "/$el/i", $char, $text );
		}
		
		return $text;
	}
	
	public static function charsetString( $text, $charset='UTF-8' )
	{
		$text_charset_detected = FeedValidator::resolveUnicode(
			mb_convert_encoding(
				htmlspecialchars( 
					simplifyText( $text )
				,ENT_DISALLOWED, $charset )
			,$charset, "auto" )
		);
		return ( strlen( $text_charset_detected ) > 0 )? $text_charset_detected : simplifyText( $text );
	}
	
	private function simplifyText( $text )
	{
		return FeedValidator::unhtmlentities( 
			urldecode( 
				stripslashes( $text ) 
			) 
		);
	}
	
	public static function stripSpecificHTMLtags( $text )
	{
		return preg_replace( array(
			'@<iframe[^>]*?>.*?</iframe>@si',  	// Strip out iframes
			'@<script[^>]*?>.*?</script>@si',  	// Strip out javascript
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'        	// Strip multi-line comments including CDATA
		), '', $text);
	}
	
	private static function unhtmlentities( $string )
	{
	   // replace numeric entities
	   $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string );
	   $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string );
	   
	   // replace literal entities
	   $trans_tbl = get_html_translation_table( HTML_ENTITIES );
	   $trans_tbl = array_flip( $trans_tbl );
	   
	   return strtr( $string, $trans_tbl );
	}
	
	
	
}