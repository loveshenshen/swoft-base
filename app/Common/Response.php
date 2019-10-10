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
 * Date: 2019/9/24
 * Time: 14:19
 * Author: shen
 */

namespace App\Common;

/**
 * Class Response
 * @package App\Common
 */
class Response
{

    /**
     * @param $code
     * @param $data
     * @param $message
     * @throws
     */
    public static function format($code,$data,$message){
       $result = [
           'code'=>$code,
           'data'=>$data,
           'message'=>$message
       ];
       return $result;
    }




}