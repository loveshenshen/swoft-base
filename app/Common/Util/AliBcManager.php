<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace app\Common\Util;

use common\models\User;
use yii\base\UserException;

/**
 * 阿里百川的manager
 *
 * @author Ather.Shu Apr 16, 2016 12:17:32 PM
 */
class AliBcManager {
    
    // ----------
    // 以下im相关
    // ----------
    const IM_MSG_TYPE_TEXT = "0";

    const IM_MSG_TYPE_CUSTOM = "17";

    const IM_TRIBE_TYPE_GROUP = "0";

    const IM_TRIBE_TYPE_DISCUSS = "1";

    /**
     * 阿里百川的topclient
     *
     * @var \TopClient
     */
    private static $_client;

    /**
     * 获取本地user映射到im的userinfo
     *
     * @param User $user
     * @return string[]
     */
    public static function getUserImInfo($user) {
        return [ 
            "im_uid" => "im" . Utils::encryptId( $user->id, Constants::ENC_TYPE_USER ),
            "im_pwd" => md5( "im" . $user->id . "pwd" ) 
        ];
    }

    /**
     * 增加im user
     *
     * @param User $user
     */
    public static function addImUser($user) {
        $request = new \OpenimUsersAddRequest();
        $userinfos = new \Userinfos();
        $imUser = self::getUserImInfo( $user );
        $userinfos->userid = $imUser ["im_uid"];
        $userinfos->password = $imUser ["im_pwd"];
        $userinfos->nick = $user->nickname;
        $userinfos->icon_url = \backend\util\Utils::getImgFullUrl( $user->avatar );
        $request->setUserinfos( json_encode( $userinfos ) );
        // code msg
        $result = self::getClient()->execute( $request );
        // var_export( $result );
        if( isset( $result->code ) || !empty( $result->fail_msg->string ) ) {
            $error = isset( $result->code ) ? $result->msg : $result->fail_msg->string [0];
            throw new UserException( "增加im账号(uid:{$user->id})错误：" . $error );
        }
    }

    /**
     * 更新im user
     *
     * @param User $user
     */
    public static function updateImUser($user) {
        $request = new \OpenimUsersUpdateRequest();
        $userinfos = new \Userinfos();
        $imUser = self::getUserImInfo( $user );
        $userinfos->userid = $imUser ["im_uid"];
        $userinfos->nick = $user->nickname;
        $userinfos->icon_url = \backend\util\Utils::getImgFullUrl( $user->avatar );
        $request->setUserinfos( json_encode( $userinfos ) );
        // code msg
        $result = self::getClient()->execute( $request );
        // var_export( $result );
        if( isset( $result->code ) || !empty( $result->fail_msg->string ) ) {
            $error = isset( $result->code ) ? $result->msg : $result->fail_msg->string [0];
            throw new UserException( "更新im账号(uid:{$user->id})错误：" . $error );
        }
    }

    /**
     * 获取im user
     *
     * @param User $user
     */
    public static function getImUser($user) {
        $request = new \OpenimUsersGetRequest();
        $imUser = self::getUserImInfo( $user );
        $request->setUserids( $imUser ["im_uid"] );
        // code msg
        $result = self::getClient()->execute( $request );
        // var_export( $result );
        if( isset( $result->code ) || !empty( $result->fail_msg->string ) ) {
            $error = isset( $result->code ) ? $result->msg : $result->fail_msg->string [0];
            throw new UserException( "获取im账号信息(uid:{$user->id})错误：" . $error );
        }
        return $result->userinfos;
    }

    /**
     * 删除im users
     *
     * @param array $users
     * @throws UserException
     */
    public static function deleteImUsers($users) {
        $request = new \OpenimUsersDeleteRequest();
        $uids = [ ];
        foreach ( $users as $user ) {
            $imUser = self::getUserImInfo( $user );
            $uids [] = $imUser ["im_uid"];
        }
        $uids = implode( ",", $uids );
        $request->setUserids( $uids );
        // code msg
        $result = self::getClient()->execute( $request );
        // var_export( $result );
        if( isset( $result->code ) || !empty( $result->fail_msg->string ) ) {
            $error = isset( $result->code ) ? $result->msg : $result->fail_msg->string [0];
            throw new UserException( "删除im账号(uids:{$uids})错误：" . $error );
        }
    }

    /**
     * 创建im群
     *
     * @param User $user 群主
     * @param string $name 名称
     * @param string $notice 群公告
     * @param int $type 类型
     */
    public static function createTribe($user, $name, $notice, $type, $members = null) {
        $request = new \OpenimTribeCreateRequest();
        // 群主
        $imAdmin = new \OpenImUser();
        $imAdmin->uid = self::getUserImInfo( $user ) ["im_uid"];
        $imAdmin->app_key = \Yii::$app->params ['alibc'] ['key'];
        $imAdmin->taobao_account = false;
        $request->setUser( json_encode( $imAdmin ) );
        $request->setTribeName( $name );
        $request->setNotice( $notice );
        $request->setTribeType( $type );
        if( !empty( $members ) ) {
            $imMembers = [ ];
            foreach ( $members as $member ) {
                $imMember = new \OpenImUser();
                $imMember->uid = self::getUserImInfo( $member ) ["im_uid"];
                $imMember->app_key = \Yii::$app->params ['alibc'] ['key'];
                $imMember->taobao_account = false;
                $imMembers [] = $imMember;
            }
            $request->setMembers( json_encode( $imMembers ) );
        }
        // code msg
        $result = self::getClient()->execute( $request );
        // var_export($request);exit;
        // var_export( $result );
        if( isset( $result->code ) || !empty( $result->fail_msg->string ) ) {
            $error = isset( $result->code ) ? $result->msg : $result->fail_msg->string [0];
            throw new UserException( "创建群失败({$name})错误：" . $error );
        }
        return $result->tribe_info->tribe_id . '';
    }

    /**
     * 发送群消息
     *
     * @param User $user 发送方
     * @param string $tribeId 聊天组id
     * @param int $msgType 聊天组id
     * @param string $content 内容
     */
    public static function sendTribeMsg($user, $tribeId, $content, $msgType = self::IM_MSG_TYPE_CUSTOM) {
        $request = new \OpenimTribeSendmsgRequest();
        // 发送者
        $sender = new \OpenImUser();
        $sender->uid = self::getUserImInfo( $user ) ["im_uid"];
        $sender->app_key = \Yii::$app->params ['alibc'] ['key'];
        $sender->taobao_account = false;
        // 消息体
        $msg = new \TribeMsg();
        $msg->at_flag = 0;
        $msg->atmembers = [ ];
        $msg->custom_push = "";
        $msg->media_attrs = "";
        // $content = [
        // 'header' => [
        // 'summary' => 'xxx'
        // ]
        // ];
        $msg->msg_content = $content;
        $msg->msg_type = $msgType;
        $msg->push = false;
        
        $request->setUser( json_encode( $sender ) );
        $request->setTribeId( $tribeId );
        $request->setMsg( json_encode( $msg ) );
        // code msg
        $result = self::getClient()->execute( $request );
        // var_export($request);exit;
        // var_export( $result );
        if( isset( $result->code ) || !empty( $result->tribe_code ) || !empty( $result->fail_msg->string ) ) {
            $error = isset( $result->code ) ? $result->msg : (!empty( $result->tribe_code ) ? $result->message : $result->fail_msg->string [0]);
            throw new UserException( "发送群消息失败({$tribeId}: {$request->getMsg()})错误：" . $error );
        }
    }

    /**
     * 获取群成员列表
     *
     * @param User $user
     * @param string $tribeId
     * @throws UserException
     */
    public static function getTribeMembers($user, $tribeId) {
        $request = new \OpenimTribeGetmembersRequest();
        
        $imUser = new \OpenImUser();
        $imUser->uid = self::getUserImInfo( $user ) ["im_uid"];
        $imUser->app_key = \Yii::$app->params ['alibc'] ['key'];
        $imUser->taobao_account = false;
        
        $request->setUser( json_encode( $imUser ) );
        $request->setTribeId( $tribeId );
        
        // code msg
        $result = self::getClient()->execute( $request );
        // var_export($request);exit;
        // var_export( $result );
        if( isset( $result->code ) || !empty( $result->fail_msg->string ) ) {
            $error = isset( $result->code ) ? $result->msg : $result->fail_msg->string [0];
            throw new UserException( "获取群成员列表失败({$tribeId})错误：" . $error );
        }
        return $result->tribe_user_list;
    }

    /**
     *
     * @return \TopClient
     */
    private static function getClient() {
        if( self::$_client == null ) {
            // 通知阿里百川im
            $alibcClient = new \TopClient();
            $alibcClient->format = "json";
            $alibcClient->appkey = \Yii::$app->params ['alibc'] ['key'];
            $alibcClient->secretKey = \Yii::$app->params ['alibc'] ['secret'];
            self::$_client = $alibcClient;
        }
        return self::$_client;
    }
    // -----------
    // 以下阿里百川顽兔多媒体相关
    // -----------
    /**
     * 获取wantu多媒体授权token
     *
     * @return ['token' => token, 'expiration' => expiration]
     */
    public static function getWantuUploadToken() {
        $config = \Yii::$app->params ['alibc'];
        // 用户控制台的AccessKey
        $ak = $config ['key'];
        // 用户控制台的AccessSecret
        $sk = $config ['secret'];
        // 上传策略，开发者可以根据需要，参考文档进行扩展
        $uploadPolicy = new \UploadPolicy( $config ['wantu'] ['ns'] );
        $uploadPolicy->insertOnly = \Conf::INSERT_ONLY_NONE;
        $uploadPolicy->detectMime = \Conf::DETECT_MIME_TRUE;
        $uploadPolicy->expiration = round( (microtime( true ) + $config ['wantu'] ['expires']) * 1000 );
        // 进行安全的Base64编码
        $encodedPolicy = \EncodeUtils::encodeWithURLSafeBase64( json_encode( $uploadPolicy ) );
        // 计算HMAC-SHA1签名
        $signed = hash_hmac( 'sha1', $encodedPolicy, $sk );
        // 拼接ak、上传策略、HMAC-SHA1签名
        $data = $ak . ":" . $encodedPolicy . ":" . $signed;
        // 再进行URL安全的Base64编码
        $token = "UPLOAD_AK_TOP " . \EncodeUtils::encodeWithURLSafeBase64( $data );
        return [ 
            'token' => $token,
            'expiration' => $uploadPolicy->expiration 
        ];
    }

    /**
     * 上传文件到wantu
     *
     * @param string $file 本地文件路径或者本地文件内容
     * @param string $dir 顽兔文件夹
     * @param string $name 顽兔文件名
     * @return string|boolean 成功返回远程全路径，失败返回false
     */
    public static function uploadToWantu($fileOrdata, $dir, $name, $isData = false) {
        $config = \Yii::$app->params ['alibc'];
        $aliImage = new \AlibabaImage( $config ['key'], $config ['secret'] );
        // 上传策略，开发者可以根据需要，参考文档进行扩展
        $uploadPolicy = new \UploadPolicy( $config ['wantu'] ['ns'] );
        $uploadPolicy->insertOnly = \Conf::INSERT_ONLY_NONE;
        $uploadPolicy->detectMime = \Conf::DETECT_MIME_TRUE;
        $uploadPolicy->dir = $dir;
        $uploadPolicy->name = $name;
        $uploadPolicy->returnBody = '${width},${height}';
        if( $isData ) {
            $result = $aliImage->uploadData( $fileOrdata, $uploadPolicy );
        } else {
            $result = $aliImage->upload( $fileOrdata, $uploadPolicy );
        }
        if( $result ['isSuccess'] ) {
            // policy会自动给dir前面加上/
            return "{$uploadPolicy->dir}/{$uploadPolicy->name}";
        }
        return false;
    }
}
