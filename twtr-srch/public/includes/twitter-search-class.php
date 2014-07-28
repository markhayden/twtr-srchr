<?
class performSearch {

	public static $key, $secret, $bearer_token;

	public function __construct( $k, $s ) {
		self::$key = $k;
		self::$secret = $s;
		self::$bearer_token = self::get_bearer_token();
	}

	private function __destroy() {
		self::invalidate_bearer_token();
	}

	/**
	*	Get the Bearer Token.
	*/
	private function get_bearer_token(){
		// url encode the key and secret
		$encoded_consumer_key = urlencode(self::$key);
		$encoded_consumer_secret = urlencode(self::$secret);

		// concatenate the key and secret into x:x format per twitters docs
		$bearer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;

		// base64 encode the token
		$base64_encoded_bearer_token = base64_encode($bearer_token);

		// send request
		$url = "https://api.twitter.com/oauth2/token"; // url to send data to for authentication
		$headers = array(
			"POST /oauth2/token HTTP/1.1",
			"Host: api.twitter.com",
			"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
			"Authorization: Basic ".$base64_encoded_bearer_token."",
			"Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
			"Content-Length: 29"
		);

		$ch = curl_init();  // setup a curl
		curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
		curl_setopt($ch, CURLOPT_POST, 1); // send as post
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); // post body/fields to be sent
		$header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$retrievedhtml = curl_exec ($ch); // execute the curl
		curl_close($ch); // close the curl
		$output = explode("\n", $retrievedhtml);
		$bearer_token = '';

		foreach($output as $line){
			if($line === false)
			{
				// there was no bearer token
			}else{
				$bearer_token = $line;
			}
		}

		$bearer_token = json_decode($bearer_token);

		return $bearer_token->{'access_token'};
	}

	/**
	* Invalidates the Bearer Token
	* Should the bearer token become compromised or need to be invalidated for any reason,
	* call this method/function.
	*/
	private function invalidate_bearer_token(){
		// url encode the key and secret
		$encoded_consumer_key = urlencode(self::$key);
		$encoded_consumer_secret = urlencode(self::$secret);

		$consumer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
		$base64_encoded_consumer_token = base64_encode($consumer_token);

		// make call to twitter for invalidation
		$url = "https://api.twitter.com/oauth2/invalidate_token"; // url to send data to for authentication
		$headers = array(
			"POST /oauth2/invalidate_token HTTP/1.1",
			"Host: api.twitter.com",
			"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
			"Authorization: Basic ".$base64_encoded_consumer_token."",
			"Accept: */*",
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".(strlen(self::$bearer_token)+13).""
		);

		$ch = curl_init();  // setup a curl
		curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
		curl_setopt($ch, CURLOPT_POST, 1); // send as post
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
		curl_setopt($ch, CURLOPT_POSTFIELDS, "access_token=".self::$bearer_token.""); // post body/fields to be sent
		$header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$retrievedhtml = curl_exec ($ch); // execute the curl
		curl_close($ch); // close the curl

		return $retrievedhtml;
	}

	/**
	* Search
	*/
	public function search($query, $result_type='recent', $count='10'){
		$url = "https://api.twitter.com/1.1/search/tweets.json"; // base url
		$q = urlencode(trim($query)); // query term
		$formed_url ='?q=' . $q; // fully formed url
		if($result_type!='mixed'){$formed_url = $formed_url.'&result_type='.$result_type;} // result type - mixed(default), recent, popular
		if($count!='50'){$formed_url = $formed_url.'&count='.$count;} // results per page - defaulted to 15
		$formed_url = $formed_url.'&include_entities=true'; // makes sure the entities are included, note @mentions are not included see documentation
		$headers = array(
			"GET /1.1/search/tweets.json".$formed_url." HTTP/1.1",
			"Host: api.twitter.com",
			"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
			"Authorization: Bearer ".self::$bearer_token."",
		);
		$ch = curl_init();  // setup a curl
		curl_setopt($ch, CURLOPT_URL,$url.$formed_url);  // set url to send to
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
		$retrievedhtml = curl_exec ($ch); // execute the curl
		curl_close($ch); // close the curl
		return $retrievedhtml;
	}
}
?>