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

use Swoft\Db\Eloquent\Collection;

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

    /**
     * 返回收据格式化
     * @param  $mixed
     * @return array
     */
    public static function formatResponse($data){
        $result = [];
        if(is_scalar($data)){
            $result['data'] = $data;
        }elseif (is_object($data)){
            if($data instanceof Collection){
                $result['data'] = $data;
                $result['total'] = count($data);
            }else{
                $result = array_merge($result,$data->toArray());
            }
        }elseif(is_array($data)){
            $firstObject = current($data);
            if(count($data) == count($data,1) && !is_object($firstObject)){
                $result = array_merge($result,$data);
            }else{
                $result['data'] = $data;
                $result['total'] = count($data);
            }
        }else{
            $result['data'] = $data;
        }
        $result['code'] = 200;
        return $result;
    }




}