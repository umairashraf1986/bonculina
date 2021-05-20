<?php

namespace WcPay360\Admin;

use WcPay360\Helpers\Factories;

/**
 * Plugin System Status
 *
 * @since      3.5.0
 */
class System_Status {
	
	public $gateway;
	public $gateway_id;
	public $plugin_license_id;
	
	public function __construct( $gateway_id, $plugin_license_id = 0 ) {
		$this->gateway_id        = $gateway_id;
		$this->plugin_license_id = $plugin_license_id;
	}
	
	/**
	 * Attach callbacks
	 *
	 * @since 3.5.0
	 */
	public function hooks() {
		add_filter( 'woocommerce_system_status_report', array( $this, 'render_system_status_items' ), 100 );
	}
	
	public function get_gateway() {
		if ( null == $this->gateway ) {
			$this->gateway = Factories::get_gateway( $this->gateway_id );
		}
		
		return $this->gateway;
	}
	
	/**
	 * Renders the Subscription information in the WC status page
	 *
	 * @since 3.5.0
	 */
	public function render_system_status_items() {
		
		$status_data['wc_pay360_integration'] = array(
			'name'      => _x( 'Integration type', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
			'label'     => 'Integration type',
			'note'      => \WC_Pay360::get_field( $this->get_gateway()->integration, $this->get_gateway()->get_integration_options(), 'not set' ),
			'mark'      => 'yes',
			'mark_icon' => '',
		);
		
		if ( 'hosted_cashier' == $this->get_gateway()->integration ) {
			$status_data['wc_pay360_3d_enabled'] = array(
				'name'      => _x( '3D Secure', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
				'label'     => '3D Secure',
				'note'      => 'yes' == $this->get_gateway()->enable_3d_secure ? __( 'Yes', \WC_Pay360::TEXT_DOMAIN ) : __( 'No', \WC_Pay360::TEXT_DOMAIN ),
				'mark'      => 'yes' == $this->get_gateway()->enable_3d_secure ? 'yes' : 'error',
				'mark_icon' => '',
			);
			
			$status_data['wc_pay360_transaction_type'] = array(
				'name'      => _x( 'Transaction Type', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
				'label'     => 'Transaction Type',
				'note'      => $this->get_gateway()->get_option( 'cashier_transaction_type' ),
				'mark'      => '',
				'mark_icon' => '',
			);
			
			$skin = $this->get_gateway()->get_option( 'hosted_cashier_skin_default' );
			if ( 'other' == $skin ) {
				$skin = $this->get_gateway()->get_option( 'hosted_cashier_skin' );
			}
			
			$status_data['wc_pay360_skin'] = array(
				'name'      => _x( 'Skin ID', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
				'label'     => 'Skin ID',
				'note'      => $skin,
				'mark'      => '',
				'mark_icon' => '',
			);
			
			$status_data['wc_pay360_use_iframe'] = array(
				'name'      => _x( 'Using iFrame', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
				'label'     => 'Using iFrame',
				'note'      => 'yes' == $this->get_gateway()->get_option( 'hosted_cashier_use_iframe' ) ? __( 'Yes', \WC_Pay360::TEXT_DOMAIN ) : __( 'No', \WC_Pay360::TEXT_DOMAIN ),
				'mark'      => 'yes' == $this->get_gateway()->get_option( 'hosted_cashier_use_iframe' ) ? 'yes' : 'error',
				'mark_icon' => '',
			);
		} elseif ( 'hosted' == $this->get_gateway()->integration ) {
			$status_data['wc_pay360_transaction_type'] = array(
				'name'      => _x( 'Transaction Type', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
				'label'     => 'Transaction Type',
				'note'      => $this->get_gateway()->transaction_type_h,
				'mark'      => '',
				'mark_icon' => '',
			);
			
			$status_data['wc_pay360_send_confirm_email'] = array(
				'name'      => _x( 'Sending Confirmation Emails', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
				'label'     => 'Sending Confirmation Emails',
				'note'      => 'yes' == $this->get_gateway()->mail_customer ? __( 'Yes', \WC_Pay360::TEXT_DOMAIN ) : __( 'No', \WC_Pay360::TEXT_DOMAIN ),
				'mark'      => 'yes' == $this->get_gateway()->mail_customer ? 'yes' : 'error',
				'mark_icon' => '',
			);
			
			$status_data['wc_pay360_3d_enabled'] = array(
				'name'      => _x( '3D Secure', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
				'label'     => '3D Secure',
				'note'      => 'yes' == $this->get_gateway()->enable_3d_secure ? __( 'Yes', \WC_Pay360::TEXT_DOMAIN ) : __( 'No', \WC_Pay360::TEXT_DOMAIN ),
				'mark'      => 'yes' == $this->get_gateway()->enable_3d_secure ? 'yes' : 'error',
				'mark_icon' => '',
			);
		}
		
		$testmode = 'yes' == $this->get_gateway()->testmode;
		
		$status_data['wc_pay360_testmode'] = array(
			'name'      => _x( 'Gateway Mode', 'Label on WooCommerce -> System Status page', \WC_Pay360::TEXT_DOMAIN ),
			'label'     => 'Gateway Mode',
			'note'      => $testmode ? __( 'Test', \WC_Pay360::TEXT_DOMAIN ) : __( 'Live', \WC_Pay360::TEXT_DOMAIN ),
			'success'   => $testmode ? 0 : 1,
			'mark'      => $testmode ? 'error' : 'yes',
			'mark_icon' => '',
		);
		
		$system_status_sections = array(
			array(
				'title'   => __( 'Pay360 Gateway', \WC_Pay360::TEXT_DOMAIN ),
				'tooltip' => __( 'System information about WooCommerce Pay360 plugin.', \WC_Pay360::TEXT_DOMAIN ),
				'data'    => apply_filters( 'wc_pay360_system_status', $status_data ),
			),
		);
		
		foreach ( $system_status_sections as $section ) {
			$section_title   = $section['title'];
			$section_tooltip = $section['tooltip'];
			$debug_data      = $section['data'];
			
			include( \WC_Pay360::plugin_path() . '/includes/admin/views/system-status.php' );
		}
	}
	
	/**
	 * Return the currency codes that we have set merchant accounts for
	 *
	 * @param $accounts
	 *
	 * @return array
	 */
	public function get_accounts_for_currencies( $accounts ) {
		$currencies = array();
		foreach ( $accounts as $data ) {
			if ( '' == $data['account_id'] ) {
				continue;
			}
			$currencies[] = $data['account_currency'];
		}
		
		return $currencies;
	}
}
