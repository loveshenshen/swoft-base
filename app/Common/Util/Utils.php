<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace common\util;

use Hashids\Hashids;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\base\UserException;

/**
 * utils
 *
 * @author Ather.Shu Apr 27, 2015 11:29:09 AM
 */
class Utils {

    private static $_idHashers = [ ];

    /**
     * 获取id hasher
     *
     * @param string $type
     * @return Hashids
     */
    private static function getIdHasher($type) {
        if( !isset( self::$_idHashers [$type] ) ) {
            self::$_idHashers [$type] = new Hashids( Constants::ID_CRYPT_KEY . $type, 5, 'abcdefghijklmnopqrstuvwxyz1234567890' );
        }
        return self::$_idHashers [$type];
    }

    /**
     * id加密
     *
     * @param int $id
     * @param string $type 如order address goods
     * @return string
     */
    public static function encryptId($id, $type) {
        return self::getIdHasher( $type )->encode( $id );
    }

    /**
     * id解密
     *
     * @param string $encID
     * @param string $type 如order address goods
     * @return int
     */
    public static function decryptId($encID, $type) {
        $data = self::getIdHasher( $type )->decode( $encID . '' );
        return empty( $data ) ? '' : $data [0];
    }

    /**
     * 模板字符串
     *
     * @param string $tpl {user}于{time}关注了您
     * @param [] $data
     */
    public static function tpl($tpl, $data) {
        $rtn = preg_replace_callback( "/{(.+?)}/i", function ($match) use($data) {
            return $data [$match [1]];
        }, $tpl );
        return $rtn;
    }

    /**
     * xml转数组
     *
     * @param string $xml
     * @return []
     */
    public static function xml2Array($xml) {
        $result = simplexml_load_string( $xml, 'SimpleXMLElement', LIBXML_NOCDATA );
        return json_decode( json_encode( $result ), true );
    }

    /**
     * 格式化
     *
     * @param string|number $money
     * @param int $precision 精度，默认小数点后两位
     * @return number
     */
    public static function roundMoney($money, $precision = 2) {
        $flag = pow( 10, $precision );
        return round( $flag * $money ) / $flag;
    }

    /**
     * 格式化时长
     *
     * @param int $duration
     * @return string
     */
    public static function formatDuration($duration) {
        if($duration == 0) {
            return '0秒';
        }
        $hours = floor($duration / 3600);
        $minutes = floor( ($duration - $hours * 3600 ) / 60 );
        $seconds = $duration - $hours * 3600 - $minutes * 60;
        return ($hours ? ($hours . '小时') : '') . ( $minutes ? ($minutes . '分钟') : '' ) . ( $seconds ? ($seconds . '秒') : '' );
    }

    /**
     * 格式化文件大小
     *
     * @param number $size
     * @return string
     */
    public static function formatSize($size) {
        if( !is_numeric( $size ) || $size < 0 ) {
            return 'N/A';
        }
        $boundary = pow( 1024, 4 );
        // TB
        if( $size > $boundary ) {
            return round( $size / $boundary, 1 ) . ' TB';
        }
        // GB
        if( $size > ($boundary / 1024) ) {
            return round( $size / $boundary * 1024, 1 ) . ' GB';
        }
        // MB
        if( $size > ($boundary / 1024 / 1024) ) {
            return round( $size / $boundary * 1024 * 1024, 1 ) . ' MB';
        }
        // KB
        if( $size > 1024 ) {
            return round( $size / 1024 ) . ' KB';
        }
        return $size . ' B';
    }

    /**
     * 抛出模型错误异常
     *
     * @param Model $model
     * @param string $message
     * @param boolean $prepend
     */
    public static function throwModelErrorException($model, $message, $prepend = true) {
        $error = '';
        if( $model->hasErrors() ) {
            $error = implode( ", ", $model->getFirstErrors() );
        }
        throw new UserException( $error ? ($prepend ? "{$message}：{$error}" : $error) : $message );
    }

    /**
     * 格式化api json response
     * @param \Swoft\Http\Message\Response $response
     */
    public static function formatApiResponse($response) {
        if( $response->isSuccessful()) {
            // 如果非数组或者纯数组，套一层data
            //array_keys($response->data) === array_keys(array_keys($response->data))
            if(!is_array( $response->getData() ) || empty($response->getData()) || array_keys($response->getData()) === range(0, count($response->getData()) - 1)) {
                $response =  $response->withData([
                    'data' => $response->getData() ,
                    'code' => 200
                ]);
            }
            else {
                $response = $response->withStatus(200);
            }
        }
        return $response;
    }

    public static function  getComment($modelClass){
        $basename = StringHelper::basename($modelClass);
        $tableName = Inflector::underscore($basename);
        $word = Inflector::camel2words($basename);
        $info = \Yii::$app->db->createCommand("show create table `$tableName`")->queryOne();
        preg_match("/(?<=COMMENT=').*(?=')/",$info['Create Table'],$data);
        if(empty($data)){
            return $word;
        }
        return array_shift($data);
    }

    function replaceSpecialChar($strParam){

    }

    /**
     * @param $str
     * @return array
     */
    public static function specialCharacter($str){
        $pattern = '/[\x{4e00}-\x{9fa5}a-zA-Z]+/u';
        preg_match($pattern,$str,$array);
        return $array;
    }


    /**
     * @param $str
     * @return null|string
     */
    public static function getFirstCharter($str)
    {
        if (empty($str)) {
            return '';
        }
        $fchar = $str{0};
        if(is_numeric($fchar)){
            return 'Y';
        }
        $fchar = ord($fchar);
        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str{0});
        }
        try {
            $s1 = iconv('UTF-8', 'gb2312', $str);
            $s2 = iconv('gb2312', 'UTF-8', $s1);
        } catch (\Exception $e) {
            $s1 = iconv('UTF-8', 'GBK', $str);
            $s2 = iconv('GBK', 'UTF-8', $s1);
        }

        $s   = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) {
            return 'A';
        }
        if ($asc >= -20283 && $asc <= -19776) {
            return 'B';
        }
        if ($asc >= -19775 && $asc <= -19219) {
            return 'C';
        }
        if ($asc >= -19218 && $asc <= -18711) {
            return 'D';
        }
        if ($asc >= -18710 && $asc <= -18527) {
            return 'E';
        }
        if ($asc >= -18526 && $asc <= -18240) {
            return 'F';
        }
        if ($asc >= -18239 && $asc <= -17923) {
            return 'G';
        }
        if ($asc >= -17922 && $asc <= -17418) {
            return 'H';
        }
        if ($asc >= -17417 && $asc <= -16475) {
            return 'J';
        }
        if ($asc >= -16474 && $asc <= -16213) {
            return 'K';
        }
        if ($asc >= -16212 && $asc <= -15641) {
            return 'L';
        }
        if ($asc >= -15640 && $asc <= -15166) {
            return 'M';
        }
        if ($asc >= -15165 && $asc <= -14923) {
            return 'N';
        }
        if ($asc >= -14922 && $asc <= -14915) {
            return 'O';
        }
        if ($asc >= -14914 && $asc <= -14631) {
            return 'P';
        }
        if ($asc >= -14630 && $asc <= -14150) {
            return 'Q';
        }
        if ($asc >= -14149 && $asc <= -14091) {
            return 'R';
        }
        if ($asc >= -14090 && $asc <= -13319) {
            return 'S';
        }
        if ($asc >= -13318 && $asc <= -12839) {
            return 'T';
        }
        if ($asc >= -12838 && $asc <= -12557) {
            return 'W';
        }
        if ($asc >= -12556 && $asc <= -11848) {
            return 'X';
        }
        if ($asc >= -11847 && $asc <= -11056) {
            return 'Y';
        }
        if ($asc >= -11055 && $asc <= -10247) {
            return 'Z';
        }
        return null;
    }

    public static function doing($contact)
    {
        $list = [];
        //给每个数组添加字段 chart 值为所属的A-Z的字母分类
        foreach ($contact as &$v) {
            $v['chart'] = self::getFirstCharter($v['real_name']);
        }
        $data = [];
        //给所有数组进行A-Z分类
        foreach ($contact as $val) {
            if (empty($data[$val['chart']])) {
                $data[$val['chart']] = [];
            }
            $data[$val['chart']][] = $val;
        }
        //按照键名排序
        ksort($data);
        dd(json_encode($data,JSON_UNESCAPED_UNICODE));
//        $list = [];
//        foreach ($data as $k => $vv) {
//            foreach ($vv as $item) {
//                array_push($list, $item);
//            }
//        }
        dd($list);
//        return json($this->render(200, '成功', $list));
    }
    /*
    * 拼凑图片前缀
    * */

    public  static function  prefixImage($path,$isArray = true){
        if(empty($path) && $isArray){
            return [];
        }elseif(empty($path)){
            return '';
        }
        if(preg_match('/http[s]?:\/\//',$path)){
            if($isArray){
                return explode(Constants::IMG_DELIMITER,trim($path,'||'));
            }
            return $path;
        }
        if(preg_match('/res\/upload/',$path)){
            $imgPrefix = \Yii::$app->params['frontUrl'];
        }else{
            $imgPrefix = \backend\util\Utils::getImgUrlPrefix();
        }
        if(stripos($path,Constants::IMG_DELIMITER)){
            $images =  explode(Constants::IMG_DELIMITER,$path);
            foreach($images as $key=>$image){
                $images[$key] = $imgPrefix.$image;
            }
            if($isArray){
                return $images;
            }
            return $images[0];
        }
        if($isArray){
            return [$imgPrefix.$path];
        }
        return $imgPrefix.$path;
    }

    public static function date($date){
        switch($date){
            case \common\util\Constants::DAY:
                //今天
                $begin=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
                break;
            case \common\util\Constants::WEEK:
                //本周
                $begin = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
                $end = mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"));
                break;
            case \common\util\Constants::MONTH:
                //本月
                $begin = mktime(0,0,0,date('m'),1,date('Y'));
                $end   = mktime(23,59,59,date('m'),date('t'),date('Y'));
                break;
            case \common\util\Constants::QUARTER:
                //本季度
                $season = ceil((date('n'))/3);//当月是第几季度
                $begin = mktime(0, 0, 0,$season * 3-3+1,1,date('Y'));
                $end = mktime(23,59,59,$season * 3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y'));
                break;
            case \common\util\Constants::YEAR:
                $begin = strtotime(date('Y').'-1'.'-1');  //本年开始
                $end   =strtotime(date('Y').'-12'.'-31'); // 本年结束
                break;
            case \common\util\Constants::LAST_MONTH:
                $begin = strtotime(date('Y-m-01 00:00:00',strtotime('-1 month')));
                $end = strtotime(date("Y-m-01 23:59:59", strtotime(-date('d').'day')));
                break;
            default :
                //全部
                $begin = 0;
                $end  = time();
                break;
        }
        return ['begin'=>$begin,'end'=>$end];
    }

    /**
     * @param array|object $array 必须是二维数组
     * @param string $column  需要的字段名
     * @param string $childColumn  子数组名称
     * @return array|bool
     */
    public  static function arrayCategory($array,$column,$childColumn = '',$countColumn = ''){
        $result = [];
        $childColumn = !empty($childColumn)?$childColumn:'child'.ucfirst($column).'s';
        $words = array_unique(array_column($array,$column));

        foreach ($words as $key=>$word){
            $childResult = [];
            $total = 0;
            foreach ($array as $index=>$value){
                if($word == $value[$column]){
                    $childResult[] = $value;
                    if(!empty($countColumn) && isset($value[$countColumn])){
                        $total += $value[$countColumn];
                    }
                }
            }
            $result[$key][$column] = $word;
            $result[$key][$childColumn] = $childResult;
            if($total != 0){
                $result[$key]["total"] = $total;
            }
        }
        return array_values($result);
    }

}