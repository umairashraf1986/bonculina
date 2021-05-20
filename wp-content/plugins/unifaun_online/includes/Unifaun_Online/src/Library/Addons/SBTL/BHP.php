<?php
/**
 * DB SCHENKERprivpak - Ombund Standard (1 package < 20 kg) (Popular)
 *
 * Only one package per shipment.
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
                'options' => ['BGTXT', 'BGOCR'],
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
                'type' => 'text',
                'required' => true,
            ],
            'text1' => [
                'label' => __um('Phone number'),
                'type' => 'text',
                'required' => true,
            ],
            'text2' => [
                'label' => __um('SMS-number'),
                'type' => 'text',
                'required' => true,
            ],
            'text3' => [
                'label' => __um('Email Address'),
                'type' => 'text',
                'required' => true,
            ],
        ],
    ],
    'RETPP' => [
        'label' => __um('Pre-paid customer return'),
        'fields' => [
            'misc' => [
                'label' => __um('PRINT or STORE pre-paid return label'),
                'type' => 'select',
                'options' => ['PRINT', 'STORE'],
            ],
        ],
    ],
    'NOT' => [
        'label' => __um('Notification'),
        'fields' => [
            'reference' => [
                'label' => __um('Contact person'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text1' => [
                'label' => __um('Phone number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text2' => [
                'label' => __um('SMS-number'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
            'text3' => [
                'label' => __um('Email Address'),
                'description' => __um('Default from receiver'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'INSU' => [
        'label' => __um('Insurance'),
        'fields' => [
            'amount' => [
                'label' => __um('Amount, mandatory for non domestic shipments'),
                'type' => 'text',
                'required' => true,
            ],
            'unit' => [
                'label' => __um('Currency'),
                'type' => 'text',
                'required' => true,
            ],
        ],
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
];