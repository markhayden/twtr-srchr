<?
class twtrSrchFormattr {
	private static $wpdb;

	public function __construct() {
		require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-settings.php' );
		require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

		if (!self::$wpdb) {
		    self::$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
		} else {
		    self::$wpdb;
		}
	}

	public function twtr_srch_format( $atts, $content="", $obj ) {
		// create array of potential information
		$replacables = array( 'twtr_id', 'twtr_handle', 'twtr_name', 'twtr_content', 'twtr_posted', 'twtr_url', 'logged' );

		// replace template with values
		foreach ( $replacables as $value ) {
			$query = "{{".$value."}}";
			$content = str_replace( $query, $obj->{$value}, $content );
		}

		return $content;
	}

	public function twtr_srch_func( $wpdb, $table_prefix, $atts, $content="" ) {
		// prepare output variable
		$output = "";

		// check the post for a limit override
		if ( isset($atts['limit']) ) {
			$limit = $atts['limit'];
		} else {
			$limit = 1;
		}

		// get the table name regardless of prefix
		$get_table_name = self::$wpdb->get_results( 'Show tables LIKE "%twtr_srchr%"' );
		$table_name = reset($get_table_name{0});

		// get the existing tweets
		$twtr_query = get_post_meta( get_the_ID(), 'twtr_search_query')[0];

		// check if string is a user. if so format it for proper searching.
		if ( $twtr_query{0} === "@" ) {
			$twtr_query = "from:" . substr($twtr_query, 1);
		}

		$results = self::$wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE twtr_query = "'.$twtr_query.'" ORDER BY twtr_id DESC LIMIT ' . $limit );

		// replace template with values
		foreach ( $results as $value ) {
			$output .= self::twtr_srch_format( $atts, $content, $value );
		}

		return $output;
	}
}
?>