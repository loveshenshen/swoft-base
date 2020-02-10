<?php
/**
 * Created by PhpStorm.
 * User: sarukinhyou
 * Date: 2019/3/8
 * Time: 9:56 AM
 * Author: shen
 */

namespace Common\Util;


use OSS\Core\OssException;
use OSS\OssClient;
use Sts\Request\V20150401\AssumeRoleRequest;

use Cloudauth\Request\V20180916 as cloudauth; //请以实际目录为准
class AliYunOss
{
    private static  $accessKeyId = '';  //
    private static  $accessKeySecret = ''; //
    private static  $bucket = 'car-repair';

    /**
     * @param $file
     * @param $type
     * @param $filename
     * @return bool|mixed
     * @throws
     */
    public static function upload($file,$type,$filename){
        $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
        // 存储空间名称
        $bucket= self::$bucket;

        try{
            $ossClient = new OssClient(self::$accessKeyId, self::$accessKeySecret, $endpoint);

            $result = $ossClient->uploadFile($bucket, $type.'/'.$filename, $file);

            if(isset($result['info']) && $result['info']['http_code'] == 200 ){
               return str_replace(config('prefix'),'',$result['info']['url']);
            }
        } catch(OssException $e) {
            throw new \App\Exception\UserException($e->getMessage() . "\n");
        }
       return false;
    }

    /**
     * @param string $filePath  要删除的文件路径 eg:/2/15520173798902.gif
     */
    public static function deleteFile($filPath){
        $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
        // 存储空间名称
        $bucket= self::$bucket;
        try{
            $ossClient = new OssClient(self::$accessKeyId, self::$accessKeySecret, $endpoint);

            $ossClient->deleteObject($bucket, trim($filPath,'/'));
        } catch(OssException $e) {
            printf($e->getDetails() . "\n");
            return;
        }
        return true;
    }


    /**
     * 生成app上传的授权token
     * */
    public static function generateToken(){

//        define("REGION_ID", "cn-beijing");
//        define("ENDPOINT", "sts.cn-beijing.aliyuncs.com");
//// 只允许子用户使用角色
//        DefaultProfile::addEndpoint(REGION_ID, REGION_ID, "Sts", ENDPOINT);
//
//        $iClientProfile = \DefaultProfile::getProfile(REGION_ID,self::$accessKeyId,self::$accessKeySecret);
//        $client = new \DefaultAcsClient($iClientProfile);
// 角色资源描述符，在RAM的控制台的资源详情页上可以获取
        $roleArn = "acs:ram::1244941066680713:role/aliyunosstokengeneratorrole";
// 在扮演角色(AssumeRole)时，可以附加一个授权策略，进一步限制角色的权限；
// 详情请参考《RAM使用指南》
// 此授权策略表示读取所有OSS的只读权限
        $policy=<<<POLICY
{
  "Statement": [
    {
      "Action": [
        "oss:Get*",
        "oss:List*"
      ],
      "Effect": "Allow",
      "Resource": "*"
    }
  ],
  "Version": "1"
}
POLICY;
        $request = new AssumeRoleRequest();
// RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
// 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName("client_name");
        $request->setRoleArn($roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds(3600);
        try {
            $response = $client->getAcsResponse($request);
            print_r($response);
        } catch(ServerErrorHttpException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        }
    }


    /**
     * 实人认证 token
     * @throws
     */
    public static function getVerifyToken(){

    // 创建DefaultAcsClient实例并初始化
        $iClientProfile = \DefaultProfile::getProfile(
            "cn-beijing",            //默认
            self::$accessKeyId,        //您的Access Key ID
            self::$accessKeySecret);    //您的Access Key Secret
        $iClientProfile::addEndpoint("oss-cn-beijing.aliyuncs.com", "cn-beijing", "Cloudauth", "cloudauth.aliyuncs.com");
        $client = new \DefaultAcsClient($iClientProfile);
        $biz = "card-identity"; //您在控制台上创建的、采用RPBasic认证方   案的认证场景标识, 创建方法：https://help.aliyun.com/document_detail/59975.html
        $ticketId  = self::guid(); //认证ID, 由使用方指定, 发起不同的认证任务需要更换不同的认证ID
        $getVerifyTokenRequest = new cloudauth\GetVerifyTokenRequest();
        $getVerifyTokenRequest->setBiz($biz);
        $getVerifyTokenRequest->setTicketId($ticketId);
        try {
            $response = $client->getAcsResponse($getVerifyTokenRequest);
            $token = $response->Data->VerifyToken->Token; //token默认30分钟时效，每次发起认证时都必须实时获取
        } catch (\Exception $e) {
            throw new \Exception( $e->getMessage());
        }
        return [
            'uid'=>$ticketId,
            'token'=>$token
        ];
    }

    /**
     * @param $userId
     * @return \SimpleXMLElement
     */
    public static function getStatus($userId){
        $iClientProfile = \DefaultProfile::getProfile(
            "cn-hangzhou",            //默认
            self::$accessKeyId,        //您的Access Key ID
            self::$accessKeySecret);    //您的Access Key Secret
        $biz = "card-identity";
        $iClientProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "Cloudauth", "cloudauth.aliyuncs.com");
        $client = new \DefaultAcsClient($iClientProfile);
        $getStatusRequest = new cloudauth\GetStatusRequest();
        $getStatusRequest->setBiz($biz);
        $getStatusRequest->setTicketId($userId);
        try {
            $response = $client->getAcsResponse($getStatusRequest);
            $statusCode = $response->Data->StatusCode;
        } catch (\ServerException $e) {
            print $e->getMessage();
        } catch (\ClientException $e) {
            print $e->getMessage();
        }
        return $statusCode;
    }


    /**
     * @param $uuid
     * @param $statusCode
     */
    public static function  getInfo($uuid){
        $iClientProfile = \DefaultProfile::getProfile(
            "cn-hangzhou",            //默认
            self::$accessKeyId,        //您的Access Key ID
            self::$accessKeySecret);    //您的Access Key Secret
        $biz = "card-identity";
        $iClientProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "Cloudauth", "cloudauth.aliyuncs.com");
        $client = new \DefaultAcsClient($iClientProfile);
        ////7. 服务端获取认证资料
        $getMaterialsRequest = new cloudauth\GetMaterialsRequest();
        $getMaterialsRequest->setBiz($biz);
        $getMaterialsRequest->setTicketId($uuid);

        try {
//            $statusCode =  self::getStatus($uuid);
//            if($statusCode !=  1){
//                throw new UserException("认证失败.请重新认证");
//            }
            $response = $client->getAcsResponse($getMaterialsRequest);
        } catch (\ServerException $e) {
            throw  $e;
        } catch (\ClientException $e) {
            throw $e;
        }catch (\Exception $e){
            throw  $e;
        }
        return $response;
    }


   public static  function guid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }

}