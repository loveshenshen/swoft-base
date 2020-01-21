<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace app\Common\Util;

/**
 * 云片短信
 * @author Ather.Shu Jul 16, 2015 7:24:48 PM
 */
class YunPianManager {

    private static $apikey = "67e47eeae296a954cbab963a5fdb5cfb";

    /**
     * 发送短息
     * 
     * @param string $mobile
     * @param string $content
     * @return boolean|string
     */
    public static function sendSMS($mobile, $content) {
        if(empty($mobile) ) {
            return false;
        }
        $data = json_decode( self::send_sms( self::$apikey, $content, $mobile ), true );
        if( $data ['code'] == 0 ) {
            return true;
        }
        else {
            return $data ['msg'] . "," . $data ['detail'];
        }
        // 请用自己的apikey代替 $mobile = "xxxxxxxxxxx"; //请用自己的手机号代替
        // 对应默认模板 【#company#】您的验证码是#code#
        // $tpl_id = 1;
        // $tpl_value = "#company#=云片网&#code#=1234";
        // self::tpl_send_sms(self::$apikey, $tpl_id, $tpl_value, $mobile);
    }

    /**
     * 通用接口发短信
     * apikey 为云片分配的apikey
     * text 为短信内容
     * mobile 为接受短信的手机号
     */
    private static function send_sms($apikey, $text, $mobile) {
        $url = "http://yunpian.com/v1/sms/send.json";
        $encoded_text = urlencode( "$text" );
        $post_string = "apikey=$apikey&text=$encoded_text&mobile=$mobile";
        return self::sock_post( $url, $post_string );
    }

    /**
     * 模板接口发短信
     * apikey 为云片分配的apikey
     * tpl_id 为模板id
     * tpl_value 为模板值
     * mobile 为接受短信的手机号
     */
    private static function tpl_send_sms($apikey, $tpl_id, $tpl_value, $mobile) {
        $url = "http://yunpian.com/v1/sms/tpl_send.json";
        $encoded_tpl_value = urlencode( "$tpl_value" ); // tpl_value需整体转义
        $post_string = "apikey=$apikey&tpl_id=$tpl_id&tpl_value=$encoded_tpl_value&mobile=$mobile";
        return self::sock_post( $url, $post_string );
    }

    /**
     * url 为服务的url地址
     * query 为请求串
     */
    private static function sock_post($url, $query) {
        $data = "";
        $info = parse_url( $url );
        $fp = fsockopen( $info ["host"], 80, $errno, $errstr, 30 );
        if( !$fp ) {
            return $data;
        }
        $head = "POST " . $info ['path'] . " HTTP/1.0\r\n";
        $head .= "Host: " . $info ['host'] . "\r\n";
        $head .= "Referer: http://" . $info ['host'] . $info ['path'] . "\r\n";
        $head .= "Content-type: application/x-www-form-urlencoded\r\n";
        $head .= "Content-Length: " . strlen( trim( $query ) ) . "\r\n";
        $head .= "\r\n";
        $head .= trim( $query );
        $write = fputs( $fp, $head );
        $header = "";
        while ( $str = trim( fgets( $fp, 4096 ) ) ) {
            $header .= $str;
        }
        while ( !feof( $fp ) ) {
            $data .= fgets( $fp, 4096 );
        }
        return $data;
    }
}