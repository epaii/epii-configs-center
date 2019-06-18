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
    public static $server_url = null;
    private static $_cls_id = 0;

    private static $_more = [];

    public static $cache_dir = null;

    // public static $goto_config_url = null;

    public static function setConfig(string $cache_dir, int $cls_id = 0, string $server_url_pre = null)
    {
        //  self::$goto_config_url = $goto_config_url;
        if ($server_url_pre === null) {
            $server_url_pre = "";
        }
        self::$server_url = $server_url_pre;
        self::$_cls_id = $cls_id;
        self::$cache_dir = $cache_dir;
    }

    public static function getConfig(int $instance_id, string $key = null, bool $array_enable = false)
    {
        if (self::$_cls_id === 0) {
            echo "\$_cls_id==0;";
            exit;
        }
        return self::instance(self::$_cls_id)->getConfig($instance_id, $key, $array_enable);
    }

    public static function getAllConfig(int $instance_id, bool $array_enable = false)
    {
        return self::getConfig($instance_id, null, $array_enable);
    }

    public static function instance(int $cls_id = 0): ConfigManager
    {
        return isset(self::$_more[$cls_id]) ? self::$_more[$cls_id] : (self::$_more[$cls_id] = new ConfigManager($cls_id));
    }

    public static function handlePost()
    {
        if ($_POST) {
            if (empty($_POST['class_id'])) ConfigTools::error("class_id is undefined");
            if (empty($_POST['object_id'])) ConfigTools::error("object_id is undefined");
            if (empty($_POST['config'])) ConfigTools::error("config is undefined");

            $out = ConfigTools::saveConfigCache($_POST["class_id"], $_POST["object_id"], [$json_config = json_decode($_POST["config"], true), ConfigTools::parse($json_config)]);
            ConfigTools::success('success');
            exit;
        } else {
            if (isset($_GET['check']) && $_GET['check'] = 1) {
                if (is_writeable(self::$cache_dir)) {
                    echo json_encode(['code' => 1]);
                } else {
                    echo json_encode(['code' => 0]);
                }
            }
            ConfigTools::error("POST data is undefined");
        }
    }

    public static function getConfigCenterUrl(int $instance_id, int $cls_id = 0)
    {
        if ($cls_id === 0) $cls_id = self::$_cls_id;
        return self::$server_url . "?app=instance@index&c_id=" . $cls_id . "&id=" . $instance_id;
    }
}