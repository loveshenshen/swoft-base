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
 * Date: 2019/9/20
 * Time: 22:44
 * Author: shen
 */

namespace App\Common;

/**
 * Class Memory
 * @package App\Common
 */
class Memory
{
    /**
     * @var \swoole_table
     */
    public static $table = null;

    public static function getInstance(){
        if(self::$table){
            return self::$table;
        }else{
           $table = new \swoole_table(10000);
           $table->column("fd",\swoole_table::TYPE_INT);
           $table->column("user_id",\swoole_table::TYPE_INT);
           $table->create();
           self::$table = $table;
           return self::$table;
        }
    }

}