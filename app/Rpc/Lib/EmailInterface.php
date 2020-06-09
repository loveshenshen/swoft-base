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
 * Date: 2020/4/28
 * Time: 17:56
 * Author: shen
 */

namespace App\Rpc\Lib;

/**
 * Interface EmailInterface
 * @package App\Rpc\Lib
 */
interface EmailInterface
{

    /**
     * 发送邮件
     * @param $email
     * @return mixed
     */
    public function sendEmail($email);

}