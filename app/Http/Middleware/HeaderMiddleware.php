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
 * Time: 15:19
 * Author: shen
 */

namespace App\Http\Middleware;
use App\Common\Auth;
use App\Common\JWT;
use App\Exception\UnauthorizedHttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Http\Server\Contract\MiddlewareInterface;

/**
 * Class HeaderMiddleware
 * @package App\Http\Middleware
 * @Bean()
 */
class HeaderMiddleware implements MiddlewareInterface
{

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // before request handle
        // 判断token
        $joke = $request->getHeaderLine("Joke");
        $device = $request->getHeaderLine("Device");

        if($joke != Auth::JOKE){
            throw new UnauthorizedHttpException("Your request was made with invalid credentials.");
        }
        if(empty($device)){
            throw new UnauthorizedHttpException("Device not be empty");
        }
        $response = $handler->handle($request);
        return $response;
    }

}