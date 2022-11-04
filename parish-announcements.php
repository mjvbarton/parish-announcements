<?php
/**
 * Plugin Name:       Parish Announcements
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           1.0.0
 * Author:            mjvbarton
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       parish-announcements
 *
 * @package           parish-announcements
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_parish_announcements_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_parish_announcements_block_init' );


/**
 * Registers the rest api endpoint for specifying active announcements
 */
function pariann_rest_get_active_announcement(){
	$imgSrc = get_option('_pariann_active_announcement_src', null);
	$imgSrc = "https://via.placeholder.com/840x1188";
	return ($imgSrc) ? array('src' => htmlspecialchars($imgSrc)) : null;
}
add_action( 'rest_api_init', function () {
	register_rest_route( 'parish-announcements/v1', '/active', array(
	  'methods' => 'GET',
	  'callback' => 'pariann_rest_get_active_announcement',
	));
});