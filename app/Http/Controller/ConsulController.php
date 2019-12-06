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
 * Date: 2019/10/28
 * Time: 11:21
 * Author: shen
 */

namespace App\Http\Controller;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Consul\Agent;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;

/**
 * Class ConsulController
 * @package App\Http\Controller
 * @Controller(prefix="/consul")
 */
class ConsulController
{

    /**
     * @Inject()
     * @var Agent
     */
     public $agent;
    /**
     * @RequestMapping(route="health",method=RequestMethod::GET)
     * @throws
     */
    public function health(){
        //  "check": {
        //    "id": "api",
        //    "name": "HTTP API on port 5000",
        //    "http": "http://localhost:5000/health",
        //    "interval": "10s",
        //    "timeout": "1s"
        //  }
        $check = [
            'id'=>'ws',
            'name'=>'ws',
            'http'=>'http://localhost:8308/redis/str',
            'interval'=>'10s',
            'timeout'=>'1s',
            ''
        ];
        $result = $this->agent->registerCheck($check);
        var_dump($result);
        $this->agent->deregisterCheck('ws');
    }

    /**
     * @RequestMapping(route="status",method={RequestMethod::GET})
     * @return int
     */
    public function status(){
        return 1;
    }

}