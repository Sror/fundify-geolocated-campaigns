<?php
function append_mapdiv() {

	global $post;
	$current_lat = get_post_meta( $post->ID, 'campaign_vb_fgm_lat', true );
	$current_lng = get_post_meta( $post->ID, 'campaign_vb_fgm_lng', true );

	if( $current_lat && $current_lng ) {
		$current_location = $current_lat . ',' . $current_lng;
	} else {
		$current_location = false;
	}
?>
<script type="text/javascript">
jQuery(document).ready( function() {

var map;


function initialize() {

	var markersArray = [];

	var mapOptions = {
	    zoom: 7,
	    mapTypeControl: false,
	    streetViewControl: false
	};

	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	  // Add click listener
	google.maps.event.addListener(map, 'click', function(event) {
		placeMarker(event.latLng);

	    var myLatLng = event.latLng;
	    var lat = myLatLng.lat();
	    var lng = myLatLng.lng();

	    document.getElementById("vb_fgm_lat").value = lat;
	    document.getElementById("vb_fgm_lng").value = lng;

	});

	<?php
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If editing existing campaign, load campagin latlng and add marker
	 * to google map
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	if( $current_location ): ?>
		var current_latlng = new google.maps.LatLng(<?php echo $current_location; ?>);
	  
		placeMarker(current_latlng);
		map.setCenter(current_latlng);
	<?php endif; ?>

	  // Place marker function
	function placeMarker(location) {
	      // first remove all markers if there are any
	    deleteOverlays();

	    var marker = new google.maps.Marker({
	        position: location, 
	        map: map
	    });

	    // add marker in markers array
	    markersArray.push(marker);
	} // End place marker

	  // Deletes all markers in the array by removing references to them
	function deleteOverlays() {
	    if (markersArray) {
	        for (i in markersArray) {
	            markersArray[i].setMap(null);
	        }
	    	markersArray.length = 0;
	    }
	  } // End delete

	// Try HTML5 geolocation
	if(navigator.geolocation) {
	    navigator.geolocation.getCurrentPosition(function(position) {
	    	var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

	    	var infowindow = new google.maps.InfoWindow({
		        map: map,
		        position: pos,
		        content: 'Please select your location'
	      	});

	      	google.maps.event.addListener(map, 'click', function(event) {
				infowindow.close();
			});

	    	map.setCenter(pos);
	    }, function() {
	    	handleNoGeolocation(true);
	    });
	} else {
	    // Browser doesn't support Geolocation
	    handleNoGeolocation(false);
	} // End try to get location

	function handleNoGeolocation(errorFlag) {
		if (errorFlag) {
	    	var content = 'The Geolocation service cannot find your location.<br />Please select your current location.';
		} else {
	    	var content = 'Error: Your browser doesn\'t support geolocation.';
		}

		var options = {
		    map: map,
		    <?php if( $current_location ): ?>
		    position: new google.maps.LatLng(<?php echo $current_location; ?>),
		    <?php else: ?>
		    position: new google.maps.LatLng(50, 5),
		    <?php endif; ?>
		    content: content
		};

		<?php if( !$current_location ): ?>
		var infowindow = new google.maps.InfoWindow(options);
		<?php endif; ?>
		map.setCenter(options.position);

		google.maps.event.addListener(map, 'click', function(event) {
			infowindow.close();
		});
	}
} // End init
google.maps.event.addDomListener(window, 'load', initialize);


});
</script>
<p class="atcf-submit-campaign-googlemap">
	<label><?php _e('Please select your location', 'vb_fgm'); ?></label>
	<div id="map-canvas"></div>
</p>
<?
}
add_filter('atcf_shortcode_submit_field_after_organization', 'append_mapdiv');