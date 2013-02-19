<?php
/**
 * Universal ESS EventFeed Entry Writer
 * FeedItem class - Used as feed element in FeedWriter class
 *
 * @package         ESSFeedWriter
 * @author          Brice Pissard
 * @link            http://eventstandardsyndication.org/index.php/ESS_structure
 */
final class EventFeed
{
	private $roots 		= array();
	private $elements 	= array();
	
	// ESS Feed DTD version 0.91 (to check if tag exists and is mandatory)
	private $rootDTD = array(
		'title' 		=> true,
        'id'			=> true,
        'access'		=> true,
        'description'	=> true,
        'tags'			=> false,
        'published'		=> true,
        'updated'		=> false,
	);
	
	private $feedDTD = array( 
		'categories' => array(
			'mandatory' => true,
			'types' 	=> array('award','competition','commemoration','conference','concert','diner','exhibition','family','festival','meeting','networking','party','seminar','theme'),
			'tags' 		=> array(
				'name' 			=> true,
				'id' 			=> false 
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
		'dates' => array(
			'types' 	=> array('standalone','recurent'),
			'tags' 		=> array(
				'name' 			=> true,
				'start' 		=> true,
				'duration' 		=> false
			)
		),
		'places' => array(
			'mandatory' => true,
			'types' 	=> array('fix','area','moving','virtual'),
			'tags' 		=> array(
				'name' 			=> true,
				'latitude' 		=> true,
				'longitude' 	=> true,
				'address' 		=> false,
				'city' 			=> false,
				'zip' 			=> false,
				'state' 		=> false,
				'state_code'	=> false,
				'country' 		=> false,
				'country_code' 	=> false,
				'start' 		=> false,
				'stop' 			=> false,
				'medium' 		=> false,
				'kml' 			=> false
			)	
		),
		'prices' => array(
			'mandatory' => true,
			'types' 	=> array('standalone','recurent'),
			'tags' 		=> array(
				'name' 			=> true,
				'value' 		=> true,
				'start' 		=> false,
				'uri' 			=> false
			)
		),
		'medias' => array(
			'mandatory' => false,
			'types' 	=> array('image','sound','video','website'),
			'tags' 		=> array(
				'name' 			=> true,
				'uri' 			=> true
			)
		),
		'people' => array(
			'mandatory' => false,
			'types' 	=> array('organizer','performer','visitor'),
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
				'minage' 		=> false
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
	 
	
	
	
	
	function __construct( $data_=null )
	{
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
					throw new Exception("Error: Event element ". $tagTest . " is not specified in ESS Feed DTD.", 1);
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
					else throw new Exception("Error: Element 'tag' must be of 'Array'type.", 1);
				}
			}
		}
	}
	
	/**
	 * Set a Feed element
	 * 
	 * @access   public
	 * @param    String  name of the feed tag
	 * @param    String  content of the feed tag
	 * @return   Void
	 */
	private function setRootElement( $elementName, $content )
	{
		$this->roots[ $elementName ] = $content ;
	}
	
	
	
	// Root wrapper functions -------------------------------------------------------------------
	
	/**
	 * Set the 'title' feed element
	 * 
	 * @access   public
	 * @param    String  value of 'title' feed tag
	 * @return   Void
	 */
	public function setTitle( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'title', $el ) == false ) 
			{
				throw new Exception( "Error: 'title' element is mandatory.", 1);
				return;
			}
			$this->setRootElement( 'title', $el );
			$this->setId( $el );
		}
	}
	
	/**
	 * Set the 'id' feed element
	 * 
	 * @access   public
	 * @param    String  value of 'id' feed tag
	 * @return   Void
	 */
	public function setId( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'id', $el ) == false ) 
			{
				throw new Exception( "Error: 'id' element is mandatory.", 1);
				return;
			}
			$this->setRootElement( 'id', FeedWriter::uuid( $el, 'FEEDID:' ) );
		}
	}
	
	/**
	 * Set the 'published' feed element
	 * 
	 * @access   public
	 * @param    String  value of 'published' feed tag
	 * @return   Void
	 */
	public function setPublished( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'published', $el ) == false ) 
			{
				throw new Exception( "Error: 'published' element is mandatory.", 1);
				return;
			}
			
			$this->setRootElement( 'published', $el );
		}
	}
	
	/**
	 * Set the 'updated' feed element
	 * 
	 * @access   public
	 * @param    String  value of 'updated' feed tag
	 * @return   Void
	 */
	public function setUpdated( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'updated', $el ) == false ) 
			{
				throw new Exception( "Error: 'updated' element is mandatory.", 1);
				return;
			}
			
			$this->setRootElement( 'updated', $el );
		}
	}
	
	/**
	 * Set the 'access' feed element
	 * 
	 * @access   public
	 * @param    String  value of 'access' feed tag
	 * @return   Void
	 */
	public function setAccess( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'access', $el ) == false ) 
			{
				throw new Exception( "Error: 'access' element is mandatory.", 1);
				return;
			}
			
			$this->setRootElement( 'access', $el );
		}
	}
	
	/**
	 * Set the 'description' feed element
	 * 
	 * @access   public
	 * @param    String  value of 'description' feed tag
	 * @return   Void
	 */
	public function setDescription( $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'description', $el ) == false ) 
			{
				throw new Exception( "Error: 'description' element is mandatory.", 1);
				return;
			}
			
			$this->setRootElement( 'description', $el );
		}
	}
	
	/**
	 * Set the 'tags' feed element
	 * 
	 * @access   public
	 * @param    String  value of 'tags' feed tag
	 * @return   Void
	 */
	public function setTags( Array $el=NULL )
	{
		if ( $el != NULL ) 
		{
			if ( $this->controlRoot( 'tags', $el ) == false ) 
			{
				throw new Exception( "Error: 'tags' element is mandatory.", 1);
				return;
			}
			
			$this->setRootElement( 'tags', $el );
		}
	}
	
	
	
	
	
	/**
	 * Add an element to elements array
	 * 
	 * @access  private
	 * @param	String	Name of the group of tag
	 * @param 	String  The tag name of an element
	 * @param 	String  The content of tag
	 * @param	Array   Attributes(if any) in 'attrName' => 'attrValue' format
	 * @return	Void
	 */
	private function addElement( $groupName, $type = '', $unit = null, Array $data_ = null, $priority = 0 )
	{
		$groupName = strtolower( $groupName );
		
		$errorType = 'Error['.$groupName.']: ';
		
		if ( @strlen( $type ) > 0 )	
		{
			if ( @count( $data_ ) > 0 )	
			{
				if ( $this->controlType( $groupName, $type ) == true ) 
				{
					if ( $this->controlTags( $groupName, $data_ ) == true ) 
					{
						array_push(
							$this->elements[ $groupName ],
							array(
								'type' 		=> $type,
								'unit' 		=> $unit,
								'priority'	=> $priority,
								'content'	=> $data_
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
						throw new Exception( $errorType . "All mandatory element are not provided (".$mandatories.").", 1);
					}
				}
				else throw new Exception( $errorType . "Attribute 'type':".$type." is not available in ESS DTD.", 1);
			}
			else throw new Exception( $errorType . "Element could not be empty.", 1);
		}
		else throw new Exception( $errorType . "The 'type' attribute is required.", 1);
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
	
	
	
	public function addCategories( 	$type, 			Array $data_ = null, $priority=0 ) { $this->addElement( 'categories', 	$type, null,  $data_, $priority ); }
	public function addDates( 		$type, $unit, 	Array $data_ = null, $priority=0 ) { $this->addElement( 'dates', 	 	$type, $unit, $data_, $priority ); }
	public function addPlaces( 		$type, 			Array $data_ = null, $priority=0 ) { $this->addElement( 'places', 		$type, null,  $data_, $priority ); }
	public function addPrices( 		$type, $unit, 	Array $data_ = null, $priority=0 ) { $this->addElement( 'prices', 	 	$type, $unit, $data_, $priority ); }
	public function addPeople( 		$type, 			Array $data_ = null, $priority=0 ) { $this->addElement( 'people', 		$type, null,  $data_, $priority ); }
	public function addMedias( 		$type, 			Array $data_ = null, $priority=0 ) { $this->addElement( 'medias', 		$type, null,  $data_, $priority ); }
	public function addRelations( 	$type, 			Array $data_ = null, $priority=0 ) { $this->addElement( 'relations', 	$type, null,  $data_, $priority ); }
	public function addAuthors( 	$type, 			Array $data_ = null, $priority=0 ) { $this->addElement( 'authors', 		$type, null,  $data_, $priority ); }
	
	
	
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
		//var_dump($data_);
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
	
	
	
	
 }