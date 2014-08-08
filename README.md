##Twtr Srchr
Contributors: Mark Hayden  
Tags: wordpress, twitter, search, plugin  
Requires at least: 3.5.1  
Tested up to: 3.6  
Stable tag: 0.0.02  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple plugin for pulling in and displaying twitter queries unique to each post.


###Description

This is a simple wordpress plugin created to perform, and cache, a search query that is unique to an individual post within wordpress. You enter a query or user name with each post. From there twitter search is queried and the results are stored for you to display. Uses a simple shortcode to make displaying data easy.


###Installation

1. Add the plugin into your wordpress install. wp-content/plugins/[PUT FOLDER HERE]
2. From the wordpress admin dashboard go to plugins and activate the "Twtr Srchr" plugin.
3. Navigate to the plugin settings page under the "Settings" menu at the left of your admin panel.
4. Follow instructions there to generate and set up a twitter app, as well as the default plugin settings.
5. You will need to go into an active post and enter a query before the tests will pass.


###Usage

* id => the unique id associated with the tweet from wordpress
* twtr_id => the numeric id for a tweet from twitter
* twtr_query => the query that is responsible for returning the tweet
* twtr_name => the authors full name
* twtr_handle => the authors twitter username
* twtr_content => the body of the tweet
* twtr_posted => the date when the tweet was first posted to twitter
* twtr_url => the url used to access the tweet on twitter.com
* logged => when the tweet was saved to the database


Print out the body of all tweets associated with posts query from wordpress.
```
[twtr_srch]
	{{twtr_content}}
[/twtr_srch]

[twtr_srch limit=5]
	{{twtr_content}}
[/twtr_srch]

[twtr_srch limit=5 date="Y-M-D"]
	{{twtr_content}}
[/twtr_srch]
```

Print out the body of all tweets associated with posts query from php.
```
<?php echo do_shortcode('[twtr_srch limit=5]{{twtr_content}}[/twtr_srch]'); ?>
<?php echo do_shortcode('[twtr_srch date="Y-M-D"]{{twtr_content}}[/twtr_srch]'); ?>
```

###Changelog
0.0.02
> Removed cron job dependency.
  Updated testing.
  Added setting to manage twitter query frequency.

0.0.01
> Initial build and launch.