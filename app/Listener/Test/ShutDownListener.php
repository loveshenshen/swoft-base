<?php declare(strict_types=1);


namespace App\Listener\Test;


use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Consul\Agent;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\Server\SwooleEvent;

/**
 * Class ShutDownListener
 *
 * @since 2.0
 *
 * @Listener(SwooleEvent::SHUTDOWN)
 */
class ShutDownListener implements EventHandlerInterface
{
    /**
     * @Inject()
     * @var  Agent
     */
    public $agent;
    /**
     * @param EventInterface $event
     * @throws
     */
    public function handle(EventInterface $event): void
    {
        //取消注册ws
//        $this->agent->deregisterService('ws');
        CLog::info("deregister server...");
    }
}