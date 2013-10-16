php-ess
=======

#### https://github.com/essfeed/php-ess

[![ESS Feed Standard](http://essfeed.org/images/8/87/ESS_logo_32x32.png)](http://essfeed.org/)

ESS: Event Standard Syndication.
ESS is free and open-source XML feed invented exclusively for events.
This library allows to generate ESS feeds with a simple instanciation and broadcast event's feeds to search engines.

To use this Class complete samples are available in /samples/complex_events.php :
- Simple fixed date.
- Recursive dates (several times a month, every last saturday for 6 months...)
- Free event or events with payment (with an payment API url).
- Event only available with invitations.
- Describe events with image, video and sounds.
...

## Usage

 	include("FeedWriter.php");
  	$new_feed_url  = 'http://sample.com/feed/sample.ess';
  	$event_webpage = 'http://madonna.com/event/page.html';

	// Create the ESS Feed
	$essFeed = new FeedWriter( 'en', array( 'title'=> 'ESS Feed','link'=> $new_feed_url,'published'=> '2013-10-25T15:30:00-08:00', 'rights'=> 'Copyright (c)'));

	// Create an Event (several methods can be called to assign various categories, prices, places,.. to the same event).
	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'uri'=> $event_webpage, 'published'=> 'now', 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert.", 'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
  		$newEvent->addCategory( 'concert', 											array('name'=> 'Rock Music', 'id'=> 'M22'));
		$newEvent->addDate( 	'recurrent', 'year', 1, null,null,null,				array('name'=> 'Yearly concert', 'start'=> '2013-10-25T15:30:00Z', 'duration'=> '7200' ) );
		$newEvent->addPlace( 	'fixed', null,										array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		$newEvent->addPrice(	'standalone', 'fixed', null,null,null,null,null,	array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
		$newEvent->addPeople(	'performer',										array('name'=> 'Madonna' ) );
		$newEvent->addMedia(	'image', 											array('name'=> 'Foto of Madonna', 'uri' => 'http://madonna.com/image.png'));

	// Add the event to the Feed
	$essFeed->addItem( $newEvent );

	// Other event feed can be added here...
	// ...

	// Display the ESS Feed generated.
	$essFeed->genarateFeed();

## PHP Composer
The library is available in [![PHP Composer]](http://getcomposer.org/) in the [![Packagist Repository]](http://packagist.org/)
To install the PHP ESS Feed library, just add the following line in your composer.json file:
	{
    	"require": {
        	...
        	"essfeed/essfeed": "1.*"
	    }
	}


# Diference between RSS and ESS
[![Publishing events with RSS](http://essfeed.org/images/6/64/Before_ess_with_rss.gif)](http://essfeed.org/)

[![Publishing events with ESS](http://essfeed.org/images/3/3b/After_with_ess.gif)](http://essfeed.org/)

[![Play the video](http://essfeed.org/images/e/ea/ESS-play-video.png)](http://www.youtube.com/watch?v=OGi0U3Eqs6E)


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
