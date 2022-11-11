<?php
/**
 * Plugin Name:       Parish Announcements
 * Description:       A simple block for displaying announcements from the parish in PDF format. Requires php_imagick.
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

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The content where converted pdfs will be contained
 */
define('PARIANN_UPLOAD_DIR', WP_CONTENT_DIR . '/uploads/parish-announcements');
define('PARIANN_UPLOAD_URL', WP_CONTENT_URL . '/uploads/parish-announcements');
require_once("vendor/autoload.php");

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_parish_announcements_block_init() {
	$block = json_decode(file_get_contents(__DIR__ . '/build/block.json'));
	register_block_type( __DIR__ . '/build' , array(
		'title' => esc_html__( $block->title, 'parish-announcements'),
		'description' => esc_html__($block->description, 'parish-announcements'),
	));	
}
add_action( 'init', 'create_block_parish_announcements_block_init' );


/**
 * Initializes the Carbon Fields extension and the plugin's text domain
 */
function pariann_plugin_init(){
	Carbon_Fields::boot();
	load_plugin_textdomain( 'parish-announcements', false, basename(dirname(__FILE__)) . '/languages/' );		
}
add_action( 'after_setup_theme', 'pariann_plugin_init');


/**
 * Registers the rest api endpoint for specifying active announcements
 * 
 * @return array|null the string containing the source url of the active announcement image
 */
function pariann_rest_get_active_announcement(){
	$imgSrc = get_option('_pariann_active_announcement_src', null);
	return ($imgSrc) ? array('src' => htmlspecialchars($imgSrc)) : null;
}
add_action( 'rest_api_init', function () {
	register_rest_route( 'parish-announcements/v1', '/active', array(
	  'methods' => 'GET',
	  'callback' => 'pariann_rest_get_active_announcement',
	));
});


/**
 * Notifies the administrator that the imagick extension was not detected during the plugin activation
 */
function pariann_notice_no_imagick(){
	?>
	<div class="notice notice-error">
		<p><?php printf(esc_html__( 'Required extension %s not found.', 'parish-announcements' ), 'php_imagick'); ?></p>		
	</div>
	<?php
}


/**
 * Activates the plugin - checking if the php_imagick is enabled, and creates upload directory for this plugin
 */
function pariann_activate_plugin(){
	if(!extension_loaded('imagick')){
		add_action('admin_notices', 'pariann_error_no_imagick');
		deactivate_plugins(__FILE__, true);		
	}

	if(!file_exists(PARIANN_UPLOAD_DIR)){
		mkdir(PARIANN_UPLOAD_DIR);
	}
}
register_activation_hook( __FILE__, 'pariann_activate_plugin' );


/**
 * Deletes the converted files and options on uninstallation
 */
function pariann_uninstall_plugin(){	
	if(file_exists(PARIANN_UPLOAD_DIR)){
		if(file_exists(PARIANN_UPLOAD_DIR . '/active.jpg')){
			wp_delete_file(PARIANN_UPLOAD_DIR . '/active.jpg');
		}
		rmdir(PARIANN_UPLOAD_DIR);
	}
	delete_option('_pariann_active_announcement_src');	
	carbon_set_theme_option('pariann_selected_announcement', null);	
}
register_uninstall_hook( __FILE__, 'pariann_uninstall_plugin');


/**
 * Registers the Carbon Fields Container for plugin administration
 */
function pariann_register_carbon_fields(){
	$c = Container::make('theme_options', 'parish-announcemetns', __('Parish Announcements', 'parish-announcements'));
	$c->set_icon('dashicons-megaphone');
	$c->set_page_menu_position(25);	
	$c->add_fields(array(
		Field::make('file', 'pariann_selected_announcement', __('Active announcement', 'parish-announcements'))
		->set_type(array('application/pdf')),

		Field::make('html', 'pariann_plugin_help', __('How to use the plugin', 'parish-announcements'))
		->set_html(
			sprintf(
				'<h3>%s</h3><p>%s</p><ol><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ol>', 
				esc_html__('How to use the plugin', 'parish-announcements'),
				esc_html__('This plugin provides a simple way to display parish announcements in PDF file at the website. The steps are simple:', 'parish-announcements'),
				esc_html__('Prepare the PDF file with announcements using your favourite editor (eg. MS Word)', 'parish-announcements'),
				esc_html__('Hit the upload button to upload the file in the media gallery', 'parish-announcements'),
				esc_html__('Click on the save button to perform the conversion', 'parish-announcements'),
				esc_html__('Go to a page where you want to display the block', 'parish-announcements'),
				esc_html__('Find the block Parish Announcements in the block gallery', 'parish-announcements'),
				esc_html__('Save the page and you are done', 'parish-announcements'),
			)),
	));	
}
add_action('carbon_fields_register_fields', 'pariann_register_carbon_fields');


/**
 * Converts the media selected as active in pdf format to JPG
 */
function pariann_convert_file(){
	$fileId = carbon_get_theme_option('pariann_selected_announcement');
	$output = PARIANN_UPLOAD_DIR . '/active.jpg';
	$outputUrl = PARIANN_UPLOAD_URL . '/active.jpg';
	if($fileId){
		$file = get_attached_file( $fileId);
		$im = new \Imagick();
		$im->setResolution(600,600);
		$im->readimage($file.'[0]');
		$im->setImageFormat('jpeg');
		$im->writeImage($output);
		$im->clear();
		$im->destroy();

		if(!get_option('_pariann_active_announcement_src')){
			add_option('_pariann_active_announcement_src', $outputUrl);
		} else {
			update_option('_pariann_active_announcement_src', $outputUrl);
		}		
	}	
}
add_action('carbon_fields_theme_options_container_saved', 'pariann_convert_file');