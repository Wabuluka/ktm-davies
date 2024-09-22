<?php

$amazonAssociateId = 'k1040041-22';
$rakutenId = '34df531b.2f0a5b2c.34df531c.15b50105';
$dlsiteAffiliateId = 'k86032';

return [
    'ktcom' => [
        'amazon' => [
            'asp' => [
                'name' => 'amazon',
                'params' => ['id' => $amazonAssociateId],
            ],
        ],
        'dlsite' => [
            'asp' => [
                'name' => 'dlsite',
                'params' => ['id' => $dlsiteAffiliateId],
            ],
        ],
        'ebookjapan' => [
            'params' => [
                'query' => [
                    'publisher' => '400486',    // キルタイムコミュニケーション
                ],
            ],
        ],
        'bookwalker' => [
            'params' => [
                'company' => '116',
            ],
        ],
        'cmoa' => [
            'params' => [
                'publisher_id' => '0000413', // キルタイムコミュニケーション
            ],
        ],
        'renta' => [
            'params' => [
                'publisher' => 'キルタイムコミュニケーション',
            ],
        ],
        'melonbooks' => [
            'params' => [
                'maker_id' => '899',  // maker_id=キルタイムコミュニケーション
            ],
        ],
    ],

    'ceriserose' => [
        'amazon' => [
            'asp' => [
                'name' => 'amazon',
                'params' => ['id' => $amazonAssociateId],
            ],
        ],
        'rakuten' => [
            'asp' => [
                'name' => 'rakuten',
                'params' => ['id' => $rakutenId, 'keisokuId' => '_RTLink23219'],
            ],
        ],
        'dlsite' => [
            'asp' => [
                'name' => 'dlsite',
                'params' => ['id' => $dlsiteAffiliateId],
            ],
        ],
        'booklive' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3701759', 'pid' => '889550802'],
            ],
            'params' => [
                'g_ids' => '3',     // BLマンガ
                'k_ids' => '4833',  // スリーズロゼコミックス
                'p_ids' => '740',   // キルタイムコミュニケーション
                'query' => [
                    'utm_source' => 'spad',
                    'utm_medium' => 'affiliate',
                    'utm_campaign' => '102',
                    'utm_content' => 'normal',
                ],
            ],
        ],
        'cmoa' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3701759', 'pid' => '889550803'],
            ],
            'params' => [
                'publisher_id' => '0000413', // キルタイムコミュニケーション
            ],
        ],
        'ebookjapan' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3701759', 'pid' => '889550798'],
            ],
            'params' => [
                'query' => [
                    'genre' => 'bl',
                    'publisher' => '400486',    // キルタイムコミュニケーション
                    'dealerid' => '30064',
                ],
            ],
        ],
        'bookwalker' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3701759', 'pid' => '889550797'],
            ],
            'params' => [
                'company' => '116',
            ],
        ],
        'renta' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3701759', 'pid' => '889550795'],
            ],
            'params' => [
                'publisher' => 'キルタイムコミュニケーション',
            ],
        ],
        'mechacomic' => [
            'params' => [
                'query' => [
                    'genre' => '24',    // BL漫画
                ],
            ],
        ],
        'melonbooks' => [
            'params' => [
                'maker_id' => '899',  // maker_id=キルタイムコミュニケーション
            ],
        ],
    ],

    'chocolatsucre' => [
        'amazon' => [
            'asp' => [
                'name' => 'amazon',
                'params' => ['id' => $amazonAssociateId],
            ],
        ],
        'rakuten' => [
            'asp' => [
                'name' => 'rakuten',
                'params' => ['id' => $rakutenId, 'keisokuId' => '_RTLink23120'],
            ],
        ],
        'dlsite' => [
            'asp' => [
                'name' => 'dlsite',
                'params' => ['id' => $dlsiteAffiliateId],
            ],
        ],
        'booklive' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3585678', 'pid' => '889532041'],
            ],
            'params' => [
                'g_ids' => '7',    // TLマンガ
                'p_ids' => '740',  // キルタイムコミュニケーション
                'query' => [
                    'utm_source' => 'spad',
                    'utm_medium' => 'affiliate',
                    'utm_campaign' => '102',
                    'utm_content' => 'normal',
                ],
            ],
        ],
        'cmoa' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3585678', 'pid' => '889532035'],
            ],
            'params' => [
                'publisher_id' => '0000413', // キルタイムコミュニケーション
            ],
        ],
        'ebookjapan' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3585678', 'pid' => '890588332'],
            ],
            'params' => [
                'query' => [
                    'genre' => 'tl',
                    'publisher' => '400486',    // キルタイムコミュニケーション
                    'dealerid' => '30064',
                ],
            ],
        ],
        'bookwalker' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3585678', 'pid' => '889550792'],
            ],
            'params' => [
                'company' => '116',
            ],
        ],
        'renta' => [
            'asp' => [
                'name' => 'valuecommerce',
                'params' => ['sid' => '3585678', 'pid' => '889532040'],
            ],
            'params' => [
                'publisher' => 'キルタイムコミュニケーション',
            ],
        ],
        'mechacomic' => [
            'params' => [
                'query' => [
                    'genre' => '6',    // TL漫画
                ],
            ],
        ],
        'melonbooks' => [
            'params' => [
                'maker_id' => '899',  // maker_id=キルタイムコミュニケーション
            ],
        ],
    ],

    'blackcherry' => [
        'amazon' => [
            'asp' => [
                'name' => 'amazon',
                'params' => ['id' => $amazonAssociateId],
            ],
        ],
        'dlsite' => [
            'asp' => [
                'name' => 'dlsite',
                'params' => ['id' => $dlsiteAffiliateId],
            ],
        ],
        'ebookjapan' => [
            'params' => [
                'query' => [
                    'genre' => 'adult',
                    'publisher' => '400486',    // キルタイムコミュニケーション
                ],
            ],
        ],
        'bookwalker' => [
            'params' => [
                'company' => '116',
            ],
        ],
        'melonbooks' => [
            'params' => [
                'maker_id' => '899',  // maker_id=キルタイムコミュニケーション
            ],
        ],
    ],
];
