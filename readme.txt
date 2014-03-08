=== Fundify Geolocated Campaigns ===
Contributors: bobz_zg
Tags: fundify, google maps, geolocation
Requires at least: 3.5
Tested up to: 3.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin enables you to show your Fundify Geolocated campagins on Google map with shortcode


== Description ==
This plugin is developed for use with [Crowdfunding by Astoundify plugin](https://wordpress.org/plugins/appthemer-crowdfunding/) and [Fundify theme](http://themeforest.net/item/fundify-the-wordpress-crowdfunding-theme/4257622).
Plugin will enable you to display your campaigns on a Google map with shortcode.
Plugin will add Google Map on "Start A Campaign", and "Edit Campaign" page, so users can select their location while they submit campaigns.
If Geolocation is not available, map will center showing all campaigns
If Geolocation is available, map will center to nearest campaigns in user current location
Plugin uses Google Maps API and Geocoding service
You need to add your Google Maps API key under Map Settings tab to enable use of this plugin
You need to enable Geocoding Service under your Google Maps console


= Important notice =
You need to add Google maps API key under Map Settings tab
How to obtain API key: https://developers.google.com/maps/documentation/javascript/tutorial#api_key
You need to enable "Geocoding Service" under your maps account
How to enable "Geocoding Service": https://developers.google.com/maps/documentation/geocoding/#api_key
To start the update process click the button below.



= Update old campaigns =
If you already have active campaigns, you can run 'Location Updater' to update current campaigns Latitude and Longitude
To be able to do this, you need to have Geocoding Service enabled under your Google Maps console, otherwise it will not work
Location updater is checking current campaign location and retrieves Latitude and Longitude via Google Maps API, and saves them to database. 
If it fails you will see a list of campaigns that did not update so you can update them manually.
Depending on number of campaigns you have, this may take some time so please be patient and don\'t leave this page until entire process is finisehd


= Shortcode info =
[fgm_campaigns height="350" show="1,2,3" campaigns="25"]
**height:** Height of map in pixels, default is 350px
**show:** Specific post(s) to show
**show="current":** Displays current campaign on a google map, can be used only on single campaign page
**show="1,2,3,4":** List of specified campagins by id to show
**campaigns="12":** Number of campaings to display on map, if omitted it will use value from settings
All parameters are optional.


== Installation ==
1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Campaign > Map settings' and add your Google Maps API code
4. Use shortcode to display google map [fgm_campaigns"]


== Screenshots ==

1. Campaigns on Google map

== Changelog ==

= 0.1 =
First release
