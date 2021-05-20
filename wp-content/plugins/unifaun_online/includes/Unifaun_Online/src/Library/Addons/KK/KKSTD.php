<?php
/**
 * Kaukokiito (KKSTD)
 */
return [
    'CARRYIN' => [
        'label' => __um('Carry in'),
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
    'DONOTSTACK' => [
        'label' => __um('Do not stack'),
    ],
    'HIABDLV' => [
        'label' => __um('Hiab delivery'),
    ],
    'HIABPICKUP' => [
        'label' => __um('HIABPICKUP'),
    ],
    'OPAY' => [
        'label' => __um('Other payer'),
        'fields' => [
            'custno' => [
                'description' => __um('Default value from freight payer if specified.'),
                'label' => __um('Customer number'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'NOTPHONE' => [
        'label' => __um('Phone notification'),
        'fields' => [
            'misc' => [
                'description' => __um('Default from receiver.'),
                'label' => __um('Phone Number'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'RPAY' => [
        'label' => __um('Receiver payer'),
        'fields' => [
            'custno' => [
                'description' => __um('Default value from receiver.'),
                'label' => __um('Customer number'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'DLVTREQ' => [
        'label' => __um('Requested Delivery Date'),
        'fields' => [
            'date' => [
                'label' => __um('Delivery Date'),
                'type' => 'text',
            ],
            'timefrom' => [
                'label' => __um('Delivery Time (from)'),
                'type' => 'text',
            ],
            'timeto' => [
                'label' => __um('Delivery Time (to)'),
                'type' => 'text',
            ],
        ],
    ],
    'TAILLIFT' => [
        'label' => __um('Tail lift'),
    ],
    'WARM' => [
        'label' => __um('Warm transport'),
    ],
];
