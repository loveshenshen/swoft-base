<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://swoft.org/docs
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller;

use App\Common\Auth;
use App\Common\JWT;
use App\Exception\ApiException;
use App\Exception\UserException;
use App\Model\Dao\UserDao;
use App\Model\Entity\UserDevice;
use App\Model\Entity\UserOauth;
use App\Model\User;
use common\util\CacheUtil;
use common\util\Constants;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Limiter\Annotation\Mapping\RateLimiter;
use Swoft\Log\Helper\CLog;
use App\Http\Middleware\AuthMiddleware;
use Swoft\Redis\Pool;
use Swoft\Redis\Redis;

// use Swoft\Http\Message\Response;

/**
 * Class UserController
 *
 * @Controller(prefix="/v1/user")
 * @package App\Http\Controller
 */
class UserController{

    /**
     * @Inject()
     * @var Pool
     */
    public $redis;



    /**
     * 登录某个device(可能会注册)
     * @param \App\Model\Entity\User $user
     * @throws
     */
    private function loginDevice($user):void
    {
         $device = context()->getRequest()->getHeaderLine("Device");
         $userDevice = UserDevice::where([
             'user_id'=>$user->getId(),
             'device'=>$device
         ])->first();
         $time = time();
         if(!$userDevice){
             $userDevice = new UserDevice();
             $userDevice->setUserId($user->getId());
             $userDevice->setDevice($device);
             $userDevice->setAccessToken(hash("sha256",strval($time)));
             $userDevice->setLastActive($time);
             $userDevice->save();
         }
         $userDevice->setLastActive($time);
         $userDevice->save();
    }


    /**
     * @RequestMapping(route="oauth",method=RequestMethod::POST)
     * @throws
     */
    public function oauth(Request $request):array
    {
        $externalUid = $request->post("external_uid",'');
        $externalName = $request->post("external_name",'');
        $type = $request->post("type",0);
        $token = $request->post("token",'');
        $other = $request->post("other",'');
        $password = $request->post("password",'123456');
        $avatar = $request->post("avatar",'');
        $gender = $request->post("gender",0);
        if(empty($externalUid) || empty($externalName) || empty($type)){
            throw new UserException("参数不能为空");
        }
        $oToken = $this->redis->get(\App\Model\Entity\User::REDIS_USER_MOBILE_CODE);
        if($oToken != $token){
            throw new UserException("验证码不正确");
        }
        $data = [
            'type' => $type,
            'externalUid' => $externalUid,
            'externalName' => $externalName,
            'token' => $token,
            'other' => $other,
            'password' => $password,
            'gender' =>$gender,
            'avatar' => $avatar,
        ];
        $user = UserDao::register($data);
        $this->loginDevice($user);
        $auth = new Auth($user);
        $data =  [
            'token'=>JWT::encode($auth),
            'user'=>$user
        ];
        return $data;
    }

    /**
     * @RequestMapping("code",method=RequestMethod::POST)
     * @RateLimiter(rate=1,max=5,key="request.query('mobile')")
     * @throws
     */
    public function code(Request $request):string
    {
        $mobile = $request->post("mobile",'');
        $type = $request->post("type",0);
        if(empty($mobile)){
            throw new UserException("参数不能为空");
        }

        if( empty( $mobile ) ) {
            throw new UserException("手机号不能为空");
        }
        if($type == 1){
            $userOauth = UserOauth::where([
                'external_uid'=>$mobile,
                'type'=>Constants::OAUTH_MOBILE
            ])->first();
            if($userOauth){
                throw new UserException("该用户已注册");
            }
        }

        $code = 8888;
        // $code = rand( 1000, 9999 );
        $this->redis->set(\App\Model\Entity\User::REDIS_USER_MOBILE_CODE.$mobile,strval($code),300);
//        $rtn = YII_DEBUG ? true : YunPianManager::sendSMS( $mobile, "您的验证码是{$code}。" );
        $rtn = true;
        if( $rtn === true ) {
            return "验证码发送成功，请注意查收";
        } else {
            throw new UserException( $rtn );
        }
    }


    /**
     * @RequestMapping("logout",method=RequestMethod::POST)
     * @Middleware(AuthMiddleware::class)
     */
    public function logout(Request $request){
        $user = $request->user;
        JWT::encode($user,0);
        return 1;
    }








}
