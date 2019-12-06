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
 * Date: 2019/10/12
 * Time: 10:45
 * Author: shen
 */

namespace App\Http\Controller;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use  Swoft\Http\Message\Request;
/**
 * Class WsController
 * @package App\Http\Controller
 * @Controller(prefix="/v1/ws")
 */
class WsController
{

    /**
     * @RequestMapping("push",method=RequestMethod::POST)
     * @return array
     */
    public function  push(Request $request):array
    {
        $userId = $request->post("");

        return [];
    }


}