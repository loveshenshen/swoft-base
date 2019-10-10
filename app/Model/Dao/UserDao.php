<?php declare(strict_types=1);


namespace App\Model\Dao;


use App\Exception\UserException;
use App\Model\Entity\User;
use App\Model\Entity\UserOauth;
use Swoft\Db\DB;

class UserDao
{

    /**
     * @param  array $data
     * @return User
     * @throws
     */
    public static function register($data):User
    {
        extract( $data );
        DB::beginTransaction();
        try{
            $userOauth = UserOauth::where([
                'type'=>$type,
                'external_uid'=>$externalUid
            ])->first();
            if(!$userOauth){
                $user = new \App\Model\Entity\User();
                $user->setUsername($externalUid);
                $user->setNickname($externalName);
                $user->setPasswordHash(md5($password));
                $user->setGender(intval($gender));
                $user->setAvatar($avatar);
                if(!$user->save()){
                    throw new UserException("User save fail");
                }
                $userOauth = new UserOauth();
                $userOauth->setUserId($user->getId());
                $userOauth->setExternalUid($externalUid);
                $userOauth->setType(intval($type));
                $userOauth->setExternalName($externalName);
                $userOauth->setOther($other);
                $userOauth->setToken($token);
                if(!$userOauth->save()){
                    throw new UserException("UserOauth save fail");
                }
            }else{
                $user = \App\Model\Entity\User::find($userOauth->getUserId());
            }
            DB::commit();
        }catch(\Exception $exception){
            DB::rollBack();
            throw $exception;
        }
        return  $user;
    }



}