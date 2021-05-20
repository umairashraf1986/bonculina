<?php
/**
 * PostNord - Varubrev 1:a klass (popular)
 */
return [
    'NOTLTR' => [
        'label' => __um('Letter notification'),
    ],
    'NOTEMAIL' => [
        'label' => __um('E-mail notification'),
        'fields' => [
            'misc' => [
                'label' => __um('Email address'),
                'type' => 'text',
            ],
        ],
    ],
    'PRENOT' => [
        'label' => __um('Pre-notification'),
        'fields' => [
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver.'),
                'type' => 'text',
                'required' => true,
            ],
        ],
    ],
    'SPBAG' => [
        'label' => __um('Bag on the door'),
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
    'RNOTLTR' => [
        'label' => __um('Letter notification'),
    ],
    'RNOTEMAIL' => [
        'label' => __um('E-mail notification'),
        'fields' => [
            'misc' => [
                'label' => __um('Email address'),
                'type' => 'text',
            ],
        ],
    ],
    'REMAILRECEIPT' => [
        'label' => __um('Email receipt'),
        'fields' => [
            'misc' => [
                'label' => __um('E-mail Address'),
                'type' => 'text',
            ],
        ],
    ],
    'RNOTSMS' => [
        'label' => __um('SMS notification'),
        'fields' => [
            'misc' => [
                'label' => __um('SMS number'),
                'type' => 'text',
            ],
        ],
    ],
];
