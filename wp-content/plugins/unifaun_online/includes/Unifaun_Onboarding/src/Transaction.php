<?php
namespace Mediastrategi\UnifaunOnboarding;

class Transaction
{

    /** @var string */
    private $error = '';

    /** @var string */
    private $responseBody = '';

    /** @var array */
    private $responseBodyDecoded = array();

    /** @var int */
    private $responseStatusCode = 0;

    /** @var string */
    private $requestBody = '';

    /** @var string */
    private $requestUri = '';

    /**
     * @param string $error
     * @param string $responseBody
     * @param array $responseBodyDecoded
     * @param int $responseStatusCode
     * @param string $requestBody
     * @param string $requestUri
     * @throws \Exception
     */
    public function __construct(
        $error,
        $responseBody,
        $responseBodyDecoded,
        $responseStatusCode,
        $requestBody,
        $requestUri
    ) {
        if (isset($error)
            && is_string($error)
            && isset($responseBody)
            && is_string($responseBody)
            && isset($responseBodyDecoded)
            && is_array($responseBodyDecoded)
            && !empty($responseStatusCode)
            && is_numeric($responseStatusCode)
            && isset($requestBody)
            && is_string($requestBody)
            && !empty($requestUri)
            && is_string($requestUri)
        ) {
            $this->error = $error;
            $this->responseBody = $responseBody;
            $this->responseBodyDecoded = $responseBodyDecoded;
            $this->responseStatusCode = $responseStatusCode;
            $this->requestBody = $requestBody;
            $this->requestUri = $requestUri;
        } else {
            throw new \Exception(sprintf(
                'Invalid arguments to constructor: %s, arguments: %s',
                __CLASS__,
                print_r(func_get_args(), true)
            ));
        }
    }

    /** @return string */
    public function getError() { return $this->error; }

    /** @return string */
    public function getResponseBody() { return $this->responseBody; }

    /** @return array */
    public function getResponseBodyDecoded() { return $this->responseBodyDecoded; }

    /** @return int */
    public function getResponseStatusCode() { return $this->responseStatusCode; }

    /** @return string */
    public function getRequestBody() { return $this->requestBody; }

    /** @return string */
    public function getRequestUri() { return $this->requestUri; }

    /** @return bool */
    public function hasError() { return !empty($this->error); }

}
