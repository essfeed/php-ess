<?php
	include("../FeedWriter.php");
  	
	// Define the absolute location of the ESS feed. if generated and written on server-side (to limit PHP and DataBase access).
  	$new_feed_url 	= 'http://example.com/feed/sample.ess';
	$feed_on_server = '/var/local/www/site/feeds/simple_event.ess';
  	
  	// OR Define the PHP dynamic script (the ESS feed will be generated on-the-fly at each request and are not witten on the server-side)
  	//$new_feed_url = FeedWriter::getCurrentURL();
	
	
	// Create the ESS Feed
	$essFeed = new FeedWriter( 'en', array( 'title'=> 'ESS Feed','link'=> $new_feed_url, 'published'=> '2013-06-10T10:55:14Z', 'rights'=> 'Copyright (c)'));
 	
 	// ####################################################################
 	// ###
	// ###  DEBUG  MODE  
	// ###	display on screen the result and explain the errors. 
	// ###	Have to be switch to false for production.
	// ###
  			$essFeed->DEBUG = false;
	// ####################################################################
	
	// Defines the first feed event's URL (the URL where will be forwarded the feed users)
	$event_webpage = 'http://madonna.com/events/concert.html';
	
	// Create an Event 
	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'uri'=> $event_webpage, 'published'=> 'now', 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert. Plus some HTML: <br><br><img src='http://madonna.com/img/concert.png' alt='The text of the image comming from the description!'>",'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
  		$newEvent->addCategory( 'concert', 											array('name'=> 'Rock Music', 'id'=> 'M22'));
		$newEvent->addDate( 	'recurrent', 'month', null, 1, 'saturday', 'last',	array('name'=> 'Yearly concert', 'start'=> 1316898400, 'duration'=> '7200' ) );
		$newEvent->addPlace( 	'fixed', null,										array('name'=> 'Yankee Stadium', 'address' => '1 E 161st St, Bronx', 'city' => 'New York', 'zip' => '10451', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		$newEvent->addPrice(	'standalone', 'fixed', null,null,null,null,null,	array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
		$newEvent->addPeople(	'performer',										array('name'=> 'Madonna' ) );
		$newEvent->addMedia(	'image', 											array('name'=> 'Foto of Madonna', 'uri' => 'http://madonna.com/image.png'));					
		// --------------------------------------------------------------------------------------------------------
		//	YOU CAN ALSO:
		// 	Parse the description to extract media files URL from an HTML content. (image, sound, youtube...)
		//	Some CMS only allow to publish event's images in the description body.
		// --------------------------------------------------------------------------------------------------------
		foreach ( FeedWriter::getMediaURLfromHTML( $newEvent->getDescription() ) as $m ) 
		{
			$newEvent->addMedia( $m['type'], array('name' => $m['name'],  'uri' => $m['uri'] ) );
		}
		
	// Add the event to the Feed
	$essFeed->addItem( $newEvent );
	
	
	// Generate the feed the server-side server file (to reduce the load of PHP and the DataBase).
	//$essFeed->genarateFeed( $feed_on_server );
	
	// OR Generate the ESS XML feed dynamicaly through PHP (can load PHP and the DataBase if a lot of robots access to this dynamic file).
	$essFeed->genarateFeed();