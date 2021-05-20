<?php
/**
 * Bring PickUp Parcel (popular)
 */
return [
    'FDNG' => [
        'label' => __um('Limited quantity'),
    ],
    'COD' => [
        'label' => __um('Cash on delivery'),
        'fields' => [
            'account' => [
                'label' => __um('Account'),
                'type' => 'text',
            ],
            'bank' => [
                'label' => __um('Bank'),
                'type' => 'text',
            ],
            'accounttype' => [
                'label' => __um('Account type'),
                'type' => 'select',
                'options' => ['PGOCR', 'PGTXT', 'ACCDKOCR', 'ACCDKTXT', 'ACCNO', 'ACCOOCR', 'IBAN', 'ACCIS'],
            ],
            'amount' => [
                'label' => __um('Amount'),
                'type' => 'text',
            ],
            'reference' => [
                'label' => __um('Reference'),
                'description' => __um('There are different rules if the account typeis OCR type'),
                'type' => 'text',
            ],
            'unit' => [
                'label' => __um('Currency'),
                'type' => 'select',
                'options' => ['SEK', 'DDK', 'NOK', 'EUR', 'ISK'],
            ],
        ],
    ],
    'DLVFLEX' => [
        'label' => __um('Flex delivery'),
    ],
    'BOX' => [
        'label' => __um('PickUp Locker'),
    ],
    'PUPOPT' => [
        'label' => __um('Custom collection point'),
    ],
    'NOT' => [
        'label' => __um('Notification'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS Number'),
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('Email Address'),
                'description' => __um('Only to Norway (NO), Sweden (SE) and Denmark (DK)'),
                'type' => 'text',
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