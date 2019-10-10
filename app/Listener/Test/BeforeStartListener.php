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
 * Date: 2019/9/21
 * Time: 09:20
 * Author: shen
 */

namespace App\Listener\Test;
use App\Common\Memory;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Event\EventSubscriberInterface;
use Swoft\Log\Helper\CLog;
use Swoft\Server\ServerEvent;
use Swoft\Event\Annotation\Mapping\Listener;
/**
 * Class BeforeStartListener
 * @package App\Listener\Test
 *
 *
 * @Listener(ServerEvent::BEFORE_START)
 */
class BeforeStartListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     * @throws
     */
    public function handle(EventInterface $event): void
    {
        //创建内存表 来实现进程间的共享
        Memory::getInstance();

        CLog::info(' before start listener 开始。。。。。。。');
    }




}