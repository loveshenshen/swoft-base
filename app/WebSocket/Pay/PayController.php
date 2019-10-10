<?php declare(strict_types=1);

namespace App\WebSocket\Pay;

use App\Common\Memory;
use App\Model\Entity\User;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Context\Context;
use Swoft\Log\Helper\Log;
use Swoft\Redis\Pool;
use Swoft\Session\Session;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;

/**
 * Class PayController
 *
 * @WsController()
 */
class PayController
{
    /**
     * @Inject()
     * @var Pool
     */
     public $redis;
    /**
     * Message command is: 'pay.index'
     *
     * @return void
     * @MessageMapping("status")
     * @throws
     */
    public function status(): void
    {
        //监听redis 的队列 若有数据则直接推送给前端
        $request = Context::get()->getRequest();

        $fd = $request->getFd();

        $this->response(200,$request->getRawData(),"");
    }

    /**
     * open 校验
     * @param string $data
     * @MessageMapping("auth")
     * @throws
     */
    public function auth($data):void
    {
        $request = Context::get()->getRequest();
        $data = $request->getMessage()->getData();
        if(!isset($data["token"])){
            $this->response(500,[],"Token in valid");
        }else{
            $user = \App\Model\Entity\UserDevice::where("access_token",$data["token"])->first();
            if(!$user){
                $this->response(500,[],"Token in valid");
            }else{
                $fd = $request->getFd();
                $memory = Memory::getInstance();
                $memory->set(strval($user->getUserId()),[
                    'fd'=>$fd,
                    'user_id'=>$user->getUserId()
                ]);
                \App\Model\Entity\User::find($user->getUserId())->update([
                    "is_online"=>User::IS_ONLINE_ON
                ]);

                //redis 实现
//                $this->redis->hSet(\App\Model\Entity\User::REDIS_USER_FD,strval($user->getUserId()),strval($fd));
                $this->response(200,"Login Success.","");
            }
        }
    }

    /**
     * @param $code
     * @param mixed $data
     * @param $message
     */
    public function response($code,$data,$message){

        if($code == 200){
            $resultData = [
                'code'=>$code,
                'data'=>$data
            ];
        }else{
            $resultData = [
                'code'=>$code,
                'message'=>$message
            ];
        }
        Session::mustGet()->push(json_encode($resultData));
    }



}
