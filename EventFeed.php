<?php
require_once( 'EssDTD.php' );
 /**
   * Universal ESS EventFeed Entry Writer
   * FeedItem class - Used as feed element in FeedWriter class
   *
   * @package ESSFeedWriter
   * @author  Brice Pissard
   * @link	http://eventstandardsyndication.org/index.php/ESS_structure
   */
final class EventFeed
{
	private $roots 		= array();
	private $elements 	= array();
	private $rootDTD	= array();
	private $feedDTD	= array();
	
	
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
	 * @access   public
	 * @param    String  value of 'uri' feed tag
	 * @return   void
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
				throw new Exception( "Error: '<id>' element is mandatory." );
				return;
			}
			$this->setRootElement( 'id', FeedWriter::uuid( $el, 'EVENTID:' ) );
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
				throw new Exception( "Error: '<published>' element is mandatory." );
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
				throw new Exception( "Error: '<updated>' element is mandatory." );
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
				throw new Exception( "Error: '<access>' element is mandatory." );
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
				throw new Exception( "Error: '<description>' element is mandatory." );
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
	 * @param	String	Name of the group of tag
	 * @param 	String  'type' attibute for this tag
	 * @param 	String  'mode' attibute for this tag
	 * @param 	String  'unit' attibute for this tag
	 * @param	Array   Array of data for this tag element
	 * @param 	String  'priority' attibute for this tag
	 * @return	Void
	 */
	private function addElement( $groupName, $type = '', $mode = '', $unit = null, Array $data_ = null, $priority = 0 )
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
									'type' 		=> $type,
									'mode'		=> $mode,
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
							throw new Exception( $errorType . "All XML mandatories elements are not provided (".$mandatories.")." );
						}
					}
					else throw new Exception( $errorType . "Attribute 'mode=".$mode."' is not available within ESS DTD." );
				}
				else throw new Exception( $errorType . "Attribute 'type=".$type."' is not available within ESS DTD." );
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
	
	
	
	public function addCategories( 	$type, 					Array $data_ = null, $priority=0 ) { $this->addElement( 'categories', 	$type, null, null,   $data_, $priority ); }
	public function addDates( 		$type, 			$unit, 	Array $data_ = null, $priority=0 ) { $this->addElement( 'dates', 	 	$type, null, $unit,  $data_, $priority ); }
	public function addPlaces( 		$type, 					Array $data_ = null, $priority=0 ) { $this->addElement( 'places', 		$type, null, null,   $data_, $priority ); }
	public function addPrices( 		$type, $mode, 	$unit, 	Array $data_ = null, $priority=0 ) { $this->addElement( 'prices', 	 	$type, $mode, $unit, $data_, $priority ); }
	public function addPeople( 		$type, 					Array $data_ = null, $priority=0 ) { $this->addElement( 'people', 		$type, null, null,   $data_, $priority ); }
	public function addMedia( 		$type, 					Array $data_ = null, $priority=0 ) { $this->addElement( 'media', 		$type, null, null,   $data_, $priority ); }
	public function addRelations( 	$type, 					Array $data_ = null, $priority=0 ) { $this->addElement( 'relations', 	$type, null, null,   $data_, $priority ); }
	public function addAuthors( 	$type, 					Array $data_ = null, $priority=0 ) { $this->addElement( 'authors', 		$type, null, null,   $data_, $priority ); }
	
	
	
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
	
	private function controlNodeContent( $name, $value )
	{
		switch ( strtolower( $name ) ) 
		{
			case 'published' 		:
			case 'updated' 			: return $this->validUTCDate( $value ); break;	
			case 'name' 			: return ( strlen( $value ) > 0 )? true : false; break;
			case 'email' 			: return $this->validEmail( $value ); break;
			case 'uri' 				: return $this->validateURL( $value ); break;
			case 'latitude'			: break;
			case 'longitude'		: break;
			case 'country_code' 	: if ( strlen( $value ) > 0 ) { return ( strlen( $value ) == 2 )? true : false; } break;
			case 'currency' 		: if ( strlen( $value ) > 0 ) { return ( strlen( $value ) == 3 )? true : false; } break;
			default					: return true; break;
		}
		return true; // Be indulgent, consider the node content as valid if it didn't failed in one of the previous cases.
	}
	
	private function validateURL( $url )
	{
		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		
		return ( @eregi( $urlregex, $url ) == true && strlen( $url ) > 10 )? true : false;  
	}
	
	private function validEmail( $email ) 
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
	 * Control the correct syntax of the date in UTC format (ISO)
	 * 
	 * @access	private
	 * @param	String	stringDate is the string formated UTC date (e.g. 2013-10-31T15:30:59Z)
	 * @return	Boolean
	 */
	private function validUTCDate( $stringDate )
	{
		date_default_timezone_set(@date_default_timezone_get());
		
		// is 'T'
		// is 2 x '-'
	}
	
	
 }