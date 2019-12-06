<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace common\util;


/**
 * 常量
 *
 * @author Ather.Shu Apr 27, 2015 10:55:22 AM
 */
class Constants {
    // http请求头
    const APP_JOKE = 'carrepair!';
    // access token有效期（多久不调接口） 10天
    const ACCESS_TOKEN_EXPIRES = 864000;
    // 内部调用api的device标示
    const DEVICE_INNER_CALL = "inner_api_call";
    // id加密
    const ID_CRYPT_KEY = "y11twoa99_k3y_";
    // id加密类型：用户
    const ENC_TYPE_USER = "user";
    
    // api data加密
    const DATA_ENCRYPT_IV = "y11twoa99_iv_";
    // img分割符
    const IMG_DELIMITER = "||";
    // 上传类型
    const UPLOAD_TYPE_AVATAR = 1;
    const UPLOAD_TYPE_CKEDITOR = 2;
    const UPLOAD_TYPE_AUDIO = 3;
    const UPLOAD_TYPE_VIDEO = 4;
    const UPLOAD_TYPE_IMAGE = 5;
    const UPLOAD_TYPE_FILE = 6;
    const UPLOAD_TYPE_PDF = 9;

    public static $UPLOAD_TYPES = [
        self::UPLOAD_TYPE_AVATAR => [
            'name' => '头像',
            'max' => 0.7
        ],
        self::UPLOAD_TYPE_AUDIO => [
            'name' => '音频',
            'max' => 10
        ],
        self::UPLOAD_TYPE_VIDEO => [
            'name' => '视频',
            'max' =>20
        ],
        self::UPLOAD_TYPE_IMAGE => [
            'name' => '图片',
            'max' =>5
        ],
        self::UPLOAD_TYPE_PDF => [
            'name' => 'pdf文件',
            'max' =>10
        ],
        self::UPLOAD_TYPE_CKEDITOR => [
            'name' => '后台编辑器',
            'max' => 1
        ],
//         self::UPLOAD_TYPE_MALL_GOODS_ANIMATION => [
//             'name' => '动画',
//             'max' => 10,
//             'extensions' => ['zip'],
//         ],
    ];

    // 区分充值、订单支付的标志
    const CHARGE_EXTRA_FLAG = '000';
    
    // 支付类型
    const PAY_TYPE_WX = 1;
    const PAY_TYPE_ZFB = 2;
    const PAY_TYPE_LL = 3;

    public static $PAY_TYPES = [ 
        self::PAY_TYPE_WX => "wx",
        self::PAY_TYPE_ZFB => "zfb" ,
        self::PAY_TYPE_LL => "ll"
    ];
    
    // 平台类型
    const PLATFORM_WEB = 1;
    const PLATFORM_IOS = 2;
    const PLATFORM_ANDROID = 3;
    const PLATFORM_XCH = 4;

    public static $PLATFORMS = [ 
        self::PLATFORM_WEB => "web",
        self::PLATFORM_IOS => "ios",
        self::PLATFORM_ANDROID => "android",
        self::PLATFORM_XCH => "xch"
    ];
    
    //OAUTH类型
    const OAUTH_MOBILE = 1;
    const OAUTH_WEIBO = 2;
    const OAUTH_QQ = 3;
    const OAUTH_WEIXIN_APP = 4;
    const OAUTH_WEIXIN_GZH = 5;
    const OAUTH_WEIXIN_XCH = 6;

    public static $OAUTHS = [
        self::OAUTH_MOBILE => '手机',
        self::OAUTH_WEIBO => '微博',
        self::OAUTH_QQ => 'QQ',
        self::OAUTH_WEIXIN_APP => '微信APP',
        self::OAUTH_WEIXIN_GZH => '微信公众号',
        self::OAUTH_WEIXIN_XCH => '小程序',
    ];
    
    // 缓存类型
    //用户手机验证码
    const CACHE_USER_MOBILE_CODE = 1;
    //平台屏蔽关键词
    const CACHE_SYSTEM_BANWORDS = 2;
    //城市列表
    const CACHE_CHINA_CITIES = 3;
    
    //性别男女
    const GENDER_FEMALE = 0;
    const GENDER_MALE = 1;
    
    //默认头像
    const DEFAULT_AVATAR = "/img/avatar.png";


    const BOOL_TRUE = 1;
    const BOOL_FALSE = 0;
    public static $BOOL = [
        self::BOOL_FALSE => '否',
        self::BOOL_TRUE => '是',
    ];


    const GENDER_MAN = 1;
    const GENDER_WOMAN = 0;
    public static $GENDER = [
        self::GENDER_WOMAN => '女',
        self::GENDER_MAN => '男',
    ];



}