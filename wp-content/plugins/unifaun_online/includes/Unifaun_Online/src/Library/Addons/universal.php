<?php
return [
    'ENOT' => [
        'label' => __um('Notification via E-mail'),
        'description' => __um('Requires Unifaun Plus'),
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
            'template' => [
                'label' => __um('Template'),
                'type' => 'text',
            ],
        ],
    ],
];