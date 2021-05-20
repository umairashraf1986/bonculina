<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_WooCommerce_DPD_UK_Flexible_Printing_Integration' ) ) {
	class WPDesk_WooCommerce_DPD_UK_Flexible_Printing_Integration extends Flexible_Printing_Integration {

		private $plugin = null;

		public function __construct( WPDesk_WooCommerce_DPD_UK_Plugin $plugin ) {
			$this->plugin = $plugin;
			$this->id = 'dpd_uk';
			$this->title = 'DPD UK';

			add_action( 'flexible_shipping_shipping_actions_html', array( $this, 'flexible_shipping_shipping_actions_html' ) );
            add_action( 'flexible_shipping_shipment_status_updated', array( $this, 'flexible_shipping_shipment_status_updated' ), 10, 3 );
		}

        /**
         * @param $old_status string
         * @param $new_status string
         * @param $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface
         */
		public function flexible_shipping_shipment_status_updated( $old_status, $new_status, $shipment ) {
			if ( $new_status != $old_status && $new_status == 'fs-confirmed' && $shipment->get_integration() == 'dpd_uk' ) {
				$all_shipping_methods = WC()->shipping()->get_shipping_methods();
				$shipping_method = $all_shipping_methods['dpd_uk'];
				if ( $shipping_method->get_option( 'auto_print', 'no' ) == 'yes'
				) {
					$label_data = $shipment->get_label();
					try {
						$content_type = 'application/pdf';
						if ( $label_data['label_format'] == 'zpl' ) {
							$content_type = 'application/zpl';
						}
						else if ( $label_data['label_format'] == 'epl' ) {
							$content_type = 'application/epl';
						}
						$this->do_print(
							$label_data['file_name'],
							$label_data['content'],
							$content_type,
							false
						);
					}
					catch ( Exception $e ) {
						error_log( sprintf( __( 'Printing error: %s', 'woocommerce-dpd-uk' ), $e->getMessage() ) );
					}
				}
			}
		}


		public function options() {
			$options = array();
/*
			$options[] = array(
				'id' => 'auto_print',
				'name' => __('Automatyczne drukowanie', 'woocommerce-dpd'),
				'desc' => __('Włącz (po utworzeniu etykieta zostanie automatycznie wydrukowana)', 'woocommerce-dpd'),
				'type' => 'checkbox',
				'std' => '',
			);
*/
			return $options;
		}

		public function do_print_action( $args ) {
            if ( !empty( $args['data']['shippment_id'] ) ) {
                $shipment = fs_get_shipment( $args['data']['shippment_id'] );
                /* @var $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface */
                $label_data = $shipment->get_label();
                $args = array(
                    'title' => $label_data['file_name'],
                    'content' => $label_data['content'],
                    'content_type' => 'application/' . $label_data['label_format'],
                    'silent' => false
                );
            }
            else {
                $shipment_id = $args['data']['shipment_id'];
                $label_data = $this->plugin->dpd_uk->get_label( $shipment_id );
                $content_type = 'application/pdf';
                if ($label_data->getLabelsResult->item->labelType == 'ZBLP') {
                    $content_type = 'application/zpl';
                }
                $args = array(
                    'title' => 'dpd_uk_' . $label_data->getLabelsResult->item->labelType . '_' . $label_data->getLabelsResult->item->labelName,
                    'content' => base64_decode($label_data->getLabelsResult->item->labelData),
                    'content_type' => $content_type,
                    'silent' => false
                );
            }
			do_action( 'flexible_printing_print', 'dpd_uk', $args );
		}

		public function flexible_shipping_shipping_actions_html( $shipping ) {
            if ( !empty( $shipping['shipment'] ) ) {
                $shipment = $shipping['shipment'];
                /* @var $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface */
                if ( $shipment->get_meta( '_integration', '' ) == 'dpd_uk' ) {
                    if ( $shipment->get_label_url() != null ) {
                        echo apply_filters( 'flexible_printing_print_button', '', 'dpd_uk',
                            array(
                                'content' => 'print',
                                'icon'    => true,
                                'id'      => str_replace( ', ', '-', $shipment->get_meta('_dpd_uk_parcel_number' ) ),
                                'tip'   => __( 'Print on: %s', 'woocommerce-dpd-uk' ),
                                'data'    => array(
                                    'shippment_id'         => $shipment->get_id(),
                                    'dpd_uk_parcel_number' => $shipment->get_meta('_dpd_uk_parcel_number' ),
                                ),
                            )
                        );
                    }
                }
            }
		}

	}
}