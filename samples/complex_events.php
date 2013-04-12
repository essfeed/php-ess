<?php
	include("../FeedWriter.php");
  
  	// ======================================================================
  	// --- Creating an instance of FeedWriter class with its tags definition. 
  	// <channel>
  	// ======================================================================
 	$essFeed = new FeedWriter('en');
	$essFeed->setTitle( 	'ESS Feed' );
  	$essFeed->setLink( 		'http://example.com/feed/sample.ess' );
  	$essFeed->setPublished( FeedWriter::getISODate() );
	$essFeed->setUpdated( 	FeedWriter::getISODate() );
	$essFeed->setRights( 	'Copyright (c) ' . date( 'Y' ) . ', ESS Generator' );
  
  
  		// ======================================================================
    	// --- Create a new Feed entry
    	// <feed> 
    	// ======================================================================
    	$newEvent = $essFeed->newEventFeed();
		$newEvent->setTitle( 		'Football match every saturdays' );
		$newEvent->setUri( 			'http://sample.com/events/specific-and-unique-event-page/' );
		$newEvent->setPublished( 	FeedWriter::getISODate() );
		$newEvent->setUpdated( 		FeedWriter::getISODate() );
		$newEvent->setAccess( 		'PUBLIC' );
		$newEvent->setDescription(	"Welcome to my first football match event.<br/> 
<b>This football</b> match is very important.</b>
<br/><br/>
Our team meets the main competitor of our league." );
		$newEvent->setTags(			array( 'Sport', 'Football', 'match' ) );
		
		
		// --- Define the event feed’s mandatory elements
		
		
		// ====== CATEGORIES =======
		// ----------- add a commonly shared category ID (from http://essfeed.org > Categories) - Useful for search engine to cross several events from the same category.
		$newEvent->addCategory( 'competition', 	array('name'=> 'Football match', 'id'=> 'C2AH' ) );
		
		// ----------- add personalized category, without ID - Useful to create several specific events that belong to the same category.
		$newEvent->addCategory( 'theme', array('name'=> 'FunnySoccer' ) );
		
		
		
		// ====== DATES =========
		// ----------- add a repetitive event that happens 3 week consecutively at the same time as the first one defined in the “start” attribute and for 2 hours.
		$newEvent->addDate( 'recurrent', 	'week',  3,  null, null, null, array('name'=> 'Matches every saturday during 3 weeks', 'start'=> '2013-10-25T15:30:00+08:00', 'duration'=> 2*3600 ) );
		
		// ----------- add a repetitive event that happens every last Sunday of every month for 2 years (24 months) starting at the same time as the first one defined in the “start” attribute and for 2 hours.  
		$newEvent->addDate( 'recurrent', 	'month', 24, null, "sunday", "last", array('name'=> 'Sunday matches at the end of the month', 'start'=> '2013-10-25T15:30:00+08:00', 'duration'=> 2*3600 ) );
		
		// ----------- add two simple dates that happen only two times: 10/25/2013 and 11/25/2013 at 3:30pm (PST: Pacific Standard Time: GMT + 8 hours) for 3 hours.
		$newEvent->addDate( 'standalone', 	null, null, null, null, null, array('name'=> 'Match next saturday', 'start'=> '2013-10-25T15:30:00+0800', 'duration'=> 3*3600 ) );
		$newEvent->addDate( 'standalone', 	null, null, null, null, null, array('name'=> 'Match next month', 	'start'=> '2013-11-25T15:30:00+0800', 'duration'=> 3*3600 ) );
		
		// ----------- add a repetitive event that happens on Monday, Tuesday and Thursday every 3 weeks for 6 months starting at the same time as the first one (8am PST) defined in the “start” attribute and for 8 hours.
		$newEvent->addDate( 'recurrent', 	'week', 6, 3, "monday,tuesday,thirsday", null, array('name'=> 'Sunday matches at the end of the month', 'start'=> '2013-10-25T08:00:00+08:00', 'duration'=> 8*3600 ) );
		
		
		
		// ====== PLACES =========
		// ----------- add a simple fix point on a map to localize the event.
		$newEvent->addPlace( 'fixed', array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		
		
		
		// ====== PRICES =========
		// ----------- Define a free entrance for the event.
		$newEvent->addPrice( 'standalone', 'free', null,null,null,null,null, array('name'=> 'Free Entrance', 'value'=> '0'));
		
		// ----------- Define a free entrance but restricted to invitation only.
		$newEvent->addPrice( 'standalone', 'invitation', null,null,null,null,null, array('name'=> 'Invitation required - please contact the organizer', 'value'=> '0'));
		
		// ----------- Define a specific VIP access for $10.
		$newEvent->addPrice( 'standalone', 'fixed',	null,null,null,null,null, array('name'=> 'Entrance with VIP access', 'value'=> '10', 'currency'=> 'USD'));
		
		// ----------- Define a billing payment (required or made) every 28th of every month during 12 months with a payment URL.
		$newEvent->addPrice( 'recurrent', 'fixed','month', 12, 28, "number", null, array('name'=> 'Monthly subscription - payment every 28th (for one year)', 'value'=> '20', 'currency'=> 'USD', 'start'=> '2013-10-25T23:59:00Z', 'uri'=>'http://payment.com/api'));
		
		// ----------- Define a pre-selling ticket available 1 month before the event (define in the “start” attribute) (the date of the event have to be defined in Object->addDate(...) ) and for 1 week of selling availability.
		$newEvent->addPrice( 'standalone', 'fixed',null,null,null,null,null, array('name'=> 'Tickets available 1 month before the event during 2 weeks.', 'value'=> '20', 'currency'=> 'USD', 'start'=> '2013-09-25T23:59:00Z', 'duration' => 3600*24*15, 'uri'=>'http://payment.com/api/script.do' ) );
		
			
			
		// --- Define the optional event's elements
		
		
		// ====== PEOPLE ============
		// ---------- Define who created this event.
		$newEvent->addPeople( 'organizer', array('name' => 'The Football Club Association','firstname' => 'John','lastname' => 'Doe','organization' => 'Football AC','logo' => 'http://example.com/logo.png','icon'=> 'http://example.com/icon.png','uri'=> 'http://example.com','address'=> 'Ave of Americas, 875','city'=> 'New York','zip'=> '10001','state'=> 'New York', 'state_code' => 'NY', 'country' => 'United States of Americas', 'country_code' => 'US','email'=> 'contact@example.com', 'phone' => '(646) 225-9987' ) );
		// ---------- Define who is performing at the event (actors, performers, singers, speakers...)
		$newEvent->addPeople( 'performer', array('name' => 'The main player', 'firstname' => 'Christiano','lastname' => 'Ronaldo' ) );
		// ---------- Define rules for the event: the stadium can hold only 2000 people, the minimum age is 16 years old and it's prohibited to smoke! 
		$newEvent->addPeople( 'attendee',  array('name' => 'All kind of public are welcomed', 'minpeople' => '0', 'maxpeople' => '2000', 'minage' => '16', 'restriction' => 'Smoking is not allowed in the stadium' ) );
		
		
		// ====== MEDIA ============
		// ---------- add several media files to display and represent your event. The quality and the number of images are very important for event search engine and event attendees.
		$newEvent->addMedia( 'image',   array('name' => 'The image 01', 'uri' => 'http://example.com/image_01.png'));
		$newEvent->addMedia( 'image',   array('name' => 'The image 02', 'uri' => 'http://example.com/image_02.png'));
		$newEvent->addMedia( 'image',   array('name' => 'The image 03', 'uri' => 'http://example.com/image_03.png'));
		$newEvent->addMedia( 'sound',   array('name' => 'The sound',  	'uri' => 'http://example.com/sound.mp3'));
		$newEvent->addMedia( 'video',   array('name' => 'The video',    'uri' => 'http://example.com/video.mp4'));
		$newEvent->addMedia( 'website', array('name' => 'The website',  'uri' => 'http://example.com/'));
		
		
		// ====== RELATIONS ============
		// ---------- add several events in relation to the current event. If you don't have the ID of the event, you can generate it from the URL of the final event web page.
		$newEvent->addRelation(	'alternative', 	array('name' => 'Another match elswhere the same day', 			'uri' => 'http://example.com/alternative/event', 	'id' => 'ESSID:65ca2c92-2c98-068e-390d-543c376f8e7d' ) );
		$newEvent->addRelation(	'related', 		array('name' => 'Art exposition about Football', 				'uri' => 'http://example.com/related/event', 		'id' => FeedWriter::uuid( 'http://example.com/related/event.html' ) ) );
		$newEvent->addRelation(	'enclosure', 	array('name' => 'Another event near the stadium the same day', 	'uri' => 'http://example.com/enclosure/event', 		'id' => FeedWriter::uuid( 'http://example.com/enclosure/event' ) ) );
		
		
		// ====== AUTHORS ============
		// ---------- add information about who created this feed (sometime it is not the same the organizer) and define two event author contributor (can be a person, a company, an association or a simple website).
		$newEvent->addAuthor( 'author', 	 array( 'name' => 'John Doe', 				 'uri' => 'http://example.com/events/', 'email' => 'jdoe@example.com', 'phone' => '001 (646) 490-8899', 'firstname' => 'Janette', 'lastname' => 'Doe', 'organization' => 'Football club association', 'address' => '80, 5th avenue / 45st E - #504', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		$newEvent->addAuthor( 'contributor', array( 'name' => 'Martine Doe - Secretary', 'uri' => 'http://example.com/events/', ) );
		$newEvent->addAuthor( 'contributor', array( 'name' => 'MyCompany', 				 'uri' => 'http://example.com' ) );
		
		
		// Add the EventFeed to ESSFeed
	  	$essFeed->addItem( $newEvent );
		
		// --- End of Feed entry 
		// </feed>
		// ======================================================================
  
  
  		// +
  		
  		
  		// ======================================================================
	  	// --- Create another event in the current ESS channel.
	  	// <feed>
	  	// All the information defined inside the <feed> element must be applicable to each sub-element:
     	// All the dates defined must be applicable to every price defined and to every image defined...
	  	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'published'=> FeedWriter::getISODate(), 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert.", 'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
	  		$newEvent->addCategory( 'concert', 										array('name'=> 'Rock Music', 'id'=> 'M22'));
			$newEvent->addDate( 	'recurrent', 'year', 12, null,null,null, 		array('name'=> 'Yearly concert', 'start'=> '2013-10-25T15:90:00Z', 'duration'=> '7200' ) );
			$newEvent->addPlace( 	'fixed', 										array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
			$newEvent->addPrice(	'standalone', 'fixed',null,null,null,null,null, array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
			$newEvent->addPeople(	'performer',									array('name'=> 'Madonna' ) );
			$newEvent->addMedia(	'image', 										array('name'=> 'Foto of Madonna', 'uri' => 'http://madonna.com/image.png'));					
		$essFeed->addItem( $newEvent );
  		// --- End of the feed entry 
		// </feed>
		// ======================================================================
  	
		
		// +
		// add other events to the feed
  		// ...
  	
  	
  	// </channel>
  	
  	
 	// Genarate the ESS feed on screen only. 
 	$essFeed->genarateFeed();
	
	// OR
	
	
	// Generate the ESS Feed file on server.
	// you have to configure the folder on the server with the same owner then the Apache user
	// #> chown www-data:www-data /var/local/www/site/
	// #> chmod 0755 /var/local/www/site/
	//$essFeed->genarateFeedFile( '/var/local/www/site/feeds/events.ess', 'http://example.com/feeds/events.ess' );