<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace App\Exception;

/**
 * 需要登录
 * @author Ather.Shu Jul 26, 2016 12:02:29 PM
 */
class NeedLoginHttpException extends \Exception {

    /**
     * NeedLoginHttpException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = "Please login first.", $code = 599, \Exception $previous = null)
    {
        parent::__construct( $message, $code, $previous);
    }
}