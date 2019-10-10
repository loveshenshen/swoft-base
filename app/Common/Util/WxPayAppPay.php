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
 * weixin app pay发起支付
 * @author Ather.Shu Apr 7, 2016 3:55:46 PM
 */
class WxPayAppPay extends \WxPayDataBase {

    /**
     * 获取app发起支付需要的数据
     * 
     * @param array $orderResult ['prepay_id']
     */
    public function getPayParameters($orderResult) {
        $this->values ['appid'] = \WxPayConfig::$APPID;
        $this->values ['partnerid'] = \WxPayConfig::$MCHID;
        $this->values ['prepayid'] = $orderResult ['prepay_id'];
        $this->values ['package'] = "Sign=WXPay";
        $this->values ['noncestr'] = \WxPayApi::getNonceStr();
        $this->values ['timestamp'] = time();
        $this->values ['sign'] = $this->MakeSign();
        
        return $this->GetValues();
    }
}