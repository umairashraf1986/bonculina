<?php
/**
 *
 */
namespace Mediastrategi\UnifaunOnline\Libraries\Carrier;

/**
 *
 */
class PLAB
{

    /**
     * @param array $shipment
     * @param array $agent
     * @param array $addons
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codingStandardsIgnoreStart
     */
    public function apply(& $shipment, $agent, $addons)
    {
        /* @codingStandardsIgnoreEnd */
        // Do we have a agent quick-id specified?
        if (isset($shipment['shipment']['agent']['quickId'])) {
            if (!isset($shipment['shipment']['service']['addons'])) {
                $shipment['shipment']['service']['addons'] = [];
            }

            // Add PUPOPT to add-ons if it's missing
            $found = false;
            foreach ($shipment['shipment']['service']['addons'] as $addon) {
                if (!empty($addon['id'])
                    && $addon['id'] == 'PUPOPT'
                ) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $shipment['shipment']['service']['addons'][] = [
                    'id' => 'PUPOPT',
                ];
            }

        } else if (isset($shipment['shipment']['service']['addons'])) {
            // Remove PUPOPT from add-ons
            $newAddons = array();
            foreach ($shipment['shipment']['service']['addons'] as $addon) {
                if (empty($addon['id'])
                    || $addon['id'] != 'PUPOPT'
                ) {
                    $newAddons[] = $addon;
                }
            }
            $shipment['shipment']['service']['addons'] = $newAddons;
            if (!count($newAddons)) {
                unset($shipment['shipment']['service']['addons']);
            }
        }
    }
}
