<?php
/**
 * Plugin Name: Send To SteadFast
 * Description: Send to SteadFast gives you ability to send your parcel request to SteadFast directly from your WooCommerce dashboard, it enables booking automation from your WordPress website. You can send your parcel to SteadFast one by one, or you can choose bulk send from "bulk action" dropdown.
 * Version: 1.0.0
 * Author: SteadFast
 * Text Domain: steadfast
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

defined( 'API_PLUGIN_URL' ) || define( 'API_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'API_PLUGIN_DIR' ) || define( 'API_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'API_PLUGIN_FILE' ) || define( 'API_PLUGIN_FILE', plugin_basename( __FILE__ ) );


/**
 * Plugin uninstall hooks.
 *
 * @return void
 */
function plugin_uninstall_hooks() {
	delete_option( 'api_settings_tab_api_secret_key', );
	delete_option( 'api_settings_tab_api_key', );
	delete_option( 'api_settings_tab_checkbox', );
	delete_option( 'api_settings_tab_notes', );

}

register_uninstall_hook( API_PLUGIN_FILE,  'plugin_uninstall_hooks'  );


if ( ! class_exists( 'SteadFast_Courier_Main' ) ) {
	/**
	 * Class SteadFast_Courier_Main
	 */
	class SteadFast_Courier_Main {

		function __construct() {

			$this->includes_files();

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'create_custom_settings_tab' ), 50 );
			add_action( 'woocommerce_settings_tabs_api_settings_tab', array( $this, 'add_custom_settings_fields' ) );
			add_action( 'woocommerce_update_options_api_settings_tab', array( $this, 'update_custom_settings_fields' ) );
		}


		/**
		 * @return void
		 */
		function includes_files() {
			require_once API_PLUGIN_DIR . '/includes/class-hooks.php';
		}

		/**
		 * @param $settings_tab
		 *
		 * @return mixed
		 */
		function create_custom_settings_tab( $settings_tab ) {

			$settings_tab['api_settings_tab'] = __( 'SteadFast API Settings', 'steadfast' );

			return $settings_tab;
		}

		/**
		 * @return void
		 */
		function add_custom_settings_fields() {
			woocommerce_admin_fields( $this->get_steadfast_settings() );
		}


		/**
		 * @return void
		 */
		function update_custom_settings_fields() {
			woocommerce_update_options( $this->get_steadfast_settings() );
		}


		/**
		 * Add setting fields WooCommerce setting tab.
		 *
		 * @return mixed|null
		 */
		function get_steadfast_settings() {

			$settings = array(

				'section_title'  => array(
					'id'   => 'api_settings_tab_settings_title',
					'type' => 'title',
					'name' => esc_html__( 'SteadFast Courier Settings', 'steadfast' ),
				),
				'api_checkbox'   => array(
					'id'   => 'api_settings_tab_checkbox',
					'type' => 'checkbox',
					'name' => esc_html__( 'Enable/Disable', 'steadfast' ),
				),
				'api_notes'      => array(
					'id'       => 'api_settings_tab_notes',
					'type'     => 'checkbox',
					'name'     => esc_html__( 'Notes', 'steadfast' ),
					'desc_tip' => esc_html__( "Please enable this check box if you want to send the customer's note", 'steadfast' ),
				),
				'api_key'        => array(
					'id'          => 'api_settings_tab_api_key',
					'type'        => 'password',
					'name'        => esc_html__( 'API Key *', 'steadfast' ),
					'placeholder' => esc_html__( 'API Key(required)', 'steadfast' ),
					'desc_tip'    => esc_html__( 'This field is required', 'steadfast' ),
				),
				'api_secret_key' => array(
					'id'          => 'api_settings_tab_api_secret_key',
					'type'        => 'password',
					'name'        => esc_html__( 'Secret Key *', 'steadfast' ),
					'placeholder' => esc_html__( 'Secret Key(required)', 'steadfast' ),
					'desc_tip'    => esc_html__( 'This field is required', 'steadfast' ),
				),
				'content_type'   => array(
					'id'          => 'api_settings_tab_api_content_type',
					'type'        => 'text',
					'name'        => esc_html__( 'Content Type', 'steadfast' ),
					'placeholder' => esc_html__( 'Content Type(optional)', 'steadfast' ),
				),
				'full_name'      => array(
					'id'          => 'api_settings_tab_api_full_name',
					'type'        => 'text',
					'name'        => esc_html__( 'Full Name', 'steadfast' ),
					'placeholder' => esc_html__( 'Full Name(optional)', 'steadfast' ),
				),
				'business'       => array(
					'id'          => 'api_settings_tab_api_business',
					'type'        => 'text',
					'name'        => esc_html__( 'Business Name', 'steadfast' ),
					'placeholder' => esc_html__( 'Business Name(optional)', 'steadfast' ),
				),
				'user_id'        => array(
					'id'          => 'api_settings_tab_api_user_id',
					'type'        => 'text',
					'name'        => esc_html__( 'User ID', 'steadfast' ),
					'placeholder' => esc_html__( 'User ID(optional)', 'steadfast' ),
				),
				'section_end'    => array(
					'id'   => 'api_settings_tab_sectionend',
					'type' => 'sectionend',
				),
			);

			return apply_filters( 'filter_api_settings_tab', $settings );
		}

		/**
		 * Admin scripts.
		 *
		 * @return void
		 */
		function admin_script() {
			wp_enqueue_script( 'steadFast-jquery', plugins_url( '/assets/admin/js/scripts.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_style( 'steadFast-style', API_PLUGIN_URL . 'assets/admin/css/style.css' );
			wp_localize_script( 'ajax-script', 'pluginObject', $this->localize_scripts() );
		}

		function localize_scripts() {
			return apply_filters( 'api_filters_localize_scripts', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			) );
		}


	}

	new SteadFast_Courier_Main();
}


