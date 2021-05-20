<?php
class Mediastrategi_UnifaunOnline_Session
{

    const AGENT = '_msunifaun_online__agent';
    const AGENT_SERVICE = '_msunifaun_online__agent_service';
    const DOWNLOAD_URL = '_msunifaun_online__download_url';
    const MESSAGE_ERROR = '_msunifaun_online__message_error';
    const MESSAGE_SUCCESS = '_msunifaun_online__message_success';
    const ZIP = '_msunifaun_online__zip';

    /**
     * @var string|bool
     */
    private static $_selectedShippingMethod;

    /**
    * @return array
    */
    public static function getAgents()
    {
        $agents = array();
        if (isset(WC()->session)) {
            if ($methods = WC()->session->get('chosen_shipping_methods')) {
                $packages = array_keys($methods);
                foreach ($packages as $package)
                {
                    $agents[$package] = array(
                        'agent' =>
                            \Mediastrategi_UnifaunOnline_Session::getAgent($package),
                        'service' =>
                            \Mediastrategi_UnifaunOnline_Session::getAgentService($package)
                    );
                }
            }
        }
        return $agents;
        }
    
    /**
     * @param string $package
     */
    public static function clear($package)
    {
        if (isset($_SESSION)) {
            if (isset($_SESSION[self::ZIP])) {
                unset($_SESSION[self::ZIP]);
            }
            if (isset($_SESSION[self::AGENT],
                $_SESSION[self::AGENT][$package])
                && is_array($_SESSION[self::AGENT])
            ) {
                unset($_SESSION[self::AGENT][$package]);
            }
            if (isset($_SESSION[self::AGENT_SERVICE],
                $_SESSION[self::AGENT_SERVICE][$package])
                && is_array($_SESSION[self::AGENT_SERVICE])
            ) {
                unset($_SESSION[self::AGENT_SERVICE][$package]);
            }
        }
    }

    public static function getAgent($package) { return self::get($package, self::AGENT); }
    public static function getAgentService($package) { return self::get($package, self::AGENT_SERVICE); }
    public static function getDownloadUrl($package = null) { return self::get($package, self::DOWNLOAD_URL); }
    public static function getMessageError($package = null) { return self::get($package, self::MESSAGE_ERROR); }
    public static function getMessageSuccess($package = null) { return self::get($package, self::MESSAGE_SUCCESS); }
    public static function getZip() { return self::get(null, self::ZIP); }

    /**
     * @param string $package
     */
    public static function getSelectedShippingMethod($package)
    {
        if (!isset(self::$_selectedShippingMethod[$package])) {
            $chosenMethod = false;
            if (isset(WC()->session)) {
                if ($methods = WC()->session
                    ->get('chosen_shipping_methods')
                ) {
                    if (isset($methods[$package])) {
                        $chosenMethod = $methods[$package];
                    }
                }
            }
            self::$_selectedShippingMethod[$package] = $chosenMethod;
        }
        return self::$_selectedShippingMethod[$package];
    }

    /**
     * @return bool
     */
    public static function isSelected($package)
    {
        $selected = false;
        if ($selectedMethod = self::getSelectedShippingMethod($package)) {
            if ($explode = explode(':', $selectedMethod)) {
                if ($explode[0] ===
                    \Mediastrategi_UnifaunOnline::METHOD_ID
                ) {
                    $selected = true;
                }
            }
        }
        return $selected;
    }

    public static function setAgent($package, $value) { return self::set($package, self::AGENT, $value); }
    public static function setAgentService($package, $value) { return self::set($package, self::AGENT_SERVICE, $value); }
    public static function setDownloadUrl($package, $value) { return self::set($package, self::DOWNLOAD_URL, $value); }
    public static function setMessageError($package, $value) { return self::set($package, self::MESSAGE_ERROR, $value); }
    public static function setMessageSuccess($package, $value) { return self::set($package, self::MESSAGE_SUCCESS, $value); }
    public static function setZip($value) { return self::set(null, self::ZIP, $value); }

    /**
     * @return bool
     */
    public static function isAvailable()
    {
        self::init();
        return isset($_SESSION);
    }

    /**
     * Initialize session if not previously initialized
     */
    private static function init()
    {
        if (!isset($_SESSION)
            && !headers_sent()
        ) {
            session_start();
        }
    }

    /**
     * @param string|null [$package = null]
     * @param string $key
     * @param string [$default = '']
     * @return mixed
     */
    private static function get($package = null, $key, $default = '')
    {
        self::init();
        if (isset($package)) {
            return (isset($_SESSION,
                $_SESSION[$key],
                $_SESSION[$key][$package])
                && is_array($_SESSION[$key])
                && $_SESSION[$key][$package]
                ? $_SESSION[$key][$package]
                : $default);
        } else {
            return (isset($_SESSION, $_SESSION[$key])
                && $_SESSION[$key]
                ? $_SESSION[$key]
                : $default);
        }
    }

    /**
     * @param string|null [$package = null]
     * @param string $key
     * @param mixed $value
     * @param mixed [$unset = '']
     */
    private static function set($package = null, $key, $value, $unset = '')
    {
        self::init();
        if (isset($_SESSION)) {
            if (isset($package)) {
                if (!is_array($_SESSION[$key])) {
                    $_SESSION[$key] = array();
                }
                if ($value === $unset) {
                    if (isset($_SESSION[$key][$package])) {
                        unset($_SESSION[$key][$package]);
                    }
                } else {
                    $_SESSION[$key][$package] = $value;
                }
            } else {
                if ($value === $unset) {
                    if (isset($_SESSION[$key])) {
                        unset($_SESSION[$key]);
                    }
                } else {
                    $_SESSION[$key] = $value;
                }
            }
        }
    }

}
