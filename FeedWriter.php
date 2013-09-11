<?php
error_reporting(E_ERROR | E_PARSE);
@mb_detect_order( "UTF-8,eucjp-win,sjis-win" );
require_once( "EssDTD.php" );
require_once( 'FeedValidator.php' );
require_once( 'EventFeed.php' );

 /**
  * Universal ESS Feed Writer class
  * Generate ESS Feed v0.9
  *                             
  * @package 	ESSFeedWriter
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed
  */ 
final class FeedWriter
{
	const LIBRARY_VERSION	= '1.2';	// GitHub library versioning control.
	const ESS_VERSION		= '0.9'; 	// ESS Feed version.
	const CHARSET			= 'UTF-8';	// Defines the encoding Chartset for the whole document and the value inserted.
	public $lang			= 'en';		// Default 2 chars language (ISO 3166-1).
	
	public $DEBUG			= false;	// output debug information.
	public $AUTO_PUSH		= true; 	// Defines feed changes have to be submited to ESS Aggregators.
	public $IS_DOWNLOAD		= false;	// Defines if the feed is to be downloaded (with Header: application/ess+xml).
	const EMAIL_UP_TO_DATE	= true;		// Defines if an email is sent to system administrator if the version is not up-to-date.
	const REPLACE_ACCENT	= false;	// if some problems occured durring encoding/decoding the data into UTF8, this parameter set to true force the replacement of åççéñts by accents.
	
	private $channel 		= array();  // Collection of Channel elements.
	private $items			= array();  // Collection of items as object of FeedItem class.					
	const TB				= '   ';	// Display a tabulation (for humans).
	const LN				= '
';										// Display breaklines (for humans).
	
	public static $AGGREGATOR_WS = "http://api.hypecal.com/v1/ess/aggregator.json";
	public static $VALIDATOR_WS  = 'http://api.hypecal.com/v1/ess/validator.json';
	
	
	/**
	 * FeedWriter Class Constructor
	 * 
	 * @access 	public
	 * @param  	String 	[OPTIONAL] 2 chars language (ISO 3166-1) definition for the current feed.
	 * @param  	Array 	[OPTIONAL] array of event's feed tags definition.
	 * @return 	void 
	 */ 
	function __construct( $lang='en', $data_=null )
	{
		$channelDTD = EssDTD::getChannelDTD(); // DTD Array of Channel first XML child elements.
		
		$this->lang = ( strlen($lang)==2 )? strtolower($lang) : $this->lang;
		
		$this->setGenerator( 'ess:php:generator:version:' . self::LIBRARY_VERSION );
		
		$mandatoryRequiredCount = 0;
		$mandatoryCount 		= 0;
		
		if ( $data_ != null )
		{
			if ( @count( $data_ ) > 0 )
			{
				foreach ( $data_ as $key => $el ) 
				{
					switch ( $key ) 
					{
						case 'title':		$this->setTitle( 	  $el ); if ( $channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'link':		$this->setLink(  	  $el ); if ( $channelDTD[ $key ] == true ) $mandatoryCount++;$mandatoryCount++; break; // + element ID
						case 'published':	$this->setPublished(  $el ); if ( $channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'updated':		$this->setUpdated(    $el ); if ( $channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'generator':	$this->setGenerator(  $el ); if ( $channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'rights':		$this->setRights(     $el ); if ( $channelDTD[ $key ] == true ) $mandatoryCount++; break;
						
						default: throw new Exception("Error: XML Channel element < ".$key." > is not defined within ESS DTD." ); break;
					}
				}
				
				foreach ( $channelDTD as $kk => $val ) 
				{
					if ( $val == true && $kk != 'feed' ) 
						$mandatoryRequiredCount++;
				}
				
				if ( $mandatoryRequiredCount != $mandatoryCount || $mandatoryCount == 0 )
				{
					$out = '';
					foreach ( $channelDTD as $key => $m) 
					{
						if ( $m == true ) 
							$out .= "< $key >, ";
					}
					throw new Exception( "Error: All XML Channel's mandatory elements are required: ". $out );
				}
			}
		}
	}
	
	
	private function t( $num )
	{
		$text = "";
		
		for ( $i=1; $i <= $num ; $i++ ) 
			$text .= self::TB;
		
		return $text;
	}
	
	/**
	 * Set a channel element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String  name of the channel tag.
	 * @param   String  content of the channel tag.
	 * @return  void
	 */
	private function setChannelElement( $elementName, $content )
	{
		$this->channel[ $elementName ] = $content;
	}
	
	/**
	 * 	Genarate the ESS Feed On-the-fly or create a file on local server disk.
	 * 	If the feed is generated and record on the server it consume less load on PHP and Database resources. 
	 * 
	 * @access 	public
	 * @return 	void
	 */ 
	public function genarateFeed( $filePath='', $displayResult=true )
	{
		@mb_internal_encoding( self::CHARSET );
		
		if ( $this->DEBUG == false && $displayResult == true )
		{
			ob_end_clean();
			header_remove();
			
			header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
			header( "Cache-Control: no-cache" );
			header( "Pragma: no-cache" );
			header( "Keep-Alive: timeout=1, max=1" );
			
			if ( $this->IS_DOWNLOAD ) { header( 'Content-Type: application/ess+xml; charset=' .self::CHARSET ); }
			else					  { header( 'Content-Type: text/xml; charset=' .self::CHARSET ); }
		}
		
		$feedData = $this->getFeedData();
		
		if ( FeedValidator::isNull( $filePath ) == false && $this->DEBUG == false )
			$this->genarateFeedFile( $filePath, $feedData );
		
		if ( $displayResult == true )
			echo $feedData;
		else 
			return $feedData;
	}
	
	/**
	 * Genarate the ESS File
	 * 
	 * @access 	public
	 * @param	String	Local server path where the feed will be stored.
	 * @param	URL		URL of the same feed but available online. this URL will be used to broadcast your event to events search engines.
	 * @return	void
	 */ 
	public function genarateFeedFile( $filePath='', $feedData=null )
	{
		@mb_internal_encoding( self::CHARSET );
		
		try
		{
			$fp = fopen( $filePath, 'w' );
			
			if ( $fp !== false )
			{
				fwrite( $fp, ( ( $feedData != null )? $feedData : $this->getFeedData() ) );
				fclose( $fp );
			}
		}
		catch( ErrorException $error )
		{
			throw new Exception( "Error: Impossible to generate the ESS file on local server disk: " . $error );
			return;
		}
	}
	
	/**
	 * Get ESS Feed data in String format.
	 * 
	 * @access  public
	 * @return  String
	 */ 
	public function getFeedData()
	{
		$out = "";
		
		$out .= $this->getHead();
		$out .= $this->getChannel();
		$out .= $this->getItems();
		$out .= $this->getEndChannel();
		
		$this->pushToAggregators( '', $out );
		
		return $out;
	}
	
	/**
	 * Create a new EventFeed.
	 * 
	 * @access  public
	 * @return 	Object  instance of EventFeed class
	 */
	public function newEventFeed( Array $arr_= null )
	{
		$newEvent = new EventFeed( null, self::CHARSET, self::REPLACE_ACCENT );
		
		if ( $arr_ )
		{
			if ( @count( $arr_ ) > 0 )
			{
				if ( FeedValidator::isNull( 	 @$arr_['title'] 		) == false ) { $newEvent->setTitle( 		$arr_['title'] 			); }
				if ( FeedValidator::isNull( 	 @$arr_['uri'] 			) == false ) { $newEvent->setUri( 			$arr_['uri'] 			); }
				if ( FeedValidator::isValidDate( @$arr_['published'] 	) == true  ) { $newEvent->setPublished( 	$arr_['published'] 		); } else { $newEvent->setPublished( self::getISODate() ); }
				if ( FeedValidator::isValidDate( @$arr_['updated'] 		) == true  ) { $newEvent->setUpdated( 		$arr_['updated'] 		); } 
				if ( FeedValidator::isNull( 	 @$arr_['access'] 		) == false ) { $newEvent->setAccess( 		$arr_['access'] 		); } else { $newEvent->setAccess( 'PUBLIC' ); }
				if ( FeedValidator::isNull(	 	 @$arr_['description']	) == false ) { $newEvent->setDescription(	$arr_['description'] 	); }
				if ( @count( $arr_['tags'] ) > 0 ) 								 	 { $newEvent->setTags(			$arr_['tags'] 			); }
			}
		}
		return $newEvent;
	}
	
	/**
	 * Add a EventFeed to the main class
	 * 
	 * @access 	public
	 * @param  	Object  instance of EventFeed class
	 * @return 	void
	 */
	public function addItem( $eventFeed )
	{
		$this->items[] = $eventFeed;    
	}
	
	
	public static function getCurrentURL()
	{
		return ( ( stripos( $_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === true )? 'https://' : 'http://' ) . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
	}
	
	
	
	
	
	
	// -------------------------------------
	// -- Getter/Setter Wrapper Functions -------------------------------------------------------------------
	// -------------------------------------
	
	
	
	/**
	 * Set the 'title' channel element
	 * 
	 * @access 	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param  	String  value of 'title' channel tag.
	 * 					Define the language-sensitive feed title. 
	 * 					Should not be longer then 128 characters.
	 * @return  void
	 */
	public function setTitle( $el=NULL )
	{
		$elNane = 'title';
		if ( self::controlChannelElements( $elNane, $el ) )
		{
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );
			
			if ( !isset( $this->channel[ 'id' ] ) || FeedValidator::isNull( $this->channel[ 'id' ] ) ) 
				$this->setId( $el ); 
		}
		else throw new Exception( "Error: '<channel><$elNane>' XML element is mandatory and can not be empty." );
	}
	public function getTitle()
	{
		return $this->channel[ 'title' ];
	}
	
	
	/**
	 * Set the 'link' channel element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String  value of 'link' channel tag.
	 * 					Define the feed URL. 
	 * @return  void
	 */
	public function setLink( $el=NULL )
	{
		$elNane = 'link';
		if ( self::controlChannelElements( $elNane, $el ) )
		{
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );
			
			if ( !isset( $this->channel[ 'id' ] ) || FeedValidator::isNull( $this->channel[ 'id' ] ) ) 
				$this->setId( $el );
		}
		else throw new Exception( "Error: '<channel><$elNane>' XML element is mandatory and can not be empty, it must be a valid URL that specified the location of this feed." );
	}
	public function getLink()
	{
		return $this->channel[ 'link' ];
	}
	
	
	/**
	 * Set the 'id' channel element
	 * 
	 * @access   public
	 * @param    String  value of 'id' channel tag
	 * @return   void
	 */
	public function setId( $el=NULL )
	{
		$elNane = 'id';
		
		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, $this->uuid( $el, 'ESSID:' ) );
		
		else throw new Exception( "Error: '<channel><$elNane>' XML element is mandatory and can not be empty." );
	}
	public function getId()
	{
		return $this->channel[ 'id' ];
	}
	
	
	/**
	 * Set the 'generator' channel element
	 * 
	 * @access   public
	 * @param    String  value of 'generator' channel tag
	 * @return   void
	 */
	public function setGenerator( $el=NULL )
	{
		$elNane = 'generator';
		
		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );
		
		else throw new Exception( "Error: '<channel><$elNane>' XML element, if it is defined, can not be empty." );
	}
	public function getGenerator()
	{
		return $this->channel[ 'generator' ];
	}
	
	
	/**
	 * Set the 'published' channel element
	 * 
	 * @access 	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String  Value of 'published' channel tag.
	 * 					Must be an UTC Date format (ISO 8601).
	 * 					e.g. 2013-10-31T15:30:59+02:00 in Paris or 2013-10-31T15:30:59-08:00 in San Francisco
	 * 
	 * @return  void
	 */
	public function setPublished( $el='now' )
	{
		$elNane = 'published';
		
		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, FeedWriter::getISODate( $el ) );
		
		else throw new Exception( "Error: '<channel><$elNane>' XML element is mandatory and can not be empty." );
	}
	public function getPublished()
	{
		return $this->channel[ 'published' ];
	}
	
	
	/**
	 * Set the 'updated' channel element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param 	String  Value of 'updated' channel tag. 
	 * 					Must be an UTC Date format (ISO 8601).
	 * 					e.g. 2013-10-31T15:30:59Z in Paris or 2013-10-31T15:30:59+0800 in San Francisco
	 * @return  void
	 */
	public function setUpdated( $el='now' )
	{
		$elNane = 'updated';
		
		if ( self::controlChannelElements( $elNane, $el ) )	
			$this->setChannelElement( 'updated', FeedWriter::getISODate( $el ) );
		
		else throw new Exception( "Error: '<channel><$elNane>' XML element, if it is defined, can not be empty." );
	}
	public function getUpdated()
	{
		return $this->channel[ 'updated' ];
	}
	
	
	/**
	 * Set the 'rights' channel element
	 * 
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param 	String  value of 'rights' channel tag.
	 * 					Define the Feed proprietary rights. 
	 * 					Should not be longer then 512 chars.
	 * 
	 * @return void
	 */
	public function setRights( $el=NULL )
	{
		$elNane = 'rights';
		
		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );
		
		else throw new Exception( "Error: '<channel><$elNane>' XML element, if it is defined, can not be empty." );
	}
	public function getRights()
	{
		return $this->channel[ 'rights' ];
	}
	
	
	
	
	
  	/**
  	 * Generates an UUID
	 * 
   	 * @author 	Anis uddin Ahmad <admin@ajaxray.com>
	 * @access	public
  	 * @param 	String  [OPTIONAL] String prefix
  	 * @return 	String  the formated uuid
  	 */
  	public static function uuid( $key=null, $prefix='ESSID:' ) 
	{
		$key = ( FeedValidator::isNull( $key ) )? uniqid( rand() ) : $key;
		$chars = md5( $key );
		$uuid  = substr( $chars, 0,8   ) . '-';
		$uuid .= substr( $chars, 8,4   ) . '-';
		$uuid .= substr( $chars, 12,4  ) . '-';
		$uuid .= substr( $chars, 16,4  ) . '-';
		$uuid .= substr( $chars, 20,12 );

		return $prefix . $uuid;
 	}
	
	/**
  	 * 	Generate or convert a String or an Integer parameter into an ISO 8601 Date format.
	 * 
	 * 	@access 	public
	 * 	@param 		Object	date in seconds OR in convertible String Date (http://php.net/manual/en/function.strtotime.php)
	 *  					to convert in a ISO 8601 Date format: 'Y-m-d\TH:i:sZ'
	 * 	@return  	String
	 */ 
	public static function getISODate( $date=null )
	{
		if ( FeedValidator::isNull( $date ) == false )
		{
			if ( strlen( $date ) >= 8 && !is_int( $date ) )	
			{
				if ( FeedValidator::isValidDate( $date ) )
					return $date;
				else 
				{
					return ( FeedValidator::isNull( strtotime( $date ) ) )? 
						self::getISODate()
						:
						date( DateTime::ATOM, strtotime( $date ) 
					);
				}
			}
			else if ( intval( $date ) > 0 && FeedValidator::isOnlyNumsChars( $date ) )
				return date( DateTime::ATOM, $date );
		}
		else
		{
			$datetime_template = 'Y-m-d\TH:i:s';
			
			// control if PHP is configured with the same timezone then the server
			$timezone_server = @exec( 'date +%:z' );
			$timezone_php	 = date( 'P' );
			
			if ( strlen( $timezone_server ) > 0 && $timezone_php != $timezone_server ) 
				return date( $datetime_template, @exec( "date --date='@" . date( 'U' ) . "'" ) ) . $timezone_server;
			else
			{
				if ( date_default_timezone_get() == 'UTC' ) 
					 $offsetString = 'Z'; // No need to calculate offset, as default timezone is already UTC 
				else 
				{ 
				    $phpTime 		= date( $datetime_template ); 
				    $millis 		= strtotime( $phpTime ); 							// Convert time to milliseconds since 1970, using default timezone 
				    $timezone 		= new DateTimeZone( date_default_timezone_get() ); 	// Get default system timezone to create a new DateTimeZone object 
				    $offset 		= $timezone->getOffset( new DateTime( $phpTime ) ); // Offset in seconds to UTC 
				    $offsetHours 	= round( abs( $offset)/3600); 
				    $offsetMinutes 	= round( ( abs( $offset ) - $offsetHours * 3600 ) / 60 ); 
				    $offsetString 	= ($offset < 0 ? '-' : '+' ) 
		                . ( $offsetHours < 10 ? '0' : '' ) . $offsetHours 
		                . ':' 
		                . ( $offsetMinutes < 10 ? '0' : '' ) . $offsetMinutes; 
				}
				
				return date( $datetime_template, $millis ) . $offsetString;
			}
		}
		
		return addslashes( date( DateTime::ATOM, date( 'U' ) ) );
	}
	
	/**
	 * 	Extract images URL from a blog HTML content.
	 * 
	 * @access	public
	 * @param	String	HTML content that can content fom <img /> XHTML element
	 * @return 	Array	Return an array of the images URL founds.
	 */
	public static function getMediaURLfromHTML( $text=null )
	{
		$media_ = array();
		
		if ( strlen( trim( $text ) ) > 0 )
		{
			$tt = @preg_match_all( '/<(source|iframe|embed|param|img)[^>]+src=[\'"]([^\'"]+)[\'"].*>/i', str_replace( '><', '>
<', FeedValidator::removeBreaklines( $text,'
' ) ), $matches );
			
			if ( $tt > 0 && @count( $matches[ 2 ] ) > 0 ) 
			{
				foreach ( $matches[ 2 ] as $i => $value ) 
				{
					$sb1 = array();
					$sb2 = array();
					$sb3 = array();
					$sb4 = array();
					if ( FeedValidator::isValidURL( $value ) )
					{
						$simple_tag = str_replace( "'","\"",strtolower( stripcslashes( $matches[ 0 ][ $i ] ) ) );
						
						$sb1 = explode( 'title="', $simple_tag );
						if ( @count( $sb1 ) > 1 ) 
							$sb3 = explode( '"', $sb1[1] );
						
						$sb2 = explode( 'alt="', $simple_tag );
						if ( @count( $sb2 ) > 1 ) 
							$sb4 = explode( '"', $sb2[1] );
				
						$media_type = FeedValidator::getMediaType( $value );
						
						array_push( 
							$media_, 
							array(
								'uri' 	=> $value, 
								'type'	=> $media_type,
								'name'	=> ( ( strlen( @$sb3[ 0 ] ) > 0 )? $sb3[ 0 ] : ( ( strlen( @$sb4[ 0 ] ) > 0 )? $sb4[ 0 ] : $media_type . " - " . $i ) )
							) 
						);
					}
				}
			}
			
			// Strip HTML content and analyzed individual world to find URL in the text. (CF: MediaWiki content).
			$text_split = explode( ' ', FeedValidator::getOnlyText( $text, self::CHARSET ) );
			
			if ( @count( $text_split ) > 0 )
			{
				foreach ( $text_split as $value ) 
				{
					foreach ( array( 'image', 'sound', 'video' ) as $media_type ) 
					{
						if ( FeedValidator::isValidURL( $value ) )
						{
							if ( FeedValidator::isValidMediaURL( $value, $media_type ) )
							{
								if ( !in_array( $value, $media_ ) )
								{
									array_push( $media_, array(
										'uri' 	=> $value, 
										'type'	=> $media_type,
										'name'	=> $media_type
									) );
								}
							}
						}
					}
				}
			}
		}
		return $media_;
	}
	
	
	
	private static function controlChannelElements( $elmName, $val=null  )
	{
		switch ( $elmName )
		{
			default	 		 : return ( FeedValidator::isNull( 		$val ) )? false : true;  break;
			case 'link' 	 : return ( FeedValidator::isValidURL( 	$val ) )? true  : false; break;
			case 'updated'	 :
			case 'published' : return ( FeedValidator::isValidDate( $val ) )? true  : FeedValidator::isValidDate( self::getISODate( $val ) ); break;
		}
	}
	
	
	
	/**
	 * Prints the xml and ESS namespace
	 * 
	 * @access   private
	 * @return   String
	 */
	private function getHead()
	{
		$out  = '<?xml version="1.0" encoding="'.self::CHARSET.'"?>' . self::LN;
		$out .= '<!DOCTYPE ess PUBLIC "-//ESS//DTD" "http://essfeed.org/history/'.urlencode( self::ESS_VERSION ).'/index.dtd">' . self::LN;
		$out .= $this->getComment();
		$out .= '<ess xmlns="http://essfeed.org/history/'.urlencode( self::ESS_VERSION ).'/" version="'. urlencode( self::ESS_VERSION ) .'" lang="'. $this->lang .'">' . self::LN;
		
		return $out;
	}
	
	private function getComment()
	{
		return '<!--' . self::LN . 
			$this->t(1) . 'ESS Feed (Event Standard Syndication)' . self::LN .
			self::LN .
			$this->t(1) . 'Your events are now available to any software that read ESS format, example:' . self::LN .
			$this->t(2) . 'http://hypecal.com          '.$this->t(1).' (Events search engine)' . self::LN .
			$this->t(2) . 'http://wp-events-plugin.com '.$this->t(1).' (Wordpress plugin)' . self::LN .
			self::LN .
			$this->t(1) . 'Standard info:   '.$this->t(1).' http://essfeed.org/' . self::LN .
			$this->t(1) . 'Other libraries: '.$this->t(1).' https://github.com/essfeed/' . self::LN .
			self::LN .
		'-->' . self::LN;
	}
	
	/**
	 * Closes the open tags at the end of file
	 * 
	 * @access   private
	 * @return   String
	 */
	private function getEndChannel()
	{
		return $this->t(1) . "</channel>" . self::LN . "</ess>". self::LN;
	}

	/**
	 * Creates a single node as xml format
	 * 
	 * @access   private
	 * @param    String  name of the tag
	 * @param    Mixed   tag value as string or array of nested tags in 'tagName' => 'tagValue' format
	 * @param    Array   Attributes(if any) in 'attrName' => 'attrValue' format
	 * @return   String  formatted XML tag
	 */
	private function makeNode( $tagName, $tagContent, $attributes = null )
	{
		$CDATA = array( 'description' ); // Names of the tags to be displayed with <[CDATA[...]]>.
		        
		$nodeText = '';
		$attrText = '';

		if ( is_array( $attributes ) )
		{
			foreach ( $attributes as $key => $value ) 
			{
				if ( @strlen( $value ) > 0 )
					$attrText .= " $key=\"$value\" ";
			}
		}
		
		$nodeText .= $this->t(2) . ( ( in_array( $tagName, $CDATA ) )? "<{$tagName}{$attrText}>" . self::LN . $this->t(3) . "<![CDATA[" . self::LN : "<{$tagName}{$attrText}>" );
		 
		if ( is_array( $tagContent ) ) // tag
		{ 
			foreach ( $tagContent as $key => $value ) 
			{
				if ( isset( $value ) || $value == 0 )
				{
					$nodeText .= $this->t(4) . $this->makeNode( $key, 
						( ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $value, $this->CHARSET ) : $value )
					);
				}
			}
		}
		else
		{
			if ( in_array( $tagName, $CDATA ) || 
				 $tagName == 'published' || 
				 $tagName == 'updated' ||
				 $tagName == 'value' )			{ $nodeText .= $tagContent; }
			else if ( $tagName == 'start' ) 	{ $nodeText .= self::getISODate( $tagContent ); }
			else if ($tagName == 'link' || 
					 $tagName == 'uri' )		{ $nodeText .= htmlentities( $tagContent ); }
			else								{ $nodeText .= FeedValidator::noAccent( $tagContent ); }		 
		}           
			
		$nodeText .= ( ( in_array( $tagName, $CDATA ) )? self::LN .  $this->t(3) . "]]>" . self::LN . $this->t(3) . "</$tagName>" : "</$tagName>" );

		return $nodeText . self::LN;
	}
	
	/**
	 * Get Channel XML content in String format
	 * 
	 * @access   private
	 * @return   String
	 */
	private function getChannel()
	{
		$out = $this->t(1) .'<channel>' . self::LN;
		
		foreach( $this->channel as $k => $v )
			$out .= $this->makeNode( $k, $v );
	
		return $out;
	}
	
	/**
	 * Get feed's items XML content in String format
	 * 
	 * @access   private
	 * @return   String
	 */
	private function getItems()
	{
		$out = "";
		
		foreach ( $this->items as $item ) 
		{
			$thisRoots = $item->getRoots();
			$thisItems = $item->getElements();
			
			$out .= $this->startFeed();
			
			if ( @count( $thisRoots ) > 0 )
			{
				foreach ( $thisRoots as $elm => $val )
				{
					if ( strlen( $elm ) > 0 && ( @strlen( $val ) > 0 || @count( $val ) > 0 ) )
					{
						if ( $elm != 'tags' )
							$out .= $this->t(1) .$this->makeNode( $elm, $val );
						else
						{
							$out .= $this->t(3) . "<tags>" . self::LN;
						
							foreach( $val as $tag )
								$out .= $this->t(2) . $this->makeNode( 'tag', ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $tag, $this->CHARSET ) : $tag );
						
							$out .= $this->t(3) . "</tags>" . self::LN;
						}
					}
				}
			}
			
			if ( @count( $thisItems ) > 0 )
			{
				foreach ( $thisItems as $key => $val )
				{
					if ( @count( $thisItems[ $key ] ) > 0 && @strlen( $key ) > 0 )
					{
						$out .= $this->t(3) . "<{$key}>" . self::LN;
						
						foreach ( $val as $position => $feedItem ) 
						{
							$out .= $this->t(4) . "<item type='". strtolower( $feedItem[ 'type' ] ) ."'".
								( ( isset( $feedItem[ 'unit' ]			) && strlen( @$feedItem[ 'unit' ] 			) > 0 )? " unit='".			strtolower( $feedItem[ 'unit' ]				) . "'" : '' ) .
								( ( isset( $feedItem[ 'mode' ]			) && strlen( @$feedItem[ 'mode' ] 		 	) > 0 )? " mode='".			strtolower( $feedItem[ 'mode' ]				) . "'" : '' ) .
								( ( isset( $feedItem[ 'selected_day' ]	) && strlen( @$feedItem[ 'selected_day' ] 	) > 0 )? " selected_day='".	strtolower( $feedItem[ 'selected_day' ]		) . "'" : '' ) .
								( ( isset( $feedItem[ 'selected_week' ]	) && strlen( @$feedItem[ 'selected_week' ]	) > 0 )? " selected_week='".strtolower( $feedItem[ 'selected_week' ]	) . "'" : '' ) .
								( ( intval( @$feedItem[ 'interval' ]		) > 1 )? " interval='".			intval( $feedItem[ 'interval' ]			) . "'" : '' ) .
								( ( intval( @$feedItem[ 'limit' ] 			) > 0 )? " limit='".			intval( $feedItem[ 'limit' ]			) . "'" : '' ) .
								( ( intval( @$feedItem[ 'moving_position' ]	) > 0 )? " moving_position='".	intval( $feedItem[ 'moving_position' ]	) . "'" : '' ) .
								( ( intval( @$feedItem[ 'priority' ]		) > 0 )? " priority='".			intval( $feedItem[ 'priority' ] 		) . "'" : " priority='".( $position + 1 ) . "'" ).
							">" . self::LN;
							
							if ( $key == 'prices' && ( $feedItem[ 'mode' ] == 'free' || $feedItem[ 'mode' ] == 'invitation' ) )
							{
								$out .= $this->t(3) . $this->makeNode( 'name', $feedItem['content']['name'] );
								$out .= $this->t(3) . $this->makeNode( 'value', 0 );
							}
							else
							{
								foreach ( $feedItem['content'] as $elm => $feedElm ) 
								{
									$out .= $this->t(3) . $this->makeNode( $elm, $feedElm );
								}
							}
							$out .= $this->t(4) . "</item>" . self::LN;
						}
						$out .= $this->t(3) . "</{$key}>" . self::LN;
					}
				}
			}
			$out .= $this->endFeed();
		}
		return $out;
	}
	
	/**
	 * Create the starting tag of feed
	 * 
	 * @access   private
	 * @return   String
	 */
	private function startFeed()
	{
		return $this->t(2) . '<feed>' . self::LN;
	}
	
	/**
	* Closes feed item tag
	* 
	* @access   private
	* @return   String
	*/
	private function endFeed()
	{
		return $this->t(2) . '</feed>' . self::LN;
	}
	
	/**
	 *	Send email to server admin in case of specific problem.
	 * 
	 * 	@access private
	 * 	@param	String	Receiver, or receivers of the mail.
	 * 	@param	String	Subject of the email to be sent.
	 * 	@param	String	Message to be sent in HTML format.
	 * 	@return Boolean	bool true if the mail was successfully accepted for delivery, false otherwise.
	 */
	private static function sendEmail( $email=null, $subject=null, $message=null )
	{
		if ( isset( $email ) || isset( $subject ) || isset( $message ) )
		{
			$headers  = "From: " . 		strip_tags( $email ) . "\r\n";
			$headers .= "Reply-To: ". 	strip_tags( $email ) . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=" . self::CHARSET . "\r\n";
			
			$msg = "<html><body>";
			$msg .= $message;
			$msg .= '</body></html>';
			
			return mail( $email, $subject, $msg, $headers );
		}
		return false;
	}
	
	private static function htmlvardump()
 	{
		ob_start(); 
		call_user_func_array( 'var_dump', func_get_args() ); 
		return ob_get_clean();
  	} 
	
	private static function get_tmp_path()
	{
		return ( ( strlen( sys_get_temp_dir() ) >= 0 )? sys_get_temp_dir() : "/tmp" );
	}
	
	public function pushToAggregators( $feedURL='', $feedData=null )
	{
		if ( $this->AUTO_PUSH )
		{
			$ch = @curl_init();
			
			if ( $ch !== false )
			{
				$post_data = array( 
					'LIBRARY_VERSION'	=> self::LIBRARY_VERSION,
					'REMOTE_ADDR' 		=> @$_SERVER[ 'REMOTE_ADDR' ],
					'SERVER_ADMIN'		=> @$_SERVER[ 'SERVER_ADMIN' ],
					'PROTOCOL'			=> ( ( stripos( @$_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === true )? 'https://' : 'http://' ),
					'HTTP_HOST'			=> @$_SERVER[ 'HTTP_HOST' ],
					'REQUEST_URI'		=> @$_SERVER[ 'REQUEST_URI' ],
					
					// if mod_geoip is installed. (http://dev.maxmind.com/geoip/mod_geoip2) very useful, you should try :)
					'GEOIP_LATITUDE' 	=> @$_SERVER[ 'GEOIP_LATITUDE' ],
  					'GEOIP_LONGITUDE'	=> @$_SERVER[ 'GEOIP_LONGITUDE' ]
				);
				
				if ( $feedData == null && FeedValidator::isValidURL( $feedURL ) )
					$post_data['feed'] = $feedURL;
				else 
					$post_data['feed_file'] = $feedData; 
				
				curl_setopt( $ch, CURLOPT_URL, 				self::$AGGREGATOR_WS );
				curl_setopt( $ch, CURLOPT_POSTFIELDS,  		$post_data );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 	1 );
				curl_setopt( $ch, CURLOPT_VERBOSE, 			1 );
				curl_setopt( $ch, CURLOPT_FAILONERROR, 		1 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 			20 );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 	0 );
				curl_setopt( $ch, CURLOPT_COOKIEJAR,  		self::get_tmp_path().'/cookies' );
				curl_setopt( $ch, CURLOPT_REFERER, 			@$_SERVER[ 'REQUEST_URI' ] );
				
				$response = json_decode( curl_exec( $ch ), true );
				$r = @$response['result'];
				$isOK = @isset( $r['result'] )? true : false;
				$isVersionUptoDate = ( (String)$r['version'] != (String)self::LIBRARY_VERSION && $isOK )? false : true;
				
				if ( $this->DEBUG == true)
				{
					$DARK_RED = '#ff0000;';
					
					$bg_color = ( $isOK )? '#91ff86' : '#ffd5d5';
					$mn_color = ( $isOK )? '#168c0a' : $DARK_RED;
					
					echo "<div style='background-color:$bg_color;color:$mn_color;border:1px solid $mn_color;width:95%;padding:10px;font-size:14px;margin:10px;'>".
						( ( $isVersionUptoDate == false )?
							"<h3 style='color:$DARK_RED;font-size:20px;border:1px dotted $DARK_RED;padding:10px;margin:5px;'>The PHP-ESS library have been updated.<br/>".
								"Download the last GitHub version (".$r['version'].") : <a target='_blank' href='https://github.com/essfeed/php-ess'  style='color:#ff0000;'>https://github.com/essfeed/php-ess</a>".
							"</h3>" .
							"<br/>"
							:''
						).
						"Set the DEBUG attribute to false to remove this warning message.".
						"<br/><br/>".
						"$ newFeed = new FeedWriter();<br/>".
						"<b>$ newFeed->DEBUG = false;</b>".
						"<br/><br/>".
						self::htmlvardump( $response ) .
					"</div>";
				}
				
				if ( $isVersionUptoDate == false && self::EMAIL_UP_TO_DATE && FeedValidator::isValidEmail( $_SERVER[ 'SERVER_ADMIN' ] ) )
				{
					self::sendEmail( 
						$_SERVER[ 'SERVER_ADMIN' ], 
						"Update your ESS Library on " . $_SERVER[ 'HTTP_HOST' ],
						"<h3>The library you used on your website ". $_SERVER[ 'HTTP_HOST' ] ." is not up-to-date</h3>".
						"<p style='background:#000;color:#FFF;padding:6px;'>".$_SERVER['DOCUMENT_ROOT'] . "</p>" .
						"You can upload the lastest version in <a href='https://github.com/essfeed/php-ess/'>https://github.com/essfeed/php-ess/</a>"
					);
				}
			} 
			else 
			{
				if ( $feedData == null && FeedValidator::isValidURL( $feedURL ) )
					@exec( "wget -q \"" . self::$AGGREGATOR_WS . "?feed=" . $feedURL . "\"" );
			}
			
		}
	}
}