<?php
/** THIS FILE IS DEPRECATED, move all specifications to specific files like '/Addons/$CarrierId/$ServiceId.php'

/**
 * Specification for all unifoun addons in alphabetical order ascending. All text should be in english for translation-support.
 *
 * Format:
 * [$addon => ['label', 'fields' => [$name => ['label', 'type']]]]
 */
return [
    'AGECHK' => [
        'label' => __um('Age check'),
        'fields' => [
            'misc' => [
                'label' => __um('Age, possible values are 16, 18 and 20'),
                'type' => 'text',
            ],
        ],
    ],
    'AIR' => [
        'label' => __um('Domestic air'),
    ],
    'BSPLIT' => [
        'label' => __um('Spread freight'), // TODO Spridningsgods -> Spread freight?
    ],
    'BOX' => [
        'label' => __um('PickUp Locker'),
    ],
    'DLV1ST' => [
        'label' => __um('1 delivery attempt, then to the post office'),
    ],
    'DLV2ND' => [
        'label' => __um('2 delivery attempts, then to the post office'),
    ],
    'COD' => [
        'label' => __um('Cash on delivery'),
        'fields' => [
            'account' => [
                'label' => __um('Account (mandatory). Default from sender'),
                'type' => 'text',
            ],
            'accounttype' => [
                'label' => __um('Account type (mandatory). Default value is BGTXT. Possible values are BGOCR or BGTXT'),
                'type' => 'text,'
            ],
            'amount' => [
                'label' => __um('Amount (mandatory)'),
                'type' => 'text',
            ],
            'unit' => [
                'label' => __um('Currency (mandatory)'),
                'type' => 'text',
            ],
        ],
    ],
    'COLD' => [
        'label' => __um('Cold'),
        'fields' => [
            'tempmax' => [
                'label' => __um('Maximum Temperature'),
                'type' => 'text',
            ],
            'tempmin' => [
                'label' => __um('Minimum Temperature'),
                'type' => 'text',
            ],
        ],
    ],
    'CRR' => [
        'label' => __um('Return action'),
        'fields' => [
            'misc' => [
                'label' => __um('Notification'),
                'type' => 'text',
            ],
            'misctype' => [
                'label' => __um('Method'),
                'type' => 'text',
            ],
            'reference' => [
                'label' => __um('Booking message'),
                'type' => 'text',
            ],
            'email' => [
                'label' => __um('E-mail for booking'),
                'type' => 'text',
            ],
            'text1' => [
                'label' => __um('Fetch message'),
                'type' => 'text',
            ],
            'text2' => [
                'label' => __um('Senders phone'),
                'type' => 'text',
            ],
            'text3' => [
                'label' => __um('Fetchers phone'),
                'type' => 'text',
            ],
            'text4' => [
                'label' => __um('Receivers phone'),
                'type' => 'text',
            ],
            'text5' => [
                'label' => __um('Senders e-mail address'),
                'type' => 'text',
            ],
        ],
    ],
    'CUSTOMSDECL' => [
        'label' => __um('Custom handling'),
    ],
    'DLV' => [
        'label' => __um('Delivery'),
    ],
    'DLV1ST' => [
        'label' => __um('1 delivery attempt, then to the post office'),
    ],
    'DLV2ND' => [
        'label' => __um('2 delivery attempts, then to the post office'),
    ],
    'DLVCS' => [
        'label' => __um('DLVCS'),
    ],
    'DLVDEP' => [
        'label' => __um('Delivery at terminal'),
    ],
    'DLVEVN20' => [
        'label' => __um('Delivery/Pickup evening 16-20'),
    ],
    'DLVEVN21' => [
        'label' => __um('Delivery/Pickup evening 17-21'),
    ],
    'DLVWEND12' => [
        'label' => __um('Delivery/Pickup weekend 9-12'),
    ],
    'DLVWEND13' => [
        'label' => __um('Delivery/Pickup weekend 9-13'),
    ],
    'DLVIN1' => [
        'label' => __um('Single Indoor'),
    ],
    'DLVIN2' => [
        'label' => __um('Double Indoor'),
    ],
    'DLVCURB' => [
        'label' => __um('Curbside'),
    ],
    'DLVEVN' => [
        'label' => __um('Evening'),
    ],
    'DLVFLEX' => [
        'label' => __um('Flex delivery'),
    ],
    'DLVIN' => [
        'label' => __um('Carry in'),
    ],
    'DLVINUNP' => [
        'label' => __um('Return of Packing'),
        'fields' => [
            'misc' => [
                'label' => __um('Number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'DLVPRIV' => [
        'label' => __um('Delivery to private person'), // TODO Should be dynamic
    ],
    'DLVT' => [
        'label' => __um('Unloading time'),
        'fields' => [
            'misc' => [
                'label' => __um('Date/time like YYYY-MM-DD HH:MM'), // TODO Make this dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'DLVT07' => [
        'label' => __um('G7'),
    ],
    'DLVT10' => [
        'label' => __um('G10'),
    ],
    'DLVT12' => [
        'label' => __um('G12'),
    ],
    'DLVNOPOD' => [
        'label' => __um('Delivery without receipt'),
    ],
    'DLVNOT' => [
        'label' => __um('Delivery notification'), // TODO By default 'text3' and 'text4' are taken from receiver
    ],
    'DLVSAT' => [
        'label' => __um('Saturday delivery'),
    ],
    'DLVT08' => [
        'label' => __um('Delivery before 08.00'),
    ],
    'DLVT12' => [
        'label' => __um('Delivery before 12.00'),
    ],
    'DLVWORK' => [
        'label' => __um('Delivery to work'),
    ],
    'DRVNOT' => [
        'label' => __um('Driver notification'), // TODO Use customer phone as field 'misc'
    ],
    'DNG' => [
        'label' => __um('Limited quantity of dangerous goods'),
    ],
    'ENOT' => [
        'label' => __um('Notification via E-mail'),
        'fields' => [
            'cc' => [
                'label' => __um('From e-mail, default from sender'),
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
        ],
    ],
    'EVENTRETSP' => [
        'label' => __um('Notification not picked up'), // TODO text3 should be sms or text4 e-mail
    ],
    'EVENTOFD' => [
        'label' => __um('Notification on way'), // TODO text3 should be sms or text4 e-mail
    ],
    'EVENTTERM' => [
        'label' => __um('Notification in terminal'), // TODO text3 should be sms or text4 e-mail
    ],
    'EVENTPUP' => [
        'label' => __um('Notification picked up'), // TODO text3 should be sms or text4 e-mail
    ],
    'EVENTDLV' => [
        'label' => __um('Notification delivered'), // TODO text3 should be sms or text4 e-mail
    ],
    'EVENTDLVSP' => [
        'label' => __um('Notification delivered at service point'), // TODO text3 should be sms or text4 e-mail
    ],
    'EXPR' => [
        'label' => __um('Express'),
    ],
    'EXPR20' => [
        'label' => __um('Express 16-20'),
    ],
    'EXPR21' => [
        'label' => __um('Express 17-21'),
    ],
    'FDNG' => [
        'label' => __um('Limited quantity'),
    ],
    'FRAG' => [
        'label' => __um('Handle with care'),
    ],
    'FRZ' => [
        'label' => __um('Frozen'),
        'fields' => [
            'tempmax' => [
                'label' => __um('Maximum Temperature'),
                'type' => 'text',
            ],
            'tempmin' => [
                'label' => __um('Minimum Temperature'),
                'type' => 'text',
            ],
        ],
    ],
    'GREEN' => [
        'label' => __um('Environmental-friendly'),
    ],
    'HOUR1' => [
        'label' => __um('1-HOUR'),
    ],
    'HOUR2' => [
        'label' => __um('2-HOUR'),
    ],
    'HOUR4' => [
        'label' => __um('4-HOUR'),
    ],
    'HOUR6' => [
        'label' => __um('6-HOUR'),
    ],
    'HOURX' => [
        'label' => __um('Long Distance'),
    ],
    'IDCHK' => [
        'label' => __um('ID check'),
    ],
    'IDCHKCTR' => [
        'label' => __um('ID contract'),
    ],
    'INSU' => [
        'label' => __um('Insurance'),
        'fields' => [
            'amount' => [
                'label' => __um('Amount, mandatory for non domestic shipments.'),
                'type' => 'text',
            ],
            'unit' => [
                'label' => __um('Currency, mandator for non domestic shipments.'),
                'type' => 'text',
            ],
        ],
    ],
    'ITLL' => [
        'label' => __um('Increase limit to liability'),
    ],
    'HOMEDLV' => [
        'label' => __um('Home delivery'), // Uses SMS number from receiver
    ],
    'HPALLET' => [
        'label' => __um('1/2 Pallet'),
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
                'label' => __um('yes if you want to send e-mail'),
                'type' => 'text',
            ],
        ],
    ],
    'LNKPRTR' => [
        'label' => __um('Link to return printing'),
        'fields' => [
            'cc' => [
                'label' => __um('From e-mail, default from sender'),
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
                'label' => __um('yes if you want to send e-mail'),
                'type' => 'text',
            ],
        ],
    ],
    'MPRC' => [
        'label' => __um('Mps'),
    ],
    'NONSTACKABLE' => [
        'label' => __um('Non stackable goods'),
    ],
    'NOT' => [
        'label' => __um('Notification'),
    ],
    'NOTEMAIL' => [
        'label' => __um('E-mail notification'), // TODO Use customer e-mail to field 'misc'
    ],
    'NOTPHONE' => [
        'label' => __um('Phone notification'), // TODO Use customer phone to field 'misc'
    ],
    'NOTSMS' => [
        'label' => __um('SMS notification'), // TODO Use customer phone for field 'misc'
    ],
    'ONETIMEAGREEMENT' => [
        'label' => __um('One time agreement'),
        'fields' => [
            'misc' => [
                'label' => __um('Code'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'OPAY' => [
        'label' => __um('Other payer'),
        'fields' => [
            'custno' => [
                'label' => __um('Customer number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'PERS' => [
        'label' => __um('ID Individual Verification'),
    ],
    'PODNOT' => [
        'label' => __um('Extradition notification'), // TODO populate text3 with SMS or text4 with e-mail from receiver
    ],
    'PUPOPT' => [
        'label' => __um('Custom collection point'),
    ],
    'PUPT' => [
        'label' => __um('Temporal loading'),
    ],
    'PRENOT' => [
        'label' => __um('Pre-notification'),
    ],
    'PRENOTDLV' => [
        'label' => __um('Information about planned delivery'),
    ],
    'PRIO' => [
        'label' => __um('Priority'),
    ],
    'PUP' => [
        'label' => __um('Pickup'),
        'fields' => [
            'custno' => [
                'label' => __um('Customer number'),
                'type' => 'text',
            ],
        ],
    ],
    'PUPDEP' => [
        'label' => __um('Receiver collects'),
    ],
    'QPALLET' => [
        'label' => __um('1/4 Pallet'),
    ],
    'RECYCLE' => [
        'label' => __um('Recycling'),
        'fields' => [
            'misc' => [
                'label' => __um('Number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'REMAILRECEIPT' => [
        'label' => __um('Email receipt'),
        'fields' => [
            'misc' => [
                'label' => __um('E-mail Address'), // TODOO Should be dynamic
                'type' => 'text',
            ],
        ],
    ],
    'RETN' => [
        'label' => __um('Decreased laytime'),  // TODO Control translation from liggetid -> laytime?
    ],
    'RETNEXT' => [
        'label' => __um('Increased laytime'),  // TODO Control translation from liggetid -> laytime?
    ],
    'RETPP' => [
        'label' => __um('Pre-paid customer return'),
        'fields' => [
            'misc' => [
                'label' => __um('PRINT or STORE pre-paid return label'),
                'type' => 'text',
            ],
        ],
    ],
    'RETXMAN' => [
        'label' => __um('Return of Goods Oversize'),
        'fields' => [
            'misc' => [
                'label' => __um('Number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'RNOTEMAIL' => [
        'label' => __um('E-mail notification'), // TODO Use customer e-mail to field 'misc',
        'fields' => [
            'misc' => [
                'label' => __um('Email address'),
                'type' => 'text',
            ],
        ],
    ],
    'RNOTLTR' => [
        'label' => __um('Letter notification'),
    ],
    'RNOTSMS' => [
        'label' => __um('SMS notification'), // TODO Use customer e-mail to field 'misc',
        'fields' => [
            'misc' => [
                'label' => __um('SMS number'),
                'type' => 'text',
            ],
        ],
    ],
    'RPAY' => [
        'label' => __um('Recipient shipping'),
        'fields' => [
            'custno' => [
                'label' => __um('Customer number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'RPODNOT' => [
        'label' => __um('Delivery notification'), // TODO text3 should be sms or text4 e-mail
    ],
    'RPS' => [
        'label' => __um('Swap'),
        'fields' => [
            'misc' => [
                'label' => __um('Number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'SOCIAL' => [
        'label' => __um('Social control'),
    ],
    'SPBAG' => [
        'label' => __um('Bag on the door'),
    ],
    'SPH' => [
        'label' => __um('Special Handling'),
        'fields' => [
            'misc' => [
                'label' => __um('Number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'SPTR' => [
        'label' => __um('Long goods'),
    ],
    'SPV' => [
        'label' => __um('Special'),
    ],
    'SWAP' => [
        'label' => __um('Swap'),
    ],
    'SYSTEM' => [
        'label' => __um('System network delivery'),
    ],
    'TECH' => [
        'label' => __um('Technical equipment'),
    ],
    'TECHDLV' => [
        'label' => __um('Tail lift loading'),
    ],
    'TECHPUP' => [
        'label' => __um('Tail lift loading'),
    ],
    'TSUP' => [
        'label' => __um('Transport support'), // TODO Check translation Transportbidrag -> Transport support
        'fields' => [
            'misc' => [
                'label' => __um('Goods number'), // TODO Should this be dynamic?
                'type' => 'text',
            ],
        ],
    ],
    'ULOAD' => [
        'label' => __um('Keeper rent'), // TODO Behållarhyra -> Keeper rent?
    ],
    'XMAN' => [
        'label' => __um('Additional crew'),
    ],
    'VALUE' => [
        'label' => __um('High value shipments'),
        'fields' => [
            'amount' => [
                'label' => __um('Amount'),
                'type' => 'text',
            ],
            'unit' => [
                'label' => __um('Currency'),
                'type' => 'text',
            ],
        ],
    ],
    'VIP' => [
        'label' => __um('VIP'),
    ],
    'WARM' => [
        'label' => __um('Warm transport'),
    ],
];
