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
 * Date: 2019/9/30
 * Time: 16:59
 * Author: shen
 */

namespace App\Http\Middleware;
use common\util\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swlib\Http\Message;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Contract\MiddlewareInterface;


/**
 * Class ResponseMiddleware
 * @package App\Http\Middleware
 * @Bean()
 *
 */
class ResponseMiddleware implements MiddlewareInterface
{

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var Response
         */
        $response = $handler->handle($request);

        //全局返回值统一API格式
        if($response instanceof Response){
            $result = [
                'code'=>200,
                'message'=>''
            ];
            if(is_array($response->getData()) && count($response->getData()) == count($response->getData(),1) && isset($response->getData()['data'])){
                $result = array_merge($result,$response->getData());
            }else{
                $result['data'] = $response->getData();
            }
        }
        $response = $response->withData($result);

        return $response ;
    }

}