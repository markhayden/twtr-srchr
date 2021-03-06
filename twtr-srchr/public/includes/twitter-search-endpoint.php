<?
// initialize variables
global $wpdb, $table_prefix;

// initialize wordpress wizardry
if(!isset($wpdb)){
	require_once('../../../../../wp-config.php');
	require_once('../../../../../wp-includes/wp-db.php');
}

require_once( 'twitter-search-class.php');

$buffer = get_option( 'twtr_query_buffer', '' );
$key = get_option( 'twtr_api_key', '' );
$secret = get_option( 'twtr_api_secret', '' );

if ( $key == '' || $secret == '' ) {
	if( $test == true ) {
		echo "You have not entered a valid twitter api key and secret. Please follow the setup instructions to do so.";
	}
	exit;
}

$tablename = $wpdb->prefix.'postmeta';

$query = $_GET['q'];
$test = $_GET['t'];

// check if string is a user. if so format it for proper searching.
if ( $query{0} === "@" ) {
	$query = "from:" . substr($query, 1);
}

// check the last time twitter was pinged for this query
$logtable = $wpdb->prefix.'twtr_srchr_log';
$get_query_allowed_sql = $wpdb->get_results("SELECT logged FROM $logtable WHERE twtr_query = '{$query}' AND logged > NOW() - INTERVAL $buffer MINUTE ORDER BY logged DESC LIMIT 1");

if ( count($get_query_allowed_sql) > 0  && $test != true) {
	// dont run the query. tweets are accurate up to five minutes.
} else {
	$searchClass = new performSearch( $key, $secret );
	$tweets = json_decode( $searchClass->search( $query ) );

	// check for authentiation issues
	if ( $tweets->errors[0]->code == 215 ) {
		if( $test == true ) {
			echo "You have not entered a valid twitter api key and secret. Please follow the setup instructions to do so.";
		}
		exit;
	}

	// check that tweets were found
	if ( count( $tweets->statuses ) == 0 ) {
		if( $test == true ) {
			echo "The searches were performed successfully but no matching tweets were found. Please wait for more tweets or change your queries.";
		}
		exit;
	} else {
		if( $test == true ) {
			echo "The search for " . $term->meta_value . " was performed successfully. Tweets added: ";
		}
	}

	foreach ( $tweets->statuses as $tweet ) {
		$id = $tweet->id;
		$text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $tweet->text);
		$created_at = date("Y-m-d H:i:s", strtotime($tweet->created_at));
		$user_name = $tweet->user->name;
		$screen_name = $tweet->user->screen_name;
		$url = 'https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet->id;

		// make urls into html links
			// pull in the link urls
			$text = preg_replace(
				'@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@',
				'<a href="$1" target="_new">$1</a>',
				$text);

			// pull in the handle links
			$text = preg_replace('/(?<=|\s)@([a-z0-9_]+)/i',
				'<a href="http://www.twitter.com/$1" target="_new">@$1</a>',
				$text);

			// pull in the hashtag links
			$text = preg_replace('/(?<=|\s)#([a-z0-9_]+)/i',
				'<a href="http://www.twitter.com/#$1" target="_new">#$1</a>',
				$text);

		if( $test == true ) {
			echo $id . ', ';
		}

		$wpdb->query("INSERT INTO wp_twtr_srchr (twtr_id, twtr_name, twtr_handle, twtr_content, twtr_posted, twtr_url, twtr_query) VALUES ('$id', '$user_name', '$screen_name', '$text', '$created_at', '$url', '$query')");
	}

	$wpdb->query("INSERT INTO wp_twtr_srchr_log (twtr_query) VALUES ('$query')");
}
?>