php-ess
=======

ESS (Event Standard Syndication) library in PHP
This two classes allow to generate ESS feed with a simple instanciation.

To use this Class a complete example is available in example_ess.php

## Usage

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



# Contributing

Contributions to the project are welcome. Feel free to fork and improve. I accept pull requests and issues,
especially when tests are included.

# License

(The MIT License)

Copyright (c) 2013

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
