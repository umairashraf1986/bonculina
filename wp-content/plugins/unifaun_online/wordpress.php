<?php
/**
 * General Wordpress functionality
 *
 * @author Christian Johansson <christian@mediastrategi.se>
 * @requires: Wordpress:3.9.0, Woocommerce:3.0.0
 */

/**
 *
 */
class Mediastrategi_UnifaunOnline_Wordpress
{
    /**
     *
     */
    public function __construct()
    {
        add_action(
            'init',
            array(
                $this,
                'init'
            )
        );
        /** @since Wordpress 3.0.0 */
        add_action(
            'add_meta_boxes',
            array(
                $this,
                'addMetaBoxes'
            )
        );
        /** @since Wordpress 3.7.0 */
        add_action(
            'save_post',
            array(
                $this,
                'savePost'
            )
        );
        /** @since Wordpress 2.8.0 */
        add_action(
            'admin_enqueue_scripts',
            array(
                $this,
                'adminEnqueueScripts'
            )
        );
        /** @since Wordpress 3.1.0 */
        add_action(
            'admin_notices',
            array(
                $this,
                'adminNotices'
            )
        );

        // Add columns to order-view
        /** @since Woocommerce 3.0.0 */
        add_filter(
            'manage_shop_order_posts_columns',
            array(
                $this,
                'manage_shop_order_posts_columns'
            ),
            100,
            2
        );
        /** @since Wordpress 1.5.0 */
        add_filter(
            'manage_posts_custom_column',
            array(
                $this,
                'manage_posts_custom_column'
            ),
            100,
            2
        );
        /** @since Wordpress 2.8.0 */
        add_action(
            'admin_footer-edit.php',
            array(
                $this,
                'custom_admin_footer',
            )
        );
        $version = get_bloginfo('version');
        if (version_compare($version, '4.7.0', '>=')) {
            /** @see \WC_Admin_Post_Types->__construct() */
            /** @since Wordpress 4.7.0 */
            add_filter(
                'bulk_actions-edit-shop_order',
                array(
                    $this,
                    'bulk_actions_shop_order'
                )
            );
        }
        /* @since Wordpress 2.1.0 */
        add_action(
            'load-edit.php',
            array(
                $this,
                'load_edit',
            )
        );

        add_action(
            'admin_menu',
            array(
                $this,
                'admin_menu'
            )
        );

        // Return meta lists partners
        if (is_admin()
            && !empty($_POST)
            && !empty($_POST['msunifaun_online_meta_partners'])
        ) {
            die(json_encode(\Mediastrategi_UnifaunOnline::getPartners()));
        }
    }

    /**
     *
     */
    public function admin_menu()
    {
        add_menu_page(
            __(
                'Unifaun Setup',
                'msunifaunonline'
            ),
            __(
                'Unifaun Setup',
                'msunifaunonline'
            ),
            'manage_woocommerce',
            'msunifaun_setup',
            array(
                $this,
                'setup'
            )
        );
        remove_menu_page('msunifaun_setup');
    }

    /**
     *
     */
    public function setup()
    {
        require_once(sprintf(
            '%s/setup/index.php',
            __DIR__
        ));
        $setup = new Mediastrategi_UnifaunOnline_Setup();
        $setup->execute();
    }

    /**
     *
     */
    public function init()
    {
        load_plugin_textdomain(
            'msunifaunonline',
            false,
            basename(dirname(__FILE__)) . '/languages'
        );
    }

    /**
     * @param array $bulkActions
     * @return array
     * @see \WC_Admin_Post_Types->shop_order_bulk_actions()
     */
    public function bulk_actions_shop_order($actions)
    {
        $actions['msunifaunonline_bulk_labels'] = __(
            "Download shipping labels",
            'msunifaunonline'
        );
        $actions['msunifaunonline_bulk_process'] = __(
            "Process shipping",
            'msunifaunonline'
        );
        return $actions;
    }

    /**
     * Handle bulk actions
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function load_edit()
    {
        /** @since Wordpress 3.1.0 */
        if ($wp_list_table = _get_list_table('WP_Posts_List_Table')) {
            $action = $wp_list_table->current_action();
            /** @since Wordpress 1.2.0 */
            if (!empty($action)
                && ($action === 'msunifaunonline_bulk_labels'
                    || $action === 'msunifaunonline_bulk_process')
                && check_admin_referer('bulk-posts')
            ) {
                $postIds = (isset($_GET['post'])
                    ? array_map('intval', $_GET['post'])
                    : array());
                if (count($postIds)) {
                    /** @since Wordpress 2.0.0 */
                    if (current_user_can('edit_posts')) {
                        if ($action === 'msunifaunonline_bulk_labels') {
                            // Generate array of consignments and filename path for composite PDF
                            $wp_upload_dir = wp_upload_dir();
                            $mergedFilename = $wp_upload_dir['basedir'] . $wp_upload_dir['subdir'];
                            $mergedUrl = $wp_upload_dir['baseurl'] . $wp_upload_dir['subdir'];
                            $shipmentNumbers = array();
                            $pdfs = array();
                            foreach ($postIds as $postId)
                            {
                                if ($order = WC_Order_Factory::get_order($postId)) {
                                    if ($orderShipping = $order->get_items('shipping')) {
                                        $packageId = 0;
                                        foreach ($orderShipping as $shippingMethod)
                                        {
                                            $isUsingUnifaun = strpos(
                                                $shippingMethod->get_method_id(),
                                                \Mediastrategi_UnifaunOnline::METHOD_ID) === 0;
                                            if ($isUsingUnifaun) {

                                                // Shipments
                                                if ($shipments = Mediastrategi_UnifaunOnline_Order::getShipments(
                                                    $postId,
                                                    $packageId
                                                )) {
                                                    if (!\Mediastrategi_UnifaunOnline::isNumericArray($shipments)) {
                                                        $shipments = array($shipments);
                                                    }
                                                    // NOTE: New syntax below from 1.2.17 and above
                                                    foreach ($shipments as $shipment)
                                                    {
                                                        if (!empty($shipment['nr'])) {
                                                            $shipmentNumbers[] = $shipment['nr'];
                                                        }
                                                        if (!empty($shipment['lbl'])
                                                            && is_array($shipment['lbl'])
                                                        ) {
                                                            foreach ($shipment['lbl'] as $printFile) {
                                                                $pdfs[] = $wp_upload_dir['basedir'] . $printFile;
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    if ($shipmentNumber = Mediastrategi_UnifaunOnline_Order::getShipments(
                                                        $postId,
                                                        $packageId
                                                    )) {
                                                        $shipmentNumbers[] = $shipmentNumber;
                                                    }
                                                    if ($pdf = Mediastrategi_UnifaunOnline_Order::getShipmentPrintFile(
                                                        $postId,
                                                        $packageId
                                                    )) {
                                                        $pdfs[] = $wp_upload_dir['basedir'] . $pdf;
                                                    }
                                                }
                                            }
                                            $packageId++;
                                        }
                                    }
                                }
                            }
                            $mergedFilename .= Mediastrategi_UnifaunOnline::getMultipleLabelsPath($shipmentNumbers) . '.txt';
                            $mergedUrl .= Mediastrategi_UnifaunOnline::getMultipleLabelsPath($shipmentNumbers) . '.txt';

                            if (file_exists($mergedFilename)) {
                                Mediastrategi_UnifaunOnline_Session::setDownloadUrl(
                                    0,
                                    $mergedUrl
                                );
                                Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                                    0,
                                    sprintf(
                                        __(
                                            'Downloading <a target="_blank" href="%s">labels</a> in new window..',
                                            'msunifaunonline'
                                        ),
                                        $mergedUrl
                                ));

                                // Redirect to get rid of argument
                                header('Location: ?post_type=shop_order');
                                exit;

                            } else if (count($pdfs) > 1) {

                                // Use PDF merger here
                                $pdfMergerPath = dirname(__FILE__) . '/includes/fpdi/src/autoload.php';
                                if (file_exists($pdfMergerPath)) {

                                    require_once($pdfMergerPath);
                                    require_once(dirname(__FILE__) . '/includes/fpdf/fpdf.php');

                                    $fpdi = new \setasign\Fpdi\Fpdi();
                                    foreach ($pdfs as $pdf)
                                    {
                                        $nrOfPagesInPdf = $fpdi->setSourceFile(
                                            \setasign\Fpdi\PdfParser\StreamReader::createByString(
                                                file_get_contents($pdf)
                                            )
                                        );
                                        $pagesToMerge = range(1, $nrOfPagesInPdf);
                                        foreach ($pagesToMerge as $pageNr)
                                        {
                                            $template = $fpdi->importPage($pageNr);
                                            $size = $fpdi->getTemplateSize($template);
                                            $fpdi->AddPage(
                                                $size['width'] > $size['height'] ? 'L' : 'P',
                                                [$size['width'], $size['height']]
                                            );
                                            $fpdi->useTemplate($template);
                                        }
                                    }
                                    file_put_contents(
                                        $mergedFilename,
                                        $fpdi->Output('', 'S')
                                    );

                                    Mediastrategi_UnifaunOnline_Session::setDownloadUrl(
                                        0,
                                        $mergedUrl
                                    );
                                    Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                                        0,
                                        sprintf(
                                            __(
                                                'Downloading <a target="_blank" href="%s">labels</a> in new window..',
                                                'msunifaunonline'
                                            ),
                                            $mergedUrl
                                    ));

                                    // Redirect to get rid of argument
                                    header('Location: ?post_type=shop_order');
                                    exit;

                                } else {
                                    Mediastrategi_UnifaunOnline_Session::setMessageError(
                                        0,
                                        sprintf(
                                            __(
                                                'Failed to find PDF merge library at %s.',
                                                'msunifaunonline'
                                            ),
                                            $pdfMergerPath
                                    ));

                                    // Redirect to get rid of argument
                                    header('Location: ?post_type=shop_order');
                                    exit;
                                }

                            } else if (count($shipmentNumbers) == 1) {

                                $printFile = '';
                                foreach ($postIds as $postId)
                                {
                                    if ($order = WC_Order_Factory::get_order($postId)) {
                                        if ($orderShipping = $order->get_items('shipping')) {
                                            $packageId = 0;
                                            foreach ($orderShipping as $shippingMethod)
                                            {
                                                $isUsingUnifaun = strpos(
                                                    $shippingMethod->get_method_id(),
                                                    \Mediastrategi_UnifaunOnline::METHOD_ID) === 0;
                                                if ($isUsingUnifaun) {
                                                    if ($printFile = Mediastrategi_UnifaunOnline_Order::getShipmentPrintFile(
                                                        $postId,
                                                        $packageId
                                                    )) {
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($printFile) {
                                        break;
                                    }
                                }

                                if (!empty($printFile)) {
                                    Mediastrategi_UnifaunOnline_Session::setDownloadUrl(
                                        0,
                                        $wp_upload_dir['baseurl'] . $printFile
                                    );
                                    Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                                        0,
                                        sprintf(
                                            __(
                                                'Downloading <a target="_blank" href="%s">label</a> in new window..',
                                                'msunifaunonline'
                                            ),
                                            $wp_upload_dir['baseurl'] . $printFile
                                    ));
                                } else {
                                    Mediastrategi_UnifaunOnline_Session::setMessageError(
                                        0,
                                        sprintf(
                                            __(
                                                'Failed to find shipping label for order %s.',
                                                'msunifaunonline'
                                            ),
                                            implode(',', $postIds)
                                    ));
                                    // Redirect to get rid of argument
                                    header('Location: ?post_type=shop_order');
                                    exit;
                                }

                            } else {
                                Mediastrategi_UnifaunOnline_Session::setMessageError(
                                    0,
                                    sprintf(
                                        __(
                                            'Failed to find shipping labels for orders %s.',
                                            'msunifaunonline'
                                        ),
                                        implode(',', $postIds)
                                ));
                                // Redirect to get rid of argument
                                header('Location: ?post_type=shop_order');
                                exit;
                            }

                        } else if ($action === 'msunifaunonline_bulk_process') {

                            $count = 0;
                            $class = false;
                            foreach ($postIds as $postId)
                            {
                                \Mediastrategi_UnifaunOnline::getWooCommerce()->processOrder(
                                    $postId,
                                    true,
                                    false
                                );
                                $count++;
                            }

                            if ($count) {
                                Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                                    0,
                                    sprintf(
                                        __(
                                            'Bulk process completed, %d items processed.',
                                            'msunifaunonline'
                                        ),
                                        $count
                                ));
                            } else {
                                Mediastrategi_UnifaunOnline_Session::setMessageError(
                                    0,
                                    __(
                                        'Bulk process completed, no items processed.',
                                        'msunifaunonline'
                                ));
                            }

                            // Redirect to get rid of argument
                            header('Location: ?post_type=shop_order');
                            exit;
                        }
                    }
                }
            }
        }
    }

    /**
     *
     */
    public function custom_admin_footer()
    {
        /** @var string $post_type */
        global $post_type;
        if (!empty($post_type)
            && $post_type === 'shop_order'
        ) {
            if (Mediastrategi_UnifaunOnline_Session::getDownloadUrl()) {
                foreach (Mediastrategi_UnifaunOnline_Session::getDownloadUrl()
                    as $package => $url
                ) {
                    echo '<script type="text/javascript">'
                        . 'jQuery(document).ready(function() { '
                        . 'window.open("'
                        . $url . '", "_blank");'
                        . ' });</script>';
                    Mediastrategi_UnifaunOnline_Session::setDownloadUrl(
                        $package,
                        ''
                    );
                }
            }
            $version = get_bloginfo('version');
            if (version_compare($version, '4.7.0', '<')) {
                // NOTE: Bulk actions if Wordpress version if below 4.7.0
                echo '<script type="text/javascript">'
                    . 'jQuery(document).ready(function() {'
                    . 'jQuery("<option>").val("msunifaunonline_bulk_labels").text("'
                    . __("Download Shipping Labels", 'msunifaunonline')
                    . '").appendTo("select[name=\'action\']");'
                    . 'jQuery("<option>").val("msunifaunonline_bulk_labels").text("'
                    . __("Download Shipping Labels", 'msunifaunonline')
                    . '").appendTo("select[name=\'action2\']");'
                    . 'jQuery("<option>").val("msunifaunonline_bulk_process").text("'
                    . __("Process Shipping", 'msunifaunonline')
                    . '").appendTo("select[name=\'action\']");'
                    . 'jQuery("<option>").val("msunifaunonline_bulk_process").text("'
                    . __("Process Shipping", 'msunifaunonline')
                    . '").appendTo("select[name=\'action2\']");'
                    . '""});</script>';
            }
        }
    }

    /**
     *
     */
    public function adminNotices()
    {
        if (Mediastrategi_UnifaunOnline_Session::getMessageSuccess()) {
            foreach (Mediastrategi_UnifaunOnline_Session::getMessageSuccess()
                as $package => $message
            ) {
                vprintf(
                    '<div class="notice notice-success"><p>%s</p></div>',
                    $message
                );
                Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                    $package,
                    ''
                );
            }
        }
        if (Mediastrategi_UnifaunOnline_Session::getMessageError()) {
            foreach (Mediastrategi_UnifaunOnline_Session::getMessageError()
                as $package => $message
            ) {
                vprintf(
                    '<div class="notice notice-error"><p>%s</p></div>',
                    $message
                );
                Mediastrategi_UnifaunOnline_Session::setMessageError(
                    $package,
                    ''
                );
            }
        }
    }

    /**
     * Adds styles and scripts to admin
     */
    public function adminEnqueueScripts()
    {
        /** @since Wordpress 2.1.0 */
        wp_register_script(
            'mediastrategi_unifaun_online_script',
            plugins_url(
                'assets/js/admin.js',
                __FILE__
            ),
            array('jquery'),
            '200604'
        );
        /** @since Wordpress 2.1.0 */
        wp_enqueue_script(
            'mediastrategi_unifaun_online_script'
        );
        /** @since Wordpress 2.6.0 */
        wp_enqueue_style(
            'mediastrategi_unifaun_online_style',
            plugins_url(
                'assets/css/admin.css',
                __FILE__
            ),
            array(),
            '200604'
        );

        // Setup
        /** @since Wordpress 2.1.0 */
        wp_register_script(
            'mediastrategi_unifaun_online_setup_script',
            plugins_url(
                'setup/assets/js/script.js',
                __FILE__
            ),
            array('jquery')
        );
        /** @since Wordpress 2.1.0 */
        wp_enqueue_script(
            'mediastrategi_unifaun_online_setup_script'
        );
        /** @since Wordpress 2.6.0 */
        wp_enqueue_style(
            'mediastrategi_unifaun_online_setup_style',
            plugins_url(
                'setup/assets/css/style.css',
                __FILE__
            ),
            array()
        );
    }

    /**
     * @param string $postType
     */
    public function addMetaBoxes($postType)
    {
        if (!empty($postType)
            && $postType === 'shop_order'
        ) {
            /** @since Wordpress 2.5.0 */
            add_meta_box(
                'msunifaunonline-shippinglabels',
                __(
                    Mediastrategi_UnifaunOnline::METHOD_TITLE,
                    'msunifaunonline'
                ),
                array(
                    $this,
                    'renderMetaBoxes'
                ),
                $postType,
                'side'
            );
        }
    }

    /**
     * @param int $postId
     * @since Wordpress 2.0.3
     */
    public function savePost($postId)
    {
        // Check if our nonce is set.
        if (!isset($_POST['msunifaunonline_process_order'])
            || !wp_verify_nonce(
                $_POST['msunifaunonline_process_order'],
                'msunifaunonline_process_order')
        ) {
            return $postId;
        }

        /**
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         *
         * @since Wordpress 3.9.0
         */
        if (defined('DOING_AUTOSAVE')
            && DOING_AUTOSAVE
        ) {
            return $postId;
        }

        /**
         * Check the user's permissions.
         *
         * @since Wordpress 2.0.0
         */
        if ($_POST['post_type'] == 'page') {
            if (!current_user_can('edit_page', $postId)) {
                return $postId;
            }
        } else {
            if (!current_user_can('edit_post', $postId)) {
                return $postId;
            }
        }

        // Support pressing the 'Process Order' button
        if (!empty($_POST['msunifaunonline_process_order_submit'])) {
            \Mediastrategi_UnifaunOnline::getWooCommerce()->processOrder(
                $postId,
                true,
                false,
                true
            );
        }

        // Support pressing 'Save Order' button
        if (!empty($_POST['msunifaunonline_update_order_submit'])
            && is_array($_POST['msunifaunonline_custom_packages'])
        ) {
            foreach (array_keys($_POST['msunifaunonline_custom_packages'])
                as $packageId
            ) {
                $this->processOrderParcelPackages(
                    $postId,
                    $packageId
                );
                $this->processOrderParcelMethods(
                    $postId,
                    $packageId
                );
            }
        }

        // Support pressing 'Process' button on a package
        if (!empty($_POST['msunifaunonline_process_package_submit'])
            && is_array($_POST['msunifaunonline_process_package_submit'])
        ) {
            foreach (array_keys($_POST['msunifaunonline_process_package_submit'])
                as $packageId
            ) {
                $this->processOrderParcelPackages(
                    $postId,
                    $packageId
                );
                \Mediastrategi_UnifaunOnline::getWooCommerce()->processOrderPackage(
                    $postId,
                    $packageId,
                    true,
                    true,
                    true
                );
            }
        }

        // Support pressing 'Save' button on a package
        if (!empty($_POST['msunifaunonline_update_package'])
            && is_array($_POST['msunifaunonline_update_package'])
        ) {
            foreach (array_keys($_POST['msunifaunonline_update_package'])
                as $packageId
            ) {
                $this->processOrderParcelPackages(
                    $postId,
                    $packageId
                );
                $this->processOrderParcelMethods(
                    $postId,
                    $packageId
                );
            }
        }
    }

    /**
     * @param int $postId
     * @param int $packageId
     */
    private function processOrderParcelMethods($postId, $packageId)
    {
        // Do we have a new order shipping method?
        if (!empty($_POST['msunifaunonline_update_shipping_method'][$packageId])
            && isset($_POST['msunifaunonline_update_shipping_method_old'][$packageId])
            && $_POST['msunifaunonline_update_shipping_method'][$packageId]
            != $_POST['msunifaunonline_update_shipping_method_old'][$packageId]
        ) {
            if ($order = WC_Order_Factory::get_order($postId)) {
                $foundShipping = false;
                if ($orderShipping = $order->get_items('shipping')) {
                    $orderPackageIndex = 0;
                    foreach ($orderShipping as $shippingMethod)
                    {
                        if ($orderPackageIndex == $packageId) {
                            $foundShipping = true;
                            $colonSplit = explode(
                                ':',
                                $_POST['msunifaunonline_update_shipping_method'][$packageId],
                                3
                            );
                            $newMethodId = isset($colonSplit[0]) ? trim($colonSplit[0]) : null;
                            $newInstanceId = isset($colonSplit[1]) ? trim($colonSplit[1]) : null;
                            $newName = isset($colonSplit[2]) ? trim($colonSplit[2]) : '';
                            if (isset($newMethodId)
                                && method_exists($shippingMethod, 'set_method_id')
                            ) {
                                $shippingMethod->set_method_id($newMethodId);
                            }
                            if (!empty($newName)) {
                                $shippingMethod->set_name($newName);
                                $shippingMethod->set_method_title($newName);
                            }
                            if (isset($newInstanceId)
                                && method_exists($shippingMethod, 'set_instance_id')
                            ) {
                                $shippingMethod->set_instance_id($newInstanceId);
                            }
                            if ($shippingMethod->save()) {
                                Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                                    $packageId,
                                    sprintf(
                                        __(
                                            'Succeeded in changing orders package %d shipping method.',
                                            'msunifaunonline'
                                        ),
                                        $packageId + 1
                                    )
                                );
                            } else {
                                Mediastrategi_UnifaunOnline_Session::setMessageError(
                                    $packageId,
                                    sprintf(
                                        __(
                                            'Failed with changing orders package %d shipping method.',
                                            'msunifaunonline'
                                        ),
                                        $packageId + 1
                                ));
                            }
                        }
                        $orderPackageIndex++;
                    }
                }

                if (!$foundShipping) {
                    // Order lacks shipping - create new shipping
                    $colonSplit = explode(
                        ':',
                        $_POST['msunifaunonline_update_shipping_method'][$packageId]
                    );
                    $newInstanceId = (isset($colonSplit[1])
                        ? trim($colonSplit[1])
                        : false);
                    $orderShipping = new \WC_Order_Item_Shipping($newInstanceId);
                    $orderShipping->set_method_id(
                        $_POST['msunifaunonline_update_shipping_method'][$packageId]
                    );
                    if ($newInstanceId !== false
                        && method_exists($orderShipping, 'set_instance_id')
                    ) {
                        $orderShipping->set_instance_id($newInstanceId);
                    }
                    $orderShipping->set_order_id($postId);
                    if ($orderShipping->save()) {
                        Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                            $packageId,
                            sprintf(
                                __(
                                    'Succeeded in adding shipping method to order.',
                                    'msunifaunonline'
                                )
                        ));
                    } else {
                        Mediastrategi_UnifaunOnline_Session::setMessageError(
                            $packageId,
                            sprintf(
                                __(
                                    'Failed with adding shipping method to order.',
                                    'msunifaunonline'
                                )
                        ));
                    }
                }
            } else {
                Mediastrategi_UnifaunOnline_Session::setMessageError(
                    $packageId,
                    sprintf(
                        __(
                            'Failed with loading order.',
                            'msunifaunonline'
                        )
                ));
            }
        }
    }

    /**
     * @param WP_Post $post
     */
    public function renderMetaBoxes($post)
    {
        /** @since Woocommerce 3.0.0 */
        if ($order = WC_Order_Factory::get_order($post->ID)) {
            if ($shipping = $order->get_items('shipping')) {
                $packageIndex = 0;
                $usingUnifaun = false;
                foreach ($shipping as $shippingMethod)
                {
                    /** @var WC_Order_Item_Shipping $shippingMethod */
                    echo '<fieldset><legend>';
                    if ($packageIndex) {
                        printf(
                            _x('Shipping %d', 'shipping packages', 'woocommerce'),
                            $packageIndex + 1
                        );
                    } else {
                        print(_x('Shipping', 'shipping packages', 'woocommerce'));
                    }
                    echo '</legend>';
                    if (!$usingUnifaun) {
                        $usingUnifaun = strpos(
                            $shippingMethod->get_method_id(),
                            \Mediastrategi_UnifaunOnline::METHOD_ID) === 0;
                    }
                    $this->renderMetaBoxesForPackage(
                        $post,
                        $order,
                        $packageIndex,
                        $shippingMethod
                    );
                    echo '</fieldset>';
                    $packageIndex++;
                }
                if ($usingUnifaun
                    && $packageIndex > 1
                ) {
                    echo '<div class="meta actions"><input name="msunifaunonline_process_order_submit" type="submit" class="button-primary" value="'
                        . __('Process Order', 'msunifaunonline') . '" />';
                    echo '<input name="msunifaunonline_update_order_submit" type="submit" class="button" value="'
                        . __('Save Order', 'msunifaunonline') . '" /></div>';
                }

                // Add an nonce field so we can check for it later.
                wp_nonce_field(
                    'msunifaunonline_process_order',
                    'msunifaunonline_process_order'
                );
            }
        }
    }

    /**
     * @param int $postId
     * @param int $packageId
     */
    private function processOrderParcelPackages($postId, $packageId)
    {
        // Should we use custom packages?
        Mediastrategi_UnifaunOnline_Order::setUseCustomPackages(
            $postId,
            $packageId,
            !empty($_POST['msunifaunonline_use_custom_packages'][$packageId])
        );
        if (!empty($_POST['msunifaunonline_use_custom_packages'][$packageId])
            && isset($_POST['msunifaunonline_custom_packages'][$packageId])
        ) {
            $customPackages = array();
            $customPackagesRaw =
                (array) $_POST['msunifaunonline_custom_packages'][$packageId];
            if (!empty($customPackagesRaw)) {
                foreach ($customPackagesRaw as $rawPackage)
                {
                    if (!empty($rawPackage['copies'])
                        && !empty($rawPackage['packageCode'])
                        && !empty($rawPackage['weight'])
                    ) {
                        $customPackages[] = array(
                            'contents' => (string) $rawPackage['contents'],
                            'copies' => (int) $rawPackage['copies'],
                            'height' => (string) $rawPackage['height'],
                            'length' => (string) $rawPackage['length'],
                            'packageCode' => (string) $rawPackage['packageCode'],
                            'weight' => (string) $rawPackage['weight'],
                            'width' => (string) $rawPackage['width'],
                        );
                    }
                }
            }
            if (!empty($customPackages)) {
                Mediastrategi_UnifaunOnline_Order::setCustomPackages(
                    $postId,
                    $packageId,
                    $customPackages
                );
                Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                    $packageId,
                    sprintf(
                        __(
                            'Updated order package %d custom parcels.',
                            'msunifaunonline'
                        ),
                        $packageId + 1
                    )
                );
            } else {
                Mediastrategi_UnifaunOnline_Order::setCustomPackages(
                    $postId,
                    $packageId,
                    array()
                );
            }
        } else {
            Mediastrategi_UnifaunOnline_Order::setCustomPackages(
                $postId,
                $packageId,
                array()
            );
        }
    }

    /**
     * @param WP_Post $post
     * @param WC_Order $order
     * @param string $packageId
     * @param WC_Order_item_Shipping $instance
     */
    public function renderMetaBoxesForPackage($post, $order, $packageId, $instance)
    {
        $debug = \Mediastrategi_UnifaunOnline::getOption('debug') === 'yes';
        $html = '<div class="wrapper ' . ($debug ? 'verbose' : 'simple') . '"><dl>';

        $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems($order, $instance);
        if ($packageItems) {
            $html .= '<dt>' . __('Items', 'woocommerce') . '</dt><dd><ul class="package-items">';
            foreach ($packageItems as $item) {
                $html .= '<li><a href="' . admin_url('post.php?post=' . $item['data']->get_product_id()
                    . '&action=edit') . '">' . $item['data']->get_name() . ' &times; ' . $item['quantity'] . '</a></li>';
            }
            $html .= '</ul></dd>';
        }

        // Show select of available shipping methods
        /** @since WooCommerce 3.0.0 */
        if ($order = WC_Order_Factory::get_order($post->ID)) {
            /** @since WooCommerce 3.0.0 */
            if ($orderData = $order->get_data()) {
                $orderPackage = array(
                    'destination' => array(
                        'country' => $orderData['shipping']['country'],
                        'state' => $orderData['shipping']['state'],
                        'postcode' => $orderData['shipping']['postcode'],
                    ),
                );
                /** @since WooCommerce 2.6.0 */
                if ($zone = WC_Shipping_Zones::get_zone_matching_package(
                    $orderPackage)
                ) {
                    /** @since WooCommerce 2.6.0 */
                    if ($shippingMethods = $zone->get_shipping_methods(true)) {
                        $oldCode = $instance->get_method_id() . ':'
                            . $instance->get_instance_id() . ':'
                            . $instance->get_name();
                        $html .= '<dt>'
                            . __(
                                'Method',
                                'msunifaunonline'
                            )
                            . ':</dt><dd class="shipping-method"><input type="hidden" name="msunifaunonline_update_shipping_method_old['
                            . $packageId . ']" value="' . esc_attr($oldCode) . '" />'
                            . '<select class="wc-enhanced-select" name="msunifaunonline_update_shipping_method['
                            . $packageId . ']">';
                        foreach ($shippingMethods as $shippingMethod)
                        {
                            $code = $shippingMethod->id . ':' . $shippingMethod->instance_id
                                .':' . $shippingMethod->title;
                            $selected = ($code == $oldCode);
                            $html .= '<option value="' . esc_attr($code) . '"'
                                .  ($selected ? ' selected="selected"' : '') . '>'
                                . $shippingMethod->title . '</option>';
                        }
                        $html .= '</select></dd>';
                    }
                }
            }
        }

        $usingUnifaunShipping = strpos(
            $instance->get_method_id(),
            \Mediastrategi_UnifaunOnline::METHOD_ID) === 0;

        $html .= '<dt>' . __('Used for Order', 'msunifaunonline')
            . ':</dt><dd>' . (empty($usingUnifaunShipping)
                ? __('No', 'msunifaunonline')
                : __('Yes', 'msunifaunonline')) . '</dd>';

        if ($usingUnifaunShipping) {
            if (!class_exists('Mediastrategi_UnifaunOnline_ShippingMethod')) {
                Mediastrategi_UnifaunOnline_ShippingMethod_init();
            }
            $instanceObject = new \Mediastrategi_UnifaunOnline_ShippingMethod(
                $instance->get_instance_id()
            );
            $unifaunStatus = Mediastrategi_UnifaunOnline_Order::getStatus(
                $post->ID,
                $packageId
            );
            $html .= '<dt>' . __('Status', 'msunifaunonline')
                . ':</dt><dd>' . (empty($unifaunStatus)
                    ? __('Not processed', 'msunifaunonline')
                    : __('Processed', 'msunifaunonline')) . '</dd>';

            if ($errors = Mediastrategi_UnifaunOnline_Order::getShipmentErrors(
                $post->ID,
                $packageId
            )) {
                $html .= '<dt>' . __('Shipment Errors', 'msunifaunonline')
                    . ':</dt><dd class="errors"><pre>' . $errors . '</pre></dd>';
            }

            if ($selectedAgent = Mediastrategi_UnifaunOnline_Order::getAgent(
                $post->ID,
                $packageId
            )) {
                $html .= '<dt>' . __('Agent', 'msunifaunonline')
                    . ':</dt><dd class="agent"><ul>';
                foreach ($selectedAgent as $key => $value) {
                    if (!empty($value)) {
                        $html .= '<li><strong>' . $key . '</strong><span>' . $value . '</span></li>';
                    }
                }
                $html .= '</ul></dd>';
            }

            if ($selectedAgentService = Mediastrategi_UnifaunOnline_Order::getAgentService(
                $post->ID,
                $packageId
            )) {
                $html .= '<dt class="debug">' . __('Agent Service', 'msunifaunonline')
                    . ':</dt><dd class="debug">' . $selectedAgentService . '</dd></dt>';
            }

            // Create
            if ($request = Mediastrategi_UnifaunOnline_Order::getShipmentRequest(
                $post->ID,
                $packageId
            )) {
                $html .= '<dt class="debug">' . __('Create Request', 'msunifaunonline')
                    . ':</dt><dd class="debug"><pre>' . htmlentities($request) . '</pre></dd>';
            }
            if ($responseCode = Mediastrategi_UnifaunOnline_Order::getShipmentResponseCode(
                $post->ID,
                $packageId
            )) {
                $html .= '<dt class="debug">' . __('Create - HTTP Response Code', 'msunifaunonline')
                    . ':</dt><dd class="debug">' . $responseCode . '</dd>';
            }
            if ($responseBody = Mediastrategi_UnifaunOnline_Order::getShipmentResponseBodyRaw(
                $post->ID,
                $packageId
            )) {
                $html .= '<dt class="debug">' . __('Create - HTTP Response Body', 'msunifaunonline')
                    . ':</dt><dd class="debug"><pre>' . htmlentities($responseBody) . '</pre></dd>';
            }
            if ($responseBodyDecoded = Mediastrategi_UnifaunOnline_Order::getShipmentResponseBodyDecoded(
                $post->ID,
                $packageId
            )) {
                $html .= '<dt class="debug">' . __('Create - Decoded Response', 'msunifaunonline')
                    . ':</dt><dd class="debug"><pre>' . print_r($responseBodyDecoded, true) . '</dd>';
            }

            $wp_upload_dir = wp_upload_dir();

            // Shipments
            if ($shipments = Mediastrategi_UnifaunOnline_Order::getShipments(
                $post->ID,
                $packageId
            )) {

                // NOTE This is for backwards compatibility < 1.3
                if (!\Mediastrategi_UnifaunOnline::isNumericArray($shipments)) {
                    $shipments = array($shipments);
                }

                // NOTE: New syntax below from 1.2.17 and above

                $html .= '<dt class="shipments">' . (count($shipments) > 1
                    ? __('Shipments:', 'msunifaunonline')
                    : __('Shipment:', 'msunifaunonline')) . '</dt><dd class="shipments"><ul>';

                foreach ($shipments as $shipment)
                {
                    $html .= '<li><dl>';
                    if (!empty($shipment['nr'])) {
                        $html .= '<dt>' . __('Number', 'msunifaunonline')
                            . '</dt><dd>' . $shipment['nr'] . '</dd>';
                    }
                    if (!empty($shipment['lnk'])) {
                        $html .= '<dt>' . __('Tracking Link', 'msunifaunonline') . '</dt><dd><a target="_blank" href="'
                            . $shipment['lnk'] . '">' . __('Click here to open', 'msunifaunonline') . '</a></dd>';
                    }
                    if (!empty($shipment['lbl'])
                        && is_array($shipment['lbl'])
                    ) {
                        $html .= '<dt>' . (count($shipment['lbl']) > 1
                            ? __('Labels', 'msunifaunonline')
                            : __('Label', 'msunifaunonline')) . '</dt><dd><ul>';
                        foreach ($shipment['lbl'] as $printFile) {
                            $html .= '<li><a target="_blank" href="'
                                . $wp_upload_dir['baseurl'] . $printFile . '">' . __('Download', 'msunifaunonline') . '</a></li>';
                        }
                        $html .= '</ul></dd>';
                    }
                    $html .= '</dl></li>';
                }
                $html .= '</ul></dd>';

            } else {

                // NOTE: This below is only for backward compatability

                // Shipment number
                if ($shipmentNumber = Mediastrategi_UnifaunOnline_Order::getShipmentNumber(
                    $post->ID,
                    $packageId
                )) {
                    $html .= '<dt>' . __('Shipment Number', 'msunifaunonline')
                        . ':</dt><dd>' . $shipmentNumber . '</dd>';
                }

                // Print
                if ($printFile = Mediastrategi_UnifaunOnline_Order::getShipmentPrintFile(
                    $post->ID,
                    $packageId
                )) {
                    $html .= '<dt>' . __('Shipping Label', 'msunifaunonline') . ':</dt><dd><a target="_blank" href="'
                        . $wp_upload_dir['baseurl'] . $printFile . '">' . __('Download', 'msunifaunonline') . '</a></dd>';
                }

                // Additional labels
                if ($additionalLabels = Mediastrategi_UnifaunOnline_Order::getShippingAdditionalLabels(
                    $post->ID,
                    $packageId
                )) {
                    if ($files = explode(',', $additionalLabels)) {
                        $html .= '<dt>' . __('Additional Labels', 'msunifaunonline') . ':</dt><dd><ul>';
                        foreach ($files as $file)
                        {
                            $html .= '<li><a target="_blank" href="'
                                . $wp_upload_dir['baseurl'] . $file . '">'
                                . __('Download', 'msunifaunonline') . '</a></li>';
                        }
                        $html .= '</ul></dd>';
                    }
                }

                // Tracking
                if ($trackingUrl = Mediastrategi_UnifaunOnline_Order::getTrackingUrl(
                    $post->ID,
                    $packageId
                )) {
                    $html .= '<dt>' . __('Tracking Link', 'msunifaunonline') . ':</dt><dd><a target="_blank" href="'
                        . $trackingUrl . '">' . __('Click here to open', 'msunifaunonline') . '</a></dd>';
                }
            }

            // Calculated preview
            $orderDescription = \Mediastrategi_UnifaunOnline::getWoocommerce()
                ->getProductsDescription($packageItems);
            $dimensionUnit = (isset($instanceObject, $instanceObject->instance_settings['dimension_unit'])
                ? $instanceObject->instance_settings['dimension_unit']
                : null);
            $weightUnit = (isset($instanceObject, $instanceObject->instance_settings['weight_unit'])
                ? $instanceObject->instance_settings['weight_unit']
                : null);
            $minimumWeight = (isset($instanceObject, $instanceObject->instance_settings['minimum_weight'])
                ? $instanceObject->instance_settings['minimum_weight']
                : null);
            $largestDimension = (isset($instanceObject, $instanceObject->instance_settings['package_force_largest_dimension'])
                ? $instanceObject->instance_settings['package_force_largest_dimension']
                : null);
            $mediumDimension = (isset($instanceObject, $instanceObject->instance_settings['package_force_medium_dimension'])
                ? $instanceObject->instance_settings['package_force_medium_dimension']
                : null);
            $smallestDimension = (isset($instanceObject, $instanceObject->instance_settings['package_force_smallest_dimension'])
                ? $instanceObject->instance_settings['package_force_smallest_dimension']
                : null);
            $size = Mediastrategi_UnifaunOnline::getOrderSize(
                $packageItems,
                $dimensionUnit,
                $weightUnit,
                $minimumWeight,
                $largestDimension,
                $mediumDimension,
                $smallestDimension
            );
            $html .= '<dt>' . __('Summary:', 'msunifaunonline') . '</dt><dd id="msunifaun_online-dimension-weight"><table>';
            $html .= '<tr><td>' . __('Contents', 'msunifaunonline') . '</td><td>' . $orderDescription . '</td></tr>';
            $html .= '<tr><td>' . __('Height', 'msunifaunonline') . '</td><td>' . round($size['height'], 2) . ' ' . ($dimensionUnit ?: 'm') . '</td></tr>';
            $html .= '<tr><td>' . __('Length', 'msunifaunonline') . '</td><td>' . round($size['length'], 2) . ' ' . ($dimensionUnit ?: 'm') . '</td></tr>';
            $html .= '<tr><td>' . __('Volume', 'msunifaunonline') . '</td><td>' . round($size['volume'], 2) . ' ' . ($dimensionUnit ?: 'm') . '<sup>3</sup></td></tr>';
            $html .= '<tr><td>' . __('Weight', 'msunifaunonline') . '</td><td>' . round($size['weight'], 2) . ' ' . ($weightUnit ?: 'kg') . '</td></tr>';
            $html .= '<tr><td>' . __('Width', 'msunifaunonline') . '</td><td>' . round($size['width'], 2) . ' ' . ($dimensionUnit ?: 'm') . '</td></tr>';
            $html .= '</table></dd>';

            // If no packages are found, generate a package preview
            if (!$customPackages = Mediastrategi_UnifaunOnline_Order::getCustomPackages(
                $post->ID,
                $packageId
            )) {
                $orderWeight = $size['weight'];
                $orderPackageType = '';
                if (!empty($instanceObject->instance_settings['package_type'])) {
                    if ($instanceObject->instance_settings['package_type'] === '-') {
                        if (!empty($instanceObject->instance_settings['package_type_custom'])) {
                            $orderPackageType = $instanceObject->instance_settings['package_type_custom'];
                        }
                    } else {
                        $orderPackageType = $instanceObject->instance_settings['package_type'];
                    }
                }
                $customPackages = array(array(
                    'contents' => $orderDescription,
                    'copies' => 1,
                    'height' => round($size['height'], 2),
                    'length' => round($size['length'], 2),
                    'packageCode' => (string) $orderPackageType,
                    'weight' => round($size['weight'], 2),
                    'width' => round($size['width'], 2),
                ));
            }
            $useCustomPackages = (Mediastrategi_UnifaunOnline_Order::getUseCustomPackages(
                $post->ID,
                $packageId
            ) ? true : false);
            $html .= '<dt>' . __('Parcels', 'msunifaunonline')
                . ':</dt><dd id="msunifaunonline-shipping-custom-packages"><script type="application/json" data-package-index="' . $packageId . '">'
                . json_encode($customPackages) . '</script><label><input name="msunifaunonline_use_custom_packages[' . $packageId
                . ']" type="checkbox" value="1" ' . ($useCustomPackages ? 'checked="checked" ': '') . '/>'
                . __('Customize Parcels', 'msunifaunonline') . '</label><table><thead><tr>'
                . '<th title="' . __('Quantity', 'msunifaunonline') . '">' . __('Qty', 'msunifaunonline') . '</th>'
                . '<th title="' . __('Package Type', 'msunifaunonline') . '">' . __('PT', 'msunifaunonline') . '</th>'
                . '<th title="' . __('Contents', 'msunifaunonline') . '">' . __('Txt', 'msunifaunonline') . '</th>'
                . '<th title="' . __('Height', 'msunifaunonline') . '">' . __('Hth', 'msunifaunonline') . '</th>'
                . '<th title="' . __('Length', 'msunifaunonline') . '">' . __('Lth', 'msunifaunonline') . '</th>'
                . '<th title="' . __('Weight', 'msunifaunonline') . '">' . __('Wght', 'msunifaunonline') . '</th>'
                . '<th title="' . __('Width', 'msunifaunonline') . '">' . __('Wth', 'msunifaunonline') . '</th>'
                . '</tr></thead><tbody></tbody></table><a class="button button-small add-row" href="#">'
                . __('+', 'msunifaunonline') . '</a>'
                . '<a class="button button-small remove-row" href="#">'
                . __('-', 'msunifaunonline') . '</a>'
                . '</dd>';
        }

        $html .= '</dl>';
        $html .= '<div class="actions">';

        if ($usingUnifaunShipping) {
            if (empty($unifaunStatus)) {
                $html .= '<input name="msunifaunonline_process_package_submit[' . $packageId . ']" type="submit" class="button-primary" value="'
                    . __('Process', 'msunifaunonline') . '" />';
            } else {
                $html .= '<input name="msunifaunonline_process_package_submit[' . $packageId . ']" type="submit" class="button debug" value="'
                    . __('Re-process', 'msunifaunonline') . '" />';
            }
        }

        $html .= '<input name="msunifaunonline_update_package[' . $packageId . ']" type="submit" class="' . ($usingUnifaunShipping ? 'button' : 'button-primary')
            . '" value="' . __('Save', 'msunifaunonline') . '" />';

        $html .= '</div></div>';
        echo $html;
    }

    /**
     * @param array [$columns = array()]
     * @return array
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function manage_shop_order_posts_columns($columns = array())
    {
        // Process orders directly from admin-table
        if (isset($_GET)
            && !empty($_GET['post_type'])
            && $_GET['post_type'] === 'shop_order'
            && !empty($_GET['msunifaunonline_process'])
        ) {
            $orderId = (int) $_GET['msunifaunonline_process'];
            \Mediastrategi_UnifaunOnline::getWooCommerce()->processOrder(
                $orderId,
                true,
                false,
                true
            );

            // Redirect to get rid of argument
            header('Location: ?post_type=shop_order&process_done='.$orderId);
            exit;
        }

        $columns['msunifaunonline_order'] = __(
            'Unifaun Online',
            'msunifaunonline'
        );
        return $columns;
    }

    /**
     * @param array $column
     * @param int $orderId
     */
    public function manage_posts_custom_column($column, $orderId)
    {
        if (!empty($column)
            && isset($orderId)
            && $column === 'msunifaunonline_order'
        ) {
            if ($order = WC_Order_Factory::get_order($orderId)) {
                /** @var WC_Order $order */
                if ($shipping = $order->get_items('shipping')) {
                    $packageIndex = 0;
                    $foundOrderToProcess = false;
                    foreach ($shipping as $shippingMethod)
                    {
                        /** @var WC_Order_Item_Shipping $shippingMethod */
                        // Is the shipping-method used for the order a Unifaun Online method?
                        if (strpos(
                            $shippingMethod->get_method_id(),
                            \Mediastrategi_UnifaunOnline::METHOD_ID) === 0
                        ) {
                            $unifaunStatus = \Mediastrategi_UnifaunOnline_Order::getStatus(
                                $orderId,
                                $packageIndex
                            );
                            if ($unifaunStatus) {
                                $wp_upload_dir = wp_upload_dir();
                                $html = '<ul style="margin: 0;">';

                                // Shipments
                                if ($shipments = Mediastrategi_UnifaunOnline_Order::getShipments(
                                    $orderId,
                                    $packageIndex
                                )) {
                                    // NOTE This is for backwards compatibility < 1.3
                                    if (!\Mediastrategi_UnifaunOnline::isNumericArray($shipments)) {
                                        $shipments = array($shipments);
                                    }

                                    // NOTE: New syntax below from 1.2.17 and above

                                    $multipleShipments = (count($shipments) > 1);
                                    foreach ($shipments as $i => $shipment)
                                    {
                                        if (!empty($shipment['lbl'])
                                            && is_array($shipment['lbl'])
                                        ) {
                                            $multipleLabels = (count($shipment['lbl']) > 1);
                                            foreach ($shipment['lbl'] as $j => $printFile) {
                                                $html .= '<li><a target="_blank" href="' . $wp_upload_dir['baseurl'] . $printFile . '">';
                                                if ($multipleShipments) {
                                                    if ($multipleLabels) {
                                                        $html .= sprintf(__('Label %d-%d', 'msunifaunonline'), $i + 1, $j + 1);
                                                    } else {
                                                        $html .= sprintf(__('Label %d', 'msunifaunonline'), $i + 1);
                                                    }
                                                } elseif ($multipleLabels) {
                                                    $html .= sprintf(__('Label %d', 'msunifaunonline'), $j + 1);
                                                } else {
                                                    $html .= __('Label', 'msunifaunonline');
                                                }
                                                $html .= '</a></li>';
                                            }
                                        }

                                        if (!empty($shipment['lnk'])) {
                                            $html .= '<li><a target="_blank" href="' . $shipment['lnk'] . '">'
                                                . ($multipleShipments
                                                    ? sprintf(__('Tracking %d', 'msunifaunonline'), $i + 1)
                                                    : __('Tracking', 'msunifaunonline'))
                                                . '</a></li>';
                                        }
                                    }

                                } else {

                                    // NOTE: This below is only for backward compatibility
                                    if ($printFile = Mediastrategi_UnifaunOnline_Order::getShipmentPrintFile(
                                        $orderId,
                                        $packageIndex
                                    )) {
                                        $html .= '<li><a target="_blank" href="' . $wp_upload_dir['baseurl'] . $printFile . '">' . __('Label', 'msunifaunonline') . '</a></li>';
                                    }
                                    if ($trackingUrl = Mediastrategi_UnifaunOnline_Order::getTrackingUrl(
                                        $orderId,
                                        $packageId
                                    )) {
                                        $html .= '<li><a target="_blank" href="' . $trackingUrl . '">' . __('Tracking', 'msunifaunonline') . '</a></li>';
                                    }
                                }

                                $html .= '</ul>';
                                echo $html;

                            } else {
                                $foundOrderToProcess = true;
                            }
                        }
                        $packageIndex++;
                    }
                    if ($foundOrderToProcess) {
                        echo '<a class="button button-primary" href="?post_type=shop_order&msunifaunonline_process='
                            . strip_tags($orderId) . '">' . __('Process', 'msunifaunonline') . '</a>';
                    }
                }
            }
        }
    }
}
