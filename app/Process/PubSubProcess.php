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
 * Time: 17:44
 * Author: shen
 */

namespace App\Process;


use App\Model\Logic\PubSubLogic;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Process\Process;
use Swoft\Process\UserProcess;


/**
 * Class PubSubProcess
 * @package App\Process
 * @Bean()
 *
 */
class PubSubProcess extends UserProcess
{

    /**
     * @Inject()
     * @var PubSubLogic
     */
    public $logic;

    /**
     * @param Process $process
     */
    public function run(Process $process): void
    {
         $this->logic->pubSub($process);
    }

}