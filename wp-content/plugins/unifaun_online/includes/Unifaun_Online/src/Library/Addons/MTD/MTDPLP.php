<?php
/**
 * MTD Postlådepaket (popular)
 *
 * Only one package per delivery
 */
return [
    'NOTEMAIL' => [
        'label' => __um('E-mail notification'),
        'fields' => [
            'misc' => [
                'label' => __um('Email Address'),
                'type' => 'text',
            ],
        ],
    ],
    'DLVSAT' => [
        'label' => __um('Saturday delivery'),
    ],
    'NOTSMS' => [
        'label' => __um('SMS notification'),
        'fields' => [
            'misc' => [
                'label' => __um('SMS number'),
                'type' => 'text',
            ],
        ],
    ],
];
