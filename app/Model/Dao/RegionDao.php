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
 * Date: 2019/9/24
 * Time: 14:45
 * Author: shen
 */

namespace App\Model\Dao;


class RegionDao
{


    /**
     * @param $type
     * @param $parentId
     * @param $page
     * @param $num
     */
    public static function getRegionKey($type,$parentId,$page,$num){
        return "region_type".$type."_parent_id".$parentId."_page".$page."num".$num;
    }

    /**
     * @param $type
     * @param $parentId
     * @param $page
     * @param $num
     */
    public static function getRegionTotalKey($type,$parentId,$page,$num){
        return "region_total_type".$type."_parent_id".$parentId."_page".$page."num".$num;
    }



}