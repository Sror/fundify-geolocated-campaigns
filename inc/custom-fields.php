<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FGM_Astoundify_Crowdfunding_Fields {

        /**
         * @var $instance
         */
        private static $instance;

        /**
         * Make sure only one instance is only running.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param void
         * @return object $instance The one true class instance.
         */
        public static function instance() {
                if ( ! isset ( self::$instance ) ) {
                        self::$instance = new self;
                }

                return self::$instance;
        }

        /**
         * Start things up.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param void
         * @return void
         */
        public function __construct() {
                $this->setup_globals();
                $this->setup_actions();
        }

        /**
         * Set some smart defaults to class variables.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param void
         * @return void
         */
        private function setup_globals() {
                $this->file         = __FILE__;

                $this->basename     = plugin_basename( $this->file );
                $this->plugin_dir   = plugin_dir_path( $this->file );
                $this->plugin_url   = plugin_dir_url ( $this->file ); 
        }

        /**
         * Hooks and filters.
         *
         * We need to hook into a couple of things:
         * 1. Output fields on frontend, and save.
         * 2. Output fields on backend, and save.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param void
         * @return void
         */
        private function setup_actions() {
                /**
                 * Filter the default fields that ship with Crowdfunding.
                 * The `form_fields` method is what we use to add our own custom fields.
                 */
                add_filter( 'atcf_shortcode_submit_fields', array( $this, 'form_fields' ) );

                /**
                 * When Crowdfunding is saving all of the default field data, we need to also
                 * save our custom fields. 
                 *
                 * If using a simple text field, or value that can be saved directly, the item
                 * will be saved automatically. Otherwise, you need to hook into the save action
                 * and perform your own processing and saving.
                 *
                 * Example:
                 *
                 * Where `subtitle` is the key of the field added to the fields array.
                 *
                 * The callback has four parameters: $key, $field, $campaign, $fields
                 */
                add_action( 'atcf_shortcode_submit_save_field_lat', array( $this, 'submit_save_field_lat' ), 10, 4 );
                add_action( 'atcf_shortcode_submit_save_field_lng', array( $this, 'submit_save_field_lng' ), 10, 4 );

                /**
                 * Load the saved data for this field.
                 * Can do some fancy stuff, but will most likely just be retreiving our saved meta value.
                 */
                add_filter( 'atcf_shortcode_submit_saved_data_lat', array( $this, 'saved_data_lat' ), 10, 3 );
                add_filter( 'atcf_shortcode_submit_saved_data_lng', array( $this, 'saved_data_lng' ), 10, 3 );

                /**
                 * Output the field in the Campaign metabox.
                 */
                add_action( 'atcf_metabox_campaign_info_after', array( $this, 'admin_submit_output' ) );

                /**
                 * Make sure our meta is saved if updated via the admin panel.
                 */
                add_filter( 'edd_metabox_fields_save', array( $this, 'admin_submit_save_fields' ) );
        }

        /**
         * Add fields to the submission form.
         *
         * Currently the fields must fall between two sections: "job" or "company". Until
         * WP Job Manager filters the data that passes to the registration template, these are the
         * only two sections we can manipulate.
         *
         * You may use a custom field type, but you will then need to filter the `job_manager_locate_template`
         * to search in `/templates/form-fields/$type-field.php` in your theme or plugin.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param array $fields The existing fields
         * @return array $fields The modified fields
         */
        function form_fields( $fields ) {
                $fields[ 'vb_fgm_lat' ] = array(
                        'label'       => null,       // The label for the field
                        'type'        => 'hidden',           // text, radio, checkbox, select
                        'placeholder' => null,             // Placeholder value
                        'default'     => null,             // Default Value
                        'required'    => true,             // If the field is required to submit the form
                        'editable'    => true,             // If it should appear on the edit screen
                        'priority'    => 4                 // Where should the field appear based on the others
                );
                $fields[ 'vb_fgm_lng' ] = array(
                        'label'       => null,       // The label for the field
                        'type'        => 'hidden',           // text, radio, checkbox, select
                        'placeholder' => null,             // Placeholder value
                        'default'     => null,             // Default Value
                        'required'    => true,             // If the field is required to submit the form
                        'editable'    => true,             // If it should appear on the edit screen
                        'priority'    => 4                 // Where should the field appear based on the others
                );

                /**
                 * Repeat this for any additional fields.
                 */

                return $fields;
        }

        /**
         * Save callback, if custom processing is needed.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param string $key
         * @param array $field
         * @param int $campaign
         * @param array $fields
         * @return void
         */
        function submit_save_field_lat( $key, $field, $campaign, $fields ) {
                $value = sanitize_text_field( $field[ 'value' ] );

                /**
                 * Do something else with the data potentially...
                 */

                update_post_meta( $campaign, 'campaign_vb_fgm_lat', $value );
        }
        function submit_save_field_lng( $key, $field, $campaign, $fields ) {
                $value = sanitize_text_field( $field[ 'value' ] );

                /**
                 * Do something else with the data potentially...
                 */

                update_post_meta( $campaign, 'campaign_vb_fgm_lng', $value );
        }
        

        /**
         * Get saved data.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param null $data
         * @param string $key
         * @param object $campaign
         * @return void
         */
        function saved_data_lat( $data, $key, $campaign ) {
                /**
                 * Potentially do something more intensive, like getting term values, etc
                 */

                // Grab the meta
                $lat = $campaign->__get( 'campaign_vb_fgm_lat' );

                return $lat;
        }
        function saved_data_lng( $data, $key, $campaign ) {
                /**
                 * Potentially do something more intensive, like getting term values, etc
                 */

                // Grab the meta
                $lat = $campaign->__get( 'campaign_vb_fgm_lng' );

                return $lat;
        }

        /**
         * Subtitle field on the backend.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param object $campaign
         * @return void
         */
        function admin_submit_output( $campaign ) {

                $lat = $campaign->__get( 'campaign_vb_fgm_lat' );
                $lng = $campaign->__get( 'campaign_vb_fgm_lng' );
        ?>

                <p>
                        <label for="campaign_vb_fgm_lat"><strong><?php _e( 'Latitude' ); ?></strong></label><br />
                        <input type="text" name="campaign_vb_fgm_lat" id="campaign_vb_fgm_lat" value="<?php echo esc_attr( $lat ); ?>" class="regular-text" />
                </p>
                <p>
                        <label for="campaign_vb_fgm_lng"><strong><?php _e( 'Longitude' ); ?></strong></label><br />
                        <input type="text" name="campaign_vb_fgm_lng" id="campaign_vb_fgm_lng" value="<?php echo esc_attr( $lng ); ?>" class="regular-text" />
                </p>
                
        <?php
        }

        /**
         * Save subtitle key on the backend.
         *
         * @since Custom Fields for Crowdfunding 1.0
         *
         * @param array $fields
         * @return array $fields
         */
        function admin_submit_save_fields( $fields ) {
                $fields[] = 'campaign_vb_fgm_lat';
                $fields[] = 'campaign_vb_fgm_lng';


                return $fields;
        }
}
add_action( 'init', array( 'FGM_Astoundify_Crowdfunding_Fields', 'instance' ) );