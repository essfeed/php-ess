<?php
	include("FeedWriter.php");
  
  	// Creating an instance of FeedWriter class with its tags definition. 
 	$essFeed = new FeedWriter( 'en', array(
  		'title'     => 'ESS Feed',
		'link' 		=> 'http://example.com/feed/sample.ess',
		'published' => FeedWriter::getISODate(),
		'updated' 	=> FeedWriter::getISODate(),
		'rights'  	=> 'Copyright (c) ' . date( 'Y' ) . ', ESS Generator'
  	));
  	
	// OR 
	$essFeed = new FeedWriter('en');
	$essFeed->setTitle( 	'ESS Feed' );
  	$essFeed->setLink( 		'http://example.com/feed/sample.ess' );
  	$essFeed->setPublished( FeedWriter::getISODate() );
	$essFeed->setUpdated( 	FeedWriter::getISODate() );
	$essFeed->setRights( 	'Copyright (c) ' . date( 'Y' ) . ', ESS Generator' );
  
  
  	
	  	// --- Create a new EventFeed entry ------------
	  	$newEvent = $essFeed->newEventFeed( array(
	  		'title' 		=> 'Football match every saturdays',
	  		'uri'			=> 'http://sample.com/events/specific-and-unique-event-page/',
	  		'published'		=> FeedWriter::getISODate(),
	  		'updated'		=> FeedWriter::getISODate(),
	 		'access'		=> 'PUBLIC',
	  		'description' 	=> "Welcome to my first football match event.\n <b>This football</b> match is very important.\nAs our team meets the main competitor of the league.",
	       	'tags'			=> array( 'Sport', 'Football', 'match' )
	  	));
		
	  	// OR 
		$newEvent = $essFeed->newEventFeed();
		$newEvent->setTitle( 		'Football match every saturdays' );
		$newEvent->setUri( 			'http://sample.com/events/specific-and-unique-event-page/' );
		$newEvent->setPublished( 	FeedWriter::getISODate() );
		$newEvent->setUpdated( 		FeedWriter::getISODate() );
		$newEvent->setAccess( 		'PUBLIC' );
		$newEvent->setDescription(	"Welcome to my first football match event.\n <b>This football</b> match is very important.\nAs our team meets the main competitor of the league." );
		$newEvent->setTags(			array( 'Sport', 'Football', 'match' ) );
		
		// --- Define mandatory event's elements
			// --- CATEGORIES
			$newEvent->addCategories( 	'competition', 						array('name'=> 'Football match', 'id'=> 'C2AH'));
			// --- DATES
			$newEvent->addDates( 		'recurrent', 	'week', 			array('name'=> 'Match Date', 'start'=> '2013-10-25T15:30:00Z', 'duration'=> 2*3600 ) );
			$newEvent->addDates(  		'standalone', 	null, 				array('name'=> 'Match Date', 'start'=> '2013-10-25T15:30:00Z', 'duration'=> 3*3600 ) );
			// --- PLACES
			$newEvent->addPlaces( 		'fixed', 							array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
			// --- PRICES
			$newEvent->addPrices(		'standalone', 	'free', 	null,	array('name'=> 'Free Entrance', 'value'=> '0'));
			$newEvent->addPrices(		'standalone', 	'fixed', 	null,	array('name'=> 'Entrance with VIP access', 'value'=> '10', 'currency'=> 'USD'));
			$newEvent->addPrices(		'recurrent', 	'fixed', 	'month',array('name'=> 'Monthly subscription', 'value'=> '20', 'currency'=> 'USD', 'start'=> '2013-10-25T23:59:00Z', 'uri'=>'http://payment.com/api'));
			
			
		// --- Define optional event's elements
			// PEOPLE
			$newEvent->addPeople(		'organizer',						array('name' => 'The Football Club Association','firstname' => 'John','lastname' => 'Doe','organization' => 'Football AC','logo' => 'http://example.com/logo.png','icon'=> 'http://example.com/icon.png','uri'=> 'http://example.com','address'=> 'Ave of Americas, 875','city'=> 'New York','zip'=> '10001','state'=> 'New York', 'state_code' => 'NY', 'country' => 'United States of Americas', 'country_code' => 'US','email'=> 'contact@example.com', 'phone' => '(646) 225-9987' ) );
			$newEvent->addPeople(		'performer',						array('name' => 'The main player', 'firstname' => 'Christiano','lastname' => 'Ronaldo' ) );
			$newEvent->addPeople(		'attendee',							array('name' => 'All kind of public are welcomed', 'minpeople' => '0', 'maxpeople' => '0', 'minage' => '0', 'restriction' => 'Smoking is not allowed in the stadium' ));
			// MEDIA
			$newEvent->addMedia(		'image', 							array('name' => 'The image',   'uri' => 'http://example.com/image.png'));
			$newEvent->addMedia(		'sound', 							array('name' => 'The sound',   'uri' => 'http://example.com/sound.mp3'));
			$newEvent->addMedia(		'video', 							array('name' => 'The video',   'uri' => 'http://example.com/video.mp4'));
			$newEvent->addMedia(		'website', 							array('name' => 'The website', 'uri' => 'http://example.com/'));
			// RELATIONS
			$newEvent->addRelations(	'alternative',						array('name' => 'Another match elswhere the same day', 			'uri' => 'http://example.com/alternative/event', 	'id' => FeedWriter::uuid( 'http://example.com/alternative/event', 'ESSID:' )));
			$newEvent->addRelations(	'related',							array('name' => 'Art exposition about Football', 				'uri' => 'http://example.com/related/event', 		'id' => FeedWriter::uuid( 'http://example.com/related/event', 'ESSID:' )));
			$newEvent->addRelations(	'enclosure',						array('name' => 'Another event near the stadium the same day', 	'uri' => 'http://example.com/enclosure/event', 		'id' => FeedWriter::uuid( 'http://example.com/enclosure/event', 'ESSID:' )));
			// AUTHORS
			$newEvent->addAuthors(		'author', 							array( 'name' => 'John Doe', 'email' => 'jdoe@example.com', 'uri' => 'http://example.com/events/', 'phone' => '001 (646) 490-8899', 'firstname' => 'Janette', 'lastname' => 'Doe', 'organization' => 'Football club association', 'address' => '80, 5th avenue / 45st E - #504', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		
		
		// Add the EventFeed to ESSFeed
	  	$essFeed->addItem( $newEvent );
		
		// --- End of EventFeed entry ------------------
	
  
  
  
  
  	// --- Create and add another Event Feed within current ESS Channel
  	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'published'=> FeedWriter::getISODate(), 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert.", 'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
  		$newEvent->addCategories( 	'concert', 						array('name'=> 'Rock Music', 'id'=> 'M22'));
		$newEvent->addDates( 		'recurrent', 	'year', 		array('name'=> 'Yearly concert', 'start'=> '2013-10-25T15:90:00Z', 'duration'=> '7200' ) );
		$newEvent->addPlaces( 		'fixed', 						array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		$newEvent->addPrices(		'standalone', 	'fixed', null,	array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
		$newEvent->addPeople(		'performer',					array('name' => 'Madonna' ) );
		$newEvent->addMedia(		'image', 						array('name' => 'Foto of Madonna', 'uri' => 'http://madonna.com/image.png'));					
	$essFeed->addItem( $newEvent );
  
  
  
  
  
 	// Genarate the ESS feed on screen only. 
	//$essFeed->genarateFeed();
	
	// OR
	
	// Generate the ESS Feed file on server and push ESS Feed URL to aggregator's list.
	//$essFeed->genarateFeedFile( '/var/local/www/site/feeds/events.ess', 'http://example.com/feeds/events.ess' );
	$essFeed->genarateFeedFile( '/home/bibi/www/ess.hypecal.com/php-ess/events.ess', 'http://ess.hypecal.com/php-ess/events.ess' );
	
	