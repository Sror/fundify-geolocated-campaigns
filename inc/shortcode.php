<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function fgm_gmaps_shortcode( $atts, $content = null ) {

    global $post;

	ob_start();

	extract( shortcode_atts( array(
	    'height' => 350,
        'show' => 0,
        'campaigns' => -1,
	), $atts ) );

    // Get API key from Options
    $fgm_api = vb_fgm_plugin_options( 'vb_fgm_api_key', true ); 

    if( !$fgm_api ):
        echo __('You need to enter valid Google Maps API key before you can use shortcode', 'vb_fgm');
    else:

    if( $show == 'current' ):
        $show_posts = explode(',', $post->ID );
    elseif( $show !== 0 ):
        $show_posts = explode(',', $show );
    endif;
    ?>

	<script type="text/javascript">

	var map, infoBubble, locations;

    <?php
    $map_options = get_option( 'fgm_map_settings' );


    if ( $map_options ) {
        
        if( isset($map_options['vb_fgm_pan_control']) && $map_options['vb_fgm_pan_control'] == 'on' ) { $pan = 'true'; } else { $pan = 'false'; }
        if( isset($map_options['vb_fgm_maptype_control']) && $map_options['vb_fgm_maptype_control'] == 'on' ) { $type = 'true'; } else { $type = 'false'; }
        if( isset($map_options['vb_fgm_streetview_control']) && $map_options['vb_fgm_streetview_control'] == 'on' ) { $street = 'true'; } else { $street = 'false'; }
        if( isset($map_options['vb_fgm_zoom_level']) ) { $zoom = $map_options['vb_fgm_zoom_level']; } else { $street = 8; }
        if( isset($map_options['vb_fgm_nr_campaigns']) ) { $nr_posts = $map_options['vb_fgm_nr_campaigns']; } else { $nr_posts = -1; }
        if( isset($map_options['vb_fgm_marker_custom']) && $map_options['vb_fgm_marker_custom'] !== '' ) { $custom_icon = $map_options['vb_fgm_marker_custom']; } else { $custom_icon = false; }

    }

    if( $campaigns ) {
        $nr_campaigns = $campaigns;
    } elseif ( $nr_posts ) {
        $nr_campaigns = $nr_posts;
    } else {
        $nr_campaigns = -1;
    }

    if( $custom_icon ): ?>
        var markericon = new google.maps.MarkerImage('<?php echo $custom_icon; ?>', new google.maps.Size(49, 56) );
    <?php endif; ?>

	function initialize() {

        var mapOptions = {
            zoom: <?php echo $zoom; ?>,
            panControl: <?php echo $pan; ?>,
            mapTypeControl: <?php echo $type; ?>,
            streetViewControl: <?php echo $street; ?>,
            mapTypeControlOptions: {
                mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
            }
        }

        var bounds = new google.maps.LatLngBounds();
        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        <?php

        $cmp_args = array(
            'post_type' => 'download',
            'posts_per_page' => $nr_campaigns,
            'post__in' => $show_posts,
            'meta_query' => array(
                array(
                    'key' => 'campaign_vb_fgm_lat',
                ),
                array(
                    'key' => 'campaign_vb_fgm_lng',
                ),
            ),
        );

        if( $show === 0 ) {
            unset( $cmp_args['post__in']);
        }

        $cmp_query = new WP_Query( $cmp_args );

        if ( $cmp_query->have_posts() ) : ?>

        var locations = [
        <?php
        $i = 1;
        while ( $cmp_query->have_posts() ) : $cmp_query->the_post();

            $latlng = get_post_meta( get_the_ID(), 'campaign_vb_fgm_lat', true ) . ',' . get_post_meta( get_the_ID(), 'campaign_vb_fgm_lng', true );

            if ( $latlng !== '' ) : ?>
            	['<?php the_title(); ?>', <?php echo $latlng; ?>, <?php echo $i; ?>, '<?php the_permalink(); ?>', '<?php the_post_thumbnail("thumbnail"); ?>'],
            <?php endif;
        endwhile; ?>
        ];
        <?php else :
        endif; wp_reset_postdata(); ?>

        infoBubble = new InfoBubble({
            shadowStyle: 1,
            arrowStyle: 2,
            padding: 0,
            borderRadius: 4,
            minWidth: 160,
            maxWidth: 160,
            maxHeight: 165,
            hideCloseButton: false,
            disableAutoPan: true,
            borderColor: '#cccccc',
            borderWidth: 1,
            backgroundColor: '#fff',
            backgroundClassName: 'fgm_mapbubble',
        });
        
        var marker;
        var markers = new Array();

        // Add the markers and infowindows to the map
        if( locations ) {
	        for (var i = 0; i < locations.length; i++) {
	            marker = new google.maps.Marker({
					position: new google.maps.LatLng(locations[i][1], locations[i][2]),
					map: map,
                    <?php if( $custom_icon ): ?>
                    icon: markericon,
                    <?php endif; ?>
					url: locations[i][4],
	            });

	            var myLatLng = new google.maps.LatLng(locations[i][1], locations[i][2]);

	            google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
	              return function() {
	                infoWindowContent = '<div class="fgm_campaign-map-item"><a href="' + locations[i][4] +'">' + locations[i][5] +'<h2>'+locations[i][0]+'</h2></a></div>'
	                infoBubble.setContent(infoWindowContent);
	                infoBubble.open(map, marker);
	              }
	            })(marker, i));

	            google.maps.event.addListener(marker, 'click', (function(marker, i) {
	            	return function() {
	                	window.location.href = marker.url;
	            	}
	            })(marker, i));

	            bounds.extend(myLatLng);
	        	markers.push(marker);
	    	}
	    }

        // Try HTML5 geolocation
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
	            var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

	            var closest_marker = find_closest_marker( position.coords.latitude, position.coords.longitude);                  
	            map.setCenter(closest_marker['position']);
            }, function() {
                	handleNoGeolocation(true);
            });
        } else {
                // Browser doesn't support Geolocation
                handleNoGeolocation(false);
        }

        function find_closest_marker( lat1, lon1 ) {    
            var pi = Math.PI;
            var R = 6371; //equatorial radius
            var distances = [];
            var closest = -1;

            for( i=0; i<markers.length; i++ ) {  
                var lat2 = markers[i].position.lat();
                var lon2 = markers[i].position.lng();

                var chLat = lat2-lat1;
                var chLon = lon2-lon1;

                var dLat = chLat*(pi/180);
                var dLon = chLon*(pi/180);

                var rLat1 = lat1*(pi/180);
                var rLat2 = lat2*(pi/180);

                var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(rLat1) * Math.cos(rLat2); 
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                var d = R * c;

                distances[i] = d;
                if ( closest == -1 || d < distances[closest] ) {
                    closest = i;
                }
            }

            return markers[closest];
        }

        function handleNoGeolocation(errorFlag) {
        	if (errorFlag) {
            	var content = 'Error: The Geolocation service failed.';
          	} else {
            	var content = 'Error: Your browser doesn\'t support geolocation.';
          	}

        	var options = {
            	map: map,
            	position: new google.maps.LatLng(53.5,2.2),
            	content: content
          	};

        	map.setCenter(options.position);
        	map.fitBounds(bounds);
        }
	}

	google.maps.event.addDomListener(window, 'load', initialize);
	</script>
	<style type="text/css">
		#map-canvas {
		    height: <?php echo $height; ?>px;
		}
	</style>
	<div id="map-canvas"></div>

	<?php
    endif;

	$output_string = ob_get_contents();
	ob_end_clean();

	return $output_string;
}

add_shortcode( 'fgm_campaigns', 'fgm_gmaps_shortcode' );
