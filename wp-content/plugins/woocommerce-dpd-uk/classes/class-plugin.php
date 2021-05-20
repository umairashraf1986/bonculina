<?php

class WPDesk_WooCommerce_DPD_UK_Plugin extends \DpdUKVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin {

	private $script_version = '4';

	public $dpd_uk = null;

	public $flexible_printing_integration = false;

	/**
	 * @param \DpdUKVendor\WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( \DpdUKVendor\WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
		parent::__construct( $this->plugin_info );
		$this->settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping&section=dpd_uk' );
	}

	public function hooks() {
		parent::hooks();

		add_filter( 'flexible_printing_integrations', array( $this, 'flexible_printing_integrations' ) );

		add_action('plugins_loaded', function() {
			if ( class_exists( 'WPDesk_Flexible_Shipping_Shipment' ) ) {
				WPDesk_Flexible_Shipping_Shipment_dpd_uk::set_plugin( $this );

				$this->dpd_uk = WPDesk_WooCommerce_DPD_UK::get_instance( $this );
				new WPDesk_WooCommerce_DPD_UK_FS_Hooks( $this );
			}
		});
	}

	/**
	 * Renders end returns selected template
	 *
	 * @param string $name Name of the template.
	 * @param string $path Additional inner path to the template.
	 * @param array  $args args Accessible from template.
	 *
	 * @return string
	 */
	public function load_template($name, $path = '', $args = array())
	{
		$resolver = new \DpdUKVendor\WPDesk\View\Resolver\ChainResolver();
		$resolver->appendResolver( new \DpdUKVendor\WPDesk\View\Resolver\WPThemeResolver( basename( $this->plugin_info->get_plugin_dir() ) ) );
		$resolver->appendResolver( new \DpdUKVendor\WPDesk\View\Resolver\DirResolver( trailingslashit( $this->plugin_info->get_plugin_dir() ) . 'templates' ) );
		$renderer = new DpdUKVendor\WPDesk\View\Renderer\SimplePhpRenderer( $resolver );

		return $renderer->render( trailingslashit( $path ) . $name, $args );
	}

	public function flexible_printing_integrations( array $integrations ) {
		$this->flexible_printing_integration                      = new WPDesk_WooCommerce_DPD_UK_Flexible_Printing_Integration( $this );
		$integrations[ $this->flexible_printing_integration->id ] = $this->flexible_printing_integration;

		return $integrations;
	}

	public function admin_enqueue_scripts( ) {
		$current_screen = get_current_screen();
		$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( in_array( $current_screen->id, array( 'shop_order', 'edit-shop_order' ) ) ) {
			wp_register_style( 'dpd_uk_admin_css', $this->get_plugin_assets_url() . 'css/admin' . $suffix . '.css', array(), $this->script_version );
			wp_enqueue_style( 'dpd_uk_admin_css' );

			wp_enqueue_script( 'dpd_uk_admin_order_js', $this->get_plugin_assets_url() . 'js/admin-order' . $suffix . '.js', array( 'jquery' ), $this->script_version, true );

			wp_localize_script( 'dpd_uk_admin_order_js', 'dpd_uk_ajax_object', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			) );
		}
		if ( in_array( $current_screen->id, array( 'woocommerce_page_wc-settings' ) ) && isset( $_GET['section'] ) && $_GET['section'] === 'dpd_uk' ) {
			wp_register_style( 'dpd_uk_admin_css', $this->get_plugin_assets_url() . 'css/admin' . $suffix . '.css', array(), $this->script_version );
			wp_enqueue_style( 'dpd_uk_admin_css' );
		}
	}

	/**
	 * action_links function.
	 *
	 * @access public
	 *
	 * @param mixed $links
	 *
	 * @return array
	 */
	public function links_filter( $links ) {

		$plugin_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=dpd_uk' ) . '">' . __( 'Settings', 'woocommerce-dpd-uk' ) . '</a>',
			'docs'     => '<a target="_blank" href="https://docs.flexibleshipping.com/collection/1-woocommerce-dpd-uk">' . __( 'Docs', 'woocommerce-dpd-uk' ) . '</a>',
			'support'  => '<a target="_blank" href="https://flexibleshipping.com/support/">' . __( 'Support', 'woocommerce-dpd-uk' ) . '</a>',
		);

		if ( defined( 'WC_VERSION' ) ) {
			if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
				$plugin_links['settings'] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=wpdesk_woocommerce_dpd_uk_shipping_method' ) . '">' . __( 'Settings', 'woocommerce-dpd-uk' ) . '</a>';
			}
		} else {
			unset( $plugin_links['settings'] );
		}

		return array_merge( $plugin_links, $links );
	}

	/**
	 * @return WPDesk_WooCommerce_DPD_UK_Shipping_Method
	 */
	public function get_dpd_uk_shipping_method() {
		$all_shipping_methods = WC()->shipping()->get_shipping_methods();
		if ( empty( $all_shipping_methods ) ) {
			$all_shipping_methods = WC()->shipping()->load_shipping_methods();
		}
		$dpd_uk_shipping_method = $all_shipping_methods['dpd_uk'];

		return $dpd_uk_shipping_method;
	}

}
