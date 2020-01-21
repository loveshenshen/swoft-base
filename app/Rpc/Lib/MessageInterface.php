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
 * Date: 2020/1/2
 * Time: 15:50
 * Author: shen
 */

namespace App\Rpc\Lib;

/**
 * Interface MessageInterface
 * @package App\Rpc\Lib
 */
interface MessageInterface
{

    /**
     * 发送短信
     * @param $mobile
     * @param $text
     * @return mixed
     */
    public function send($mobile,$text);
}