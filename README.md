php-ess
=======

ESS (Event Standard Syndication) library in PHP
This two classes allow to generate ESS feed with a simple instanciation.

To use this Class a complete example is available in example_ess.php

<?php
	include("FeedWriter.php");
  	
  	// Create the ESS Feed
  	$essFeed = new FeedWriter( 'en', array( 'title'=> 'ESS Feed','link'=> 'http://example.com/feed/sample.ess','published'=> FeedWriter::getISODate(), 'rights'=> 'Copyright (c)'));
	
	// Create an Event
	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'published'=> FeedWriter::getISODate(), 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert.", 'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
  		$newEvent->addCategories( 	'concert', 					array('name'=> 'Rock Music', 'id'=> 'M22'));
		$newEvent->addDates( 		'recurent', 	'year', 	array('name'=> 'Yearly concert', 'start'=> '2013-10-25T15:30:00Z', 'duration'=> '7200' ) );
		$newEvent->addPlaces( 		'fix', 						array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		$newEvent->addPrices(		'standalone', 	null,		array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
		$newEvent->addPeople(		'performer',				array('name' => 'Madonna' ) );
		$newEvent->addMedias(		'image', 					array('name' => 'Foto of Madonna', 'uri' => 'http://madonna.com/image.png'));					
	
	// Add the event to the Feed
	$essFeed->addItem( $newEvent );
	
	// Print on screen the ESS Feed
	$essFeed->genarateFeed();
?>

### list

