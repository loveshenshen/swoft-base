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
 * Date: 2020/2/5
 * Time: 16:11
 * Author: shen
 */

namespace App\Model\Entity;


use App\Exception\UserException;
use common\util\Utils;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Eloquent\Model;

class BaseEntity extends Model
{
    public  $isExtra = false;
    /**
     * @return string
     */
    public static function className(){
        return get_called_class();
    }

    /**
     * @param $name
     * @throws
     */
    public function __get($name)
    {
        $name = $this->fieldToClass($name);
        $getter = 'get'.$name;
        return $this->$getter();
    }

    /**
     * @param string $name
     * @throws
     */
    public function __set($name, $value)
    {
        $name = $this->fieldToClass($name);
        $setter = 'set'.$name;
        if(method_exists($this,$setter)){
            $this->$setter($value);
        }else{
            throw new UserException("Property has not exists");
        }
    }

    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }
        return false;
    }

    /**
     * @param string $field
     * @return string
     */
    public function fieldToClass($field):string
    {
        return str_replace(' ','',ucwords(str_replace('_',' ',$field)));
    }

    /**
     * 规定要返回的数据
     * @return array
     */
    public function fields():array
    {
        return array_keys($this->getModelAttributes());
    }

    /**
     * @return array
     */
    public function extraFields():array {
        return [];
    }


    public function getAttributes(){
        $fields = $this->fields();
        if($this->isExtra){
            $fields = array_merge($fields,$this->extraFields());
        }
        return $fields;
    }


    /**
     * 格式化返回的数据
     * @return array
     * @throws
     */
    public function toArray(): array
    {
        $result = [];
        $attributes = $this->getModelAttributes();
        foreach ($this->getAttributes() as $key=>$value){
            if(is_callable($value)){
                $result[$key] = $value();
            }elseif(is_string($value)){
                $methodName = 'get' . $this->fieldToClass($value);
                $field = is_string($key) ? $key : $value;
                if(method_exists($this,$methodName)){
                    $result[$field] =  $this->$methodName();
                    continue;
                }
                if(!isset($attributes[$value])){
                    throw new UserException('Property has not exists');
                }
                $result[$field] = $attributes[$value];
            }
        }
        return $result;
    }




    /**
     * @param string $className
     * @param array $link
     * @return mixed
     */
    public function hasOne($className,$link = ['id'=>'user_id'])
    {
        $before = key($link);
        $after = current($link);
        return $className::where([
           $before => $this->$after
        ])->first();
    }

    /**
     * @param string $className
     * @param array $link
     * @return
     */
    public function hasMany($className,$link = ['user_id'=>'id'])
    {
        $before = key($link);
        $after = current($link);
        return $className::where([
             $before => $this->$after
        ])->get();
    }

}