<?php
 /**
 * Univarsal ESS Feed Writer
 * 
 * FeedItem class - Used as feed element in FeedWriter class
 *
 * @package         ESSFeedWriter
 * @author          Brice Pissard
 * @link            http://eventstandardsyndication.org
 */
class FeedItem
{
	private $elements = array();    //Collection of feed elements
	private $version;
	
	
	function __construct(){}
	
	
	/**
	* Add an element to elements array
	* 
	* @access   public
	* @param    srting  The tag name of an element
	* @param    srting  The content of tag
	* @param    array   Attributes(if any) in 'attrName' => 'attrValue' format
	* @return   void
	*/
	public function addElement($elementName, $content, $attributes = null)
	{
		$this->elements[$elementName]['name']       = $elementName;
		$this->elements[$elementName]['content']    = $content;
		$this->elements[$elementName]['attributes'] = $attributes;
	}
	
	/**
	* Set multiple feed elements from an array. 
	* Elements which have attributes cannot be added by this method
	* 
	* @access   public
	* @param    array   array of elements in 'tagName' => 'tagContent' format.
	* @return   void
	*/
	public function addElementArray($elementArray)
	{
		if(! is_array($elementArray)) return;
		foreach ($elementArray as $elementName => $content) 
		{
			$this->addElement($elementName, $content);
		}
	}
	
	/**
	* Return the collection of elements in this feed item
	* 
	* @access   public
	* @return   array
	*/
	public function getElements()
	{
		return $this->elements;
	}
	
	// Wrapper functions ------------------------------------------------------
	
	/**
	 * Set the 'dscription' element of feed item
	 * 
	 * @access   public
	 * @param    string  The content of 'description' element
	 * @return   void
	 */
	public function setDescription( $description ) 
	{
		$this->addElement('description', $description);
	}
	
	/**
	 * Set the 'title' element of feed item
	 * 
	 * @access   public
	 * @param    string  The content of 'title' element
	 * @return   void
	 */
	public function setTitle( $title ) 
	{
		$this->addElement( 'title', $title );  	
	}
	
	/**
	* Set the 'date' element of feed item
	* 
	* @access   public
	* @param    String  The content of 'date' element
	* @return   Void
	*/
	public function setDate( $date )  
	{
		if ( !is_numeric( $date ) )
		{
			$date = strtotime($date);
		}
		
		$tag    = 'date';
		$value  = date("Y-m-d", $date);
		
		$this->addElement($tag, $value);    
	}
	
	/**
	* Set the 'link' element of feed item
	* 
	* @access   public
	* @param    string  The content of 'link' element
	* @return   void
	*/
	public function setLink($link) 
	{
		if($this->version == RSS2 || $this->version == RSS1)
		{
			$this->addElement('link', $link);
		}
		else
		{
			$this->addElement('link','',array('href'=>$link));
			$this->addElement('id', FeedWriter::uuid($link,'urn:uuid:'));
		} 
		
	}
	
	/**
	 * Set the 'image' channel element
	 * 
	 * @access   public
	 * @param    srting  title of image
	 * @param    srting  link url of the imahe
	 * @param    srting  path url of the image
	 * @return   void
	 */
	public function setImage($title, $link, $url)
	{
		$this->setChannelElement('image', array('title'=>$title, 'link'=>$link, 'url'=>$url));
	}
	
	/**
	* Set the 'encloser' element of feed item
	* For RSS 2.0 only
	* 
	* @access   public
	* @param    string  The url attribute of encloser tag
	* @param    string  The length attribute of encloser tag
	* @param    string  The type attribute of encloser tag
	* @return   void
	*/
	public function setEncloser($url, $length, $type)
	{
		$attributes = array('url'=>$url, 'length'=>$length, 'type'=>$type);
		$this->addElement('enclosure','',$attributes);
	}
	
 }