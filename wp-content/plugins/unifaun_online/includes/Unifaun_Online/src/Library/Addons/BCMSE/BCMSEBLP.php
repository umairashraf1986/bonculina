<?php
/**
 * Bring Citymail Brevlådepaket (popular)
 */
return [
    'SPBAG' => [
        'label' => __um('Bag on the door'),
    ],
    'LNKPRTN' => [
        'label' => __um('Link to delivery printing'),
        'fields' => [
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
                'label' => __um('Language of e-mail support GB, SE and FI'),
                'type' => 'text',
            ],
            'sendemail' => [
                'label' => __um('Send e-mail'),
                'type' => 'checkbox',
                'value' => 'yes',
            ],
        ],
    ],
];
