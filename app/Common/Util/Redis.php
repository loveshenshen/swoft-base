<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2019 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////

/**
 * Created by PhpStorm.
 * User: sarukinhyou
 * Date: 2019/6/26
 * Time: 10:54
 * Author: shen
 */

namespace app\Common\Util;


use yii\base\InvalidConfigException;
use yii\base\UserException;

class Redis
{
    private static $hostname = '127.0.0.1';
    private static $port = 6379;
    private static $auth = '';

    /**
     * @return \Redis
     * @throws InvalidConfigException
     */
    public static function getRedis(){
        if(!extension_loaded('Redis') ){
           throw new InvalidConfigException("Please install redis extension.");
        }
        $config = \Yii::$app->components;
        if(isset($config['Redis'])){
            self::$hostname = $config['Redis']['hostname'];
            self::$port = $config['Redis']['port'];
            if(isset($config['Redis']['password'])){
                self::$auth = $config['Redis']['password'];
            }
        }
        $redis = new \Redis();
        if(!$redis->connect(self::$hostname,self::$port)){
            throw new InvalidConfigException("Redis connect fail.");
        }
        if(!empty(self::$auth) && !$redis->auth(self::$auth)){
            throw new InvalidConfigException("Redis auth fail.");
        }
        return $redis;
    }
}