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
 * token无效
 * @author Ather.Shu Jul 26, 2016 12:02:29 PM
 */
class InvalidTokenHttpException extends \Exception {

    /**
     * InvalidTokenHttpException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = "Token invalid.", $code = 598, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}