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
 * Time: 10:13
 * Author: shen
 */

namespace App\Exception;


class UserException extends \Exception
{
    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = "", $code = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}