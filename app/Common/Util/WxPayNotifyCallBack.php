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
 * 微信支付成功回调
 *
 * @author Ather.Shu Jul 26, 2015 11:57:10 PM
 */
class WxPayNotifyCallBack extends \WxPayNotify {

    private $_platform;

    function __construct($platform) {
        $this->_platform = $platform;
        \WxPayConfig::initConfig($platform);
    }
    // 查询订单
    public function Queryorder($transaction_id) {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id( $transaction_id );
        $result = \WxPayApi::orderQuery( $input );
        if( array_key_exists( "return_code", $result ) && array_key_exists( "result_code", $result ) && $result ["return_code"] == "SUCCESS" &&
                 $result ["result_code"] == "SUCCESS" ) {
            return true;
        }
        return false;
    }
    
    // 重写回调处理函数
    public function NotifyProcess($data, &$msg) {
        file_put_contents( \Yii::getAlias( "@frontend/runtime/logs/wxpay.log" ), date("Y-m-d H:i:s") . "\n" . var_export( $data, true ) . "\n\n", FILE_APPEND );
        
        $notfiyOutput = array ();
        
        if( !array_key_exists( "transaction_id", $data ) ) {
            $msg = "输入参数不正确";
            return false;
        }
        // 查询订单，判断订单真实性
        if( !$this->Queryorder( $data ["transaction_id"] ) ) {
            $msg = "订单查询失败";
            return false;
        }
        // 获取订单号
        $sn = $data ['out_trade_no'];
        if( empty( $sn ) ) {
            return false;
        }
        // 附加信息（判断是否充值）
        $remark = $data ['attach'];
        if( empty( $remark ) ) {
            return false;
        }
        // 金额
        $fee = $data ['total_fee'] / 100;
        
        return PayUtil::checkNotify( $sn, $fee, $remark, $data ['transaction_id'], Constants::PAY_TYPE_WX, $this->_platform );
    }
}