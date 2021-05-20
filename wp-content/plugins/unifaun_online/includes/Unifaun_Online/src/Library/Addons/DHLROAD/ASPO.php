<?php
/**
 * DHL Service Point (Popular)
 *
 * Only one package per delivery.
 */
return [
    'OPAY' => [
        'label' => __um('Other payer'),
        'fields' => [
            'custno' => [
                'label' => __um('Customer number'),
                'description' => __um('No default value'),
                'type' => 'text',
                'required' => true,
            ],
        ],
    ],
    'NOT' => [
        'label' => __um('Notification'),
        'fields' => [
            'misc' => [
                'label' => __um('Notification type'),
                'description' => __um('Default from receiver'),
                'type' => 'text',
                'required' => true,
            ],
            'misctype' => [
                'label' => __um('Method'),
                'type' => 'select',
                'options' => ['EMAIL', 'SMS', 'LETTER'],
                'required' => true,
            ],
        ],
    ],
    'FDNG' => [
        'label' => __um('Limited quantity'),
    ],
    'INSU' => [
        'label' => __um('Insurance'),
        'fields' => [
            'amount' => [
                'label' => __um('Amount'),
                'description' => __um('Max value is 10 000 SEK'),
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
    'EVENTRETSP' => [
        'label' => __um('Notification not picked up'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'EVENTOFD' => [
        'label' => __um('Notification on way'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'EVENTTERM' => [
        'label' => __um('Notification in terminal'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'EVENTPUP' => [
        'label' => __um('Notification picked up'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'EVENTDLV' => [
        'label' => __um('Notification delivered'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'EVENTDLVSP' => [
        'label' => __um('Notification delivered at service point'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'GREEN' => [
        'label' => __um('Environmental-friendly'),
    ],
];