<?php
/**
 * Posti - Logistics Oy, Domestic Freight
 */
return [
    'NOTPHONE' => [
        'label' => __um('Call recepient before delivery'),
        'fields' => [
            'misc' => [
                'label' => __um('Phone Number'),
                'type' => 'text',
                'default' => __um('Default from receiver'),
                'required' => true,
            ],
        ],
    ],
    'DNG' => [
        'label' => __um('Limited quantity of dangerous goods'),
        'fields' => [
            'declarant' => [
                'type' => 'text',
                'label' => __um('Declarant'),
            ],
            'documenttype' => [
                'type' => 'text',
                'label' => __um('Document type'),
            ],
            'text1' => [
                'type' => 'text',
                'label' => __um('Handling information 1'),
            ],
            'text2' => [
                'type' => 'text',
                'label' => __um('Handling information 2'),
            ],
            'text3' => [
                'type' => 'text',
                'label' => __um('Handling information 3'),
            ],
            'text4' => [
                'type' => 'text',
                'label' => __um('Handling information 4'),
            ],
            'text5' => [
                'type' => 'text',
                'label' => __um('Calculated quantity'),
            ],
            'text6' => [
                'type' => 'text',
                'label' => __um('Sum calculated quantity'),
            ],
        ],
    ],
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