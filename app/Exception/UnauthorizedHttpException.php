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
 * Date: 2019/9/25
 * Time: 10:43
 * Author: shen
 */

namespace App\Exception;

/**
 * Class UnauthorizedHttpException
 * @package App\Exception
 */
class UnauthorizedHttpException extends \Exception
{
    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = "", $code = 401, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


}