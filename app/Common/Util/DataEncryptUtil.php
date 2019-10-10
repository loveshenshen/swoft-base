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
 * api data 加密解密
 *
 * @author Ather.Shu May 22, 2015 3:50:19 PM
 */
class DataEncryptUtil {

    private static $_cipher = MCRYPT_RIJNDAEL_128;

    private static $_mode = MCRYPT_MODE_CBC;
    
    // mcrypt_get_iv_size( self::$_cipher, self::$_mode );
    // mcrypt_get_key_size(self::$_cipher, self::$_mode);
    private static $_maxKeyLen = 32;

    public static function encrypt($data, $key) {
        $key = substr( $key, 0, self::$_maxKeyLen );
        return base64_encode( mcrypt_encrypt( self::$_cipher, $key, $data, self::$_mode, Constants::DATA_ENCRYPT_IV ) );
    }

    public static function decrypt($data, $key) {
        $key = substr( $key, 0, self::$_maxKeyLen );
        $data = base64_decode( $data );
        $data = mcrypt_decrypt( self::$_cipher, $key, $data, self::$_mode, Constants::DATA_ENCRYPT_IV );
        $data = rtrim( rtrim( $data ), "\x00..\x1F" );
        return $data;
    }
}