<?php /*
    Plugin Name: Fundify Geolocated Campaigns
    Plugin URL: #
    Description: This plugin enables you to show your Fundify Geolocated campaigns on Google map with shortcode
    Author: Bobz
    Version: 0.1
    Author URI: http://www.bobz.co
    Text Domain: vb_fgm
    Domain Path: /lang
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FGM_VERSION', '0.1' );
define( 'FGM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FGM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

include_once ( FGM_PLUGIN_PATH . 'inc/custom-fields.php' );
include_once ( FGM_PLUGIN_PATH . 'inc/class.settings-api.php' );
include_once ( FGM_PLUGIN_PATH . 'inc/options-page.php' );
include_once ( FGM_PLUGIN_PATH . 'inc/campaign-edit.php' );
include_once ( FGM_PLUGIN_PATH . 'inc/shortcode.php' );
include_once ( FGM_PLUGIN_PATH . 'inc/updater.php' );
include_once ( FGM_PLUGIN_PATH . 'inc/help.php' );


/**
 * Include styles and scripts
 *
 */
function vb_fgm_styles() {
	wp_register_style( 'fgm_style', FGM_PLUGIN_URL . 'assets/css/style.css' );
	wp_enqueue_style( 'fgm_style' );
}
add_action( 'wp_enqueue_scripts', 'vb_fgm_styles' );

function vb_fgm_script() {

	$apikey = vb_fgm_plugin_options('vb_fgm_api_key', true );

	wp_register_script( 'fgm_gmaps', 'http://maps.googleapis.com/maps/api/js?key='.$apikey.'&sensor=true' );
	wp_register_script( 'fgm_gmaps_bubble', FGM_PLUGIN_URL . 'assets/js/infobubble.min.js' );

	// Include google maps script if not disabled in plugin options
	if( !vb_fgm_plugin_options('vb_fgm_remove_script', 'on' ) ) {
		wp_enqueue_script( 'fgm_gmaps' );
	}
	wp_enqueue_script( 'fgm_gmaps_bubble' );
}
add_action( 'wp_enqueue_scripts', 'vb_fgm_script' );


/**
 * Include admin styles and scripts
 *
 */
function vb_fgm_admin_styles() {
    wp_register_style( 'vb_fgm_admin_css', FGM_PLUGIN_URL .'assets/css/fgm-admin-style.css' );

    if( isset( $_GET['page'] ) && $_GET['page'] == 'fundify-map-settings' ) {
	 	wp_enqueue_style( 'vb_fgm_admin_css' );
	}
    
}
add_action( 'admin_enqueue_scripts', 'vb_fgm_admin_styles' );

function vb_fgm_admin_script() {
	wp_register_script( 'fgm_ajax_js', FGM_PLUGIN_URL .'assets/js/admin-ajax-js.js' );

    if( isset( $_GET['page'] ) && $_GET['page'] == 'fundify-map-settings' ) {
	 	wp_enqueue_script( 'fgm_ajax_js' );

	 	wp_localize_script( 'fgm_ajax_js', 'fgm_var', array(
			'fgm_ajax_url' => admin_url( 'admin-ajax.php' ),
			'fgm_nonce' => wp_create_nonce( 'fgm_nonce' ),
			)
		);
	}

}
add_action( 'admin_enqueue_scripts', 'vb_fgm_admin_script' );

/*
 * Get plugin options
 *
 * @param $key - Key to search inside options array
 * @param $value - Value that we excpect to find
 */
function vb_fgm_plugin_options( $key, $value ) {
	
	$options = get_option( 'fgm_map_settings' );

	if( array_key_exists( $key, $options) && $options[$key] == $value ) {
		return $options[$key];
	} else {
		return false;
	}

}

/**
 * Add settings link on plugin page
 *
 */
function vb_fgm_settings_link($links) { 
	// If crowdfunding is active set settings link to subpage in campaigns, else use options
	if ( is_plugin_active( 'appthemer-crowdfunding/crowdfunding.php' ) ) {
		$settings_link = '<a href="edit.php?post_type=download&page=fundify-map-settings">Settings</a>'; 
	} else {
		$settings_link = '<a href="options-general.php?page=fundify-map-settings">Settings</a>'; 
	}
	
	array_unshift($links, $settings_link); 
	return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter( "plugin_action_links_$plugin", 'vb_fgm_settings_link' );


/**
 * Add admin notice on settings page if Fundify and Crowdfunding plugin are not installed
 *
 */
function vb_fgm_fundify_notice($links) { 
	// If crowdfunding is active set settings link to subpage in campaigns, else use options
	if ( !is_plugin_active( 'appthemer-crowdfunding/crowdfunding.php' ) && isset( $_GET['page'] ) && $_GET['page'] == 'fundify-map-settings' ) { ?>
	    <div class="updated">
	        <p><?php _e( 'Notice: You need to install <a href="https://wordpress.org/plugins/appthemer-crowdfunding/" target="_blank">Crowdfunding by Astoundify plugin</a> and <a href="http://themeforest.net/item/fundify-the-wordpress-crowdfunding-theme/4257622" target="_blank">Fundify theme</a> in order to use this plugin', 'vb_fgm' ); ?></p>
	    </div>
    <?php
	}
}
add_action( 'admin_notices', 'vb_fgm_fundify_notice' );


/**
 * Unset location field if set in options
 *
 */
function wes_atcf_shortcode_submit_fields( $fields ) {

	if( vb_fgm_plugin_options( 'vb_fgm_remove_loc', 'on' ) ) {
		unset( $fields['location'] );
		return $fields;
	}
}
add_filter( 'atcf_shortcode_submit_fields', 'wes_atcf_shortcode_submit_fields' );