<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace common\util;

use common\models\ConsumeDetail;
use common\models\Order;
use yii\base\UserException;

/**
 * pay util
 *
 * @author Ather.Shu Apr 7, 2016 1:33:48 PM
 */
class PayUtil {

    /**
     * 生成微信订单，返回微信订单
     *
     * @param string $platform 发起支付的渠道
     * @param string $title 付款标题
     * @param string $tradeNo 单号
     * @param string $remark 备注
     * @param int $amount 元
     * @param string $openId 如果微信js支付，需要传入支付账号的openid
     * @throws UserException
     * @return array ['prepay_id']
     */
    public static function wxOrder($platform, $title, $tradeNo, $remark, $amount, $openId = null) {
        $tradeType = ($platform == Constants::PLATFORM_XCH || Constants::PLATFORM_WEB ? "JSAPI" : "APP");
        \WxPayConfig::initConfig( $platform );

        // 统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody( $title );
        $input->SetAttach( $remark );
        $input->SetOut_trade_no( $tradeNo );
        $input->SetTotal_fee( $amount * 100 );
        $input->SetTime_start( date( "YmdHis" ) );
        $input->SetTime_expire( date( "YmdHis", time() + 1800 ) );
        // $input->SetProduct_id( $productId );
        // $input->SetGoods_tag("test");
        $input->SetNotify_url( \Yii::$app->params ['frontUrl'] . "/pay/notify/" . Constants::PAY_TYPE_WX . "/" . $platform );
        $input->SetTrade_type( $tradeType );
        
        // 如果是js调用，获取用户openid
        if( ($platform == Constants::PLATFORM_WEB || $platform == Constants::PLATFORM_XCH ) && isset( $openId ) ) {
            $input->SetOpenid( $openId );
        }
        try {
            $order = \WxPayApi::unifiedOrder( $input );
            // var_export($order);exit;
            if( $order ['return_code'] == 'FAIL' ) {
                throw new UserException( $order ['return_msg'] );
            } else if( $order ['result_code'] == 'FAIL' ) {
                throw new UserException( $order ['err_code_des'] );
            }
        } catch ( \Exception $e ) {
            throw $e;
        }
        return $order;
    }

    /**
     * 发放微信红包（只能是微信公众号，不能是微信开发者app）
     *
     * @param string $tradeNo 商户订单号
     * @param string $sender 红包发送者名称
     * @param string $openId 接受红包的用户openid
     * @param string $amount 付款金额，元
     * @param string $wish 红包祝福语
     * @param string $activity 活动名称
     * @param string $remark 备注信息
     * @throws UserException
     * @throws Exception
     * @return array ['send_listid']
     */
    public static function wxRedpack($tradeNo, $sender, $openId, $amount, $wish, $activity, $remark) {
        // 红包只能web平台发放
        \WxPayConfig::initConfig( Constants::PLATFORM_WEB );
        $input = new \WxPayRedpack();
        $input->SetMch_billno( $tradeNo );
        $input->SetSend_name( $sender );
        $input->SetRe_openid( $openId );
        $input->SetTotal_amount( $amount * 100 );
        $input->SetTotal_num( 1 );
        $input->SetWishing( $wish );
        $input->SetAct_name( $activity );
        $input->SetRemark( $remark );
        
        try {
            $result = \WxPayApi::redpack( $input );
            // var_export($result);exit;
            if( $result ['return_code'] == 'FAIL' ) {
                throw new UserException( $result ['return_msg'] );
            } else if( $result ['result_code'] == 'FAIL' ) {
                throw new UserException( $result ['err_code_des'] );
            }
        } catch ( \Exception $e ) {
            throw $e;
        }
        return $result;
    }

    /**
     * 生成支付宝订单
     *
     * @param string $platform 发起支付的渠道
     * @param string $title 付款标题
     * @param string $tradeNo 单号
     * @param string $remark 备注
     * @param int $amount 元
     * @return string
     */
    public static function zfbOrder($platform, $title, $tradeNo, $remark, $amount) {
        $alipay_config = \AliPayConfig::getConfig();
        // 通用参数
        $params = [ 
            "partner" => $alipay_config ['partner'],
            "seller_id" => $alipay_config ['seller_id'],
            "_input_charset" => trim( strtolower( $alipay_config ['input_charset'] ) ),
            "payment_type" => 1,
            "it_b_pay" => "30m",
            "notify_url" => \Yii::$app->params ['frontUrl'] . "/pay/notify/" . Constants::PAY_TYPE_ZFB . "/" . $platform,
            "out_trade_no" => $tradeNo,
            "subject" => $title,
            "total_fee" => $amount,
            "body" => $remark 
        ];
        // 不同支付平台的特定参数
        if( $platform == Constants::PLATFORM_WEB ) {
            $params ["service"] = "alipay.wap.create.direct.pay.by.user";
            $params ["show_url"] = \Yii::$app->params ['frontUrl'];
            // 支付完成回调地址
            $params ["return_url"] = \Yii::$app->params ['frontUrl'] . '/h5/me_order.html';
        } else {
            $params ["service"] = "mobile.securitypay.pay";
        }
        // web生成url或者app生成参数数组
        $alipaySubmit = new \AlipaySubmit( $alipay_config );
        if( $platform == Constants::PLATFORM_WEB ) {
            return $alipaySubmit->alipay_gateway_new . $alipaySubmit->buildRequestParaToString( $params );
        } else {
            return $alipaySubmit->buildRequestParaToString( $params );
        }
    }

    /**
     * 生成连连支付订单
     *
     * @param string $userId 用户id
     * @param string $riskItem 风控参数
     * @param string $platform 发起支付的渠道
     * @param string $title 付款标题
     * @param string $tradeNo 单号
     * @param string $remark 备注
     * @param int $amount 元
     * @return string
     */
    public static function llOrder($userId, $riskItem, $platform, $title, $tradeNo, $remark, $amount, $tradeType = \LLPayConfig::TRADE_TYPE_REAL_GOODS) {
        $llpay_config = \LLPayConfig::getConfig();
        // 通用参数
        $params = [ 
            "oid_partner" => trim( $llpay_config ['oid_partner'] ),
            "sign_type" => trim( $llpay_config ['sign_type'] ),
            // 订单有效期30分钟
            "valid_order" => "30",
            "user_id" => "$userId",
            // 商户业务类型 虚拟商品：101001 实物商品：109001 账户充值：108001
            "busi_partner" => $tradeType,
            "no_order" => $tradeNo,
            "dt_order" => date( 'YmdHis', time() ),
            "name_goods" => $title,
            "info_order" => $remark,
            "money_order" => $amount,
            "notify_url" => \Yii::$app->params ['frontUrl'] . "/pay/notify/" . Constants::PAY_TYPE_LL . "/" . $platform,
            "risk_item" => $riskItem 
        ];
        // 不同支付平台的特定参数
        // 请求应用标识 app_request 1-Android 2-ios 3-WAP
        if( $platform == Constants::PLATFORM_WEB ) {
            $params ["app_request"] = "3";
            $params ["risk_item"] = preg_replace( '/"/', '\"', $riskItem );
            // 支付完成回调地址
            $params ["url_return"] = \Yii::$app->params ['frontUrl'] . '/h5/me_order.html';
        } else {
            $params ["app_request"] = $platform == Constants::PLATFORM_ANDROID ? "1" : "2";
        }
        // web生成url或者app生成参数数组
        $llpaySubmit = new \LLpaySubmit( $llpay_config );
        if( $platform == Constants::PLATFORM_WEB ) {
            return $llpaySubmit->llpay_gateway_new . "?req_data=" . rawurlencode( $llpaySubmit->buildRequestParaToString( $params ) );
        } else {
            return $llpaySubmit->buildRequestPara( $params );
        }
    }

    /**
     * 检查支付异步回调
     *
     * @param string $sn 订单编号或者充值编号
     * @param number $fee 金额
     * @param string $extraInfo 支付额外信息
     * @param string $payTradeNo 第三方交易id
     * @param int $payType 支付类型
     * @param int $payPlatform 支付发起平台
     * @throws UserException
     * @return boolean
     */
    public static function checkNotify($sn, $fee, $extraInfo, $payTradeNo, $payType, $payPlatform) {
        // 是否充值 CHARGE_EXTRA_FLAG
        $trans = \Yii::$app->db->beginTransaction();
        try{
            $order = Order::findOne([
                'sn'=>$sn
            ]);
            if(!$order){
                return false;
            }
            if($order->status != Order::STATUS_PAY_STATUS_NONE){
                return  false;
            }
            if($payType != Order::PAY_TYPE_WX){
                return false;
            }
            $order->status = Order::STATUS_PAY_STATUS_SUCCESS;
            $order->pay_amount = $fee;
            $order->trade_no = $payTradeNo;
            $order->save();
            $order->user->balance += $order->pay_amount;
            $order->user->save();

            //添加充值明细
            $log = new ConsumeDetail();
            $log->user_id = $order->user_id;
            $log->is_integral = Constants::BOOL_FALSE;
            $log->type = ConsumeDetail::TYPE_CHARGE;
            $log->child_type = ConsumeDetail::CHILD_TYPE_ONLINE_PAY;
            $log->price = + $order->pay_amount;
            $log->remark = "微信充值";
            $log->save();

            $trans->commit();
        }catch(\Exception $exception){
            $trans->rollBack();
            throw $exception;
        }
        echo "ok";
    }

    /**
     * 申请退款
     *
     * @param int $payType 原始第三方支付类型
     * @param int $payPlatform 原始第三方支付发起平台
     * @param string $payTradeNo 原始第三方交易id
     * @param string $refundNo 退款单号（统一为支付宝格式：退款日期(8 位当天 日期)+流水号(3~24 位, 流水号可以接受数字或英文字符,建议使用数字,但不可接受“000”)）<br>
     *        对订单退款：date("Ymd") . "order" . $order->id<br>
     *        支付遇到问题（金额不等于需支付金额）自动退款：date("Ymd") . "nf" . $sn . rand(1, 100)
     * @param number $totalAmount 原始交易总金额，元
     * @param number $refundAmount 退款金额，元
     * @param string $reason 退款原因
     */
    public static function refund($payType, $payPlatform, $payTradeNo, $refundNo, $totalAmount, $refundAmount, $reason = "协商退款") {
        if( $payType == Constants::PAY_TYPE_ZFB ) {
            $alipay_config = \AliPayConfig::getConfig();
            // 退款就不异步回调了
            // 两个回调，notify_url 是退到支付宝完成时的回调
            // dback_notify_url 是从支付宝退到银行卡完成的回调
            $params = [ 
                "service" => "refund_fastpay_by_platform_nopwd",
                "partner" => $alipay_config ['partner'],
                "_input_charset" => trim( strtolower( $alipay_config ['input_charset'] ) ),
                // "notify_url" => \Yii::$app->params ['frontUrl'] . "/pay/refund-notify/" . Constants::PAY_TYPE_ZFB,
                // "dback_notify_url" => "",
                "batch_no" => $refundNo,
                "refund_date" => date( "Y-m-d H:i:s" ),
                "batch_num" => 1,
                "detail_data" => "{$payTradeNo}^{$refundAmount}^{$reason}" 
            ];
            // 建立请求
            try {
                $alipaySubmit = new \AlipaySubmit( $alipay_config );
                $result = Utils::xml2Array( $alipaySubmit->buildRequestHttp( $params ) );
                // var_export( $result );
                if( !empty( $result ['error'] ) ) {
                    throw new UserException( $result ['error'] );
                }
            } catch ( \Exception $e ) {
                throw $e;
            }
        } else if( $payType == Constants::PAY_TYPE_WX ) {
            \WxPayConfig::initConfig( $payPlatform );
            // 退款
            $input = new \WxPayRefund();
            $input->SetTransaction_id( $payTradeNo );
            $input->SetOut_refund_no( $refundNo );
            $input->SetTotal_fee( $totalAmount * 100 );
            $input->SetRefund_fee( $refundAmount * 100 );
            $input->SetOp_user_id( 1 );
            
            try {
                $refund = \WxPayApi::refund( $input );
                // var_export( $refund );
                if( $refund ['return_code'] == 'FAIL' ) {
                    throw new UserException( $refund ['return_msg'] );
                } else if( $refund ['result_code'] == 'FAIL' ) {
                    throw new UserException( $refund ['err_code_des'] );
                }
            } catch ( \Exception $e ) {
                throw $e;
            }
        } else if( $payType == Constants::PAY_TYPE_LL ) {
            $llpay_config = \LLPayConfig::getConfig();
            // 通用参数
            $params = [ 
                "oid_partner" => trim( $llpay_config ['oid_partner'] ),
                "sign_type" => trim( $llpay_config ['sign_type'] ),
                "no_refund" => $refundNo,
                "dt_refund" => date( 'YmdHis', time() ),
                "money_refund" => $refundAmount,
                "oid_paybill" => $payTradeNo,
                "notify_url" => \Yii::$app->params ['frontUrl'] . "/pay/refund-notify/" . Constants::PAY_TYPE_LL 
            ];
            // 建立请求
            try {
                $llpaySubmit = new \LLpaySubmit( $llpay_config );
                $result = json_decode( $llpaySubmit->buildRequestJSON( $params, $llpaySubmit->llpay_refund_gateway ), true );
                // var_export( $result );
                if( $result ['ret_code'] != "0000" ) {
                    throw new UserException( $result ['ret_msg'] );
                }
            } catch ( \Exception $e ) {
                throw $e;
            }
        }
    }
}