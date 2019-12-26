<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: sarukinhyou
 * Date: 2019/9/20
 * Time: 13:01
 * Author: shen
 */
namespace App\WebSocket;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\WebSocket\Server\Contract\MessageParserInterface;
use Swoft\WebSocket\Server\Message\Message;

/**
 * Class JsonParser
 *
 * @since 2.0
 * @Bean()
 */
class JsonParser implements MessageParserInterface
{
    /**
     * @param Message $message
     *
     * @return string
     */
    public function encode(Message $message): string
    {
        return JsonHelper::encode($message->getData());
    }

    /**
     * Decode data to array.
     *
     * @param string $data Message data. It's {@see \Swoole\WebSocket\Frame::$data)
     *
     * @return Message
     */
    public function decode(string $data): Message
    {
        $map = JsonHelper::decode($data, true);

        return Message::newFromArray($map);
    }
}
