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
 * Time: 17:23
 * Author: shen
 */

namespace App\Aspect;

use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\Before;
use Swoft\Aop\Annotation\Mapping\PointBean;
use App\Http\Controller\HomeController;
use Swoft\Aop\Point\JoinPoint;

/**
 * Class CountTimeAspect
 * @package App\Aspect
 * @Aspect(order=1)
 * @PointBean(include={HomeController::class})
 */
class CountTimeAspect
{
    /**
     * @var float
     */
    public $beginTime = 0.00;

    /**
     * @Before()
     */
    public function before(){
        $this->beginTime = microtime(true);
    }

    /**
     * @After()
     * @param JoinPoint $joinPoint
     */
    public function after(JoinPoint $joinPoint){
        $endTime = microtime(true);

        $method = $joinPoint->getMethod();
        switch($method){
            case 'aspect':
                echo "{$method}方法耗时:".($endTime - $this->beginTime);
                break;
        }
    }
}