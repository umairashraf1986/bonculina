<?php
/**
 * DHL Service Point Return (Popular)
 *
 * Only one package per delivery.
 */
return [
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
    'FDNG' => [
        'label' => __um('Limited quantity'),
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