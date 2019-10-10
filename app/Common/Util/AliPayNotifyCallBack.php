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
 * 支付宝回调
 *
 * @author Ather.Shu Apr 8, 2016 3:20:41 PM
 */
class AliPayNotifyCallBack {

    public static function verify($platform) {
        // 交易状态TRADE_SUCCESS的通知触发条件是商户签约的产品支持退款功能 的前提下,买家付款成功;
        // 交易状态TRADE_FINISHED的通知触发条件是商户签约的产品不支持退款功 能的前提下,买家付款成功;或者,商户签约的产品支持退款功能的前提下, 交易已经成功并且已经超过可退款期限;
        // 计算得出通知验证结果
        $alipay_config = \AliPayConfig::getConfig();
        $alipayNotify = new \AlipayNotify( $alipay_config );
        $verify_result = $alipayNotify->verifyNotify();
        
        file_put_contents( \Yii::getAlias( "@frontend/runtime/logs/alipay.log" ), date("Y-m-d H:i:s") . "\n" . var_export( $_POST, true ) . "\n\n", FILE_APPEND );
        
        if( $verify_result ) {
            // 验证成功
            // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // 请在这里加上商户的业务逻辑程序代
            
            // ——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            
            // 获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            
            // 商户订单号
            $sn = $_POST ['out_trade_no'];
            // 金额
            $fee = $_POST ['total_fee'];
            // 备注
            $remark = $_POST ['body'];
            // 支付宝交易号
            $trade_no = $_POST ['trade_no'];
            // 交易状态
            $trade_status = $_POST ['trade_status'];
            
            if( $trade_status == 'TRADE_FINISHED' ) {
                // 判断该笔订单是否在商户网站中已经做过处理
                // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                // 请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                // 如果有做过处理，不执行商户的业务程序
                
                // 注意：
                // 退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                
                // 调试用，写文本函数记录程序运行情况是否正常
                // logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if( $trade_status == 'TRADE_SUCCESS' ) {
                // 判断该笔订单是否在商户网站中已经做过处理
                // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                // 请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                // 如果有做过处理，不执行商户的业务程序
                if( !PayUtil::checkNotify( $sn, $fee, $remark, $trade_no, Constants::PAY_TYPE_ZFB, $platform ) ) {
                    echo "fail";
                }
                // 注意：
                // 付款完成后，支付宝系统发送该交易状态通知
                
                // 调试用，写文本函数记录程序运行情况是否正常
                // logResult($order->id);
            }
            
            // ——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            
            echo "success"; // 请不要修改或删除
                                
            // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            // 验证失败
            echo "fail";
            
            // 调试用，写文本函数记录程序运行情况是否正常
            // logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
}