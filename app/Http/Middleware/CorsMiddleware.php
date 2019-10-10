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
 * Time: 11:47
 * Author: shen
 */

namespace App\Http\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use \Swoft\Http\Server\Contract\MiddlewareInterface;


/**
 * Class CorsMiddleware
 * @package App\Http\Middleware
 * @Bean()
 */
class CorsMiddleware implements MiddlewareInterface
{

    /**
     * @param ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $requestHandler
     * @return \Psr\Http\Message\ResponseInterface
     */
   public function process(ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): ResponseInterface
   {
       if ('OPTIONS' === $request->getMethod()) {
           $response = \context()->getResponse();
           return $this->configResponse($response);
       }
       $response = $handler->handle($request);
       return $this->configResponse($response);
   }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    private function configResponse(ResponseInterface $response)
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin,Joke,Device,Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    }

}