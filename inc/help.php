<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add updater to options page
 *
 */
function fgm_help_option() {
	echo '<h3>' . __( 'Plugin info', 'vb_fgm' ) . '</h3>';
	echo '<p>' . __( 'Please read..', 'vb_fgm' ) . '</p>';
	echo '<ol><li>' . __( 'This plugin is developed for use with <a href="https://wordpress.org/plugins/appthemer-crowdfunding/" target="_blank">Crowdfunding by Astoundify plugin</a> and <a href="http://themeforest.net/item/fundify-the-wordpress-crowdfunding-theme/4257622" target="_blank">Fundify theme</a>.', 'vb_fgm' ) . '</li>';
	echo '<li>' . __( 'Plugin will enable you to display your campaigns on a Google map with shortcode', 'vb_fgm' ) . '</li>';
	echo '<li>' . __( 'Plugin will add Google Map on "Start A Campaign", and "Edit Campaign" page, so users can select their location while they submit campaigns.', 'vb_fgm' ) . '</li>';
	echo '<li>' . __( 'If Geolocation is not available, map will center showing all campaigns', 'vb_fgm' ) . '</li>';
	echo '<li>' . __( 'If Geolocation is available, map will center to nearest campaigns in user current location', 'vb_fgm' ) . '</li>';
	echo '<li>' . __( 'Plugin uses Google Maps API and Geocoding service', 'vb_fgm' ) . '</li>';
	echo '<li>' . __( 'You need to add your Google Maps API key under Map Settings tab to enable use of this plugin', 'vb_fgm' ) . '<br />';
	echo __('How to obtain API key: <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">https://developers.google.com/maps/documentation/javascript/tutorial#api_key</a>', 'vb_fgm' ) . '</li>';
	echo '<li>' . __( 'You need to enable Geocoding Service under your Google Maps console', 'vb_fgm' ) . '<br />';
	echo __('How to enable "Geocoding API": <a href="https://developers.google.com/maps/documentation/geocoding/#api_key" target="_blank">https://developers.google.com/maps/documentation/geocoding/#api_key</a>', 'vb_fgm' ) . '</li>';
	echo '<li>' . __('If you already have active campaigns, you can run \'Location Updater\' to update current campaigns Latitude and Longitude', 'vb_fgm') . '</li>';
	echo '<li>' . __('To be able to do this, you need to have Geocoding Service enabled under your Google Maps console, otherwise it will not work', 'vb_fgm') . '</li>';
	echo '<li>' . __('Location updater is checking current campaign location and retrieves Latitude and Longitude via Google Maps API, and saves them to database.', 'vb_fgm') . '</li>';
	echo '</ol>';
	echo '<h3>' . __( 'Shortcode info', 'vb_fgm' ) . '</h3>';
	echo '<p><code>[fgm_campaigns height="350" show="1,2,3" campaigns="25"]</code></p>';
	echo '<p><b>height</b> = ' . __('Height of map, in pixels, default 350px', 'vb_fgm') . '</p>';
	echo '<p><b>show</b> = ' . __('Specific post(s) to show', 'vb_fgm') . '<br />';
	echo '<p>show="current": ' . __('Displays current campaign on a google map, can be used only on single campaign page', 'vb_fgm') . '<br />';
	echo '<p>show="1,2,3,4": ' . __('List of specified campagins by id to show', 'vb_fgm') . '</p>';
	echo '<p><b>campaigns</b> = ' . __('Number of campaings to display on map', 'vb_fgm') . '<br />';
	echo __('If omitted it will use value from settings', 'vb_fgm') . '</p>';
	
}

add_action( 'fgm_tab_fgm_map_help', 'fgm_help_option' );
