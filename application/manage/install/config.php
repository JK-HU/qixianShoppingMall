<?php
/**
 * 模块配置
 */
return [
    'region' => [
        'name' => '区域',
        'unified' => true,
        'content' => false,
        'data' => 'region',
    ],
    'store'  => [
        'name' => '门店',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'store',
            'controller' => ['store'],
            'view' => ['store'],
            'view_dir' => ['store'],
            'table' => ['shop_store']
        ]
    ],
    'goods'  => [
        'name' => '商品/订单',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'goods',
            'controller' => ['goods','order'],
            'view' => ['goods','order'],
            'view_dir' => ['goods','order'],
            'table' => ['shop_goods','shop_goods_type','shop_goods_norms','shop_goods_norms_type','shop_goods_nt','shop_order','express_code']
        ]
    ],
    'coupon'  => [
        'name' => '优惠券',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'coupon',
            'controller' => ['coupon'],
            'view' => ['coupon'],
            'view_dir' => ['coupon'],
            'table' => ['mtt_coupon, mtt_mycoupon']
        ]
    ],
    'express'  => [
        'name' => '物流',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'express',
            'controller' => ['express'],
            'view' => ['express'],
            'view_dir' => ['express'],
            'table' => ['express']
        ]
    ],
//    'order'  => [
//        'name' => '订单',
//        'unified' => true,
//        'content' => false,
//        'data' => 'order'
//    ],
    'baidu_map' => [
        'name' => '百度地图',
        'unified' => false,
        'content' => true,
        'data' => [
            'sql' => 'baidu_map',
            'controller' => 'system',
            'view' => ['baidu_map'],
            'view_dir' => 'system',
            'content_txt' => ['baidu_map'],
            'functions' => ['admin/system/baidu_map']
        ]
    ],
    'feast' => [
        'name' => '节日',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'feast',
            'controller' => ['feast'],
            'view' => ['feast'],
            'view_dir' => ['feast'],
            'table' => ['mtt_feast','mtt_feast_element']
        ]
    ],
    'wechat' => [
        'name' => '微信',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'wechat',
            'controller' => ['wechat'],
            'view' => ['wechat'],
            'view_dir' => ['wechat'],
            'table' => [
                'mtt_wx_auth',
                'mtt_wx_config',
                'mtt_wx_default_replay',
                'mtt_wx_fans',
                'mtt_wx_follow_replay',
                'mtt_wx_key_replay',
                'mtt_wx_media',
                'mtt_wx_media_item',
                'mtt_wx_menu',
                'mtt_wx_user',
                'mtt_wx_user_msg',
                'mtt_wx_user_msg_replay'
            ]
        ]
    ],
    'template' => [
        'name' => '模板',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'template',
            'controller' => ['template','debris'],
            'view' => ['template','debris'],
            'view_dir' => ['template'],
            'table' => ['mtt_debris','mtt_debris_type']
        ]
    ],
    'site' => [
        'name' => '网站功能',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'site',
            'controller' => ['link','message','donation'],
            'view' => ['link','message','donation'],
            'view_dir' =>  ['link','message','donation'],
            'table' => ['mtt_link','mtt_message','mtt_donation']
        ]
    ],
    'ad' => [
        'name' => '广告',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'ad',
            'controller' => ['ad'],
            'view' => ['ad'],
            'view_dir' =>  ['ad'],
            'table' => ['mtt_ad', 'mtt_ad_type']
        ]
    ],
    'database' => [
        'name' => '数据库',
        'unified' => false,
        'content' => false,
        'data' => [
            'sql' => 'database',
            'controller' => ['database'],
            'view' => ['database'],
            'view_dir' =>  ['database'],
            'table' => []
        ]
    ],
    'sms' => [
        'name' => '短信',
        'unified' => false,
        'content' => true,
        'data' => [
            'sql' => 'sms',
            'controller' => 'system',
            'view' => ['sms'],
            'view_dir' => 'system',
            'content_txt' => ['sms'],
            'functions' => ['admin/system/sms']
        ]
    ],
    'login' => [
        'name' => '第三方登录',
        'unified' => false,
        'content' => true,
        'data' => [
            'sql' => 'login',
            'controller' => 'system',
            'view' => ['login'],
            'view_dir' => 'system',
            'content_txt' => ['login'],
            'functions' => ['admin/system/login']
        ]
    ],
    'email' => [
        'name' => '邮箱',
        'unified' => false,
        'content' => true,
        'data' => [
            'sql' => 'email',
            'controller' => 'system',
            'view' => ['email'],
            'view_dir' => 'system',
            'content_txt' => ['email'],
            'functions' => ['admin/system/email','admin/system/trySend']
        ]
    ],
];