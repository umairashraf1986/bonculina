<?php
/**
 *
 */

namespace Mediastrategi\UnifaunOnboarding;

require_once(dirname(__FILE__) . '/Transaction.php');

/**
 *
 */
class Rest
{

    /** @var string */
    const METHOD_DELETE = 'DELETE';

    /** @var string */
    const METHOD_GET = 'GET';

    /** @var string */
    const METHOD_POST = 'POST';

    /** @var string */
    private $language = '';

    /** @var array */
    private $configuration = array();

    /**
     * @param array $language
     * @throws \Exception
     */
    public function __construct($configuration)
    {
        if (!empty($configuration)
            && is_array($configuration)
            && !empty($configuration['language'])
            && is_string($configuration['language'])
            && !empty($configuration['password'])
            && is_string($configuration['password'])
            && !empty($configuration['url'])
            && is_string($configuration['url'])
            && !empty($configuration['userid'])
            && is_string($configuration['userid'])
            && !empty($configuration['username'])
            && is_string($configuration['username'])
        ) {
            $this->configuration = $configuration;
        } else {
            throw new \Exception(sprintf(
                'Invalid arguments to constructor in %s, args: %s',
                __CLASS__,
                print_r(
                    func_get_args(),
                    true
                )
            ));
        }
    }

    /**
     * @return bool
     */
    public function termsGet()
    {
        return $this->request(
            sprintf(
                'onboarding/terms/%s',
                $this->configuration['language']
            )
        );
    }

    /**
     * @return bool
     */
    public function applicationsGet()
    {
        return $this->request(
            sprintf(
                'onboarding/applications/%s/',
                $this->configuration['language']
            )
        );
    }

    /**
     * @param string $applicationCode
     * @param array $body
     * @return bool
     * @throws \Exception
     */
    public function applicationsPost($applicationCode, $body)
    {
        if (!empty($applicationCode)
            && is_string($applicationCode)
            && !empty($body)
            && is_array($body)
        ) {
            return $this->request(
                sprintf(
                    'onboarding/applications/%s/',
                    $applicationCode
                ),
                $body,
                self::METHOD_POST
            );
        } else {
            throw new \Exception(sprintf(
                'Invalid arguments to method %s, args: %s',
                __METHOD__,
                print_r(
                    func_get_args(),
                    true
                )
            ));
        }
    }

    /**
     * @param array $args
     * @return bool
     * @throws \Exception
     */
    public function accountsGet($args)
    {
        if (!empty($args)
            && is_array($args)
            && ((!empty($args['orgNo'])
                && is_string($args['orgNo']))
                || ((!empty($args['userId'])
                    && is_string($args['userId']))))
        ) {
            $newArgs = array();
            if (!empty($args['orgNo'])
                && is_string($args['orgNo'])
            ) {
                $newArgs['orgNo'] = $args['orgNo'];
            }
            if (!empty($args['userId'])
                && is_string($args['userId'])
            ) {
                $newArgs['userId'] = $args['userId'];
            }
            return $this->request(
                'onboarding/accounts/',
                $newArgs
            );
        } else {
            throw new \Exception(sprintf(
                'Invalid arguments to method: %s, args: %s',
                __METHOD__,
                print_r(
                    func_get_args(),
                    true
                )
            ));
        }
    }

    /**
     * @param string $url
     * @param array [$contents = array()]
     * @param string [$method = self::METHOD_GET]
     * @return Transaction
     * @throws \Exception
     */
    private function request(
        $url,
        $contents = array(),
        $method = self::METHOD_GET
    ) {
        $url = sprintf(
            '%s%s',
            $this->configuration['url'],
            $url

        );
        if (!isset($method)) {
            $method = self::METHOD_GET;
        }
        if (!empty($contents)
            && $method === self::METHOD_GET
        ) {
            $url .= '?';
            $getIndex = 0;
            foreach ($contents as $key => $value)
            {
                if ($getIndex) {
                    $url .= '&';
                }
                $url .= urlencode($key) . '=' . urlencode($value);
                $getIndex++;
            }
        }
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_USERPWD,
            sprintf(
                '%s:%s',
                $this->configuration['username'],
                $this->configuration['password']
            )
        );
        curl_setopt(
            $ch,
            CURLOPT_URL,
            $url
        );
        $request = '';
        if ($method === self::METHOD_POST) {
            curl_setopt(
                $ch,
                CURLOPT_POST,
                true
            );
        }
        if ($method === self::METHOD_DELETE) {
            curl_setopt(
                $ch,
                CURLOPT_CUSTOMREQUEST,
                'DELETE'
            );
        }
        if (!empty($contents)
            && ($method === self::METHOD_POST
                || $method === self::METHOD_DELETE)
        ) {
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array('Content-Type: application/json')
            );
            $request = json_encode($contents);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                $request
            );
        }
        curl_setopt(
            $ch,
            CURLOPT_RETURNTRANSFER,
            true
        );
        $error = '';

        try {
            $response = curl_exec($ch);
            $responseCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );
            $errorMessage = curl_error($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            $responseCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );
            $error = sprintf(
                'Curl Exception - message: "%s", response-code: %d, response: "%s"',
                $e->getMessage(),
                $responseCode,
                $response
            );
        }
        $decodedResponse = array();
        try {
            $tryToDecodedResponse = json_decode(
                $response,
                true
            );
            if (isset($tryToDecodedResponse)) {
                $decodedResponse = $tryToDecodedResponse;
            }
        } catch (\Exception $e) {
            $decodedResponse = array();
        }

        return new Transaction(
            $error,
            $response,
            $decodedResponse,
            $responseCode,
            $request,
            $url
        );
    }

}
