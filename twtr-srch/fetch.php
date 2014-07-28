<?
	error_reporting(0);
	$target = ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . 'wp-content/plugins/twtr-aggregatr/public/includes/twitter-search-endpoint.php';
	header( 'Location: ' . $target );
?>