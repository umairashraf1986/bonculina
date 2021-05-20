<?php
/**
 * PostNord MyPack Return (P24) - PostNord Return Drop Off (Popular)
 */
return [
    'PODNOT' => [
        'label' => __um('Confirmation of Delivery'),
        'fields' => [
            'text3' => [
                'label' => __um('SMS-number'),
                'required' => true,
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('Email Address'),
                'required' => true,
                'type' => 'text',
            ],
        ],
    ],
    'INSU' => [
        'label' => __um('Insurance'),
        'fields' => [
            'amount' => [
                'label' => __um('Amount'),
                'required' => true,
                'type' => 'text',
            ],
            'unit' => [
                'label' => __um('Currency'),
                'required' => true,
                'type' => 'select',
                'options' => ['SEK'],
            ],
            'confirmation' => [
                'label' => __um('I want to receive a proof of insurance to the following e-mail address'),
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