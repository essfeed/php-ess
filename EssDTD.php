<?php
/**
 * ESS Feed DTD v0.9 (to check if tags exists and are mandatories)
 *
 * @package	ESSFeedWriter
 * @author  Brice Pissard
 * @link    http://eventstandardsyndication.org/index.php/ESS_structure
 */
final class EssDTD
{
	public function __construct() {}
	
	
	/**
	 *  Get Channel's first available XML child
	 * 
	 * 	@access public
	 * 	@return Array	Return an Array of the DTD
	 */
	public static function getChannelDTD()
	{
		return array(
			'title'     	=> true,
			'link'			=> true,
			'id'			=> true,
			'published'		=> true,
			'updated'		=> false,
			'generator'		=> false,
			'rights'		=> false,
			'feed'			=> true  
		);
	}
	
	/**
	 *  Get Feed first XML element DTD
	 * 
	 * 	@access public
	 * 	@return Array	Return an Array of the DTD
	 */
	public static function getRootDTD()
	{
		return array(
			'title' 		=> true,
		    'id'			=> true,
		    'access'		=> true,
		    'description'	=> true,
		    'published'		=> true,
		    'uri'			=> false,
		    'updated'		=> false, 
		    'tags'			=> false
		);
	}
	
	/**
	 *  Get Feed Complex XML child element DTD
	 * 
	 * 	@access public
	 * 	@return Array	Return an Array of the DTD
	 */	
	public static function getFeedDTD()
	{
		return array( 
			'categories' => array(
				'mandatory' => true,
				'types' 	=> array('award','competition','commemoration','conference','concert','diner','exhibition','family','festival','meeting','networking','party','seminar','theme'),
				'tags' 		=> array(
					'name' 			=> true,
					'id' 			=> false 
				)
			),
			'dates' => array(
				'mandatory'	=> true,
				'types' 	=> array('standalone','recurrent','permanent'),
				'units'		=> array('hour','day','week','month','year'),
				'tags' 		=> array(
					'name' 			=> true,
					'start' 		=> true,
					'duration' 		=> false
				)
			),
			'places' => array(
				'mandatory' => true,
				'types' 	=> array('fixed','area','moving','virtual'),
				'tags' 		=> array(
					'name' 			=> true,
					'country' 		=> true,
					'country_code' 	=> true,
					'latitude' 		=> false,
					'longitude' 	=> false,
					'address' 		=> false,
					'city' 			=> false,
					'zip' 			=> false,
					'state' 		=> false,
					'state_code'	=> false,
					'start' 		=> false,
					'stop' 			=> false,
					'medium_name' 	=> false,
					'medium_type'	=> false,
					'kml' 			=> false
				)	
			),
			'prices' => array(
				'mandatory' => true,
				'types' 	=> array('standalone','recurrent'),
				'modes'		=> array('fixed','free','invitation','renumerated','prepaid'),
				'units'		=> array('hour','day','week','month','year'),
				'tags' 		=> array(
					'name' 			=> true,
					'value' 		=> true,
					'currency'		=> false,
					'start' 		=> false,
					'duration'		=> false,
					'uri' 			=> false
				)
			),
			'media' => array(
				'mandatory' => false,
				'types' 	=> array('image','sound','video','website'),
				'tags' 		=> array(
					'name' 			=> true,
					'uri' 			=> true
				)
			),
			'people' => array(
				'mandatory' => false,
				'types' 	=> array('organizer','performer','attendee'),
				'tags' 		=> array(
					'name' 			=> true,
					'id' 			=> false,
					'firstname' 	=> false,
					'lastname' 		=> false,
					'organization' 	=> false,
					'logo' 			=> false,
					'icon' 			=> false,
					'uri' 			=> false,
					'address' 		=> false,
					'city' 			=> false,
					'zip' 			=> false,
					'state' 		=> false,
					'state_code'	=> false,
					'country' 		=> false,
					'country_code' 	=> false,
					'email' 		=> false,
					'phone' 		=> false,
					'minpeople' 	=> false,
					'maxpeople' 	=> false,
					'minage' 		=> false,
					'restriction'	=> false,
				)
			),
			'authors' => array(
				'mandatory'	=> false,
				'types' 	=> array('author','contributor'),
				'tags' 		=> array(
					'name'			=> true,
					'uri'			=> true,
					'firstname' 	=> false,
					'lastname'		=> false,
					'organization'	=> false,
					'address'		=> false,
					'city'			=> false,
					'zip'			=> false,
					'state' 		=> false,
					'state_code'	=> false,
					'country' 		=> false,
					'country_code' 	=> false,
					'email'			=> false,
					'phone'			=> false
				)
			), 
			'relations' => array(
				'mandatory' => false,
				'types' 	=> array('alternative','related','enclosure'),
				'tags' 		=> array(
					'name' 			=> true,
					'uri'			=> true,
					'id' 			=> true
				)
			),
		);
	}


}
