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
use yii\base\UserException;
use yii\helpers\ArrayHelper;

/**
 * qcloud
 *
 * @author Ather.Shu Jul 9, 2016 5:34:48 PM
 */
class QCloudManager {
    
    // 群类型
    const IM_TRIBE_TYPE_PRIVATE = "Private";

    const IM_TRIBE_TYPE_PUBLIC = "Public";

    const IM_TRIBE_TYPE_CHATROOM = "ChatRoom";

    const IM_TRIBE_TYPE_AVCHATROOM = "AVChatRoom";
    
    // 群申请权限
    const IM_TRIBE_APPLY_JOIN_OPTION_FREE = "FreeAccess";

    const IM_TRIBE_APPLY_JOIN_OPTION_PERMISSION = "NeedPermission";

    const IM_TRIBE_APPLY_JOIN_OPTION_DISABLE = "DisableApply";
    
    // 消息类型
    const IM_MSG_TYPE_TEXT = "TIMTextElem";

    const IM_MSG_TYPE_CUSTOM = "TIMCustomElem";
    
    // 消息优先级
    const IM_MSG_PRIORITY_HIGH = "High";

    const IM_MSG_PRIORITY_NORMAL = "Normal";

    const IM_MSG_PRIORITY_LOW = "Low";

    const IM_MSG_PRIORITY_LOWEST = "Lowest";
    
    // 消息history类型
    const IM_MSG_HISTORY_C2C = "C2C";

    const IM_MSG_HISTORY_GROUP = "Group";

    private static $_imApi;

    private static $_imSigner;
    
    // 视频相关
    const API_METHOD_GET = "GET";

    const API_METHOD_POST = "POST";

    private static $_vodApi;

    /**
     * 获取本地user映射到im的userinfo
     *
     * @param User $user
     * @return string[]
     */
    public static function getUserImInfo($user, $needSig = true) {
        $uid = Utils::encryptId( $user->id, Constants::ENC_TYPE_USER );
        $rtn = [ 
            "im_uid" => $uid 
        ];
        if( $needSig ) {
            $rtn ["im_sig"] = self::getImSigner()->genSig( $uid );
        }
        return $rtn;
    }

    /**
     * 增加im user
     *
     * @param User $user
     */
    public static function addImUser($user) {
        $imUser = self::getUserImInfo( $user );
        // code msg
        $result = self::getIMApi()->account_import( $imUser ["im_uid"], $user->nickname, \backend\util\Utils::getImgFullUrl( $user->avatar ) );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "增加im账号(uid:{$user->id})错误：" . $error );
        }
    }

    /**
     * 更新im user
     *
     * @param User $user
     */
    public static function updateImUser($user) {
        $imUser = self::getUserImInfo( $user );
        // code msg
        $result = self::getIMApi()->profile_portrait_set2( $imUser ["im_uid"], 
                [ 
                    [ 
                        "Tag" => "Tag_Profile_IM_Nick",
                        "Value" => $user->nickname 
                    ],
                    [ 
                        "Tag" => "Tag_Profile_IM_Image",
                        "Value" => \backend\util\Utils::getImgFullUrl( $user->avatar ) 
                    ] 
                ] );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "更新im账号(uid:{$user->id})错误：" . $error );
        }
    }

    /**
     * 获取im user
     *
     * @param User $user
     */
    public static function getImUser($user) {
        $imUser = self::getUserImInfo( $user );
        // code msg
        $result = self::getIMApi()->profile_portrait_get2( [ 
            $imUser ["im_uid"] 
        ], 
                [ 
                    "Tag_Profile_IM_Nick",
                    "Tag_Profile_IM_AllowType",
                    "Tag_Profile_IM_SelfSignature",
                    "Tag_Profile_IM_Image" 
                ] );
        var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "获取im账号(uid:{$user->id})信息错误：" . $error );
        }
    }

    /**
     * 向某些用户发送im消息
     *
     * @param User $user 发送者
     * @param User[] $targets 接受者，最多500人
     * @param string $content 消息体
     * @param string $msgType
     * @throws UserException
     */
    public static function sendMsg($user, $targets, $content, $msgType = self::IM_MSG_TYPE_CUSTOM) {
        // code msg
        $targetImUsers = [ ];
        foreach ( $targets as $target ) {
            $targetImUsers [] = self::getUserImInfo( $target ) ['im_uid'];
        }
        $result = self::getIMApi()->openim_batch_sendmsg2( $targetImUsers, 
                [ 
                    [ 
                        "MsgType" => $msgType,
                        "MsgContent" => [ 
                            "Data" => $content,
                            "Ext" => $content 
                        ] 
                    ] 
                ], self::getUserImInfo( $user ) ["im_uid"] );
//         var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "批量发送消息({$user->id} to {$content})错误：" . $error );
        }
    }

    /**
     * 增加好友
     *
     * @param User $user
     * @param User $target
     */
    public static function addFriend($user, $target) {
        // code msg
        $result = self::getIMApi()->sns_friend_import( self::getUserImInfo( $user ) ["im_uid"], self::getUserImInfo( $target ) ["im_uid"] );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "增加好友({$user->id} to {$target->id})错误：" . $error );
        }
    }

    /**
     * 删除好友
     *
     * @param User $user
     * @param User $target
     */
    public static function deleteFriend($user, $target) {
        // code msg
        $result = self::getIMApi()->sns_friend_delete( self::getUserImInfo( $user ) ["im_uid"], self::getUserImInfo( $target ) ["im_uid"] );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "删除好友({$user->id} to {$target->id})错误：" . $error );
        }
    }

    /**
     * 增加黑名单
     *
     * @param User $user
     * @param User $target
     */
    public static function addBlacklist($user, $target) {
        // code msg
        $result = self::getIMApi()->sns_blacklist_add( self::getUserImInfo( $user ) ["im_uid"], self::getUserImInfo( $target ) ["im_uid"] );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "增加黑名单({$user->id} to {$target->id})错误：" . $error );
        }
    }

    /**
     * 删除黑名单
     *
     * @param User $user
     * @param User $target
     */
    public static function deleteBlacklist($user, $target) {
        // code msg
        $result = self::getIMApi()->sns_blacklist_delete( self::getUserImInfo( $user ) ["im_uid"], self::getUserImInfo( $target ) ["im_uid"] );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "删除黑名单({$user->id} to {$target->id})错误：" . $error );
        }
    }

    /**
     * 群组禁言某用户
     *
     * @param string $tribeId
     * @param User $user
     */
    public static function tribeProhibit($tribeId, $user, $second) {
        // code msg
        $result = self::getIMApi()->group_forbid_send_msg( $tribeId, self::getUserImInfo( $user ) ["im_uid"], $second );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( ($second ? '' : '取消') . "禁言({$user->id} {$tribeId})错误：" . $error );
        }
    }

    /**
     * 群组取消禁言某用户
     *
     * @param string $tribeId
     * @param User $user
     */
    public static function tribeUnprohibit($tribeId, $user) {
        return self::tribeProhibit( $tribeId, $user, 0 );
    }

    /**
     * 群组踢出某用户
     *
     * @param string $tribeId
     * @param User $user
     * @param bool $silence
     */
    public static function tribeKickout($tribeId, $user, $silence = true) {
        // code msg
        $result = self::getIMApi()->group_delete_group_member( $tribeId, self::getUserImInfo( $user ) ["im_uid"], $silence ? 1 : 0 );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "踢人({$user->id} {$tribeId})错误：" . $error );
        }
    }

    /**
     * 创建im群
     *
     * @param User $user 群主
     * @param string $name 名称
     * @param string $notice 群公告
     * @param int $type 类型
     * @param User[] $members 初始成员
     *        $param string $joinOption 加入权限
     */
    public static function createTribe($user, $name, $notice, $type, $members = null, $joinOption = self::IM_TRIBE_APPLY_JOIN_OPTION_FREE) {
        // code msg
        $imMembers = [ ];
        if( !empty( $members ) ) {
            foreach ( $members as $member ) {
                $imMember = [ 
                    'Member_Account' => self::getUserImInfo( $member ) ["im_uid"],
                    'Role' => 'Admin' 
                ];
                $imMembers [] = $imMember;
            }
        }
        $result = self::getIMApi()->group_create_group2( $type, $name, self::getUserImInfo( $user ) ["im_uid"], 
                [ 
                    'notification' => $notice,
                    'apply_join' => $joinOption 
                ], $imMembers );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "创建群失败({$name})错误：" . $error );
        }
        return $result ['GroupId'];
    }

    /**
     * 解散im群
     *
     * @param string $tribeId
     */
    public static function destroyTribe($tribeId) {
        // code msg
        $result = self::getIMApi()->group_destroy_group( $tribeId );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "解散群组({$tribeId})错误：" . $error );
        }
    }

    /**
     * 发送群系统通知
     *
     * @param string $tribeId
     * @param string $content
     * @param User[] $receivers
     */
    public static function sendTribeNotice($tribeId, $content, $receivers = null) {
        // code msg
        $imUids = [ ];
        if( !empty( $receivers ) ) {
            foreach ( $receivers as $receiver ) {
                $imUids [] = self::getUserImInfo( $receiver ) ["im_uid"];
            }
        }
        $result = self::getIMApi()->group_send_group_system_notification2( $tribeId, $content, $imUids );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "发送群通知({$tribeId})：{$content}错误：" . $error );
        }
    }

    /**
     * 发送群消息
     *
     * @param User $user 发送方（通过这个，可以模拟某个用户发消息）
     * @param string $tribeId 聊天组id
     * @param int $msgType 聊天组id
     * @param string $content 内容
     * @param string $priority 消息优先级
     */
    public static function sendTribeMsg($user, $tribeId, $content, $msgType = self::IM_MSG_TYPE_CUSTOM, $priority = self::IM_MSG_PRIORITY_HIGH) {
        // code msg
        $imUids = [ ];
        if( !empty( $receivers ) ) {
            foreach ( $receivers as $receiver ) {
                $imUids [] = self::getUserImInfo( $receiver ) ["im_uid"];
            }
        }
        $result = self::getIMApi()->group_send_group_msg2( self::getUserImInfo( $user ) ["im_uid"], $tribeId, 
                [ 
                    [ 
                        "MsgType" => $msgType,
                        "MsgContent" => [ 
                            "Data" => $content,
                            "Ext" => $content 
                        ] 
                    ] 
                ], $priority );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "发送群消息({$tribeId})：{$content}错误：" . $error );
        }
    }

    /**
     * 获取群成员列表
     *
     * @param string $tribeId
     * @throws UserException
     */
    public static function getTribeMembers($tribeId) {
        $result = self::getIMApi()->group_get_group_info( $tribeId );
        // var_export( $result );
        if( $result ['ActionStatus'] == "FAIL" ) {
            $error = $result ['ErrorInfo'];
            throw new UserException( "获取群信息({$tribeId})错误：" . $error );
        }
        return $result;
    }

    /**
     * 获取某天的聊天记录文件下载地址
     *
     * @param string $date 如20150612
     * @param string $hisotryType
     * @throws UserException
     * @return string[]
     */
    public static function getHistoryFile($date, $hisotryType) {
        $urls = [ ];
        for($hour = 0; $hour < 24; $hour++) {
            $result = self::getIMApi()->comm_rest( "open_msg_svc", "get_history", 
                    [ 
                        "ChatType" => $hisotryType,
                        "MsgTime" => $date . ($hour < 10 ? ("0" . $hour) : $hour) 
                    ] );
            var_export( $result );
            if( $result ['ActionStatus'] == "FAIL" ) {
                $error = $result ['ErrorInfo'];
                echo "获取聊天记录文件({$date}{$hour})：{$hisotryType}错误：" . $error . "\n";
                continue;
                // throw new UserException( "获取聊天记录文件({$date}{$hour})：{$hisotryType}错误：" . $error );
            }
            $urls = array_merge( $urls, ArrayHelper::getColumn( $result ['File'], 'URL' ) );
        }
        return $urls;
    }

    private static function getImSigner() {
        if( self::$_imSigner == null ) {
            // im配置
            $imConfig = \Yii::$app->params ['tencent'] ['im'];
            
            $signer = new \TLSSigAPI();
            $signer->SetAppid( $imConfig ['sdkappid'] );
            $privatekey = file_get_contents( \TimRestAPI::$PATH . $imConfig ["private_pem_path"] );
            $publicKey = file_get_contents( \TimRestAPI::$PATH . $imConfig ["public_pem_path"] );
            $signer->SetPrivateKey( $privatekey );
            $signer->SetPublicKey( $publicKey );
            self::$_imSigner = $signer;
            // $sig = $signer->genSig('user1');
            // $result = $signer->verifySig($sig, 'user1', $init_time, $expire_time, $error_msg);
            // 用exec方式获取user sign
            // if( is_64bit() ) {
            // if( PATH_SEPARATOR == ':' ) {
            // $signature = "/signature/linux-signature64";
            // } else {
            // $signature = "\\signature\\windows-signature64.exe";
            // }
            // } else {
            // if( PATH_SEPARATOR == ':' ) {
            // $signature = "/signature/linux-signature32";
            // } else {
            // $signature = "\\signature\\windows-signature32.exe";
            // }
            // }
            // $ret = self::getIMApi()->generate_user_sig( $uid, '36000', TimRestAPI::$PATH . $imConfig ["private_pem_path"], TimRestAPI::$PATH . $signature );
            // if( $ret == null || strstr( $ret [0], "failed" ) ) {
            // throw new UserException( "获取usrsig失败, 请确保Tencent Im配置信息正确" );
            // }
        }
        return self::$_imSigner;
    }

    private static function getIMApi() {
        if( self::$_imApi == null ) {
            // 用管理员账号调用rest api
            // https://www.qcloud.com/doc/product/269/4029
            // 调用REST API时请务必使用APP管理员账号，否则会导致不必要的调用错误。
            $adminUid = 1;
            $uid = Utils::encryptId( $adminUid, Constants::ENC_TYPE_USER );
            // im配置
            $imConfig = \Yii::$app->params ['tencent'] ['im'];
            $sdkappid = $imConfig ['sdkappid'];
            
            $api = new \TimRestAPI();
            $api->init( $imConfig ['sdkappid'], $uid );
            // 生成签名
            $api->set_user_sig( self::getImSigner()->genSig( $uid ) );
            self::$_imApi = $api;
        }
        
        return self::$_imApi;
    }
    //
    // 以下视频直播相关api
    //
    
    /**
     * 获取录播视频播放信息
     * 
     * @param string $vid
     * @throws UserException
     * @return array @see https://www.qcloud.com/doc/api/257/%E8%8E%B7%E5%8F%96%E5%BD%95%E6%92%AD%E8%A7%86%E9%A2%91%E6%92%AD%E6%94%BE%E4%BF%A1%E6%81%AF-%E4%BA%92%E5%8A%A8%E7%9B%B4%E6%92%AD%E7%94%A8%E6%88%B7%E4%B8%93%E7%94%A8
     */
    public static function describeRecordPlayInfo($vid) {
        $api = self::getVodApi();
        $result = $api->DescribeRecordPlayInfo( [ 
            'vid' => $vid 
        ] );
        if( !$result ) {
            $error = $api->getError();
            throw new UserException( "获取录制视频信息({$vid})错误：" . $error->getCode() . ' ' . $error->getMessage() );
        }
        return $result ['fileSet'];
    }
    
    /**
     * 获取录播视频播放信息
     *
     * @param string $fileId
     * @throws UserException
     */
    public static function describeVodPlayUrls($fileId) {
        $api = self::getVodApi();
        $result = $api->DescribeVodPlayUrls( [
            'fileId' => $fileId
        ] );
        if( !$result ) {
            $error = $api->getError();
            throw new UserException( "获取视频信息({$fileId})错误：" . $error->getCode() . ' ' . $error->getMessage() );
        }
        return $result ['playSet'];
    }

    /**
     * 删除视频文件
     * 
     * @param string $fileId
     * @throws UserException
     * @return boolean
     */
    public static function deleteVodFile($fileId) {
        $api = self::getVodApi();
        $result = $api->DeleteVodFile( [ 
            'fileId' => $fileId,
            'priority' => 0 
        ] );
        if( !$result ) {
            $error = $api->getError();
            throw new UserException( "删除视频({$fileId})错误：" . $error->getCode() . ' ' . $error->getMessage() );
        }
        return true;
    }

    private static function getVodApi($method = self::API_METHOD_GET) {
        if( self::$_vodApi == null ) {
            $config = [ 
                'SecretId' => \Yii::$app->params ['tencent'] ['apiSecretId'],
                'SecretKey' => \Yii::$app->params ['tencent'] ['apiSecretKey'],
                'RequestMethod' => $method,
                'DefaultRegion' => 'gz' 
            ];
            self::$_vodApi = \QcloudApi::load( \QcloudApi::MODULE_VOD, $config );
        }
        return self::$_vodApi;
    }
}