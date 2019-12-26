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
 * Date: 2019/12/26
 * Time: 13:23
 * Author: shen
 */

namespace App\WebSocket;

use Swoft\Session\Session;

/**
 * Class BaseController
 * @package App\WebSocket
 */
class BaseController
{
    /**
     * @param int $fd
     * @param mixed $data
     */
    public function send($data,$fd = 0):void
    {
       $result = [
           'code'=>200,
           'data'=>$data
       ];
        $message = json_encode($result);
        if(empty($fd)){
            Session::mustGet()->push($message);
        }else{
            server()->push($fd,$message);
        }
    }
}