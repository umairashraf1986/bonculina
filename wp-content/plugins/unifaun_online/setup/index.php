<?php
/**
 * TODO Make is possible to specify existing user id when selecting account
 * TODO Use existing id in request to Unifaun when ordering ERP-connect (waiting on Unifaun)
 */

/**
 *
 */
class Mediastrategi_UnifaunOnline_Setup
{

    /**
     *
     */
    public function execute()
    {
        $page = array(
            'body' => '',
            'body_after' => '</section>',
            'body_before' => '<section id="msunifaun-setup-body" class="form-table">',
            'message_error' => '',
            'message_error_after' => '</span>',
            'message_error_before' => '<span class="message error">',
            'message_success' => '',
            'message_success_after' => '</span>',
            'message_success_before' => '<span class="message success">',
            'subtitle' => '',
            'subtitle_after' => '</h2>',
            'subtitle_before' => '<h2>',
            'title' => __(
                'Unifaun Setup',
                'msunifaunonline'
            ),
            'title_after' => '</h1>',
            'title_before' => '<h1>',
            'wrapper_after' => '</div>',
            'wrapper_before' => '<div id="msunifaun-setup" class="wrap">'
        );
        if (is_admin()) {
            $route = $this->getRoute();
            $page = $this->dispatch(
                $page,
                $route
            );
        }
        $this->output($page);
    }

    /**
     * @param array $page
     */
    private function output($page)
    {
        echo $this->getOutput($page);
    }

    /**
     * @param array $page
     * @return string
     */
    private function getOutput($page)
    {
        return sprintf(
            '%s%s%s%s%s%s%s',
            $page['wrapper_before'],
            (!empty($page['title'])
                ? $page['title_before'] . $page['title'] . $page['title_after']
                : ''),
            (!empty($page['subtitle'])
                ? $page['subtitle_before'] . $page['subtitle'] . $page['subtitle_after']
                : ''),
            (!empty($page['message_error'])
                ? $page['message_error_before'] . $page['message_error'] . $page['message_error_after']
                : ''),
            (!empty($page['message_success'])
                ? $page['message_success_before'] . $page['message_success'] . $page['message_success_after']
                : ''),
            (!empty($page['body'])
                ? $page['body_before'] . $page['body'] . $page['body_after']
                : ''),
            $page['wrapper_after']
        );
    }

    /**
     * @return string
     */
    private function getRoute()
    {
        $route = 'index';
        if (!empty($_GET['route'])) {
            $rawRoute = (string) $_GET['route'];
            if (preg_match(
                '/^[a-z]+$/',
                $rawRoute) === 1
            ) {
                $route = $rawRoute;
            }
        }
        return $route;
    }

    /**
     * @param array $page
     * @param array $route
     */
    private function dispatch(
        $page,
        $route
    ) {
        $methodName = sprintf(
            '%sRoute',
            $route
        );
        if (method_exists(
            $this,
            $methodName
        )) {
            $acl = sprintf(
                '%sRouteACL',
                $route
            );
            if (!method_exists(
                $this,
                $acl
                )
                || $this->$acl()
            ) {
                $page = $this->$methodName(
                    $page,
                    $route
                );
            } else {
                $page['message_error'] = __(
                    'You lack permissions to access this area!',
                    'msunifaunonline'
                );
            }

        } else {
            $page['message_error'] = __(
                'Route does not exist',
                'msunifaunonline'
            );
        }
        return $page;
    }

    /**
     * @param array $page
     * @param array $route
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFunctionParameter)
     * @codingStandardsIgnoreStart
     */
    private function indexRoute(
        $page,
        $route
    ) {
        /* @codingStandardsIgnoreStart */
        $page['subtitle'] = __(
            'Welcome',
            'msunifaunonline'
        );
        $body = & $page['body'];
        $body = $this->processTemplate(
            'index',
            array(
                'hasAccount' =>
                    \Mediastrategi_UnifaunOnline::hasAccount()
            )
        );
        return $page;
    }

    /**
     * @return bool
     */
    private function accountRouteACL()
    {
        return !\Mediastrategi_UnifaunOnline::hasAccount();
    }

    /**
     * @param array $page
     * @param array $route
     * @return array
     */
    private function accountRoute(
        $page,
        $route
    ) {
        $page['subtitle'] = __(
            'Account',
            'msunifaunonline'
        );

        // Catch valid nonce
        $validNonce = !empty($_POST['unifaun_setup_nonce'])
            && wp_verify_nonce(
                $_POST['unifaun_setup_nonce'],
                'unifaun_setup_nonce'
        );

        // Catch org-no
        $orgNo = '';
        if (!empty($_POST['account_orgno'])) {
            $rawOrgNo = (string) $_POST['account_orgno'];
            $rawOrgNo = str_replace(
                array(
                    ' ',
                    '-',
                ),
                array(
                    '',
                    ''
                ),
                $rawOrgNo
            );
            if (preg_match(
                '/^[0-9]{10}$/',
                $rawOrgNo
                ) === 1
            ) {
                $orgNo = $rawOrgNo;
            } else {
                $page['message_error'] = __(
                    'You have entered a invalid organization number.',
                    'msunifaunonline'
                );
            }
        }

        // Catch user-id
        $userId = '';
        if (!empty($_POST['account_userid'])) {
            $rawUserId = (string) $_POST['account_userid'];
            if (preg_match(
                '/^[0-9]+$/',
                $rawUserId
                ) === 1
                || $rawUserId === '-'
            ) {
                $userId = $rawUserId;
            }
        }

        // Catch application-code, application-products and application-product-partners here
        $applicationCode = '';
        $applicationProducts = array();
        $applicationProductPartners = array();
        if (!empty($_POST['account_application_code'])) {
            $rawApplicationCode = (string) $_POST['account_application_code'];
            if (preg_match(
                '/^[A-Z\-_0-9]+$/',
                $rawApplicationCode
                ) === 1
            ) {
                $applicationCode = $rawApplicationCode;

                // Catch application-products here
                if (!empty($_POST['products'])
                    && is_array($_POST['products'])
                    && !empty($_POST['products'][$applicationCode])
                    && is_array($_POST['products'][$applicationCode])
                ) {
                    foreach (
                        array_keys($_POST['products'][$applicationCode])
                        as $selectedProduct
                    ) {
                        if (is_string($selectedProduct)
                            && preg_match(
                                '/^[A-Z\-_0-9]+$/',
                                $selectedProduct
                            ) === 1
                            && !isset($applicationProducts[$selectedProduct])
                        ) {
                            $applicationProducts[$selectedProduct] = array(
                                'code' => $selectedProduct
                            );

                            // Catch application-product-partners here
                            if (!empty($_POST['partners'])
                                && is_array($_POST['partners'])
                                && !empty($_POST['partners'][$applicationCode])
                                && is_array($_POST['partners'][$applicationCode])
                                && !empty($_POST['partners'][$applicationCode][$selectedProduct])
                                && is_array($_POST['partners'][$applicationCode][$selectedProduct])
                            ) {
                                foreach (
                                    $_POST['partners'][$applicationCode][$selectedProduct]
                                    as $partnerCode => $fields
                                ) {
                                    if (preg_match(
                                        '/^[A-Z0-9\-_]+$/',
                                        $partnerCode)
                                        === 1
                                        && !empty($fields['CUSTNO'])
                                        && is_string($fields['CUSTNO'])
                                    ) {
                                        if (!isset($applicationProductPartner[$selectedProduct])) {
                                            $applicationProductPartner[$selectedProduct] = array();
                                        }
                                        $applicationProductPartners[$selectedProduct][$partnerCode] = array(
                                            'code' => $partnerCode,
                                            'customerNumber' => $fields['CUSTNO']
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Catch address here
        $address = array();
        if (!empty($_POST['address'])
            && is_array($_POST['address'])
            && !empty($_POST['address']['company'])
            && is_array($_POST['address']['company'])
        ) {
            $address = array(
                'company' => array(
                    'invoiceEmail' => (!empty($_POST['address']['company']['invoiceEmail'])
                        ? (string) $_POST['address']['company']['invoiceEmail']
                        : ''),
                    'orgNo' => (!empty($_POST['address']['company']['orgNo'])
                        ? (string) $_POST['address']['company']['orgNo']
                        : ''),
                    'name' => (!empty($_POST['address']['company']['name'])
                        ? (string) $_POST['address']['company']['name']
                        : ''),
                    'vatNo' => (!empty($_POST['address']['company']['vatNo'])
                        ? (string) $_POST['address']['company']['vatNo']
                        : ''),
                ),
            );

            if (!empty($_POST['address']['company']['contact1'])
                && is_array($_POST['address']['company']['contact1'])
            ) {
                $address['company']['contact1'] = array(
                    'email' => (!empty($_POST['address']['company']['contact1']['email'])
                        ? (string) $_POST['address']['company']['contact1']['email']
                        : ''),
                    'name' => (!empty($_POST['address']['company']['contact1']['name'])
                        ? (string) $_POST['address']['company']['contact1']['name']
                        : ''),
                    'phone' => (!empty($_POST['address']['company']['contact1']['phone'])
                        ? (string) $_POST['address']['company']['contact1']['phone']
                        : ''),
                    'sms' => (!empty($_POST['address']['company']['contact1']['sms'])
                        ? (string) $_POST['address']['company']['contact1']['sms']
                        : '')
                );
            }

            if (!empty($_POST['address']['company']['deliveryAddress'])
                && is_array($_POST['address']['company']['deliveryAddress'])
            ) {
                $address['company']['deliveryAddress'] = array(
                    'address1' => (!empty($_POST['address']['company']['deliveryAddress']['address1'])
                        ? (string) $_POST['address']['company']['deliveryAddress']['address1']
                        : ''),
                    'city' => (!empty($_POST['address']['company']['deliveryAddress']['city'])
                        ? (string) $_POST['address']['company']['deliveryAddress']['city']
                        : ''),
                    'country' => (!empty($_POST['address']['company']['deliveryAddress']['country'])
                        ? (string) $_POST['address']['company']['deliveryAddress']['country']
                        : ''),
                    'zipcode' => (!empty($_POST['address']['company']['deliveryAddress']['zipcode'])
                        ? (string) $_POST['address']['company']['deliveryAddress']['zipcode']
                        : '')
                );
            }

            if (!empty($_POST['address']['company']['postalAddress'])
                && is_array($_POST['address']['company']['postalAddress'])
            ) {
                if (!empty($_POST['address']['company']['postalAddress']['sameAsDeliveryAddress'])) {
                    $address['company']['postalAddress'] =
                        $address['company']['deliveryAddress'];
                } else {
                    $address['company']['postalAddress'] = array(
                        'address1' => (!empty($_POST['address']['company']['postalAddress']['address1'])
                            ? (string) $_POST['address']['company']['postalAddress']['address1']
                            : ''),
                        'city' => (!empty($_POST['address']['company']['postalAddress']['city'])
                            ? (string) $_POST['address']['company']['postalAddress']['city']
                            : ''),
                        'country' => (!empty($_POST['address']['company']['postalAddress']['country'])
                            ? (string) $_POST['address']['company']['postalAddress']['country']
                            : ''),
                        'zipcode' => (!empty($_POST['address']['company']['postalAddress']['zipcode'])
                            ? (string) $_POST['address']['company']['postalAddress']['zipcode']
                            : '')
                    );
                }
            }

            if (!empty($_POST['address']['company']['invoiceAddress'])
                && is_array($_POST['address']['company']['invoiceAddress'])
            ) {
                if (!empty($_POST['address']['company']['invoiceAddress']['sameAsPostalAddress'])) {
                    $address['company']['invoiceAddress'] =
                        $address['company']['postalAddress'];
                } else {
                    $address['company']['invoiceAddress'] = array(
                        'address1' => (!empty($_POST['address']['company']['invoiceAddress']['address1'])
                            ? (string) $_POST['address']['company']['invoiceAddress']['address1']
                            : ''),
                        'city' => (!empty($_POST['address']['company']['invoiceAddress']['city'])
                            ? (string) $_POST['address']['company']['invoiceAddress']['city']
                            : ''),
                        'country' => (!empty($_POST['address']['company']['invoiceAddress']['country'])
                            ? (string) $_POST['address']['company']['invoiceAddress']['country']
                            : ''),
                        'zipcode' => (!empty($_POST['address']['company']['invoiceAddress']['zipcode'])
                            ? (string) $_POST['address']['company']['invoiceAddress']['zipcode']
                            : '')
                    );
                }
            }
        }

        /* die(sprintf(
        'partners: <pre>%s</p>, products: <pre>%s</pre>, address: <pre>%s</pre>, post: <pre>%s</pre>',
        print_r($applicationProductPartners, true),
        print_r($applicationProducts, true),
        print_r($address, true),
        print_r($_POST, true)
        )); */

        // Catch approved-terms
        $approvedTerms = false;
        if (!empty($_POST['terms_approved'])) {
            $approvedTerms = true;
        }

        if ($validNonce
            && $orgNo
            && $userId
        ) {
            return $this->accountsApplication(
                $address,
                $applicationCode,
                $applicationProductPartners,
                $applicationProducts,
                $approvedTerms,
                $orgNo,
                $page,
                $route,
                $userId
            );

        } else if ($validNonce
            && $orgNo
        ) {
            return $this->accountsSelectAccount(
                $orgNo,
                $page,
                $route
            );

        } else {
            return $this->accountsIndex(
                $page,
                $route
            );
        }
    }

    /**
     * @param array $page
     * @param array $route
     * @return array
     */
    private function accountsIndex(
        $page,
        $route
    ) {
        $body = & $page['body'];
        $body .= $this->processTemplate(
            'account/index',
            array('route' => $route)
        );
        return $page;
    }

    /**
     * @param string $orgNo
     * @param array $page
     * @param array $route
     * @return array
     */
    private function accountsSelectAccount(
        $orgNo,
        $page,
        $route
    ) {
        $page['subtitle'] = __(
            'Select existing account',
            'msunifaunonline'
        );
        $body = & $page['body'];
        $onboarding =
            \Mediastrategi_UnifaunOnline::getOnboarding();
        $transaction = $onboarding->accountsGet(array(
            'orgNo' => $orgNo
        ));

        $accounts = array();
        if (!$transaction->hasError()) {
            $accounts = $transaction->getResponseBodyDecoded();
        }

        $body = $this->processTemplate(
            'account/select-account',
            array(
                'accounts' => (!empty($accounts)
                    && !empty($accounts['accounts'])
                    ? $accounts['accounts']
                    : array()),
                'error' => $transaction->getError(),
                'orgNo' => $orgNo,
                'route' => $route
            )
        );

        return $page;
    }

    /**
     * @param array $address
     * @param string $applicationCode
     * @param array $applicationProductPartners
     * @param array $applicationProducts
     * @param bool $approvedTerms
     * @param string $orgNo
     * @param array $page
     * @param array $route
     * @param string $userId
     * @return array
     */
    private function accountsApplication(
        $address,
        $applicationCode,
        $applicationProductPartners,
        $applicationProducts,
        $approvedTerms,
        $orgNo,
        $page,
        $route,
        $userId
    ) {
        $page['subtitle'] = __(
            'Select application and specify company details to complete order',
            'msunifaunonline'
        );
        $body = & $page['body'];

        $onboarding =
            \Mediastrategi_UnifaunOnline::getOnboarding();

        // Is the user application ready for approval?
        if ($applicationCode
            && $address
            && $approvedTerms
        ) {
            $uniquePartners = array();
            foreach ($applicationProductPartners as $product => $partners) {
                foreach ($partners as $partnerCode => $partner)
                {
                    if (!isset($uniquePartners[$partnerCode])) {
                        $uniquePartners[$partnerCode] = $partner;
                    }
                }
            }

            $applicationBody = array(
                'company' =>
                    $address['company'],
                'language' =>
                    \Mediastrategi_UnifaunOnline::getOnboardingLanguage(),
                'partners' =>
                    array_values($uniquePartners),
                'products' =>
                    array_values($applicationProducts)
            );

            /* die(sprintf(
                'application body:<pre>%s</pre>',
                print_r($applicationBody, true)
            )); */

            // Try finalize order here
            $transaction = $onboarding->applicationsPost(
                $applicationCode,
                $applicationBody
            );
            $login = '';
            $apiKey = '';
            $apiSecret = '';

            /* printf(
            '<pre>request: %s</pre>',
            print_r($transaction, true)
            ); */

            if (!$transaction->hasError()
                && $transaction->getResponseStatusCode() != 500
            ) {
                $application =
                    $transaction->getResponseBodyDecoded();

                if (!empty($application['message'])
                    && !empty($application['code'])
                ) {
                    $page['message_error'] = sprintf(
                        '%s (%s)',
                        $application['message'],
                        $application['code']
                    );
                }

                if (!empty($application['login'])) {
                    $login = $application['login'];
                    \Mediastrategi_UnifaunOnline::setOption(
                        'api_user_id',
                        $login
                    );
                }
                if (!empty($application['apiKey'])) {
                    $explode = explode(
                        '-',
                        $application['apiKey']
                    );
                    if ($explode
                        && count($explode) === 2
                    ) {
                        $apiKey = $explode[0];
                        $apiSecret = $explode[1];
                        \Mediastrategi_UnifaunOnline::setOption(
                            'api_key_id',
                            $apiKey
                        );
                        \Mediastrategi_UnifaunOnline::setOption(
                            'api_key_secret',
                            $apiSecret
                        );
                    }

                    if (empty($page['message_error'])) {
                        $page['subtitle'] = __(
                            'Order confirmation',
                            'msunifaunonline'
                        );

                        $body = $this->processTemplate(
                            'account/order',
                            array(
                                'apiKey' =>
                                    $apiKey,
                                'apiSecret' =>
                                    $apiSecret,
                                'login' =>
                                    $login
                            )
                        );
                        return $page;
                    }
                }
            } else {
                if (!$transaction->hasError()) {
                    $page['message_error'] = __(
                        'API server responded with error, see log for more information.',
                        'msunifaunonline'
                    );
                    error_log(sprintf(
                        'Unifaun API response (%s): %s',
                        $transaction->getResponseStatusCode(),
                        $transaction->getResponseBody()
                    ));
                }
            }
        }

        // Get list of applicable applications
        $transaction = $onboarding->applicationsGet();
        $applications = array();
        if (!$transaction->hasError()) {
            $decoded =
                $transaction->getResponseBodyDecoded();
            if (!empty($decoded['applications'])
                && is_array($decoded['applications'])
            ) {
                $applications = $decoded['applications'];
            }
        }

        $transaction = $onboarding->termsGet();
        $terms = $transaction->getResponseBodyDecoded();

        if (!$address) {
            $address = array(
                'company' => array(
                    'contact1' => array(
                        'email' => '',
                        'name' => '',
                        'phone' => '',
                        'sms' => ''
                    ),
                    'deliveryAddress' => array(
                        'address1' => '',
                        'city' => '',
                        'country' => '',
                        'zipcode' => ''
                    ),
                    'invoiceAddress' => array(
                        'address1' => '',
                        'city' => '',
                        'country' => '',
                        'zipcode' => ''
                    ),
                    'invoiceEmail' => '',
                    'name' => '',
                    'orgNo' => $orgNo,
                    'postalAddress' => array(
                        'address1' => '',
                        'city' => '',
                        'country' => '',
                        'zipcode' => ''
                    ),
                    'vatNo' => ''
                )
            );
        }

        $body = $this->processTemplate(
            'account/application',
            array(
                'address' =>
                    $address,
                'applicationCode' =>
                    $applicationCode,
                'applicationProductPartners' =>
                    $applicationProductPartners,
                'applicationProducts' =>
                    $applicationProducts,
                'applications' =>
                    $applications,
                'deliveryAddressCountryPickerHtml' =>
                    $this->getCountryPickerHtml(
                        $address['company']['deliveryAddress']['country']
                ),
                'error' =>
                    $transaction->hasError()
                    ? $transaction->getError()
                    : false,
                'invoiceAddressCountryPickerHtml' =>
                    $this->getCountryPickerHtml(
                        $address['company']['invoiceAddress']['country']
                ),
                'invoiceSameAsPostal' => (!empty($_POST['address'])
                    && !empty($_POST['address']['company'])
                    && !empty($_POST['address']['company']['invoiceAddress'])
                    && !empty($_POST['address']['company']['invoiceAddress']['sameAsPostalAddress'])
                    ? true : false),
                'orgNo' =>
                    $orgNo,
                'postalAddressCountryPickerHtml' =>
                    $this->getCountryPickerHtml(
                        $address['company']['postalAddress']['country']
                ),
                'postalSameAsDelivery' =>
                    (!empty($_POST['address'])
                        && !empty($_POST['address']['company'])
                        && !empty($_POST['address']['company']['postalAddress'])
                        && !empty($_POST['address']['company']['postalAddress']['sameAsDeliveryAddress'])
                        ? true : false),
                'terms' =>
                    $terms,
                'userId' =>
                    $userId
            )
        );
        return $page;
    }

    /**
     * @param string $template
     * @param array [$variables = array()]
     * @return string
     * @throws \Exception
     */
    private function processTemplate(
        $template,
        $variables = array()
    ) {
        $file = sprintf(
            '%s/templates/%s.phtml',
            dirname(__FILE__),
            $template
        );
        if (file_exists($file)) {
            if (!empty($variables)
                && is_array($variables)
            ) {
                foreach ($variables as $key => $value)
                {
                    ${$key} = $value;
                }
            }
            ob_start();
            include($file);
            if (!empty($variables)
                && is_array($variables)
            ) {
                foreach ($variables as $key => $value)
                {
                    unset(${$key});
                }
            }
            return ob_get_clean();
        } else {
            throw new \Exception(sprintf(
                __(
                    'Failed to find template: "%s" at: "%s"',
                    'msunifaunonline'
                ),
                $template,
                $file
            ));
        }
    }

    /**
     * @param string [$value = false]
     * @return string
     */
    private function getCountryPickerHtml($value = false)
    {
        $html = '';
        $countryList = require(dirname(__FILE__) . '/country-list.php');
        foreach ($countryList as $code => $name) {
            $selected = (!$value
                && $code === 'SE'
                || $value == $code);
            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                $code,
                ($selected ? ' selected="selected"' : ''),
                $name

            );
        }
        return $html;
    }

}
