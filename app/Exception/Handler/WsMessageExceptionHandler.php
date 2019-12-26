<?php declare(strict_types=1);

namespace App\Exception\Handler;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Error\Annotation\Mapping\ExceptionHandler;
use Swoft\Log\Helper\Log;
use Swoft\WebSocket\Server\Exception\Handler\AbstractMessageErrorHandler;
use Swoole\WebSocket\Frame;
use Throwable;
use App\Exception\UserException;
use function server;
use const APP_DEBUG;

/**
 * Class WsMessageExceptionHandler
 *
 * @since 2.0
 *
 * @ExceptionHandler(\Throwable::class)
 */
class WsMessageExceptionHandler extends AbstractMessageErrorHandler
{
    /**
     * @param Throwable $e
     * @param Frame     $frame
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function handle(Throwable $e, Frame $frame): void
    {
        // Debug is false
        if (APP_DEBUG) {
            $message = sprintf('%s At %s line %d', $e->getMessage(), $e->getFile(), $e->getLine());
            Log::error('Ws server error(%s)', $message);
        }
        $messages = [
            'code'=>$e->getCode(),
            'message'=>$e->getMessage(),
        ];
        server()->push($frame->fd, json_encode($messages));
    }
}
