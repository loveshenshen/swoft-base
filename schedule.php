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
 * Date: 2019/12/26
 * Time: 11:34
 * Author: shen
 */

//$schedule  = new \Co\Scheduler();
//
//$schedule->add(function(){
//    co::sleep(1);
//    go(function(){
//        echo "co2";
//    });
//});
//$schedule->start();

$chan = new chan(10);

go(function()use($chan){
    while(true){
        co::sleep(2);
        $chan->push([
            'cmd'=>'chan',
            'data'=>'111'
        ]);
        var_dump(co::getCid(),co::getContext(),co::getPcid());
    }
});


go(function()use($chan){
    while(true){
        var_dump($chan->pop());
        var_dump(co::getCid(),co::getContext(),co::getPcid());
    }

});