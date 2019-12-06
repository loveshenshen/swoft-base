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
 * Date: 2019/10/15
 * Time: 10:34
 * Author: shen
 */

namespace App\Task\Task;
use App\Model\Entity\ConsumeDetail;
use Swoft\Redis\Pool;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;
use Swoft\Bean\Annotation\Mapping\Inject;


/**
 * 用户历史消息任务
 * Class UserMessageTask
 * @package App\Task\Task
 * @Task(name="userMessage")
 *
 */
class UserMessageTask
{

    /**
     * @Inject()
     * @var Pool
     */
    public $redis;
    /**
     * @param string $userId
     * @param int $fd
     * @param array $messages
     *
     * @TaskMapping(name="push")
     */
    public function push($userId,$fd):string
    {
        $key = ConsumeDetail::REDIS_USER_SADD_KEY.$userId;
        $this->redis->zUnionStore($key,[
            ConsumeDetail::getRedisKey($userId,ConsumeDetail::REDIS_TYPE_SCAN),
            ConsumeDetail::getRedisKey($userId,ConsumeDetail::REDIS_TYPE_PAY),
        ]);
        $messages = array_reverse($this->redis->zRevRange($key,0,4));
        $result = '';
        foreach ($messages as $message){
            $content = json_decode($message,true);
            $result .= strval(server()->push($fd,json_encode($content))) ."\n";
        }
        return $result;
    }


}