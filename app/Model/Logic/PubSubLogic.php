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
 * Date: 2019/9/23
 * Time: 17:46
 * Author: shen
 */

namespace App\Model\Logic;
use App\Common\Memory;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Log\Helper\CLog;
use Swoft\Process\Process;
use Swoft\Redis\Pool;
use App\Model\Entity\ConsumeDetail;


/**
 * Class PubSubLogic
 * @package App\Model\Logic
 * @Bean()
 */
class   PubSubLogic
{

    /**
     * @Inject()
     * @var Pool
     */
    public $redis;

    /**
     * @param Process $process
     * @throws
     */
    public function pubSub(Process $process){
        CLog::info("Redis pubsub Process Start");
        ini_set('default_socket_timeout', -1);
        $this->redis->subscribe([
            ConsumeDetail::REDIS_LIST_PAY,
            ConsumeDetail::REDIS_LIST_PAY_SCAN
        ],function ($redis,$chan,$message){
            switch($chan){
                case ConsumeDetail::REDIS_LIST_PAY:
                    $json = json_decode($message,true);
                    if(!isset($json['user_id'])){
                        CLog::info("用户id不存在");
                        return ;
                    }
                    $userId = strval($json['user_id']);

                    $memory = Memory::getInstance();
                    //内存实现
                    if(!$memory->exist($userId)){
                        CLog::info("fd 不存在");
                        return ;
                    }
                    $fd = intval($memory->get($userId)['fd']);

                    //redis 实现
//           if(! $this->redis->hExists(User::REDIS_USER_FD,$userId)){
//               CLog::info("fd 不存在");
//              return ;
//           }
//           $fd = intval($this->redis->hGet(User::REDIS_USER_FD,$userId));

                    DB::beginTransaction();
                    $consumeDetail = \App\Model\Entity\ConsumeDetail::find($json['id']);
                    if(!$consumeDetail){
                        DB::rollBack();
                        CLog::info("消费数据不存在");
                        return;
                    }
                    $resultData = [
                        'code'=>200,
                        'price'=>$consumeDetail->getPrice(),
                        'origin_price'=>$consumeDetail->getOriginPrice(),
                        'user_id'=>$consumeDetail->getUserId(),
                        'nickanme'=>$json['nickname'],
                        'mobile'=>$json['mobile'],
                        'type'=>1,
                        'status'=>2
                    ];

                    //消息推送
                    server()->sendTo($fd,json_encode($resultData));
                    $consumeDetail->setIsPush(1);
                    $consumeDetail->save();
                    DB::commit();
                    break;
                case ConsumeDetail::REDIS_LIST_PAY_SCAN:
                    $json = json_decode($message,true);
                    if(!isset($json['user_id'])){
                        CLog::info("用户id不存在");
                        return ;
                    }
                    $userId = strval($json['user_id']);

                    $memory = Memory::getInstance();
                    //内存实现
                    if(!$memory->exist($userId)){
                        CLog::info("fd 不存在");
                        return ;
                    }
                    $fd = intval($memory->get($userId)['fd']);

                    $resultData = [
                        'code'=>200,
                        'user_id'=>$userId,
                        'nickname'=>$json['nickname'],
                        'mobile'=>$json['mobile'],
                        'type'=>2,
                        'status'=>1

                    ];

                    server()->sendTo($fd,json_encode($resultData));
                    break;
                default:
                    CLog::info("类型不正确");
                    break;
            }
        });

    }
}