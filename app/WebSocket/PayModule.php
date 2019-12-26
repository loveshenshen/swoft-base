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
 * Date: 2019/9/20
 * Time: 13:01
 * Author: shen
 */

namespace App\WebSocket;
use App\Common\Memory;
use App\Model\Entity\User;
use App\WebSocket\Pay\PayController;
use function foo\func;
use Swoft\Context\Context;
use Swoft\Http\Message\Request;
use Swoft\Session\Session;
use Swoft\WebSocket\Server\Annotation\Mapping\OnOpen;
use Swoft\WebSocket\Server\Annotation\Mapping\OnClose;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use App\WebSocket\JsonParser;
use Swoole\WebSocket\Server;
use function server;


/**
 * Class PayModule
 *
 * @WsModule(
 *     "/pay",
 *     messageParser=JsonParser::class,
 *     controllers={PayController::class}
 * )
 */
class PayModule
{
    /**
     * @OnOpen
     * @param Request $request
     * @param int     $fd
     * @throws
     */
    public function onOpen(Request $request, int $fd): void
    {
        //握手成功
        $data = [
            'code'=>200,
            'data'=>'Open success'
        ];
        server()->push($request->getFd(), json_encode($data));
    }


    /**
     * On connection closed
     * - you can do something. eg. record log
     *
     * @OnClose()
     * @param Server $server
     * @param int    $fd
     * @throws
     */
    public function onClose(Server $server, int $fd):void
    {
        //清理内存表的
        Memory::delete($fd);
    }
}