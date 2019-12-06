<?php declare(strict_types=1);

namespace App\Crontab;

use App\Common\Memory;
use App\Model\Entity\ConsumeDetail;
use App\Model\Entity\User;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Consul\Agent;
use Swoft\Consul\Health;
use Swoft\Consul\KV;
use Swoft\Crontab\Annotaion\Mapping\Cron;
use Swoft\Crontab\Annotaion\Mapping\Scheduled;
use Swoft\Db\DB;
use Swoft\Log\Helper\CLog;
use Swoft\Redis\Pool;
use Swoft\Session\Session;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class CronTask
 *
 * @since 2.0
 *
 * @Scheduled()
 */
class CronTask
{
    /**
     * @Inject()
     * @var Agent
     */
    public $agent;

    /**
     * @var KV
     */
    public $kv;

    /**
     * @var  Health
     */
    public $health;

    /**
     * @var Pool
     * @Inject()
     *
     */
    public $redis;

    /**
     * @throws
     */
    public function secondTask()
    {
        //监听redis队列
       if($data = $this->redis->lPop(ConsumeDetail::REDIS_LIST_PAY)){
           $json = json_decode($data,true);
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
           $consumeDetail = ConsumeDetail::find($json['id']);
           if(!$consumeDetail){
               DB::rollBack();
               CLog::info("消费数据不存在");
               return;
           }
           $resultData = [
               'code'=>200,
               'price'=>$consumeDetail->getPrice(),
               'origin_price'=>$consumeDetail->getOriginPrice(),
               'user_id'=>$consumeDetail->getUserId()
           ];

           //消息推送
           server()->sendTo($fd,json_encode($resultData));
           $consumeDetail->setIsPush(1);
           $consumeDetail->save();
           DB::commit();
       }
    }


    /**
     * 每天执行一下 删除用户的历史消息
     * @Cron("1 1 0 * * *")
     * @throws
     */
    public function minuteTask()
    {
        $userIds = $titles = DB::table('user')->pluck('id');
        foreach ($userIds as $userId){
            $scanKey  =  ConsumeDetail::getRedisKey($userId,ConsumeDetail::REDIS_TYPE_SCAN);
            $payKey   =  ConsumeDetail::getRedisKey($userId,ConsumeDetail::REDIS_TYPE_SCAN);
            if($this->redis->exists($scanKey) && $this->redis->zCard($scanKey) > ConsumeDetail::REDIS_LIST_HISTORY_NUM){
                $this->redis->zRemRangeByRank($scanKey,ConsumeDetail::REDIS_LIST_HISTORY_NUM,-1);
            }
            if($this->redis->exists($payKey) && $this->redis->zCard($payKey) > ConsumeDetail::REDIS_LIST_HISTORY_NUM  ){
                $this->redis->zRemRangeByRank($payKey,ConsumeDetail::REDIS_LIST_HISTORY_NUM,-1);
            }
        }
        CLog::info("clear redis zset over...");
    }


    /**
     * @Cron("0/10 * * * * *")
     * @throws
     * */
    public function  heartbeatCron(){
//        consul 心跳检测
//        $this->agent->passCheck("ws");

    }





}