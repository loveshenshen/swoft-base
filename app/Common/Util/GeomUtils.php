<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace common\util;

/**
 * geometry
 * 
 * @author Ather.Shu May 18, 2015 2:16:36 PM
 */
class GemoUtils {

    /**
     * 地球半径km
     * 
     * @var number
     */
    const EARTH_RADIUS = 6378.137;

    private static function rad($d) {
        return $d * pi() / 180.0;
    }

    /**
     * 获取两个点之间的距离
     * @param number $lng1
     * @param number $lat1
     * @param number $lng2
     * @param number $lat2
     * 
     * @return m
     */
    public static function distance($lng1, $lat1, $lng2, $lat2) {
        // http://www.cnblogs.com/ycsfwhh/archive/2010/12/20/1911232.html
        $radLat1 = self::rad( $lat1 );
        $radLat2 = self::rad( $lat2 );
        $a = $radLat1 - $radLat2;
        $b = self::rad( $lng1 ) - self::rad( $lng2 );
        
        $s = 2 * asin( (sqrt( pow( sin( $a / 2 ), 2 ) + cos( $radLat1 ) * cos( $radLat2 ) * pow( sin( $b / 2 ), 2 ) )) );
        $s = $s * self::EARTH_RADIUS;
        $s = round( $s * 1000 );
        return $s;
    }
}