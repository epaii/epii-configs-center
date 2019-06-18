<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2019/6/18
 * Time: 1:38 PM
 */

namespace epii\configs\center;


class ConfigTools
{
    public static function getCacheFileName($class_id, $object_id)
    {
        return ConfigsCenter::$cache_dir . "/" . $class_id . "/config" . $class_id . "_" . $object_id . ".php";
    }

    public static function creatCacheFilePath($class_id)
    {
        @mkdir(ConfigsCenter::$cache_dir . "/" . $class_id . "/", 0777, true);
    }

    public static function getCachePathByClsId($class_id)
    {
        return ConfigsCenter::$cache_dir . "/" . $class_id . "";
    }
    private static function parseKeyValue($key, $value, &$p)
    {

        if (stripos($key, ".") > 0) {
            if(!isset($p[$tmp = substr($key, 0, $index = strpos($key, "."))]))
                $p[$tmp ] = [];
            self::parseKeyValue(substr($key, $index+1), $value, $p[$tmp]);
        } else
            $p[$key] = $value;
    }

    public static function parse($array)
    {
        $out = [];
        foreach ($array as $key => $value) {
            self::parseKeyValue($key,$value,$out);
        }
        return $out;
    }
}