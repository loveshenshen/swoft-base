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
 * Time: 11:10
 * Author: shen
 */

namespace App\Aspect;
use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\AfterReturning;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\PointBean;
use Swoft\Log\Helper\CLog;


/**
 * Class ResponseAspect
 * @package App\Aspect
 * @Aspect(order=1)
 * @PointBean(include={"App\Http\Controller\UserController"})
 */

class ResponseAspect
{

    /**
     * @After()
     * @throws
     */
    public function after(){

       return [];
    }
}