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
 * Time: 15:56
 * Author: shen
 */

namespace App\Rpc\Lib;


interface SmsInterface
{
    const SMS_TYPE_YUNPIAN = 1;
    const SMS_TYPE_ALIYUN = 2;
    /**
     * @param $mobile
     * @param $text
     * @return mixed
     */
    public function sendMessage($mobile,$text);

}