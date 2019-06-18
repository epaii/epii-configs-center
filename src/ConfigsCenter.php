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
    public static $goto_config_url = null;

    public static function setConfig(string $cache_dir, int $cls_id = 0, string $goto_config_url = null, string $server_api_pre = null)
    {
        self::$goto_config_url = $goto_config_url;
        self::$_server_api_pre = $server_api_pre;
        self::$_cls_id = $cls_id;
        self::$cache_dir = $cache_dir;
    }

    public static function getConfig(string $key = null, int $obj_id = 0, bool $array_enable = false)
    {
        if (self::$_cls_id === 0) {
            echo "\$_cls_id==0;";
            exit;
        }
        return self::instance(self::$_cls_id)->getConfig($key, $obj_id, $array_enable);
    }

    public static function instance(int $cls_id = 0): ConfigManager
    {
        return isset(self::$_more[$cls_id]) ? self::$_more[$cls_id] : (self::$_more[$cls_id] = new ConfigManager($cls_id));
    }

    public static function handlePost()
    {
        if ($_POST) {
            var_dump($_POST);
            if (empty($_POST['class_id'])) exit("class_id is undefined");
            if (empty($_POST['object_id'])) exit("object_id is undefined");
            if (empty($_POST['config'])) exit("config is undefined");

            $file_name = self::getCacheFileName($_POST['class_id'], $_POST['object_id']);
            $config = json_decode($_POST['config'], true);

            @$has_config = include $file_name;

            if(empty($has_config)){
                $has_config = [];
            }
            foreach ($config as $k => $v) {
                if (isset($has_config[$k])) {
                    if ($has_config[$k] != $config[$k]) {
                        $has_config[$k] = $config[$k];
                    }
                } else {
                    $has_config[$k] = $config[$k];
                }
            }
            if (file_exists($file_name)) {
                @unlink($file_name);
            }else{
                self::getCacheFileName($_POST['class_id']);
                file_put_contents($file_name, "<?php \n return ", FILE_APPEND);
                ob_start();
                var_export($has_config);
                file_put_contents($file_name, ob_get_contents(), FILE_APPEND);
                ob_end_clean();
                file_put_contents($file_name, ";" . PHP_EOL, FILE_APPEND);
            }
        } else {
            exit("POST data is undefined");
        }
    }

    public static function getCacheFileName($class_id, $object_id)
    {
        return ConfigsCenter::$cache_dir . "/" . $class_id . "/config" . $class_id . "_" . $object_id . ".php";
    }

    public static function creatCacheFilePath($class_id)
    {
        @mkdir(ConfigsCenter::$cache_dir ."/" . $class_id ."/" , 0777, true);
    }

    public static function getCachePathByClsId($class_id)
    {
        return ConfigsCenter::$cache_dir ."/" . $class_id ."";
    }
}