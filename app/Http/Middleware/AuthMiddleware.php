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
 * Time: 11:58
 * Author: shen
 */

namespace App\Http\Middleware;
use App\Common\Auth;
use App\Common\JWT;
use App\Common\Response;
use App\Exception\InvalidTokenHttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Http\Message\Contract\ServerRequestInterface;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Log\Helper\CLog;


/**
 * Class AuthMiddleware
 * @package App\Http\Middleware
 * @Bean()
 */
class AuthMiddleware implements MiddlewareInterface
{

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws
     */
   public function process(\Psr\Http\Message\ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
   {
       // before request handle
       // 判断token
       $token = $request->getHeaderLine("Authorization");
       try {
           $user = JWT::decode($token);
       } catch (\Exception $e) {
           throw new InvalidTokenHttpException();
       }
       if(!$user){
           throw new InvalidTokenHttpException();
       }
       $request->user = $user;
       $response = $handler->handle($request);
       return $response;
   }


}