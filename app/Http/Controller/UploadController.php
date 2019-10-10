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
 * Date: 2019/10/3
 * Time: 09:07
 * Author: shen
 */

namespace App\Http\Controller;
use App\Exception\UserException;
use common\util\AliYunOss;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Upload\UploadedFile;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Log\Helper\CLog;

/**
 * Class UploadController
 * @package App\Http\Controller
 *
 * @Controller(prefix="/v1/upload")
 */
class UploadController
{

    /**
     * 文件上传
     * @RequestMapping("upload",method=RequestMethod::POST)
     * @throws
     */
    public function upload(Request $request){
        $files = $request->getUploadedFiles();
        $type = $request->post("type",1);
        /**
         * @var $file UploadedFile
         */
        $file = $files["file"];
        if(empty($file)){
            throw new UserException("文件不能为空".$file->getError());
        }
        $now = time() . mt_rand( 1, 10000 );
        $filename = $file->getClientFilename();
        $ext = pathinfo($filename,PATHINFO_EXTENSION);
        return AliYunOss::upload( $file->getFile(), $type, "{$now}.{$ext}" );
    }
}