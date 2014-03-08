<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add updater to options page
 *
 */
function fgm_updater_option() {
	echo '<h3>' . __( 'Update campaigns', 'vb_fgm' ) . '</h3>';
	echo '<p>' . __( 'Use this tool to update your current campaigns that don\'t have latitude and longitude set.', 'vb_fgm' ) . '<br />';
	echo __( 'Plugin will try to update latitude and longitude from currrent location field using Google Geocoding API service. If it fails you will see a list of campaigns that did not update so you can update them manually.', 'vb_fgm' ) . '<br />';
	echo __( 'Depending on number of campaigns you have, this may take some time so please be patient and don\'t leave this page until entire process is finisehd', 'vb_fgm' ) . '</p>';
	echo '<h3>' . __('Important notice', 'vb_fgm' ) . '</h3>';
	echo '<ol><li>' . __('You need to add Google maps API key under Map Settings tab', 'vb_fgm' ) . '<br />';
	echo __('How to obtain API key: <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">https://developers.google.com/maps/documentation/javascript/tutorial#api_key</a>', 'vb_fgm' ) . '</li>';
	echo '<li>' . __('You need to enable "Geocoding Service" under your maps account<br /> How to enable "Geocoding Service": <a href="https://developers.google.com/maps/documentation/geocoding/#api_key" target="_blank">https://developers.google.com/maps/documentation/geocoding/#api_key</a>', 'vb_fgm' ) . '</li></ol>';
	echo '<p>' . __('To start the update process click the button below.', 'vb_fgm' ) . '</p>';

	$overall_campaigns = new WP_Query( array('post_type' => 'download' ) );

	$to_update = new WP_Query( array(
		'post_type' => 'download',
		'meta_query' => array(
                array(
                    'key' => 'campaign_vb_fgm_lat',
                    'value' => '',
                    'compare'=>'NOT EXISTS',
                ),
                array(
                    'key' => 'campaign_vb_fgm_lng',
                    'value' => '',
                    'compare'=>'NOT EXISTS',
                ),
                array(
                    'key' => 'campaign_location',
                    'value' => '',
                    'compare'=>'!=',
                ),
            ),
		)
	);
	

	echo '<p>' . __( 'Overall number of campaigns: ', 'vb_fgm' ) . $overall_campaigns->found_posts . '</p>';
	echo '<p>' . __( 'Campaigns need to update: ', 'vb_fgm' ) . $to_update->found_posts . '</p>';

	echo '<a class="button button-primary" id="fgm-update-campaigns">'. __( 'Update campaigns', 'vb_fgm' ) .'</a>';

	echo '<div class="fgm-update-status">';
	echo '<div class="fgm-update-results" style="display:none;"></div>';
	echo '<div class="ajax-loader" style="display:none;"><img src="'.FGM_PLUGIN_URL . 'assets/img/ajax-loader.gif'.'" /> ' . __('Updating campaign locations, please wait...','vb_fgm') . '</div>';
	echo '</div>';
}

add_action( 'fgm_tab_fgm_map_updater', 'fgm_updater_option' );



/**
 * Loop trough campaigns and update latitude and longitude based on location field
 * using google mapi geolocation
 */
function vb_fgm_updater() {

	 // Verify nonce
	if( !isset( $_POST['fgm_nonce'] ) || !wp_verify_nonce( $_POST['fgm_nonce'], 'fgm_nonce' ) )
		die('Permission denied');

	// Get API key from Options
	$fgm_api = vb_fgm_plugin_options( 'vb_fgm_api_key', true );	

	if( !$fgm_api ) {
		die('You need to enter valid Google Maps API key before making update');
	}

	$args = array(
		'post_type' => 'download',
		'posts_per_page' => -1
		);

	$updates = new WP_Query( $args );

	if ( $updates->have_posts() ) : 

		/**
		 * Will use this to count number of campaigns updated
		 * 
		 */
		$i = 0; // Success
		$u = 0; // Failed
		while ( $updates->have_posts() ) : $updates->the_post();

		// Get post meta
		$has_lat = get_post_meta( get_the_ID(), 'campaign_vb_fgm_lat', true );
		$has_lng = get_post_meta( get_the_ID(), 'campaign_vb_fgm_lng', true );
		$current_loc = get_post_meta( get_the_ID(), 'campaign_location', true );

		/**
		 * Check if current post has location entered but does not have latitude and longitude set
		 * If found it will try to update latitude and longitude
		 * If Google returns error it will skip campaign and show URL's of all campaigns that are failed to update
		 */
		if( !$has_lat && !$has_lng && $current_loc ):

			/**
			 * Remove everything after first comma.
			 * Sometimes they add country next to city name and Google will return error 400
			 * I will strip country and search only for city
			 */
			$current_loc = preg_replace('/^([^,]*).*$/', '$1', $current_loc);
			// Encode address
			$current_loc = urlencode( $current_loc );

			// Address to get using current location entered
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$current_loc.'&sensor=true&key='.$fgm_api;
			$response = wp_remote_get( $url );
			
			/**
			 * If is response 200, update the post meta with latitude and longitude
			 *
			 */
			if( $response['response']['code'] == 200 ):

				$response_body = wp_remote_retrieve_body( $response );

				$decoded = json_decode( $response_body );

				// Get first result
				$lat = $decoded->results['0']->geometry->location->lat;
				$lng = $decoded->results['0']->geometry->location->lng;

				// Update post meta
				update_post_meta( get_the_ID(), 'campaign_vb_fgm_lat', $lat );
				update_post_meta( get_the_ID(), 'campaign_vb_fgm_lng', $lng );

				// Update counter
				$i++;
			else:
				// Update failed campaigns counter
				$u++;

				// Collect failed campaigns in array
				$failed[] = get_permalink( get_the_ID() );
			endif;

		endif;

	endwhile; ?>
	<!-- post navigation -->
	<?php else: ?>
		<p><?php _e('No campaigns found to update', 'vb_fgm'); ?></p>
	<?php endif; wp_reset_postdata();
	// Show result of updates
	echo '<p>' . __('Campaigns found: ', 'vb_fgm') . $updates->found_posts .'</p>';
	echo '<p>' . __('Campaigns updated: ', 'vb_fgm') . $i .'</p>';
	echo '<p>' . __('Failed to update: ', 'vb_fgm') . $u .'</p>';

	/**
	 * If some campaigns fail to update latitude and longitude
	 * show permalinks for them so user can update them manually
	 *
	 */
	if( $u ):
		echo '<h3>' . __('Campaigns failed to update', 'vb_fgm') . '</h3>';
		echo '<p>' . __('You can review this campaigns and manually update location', 'vb_fgm') . '</p>';
		foreach( $failed as $link ):
			echo $link . '<br />';
		endforeach;
	endif;

	die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_nopriv_vb_fgm_updater', 'vb_fgm_updater' );
    add_action( 'wp_ajax_vb_fgm_updater', 'vb_fgm_updater' );
}