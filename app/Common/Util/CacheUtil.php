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
 * cache util
 *
 * @author Ather.Shu Jun 25, 2016 6:06:10 PM
 */
class CacheUtil {

    /**
     * 获取缓存
     *
     * @param int $type Constants::CACHE_**
     * @param array $params 确定key的参数数组
     * @return mixed|boolean|\yii\caching\Dependency|string
     */
    public static function getCache($type, $params = []) {
        $cache = \Yii::$app->cache;
        $key = self::getKey( $type, $params );
        return $cache->get( $key );
    }

    /**
     * 设置cache
     *
     * @param int $type Constants::CACHE_**
     * @param mixed $data 缓存数据，null代表删除该缓存
     * @param array $params 确定key的参数数组
     */
    public static function setCache($type, $data, $params = []) {
        $cache = \Yii::$app->cache;
        $key = self::getKey( $type, $params );
        if( !isset( $data ) ) {
            $cache->delete( $key );
        } else {
            $duration = self::getDuration( $type, $params );
            $cache->set( $key, $data, $duration );
        }
    }

    /**
     * 缓存key
     *
     * @return string
     */
    private static function getKey($type, $params) {
        $key = "";
        switch ($type) {
            case Constants::CACHE_USER_MOBILE_CODE :
                $key = "code_" . $params ['mobile'];
                break;
            case Constants::CACHE_SYSTEM_BANWORDS :
                $key = "banwords";
                break;
        }
        return $key;
    }

    /**
     * 缓存duration 秒s
     *
     * @return number
     */
    private static function getDuration($type, $params) {
        $duration = 0;
        switch ($type) {
            case Constants::CACHE_USER_MOBILE_CODE :
                // 短信验证码，30分钟
                $duration = 1800;
                break;
            default :
                $duration = 0;
                break;
        }
        return $duration;
    }
}