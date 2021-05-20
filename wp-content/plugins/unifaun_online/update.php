<?php
/**
 * @see https://code.tutsplus.com/tutorials/a-guide-to-the-wordpress-http-api-automatic-plugin-updates--wp-25181
 * @see https://code.tutsplus.com/tutorials/distributing-your-plugins-in-github-with-automatic-updates--wp-34817
 */

if (!class_exists('Mediastrategi_UnifaunOnline_Update')) {
    class Mediastrategi_UnifaunOnline_Update
    {
        /**
         * @var array
         */
        private static $curlCache = array();

        /**
         * @var string
         */
        private $name = '';

        /**
         * @var string
         */
        private $version = '';

        /**
         * @var string
         */
        private $slug = '';

        /**
         * @var string
         */
        private $infoUrl = '';

        /**
         * @var string
         */
        private $archiveUrl = '';

        /**
         * @var string
         */
        private $httpBasicAuthUsername = '';

        /**
         * @var string
         */
        private $httpBasicAuthPassword = '';

        /**
         * @param array $options
         */
        public function __construct($options)
        {
            // Parse options
            if (!empty($options['name'])) {
                $this->name = (string) $options['name'];
            }
            if (!empty($options['slug'])) {
                $this->slug = (string) $options['slug'];
            }
            if (isset($options['infoUrl'])) {
                $this->infoUrl = (string) $options['infoUrl'];
            }
            if (isset($options['archiveUrl'])) {
                $this->archiveUrl = (string) $options['archiveUrl'];
            }
            if (!empty($options['version'])) {
                $this->version = (string) $options['version'];
            }
            if (isset($options['httpBasicAuthUsername'])) {
                $this->httpBasicAuthUsername =
                    (string) $options['httpBasicAuthUsername'];
            }
            if (isset($options['httpBasicAuthPassword'])) {
                $this->httpBasicAuthPassword =
                    (string) $options['httpBasicAuthPassword'];
            }

            // If we got a version and a version-url add update check
            if (!empty($this->name)
                && !empty($this->slug)
                && !empty($this->version)
                && !empty($this->infoUrl)
            ) {
                // die('<pre>' . print_r(func_get_args(), true) . '</pre>');
                // For new version checking
                add_filter(
                    'pre_set_site_transient_update_plugins',
                    array(
                        & $this,
                        'pre_set_site_transient_update_plugins'
                    )
                );

                // For change-log displaying
                add_filter(
                    'plugins_api',
                    array(
                        & $this,
                        'plugins_api'
                    ),
                    10,
                    3
                );

                // If we got a archive-url add post upgrade logic
                add_filter(
                    'upgrader_post_install',
                    array(
                        & $this,
                        'upgrader_post_install'
                    ),
                    10,
                    3
                );

                if (!empty($this->httpBasicAuthUsername)
                    && !empty($this->httpBasicAuthPassword)
                    && !empty($this->archiveUrl)
                ) {
                    // Add Basic Auth to HTTP requests
                    add_filter(
                        'http_request_args',
                        array (
                            & $this,
                            'http_request_args'
                        ),
                        10,
                        2
                    );
                }
            }

            // NOTE Comment out this to test update functionality
            /* set_site_transient(
                'update_plugins',
                null
            ); */
        }

        /**
         * Add basic auth if needed.
         *
         * @param array $request
         * @param string $url
         * @return array
         * @see wp-includes/class-http.php:232
         * @see https://johnblackbourn.com/wordpress-http-api-basicauth/
         */
        public function http_request_args($request, $url)
        {
            if ($url == $this->archiveUrl) {
                $request['headers']['Authorization'] =
                    sprintf(
                        'Basic %s',
                        base64_encode(
                            $this->httpBasicAuthUsername
                                . ':' . $this->httpBasicAuthPassword
                        )
                    );
            }
            return $request;
        }

        /**
         * Check for new version
         *
         * @param $transient
         * @return object $ transient
         */
        public function pre_set_site_transient_update_plugins($transient)
        {
            // Wait until plug-in version have been populated
            if (empty($transient)
                || empty($transient->checked)
                || empty($transient->checked[$this->name])
            ) {
                return $transient;
            }
            $version = $this->version;

            $newVersion = false;
            if ($result = $this->getUrl($this->infoUrl)) {
                if (!empty($result['new_version'])) {
                    $newVersion = $result['new_version'];
                }
            }

            if (!empty($result)
                && !empty($newVersion)
            ) {
                if (version_compare($version, $newVersion, '<')) {
                    $transient->response[$this->name] = (object) $result;
                } else {
                    $transient->no_update[$this->name] = (object) $result;
                }
            }

            // die(sprintf('%s this: <pre>%s</pre> args: <pre>%s</pre> result: <pre>%s</pre>',__METHOD__, print_r($this, true), print_r(func_get_args(), true), print_r($result, true)));
            return $transient;
        }


        /**
         * Do actions after installation of new version
         *
         * @param bool $res
         * @param $hook_extra
         * @param $result
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         * @codingStandardsIgnoreStart
         */
        public function upgrader_post_install($res, $hook_extra, $result)
        {
            /* @codingStandardsIgnoreEnd */
            // error_log(__METHOD__ . print_r(func_get_args(), true) . print_r($this, true));
            if (!empty($hook_extra)
                && !empty($hook_extra['plugin'])
                && $hook_extra['plugin'] == $this->name
            ) {
                global $wp_filesystem;
                $wasActived = is_plugin_active($this->name);
                $pluginFolder = dirname(__FILE__);

                // Is destination equal to current directory?
                if ($result['destination'] != $pluginFolder) {
                    // error_log(sprintf('moving plugin to %s', $pluginFolder));
                    $wp_filesystem->move(
                        $result['destination'],
                        $pluginFolder
                    );
                    $result['destination'] = $pluginFolder;
                    $result['destination_name'] = basename($pluginFolder);
                    if ($wasActived) {
                        activate_plugin($this->name);
                    }
                }
            }
            return $result;
        }
        
        /**
         * For information display.
         *
         * @param bool $res
         * @param array $action
         * @param object $arg
         * @return bool|object
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         * @codingStandardsIgnoreStart
         */
        public function plugins_api($res, $action, $arg)
        {
            /* @codingStandardsIgnoreEnd */
            if (!empty($arg)
                && !empty($arg->slug)
                && $arg->slug == $this->slug
            ) {
                if ($response = $this->getUrl($this->infoUrl)) {

                    // Convert description to HTML
                    if (!empty($response['sections'])
                        && !empty($response['sections']['description'])
                    ) {
                        $response['sections']['description'] =
                            self::getHtmlFromMarkdown($response['sections']['description']);
                    }

                    // Convert change-log to HTML
                    if (!empty($response['sections'])
                        && !empty($response['sections']['changelog'])
                    ) {
                        $response['sections']['changelog'] =
                            self::getHtmlFromMarkdown($response['sections']['changelog']);
                    }

                    // die('response: <pre>' . print_r($response, true) . ', this: ' . print_r($this, true) . '</pre>');

                    return (object) $response;
                }
            }
            return $res;
        }

        /**
         * @param string $url
         * @param bool [$json = true]
         * @return bool|string|array
         */
        private function getUrl($url, $json = true)
        {
            if (!isset(self::$curlCache[$url])) {
                $ch = curl_init();
                curl_setopt(
                    $ch,
                    CURLOPT_URL,
                    $url
                );
                curl_setopt(
                    $ch,
                    CURLOPT_TIMEOUT,
                    3
                ); 
                curl_setopt(
                    $ch,
                    CURLOPT_RETURNTRANSFER,
                    true
                );

                // HTTP Basic Authentication
                if (!empty($this->httpBasicAuthUsername)
                    && !empty($this->httpBasicAuthPassword)
                ) {
                    curl_setopt(
                        $ch,
                        CURLOPT_USERPWD,
                        sprintf(
                            '%s:%s',
                            $this->httpBasicAuthUsername,
                            $this->httpBasicAuthPassword
                        )
                    );
                }

                try {
                    $result = curl_exec($ch);
                    if (!empty($result)
                        && $json
                    ) {
                        $result = json_decode($result, true);
                    }
                } catch (\Exception $e) {
                    $result = false;
                }

                curl_close($ch);
                self::$curlCache[$url] = $result;
            }
            return self::$curlCache[$url];
        }

        /**
         * @param string $response
         * @return string
         */
        private function getHtmlFromMarkdown($response)
        {
            $response = preg_replace('/^# (.+)/m', '<h1>$1</h1>', $response);
            $response = preg_replace('/^## (.+)/m', '<h2>$1</h2>', $response);
            $response = preg_replace('/^### (.+)/m', '<h3>$1</h3>', $response);
            $response = preg_replace('/\n\n\* /m', "<ul>\n* ", $response);
            $response = preg_replace('/^\* (.+)\n\n/m', "<li>$1</li>\n</ul>\n", $response);
            $response = preg_replace('/^\* (.+)\n/m', "<li>$1</li>\n", $response);
            $response = preg_replace('/`([^`]+)`/m', '<code>$1</code>', $response);
            $response = str_replace('`', '', $response);
            $response = preg_replace('/\*\*(.+)\*\*/', '<strong>$1</strong>', $response);
            $response = preg_replace('/\*(.+)\*/', '<em>$1</em>', $response);
            return $response;
        }
    }
}
