<?php
error_reporting(E_ERROR | E_PARSE);
require_once( 'EssDTD.php' );

 /**
  * Universal ESS Feed Writer class
  * Generate ESS Feed v0.9
  *                             
  * @package     ESSFeedWriter
  * @author      Brice Pissard
  * @link        http://essfeed.org
  */ 
final class FeedWriter
{
	private $version	= 0.9; 						// ESS Feed version 
 	private $lang		= 'en';						// Default language
	private $channel 	= array();  				// Collection of channel elements
	private $items		= array();  				// Collection of items as object of FeedItem class.
	private $channelDTD	= array();					// DTD Array of Channel first XML child elements 
	private $CDATA  	= array( 'description' );  	// The tag names which have to encoded as CDATA
	private $DEBUG		= false;					// output debug information
	private $autoPush	= true; 					// Auto-push changes to ESS Feed Aggregators.
	
	/**
	 * FeedWriter Class Constructor
	 * 
	 * @access 	public
	 * @param  	String 	[OPTIONAL] 2 chars language definition for the current feed.
	 * @param  	Array 	[OPTIONAL] array of event's feed tags definition.
	 * @return 	void 
	 */ 
	function __construct( $lang='en', $data_=null )
	{
		$this->channelDTD = EssDTD::getChannelDTD();
		
		$this->lang = ( strlen($lang)==2 )? strtolower($lang) : $this->lang;
		
		$this->setGenerator( 'ess:php:generator:version:' . $this->version );
		
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
						case 'title':		$this->setTitle( 	  $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'link':		$this->setLink(  	  $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++;$mandatoryCount++; break; // + element ID
						case 'published':	$this->setPublished(  $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'updated':		$this->setUpdated(    $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'generator':	$this->setGenerator(  $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'rights':		$this->setRights(     $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						
						default: throw new Exception("Error: XML Channel element <".$key."> is not defined within ESS DTD." ); break;
					}
				}
				
				foreach ( $this->channelDTD as $kk => $val ) 
				{
					if ( $val == true && $kk != 'feed' ) $mandatoryRequiredCount++;
				}
				
				if ( $mandatoryRequiredCount != $mandatoryCount || $mandatoryCount == 0 )
				{
					$out = '';
					foreach ( $this->channelDTD as $key => $m) 
					{
						if ( $m == true ) $out .= "<$key>, ";
					}
					throw new Exception( "Error: All XML Channel's mandatory elements are required: ". $out );
				}
			}
		}
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
		$this->channel[ $elementName ] = $content ;
	}
	
	/**
	 * Genarate the ESS Feed
	 * 
	 * @access 	public
	 * @return 	void
	 */ 
	public function genarateFeed()
	{
		if ( $this->DEBUG == false )
		{
			header( "Content-type: text/xml" );
		}
		
		echo $this->getFeedData();
	}
	
	/**
	 * Genarate the ESS File
	 * 
	 * @access 	public
	 * @param	String	Local server path where the feed will be stored.
	 * @param	URL		URL of the same feed but available online. this URL will be used to broadcast your event to events search engines.
	 * @return	void
	 */ 
	public function genarateFeedFile( $filePath, $feedURL )
	{
		$this->setLink( $feedURL );
		
		try
		{
			$fp = fopen( $filePath, 'w' );
			fwrite( $fp, $this->getFeedData() );
			fclose( $fp );
		}
		catch( ErrorException $error )
		{
			throw new Exception( "Error: Impossible to generate file in local disk: " . $error );
			return;
		}
		
		$this->pushToAggregators( $feedURL );
	}
	
	/**
	 * Get ESS Feed data in String format.
	 * 
	 * @access  private
	 * @return  String
	 */ 
	private function getFeedData()
	{
		$out = "";
		
		$out .= $this->getHead();
		$out .= $this->getChannel();
		$out .= $this->getItems();
		$out .= $this->getEndChannel();
		
		$this->pushToAggregators('',$out);
		
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
		$newEvent = new EventFeed();
		
		if ( $arr_ )
		{
			if ( @count( $arr_ ) > 0 )
			{
				if ( FeedValidator::isNull( $arr_['title'] 			) == false ) { $newEvent->setTitle( 		$arr_['title'] 			); }
				if ( FeedValidator::isNull( $arr_['uri'] 			) == false ) { $newEvent->setUri( 			$arr_['uri'] 			); }
				if ( FeedValidator::isValidDate( $arr_['published'] ) == true  ) { $newEvent->setPublished( 	$arr_['published'] 		); } else { $newEvent->setPublished(FeedWriter::getISODate() 	); }
				if ( FeedValidator::isValidDate( $arr_['updated'] 	) == true  ) { $newEvent->setUpdated( 		$arr_['updated'] 		); } else { $newEvent->setUpdated(	FeedWriter::getISODate() 	); }
				if ( FeedValidator::isNull( $arr_['access'] 		) == false ) { $newEvent->setAccess( 		$arr_['access'] 		); } else { $newEvent->setAccess( 	'PUBLIC' 					); }
				if ( FeedValidator::isNull( $arr_['description']	) == false ) { $newEvent->setDescription(	$arr_['description'] 	); }
				if ( @count( $arr_['tags'] ) > 0 ) 								 { $newEvent->setTags(			$arr_['tags'] 			); }
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
	
	
	
	
	
	
	// Wrapper functions -------------------------------------------------------------------
	
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
		if ( $el != NULL ) $this->setChannelElement( 'title', $el );
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
		if ( $el != NULL ) 
		{
			$this->setChannelElement( 'link', $el );
			$this->setId( $el );
		}
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
		if ( $el != NULL )
		{
			$this->setChannelElement( 'id', $this->uuid( $el, 'ESSID:' ) );
		}
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
		if ( $el != NULL ) 
		{
			$this->setChannelElement( 'generator', $el );
		}
	}
	
	/**
	 * Set the 'published' channel element
	 * 
	 * @access 	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * 
	 * @param   String  Value of 'published' channel tag.
	 * 					Must be an UTC Date format (ISO 8601).
	 * 					e.g. 2013-10-31T15:30:59Z in Paris or 2013-10-31T15:30:59+0800 in San Francisco
	 * 
	 * @return  void
	 */
	public function setPublished( $el=NULL )
	{
		if ( $el != NULL ) $this->setChannelElement( 'published', $el );
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
	public function setUpdated( $el=NULL )
	{
		if ( $el != NULL ) $this->setChannelElement( 'updated', $el );
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
		if ( $el != NULL ) $this->setChannelElement( 'rights', $el );
	}
	
	
	
	
	
	
  	/**
  	 * Generates an UUID
	 * 
   	 * @author 	Anis uddin Ahmad <admin@ajaxray.com>
	 * @access	public
  	 * @param 	String  [OPTIONAL] String prefix
  	 * @return 	String  the formated uuid
  	 */
  	public static function uuid( $key = null, $prefix = 'ESSID:' ) 
	{
		$key = ( $key == null )? uniqid( rand() ) : $key;
		$chars = md5( $key );
		$uuid  = substr( $chars, 0,8   ) . '-';
		$uuid .= substr( $chars, 8,4   ) . '-';
		$uuid .= substr( $chars, 12,4  ) . '-';
		$uuid .= substr( $chars, 16,4  ) . '-';
		$uuid .= substr( $chars, 20,12 );

		return $prefix . $uuid;
 	}
	
	/**
  	 * Generate current String date in ISO standard format
	 * 
	 * @access   public
	 * @return   String
	 */ 
	public static function getISODate( $date=null )
	{
		return ( $date == null )? urldecode( date( "Y-m-d" ). "T" . date( "H:i:s" ) . "Z" ) : date( "Y-m-d\TH:i:s", $date ) . "Z";
	}
	
	/**
	 * Prints the xml and ESS namespace
	 * 
	 * @access   private
	 * @return   String
	 */
	private function getHead()
	{
		$out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$out .= '<ess xmlns="http://essfeed.org/history/'.$this->version.'" version="'. $this->version .'" lang="'. $this->lang .'">'; // . PHP_EOL;
		
		return $out;
	}
	
	/**
	 * Closes the open tags at the end of file
	 * 
	 * @access   private
	 * @return   String
	 */
	private function getEndChannel()
	{
		//echo '</channel>' . PHP_EOL . '</ess>';
		return "</channel></ess>";
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
		$nodeText = '';
		$attrText = '';

		if ( is_array( $attributes ) )
		{
			foreach ( $attributes as $key => $value ) 
			{
				$attrText .= " $key=\"$value\" ";
			}
		}
		
		$nodeText .= (in_array($tagName, $this->CDATA))? "<{$tagName}{$attrText}><![CDATA[" : "<{$tagName}{$attrText}>";
		 
		if ( is_array( $tagContent ) )
		{ 
			foreach ( $tagContent as $key => $value ) 
			{
				$nodeText .= $this->makeNode( $key, $value );
			}
		}
		else
		{
			$nodeText .= ( in_array( $tagName, $this->CDATA ) )? $tagContent : htmlentities( $tagContent );
		}           
			
		$nodeText .= ( in_array( $tagName, $this->CDATA ) )? "]]></$tagName>" : "</$tagName>";

		return $nodeText;
	}
	
	/**
	 * Get Channel XML content in String format
	 * 
	 * @access   private
	 * @return   String
	 */
	private function getChannel()
	{
		$out = '<channel>';
		
		foreach( $this->channel as $k => $v ) 
		{
			$out .= $this->makeNode( $k, $v );
		}
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
						{
							$out .= $this->makeNode( $elm, $val );
						}
						else
						{
							$out .= "<tags>";
							foreach( $val as $tag )
							{
								$out .= $this->makeNode( 'tag', $tag );
							}
							$out .= "</tags>";
						}
					}
				}
			}
			
			if ( @count( $thisItems ) > 0 )
			{
				foreach ( $thisItems as $key => $val )
				{
					if ( @count( $thisItems[ $key ] ) > 0 && strlen( $key ) > 0 )
					{
						$out .= "<{$key}>";
						
						foreach ( $val as $position => $feedItem ) 
						{
							$out .= "<item type='". strtolower( $feedItem[ 'type' ] ) ."'".
								( ( isset( $feedItem[ 'unit' ]			) )? " unit='".			strtolower( $feedItem[ 'unit' ]			) . "'" : '' ) .
								( ( isset( $feedItem[ 'mode' ]			) )? " mode='".			strtolower( $feedItem[ 'mode' ]			) . "'" : '' ) .
								( ( isset( $feedItem[ 'padding_day' ]	) )? " padding_day='".	strtolower( $feedItem[ 'padding_day' ]	) . "'" : '' ) .
								( ( isset( $feedItem[ 'padding_week' ]	) )? " padding_week='".	strtolower( $feedItem[ 'padding_week' ]	) . "'" : '' ) .
								( ( intval( @$feedItem[ 'padding' ]	) > 1 )? " padding='".		intval( 	$feedItem[ 'padding' ]		) . "'" : '' ) .
								( ( intval( @$feedItem[ 'limit' ] 	) > 0 )? " limit='".		intval( 	$feedItem[ 'limit' ]		) . "'" : '' ) .
								( ( intval( @$feedItem[ 'priority' ]) > 0 )? " priority='".		intval( 	$feedItem[ 'priority' ] 	) . "'" : " priority='".( $position + 1 ) . "'" ).
							">";
							
							foreach ( $feedItem['content'] as $elm => $feedElm ) 
							{
								$out .= $this->makeNode( $elm, $feedElm	);
							}
							
							$out .= "</item>";
						}
						$out .= "</{$key}>";
					}
				}
			}
			$out .= $this->endFeed();
		}
		return $out;
	}
	
	/**
	 * Make the starting tag of feed
	 * 
	 * @access   private
	 * @return   String
	 */
	private function startFeed()
	{
		return '<feed>';
	}
	
	/**
	* Closes feed item tag
	* 
	* @access   private
	* @return   String
	*/
	private function endFeed()
	{
		return '</feed>';
	}
	
	private function isValidURL( $url )
	{
	    return preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url );
	}
	
	private function pushToAggregators( $feedURL, $feedData=null )
	{
		if ( $this->autoPush == true )
		{
			$ch = @curl_init();
			if ( $ch )
			{
				$post_data = array( 'ip' => $_SERVER[ 'REMOTE_ADDR' ] );
				
				if ( $feedData == null && $this->isValidURL( $feedURL ) )
					$post_data['feed'] = $feedURL;
				else 
					$post_data['feed_file'] = $feedData; 
				
				curl_setopt($ch, CURLOPT_URL, 				"http://api.hypecal.com/v1/ess/aggregator.json");
				curl_setopt($ch, CURLOPT_POSTFIELDS,  		$post_data );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	1 );
				curl_setopt($ch, CURLOPT_VERBOSE, 			1 );
				
				$response = json_decode( curl_exec( $ch ), true );
				
				if ( $this->DEBUG == true)
				{
					var_dump( $response );
				}
			} 
			else 
			{
				if ( $feedData == null && $this->isValidURL( $feedURL ) )
				{
					@exec( "wget -q \"".$url.$feedURL."?feed=".$feedURL."\"" );
				}
			}
			
		}
	}
}

function __autoload( $class_name ) 
{
	require_once $class_name . '.php';
}