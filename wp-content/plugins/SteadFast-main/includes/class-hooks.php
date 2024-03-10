<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SteadFast_Hooks' ) ) {

	/**
	 * SteadFast Order Page
	 */
	class SteadFast_Hooks {

		protected static $_instance = null;

		private $success = '';
		public $print_invoice_id = '';

		function __construct() {

			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'register_bulk_action_send_steadfast' ), 999 );
			add_action( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'send_to_steadfast_bulk_process' ), 20, 3 );
			add_filter( 'woocommerce_shop_order_list_table_columns', array( $this, 'add_steadfast_custom_column' ) );
			add_action( 'woocommerce_shop_order_list_table_custom_column', array( $this, 'add_steadfast_custom_column_content' ), 10, 2 );
			add_action( 'wp_ajax_api_ajax_call', array( $this, 'api_ajax_call' ) );
			add_action( 'wp_ajax_input_amount', array( $this, 'steadfast_custom_amount_pay' ) );
			add_action( 'wp_ajax_print_invoice_id', array( $this, 'steadfast_invoice_print' ) );
			add_filter( 'woocommerce_shop_order_list_table_order_css_classes', array( $this, '_admin_orders_table_row_unlink' ) );
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 4 );

		}


		/**
		 * @return void
		 */
		function steadfast_invoice_print() {


			$print_id = isset( $_POST['invoice_id'] ) ? $_POST['invoice_id'] : '';

			$this->print_invoice_id = $print_id;

			if ( empty( $print_id ) ) {
				$message = array(
					'message' => esc_html__( 'success', 'steadfast' ),
				);
			} else {
				$message = array(
					'message' => esc_html__( 'failed', 'steadfast' ),
				);
			}
			wp_send_json_success( $message, 200 );

		}

		function add_plugin_action_links( $links, $file, $plugin_data, $context ) {

			if ( 'dropins' === $context ) {
				return $links;
			}

			$what      = ( 'mustuse' === $context ) ? 'muplugin' : 'plugin';
			$new_links = array();

			foreach ( $links as $link_id => $link ) {

				if ( 'deactivate' == $link_id && API_PLUGIN_FILE == $file ) {
					$new_links['steadfast-settings'] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wc-settings&tab=api_settings_tab' ), esc_html__( 'Settings', 'steadfast' ) );
				}

				$new_links[ $link_id ] = $link;
			}

			return $new_links;
		}

		/**
         * Admin Order List Table Row Unlink
         * 
		 * @param $classes
		 *
		 * @return mixed
		 */
		function _admin_orders_table_row_unlink( $classes ) {
			$classes[] = 'no-link';
			return $classes;
		}

		/**
		 * Get payment option value using ajax.
		 *
		 * @return void
		 */
		function steadfast_custom_amount_pay() {

			$input_value = isset( $_POST['input_value'] ) ? $_POST['input_value'] : '';
			$input_id    = isset( $_POST['input_id'] ) ? $_POST['input_id'] : '';
			$update      = update_post_meta( $input_id, 'steadfast_amount', $input_value );


			if ( $update === true ) {
				$message = array(
					'message' => esc_html__( 'success', 'steadfast' ),
				);
			}
			wp_send_json_success( $message, 200 );
		}

		/**
		 * Get send button value using ajax.
		 *
		 * @return void
		 */
		function api_ajax_call() {

			$order_id = isset( $_POST['order_id'] ) ? $_POST['order_id'] : '';
			$send     = $this->call_steadfast_api( $order_id );
			if ( $send == 'success' ) {
				$message = array(
					'message' => esc_html__( 'success', 'steadfast' ),
				);

				update_post_meta( $order_id, 'steadfast_is_sent', 'yes' );

			} else {
				$message = array(
					'message' => esc_html__( 'failed', 'steadfast' ),
				);
			}

			wp_send_json_success( $message, 200 );
		}

		/**
		 * Send bulks data to SteadFast.
		 *
		 * @param $bulk_actions
		 *
		 * @return void
		 */
		function register_bulk_action_send_steadfast( $bulk_actions ) {

			$checkbox = get_option( 'api_settings_tab_checkbox', false );

			if ( $checkbox == 'yes' ) {

				$bulk_actions['send_to_steadFast_bulk'] = esc_html__( 'Send to SteadFast', 'steadfast' );

				return $bulk_actions;
			}
		}

		/**
		 * Create custom column order dashboard.
		 *
		 * @param $columns
		 *
		 * @return array
		 */
		function add_steadfast_custom_column( $columns ) {
			$new_columns = array();
			$checkbox    = get_option( 'api_settings_tab_checkbox', false );

			foreach ( $columns as $column_name => $column_info ) {
				$new_columns[ $column_name ] = $column_info;


				if ( 'order_status' === $column_name ) {
					if ( $checkbox == 'yes' ) {
						$new_columns['amount'] = esc_html__( 'Amount', 'steadfast' );
					}
				}

				if ( 'order_status' === $column_name ) {
					if ( $checkbox == 'yes' ) {
						$new_columns['send_steadFast'] = esc_html__( 'Send to SteadFast', 'steadfast' );
					}
				}

				if ( 'order_status' === $column_name ) {
					if ( $checkbox == 'yes' ) {
						$new_columns['print_details'] = esc_html__( 'Print Details', 'steadfast' );
					}
				}

				if ( 'order_status' === $column_name ) {
					if ( $checkbox == 'yes' ) {
						$new_columns['consignment_id'] = esc_html__( 'Consignment ID', 'steadfast' );
					}
				}
			}

			return $new_columns;
		}


		/**
		 * @param $column
		 * @param $order
		 *
		 * @return void
		 */
		function add_steadfast_custom_column_content( $column, $order ) {

			$order_id = $order->get_id();

			$meta_value = get_post_meta( $order_id, 'steadfast_is_sent', true );

			$classes = 'yes' == $meta_value ? 'steadfast-send-success' : 'steadfast_send';

			$checkbox = get_option( 'api_settings_tab_checkbox', false );

			if ( $checkbox == 'yes' ) {
				if ( 'send_steadFast' === $column ) {
					?>
                    <button class="<?php echo esc_attr__( $classes ) ?>" data-order-id="<?php echo $order_id; ?>" name="steadfast"><?php echo esc_html__( 'Send', 'steadfast' ); ?></button>
					<?php
				}


				$consignment_id = get_post_meta( $order_id, 'steadfast_consignment_id', true );
				if ( ! empty( $consignment_id ) ) {
					if ( 'consignment_id' === $column ) {
						printf( '<div class="steadfast-consignment-id">%s</div>', $consignment_id );
					}

					if ( 'print_details' === $column ) {
						printf( '<div class="print-order-detail" data-print-id="' . $order_id . '">%s</div>', esc_html__( 'Print', 'steadfast' ) );
					}
				}

				$amnt_class  = $meta_value == 'yes' ? 'amount-disable' : '';
				$input_value = get_post_meta( $order_id, 'steadfast_amount', true );
				if ( 'amount' === $column ) { ?>
                    <input type="text" id="steadfast-amount" name="steadfast-amount" class="<?php echo $amnt_class; ?>" value="<?php echo $input_value; ?>" data-order-id="<?php echo $order_id; ?>" style="width: 80px">
				<?php }
			}

		}

		/**
		 * @param $redirect
		 * @param $doaction
		 * @param $object_ids
		 *
		 * @return mixed|string
		 */
		function send_to_steadfast_bulk_process( $redirect, $doaction, $object_ids ) {

			if ( 'send_to_steadFast_bulk' === $doaction ) {
				foreach ( $object_ids as $order_id ) {
					$sent = $this->call_steadfast_api( $order_id );
					if ( $sent == 'success' ) {
						$this->success = esc_html__( 'success', 'steadfast' );

						update_post_meta( $order_id, 'steadfast_is_sent', 'yes' );

					} else {
						$this->success = esc_html__( 'failed', 'steadfast' );
					}
				}

				$redirect = add_query_arg(
					array(
						'bulk_action' => 'send_to_steadFast_bulk',
						'changed'     => count( $object_ids ),
					),
					$redirect
				);
			}

			return $redirect;
		}


		/**
		 * Send Data To SteadFast Api.
		 *
		 * @param $order_id
		 *
		 * @return string
		 */
		function call_steadfast_api( $order_id ): string {

			$checkbox       = get_option( 'api_settings_tab_checkbox', false );
			$api_secret_key = get_option( 'api_settings_tab_api_secret_key', false );
			$api_key        = get_option( 'api_settings_tab_api_key', false );
			$api_notes      = get_option( 'api_settings_tab_notes', false );

			$order      = new WC_Order( $order_id );
			$order_data = $order->get_data();

			$input_amount = get_post_meta( $order_id, 'steadfast_amount', true );
			$input_amount = ! empty( $input_amount ) || $input_amount == 0 ? $input_amount : $order_data['total'];


			$fast_name               = $order_data['billing']['first_name'];
			$last_name               = $order_data['billing']['last_name'];
			$order_billing_address   = $order_data['billing']['address_1'];
			$order_billing_phone     = $order_data['billing']['phone'];
			$order_shipping_city     = $order_data['billing']['city'];
			$order_shipping_postcode = $order_data['billing']['postcode'];
			$order_note              = $order->get_customer_note();

			$order_note = $api_notes == 'yes' ? $order->get_customer_note() : '';

			//Check Customer Valid Phone Number.
			$n              = 10;
			$number         = strlen( $order_billing_phone ) - $n;
			$phone          = substr( $order_billing_phone, $number );
			$customer_phone = '0' . $phone;

			$recipient_address = $order_billing_address . ',' . $order_shipping_city . '-' . $order_shipping_postcode;
			$body              = array(
				"invoice"           => date( "ymj" ) . '-' . $order_id,
				"recipient_name"    => $fast_name . ' ' . $last_name,
				"recipient_phone"   => $customer_phone,
				"recipient_address" => $recipient_address,
				"cod_amount"        => $input_amount,
				"note"              => $order_note,
			);

			$args = array(
				'method'      => 'POST',
				'headers'     => array(
					'content-type' => 'application/json',
					'api-key'      => sanitize_text_field( $api_key ),
					'secret-key'   => sanitize_text_field( $api_secret_key ),
				),
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'body'        => json_encode( $body ),
				'cookies'     => array()
			);
			if ( $checkbox == 'yes' ) {
				$response = wp_remote_post( 'https://portal.steadfast.com.bd/api/v1/create_order', $args );

				$request = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			$consignment_id = $request['consignment']['consignment_id'];

			if ( $request['status'] == 200 ) {
				update_post_meta( $order_id, 'steadfast_consignment_id', $consignment_id );

				return esc_html__( 'success', 'steadfast' );
			} else {
				return esc_html__( 'failed', 'steadfast' );
			}


		}

		/**
		 * @return self|null
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

}

SteadFast_Hooks::instance();
