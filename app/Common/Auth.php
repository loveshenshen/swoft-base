<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2019 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////

/**
 * Created by PhpStorm.
 * User: sarukinhyou
 * Date: 2019/9/24
 * Time: 14:06
 * Author: shen
 */

namespace App\Common;
use App\Model\Entity\User;


/**
 * Class Auth
 * @package App\Common
 * @property int $id  用户id
 * @property string $username  用户名
 * @property string $nickname  昵称
 * @property string $avatar  头像
 */
class Auth
{

     const JOKE = "shenbaseswoft";

     public $id;
     public $username;
     public $nickname;
     public $avatar;

    /**
     * Auth constructor.
     * @param mixed $user
     */
     public function __construct($user)
     {
         if($user instanceof User){
             $this->id = $user->getId();
             $this->username = $user->getUsername();
             $this->avatar = $user->getAvatar();
             $this->nickname = $user->getNickname();
         }else{
             $this->id = $user->id;
             $this->username = $user->username;
             $this->avatar = $user->avatar;
             $this->nickname = $user->nickname;
         }

     }

}