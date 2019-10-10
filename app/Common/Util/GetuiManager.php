<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace common\util;

use common\models\User;
use yii\helpers\ArrayHelper;

/**
 * 个推manager
 *
 * @author Ather.Shu Jan 20, 2015 10:42:28 AM
 */
class GetuiManager {

    /**
     * 推送给某具体个推client
     *
     * @param string $gtClientId
     * @param array $data
     */
    public static function pushToGTClient($gtClientId, $data) {
        $igt = new \IGeTui( \Yii::$app->params ['getui'] ['host'], \Yii::$app->params ['getui'] ['appKey'], 
                \Yii::$app->params ['getui'] ['masterSecret'] );
        $igt->debug = false;
        // 消息
        $template = self::buildGTTpl( $data );
        // 个推信息体
        $message = new \IGtSingleMessage();
        $message->set_isOffline( true ); // 是否离线
        $message->set_offlineExpireTime( 3600 * 12 * 1000 ); // 离线时间
        $message->set_data( $template ); // 设置推送消息类型
        $message->set_PushNetWorkType( 0 ); // 设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
                                            // 目标设备
        $target = new \IGtTarget();
        $target->set_appId( \Yii::$app->params ['getui'] ['appId'] );
        $target->set_clientId( $gtClientId );
        
        $igt->pushMessageToSingle( $message, $target );
    }

    /**
     * 推送给用户
     *
     * @param int|array $uids 目标用户uids
     * @param array $data 数据，请使用genData()生成
     */
    public static function pushToUsers($uids, $data) {
        if( empty( $uids ) ) {
            return;
        }
        if( !is_array( $uids ) ) {
            $uids = [ 
                $uids 
            ];
        }
        
        $cids = ArrayHelper::getColumn( 
                User::find()->joinWith('userDevices')->where( [ 
                    'in',
                    'user.id',
                    $uids 
                ] )->andWhere( [ 
                    'is not',
                    'user_device.push_cid',
                    null 
                ] )->andWhere( [ 
                    'user_device.loggedout' => 0
                ] )->all(), 'push_cid' );
        if( empty( $cids ) ) {
            return;
        }
        $igt = new \IGeTui( \Yii::$app->params ['getui'] ['host'], \Yii::$app->params ['getui'] ['appKey'], 
                \Yii::$app->params ['getui'] ['masterSecret'] );
        $igt->debug = false;
        // 消息
        $template = self::buildGTTpl( $data );
        // 个推信息体
        $message = new \IGtListMessage();
        $message->set_isOffline( true ); // 是否离线
        $message->set_offlineExpireTime( 3600 * 12 * 1000 ); // 离线时间
        $message->set_data( $template ); // 设置推送消息类型
        $message->set_PushNetWorkType( 0 ); // 设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        $contentId = $igt->getContentId( $message );
        // 目标设备
        $targetList = [ ];
        
        foreach ( $cids as $cid ) {
            $target = new \IGtTarget();
            $target->set_appId( \Yii::$app->params ['getui'] ['appId'] );
            $target->set_clientId( $cid );
            array_push( $targetList, $target );
        }
        $rtn = $igt->pushMessageToList( $contentId, $targetList );
        // var_export($rtn);
    }

    /**
     * 推送给所有人
     *
     * @param array $data 数据
     * @param string $phoneType 手机类型，null代表所有，ANDROID，IOS
     */
    public static function pushToAll($data, $phoneType = NULL) {
        $igt = new \IGeTui( \Yii::$app->params ['getui'] ['host'], \Yii::$app->params ['getui'] ['appKey'], 
                \Yii::$app->params ['getui'] ['masterSecret'] );
        $igt->debug = false;
        // 消息
        $template = self::buildGTTpl( $data );
        // 个推信息体
        $message = new \IGtAppMessage();
        $message->set_isOffline( true ); // 是否离线
        $message->set_offlineExpireTime( 3600 * 12 * 1000 ); // 离线时间
        $message->set_data( $template ); // 设置推送消息类型
        $message->set_PushNetWorkType( 0 ); // 设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        $message->set_appIdList( array (
            \Yii::$app->params ['getui'] ['appId'] 
        ) );
        if( $phoneType != null ) {
            $message->set_phoneTypeList( [ 
                $phoneType 
            ] );
        }
        // $message->set_provinceList(array('浙江','北京','河南'));
        // $message->set_tagList(array('开心'));
        
        $rtn = $igt->pushMessageToApp( $message );
        // var_export($rtn);
    }

    private static function buildGTTpl(&$data) {
        $iosTitle = empty( $data ['summary'] ) ? $data ['title'] : $data ['summary'];
        if( isset( $data ['iosTitle'] ) ) {
            $iosTitle = $data ['iosTitle'];
            unset( $data ['iosTitle'] );
        }
        $template = new \IGtTransmissionTemplate();
        $template->set_appId( \Yii::$app->params ['getui'] ['appId'] );
        $template->set_appkey( \Yii::$app->params ['getui'] ['appKey'] );
        $template->set_transmissionType( 2 );
        $template->set_transmissionContent( json_encode( $data ) );
        $template->set_pushInfo( "", 1, $iosTitle, null, null, null, null, null );
        return $template;
    }
}