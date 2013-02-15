<?php
 /**
 * Univarsal ESS Feed Writer class
 * Genarate ESS 0.91 Feed
 *                             
 * @package     ESSFeedWriter
 * @author      Brice Pissard
 * @link        http://eventstandardsyndication.org
 */
class FeedWriter
{
 	private $version   		= 0.91; 	// ESS Feed version 
 	private $lang			= 'en';		// Default language
	private $channel      	= array();  // Collection of channel elements
	private $items         	= array();  // Collection of items as object of FeedItem class.
	private $data          	= array();  // Store some other version wise data
	private $CDATAEncoding 	= array();  // The tag names which have to encoded as CDATA
	 
	 
	
	/**
	 * Constructor
	 */ 
	function __construct()
	{		
		// Setting default value
		$this->channel['title']        	= 'ESS Feed ' . $this->version;
		$this->channel['link']         	= 'http://eventstandardsyndication.org';
		$this->channel['id']         	= $this->uuid( $this->channel['link'], 'ESSID:' );
		$this->channel['published']   	= $this->getDate();
		$this->channel['updated']   	= $this->getDate();
		$this->channel['generator']   	= 'ess:php:generator:version' . $this->version;
		$this->channel['rights']   		= 'Copyright (c) ' . date( 'Y' ) . ', ESS Generator';
		
		//Tag names to encode in CDATA
		$this->CDATAEncoding = array('description');
	}

	/**
	 * Set a channel element
	 * @access   public
	 * @param    srting  name of the channel tag
	 * @param    string  content of the channel tag
	 * @return   void
	 */
	public function setChannelElement($elementName, $content)
	{
		$this->channel[$elementName] = $content ;
	}
	
	/**
	* Set multiple channel elements from an array. Array elements 
	* should be 'channelName' => 'channelContent' format.
	* 
	* @access   public
	* @param    array   array of channel
	* @return   void
	*/
	public function setChannelElementsFromArray($elementArray)
	{
		if(! is_array($elementArray)) return;
		
		foreach ($elementArray as $elementName => $content) 
		{
			$this->setChannelElement($elementName, $content);
		}
	}
	
	/**
	 * Genarate the ESS file
	 * 
	 * @access   public
	 * @return   void
	 */ 
	public function genarateFeed()
	{
		header( "Content-type: text/xml" );
		
		$this->printChannel();
		$this->printItems();
		$this->printTale();
	}
	
	/**
	 * Create a new FeedItem.
	 * 
	 * @access   public
	 * @return   object  instance of FeedItem class
	 */
	public function createNewItem()
	{
		$Item = new FeedItem();
		return $Item;
	}
	
	/**
	* Add a FeedItem to the main class
	* 
	* @access   public
	* @param    object  instance of FeedItem class
	* @return   void
	*/
	public function addItem( $feedItem )
	{
		$this->items[] = $feedItem;    
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
			$this->setChannelElement( 'id', $this->uuid( $el, 'ESSID:' ) );
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
  	public function uuid($key = null, $prefix = '') 
	{
		$key = ($key == null)? uniqid(rand()) : $key;
		$chars = md5($key);
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);

		return $prefix . $uuid;
 	}
	
	/**
  	 * Genarate current Strind date in ISO standard format
	 * 
	 * @access   private
	 * @return   String
	 */ 
	public function getDate()
	{
		return urlEncode( date( "Y-m-d" ). "T" . date( "H:i:s" ) . "Z" );
	}
	
	/**
	* Prints the xml and ess namespace
	* 
	* @access   private
	* @return   void
	*/
	private function printHead()
	{
		$out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$out .= '<ess xmlns="http://eventstandardsyndication.org/history/'.$this->version.'" version="'. $this->version .'" lang="'. $this->lang .'">' . PHP_EOL;
		echo $out;
	}
	
	/**
	* Closes the open tags at the end of file
	* 
	* @access   private
	* @return   void
	*/
	private function printTale()
	{
		echo '</channel>' . PHP_EOL . '</ess>'; 
	}

	/**
	* Creates a single node as xml format
	* 
	* @access   private
	* @param    srting  name of the tag
	* @param    mixed   tag value as string or array of nested tags in 'tagName' => 'tagValue' format
	* @param    array   Attributes(if any) in 'attrName' => 'attrValue' format
	* @return   string  formatted xml tag
	*/
	private function makeNode($tagName, $tagContent, $attributes = null)
	{        
		$nodeText = '';
		$attrText = '';

		if ( is_array( $attributes ) )
		{
			foreach ($attributes as $key => $value) 
			{
				$attrText .= " $key=\"$value\" ";
			}
		}
		
		$nodeText .= (in_array($tagName, $this->CDATAEncoding))? "<{$tagName}{$attrText}><![CDATA[" : "<{$tagName}{$attrText}>";
		 
		if ( is_array( $tagContent ) )
		{ 
			foreach ($tagContent as $key => $value) 
			{
				$nodeText .= $this->makeNode($key, $value);
			}
		}
		else
		{
			$nodeText .= (in_array($tagName, $this->CDATAEncoding))? $tagContent : htmlentities($tagContent);
		}           
			
		$nodeText .= (in_array($tagName, $this->CDATAEncoding))? "]]></$tagName>" : "</$tagName>";

		return $nodeText . PHP_EOL;
	}
	
	/**
	* @desc     Print channel
	* @access   private
	* @return   void
	*/
	private function printChannel()
	{
		echo '<channel>' . PHP_EOL;
		
		foreach( $this->channel as $key => $value ) 
		{
			echo $this->makeNode( $key, $value );
		}
	}
	
	/**
	* Prints formatted feed items
	* 
	* @access   private
	* @return   void
	*/
	private function printItems()
	{    
		foreach ( $this->items as $item ) 
		{
			$thisItems = $item->getElements();
			
			echo $this->startFeed();
			
			foreach ($thisItems as $feedItem ) 
			{
				echo $this->makeNode($feedItem['name'], $feedItem['content'], $feedItem['attributes']); 
			}
			echo $this->endFeed();
		}
	}
	
	/**
	* Make the starting tag of feed
	* 
	* @access   private
	* @return   void
	*/
	private function startFeed()
	{
		echo '<feed>' . PHP_EOL; 
	}
	
	/**
	* Closes feed item tag
	* 
	* @access   private
	* @return   void
	*/
	private function endFeed()
	{
		echo '</feed>' . PHP_EOL; 
	}
	
	
}

function __autoload( $class_name ) 
{
	require_once $class_name . '.php';
}