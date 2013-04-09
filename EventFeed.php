<?php
require_once( 'EssDTD.php' );
require_once( 'FeedValidator.php' );
 /**
   * Universal ESS EventFeed Entry Writer
   * FeedItem class - Used as feed element in FeedWriter class
   *
   * @package 	ESSFeedWriter
   * @author  	Brice Pissard
   * @link		http://essfeed.org/index.php/ESS_structure
   */
final class EventFeed
{
	private $roots 		= array();
	private $elements 	= array();
	private $rootDTD	= array();
	private $feedDTD	= array();
	
	public $errors_ 	= array();
	 
	/**
	 * 	@access	public
	 * 	@see	http://essfeed.org/index.php/ESS_structure
	 * 	@param	Array	Array of data that represent the first elements of the feed.
	 * 		
	 * 	@return void;
	 */
	function __construct( $data_=null )
	{
		$this->rootDTD = EssDTD::getRootDTD();
		$this->feedDTD = EssDTD::getFeedDTD();
		
		foreach ( $this->feedDTD as $key => $value ) 
		{
			$this->elements[ $key ]	= array();
		}
		
		if ( $data_ != null && @count( $data_ ) > 0 )
		{
			foreach ( $this->rootDTD as $elementName => $mandatory ) 
			{
				if ( $mandatory == true && @strlen( $data_[ $elementName ] ) <= 0 )
					throw new Exception("Error: Event element ". $elementName . " is mandatory.", 1);
					
			}
			
			foreach ( $data_ as $tagTest => $value ) 
			{
				$isFound = false;
				
				foreach ( $this->rootDTD as $tag ) 
				{
					if ( strtolower( $tagTest ) == $tag ) $isFound = true;
				}
				
				if ( $isFound == false )
					throw new Exception("Error: Event XML element <". $tagTest . "> is not specified within ESS Feed DTD." );
			}
			
			foreach ( $data_ as $tag => $value ) 
			{
				if ( $tag != 'tag' )
				{
					$this->roots[ $tag ] = $value;
				}
				else 
				{
					if ( is_array( $value ) )
					{
						$this->roots[ $tag ] = $value;
					}
					else throw new Exception("Error: Element <tag> must be of 'Array' type." );
				}
			}
		}
	}
	
	/**
	 * Set a Feed element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * @param 	String  name of the feed tag.
	 * @param 	String  content of the feed tag.
	 * @return 	void
	 */
	private function setRootElement( $elementName, $content )
	{
		$this->roots[ $elementName ] = $content ;
	}
	
	
	
	// Root wrapper functions -------------------------------------------------------------------
	
	/**
	 * Set the 'title' feed element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String  value of 'title' feed tag.
	 * 					Define the language-sensitive feed title. 
	 * 					Should not be longer then 128 characters
	 * 
	 * @return  void
	 */
	public function setTitle( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'title', $el ) == false ) 
			{
				throw new Exception( "Error: '<title>' element is mandatory." );
				return;
			}
			$this->setRootElement( 'title', $el );
			$this->setId( $el );
		}
	}
	
	/**
	 * Set the 'uri' feed element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String  value of 'uri' feed tag.
	 * 			The URL have to be formated under RFC 3986 format, an IP can also be submited as a URL.
	 * 
	 * @return 	void
	 */
	public function setUri( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'uri', $el ) == false ) 
			{
				throw new Exception( "Error: '<uri>' element is mandatory." );
				return;
			}
			$this->setRootElement( 'uri', $el );
			$this->setId( $el );
		}
	}
	
	
	
	/**
	 * Set the 'id' feed element
	 * 
	 * @access	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param 	String  value of 'id' feed tag
	 * 
	 * @return 	void
	 */
	public function setId( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'id', $el ) == false ) 
			{
				throw new Exception( "Error: '<id>' element is mandatory." );
				return;
			}
			$this->setRootElement( 'id', FeedWriter::uuid( $el, 'EVENTID:' ) );
		}
	}
	
	/**
	 * Set the 'published' feed element
	 * 
	 * @access	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param 	String  value of 'published' feed tag
	 * 			Must be an UTC Date format (ISO 8601).
	 * 			e.g. 2013-10-31T15:30:59Z in Paris or 2013-10-31T15:30:59+0800 in San Francisco 
	 * 	
	 * @return 	void
	 */
	public function setPublished( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'published', $el ) == false ) 
			{
				throw new Exception( "Error: '<published>' element is mandatory." );
				return;
			}
			
			$this->setRootElement( 'published', $el );
		}
	}
	
	/**
	 * Set the 'updated' feed element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String  value of 'updated' feed tag
	 * 			Must be an UTC Date format (ISO 8601).
	 * 			e.g. 2013-10-31T15:30:59Z in Paris or 2013-10-31T15:30:59+0800 in San Francisco 
	 * 
	 * @return  void
	 */
	public function setUpdated( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'updated', $el ) == false ) 
			{
				throw new Exception( "Error: '<updated>' element is mandatory." );
				return;
			}
			
			$this->setRootElement( 'updated', $el );
		}
	}
	
	/**
	 * Set the 'access' feed element
	 * 
	 * @access 	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String 	Define if the event have a public access.
	 * 					Can take the values: 'PUBLIC' or 'PRIVATE'
	 * @return 	void
	 */
	public function setAccess( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'access', $el ) == false ) 
			{
				throw new Exception( "Error: '<access>' element is mandatory." );
				return;
			}
			
			$this->setRootElement( 'access', $el );
		}
	}
	
	/**
	 * Set the 'description' feed element
	 * 
	 * @access 	public
	 * @see 	http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param  	String  Event Feed description. 
	 * 					This XML element contain the main text event description. 
	 * 					ESS processors should use this content as main event description. 
	 * 					Using HTML inside this section is not recommended because ESS processors could 
	 * 					use this information in an environment that can not read HTML (car devices interface, iCal on mac...).
	 * @return 	void
	 */
	public function setDescription( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'description', $el ) == false ) 
			{
				throw new Exception( "Error: '<description>' element is mandatory." );
				return;
			}
			
			$this->setRootElement( 'description', $el );
		}
	}
	
	/**
	 * Set the 'tags' feed element
	 * 
	 * @access 	public
	 * @see 	http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param  	Array 	Array of child elements with keywords content. 
	 * 					ESS processors should use those keywords to specify the correct category that match with the event purpose.
	 * 
	 * @return 	void
	 */
	public function setTags( Array $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'tags', $el ) == false ) 
			{
				throw new Exception( "Error: '<tags>' element is mandatory." );
				return;
			}
			
			$this->setRootElement( 'tags', $el );
		}
	}
	
	
	
	
	
	/**
	 * Add an element to elements array
	 * 
	 * @access  private
	 * @param	String	Name of the group of tag.
	 * @param 	String  'type' attibute for this tag.
	 * @param 	String  'mode' attibute for this tag.
	 * @param 	String  'unit' attibute for this tag.
	 * @param	Array   Array of data for this tag element.
	 * @param 	String  'priority' attibute for this tag.
	 * @param 	String  'limit' attribute to restrict recurent type occuencies in Date or Price objects.
	 * @param 	String  'padding' attribute.
	 * @param 	String  'padding_day' attribute.
	 * @param 	String 	'padding_week' attribute.	
	 * @return	void
	 */
	private function addElement( 
		$groupName, 
		$type 			= '', 
		$mode 			= '', 
		$unit 			= null, 
		$data_ 			= null, 
		$priority 		= 0, 
		$limit			= 0, 
		$padding		= 1, 
		$padding_day	= null, 
		$padding_week	= null 
	)
	{
		$groupName = strtolower( $groupName );
		
		$errorType = 'Error['.$groupName.']: ';
		
		if ( @strlen( $type ) > 0 )	
		{
			if ( @count( $data_ ) > 0 )	
			{
				if ( $this->controlType( $groupName, $type ) == true )
				{
					if ( $this->controlMode( $groupName, $mode ) == true )
					{
						if ( $this->controlPaddingDay( $groupName, $padding_day ) == true )
						{
							if ( $this->controlPaddingWeek( $groupName, $padding_week ) == true )
							{
								if ( $this->controlTags( $groupName, $data_ ) == true ) 
								{
									foreach ( $data_ as $tag => $value ) 
									{
										//echo $tag."=> " . $value."<br>";
										if ( $this->controlNodeContent( $tag, $value ) == false )
										{
											throw new Exception( $errorType . "The XML element <$tag> have an invalid content: '$value', please control the correct syntax in ESS DTD." );
											break;
										}
									}
									
									array_push(
										$this->elements[ $groupName ],
										array(
											'type' 			=> $type,
											'mode'			=> $mode,
											'unit' 			=> $unit,
											'priority'		=> $priority,
											'limit'			=> $limit,
											'padding'		=> $padding,
											'padding_day'	=> $padding_day,
											'padding_week'	=> $padding_week,
											
											'content'		=> $data_
										)
									);
								}
								else 
								{
									$mandatories = "";
									foreach ( $this->feedDTD[ $groupName ][ 'tags' ] as $tag => $mandatory )
									{
										if ( $mandatory == true &&  @strlen( $data_[ $tag ] ) <= 0 ) $mandatories .= "<" .$tag."> ";
									}
									throw new Exception( $errorType . "All XML mandatories elements are not provided (".$mandatories.")." );
								}
							}
							else throw new Exception( $errorType . "Attribute padding_week='".$padding_week."' is not available within ESS DTD." );	
						}
						else throw new Exception( $errorType . "Attribute padding_day='".$padding_day."' is not available within ESS DTD." );
					}
					else throw new Exception( $errorType . "Attribute mode='".$mode."' is not available within ESS DTD." );
				}
				else throw new Exception( $errorType . "Attribute type='".$type."' is not available within ESS DTD." );
			}
			else throw new Exception( $errorType . "Element could not be empty." );
		}
		else throw new Exception( $errorType . "The 'type' attribute is required." );
	}
	
	/**
	 * Return the collection of root elements in this feed item
	 * 
	 * @access   public
	 * @return   Array
	 */
	public function getRoots()
	{
		return $this->roots;
	}
	
	/**
	 * Return the collection of elements in this feed item
	 * 
	 * @access   public
	 * @return   Array
	 */
	public function getElements()
	{
		return $this->elements;
	}
	
	
	/**
	 * 	[MANDATORY] Add a Category to the current event feed.
	 * 				it is recommended add two categories per event feed for search engines to be more pertinents.
	 * 
	 * @access  public
	 * @see 	http://essfeed.org/index.php/ESS:Categories
	 * 
	 * @param	String	Define the purpose ot the current event. 
	 * 					Can take the values: 'award', 'commemoration', 'competition', 'conference', 'concert', 
	 * 					'diner', 'exhibition', 'family', 'festival', 'meeting', 'networking', 'party', 'seminar' or 'theme'.
	 * 
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 	=> xxx,	// [MANDATORY]					String 	Category name (Should not be longer then 128 chars)
	 * 						'id'	=> xxx	// [OPTIONAL but RECOMMENDED] 	String 	Category ID (according to a specific taxonimy).	
	 * 					);
	 * 
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * 
	 * @return 	void
	 */
	public function addCategory( 
		$type, 
		$data_ 		= null, 
		$priority	= 0 
	) 
	{
		 $this->addElement( 'categories', $type, null, null, $data_, $priority ); 
	}
	
	
	/**
	 * 	[MANDATORY] Add a Date for the event within the current event's feed.
	 * 
	 * @access  public
	 * @see 	http://essfeed.org/index.php/ESS:Dates
	 * 
	 * @param	String	Define the type of date of this event. 
	 * 					Can take the values ("standalone", "permanent" or "recurrent").
	 * 
	 * @param 	String	The "unit" attribute only applied if type="recurrent" is specified. 
	 * 					The "unit" attribute can take five values: "hour", "day", "week", "month" or "year". 
	 * 					ESS processors should consider "hour" as the default "unit" attribute if it is not specified.
	 * 
	 * @param 	int		[OPTIONAL] 	The "limit" attribute only applies if type="recurrent" is specified. 
	 * 								The "limit" attribute is optional and defines the number of times the recurrent event will happen. 
	 * 								If the "limit" attribute is not specified or if limits equal zero ESS Processors should consider 
	 * 								the current event as infinite.
	 * 
	 * @param	int		[OPTIONAL] 	The "padding" attribute only applies if type="recurrent" is specified. 
	 * 								The "padding" attribute is optional and defines the number of time the recurrent event has to be rescheduled "unit" attribute to happen again. 
	 * 								If the "padding" attribute is not specified ESS Processors should be consider the event with a padding="1".
	 * 
	 * @param	String	[OPTIONAL] 	The "padding_day" attribute defines the type of "unit" attribute that has to be considered as repeated.
	 * 								The "padding_day" attribute can take eight types of values: "number", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" or "sunday". 
	 * 								The "padding_day" attribute only applied if type="recurrent" is specified and if the unit attribute is "week" or "month". 
	 * 				
	 * @param	String	[OPTIONAL] 	The "padding_week" attribute defines the section of the month that has to considered to be repeated. 
	 * 								The "padding_week" attribute can take five types of values: "first", "second", "third", "fourth" or "last". 
	 * 								The "padding_week" attribute only applies if type="recurrent" is specified and if the unit attribute is "month". 
	 * 								If the "padding_zone" attribute is not specified ESS Processors should be considered the event as without "padding_week".
	 * 				
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 		=> xxx,	// [MANDATORY]					String 	date name (Should not be longer then 128 chars)
	 * 						'start'		=> xxx,	// [MANDATORY]					Date	date of the event under ISO 8601 format (e.g. 2013-10-31T15:30:59+0800 in Pasific Standard Time).
	 * 						'duration'	=> xxx	// [OPTIONAL but RECOMMENDED]  	Integer	duration in seconds (from start date).
	 * 					);
	 * 
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addDate( 
		$type, 
		$unit, 	
		$limit			= 0,
		$padding		= 1,
		$padding_day	= "number", 
		$padding_week	= "first",
		$data_ 			= null, 
		$priority		= 0 
	) 
	{
		 $this->addElement( 'dates', $type, null, $unit, $data_, $priority, $limit, $padding, $padding_day, $padding_week ); 
	}
	
	
	/**
	 * 	[MANDATORY] Add a Place to the current event feed.
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Places
	 * 
	 * @param	String	Define the type of place of this event. 
	 * 					Can take the values: "fixed", "area", "moving" or "virtual".
	 * 
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]					String 	location name (Should not be longer then 128 chars).
	 *						'country_code' 	=> xxx,	// [MANDATORY]					String 	2 chars country code (ISO 3166-1).
	 *						'country' 		=> xxx,	// [OPTIONAL but RECOMMENDED] 	String 	country name.
	 *						'latitude' 		=> xxx,	// [OPTIONAL but RECOMMENDED] 	Float 	number of the latitude of the event in Decimal Degrees: -90.XXXXXX to 90.XXXXXX (ISO 6709).
	 *						'longitude' 	=> xxx,	// [OPTIONAL but RECOMMENDED] 	Float 	number of the latitude of the event in Decimal Degrees: -180.XXXXXX to 180.XXXXXX (ISO 6709).
	 *						'address' 		=> xxx,	// [OPTIONAL but RECOMMENDED] 	String	event address.
	 *						'city' 			=> xxx,	// [OPTIONAL but RECOMMENDED] 	String	event city.
	 *						'zip' 			=> xxx,	// [OPTIONAL] 					String	event zip code.
	 *						'state' 		=> xxx,	// [OPTIONAL] 					String	event state.
	 *						'state_code'	=> xxx,	// [OPTIONAL] 					String	event state code.
	 *						'begining' 		=> xxx,	// [OPTIONAL] 					XML		moving event starting location. (only for type="moving").
	 *						'ending' 		=> xxx,	// [OPTIONAL] 					XML		moving event ending location. (only for type="moving").
	 *						'medium_name' 	=> xxx,	// [OPTIONAL] 					String	virtual event medium name. (only for type="virtual").
	 *						'medium_type'	=> xxx,	// [OPTIONAL] 					String	virtual event medium type ("television", "radio" or "internet").  (only for type="virtual").
	 *						'kml' 			=> xxx	// [OPTIONAL] 					XML		area event surface representation. (only for type="area").
	 * 					);
	 *  
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addPlace( 
		$type, 
		$data_ 		= null, 
		$priority	= 0 
	) 
	{
		$this->addElement( 'places', $type, null, null, $data_, $priority ); 
	}
	
	
	/**
	 * 	[MANDATORY] Add a Price to the current event feed.
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Prices
	 * 
	 * @param	String	Define the type of Price of this event. 
	 * 					Can take the values: "standalone" or "recurrent".
	 * 
	 * @param 	String	The "unit" attribute only applied in type="recurrent" is specified. 
	 * 					The "unit" attribute can take five values: "hour", "day", "week", "month" or "year". 
	 * 					ESS processors should consider "hour" as the default "unit" attribute. 
	 * 
	 * @param 	String	Reprensent the payment mode to assist to the event.
	 * 					The "mode" attribute can take four values: "fixed", "free", "invitation", "renumerated" or "prepaid". 
	 * 					ESS Processors should consider that "fixed" is the default attribute if it is not specified.
	 * 
	 * @param 	int		[OPTIONAL] 	The "limit" attribute only applies if type="recurrent" is specified. 
	 * 								The "limit" attribute is optional and defines the number of times the recurrent event will happen. 
	 * 								If the "limit" attribute is not specified or if limits equal zero ESS Processors should consider 
	 * 								the current event as infinite.
	 * 
	 * @param	int		[OPTIONAL] 	The "padding" attribute only applies if type="recurrent" is specified. 
	 * 								The "padding" attribute is optional and defines the number of time the recurrent event has to be rescheduled "unit" attribute to happen again. 
	 * 								If the "padding" attribute is not specified ESS Processors should be consider the event with a padding="1".
	 * 
	 * @param	String	[OPTIONAL] 	The "padding_day" attribute defines the type of "unit" attribute that has to be considered as repeated.
	 * 								The "padding_day" attribute can take eight types of values: "number", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" or "sunday". 
	 * 								The "padding_day" attribute only applied if type="recurrent" is specified and if the unit attribute is "week" or "month". 
	 * 				
	 * @param	String	[OPTIONAL] 	The "padding_week" attribute defines the section of the month that has to considered to be repeated. 
	 * 								The "padding_week" attribute can take five types of values: "first", "second", "third", "fourth" or "last". 
	 * 								The "padding_week" attribute only applies if type="recurrent" is specified and if the unit attribute is "month". 
	 * 								If the "padding_zone" attribute is not specified ESS Processors should be considered the event as without "padding_week".
	 * 
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]	String 	current price name (Should not be longer then 128 chars).
	 * 						'value' 		=> xxx,	// [MANDATORY]	Number 	current price value.
	 *						'currency'		=> xxx,	// [MANDATORY]	String 	current price 3chars currency (ISO 4217 format, e.g. USD, EUR...).  
	 *						'start' 		=> xxx, // [OPTIONAL]	Date	date of the recurent billing under ISO 8601 format (only if type="recurent")
	 *						'duration'		=> xxx, // [OPTIONAL]	Integer	duration in seconds (from start date).
	 *						'uri' 			=> xxx  // [OPTIONAL]	URI		URL of the payment validation (invitation, webservice, paypal...) -  RFC 3986 format.				
	 * 					);
	 * 
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addPrice( 
		$type, 
		$unit, 	
		$mode,
		$limit			= 0,
		$padding		= 1,
		$padding_day	= "number", 
		$padding_week	= "first",	
		$data_ 			= null, 
		$priority		= 0 
	) 
	{
		 $this->addElement( 'prices', $type, $mode, $unit, $data_, $priority, $limit, $padding, $padding_day, $padding_week ); 
	}
	
	
	/**
	 * 	[OPTIONAL] Add a Person involve in the current event.
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:People
	 * 
	 * @param	String	Define the type of persons involve in the event. 
	 * 					Can take the values: "organizer", "performer" or "attendee".
	 * 
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]					String 	current price name (Should not be longer then 128 chars).
	 * 						'id' 			=> xxx,	// [OPTIONAL]					URI		Unique and universal person identifier.
	 *						'firstname' 	=> xxx,	// [OPTIONAL]					String 	lirst name of the person.
	 *						'lastname' 		=> xxx,	// [OPTIONAL]					String 	last name of the person.
	 *						'organization' 	=> xxx,	// [OPTIONAL]					String 	organisation name (if applicable).
	 *						'logo' 			=> xxx,	// [OPTIONAL]					String 	URL of an image that identify the event actor (> 64px).
	 *						'icon' 			=> xxx,	// [OPTIONAL]					String 	URL of an icon that identify the event actor (<= 64px).
	 *						'uri' 			=> xxx,	// [OPTIONAL]					String 	URL of a page that describe the event actor.
	 *						'address' 		=> xxx,	// [OPTIONAL]					String 	address of the person.
	 *						'city' 			=> xxx,	// [OPTIONAL]					String 	city of the person.
	 *						'zip' 			=> xxx,	// [OPTIONAL]					String 	zip code of the person.
	 *						'state' 		=> xxx,	// [OPTIONAL]					String 	state code of the person.
	 *						'state_code'	=> xxx,	// [OPTIONAL]					String 	city of the person.
	 *						'country' 		=> xxx,	// [OPTIONAL]					String 	country name of the person.
	 *						'country_code' 	=> xxx,	// [OPTIONAL]					String 	country code in 2 chars of the person (ISO 3166).
	 *						'email' 		=> xxx,	// [OPTIONAL]					String 	email to contact the person.
	 *						'phone' 		=> xxx,	// [OPTIONAL]					String 	phone number to contact the person.
	 *						'minpeople' 	=> xxx,	// [OPTIONAL but RECOMMENDED]	String 	Defines the minimum amount of attendees for this event. (only for type="attendee").
	 *						'maxpeople' 	=> xxx,	// [OPTIONAL but RECOMMENDED]	String 	Defines the maximum amount of attendees for this event. (only for type="attendee").
	 *						'minage' 		=> xxx,	// [OPTIONAL but RECOMMENDED]	String 	Defines the age minimum of attendees for this event. (only for type="attendee").
	 *						'restriction'	=> xxx	// [OPTIONAL but RECOMMENDED]	String 	Defines the list of rules that the attendee should be aware of before attending the event. (only for type="attendee").
	 * 					);
	 *  
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addPeople( 
		$type, 
		$data_ 		= null, 
		$priority	= 0 
	) 
	{
		 $this->addElement( 'people', $type, null, null, $data_, $priority ); 
	}
	
	
	/**
	 * 	[OPTIONAL] Add a Media file URL to the current event feed.
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Media
	 * 
	 * @param	String	Define the type of Media file that represent the event. 
	 * 					Can take the values: "image", "sound", "video" or "website".
	 * 
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 	=> xxx,	// [MANDATORY]	String 	name of the current media file. (Should not be longer then 128 chars).
	 * 						'uri' 	=> xxx	// [MANDATORY]	URI 	current media file URL - under RFC 2396 format.
	 * 					);
	 *  
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addMedia( 
		$type, 
		$data_ 		= null, 
		$priority	= 0 
	) 
	{
		$this->addElement( 'media', $type, null, null, $data_, $priority ); 
	}
	
	/**
	 * 	[OPTIONAL] Add a Relation other events have with this current event feed.
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Relations
	 * 
	 * @param	String	Define the type of event's relationanother event have with this event. 
	 * 					Can take the values: "alternative", "related" or "enclosure".
	 * 
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 	=> xxx,	// [MANDATORY]	String 	name of the other ess event in relation with the current one. (Should not be longer then 128 chars).
	 * 						'id' 	=> xxx,	// [MANDATORY]	URI 	unique and universal ESS feed (ess:id) identifier. Must be the same then the one defined the other ESS document.
	 * 						'uri' 	=> xxx	// [MANDATORY]	URI 	define distant URI where is placed ESS Feed Document.
	 * 					);
	 *  
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addRelation( 
		$type, 
		$data_ 		= null, 
		$priority	= 0 
	) 
	{
		$this->addElement( 'relations', $type, null, null, $data_, $priority ); 
	}
	
	
	/**
	 * 	[OPTIONAL] Add a Relation other events have with this current event feed.
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Relations
	 * 
	 * @param	String	Define the type of authorship the current person have with this event. 
	 * 					Can take the values: "alternative", "related" or "enclosure".
	 * 
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]	String 	Short name of the author. (Should not be longer then 128 chars).
	 * 						'uri' 			=> xxx,	// [MANDATORY]	URI 	Define URI where more information is available about the author.
	 *						'firstname' 	=> xxx,	// [OPTIONAL]	String 	lirst name of the author.
	 *						'lastname' 		=> xxx,	// [OPTIONAL]	String 	last name of the author.
	 *						'address' 		=> xxx,	// [OPTIONAL]	String 	address of the author.
	 *						'city' 			=> xxx,	// [OPTIONAL]	String 	city of the author.
	 *						'zip' 			=> xxx,	// [OPTIONAL]	String 	zip code of the author.
	 *						'state' 		=> xxx,	// [OPTIONAL]	String 	state code of the author.
	 *						'state_code'	=> xxx,	// [OPTIONAL]	String 	city of the author.
	 *						'country' 		=> xxx,	// [OPTIONAL]	String 	country name of the author.
	 *						'country_code' 	=> xxx,	// [OPTIONAL]	String 	country code in 2 chars of the author (ISO 3166).
	 *						'email' 		=> xxx,	// [OPTIONAL]	String 	email to contact the author.
	 * 						'phone' 		=> xxx	// [OPTIONAL]	String 	phone number to contact the author.
	 * 					);
	 *  
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements. 
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addAuthor( 
		$type, 
		$data_ 		= null, 
		$priority	= 0 
	) 
	{
		$this->addElement( 'authors', $type, null, null, $data_, $priority ); }
	
	
	
	
	
	// -- Private Methods --
	
	private function controlRoot( $elmName, $val=null  )
	{
		foreach ( $this->rootDTD as $elm => $mandatory ) 
		{
			if ( strtolower( $elmName ) != 'tags' )
			{
				if ( $mandatory == true && @strlen( $val ) <= 0 ) return false;
			}
			else
			{
				if ( is_array( $val ) )
				{
					if ( @count( $val ) <= 0 ) return false;
				}
				else false;
			}
		}
		return true;
	}
	
	private function controlTags( $elmName='', Array $data_=null )
	{
		foreach ( $this->feedDTD[ $elmName ][ 'tags' ] as $tag => $mandatory )
		{
			if ( $mandatory == true &&  @strlen( $data_[ $tag ] ) <= 0 ) return false;
		}
		
		foreach ( $data_ as $tagTest => $value ) 
		{
			$isFound = false;
			
			foreach ( $this->feedDTD[ $elmName ][ 'tags' ] as $tag ) 
			{
				if ( strtolower( $tagTest ) == $tag ) $isFound = true;
			}
			
			if ( $isFound == true )
			{
				if ( @strlen( $value ) <= 0 ) return false;
			} 
			else return false;
		}
		
		return true;
	}
	
	private function controlType( $elmName='', $typeToControl='' )
	{
		foreach ( $this->feedDTD[ $elmName ][ 'types' ] as $type ) 
		{
			if ( strtolower( $typeToControl ) == $type ) return true;
		}
		return false;
	}
	
	private function controlMode( $elmName='', $modeToControl='' )
	{
		if ( isset( $this->feedDTD[ $elmName ][ 'modes' ] ) )
		{
			foreach ( $this->feedDTD[ $elmName ][ 'modes' ] as $mode ) 
			{
				if ( strtolower( $modeToControl ) == $mode ) return true;
			}
		}
		else return true;	
		return false;
	}
	
	private function controlPaddingDay( $elmName='', $padding_dayToControl='' )
	{
		if ( isset( $this->feedDTD[ $elmName ][ 'padding_days' ] ) )
		{
			$padding_ = explode( ',', $padding_dayToControl );
			
			if ( @count( $padding_ ) > 0 )
			{
				foreach( $padding_ as $padding_dayToControl )
				{
					foreach ( $this->feedDTD[ $elmName ][ 'padding_days' ] as $padding_day ) 
					{
						if ( strtolower( $padding_dayToControl ) == $padding_day || $padding_dayToControl == '' ) return true;
					}
				}
			}
			else 
			{
				foreach ( $this->feedDTD[ $elmName ][ 'padding_days' ] as $padding_day ) 
				{
					if ( strtolower( $padding_dayToControl ) == $padding_day || $padding_dayToControl == '' ) return true;
				}
			}
		}
		else return true;	
		return false;
	}
	
	private function controlPaddingWeek( $elmName='', $padding_weekToControl='' )
	{
		if ( isset( $this->feedDTD[ $elmName ][ 'padding_weeks' ] ) )
		{
			foreach ( $this->feedDTD[ $elmName ][ 'padding_weeks' ] as $padding_day ) 
			{
				if ( strtolower( $padding_weekToControl ) == $padding_day || $padding_weekToControl == '' ) return true;
			}
		}
		else return true;	
		return false;
	}
	
	private function controlNodeContent( $name, $value )
	{
		switch ( strtolower( $name ) ) 
		{
			case 'start'			:	
			case 'published' 		:
			case 'updated' 			: return FeedValidator::isValidDate( 		$value ); break;	
			case 'name' 			: return ( FeedValidator::isNull( 			$value ) == false )? true : false; break;
			case 'email' 			: return FeedValidator::isValidEmail( 		$value ); break;
			case 'logo' 			:
			case 'icon' 			:
			case 'uri' 				: return FeedValidator::isValidURL( 		$value ); break;
			case 'latitude'			: return FeedValidator::isValidLatitude(	$value ); break;
			case 'longitude'		: return FeedValidator::isValidLongitude(	$value ); break;
			case 'country_code' 	: return FeedValidator::isValidCountryCode( $value ); break;
			case 'currency' 		: return FeedValidator::isValidCurrency(	$value ); break;
			default					: return true; break;
		}
		return true;
	}
	
	
 }