<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class VB_FGM_Settings_API {

    private $settings_api;

    function __construct() {
        $this->settings_api = WeDevs_Settings_API::getInstance();

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }


    function admin_menu() {
        // If crowdfunding is active set settings link to subpage in campaigns, else use options
        if ( is_plugin_active( 'appthemer-crowdfunding/crowdfunding.php' ) ) {
            $submenu_position = 'edit.php?post_type=download'; 
        } else {
            $submenu_position = 'options-general.php'; 
        }
        
        add_submenu_page( $submenu_position, 'Map Settings', 'Map Settings', 'manage_options', 'fundify-map-settings', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'fgm_map_settings',
                'title' => __( 'Map Settings', 'vb_fgm' )
            ),
            array(
                'id' => 'fgm_map_updater',
                'title' => __( 'Location Updater', 'vb_fgm' )
            ),
            array(
                'id' => 'fgm_map_help',
                'title' => __( 'Help', 'vb_fgm' )
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'fgm_map_settings' => array(
                array(
                    'name' => 'vb_fgm_api_key',
                    'label' => __( 'API Key', 'vb_fgm' ),
                    'desc' => __( 'Enter your Google maps key.', 'vb_fgm' ),
                    'type' => 'text',
                ),
                array(
                    'name' => 'vb_fgm_zoom_level',
                    'label' => __( 'Zoom Level', 'vb_fgm' ),
                    'desc' => __( 'Map Zoom Level', 'vb_fgm' ),
                    'type' => 'text',
                    'default' => 8,
                ),
                array(
                    'name' => 'vb_fgm_pan_control',
                    'label' => __( 'Pan control', 'vb_fgm' ),
                    'desc' => __( 'Show Pan control', 'vb_fgm' ),
                    'type' => 'checkbox'
                ),
                array(
                    'name' => 'vb_fgm_streetview_control',
                    'label' => __( 'Street View', 'vb_fgm' ),
                    'desc' => __( 'Show Street View control', 'vb_fgm' ),
                    'type' => 'checkbox'
                ),
                array(
                    'name' => 'vb_fgm_maptype_control',
                    'label' => __( 'Map type', 'vb_fgm' ),
                    'desc' => __( 'Show map type control', 'vb_fgm' ),
                    'type' => 'checkbox'
                ),
                array(
                    'name' => 'vb_fgm_marker_custom',
                    'label' => __( 'Custom marker image', 'vb_fgm' ),
                    'desc' => __( 'Enter URL of your custom marker image, use 49x56px png image', 'vb_fgm' ),
                    'type' => 'text',
                ),
                array(
                    'name' => 'vb_fgm_nr_campaigns',
                    'label' => __( 'Number of campaigns', 'vb_fgm' ),
                    'desc' => __( 'Number of campaigns to display on the map', 'vb_fgm' ),
                    'type' => 'text',
                    'default' => 999,
                ),
                array(
                    'name' => 'vb_fgm_remove_loc',
                    'label' => __( 'Remove location field', 'vb_fgm' ),
                    'desc' => __( 'Remove default location field', 'vb_fgm' ),
                    'type' => 'text',
                    'type' => 'checkbox',
                ),
                array(
                    'name' => 'vb_fgm_remove_script',
                    'label' => __( 'Dont load script' , 'vb_fgm' ),
                    'desc' => __( 'If you have any conflicts with other plugins, try not to load google map script with plugin', 'vb_fgm' ),
                    'type' => 'text',
                    'type' => 'checkbox',
                ),
            ),
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';
        settings_errors();

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        do_action('fgm_updater');

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}

$settings = new VB_FGM_Settings_API();