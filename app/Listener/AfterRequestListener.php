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
 * Date: 2019/9/29
 * Time: 13:32
 * Author: shen
 */

namespace App\Listener;


use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Http\Message\Response;
use Swoft\Log\Helper\CLog;
use Swoft\SwoftEvent;
use Swoft\Http\Server\HttpServerEvent;


/**
 * Class AfterRequestListener
 * @package App\Listener
 * @Listener(HttpServerEvent::AFTER_REQUEST)
 */
class AfterRequestListener implements EventHandlerInterface
{

    /**
     * @param EventInterface $event
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function handle(EventInterface $event): void
    {
        //请求结束后 记录日志
//        CLog::info(1111);

    }

}