<?php
/**
 *
 */

namespace Mediastrategi\UnifaunOnline;

/**
 *
 */
class Shipment
{
    /**
     * @var [string]    Array of binary contents of labels
     */
    private $labels = array();

    /**
     * @var string
     */
    private $number = '';

    /**
     * @var string    URL to tracking information
     */
    private $trackingLink = '';

    /**
     * @param string $number
     * @param string $trackingLink
     * @param array [$labels = array()]
     * @throws \Exception
     */
    public function __construct($number, $trackingLink, $labels = array())
    {
        if (empty($number)) {
            throw new \Exception('Missing shipment number!');
        }
        if (empty($trackingLink)) {
            throw new \Exception('Missing tracking-link');
        }
        $this->number = $number;
        $this->trackingLink = $trackingLink;
        $this->labels = $labels;
    }

    /**
     * @return [string]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return reset($this->labels);
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getTrackingLink()
    {
        return $this->trackingLink;
    }
}
