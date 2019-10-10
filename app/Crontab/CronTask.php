<?php declare(strict_types=1);

namespace App\Crontab;

use App\Common\Memory;
use App\Model\Entity\ConsumeDetail;
use App\Model\Entity\User;
use Swoft\Bean\Annotation\Mapping\Inject;
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
     * @var Pool
     * @Inject()
     *
     */
    public $redis;

    /**
     * @Cron("* * * * * *")
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

    public function minuteTask()
    {
        CLog::info("minute task run: %s ", date('Y-m-d H:i:s', time()));
    }

}