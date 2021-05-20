<?php
/**
 * Posti - Freight
 */
return [
    'COD' => [
        'label' => __um('Cash on delivery'),
        'fields' => [
            'bank' => [
                'label' => __um('Bank'),
                'type' => 'text',
            ],
            'account' => [
                'label' => __um('Account'),
                'description' => __um('Default from sender'),
                'type' => 'text',
                'required' => true,
            ],
            'accounttype' => [
                'label' => __um('Account type'),
                'type' => 'select',
                'options' => ['BGTXT', 'BGOCR', 'PGTXT', 'PGOCR'],
                'required' => true,
            ],
            'amount' => [
                'label' => __um('Amount'),
                'type' => 'text',
                'required' => true,
            ],
            'reference' => [
                'label' => __um('Reference'),
                'type' => 'text',
                'required' => true,
            ],
            'unit' => [
                'label' => __um('Currency'),
                'type' => 'select',
                'options' => ['SEK'],
                'required' => true,
            ],
        ],
    ],
    'DLVCALL' => [
        'label' => __um('Call before Delivery'),
        'fields' => [
            'misc' => [
                'label' => __um('Phone Number'),
                'type' => 'text',
                'default' => __um('Default from receiver'),
                'required' => true,
            ],
        ],
    ],
    'DLVPRIV' => [
        'label' => __um('Consumer Delivery'),
        'fields' => [
            'misc' => [
                'description' => __um('Default from receiver'),
                'label' => __um('Number'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'TECH' => [
        'label' => __um('Crane Delivery Service'),
    ],
    'DLVDEP' => [
        'label' => __um('Delivery to Terminal'),
    ],
    'WARM' => [
        'label' => __um('Heated Transport'),
    ],
    'SPTR' => [
        'label' => __um('Long shipment'),
    ],
    'PUPDEP' => [
        'label' => __um('Pick-up from Terminal'),
        'fields' => [
            'misc' => [
                'description' => __um('Default from receiver'),
                'label' => __um('Number'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'DLVT' => [
        'label' => __um('Scheduled Delivery'),
        'fields' => [
            'misc' => [
                'description' => __um('Time windows for delivery: 7 (07-09), 9 (09-12), 12 (12-14) and 14 (14-16)'),
                'label' => __um('Time slot'),
                'type' => 'text',
            ],
        ],
    ],
    'DNG' => [
        'label' => __um('Dangerous goods'),
        'fields' => [
            'declarant' => [
                'label' => __um('Declarant'),
                'type' => 'text',
            ],
            'documenttype' => [
                'description' => __um('Default value ROAD. Possible values ROAD.'),
                'label' => __um('Document type'),
                'type' => 'text',
                'required' => true,
            ],
            'text1' => [
                'label' => __um('Handling information 1.'),
                'type' => 'text',
            ],
            'text2' => [
                'label' => __um('Handling information.'),
                'type' => 'text',
            ],
            'text3' => [
                'label' => __um('Handling information.'),
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('Handling information.'),
                'type' => 'text',
            ],
            'text5' => [
                'label' => __um('Calculated value'),
                'type' => 'text',
            ],
            'text6' => [
                'label' => __um('Handling information.'),
                'type' => 'text',
            ],
        ],
    ],
];
