<?php
/**
 *
 */

/**
 *
 */
class Mediastrategi_UnifaunOnline_ApiCache
{

    /**
     * @var int
     */
    const CACHE_EXPIRATION = 604800;

    /**
     * @var int
     */
    const INDEX_EXPIRATION = 0;

    /**
     * @var int
     */
    const INDEX_DATA = 1;

    /**
     * @static
     * @var bool
     */
    private static $debug = false;

    /**
     * @static
     * @var Mediastrategi_UnifaunOnline_ApiCache
     */
    private static $instance = null;

    /**
     *
     */
    public function __construct()
    {
        $this->getCacheDirectory();
    }

    /**
     * @static
     * @return Mediastrategi_UnifaunOnline_ApiCache
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param mixed $data
     * @param string $key
     * @param int|null [$expiration = null]
     * @return bool
     */
    public function save(
        $data,
        $key,
        $expiration = null
    ) {
      if (!isset($expiration)) {
        $expiration = self::CACHE_EXPIRATION;
      }
        $path = $this->getCachePath($key);
        try {
            $write = array();
            $write[self::INDEX_EXPIRATION] = (time() + $expiration) . "\n";
            $write[self::INDEX_DATA] = json_encode($data) . "\n";
            file_put_contents(
                $path,
                $write
            );
            $this->log(sprintf(
                'Saved cache for key "%s" into "%s"',
                $key,
                $path
            ));
            return true;
        } catch (Exception $e) {
            $this->log(sprintf(
                'Failed to save cache, error: "%s"',
                $e->getMessage()
            ));
        }
        return false;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function test($key)
    {
        if ($data = $this->loadKey($key)) {
            if ($data[self::INDEX_EXPIRATION] > time()) {
                $this->log(sprintf(
                    '%s cache hit for key "%s"',
                    date('Y-m-d H:i:s'),
                    $key
                ));
                return true;
            } else {
                $this->log(sprintf(
                    'Expiration "%s" has expired because time is "%s", expired: %s',
                    $data[self::INDEX_EXPIRATION],
                    time(),
                    $data[self::INDEX_EXPIRATION] <= time()
                ));
            }
        }
        return false;
    }

    /**
     * @param string $key
     * @return array|bool
     */
    public function load($key)
    {
        if ($data = $this->loadKey($key)) {
            return $data[self::INDEX_DATA];
        }
        return false;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getCachePath($key)
    {
        return $this->getCacheDirectory() . '/' . $key;
    }

    /**
     * @return string
     */
    private function getCacheDirectory()
    {
        $uploadDir = wp_upload_dir()['basedir'] . '/msunifaunonline';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir);
        }
        return $uploadDir;
    }

    /**
     * @param string $key
     * @return bool|array
     * @throws Exception
     */
    private function loadKey($key)
    {
        $path = $this->getCachePath($key);
        if (file_exists($path)) {
            try {
                if ($handle = fopen($path, "r")) {
                    $data = array();
                    $data[self::INDEX_EXPIRATION] = (int) trim(fgets($handle));
                    $data[self::INDEX_DATA] = json_decode(trim(fgets($handle)), true);
                    fclose($handle);
                    unset($handle);
                    return $data;
                }
            } catch (Exception $e) {
                $this->log(sprintf(
                    'Failed to load cache with error: "%s"',
                    $e->getMessage()
                ));
            }
        }
        return false;
    }

    /**
     * @param string $message
     */
    private function log($message)
    {
        if (self::$debug) {
            error_log(sprintf(
                '%s - %s - %s',
                date('Y-m-d H:i:s'),
                __CLASS__,
                $message
            ));
        }
    }

}
