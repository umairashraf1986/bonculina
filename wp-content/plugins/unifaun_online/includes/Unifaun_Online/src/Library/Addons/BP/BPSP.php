<?php
/**
 * Bring Klimanøytral Servicepakke
 */
return [
    'COD' => [
        'label' => __um('Cash on delivery'),
        'fields' => [
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
    'IDCHK' => [
        'label' => __um('Personal identity check'),
    ],
    'LNKPRTN' => [
        'label' => __um('Link to delivery printing'),
        'fields' => [
            'from' => [
                'label' => __um('From e-mail'),
                'description' => __um('Default from sender'),
                'type' => 'text',
            ],
            'to' => [
                'label' => __um('To e-mail'),
                'description' => __um('Default from receiver'),
                'type' => 'text',
            ],
            'cc' => [
                'label' => __um('Copy e-mail'),
                'type' => 'text',
            ],
            'bcc' => [
                'label' => __um('Blind copy e-mail'),
                'type' => 'text',
            ],
            'message' => [
                'label' => __um('Short message to receiver'),
                'type' => 'textarea',
            ],
            'language' => [
                'label' => __um('Language'),
                'type' => 'select',
                'options' => ['GB', 'SE', 'FI'],
            ],
            'sendemail' => [
                'label' => __um('Send e-mail'),
                'type' => 'checkbox',
                'value' => 'yes',
            ],
        ],
    ],
    'NOTEMAIL' => [
        'label' => __um('E-mail notification'),
        'fields' => [
            'misc' => [
                'label' => __um('Email Address'),
                'type' => 'text',
            ],
        ],
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
    'PERS' => [
        'label' => __um('Direct personal deliveryPersonal identity check'),
    ],
    'PUPOPT' => [
        'label' => __um('Pick-up point'),
    ],
];
