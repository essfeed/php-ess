<?php
 /**
 * Univarsal ESS Feed Writer class
 * Genarate ESS 0.91 Feed
 *                             
 * @package     ESSFeedWriter
 * @author      Brice Pissard
 * @link        http://eventstandardsyndication.org
 */
final class FeedWriter
{
	// --- [ EDITABLE ] --------------------------------------------------------
	private $eventAggregators = array(
		'http://api.hypecal.com/v1/ess/aggregator.json',
		'http://...' // you can add any ESS aggregators where you want your event to be published or updated at any changes.
	); 
	// ------------------------------------------------------------------------- 
	
 	private $version	= 0.91; 	// ESS Feed version 
 	private $lang		= 'en';		// Default language
	private $channel 	= array();  // Collection of channel elements
	private $items		= array();  // Collection of items as object of FeedItem class.
	private $CDATA  	= array( 'description' );  	// The tag names which have to encoded as CDATA
	private $DEBUG		= false;
	
	private $channelDTD = array(
		'title'     	=> true,
		'link'			=> true,
		'published'		=> true,
		'updated'		=> false,
		'generator'		=> false,
		'rights'		=> false
	);
	
	/**
	 * FeedWriter Class Constructor
	 * 
	 * @access   public
	 * @param    String [OPTIONAL] 2 chars language definition for the feed.
	 * @param    Array 	[OPTIONAL] array of Event Feed tags definition.
	 * @return   Void 
	 */ 
	function __construct( $lang='en', $data_=null )
	{
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
						case 'link':		$this->setLink(  	  $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'published':	$this->setPublished(  $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'updated':		$this->setUpdated(    $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'generator':	$this->setGenerator(  $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						case 'rights':		$this->setRights(     $el ); if ( $this->channelDTD[ $key ] == true ) $mandatoryCount++; break;
						
						default: throw new Exception("Error: Channel element '".$el."' unauthorized", 1 ); break;
					}
				}
				
				foreach ( $this->channelDTD as $val ) 
				{
					if ( $val == true ) $mandatoryRequiredCount++;
				}
				
				if ( $mandatoryRequiredCount != $mandatoryCount || $mandatoryCount == 0 )
				{
					throw new Exception( "Error: All Channel mandatory elements are required '".explode( $this->channelDTD )."'", 1 );
				}
			}
		}
	}

	/**
	 * Set a channel element
	 * 
	 * @access   public
	 * @param    String  name of the channel tag
	 * @param    String  content of the channel tag
	 * @return   Void
	 */
		private function setChannelElement( $elementName, $content )
	{
		$this->channel[ $elementName ] = $content ;
	}
	
	/**
	 * Genarate the ESS file
	 * 
	 * @access   public
	 * @return   void
	 */ 
	public function genarateFeed()
	{
		if ( $this->DEBUG == false )
		{
			header( "Content-type: text/xml" );
		}
		
		echo $this->getHead();
		echo $this->getChannel();
		echo $this->getItems();
		echo $this->getEndChannel();
	}
	
	/**
	 * Create a new EventFeed.
	 * 
	 * @access   public
	 * @return   Object  instance of EventFeed class
	 */
	public function newEventFeed()
	{
		return new EventFeed();
	}
	
	/**
	 * Add a EventFeed to the main class
	 * 
	 * @access   public
	 * @param    Object  instance of EventFeed class
	 * @return   Void
	 */
	public function addItem( $eventFeed )
	{
		$this->items[] = $eventFeed;    
	}
	
	
	
	
	
	
	// Wrapper functions -------------------------------------------------------------------
	
	/**
	 * Set the 'title' channel element
	 * 
	 * @access   public
	 * @param    String  value of 'title' channel tag
	 * @return   Void
	 */
	public function setTitle( $el=NULL )
	{
		if ( $el != NULL ) $this->setChannelElement( 'title', $el );
	}
	
	/**
	 * Set the 'link' channel element
	 * 
	 * @access   public
	 * @param    String  value of 'link' channel tag
	 * @return   Void
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
	 * @return   Void
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
	 * @return   Void
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
	 * @access   public
	 * @param    String  value of 'published' channel tag
	 * @return   Void
	 */
	public function setPublished( $el=NULL )
	{
		if ( $el != NULL ) $this->setChannelElement( 'published', $el );
	}
	
	/**
	 * Set the 'updated' channel element
	 * 
	 * @access   public
	 * @param    String  value of 'updated' channel tag
	 * @return   Void
	 */
	public function setUpdated( $el=NULL )
	{
		if ( $el != NULL ) $this->setChannelElement( 'updated', $el );
	}
	
	/**
	 * Set the 'rights' channel element
	 * 
	 * @access   public
	 * @param    String  value of 'rights' channel tag
	 * @return   Void
	 */
	public function setRights( $el=NULL )
	{
		if ( $el != NULL ) $this->setChannelElement( 'rights', $el );
	}
	
	
	
	
	
	
  	/**
  	 * Genarates an UUID
	 * 
   	 * @author     Anis uddin Ahmad <admin@ajaxray.com>
  	 * @param      string  an optional prefix
  	 * @return     string  the formated uuid
  	 */
  	public static function uuid( $key = null, $prefix = '' ) 
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
  	 * Genarate current String date in ISO standard format
	 * 
	 * @access   private
	 * @return   String
	 */ 
	public static function getISODate()
	{
		return urldecode( date( "Y-m-d" ). "T" . date( "H:i:s" ) . "Z" );
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
		$out .= '<ess xmlns="http://eventstandardsyndication.org/history/'.$this->version.'" version="'. $this->version .'" lang="'. $this->lang .'">'; // . PHP_EOL;
		
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
			foreach ($tagContent as $key => $value) 
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
			
			//var_dump( $thisItems );
			
			if ( @count( $thisItems ) > 0 )
			{
				foreach ( $thisItems as $key => $val )
				{
					if ( @count( $thisItems[ $key ] ) > 0 )
					{
						$out .= "<{$key}>";
						
						foreach ( $thisItems[ $key ] as $feedItem ) 
						{
							//var_dump( $feedItem );
							
							$out .= $this->makeNode(
								$feedItem[ 'name' ], 
								$feedItem[ 'content' ], 
								$feedItem[ 'attributes' ]
							); 
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
	
	
}

function __autoload( $class_name ) 
{
	require_once $class_name . '.php';
}