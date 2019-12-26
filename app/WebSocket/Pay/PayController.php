<?php declare(strict_types=1);

namespace App\WebSocket\Pay;

use App\Common\Memory;
use App\Exception\UserException;
use App\Model\Entity\User;
use App\WebSocket\BaseController;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Context\Context;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Swoft\Redis\Pool;
use Swoft\Session\Session;
use Swoft\Task\Task;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use App\WebSocket\Middleware\CorsMiddleware;
use Swoft\WebSocket\Server\Message\Request;
use App\WebSocket\Middleware\PayMiddleware;

/**
 * Class PayController
 *
 * @WsController()
 *
 */
//@WsController(middlewares={PayMiddleware::class})
class PayController extends BaseController
{
    /**
     * @Inject()
     * @var Pool
     */
     public $redis;
    /**
     * Message command is: 'pay.index'
     *
     * @return mixed
     * @MessageMapping("status")
     * @throws
     */
    public function status():array
    {
        //监听redis 的队列 若有数据则直接推送给前端
        $lastError = server()->getSwooleServer()->getLastError();
        $data =  ['name'=>'1','age'=>'shen','heartbeat'=>$lastError];
        //若有中间件则使用中间件
        $this->send($data);
    }

    /**
     * open 校验
     * @param string $data
     * @MessageMapping("auth")
     * @throws
     */
    public function auth($data):void
    {
        $memory = Memory::getInstance();
        $request = Context::get()->getRequest();
//        $fd = Session::mustGet()->getFd();
        $fd = $request->getFd();
        $userId = Memory::getUserId($fd);
        if(!$userId){
            $data = $request->getMessage()->getData();
            if(!isset($data["token"])){
                throw new UserException('Token in valid');
            }
            $user = \App\Model\Entity\UserDevice::where("access_token",$data["token"])->first();
            if(!$user){
                throw new UserException('Token in valid');
            }
            $memory->set(strval($user->getUserId()),[
                'fd'=>$fd,
                'user_id'=>$user->getUserId()
            ]);
            \App\Model\Entity\User::find($user->getUserId())->update([
                "is_online"=>User::IS_ONLINE_ON
            ]);
           $userId = $user->getUserId();
           CLog::info("Find database....");
        }
        $this->send("Login Success.");
        //投递任务
        $result = Task::co("userMessage","push",[$userId,$fd]);
        CLog::info('co task.....'.$result);
        //redis 实现
//     $this->redis->hSet(\App\Model\Entity\User::REDIS_USER_FD,strval($user->getUserId()),strval($fd));
    }
}
