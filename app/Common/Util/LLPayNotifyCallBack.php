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
 * 连连支付回调
 *
 * @author Ather.Shu Apr 8, 2016 3:20:41 PM
 */
class LLPayNotifyCallBack {

    public static function verify($platform) {
        // 计算得出通知验证结果
        $llpay_config = \LLPayConfig::getConfig();
        
        $llpayNotify = new \LLpayNotify( $llpay_config );
        $llpayNotify->verifyNotify();
        
        file_put_contents( \Yii::getAlias( "@frontend/runtime/logs/llpay.log" ), 
                date( "Y-m-d H:i:s" ) . "\n" . var_export( file_get_contents( "php://input" ), true ) . "\n\n", FILE_APPEND );
        
        if( $llpayNotify->result ) {
            // 验证成功
            // 获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
            // 商户订单号
            $sn = $llpayNotify->notifyResp ['no_order'];
            // 金额
            $fee = $llpayNotify->notifyResp ['money_order'];
            // 备注
            $remark = $llpayNotify->notifyResp ['info_order'];
            // 连连支付单号
            $trade_no = $llpayNotify->notifyResp ['oid_paybill'];
            // 支付结果，SUCCESS：为支付成功
            $result_pay = $llpayNotify->notifyResp ['result_pay'];
            
            if( $result_pay == "SUCCESS" ) {
                // 请在这里加上商户的业务逻辑程序代(更新订单状态、入账业务)
                // ——请根据您的业务逻辑来编写程序——
                // payAfter($llpayNotify->notifyResp);
                if( !PayUtil::checkNotify( $sn, $fee, $remark, $trade_no, Constants::PAY_TYPE_LL, $platform ) ) {
                    die( "{'ret_code':'9999','ret_msg':'支付处理失败'}" );
                }
            }
            die( "{'ret_code':'0000','ret_msg':'交易成功'}" ); // 请不要修改或删除
                                                               // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            // 验证失败
            die( "{'ret_code':'9999','ret_msg':'验签失败'}" );
            // 调试用，写文本函数记录程序运行情况是否正常
            // logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
}