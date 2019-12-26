<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://swoft.org/docs
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\WebSocket\Middleware;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Log\Helper\CLog;
use Swoft\WebSocket\Server\Contract\MessageHandlerInterface;
use Swoft\WebSocket\Server\Contract\MiddlewareInterface;
use Swoft\WebSocket\Server\Contract\RequestInterface;
use Swoft\WebSocket\Server\Contract\ResponseInterface;

/**
 * Class ResponseMiddleware - Custom middleware
 * @Bean()
 */
class ResponseMiddleware implements MiddlewareInterface
{
    /**
     * @param RequestInterface        $request
     * @param MessageHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, MessageHandlerInterface $handler): ResponseInterface
    {
        CLog::info('before handle');
        $resp = $handler->handle($request);
        CLog::info('after handle');
        //所有的接口返回数据调解
        $data = $resp->getData();
        $resp->setData([
            'data'=>$data,
            'code'=>200
        ]);
        return $resp;
    }
}
