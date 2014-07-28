<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Twtr_Srchr
 * @author    Mark Hayden <hi@markhayden.me>
 * @license   GPL-2.0+
 * @link      https://github.com/markhayden/twtr-srchr
 * @copyright 2014 Mark Hayden
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	global $wpdb;

	$table_name = $wpdb->prefix . "twtr_srchr";
	$options_table_name = $wpdb->prefix . "options";

	$sql = "DROP TABLE `".$table_name."`";
	dbDelta( $sql );

	exit;
}