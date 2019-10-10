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
 * Time: 12:37
 * Author: shen
 */

namespace App\Common;


use App\Model\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;

class JWT
{

    private static $key = "shen";

    /**
     * @return string
     */
    public static function  encode(Auth $auth,$expire = 3600):string
    {
        $time = time();
        $token = (new Builder())
        ->identifiedBy(self::$key, true) // Configures the id (jti claim), replicating as a header item
        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
        ->canOnlyBeUsedAfter($time + $expire) // Configures the time that the token can be used (nbf claim)
        ->expiresAt($time + $expire) // Configures the expiration time of the token (exp claim)
        ->withClaim('user', $auth) // Configures a new claim, called "uid"
        ->getToken();
       return $token;
    }


    /**
     * @param string $token
     * @return  Auth
     */
    public static function  decode(string $token):Auth
    {
        $token =  (new Parser())->parse($token);
        if($token->getClaim("user")){
            return new Auth($token->getClaim("user"));
        }
        return  null;
    }





}