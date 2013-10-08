<?php
	include( "../FeedWriter.php" );
  
  	// ======================================================================
  	// --- Creating an instance of FeedWriter class with its tags definition. 
  	// <channel>
  	// ======================================================================
 	$essFeed = new FeedWriter( 'en' ); // ISO 4217 language code (2 chars).
	
	// ####################################################################
 	// ###
	// ###  DEBUG  MODE  
	// ###	display on screen the result and explain the errors. 
	// ###	Have to be switch to false for production.
	// ###
  			$essFeed->DEBUG = TRUE;
	// ####################################################################
  	
  	
  	// ESS Generated On-The-Fly by PHP
	$new_feed_url = FeedWriter::getCurrentURL(); 					// if the feed have to be regenerated at each request define here the PHP file that return the ESS XML content (generate extra load of the Database and PHP resources)
	
	// OR Specify where will be accessible the feed on server-side.
	//$new_feed_url = 'http://example.com/feed/complex_events.ess'; // The feed have to be generated and written in server harddrive (to limit PHP and DataBase load request), seed at the end of this document the file generation options.
	
	
	$essFeed->setTitle( 'ESS Feed sample with åççéñts.' );						// Defines the Feed name (not the event).
  	$essFeed->setLink( $new_feed_url );											// Define the URL of the Feed (must be unic and specific to this feed).
  	$essFeed->setPublished( 'now' );											// Current date (according to server time). 
  	//$essFeed->setPublished( 1361791459 ); 									// OR date in seconds.
	$essFeed->setUpdated( 'Jun 10, 2012, 6pm PST' );							// OR date in convertible String format (http://php.net/manual/en/function.strtotime.php)
	$essFeed->setUpdated( '2013-10-31T15:30:59Z' );								// OR ISO 8601 Date (recommanded)
	$essFeed->setRights( 'Copyright (c) ' . date( 'Y' ) . ', ESS Generator' );	// Specified the Copyright of this Feed.
	
	
  	
  	
  		// ======================================================================
    	// --- Create and add a new event to the feed.
    	// <feed> 
    	// ======================================================================
    	$newEvent = $essFeed->newEventFeed();
		$newEvent->setTitle( 		'Football match every saturdays, text with åççéñts and 汉语/漢語 Hànyǔ.' );	// Defines the title of the Event.
		$newEvent->setUri( 			'http://sample.com/events/unique-event-page/index.html?with=param&additional' );	// Defines the URL of the event page
		// $newEvent->setId(		'YOUR_EVENT_UNIC_ID' );				// You can define your event unic ID, otherwise the event URL will be used to generate a unic ID. 		
		$newEvent->setPublished( 	'now' );							// check strtotime() to see all the format supported.
		$newEvent->setUpdated( 		"2013-10-31T19:90:99-08:00" ); 		// A valid date is at the format ISO 8601 (e.g. 2013-10-31T15:30:59Z or 2013-10-31T15:30:59+02:00), if the format is not reconized it is set at the current date.
		$newEvent->setAccess( 		EssDTD::ACCESS_PUBLIC );			// Defines if the event is 'PUBLIC' or 'PRIVATE'
		$newEvent->setTags(	array( 'Sport', 'Football', 'match' ) );	// Defines an array of keywords or tags to Help search engine to find your event.
		
		// -- Add some complex HTML event description content.
		$description_HTML ="<h1>Welcome to my first football match event.<h1><br/> 
			<p>
				<b>This football</b> match is very important.</b>
				<br/><br/>
				Our team meets the main competitor of our league.
			</p>
			<br/><br/>	
			<h2>Title of a subsection</h2>
			<img src='http://sample.com/images/with/dynamic/url/abcdef'/>
			<img src='http://sample.com/images/pictute_02.png' alt='description 02'/>
			<img src='http://sample.com/images/picture_03.png' title='Title of the third image' />
			<!-- Text with comments -->
			<p>Display a video in the description</p>
			<br/>
			<video width='320' height='240' controls>
			  	<source src='http://sample.com/videos/movie.mp4' type='video/mp4'/>
			  	<source src='http://sample.com/videos/movie.ogg' type='video/ogg'/>
			</video>
			<br/>
			<br/>
			<h3>Display a Youtube video</h3>
			<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0' width='100%' height='100%'>
			    <param name='movie' value='http://www.youtube.com/v/gquUW1u-BN0' />
			    <param name='quality' value='high' />
			    <param name='id' value='PortfolioScroller' />
			    <param name='wmode' value='opaque' />
			    <param name='FlashVars' value='pfcase=1'>
			    <embed src='http://www.youtube.com/v/gquUW1u-BN0' width='100%' height='100%' id='PortfolioScroller' quality='high' pluginspage='http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' wmode='opaque' name='PortfolioScroller'></embed>
			</object>
		";
		$newEvent->setDescription( $description_HTML );
		
		
		// --- Define the event feed’s mandatory elements
		
		
		// ====== CATEGORIES == (add one or more categories) =======
		// ----------- Add a commonly shared category ID (from http://essfeed.org > Categories) - Useful for search engine to cross several events from the same category.
		$newEvent->addCategory( 'competition', 	array('name'=> 'Football match', 'id'=> 'C2AH' ) );
		
		// ----------- Add personalized category, without ID - Useful to create several specific events that belong to the same category.
		$newEvent->addCategory( 'general', array('name'=> 'FunnySoccer' ) );
		
		
		
		
		// ====== DATES == (add one or more dates) =======
		// ----------- Add a repetitive event that happens 3 week consecutively at the same time as the first one defined in the “start” attribute and for 2 hours.
		$newEvent->addDate( 'recurrent', 	'week',  3,  null, null, null, array('name'=> 'Matches every saturday during 3 weeks', 'start'=> '2013-10-25T15:30:00+08:00', 'duration'=> 2*3600 ) );
		
		// ----------- Add a repetitive event that happens every second and last Sunday of every month for 2 years (24 months) starting at the same time as the first one defined in the “start” attribute and for 2 hours.  
		$newEvent->addDate( 'recurrent', 	'month', 24, null, "sunday", "second,last", array('name'=> 'Monthly matches every last Sunday', 'start'=> '2013-10-25T15:30:00+08:00', 'duration'=> 2*3600 ) );
		
		// ----------- Add a repetitive event that happens every 15th and 26th of every month for 2 years (24 months) starting at the same time as the first one defined in the “start” attribute and for 2 hours.  
		$newEvent->addDate( 'recurrent', 	'month', 24, null, "15,26", null, array('name'=> 'Monthly matches every 15 and 16', 'start'=> '2013-10-25T15:30:00+08:00', 'duration'=> 2*3600 ) );
		
		// ----------- Add two simple dates that happen only two times: 10/25/2013 and 11/25/2013 at 3:30pm (PST: Pacific Standard Time: GMT + 8 hours) for 3 hours.
		$newEvent->addDate( 'standalone', 	null, null, null, null, null, array('name'=> 'Match next saturday', 'start'=> '2013-10-25T15:30:00-08:00', 'duration'=> 3*3600 ) );
		$newEvent->addDate( 'standalone', 	null, null, null, null, null, array('name'=> 'Match next month', 	'start'=> '2013-11-25T15:30:00-08:00', 'duration'=> 3*3600 ) );
		
		// ----------- Add a repetitive event that happens on Monday, Tuesday and Thursday every 3 weeks for 6 months starting at the same time as the first one (8am PST) defined in the “start” attribute and for 8 hours.
		$newEvent->addDate( 'recurrent', 	'week', 6, 3, "monday,tuesday,thursday", null, array('name'=> 'Monday, Tuesday and Thursday every 3 weeks for 6 months', 'start'=> '2013-10-25T08:00:00+08:00', 'duration'=> 8*3600 ) );
		
		// =========
		// === /!\ 		Useful function to get the number of occurence between two dates in seconds, minutes, hours, days, weeks, months or years.
		// =========	This function can be used to define the Date "limit" of an event according to the "unit" defined.
		// =========	Sometime the end of an event is define by a Date and not by the number of occurences.
		// =========	
		// $limit = FeedValidator::getDateDiff( 's', 	'2013-10-25T15:30:00-08:00', '2013-11-25T15:30:00-08:00' ); // numbers of seconds between this two dates.
		// $limit = FeedValidator::getDateDiff( 'h', 	'2013-10-25T15:30:00-08:00', '2013-11-25T18:30:00-08:00' ); // numbers of hours between this two dates.
		// $limit = FeedValidator::getDateDiff( 'd', 	'2013-10-25T15:30:00-08:00', '2013-11-25T15:30:00-08:00' ); // numbers of days between this two dates.
		// $limit = FeedValidator::getDateDiff( 'ww', 	1366171200, 				 '2013-12-25T15:30:00-08:00' ); // numbers of weeks between this two dates.
		// $limit = FeedValidator::getDateDiff( 'm', 	'2013-10-25T15:30:00-08:00', 1396171200 				 ); // numbers of months between this two dates.
		// $limit = FeedValidator::getDateDiff( 'yyyy', 1366171200, 				 1396171200 				 ); // numbers of years between this two dates.
		

		
		
		// ====== PLACES == (add one or more places) =======
		// ----------- Add a simple fix point on a map to localize the event.
		$newEvent->addPlace( 'fixed', null, array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Av of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		
		// ----------- Add a moving event with 5 places to go.
		$newEvent->addPlace( 'moving', 1, array('name'=> 'Race stop 01', 'latitude'=> '40.71675', 'longitude' => '-74.05671', 'country_code' => 'US' ) );
		$newEvent->addPlace( 'moving', 2, array('name'=> 'Race stop 02', 'latitude'=> '41.91073', 'longitude' => '-73.41674', 'country_code' => 'US' ) );
		$newEvent->addPlace( 'moving', 3, array('name'=> 'Race stop 03', 'latitude'=> '42.61672', 'longitude' => '-72.06675', 'country_code' => 'US' ) );
		$newEvent->addPlace( 'moving', 4, array('name'=> 'Race stop 04', 'latitude'=> '43.41478', 'longitude' => '-71.70684', 'country_code' => 'US' ) );
		$newEvent->addPlace( 'moving', 5, array('name'=> 'Race stop 05', 'latitude'=> '44.73675', 'longitude' => '-70.00474', 'country_code' => 'US' ) );
      
	  	
	  
	  
	  	// --- Define the optional event's elements
	  
	  
	  
		
		// ====== PRICES == (add one or more prices) =======
		// =========
		// == /!\ == 	EVEN IF THE PRICE ELEMENT IS NOT MANDATORY, 
		// =========	ESS PROCESSORS CAN UNDERSTAND THE EVENT AS FREE IF IT IS NOT DENINE.
		// =========
		// ----------- Define a free entrance for the event.
		$newEvent->addPrice( 'standalone', 'free', null,null,null,null,null, array('name'=> 'Free Entrance', 'value'=> 0 ) );
		
		// ----------- Define a free entrance but restricted to invitation only.
		$newEvent->addPrice( 'standalone', 'invitation', null,null,null,null,null, array('name'=> 'Invitation required - please contact the organizer', 'value'=> 0 ) );
		
		// ----------- Define a specific VIP access for $10.
		$newEvent->addPrice( 'standalone', 'fixed',	null,null,null,null,null, array('name'=> 'Entrance with VIP access', 'value'=> '10', 'currency'=> 'USD' ) );
		
		// ----------- Define a billing payment to make every 28th of every month during 12 months with a payment URL.
		$newEvent->addPrice( 'recurrent', 'fixed','month', 12, 1, "28", null, array('name'=> 'Monthly subscription - payment every 28th (for one year)', 'value'=> '20', 'currency'=> 'USD', 'start'=> '2013-10-25T23:59:00Z', 'uri'=>'http://payment.com/api'));
		
		// ----------- Define a pre-selling ticket available 1 month before the event (define in the “start” attribute) (the date of the event have to be defined in Object->addDate(...) ) and for 1 week of selling availability.
		$newEvent->addPrice( 'standalone', 'fixed',null,null,null,null,null, array('name'=> 'Tickets available 1 month before the event during 2 weeks.', 'value'=> '20', 'currency'=> 'USD', 'start'=> '2013-09-25T23:59:00Z', 'duration' => 3600*24*15, 'uri'=>'http://payment.com/api/script.do' ) );
		
		
		
		
		// ====== PEOPLE == (add one or more persones involve) ==========
		// ---------- Define who created this event.
		$newEvent->addPeople( 'organizer', 	 array( 'name' => 'The Football Club Association','firstname' => 'John','lastname' => 'Doe','organization' => 'Football AC','logo' => 'http://example.com/logo.png','icon'=> 'http://example.com/icon.png','uri'=> 'http://example.com','address'=> 'Ave of Americas, 875','city'=> 'New York','zip'=> '10001','state'=> 'New York', 'state_code' => 'NY', 'country' => 'United States of Americas', 'country_code' => 'US','email'=> 'contact@example.com', 'phone' => '(646) 225-9987' ) );
		
		// ---------- Define who is performing at the event (actors, performers, singers, speakers...)
		$newEvent->addPeople( 'performer', 	 array( 'name' => 'The main player', 'firstname' => 'Christiano','lastname' => 'Ronaldo' ) );
		
		// ---------- Define rules for the event: the stadium can hold only 2000 people, the minimum age is 16 years old and it's prohibited to smoke! 
		$newEvent->addPeople( 'attendee',  	 array( 'name' => 'Attendees informations: ', 'minpeople' => '0', 'maxpeople' => '2000', 'minage' => '16', 'restriction' => 'Smoking is not allowed in the stadium' ) );
		
		// ---------- Define a social network uri to share, rate or get the notes of the event.
		$newEvent->addPeople( 'social',  	 array( 'name' => 'Facebook Event', 'uri' => 'http://facebook.com/events/my_event' ) );
		
		// ---------- Define who created this feed (sometime it is not the same as the organizer).
		$newEvent->addPeople( 'author', 	 array( 'name' => 'ESS Feed Powered by Addon-XXX', 'icon' => 'http://example.com/images/icon.png' ) );
		$newEvent->addPeople( 'contributor', array( 'name' => 'Martine Doe - Secretary', 'uri' => 'http://example.com/events/', ) );
		
		
		
		
		// ====== MEDIA == (add one or more media files) ==========
		// ---------- add several media files to display and represent your event. The quality and the number of images are very important for event search engine and event attendees.
		$newEvent->addMedia( 'image',   array('name' => 'The image 01', 'uri' => 'http://example.com/image_01.png'));
		$newEvent->addMedia( 'image',   array('name' => 'The image 02', 'uri' => 'http://example.com/image_02.png'));
		$newEvent->addMedia( 'image',   array('name' => 'The image 03', 'uri' => 'http://example.com/image_03.png'));
		$newEvent->addMedia( 'sound',   array('name' => 'The sound',  	'uri' => 'http://example.com/sound.mp3'));
		$newEvent->addMedia( 'video',   array('name' => 'The video',    'uri' => 'http://example.com/video.mp4'));
		$newEvent->addMedia( 'website', array('name' => 'The website',  'uri' => 'http://example.com/'));
		
		// --------------------------------------------------------
		//	YOU CAN ALSO:
		// 	Parse the description to extract media files URL from an HTML content. (image, sound, youtube...)
		//	Some CMS only allow to publish event's images in the description body.
		//
		// --------------------------------------------------------
		foreach ( FeedWriter::getMediaURLfromHTML( $description_HTML ) as $m ) 
		{
			//echo $m['type'] . " name:". $m['name'] ." >> " . $m['uri'] . "<br/>";
			$newEvent->addMedia( $m['type'], array('name' => $m['name'],  'uri' => $m['uri'] ) );
		}
		
		
		
		// ====== RELATIONS == (add one or more other events connected to the this event) ==========
		// ---------- Add several events in relation to the current event. If you don't have the ID of the event, you can generate it from the URL of the final event web page.
		$newEvent->addRelation(	'alternative', 	array('name' => 'Another match elswhere the same day', 			'uri' => 'http://example.com/alternative/event', 	'id' => 'ESSID:65ca2c92-2c98-068e-390d-543c376f8e7d' ) );
		$newEvent->addRelation(	'related', 		array('name' => 'Art exposition about Football', 				'uri' => 'http://example.com/related/event.html', 	'id' => FeedWriter::uuid( 'http://example.com/related/event.html' ) ) );
		$newEvent->addRelation(	'enclosure', 	array('name' => 'Another event near the stadium the same day', 	'uri' => 'http://example.com/enclosure/event', 		'id' => FeedWriter::uuid( 'http://example.com/enclosure/event' ) ) );
		
		
		
		
		// -- Add the EventFeed to ESSFeed
	  	$essFeed->addItem( $newEvent );
		
		// --- End of Feed entry 
		// </feed>
		// ======================================================================
  
  
  		// + Add another simple a event to the feed...
  		
  		
  		// ======================================================================
	  	// --- Create another event in the current ESS channel.
	  	// <feed>
	  	// All the information defined inside the <feed> element must be applicable to each sub-element:
     	// All the dates defined must be applicable to every price defined and to every image defined...
	  	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'uri'=>'htp://madonna.com/concert/page.html', 'published'=> FeedWriter::getISODate(), 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert.", 'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
	  		$newEvent->addCategory( 'concert', 										array('name'=> 'Rock Music', 'id'=> 'M22'));
			$newEvent->addDate( 	'recurrent', 'year', 2, null,null,null, 		array('name'=> 'Yearly concert for the next two years', 'start'=> '2013-10-25T15:50:00Z', 'duration'=> '7200' ) );
			$newEvent->addPlace( 	'fixed', null,									array('name'=> 'Stadium NYC', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
			$newEvent->addPrice(	'standalone', 'fixed',null,null,null,null,null, array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
			$newEvent->addPeople(	'performer',									array('name'=> 'Madonna' ) );
			$newEvent->addMedia(	'image', 										array('name'=> 'Foto of Madonna', 'uri' => 'http://cdn.madonna.com/non_secure/goodies/wallpapers/01_1440x900.jpg'));					
		$essFeed->addItem( $newEvent );
  		// --- End of the feed entry 
		// </feed>
		// ======================================================================
  	
		
		// +
		// add other events to the feed
  		// ...
  	
  	
  	// </channel>
  	
  	
 	// Genarate the ESS feed dynamicaly at each request (load the DataBase and PHP). 
 	//$essFeed->genarateFeed();
	
	
	// OR 
	
	
	// Generate the ESS Feed file on server (to limit the load of PHP and DataBase).
	// you have to configure the folder on the server with the same owner then the Apache user
	// #> chown www-data:www-data /var/local/www/site/feeds
	// #> chmod 0755 /var/local/www/site/feeds
	//$essFeed->genarateFeed( '/var/local/www/site/feeds/complex_events.xml' );
	$essFeed->genarateFeed( '/home/bibi/www/ess.hypecal.com/php-ess/samples/complex_events.ess' );
	