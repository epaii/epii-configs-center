<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2019/6/17
 * Time: 9:43 AM
 */

namespace epii\configs\center;

class ConfigsCenter
{
    private static $_server_api_pre = null;
    private static $_cls_id = 0;

    private static $_more = [];
    public static $cache_dir = null;

    public static function setConfig(string $server_api_pre, string $cache_dir, int $cls_id = 0)
    {
        self::$_server_api_pre = $server_api_pre;
        self::$_cls_id = $cls_id;
        self::$cache_dir = $cache_dir;
    }

    public static function getConfig(string $key = null, bool $array_enable = false)
    {
        if (self::$_cls_id===0)
        {
            echo "\$_cls_id==0;";
            exit;
        }
        return self::instance(self::$_cls_id)->getConfig($key, $array_enable);
    }

    public static function instance(int $cls_id = 0): ConfigManager
    {
        return isset(self::$_more[$cls_id]) ? self::$_more[$cls_id] : (self::$_more[$cls_id] = new ConfigManager($cls_id));
    }

    public static function handlePost()
    {

    }
}