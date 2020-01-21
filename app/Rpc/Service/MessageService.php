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
 * Time: 15:52
 * Author: shen
 */

namespace App\Rpc\Service;
use app\Common\Util\YunPianManager;
use App\Rpc\Lib\MessageInterface;
use App\Rpc\Lib\SmsInterface;
use Swoft\Rpc\Server\Annotation\Mapping\Service;

/**
 * Class MessageService
 * @package App\Rpc\Service
 * @Service()
 */
class MessageService implements SmsInterface
{

    /**
     * 发送短信
     * @param $mobile
     * @param $text
     * @return bool|mixed|string
     */
     public function sendMessage($mobile, $text)
     {
         return  YunPianManager::sendSMS($mobile,$text);
     }

}