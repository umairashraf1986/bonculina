<?php
/**
 *
 */

namespace Mediastrategi\UnifaunOnline;

/** \Mediastrategi\UnifaunOnline\Shipment */
require_once('Shipment.php');

/**
 *
 */
class Rest
{

    /**
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * @internal
     * @var array
     */
    private $_configuration = array();

    /**
     * @internal
     * @var array|bool
     */
    private $_lastDecodedResponse = false;

    /**
     * @internal
     * @var string
     */
    private $_lastErrorMessage = '';

    /**
     * @internal
     * @var array
     */
    private $_lastResponse = '';

    /**
     * @internal
     * @var int
     */
    private $_lastResponseCode = 0;

    /**
     * @internal
     * @var string
     */
    private $_lastRequest = '';

    /**
     * @var string
     */
    private $_lastRequestUri = '';

    /**
     * @internal
     * @var string
     */
    private $_lastTrackingUrl = '';

    /**
     * @internal
     * @var [\Mediastrategi\UnifaunOnline\Shipment]
     */
    private $_lastShipments = array();

    /**
     * @param array $configuration
     * @throws \Exception
     */
    public function __construct($configuration)
    {
        if (!empty($configuration)) {
            if (!empty($configuration['uri'])
                && !empty($configuration['username'])
                && !empty($configuration['user_id'])
                && !empty($configuration['password'])
            ) {
                $this->setConfiguration($configuration);
            } else {
                throw new \Exception(sprintf(
                    'Configuration is missing uri, username, user-id or password in %s',
                    __METHOD__
                ));
            }
        } else {
            throw new \Exception(sprintf(
                'Missing configuration in %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->_configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getLastTrackingUrl()
    {
        return (!empty($this->_lastShipments)
            ? reset($this->_lastShipments)->getTrackingLink()
            : '');
    }

    /**
     * @return string
     */
    public function getLastShipmentNumber()
    {
        return (!empty($this->_lastShipments)
            ? reset($this->_lastShipments)->getNumber()
            : '');
    }

    /**
     * @return string
     */
    public function getLastShippingLabel()
    {
        return (!empty($this->_lastShipments)
            ? reset($this->_lastShipments)->getLabel()
            : '');
    }

    /**
     * @return array
     */
    public function getLastAdditionalShippingLabels()
    {
        if (!empty($this->_lastShipments)) {
            if ($labels = reset($this->_lastShipments)->getLabels()) {
                $additionalLabels = array();
                $i = 0;
                foreach ($labels as $label) {
                    if ($i) {
                        $additionalLabels[] = $label;
                    }
                    $i++;
                }
                if ($additionalLabels) {
                    return $additionalLabels;
                }

            }
        }
        return array();
    }

    /**
     * @return string
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }

    /**
     * @return string
     */
    public function getLastRequest()
    {
        return $this->_lastRequest;
    }

    /**
     * @return string
     */
    public function getLastRequestUri()
    {
        return $this->_lastRequestUri;
    }

    /**
     * @return [\Mediastrategi\UnifaunOnline\Shipment]
     */
    public function getLastShipments()
    {
        return $this->_lastShipments;
    }

    /**
     * @return array|bool
     */
    public function getLastDecodedResponse()
    {
        return $this->_lastDecodedResponse;
    }

    /**
     * @return int
     */
    public function getLastResponseCode()
    {
        return $this->_lastResponseCode;
    }

    /**
     * @return string
     */
    public function getLastErrorMessage()
    {
        return $this->_lastErrorMessage;
    }

    /**
     * Create a stored shipment.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function storedShipmentsPost($data)
    {
        if (!empty($data)) {
            if ($this->request(
                'stored-shipments',
                $data,
                self::METHOD_POST
            )) {
                if ($decodedResponse = $this->getLastDecodedResponse()) {
                    if (!empty($decodedResponse['status'])
                        && strtolower($decodedResponse['status']) != 'invalid'
                    ) {
                        return true;

                    } elseif (!empty($decodedResponse[0])
                        && !empty($decodedResponse[0]['status'])
                        && strtolower($decodedResponse[0]['status']) != 'invalid'
                    ) {
                        return true;

                    }

                }
            }
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
        return false;
    }

    /**
     * Created live shipment.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function shipmentsPost($data)
    {
        if (!empty($data)) {
            return $this->request(
                'shipments',
                $data,
                self::METHOD_POST
            );
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param string $shipmentId
     * @param string $pdfId
     * @return bool
     * @throws \Exception
     */
    public function shipmentsPdfs($shipmentId, $pdfId)
    {
        if (!empty($shipmentId)
            && !empty($pdfId)
        ) {
            return $this->request(
                sprintf(
                    'shipments/%s/pdfs/%s',
                    $shipmentId,
                    $pdfId
                )
            );
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param string $shipmentId
     * @return bool
     * @throws \Exception
     */
    public function shipmentPdfs($shipmentId)
    {
        if (!empty($shipmentId)) {
            return $this->request(
                sprintf(
                    'shipments/%s/pdfs',
                    $shipmentId
                )
            );
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param string $shipmentId
     * @return bool
     * @throws \Exception
     */
    public function storedShipmentsDelete($shipmentId)
    {
        if (!empty($shipmentId)) {
            return $this->request(
                sprintf(
                    'stored-shipments/%s',
                    $shipmentId
                ),
                null,
                self::METHOD_DELETE
            );
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param string $shipmentId
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function storedShipmentsShipmentsPost($shipmentId, $data)
    {
        if (!empty($shipmentId)
            && !empty($data)
        ) {
            return $this->request(
                sprintf(
                    'stored-shipments/%s/shipments',
                    $shipmentId
                ),
                $data,
                self::METHOD_POST
            );
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param string $shipmentId
     * @return bool
     * @throws \Exception
     */
    public function shipmentsDelete($shipmentId)
    {
        if (!empty($shipmentId)) {
            return $this->request(
                sprintf(
                    'shipments/%s',
                    $shipmentId
                ),
                null,
                self::METHOD_DELETE
            );
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function addressesAgentsGet($data)
    {
        if (!empty($data)) {
            return $this->request(
                'addresses/agents',
                $data
            );
        } else {
            throw new \Exception(sprintf(
                'Missing argument data for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @return mixed
     */
    public function metaListsPartnersGet()
    {
        return $this->request(
            'meta/lists/partners'
        );
    }

    /**
     * @param string $response
     * @throws \Exception
     */
    public function decodeResponse($response)
    {
        if (!empty($response)) {
            try {
                $decodedResponse = json_decode($response, true);
                if (is_array($decodedResponse)
                    && !empty($decodedResponse)
                    && !isset($decodedResponse[0])
                ) {
                    $decodedResponse = array($decodedResponse);
                }
                return $decodedResponse;
            } catch (\Exception $e) {
                throw new \Exception(sprintf(
                    'Response contained invalid JSON "%s" in %s',
                    $response,
                    __METHOD__
                ));
            }
        }
        return '';
    }

    /**
     * @return bool
     */
    public function isPacsoft()
    {
        return !empty($this->_configuration)
            && !empty($this->_configuration['pacsoft']);
    }

    /**
     * @param string $receiverCountry
     * @param string $userId
     * @param string $reference
     * @return string
     * @throws \Exception
     */
    public function getTrackingLink($receiverCountry, $userId, $reference)
    {
        if (!empty($receiverCountry)
            && !empty($userId)
            && !empty($reference)
        ) {
            $prefix = '';
            if ($this->isPacsoft()) {
                $prefix = 'https://www.pacsoftonline.com/ext.po.en.gb.track';
                if ($receiverCountry == 'FI') {
                    $prefix = 'https://www.pacsoftonline.com/ext.po.fi.fi.track';
                } elseif ($receiverCountry == 'DK') {
                    $prefix = 'https://www.pacsoftonline.com/ext.po.dk.dk.track';
                } elseif ($receiverCountry == 'SE') {
                    $prefix = 'https://www.pacsoftonline.com/ext.po.se.se.track';
                }
            } else {
                $prefix = 'https://www.unifaunonline.com/ext.uo.en.gb.track';
                if ($receiverCountry == 'FI') {
                    $prefix = 'https://www.unifaunonline.com/ext.uo.fi.fi.track';
                } elseif ($receiverCountry == 'DK') {
                    $prefix = 'https://www.unifaunonline.com/ext.uo.dk.dk.track';
                } elseif ($receiverCountry == 'SE') {
                    $prefix = 'https://www.unifaunonline.com/ext.uo.se.se.track';
                }
            }
            return sprintf(
                '%s?key=%s&reference=%s',
                $prefix,
                urlencode($userId),
                urlencode($reference)
            );
        } else {
            throw new \Exception(sprintf(
                'Missing receiver-country, user-id or reference for %s',
                __METHOD__
            ));
        }
    }

    /**
     * @param string $url
     * @param array [$contents = array()]
     * @param string [$method = self::METHOD_GET]
     * @return bool
     * @throws \Exception
     */
    public function request($url, $contents = array(), $method = self::METHOD_GET)
    {
		$t=time();
		$rand = rand(10,100);
        $url = $this->_configuration['uri'] . $url;
        if (!isset($method)) {
            $method = self::METHOD_GET;
        }
        if (!empty($contents)
            && $method === self::METHOD_GET
        ) {
            $url .= '?';
            $getIndex = 0;
			update_option('custom_unifaun_content_array_'.$t.$rand,$contents);
            foreach ($contents as $key => $value)
            {
                if ($getIndex) {
                    $url .= '&';
                }
                $url .= urlencode($key) . '=' . urlencode($value);
                $getIndex++;
            }
        }
		else{
			if(isset($contents['pdfConfig'])){
				unset($contents['pdfConfig']);
				$contents['printConfig'] = [];
				$contents['printConfig']['target1Media'] = 'thermo-250';
				$contents['printConfig']['target1Type'] = 'zpl';
				$contents['printConfig']['target1YOffset'] = 0;
				$contents['printConfig']['target1XOffset'] = 0;
				$contents['printConfig']['target1Options'][0]['key'] = 'mode';
				$contents['printConfig']['target1Options'][0]['value'] = 'DT';
				$contents['printConfig']['target2Media'] = 'laser-a4';
				$contents['printConfig']['target2Type'] = 'pdf';
				$contents['printConfig']['target2YOffset'] = 0;
				$contents['printConfig']['target2XOffset'] = 0;
				$contents['printConfig']['target3Media'] = null;
				$contents['printConfig']['target3Type'] = 'pdf';
				$contents['printConfig']['target3YOffset'] = 0;
				$contents['printConfig']['target3XOffset'] = 0;
				$contents['printConfig']['target4Media'] = null;
				$contents['printConfig']['target4Type'] = 'pdf';
				$contents['printConfig']['target4YOffset'] = 0;
				$contents['printConfig']['target4XOffset'] = 0;
			}
		}
        $this->_lastRequestUri = $url;
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_USERPWD,
            sprintf(
                '%s:%s',
                $this->_configuration['username'],
                $this->_configuration['password']
            )
        );
        curl_setopt(
            $ch,
            CURLOPT_URL,
            $url
        );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $this->_lastRequest = '';
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
			//update_option('custom_unifaun_content_array_encode_'.$t.$rand,$contents);
			//update_option('custom_unifaun_username_'.$t.$rand,$this->_configuration['username']);
			//update_option('custom_unifaun_pwd_'.$t.$rand,$this->_configuration['password']);
            $request = json_encode($contents);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                $request
            );
            $this->_lastRequest = $request;
        }
        curl_setopt(
            $ch,
            CURLOPT_RETURNTRANSFER,
            true
        );
		
		//update_option('custom_unifaun_url_'.$t.$rand,$url);
		//update_option('custom_unifaun_content_'.$t.$rand,$request);
        // Reset fields
        $this->_lastDecodedResponse = false;
        $this->_lastShipments = array();

        try {
            $response = curl_exec($ch);
            $this->_lastResponse = $response;
            $responseCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );
            $this->_lastResponseCode = $responseCode;
            $errorMessage = curl_error($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            $this->_lastResponse = $response;
            $responseCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );
            $this->_lastResponseCode = $responseCode;
            $this->_lastErrorMessage = sprintf(
                'Curl Exception - message: "%s", response-code: %d, response: "%s"',
                $e->getMessage(),
                $responseCode,
                $response
            );
            return false;
        }
        $this->_lastDecodedResponse = $this->decodeResponse($response);
		//update_option('custom_unifaun_response_'.$t.$rand,$this->_lastDecodedResponse);
        // Successful request?
        if ($responseCode === 201
            || $responseCode === 200
            || $responseCode === 204
        ) {

            // Did we get a response?
            if (!empty($this->_lastDecodedResponse)
                && is_array($this->_lastDecodedResponse)
            ) {
                foreach ($this->_lastDecodedResponse as $shipment)
                {
                    // Collect labels - pdf
                    /*$labels = array();
                    if (!empty($shipment['pdfs'])
                        && is_array($shipment['pdfs'])
                    ) {
                        foreach ($shipment['pdfs'] as $pdf)
                        {
                            if (!empty($pdf['pdf'])) {
                                $labels[] = $pdf['pdf'];
                            } else if (!empty($pdf['href'])) {
                                $labels[] = $this->getRemoteFile($pdf['href']);
                            }
                        }
                    }*/
					
					// Collect labels - zpl
                    $labels = array();
                    if (!empty($shipment['prints'])
                        && is_array($shipment['prints'])
                    ) {
                        foreach ($shipment['prints'] as $zpl)
                        {
                            if (!empty($zpl['data'])) {
                                $labels[] = $zpl['data'];
                            } else if (!empty($zpl['href'])) {
                                $labels[] = $this->getRemoteFile($zpl['href']);
                            }
                        }
                    }
					
                    // Get shipment number
                    $shipmentNumber = '';
                    if (!empty($shipment['id'])) {
                        $shipmentNumber = $shipment['id'];
                    }

                    // Tracking link
                    $trackingLink = '';
                    if (!empty($shipment['rcvCountry'])
                        && !empty($shipment['reference'])
                    ) {
                        $trackingLink = $this->getTrackingLink(
                            $shipment['rcvCountry'],
                            $this->_configuration['user_id'],
                            $shipment['reference']
                        );
                    }

                    // Add shipment to list if we found shipment-number and tracking link
                    if (!empty($shipmentNumber)
                        && !empty($trackingLink)
                    ) {
                        $this->_lastShipments[] = new Shipment(
                            $shipmentNumber,
                            $trackingLink,
                            $labels
                        );
                    }
                }
            }

            return true;
        } else {
            $error = '';
            if ($responseCode === 403) {
                $error = 'Authentication failed: Invalid user credentials or Missing user credentials';
            } else if ($responseCode === 404) {
                $error = 'Resource Not Found';
            } else if ($responseCode === 406) {
                $error = 'Conflict: Contents is empty';
            } else if ($responseCode === 500) {
                $error = 'Internal server error';
            } else if ($responseCode == 503) {
                $error = 'No matching entry found or server not available';
            } else  {
                $error = 'Request failed';
            }
            $error .= sprintf(
                ' (%s) - response: "%s", request: "%s", url: "%s", method: "%s"',
                $responseCode,
                $response,
                $this->_lastRequest,
                $url,
                $method
            );
            $error .= sprintf(', error: "%s"', $errorMessage);
            $this->_lastErrorMessage = $error;
            throw new \Exception($error);
        }
        return false;
    }

    /**
     * @param string $url
     * @return string|bool
     * @throws \Exception
     */
    public function getRemoteFile($url)
    {
        if (!empty($url)) {
            $ch = curl_init();
            curl_setopt(
                $ch,
                CURLOPT_USERPWD,
                sprintf(
                    '%s:%s',
                    $this->_configuration['username'],
                    $this->_configuration['password']
                )
            );
            curl_setopt(
                $ch,
                CURLOPT_URL,
                $url
            );
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );

            $exception = false;
            try {
                $response = curl_exec($ch);
            } catch (\Exception $e) {
                $exception = $e->getMessage();
                $response = false;
            }

            $responseCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );
            $errorMessage = curl_error($ch);
            curl_close($ch);

            if ($responseCode == 200
                && !empty($response)
            ) {
                return $response;
            } else {
                throw new \Exception(sprintf(
                    'Failed to download %s (%s), exception: %s, message: %s',
                    $url,
                    $responseCode,
                    $exception,
                    $errorMessage
                ));
            }
        } else {
            throw new \Exception(sprintf(
                'Missing url argument for %s',
                __METHOD__
            ));
        }
        return false;
    }

}
