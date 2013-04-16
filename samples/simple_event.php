<?php
	include("../FeedWriter.php");
  	$new_feed_url = 'http://example.com/feed/sample.ess';
  	
	// Create the ESS Feed
	$essFeed = new FeedWriter( 'en', array( 'title'=> 'ESS Feed','link'=> $new_feed_url,'published'=> FeedWriter::getISODate(), 'rights'=> 'Copyright (c)'));
 	
 	// #######################
	// ###  DEBUG  MODE  
	// ###	display on screen the result, and explain the errors. 
	// ###	Have to be switch to false for production.
  	$essFeed->DEBUG = true;
	// #######################
	
	// Create an Event 
	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'published'=> FeedWriter::getISODate(), 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert.", 'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
  		$newEvent->addCategory( 'concert', 											array('name'=> 'Rock Music', 'id'=> 'M22'));
		$newEvent->addDate( 	'recurrent', 'year', 1, null,null,null,				array('name'=> 'Yearly concert', 'start'=> '2013-10-25T15:30:00Z', 'duration'=> '7200' ) );
		$newEvent->addPlace( 	'fixed', null,										array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		$newEvent->addPrice(	'standalone', 'fixed', null,null,null,null,null,	array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
		$newEvent->addPeople(	'performer',										array('name'=> 'Madonna' ) );
		$newEvent->addMedia(	'image', 											array('name'=> 'Foto of Madonna', 'uri' => 'http://madonna.com/image.png'));					
		
	// Add the event to the Feed
	$essFeed->addItem( $newEvent );
	
	// Print on screen the ESS Feed
	$essFeed->genarateFeed();
	
	// OR Generate the feed in a local server file
	// $essFeed->genarateFeedFile( '/var/local/www/site/feeds/events.ess', $new_feed_url );