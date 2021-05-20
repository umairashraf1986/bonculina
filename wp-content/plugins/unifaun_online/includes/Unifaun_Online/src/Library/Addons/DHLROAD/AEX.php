<?php
/**
 * DHL Paket
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
    'GREEN' => [
        'label' => __um('Environmental-friendly'),
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
    'FDNG' => [
        'label' => __um('Limited quantity'),
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
    'DLVNOPOD' => [
        'label' => __um('Delivery without POD (Proof of Delivery) '),
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
    'DLVIN' => [
        'label' => __um('Indoor delivery '),
        'fields' => [
            'misc' => [
                'label' => __um('Number'),
                'description' => __um('Default from receiver.'),
                'type' => 'text',
                'required' => true,
            ],
            'misctype' => [
                'label' => __um('Method'),
                'description' => __um('Default and the only value is PHONE.'),
                'type' => 'select',
                'options' => ['PHONE'],
                'required' => true,
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
    'NOT' => [
        'label' => __um('Notification'),
        'fields' => [
            'misc' => [
                'label' => __um('Number'),
                'description' => __um('Default from receiver'),
                'type' => 'text',
                'required' => true,
            ],
            'misctype' => [
                'label' => __um('Method'),
                'description' => __um('Default is SMS'),
                'type' => 'select',
                'options' => ['PHONE', 'SMS'],
                'required' => true,
            ],
            'reference' => [
                'label' => __um('Contact person'),
                'description' => __um('Default from receiver'),
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
