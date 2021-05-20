<?php
/**
 * PostNord MyPack Collect (Popular)
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
    'INSU' => [
        'label' => __um('Insurance'),
        'fields' => [
            'amount' => [
                'label' => __um('Amount'),
                'type' => 'text',
                'required' => true,
            ],
            'unit' => [
                'label' => __um('Currency'),
                'type' => 'select',
                'options' => ['SEK'],
                'required' => true,
            ],
            'confirmation' => [
                'label' => __um('I want to receive a proof of insurance to the following e-mail address'),
                'type' => 'text',
                'required' => false,
            ],
        ],
    ],
    'RETNEXT' => [
        'label' => __um('Extended Period of Retention'),
    ],
    'DLV' => [
        'label' => __um('Delivery'),
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
    'NOTLTR' => [
        'label' => __um('Letter notification'),
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
    'PUPOPT' => [
        'label' => __um('Optional Service Point'),
    ],
    'PRENOT' => [
        'label' => __um('Pre-notification'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'type' => 'text',
                'required' => true,
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver.'),
                'type' => 'text',
                'required' => true,
            ],
        ],
    ],
    'RETN' => [
        'label' => __um('Reduced Period of Retention'),
    ],
    'RPODNOT' => [
        'label' => __um('Pre-notification'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS number'),
                'description' => __um('Default from receiver'),
                'type' => 'text',
                'required' => true,
            ],
            'text4' => [
                'label' => __um('E-mail address'),
                'description' => __um('Default from receiver.'),
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
    'LNKPRTR' => [
        'label' => __um('Link to return printing'),
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
