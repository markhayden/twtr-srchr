<?
// initialize variables
global $wpdb, $table_prefix;

// initialize wordpress wizardry
if(!isset($wpdb)){
	require_once('../../../../../wp-config.php');
	require_once('../../../../../wp-includes/wp-db.php');
}

require_once( 'twitter-search-class.php');

$key = get_option( 'twtr_api_key', '' );
$secret = get_option( 'twtr_api_secret', '' );

if ( $key == '' || $secret == '' ) {
	echo "You have not entered a valid twitter api key and secret. Please follow the setup instructions to do so.";
	exit;
}


$tablename = $wpdb->prefix.'postmeta';
$get_terms = $wpdb->get_results("SELECT meta_key, meta_value FROM $tablename WHERE meta_key = 'twtr_search_query'");

// check to make sure that there are queries to perform.
if ( $wpdb->num_rows < 1 ) {
	echo "Could not find any search queries to perform. Please make sure that you have entered a query on at least one post.";
	exit;
}

foreach ( $get_terms as $term ) {
	$query = $term->meta_value;

	// check if string is a user. if so format it for proper searching.
	if ( $query{0} === "@" ) {
		$query = "from:" . substr($query, 1);
	}

	$searchClass = new performSearch( $key, $secret );
	$tweets = json_decode( $searchClass->search( $query ) );

	// check for authentiation issues
	if ( $tweets->errors[0]->code == 215 ) {
		echo "You have not entered a valid twitter api key and secret. Please follow the setup instructions to do so.";
		exit;
	}

	// check that tweets were found
	if ( count( $tweets->statuses ) == 0 ) {
		echo "The searches were performed successfully but no matching tweets were found. Please wait for more tweets or change your queries.";
		exit;
	} else {
		echo "The search for " . $term->meta_value . " was performed successfully. Tweets added: [ ";
	}

	foreach ( $tweets->statuses as $tweet ) {
		$id = $tweet->id;
		$text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $tweet->text);
		$created_at = date("Y-m-d H:i:s", strtotime($tweet->created_at));
		$user_name = $tweet->user->name;
		$screen_name = $tweet->user->screen_name;
		$url = 'https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet->id;

		echo $id . ', ';
		$wpdb->query("INSERT INTO wp_twtr_srchr (twtr_id, twtr_name, twtr_handle, twtr_content, twtr_posted, twtr_url, twtr_query) VALUES ('$id', '$user_name', '$screen_name', '$text', '$created_at', '$url', '$query')");
	}

	echo ' ]<br/>';
}
?>