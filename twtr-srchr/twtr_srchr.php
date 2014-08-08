<?php
/**
 * Twtr Srchr
 *
 * Simple plugin for pulling in twitter queries unique to each post.
 *
 * @package   Twtr_Srchr
 * @author    Mark Hayden <hi@markhayden.me>
 * @license   GPL-2.0+
 * @link      https://github.com/markhayden/twtr-srchr
 * @copyright 2014 Mark Hayden
 *
 * @wordpress-plugin
 * Plugin Name:       Twtr Srchr
 * Plugin URI:        https://github.com/markhayden/twtr-srchr
 * Description:       Simple plugin for pulling in twitter queries unique to each post.
 * Version:           0.0.1
 * Author:            Mark Hayden
 * Author URI:        https://github.com/markhayden/
 * Text Domain:       twtr_srchr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/markhayden/twtr-srchr
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-twtr_srchr.php' );

global $wpdb, $table_prefix;

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Twtr_Srchr', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Twtr_Srchr', 'deactivate' ) );
add_action( 'plugins_loaded', array( 'Twtr_Srchr', 'get_instance' ) );

// Load the class files to manage custom twitter handle field on posts.
include('includes/twtr-srch-formattr.php');

function siteURL(){
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'].'/';
    return $protocol.$domainName;
}

function format_shortcode( $atts, $content="" ) {
	$twtr_query_raw = get_post_meta( get_the_ID(), 'twtr_search_query');
	$twtr_site_url = siteURL();

	// determine if the tweets need to be pulled in
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $twtr_site_url . 'wp-content/plugins/twtr-srchr/public/includes/twitter-search-endpoint.php?q=' . $twtr_query_raw[0],
	));
	$resp = curl_exec($curl);
	curl_close($curl);

	// initiate class object
	$twtr_class = new twtrSrchFormattr();

	// return formatted content
	return $twtr_class->twtr_srch_func( $wpdb, $table_prefix, $atts, $content );
}

add_shortcode('twtr_srch', 'format_shortcode');

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-twtr_srchr-admin.php' );
	add_action( 'plugins_loaded', array( 'Twtr_Srchr_Admin', 'get_instance' ) );

}
