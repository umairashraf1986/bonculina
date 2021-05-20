<?php
/**
 * Bring Mail Pakke i postkassen med sporing
 */
return [
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
    'SPXL' => [
        'label' => __um('Formattillegg'),
    ],
];
