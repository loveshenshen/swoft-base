<?php declare(strict_types=1);


namespace App\Listener\Test;


use App\Common\Memory;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Consul\Agent;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Http\Server\HttpServer;
use Swoft\Log\Helper\CLog;
use Swoft\Server\SwooleEvent;

/**
 * Class StartListener
 *
 * @since 2.0
 *
 * @Listener(event=SwooleEvent::START)
 */
class StartListener implements EventHandlerInterface
{
    /**
     * @Inject()
     * @var  Agent
     */
    public $agent;
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {

        /* @var HttpServer $httpServer */
//        $httpServer = $event->getTarget();
//
//        $service = [
//            'ID'                => 'ws',
//            'Name'              => 'ws',
//            'Tags'              => [
//                'ws'
//            ],
//            'Address'           => '127.0.0.1',
//            'Port'              => $httpServer->getPort(),
//            'Meta'              => [
//                'version' => '2.0'
//            ],
//            'EnableTagOverride' => false,
//            'Weights'           => [
//                'Passing' => 10,
//                'Warning' => 1
//            ]
//        ];
//
//        // Register
//        $this->agent->registerService($service);
//        CLog::info('Swoft http register service success by consul!');
//
//        //添加健康检测
//        $result  = $this->agent->registerCheck([
//            "id"=> "ws",
//            "name"=> "ws",
//            "notes"=>"Web app does a curl internally every 10 seconds",
//            "ttl"=> "30s", //心跳方式
//            "timeout"=> "1s",
//            "serviceId"=>"ws",
//            "serviceName"=>"ws"
//        ]);
//        if($result->getStatusCode() != 200 ){
//            CLog::info('result'.$result->getBody());
//        }

    }
}