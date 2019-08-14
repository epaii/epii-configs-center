<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-08-14
 * Time: 11:31
 */

namespace epii\configs\center;


class ConfigApi
{
    public static function setConfig($class_id,$object_id,$name,$value,$tip = null,$url = "http://configs.wszx.cc/?app=api")
    {
        $config_manager = new ConfigManager($class_id);
        $has = $config_manager->getConfig($object_id,$name);
        if($has){
            return self::setSetting($class_id,$object_id,$name,$value,null,$url);
        }else{
            if(empty($tip)){
                return false;
            }
            return self::setSetting($class_id,$object_id,$name,$value,$tip,$url);
        }
    }

    private static function setSetting($class_id,$object_id,$name,$value,$tip = null,$url = null)
    {
        if(empty($class_id) || empty($object_id) || empty($name) || empty($value)) return false;

        $post_data = [
            "class_id" => $class_id,
            "object_id" => $object_id,
            "name" => $name,
            "value" => $value,
            "sign" => ConfigTools::mksign($class_id,$object_id)
        ];

        if(!empty($tip)){
            $post_data['tip'] = $tip;
        }

        $request = json_decode(ConfigTools::curlRequest($url,false,'post',$post_data),true);

        if($request['code'] == 1){
            return true;
        }else{
            return false;
        }
    }
}