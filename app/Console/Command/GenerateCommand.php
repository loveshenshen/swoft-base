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
 * Date: 2019/12/30
 * Time: 09:18
 * Author: shen
 */

namespace App\Console\Command;
use App\Application;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Db\Database;
use Swoft\Db\DB;
use Swoft\Devtool\Migration\Migration;
use Swoft\Devtool\Model\Dao\MigrateDao;
use Toolkit\Cli\App;

/**
 * Class GenerateCommand
 * @package App\Console\Command
 * @Command()
 */
class GenerateCommand
{
    /**
     * @CommandMapping("entity")
     *
     */
    public function  entity(){
        $db = \Swoft::getBean('db');
        $pdo = new \PDO($db->getDsn(),$db->getUsername(),$db->getPassword());
        $tables = $pdo->query('show  tables;')->fetchAll();
        /*
              * 先生成 model (默认是使用默认的模板curd模板和model模板)
              * 后生成curd
              * */
        $model = "#!/user/bin/env php".PHP_EOL;
        $curd = "";
        $dirNames = $this->getDir();
        foreach($tables as $table ){
            $table = current($table);
            if(!in_array($table,$dirNames)){
//                $word = str_replace(' ','',ucwords(str_replace('_',' ',$table)));
                $model .= 'php bin/swoft entity:create -y --table='.$table.PHP_EOL;
            }
        }
        $data = $model.PHP_EOL.$curd;
        $fileName = 'sarukinhyou.sh';
        file_put_contents($fileName,$data);
        passthru("sh $fileName",$returnVar);
        if($returnVar){
            echo "error";
        }else{
            echo "success";
        }
    }

    public function  getDir(){
        $handle = opendir(\Swoft::getAlias("@app/Model/Entity/"));
        $names = [];
        while (($fileName = readdir($handle)) != false) {
            if ($fileName != '.' && $fileName != '..' && !in_array($fileName, $names)) {
                $names[] = $this->toUnderScore($fileName);
            }
        }
        return $names;
    }

    //驼峰命名转下划线命名
    public  function toUnderScore($str)
    {
        $str =  basename($str,'.php');
        $dstr = preg_replace_callback('/([A-Z]+)/',function($matchs)
        {
            return '_'.strtolower($matchs[0]);
        },$str);
        return trim(preg_replace('/_{2,}/','_',$dstr),'_');
    }


}