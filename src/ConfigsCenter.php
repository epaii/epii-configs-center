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

    public static $_class_config = [];
    private static $_more = [];

    public static $cache_dir = null;

    // public static $goto_config_url = null;

    public static function setConfig( $cache_dir,  $server_url_pre = null)
    {
        //  self::$goto_config_url = $goto_config_url;
        if ($server_url_pre === null) {
            $server_url_pre = "http://configs.wszx.cc/";
        }
        self::$server_url = $server_url_pre;

        self::$cache_dir = $cache_dir;
    }

    public static function addClass( $cls_id,  $sign, $is_default = true)
    {
        self::$_class_config[$cls_id] = $sign;
        if ($is_default)
            self::$_cls_id = $cls_id;

    }

    public static function getConfig( $instance_id,  $key = null,  $array_enable = false)
    {
        if (self::$_cls_id === 0) {
            echo "\$_cls_id==0;";
            exit;
        }
        return self::instance(self::$_cls_id)->getConfig($instance_id, $key, $array_enable);
    }

    public static function getAllConfig( $instance_id,  $array_enable = false)
    {
        return self::getConfig($instance_id, null, $array_enable);
    }

    public static function instance( $cls_id)
    {
        return isset(self::$_more[$cls_id]) ? self::$_more[$cls_id] : (self::$_more[$cls_id] = new ConfigManager($cls_id));
    }

    public static function handlePost()
    {
        if ($_POST) {

            $sign = ConfigTools::mksign($_POST['class_id'], $_POST['object_id']);
            if ($_POST['sign'] != $sign) {
                ConfigTools::error("验证失败");
            }

            if (empty($_POST['class_id'])) ConfigTools::error("class_id is undefined");
            if (empty($_POST['object_id'])) ConfigTools::error("object_id is undefined");
            if (empty($_POST['config'])) ConfigTools::error("config is undefined");

            $out = ConfigTools::saveConfigCache($_POST["class_id"], $_POST["object_id"], [$json_config = json_decode($_POST["config"], true), ConfigTools::parse($json_config)]);
            ConfigTools::success('success');
            exit;
        } else {
            if (isset($_GET['check']) && $_GET['check'] = 1) {
                if (!is_dir(self::$cache_dir)) {
                    if (!mkdir(self::$cache_dir, 0777, true)) {
                        ConfigTools::error("dir wrong 1");
                    }
                }
                if (is_writeable(self::$cache_dir)) {
                    ConfigTools::success("成功");
                } else {
                    ConfigTools::error("目录不可写");
                }
            }
            ConfigTools::error("POST data is undefined");
        }
    }

    public static function getConfigCenterUrl( $instance_id,  $cls_id = 0)
    {
        if ($cls_id === 0) $cls_id = self::$_cls_id;
        return self::$server_url . "?app=instance@index&c_id=" . $cls_id . "&id=" . $instance_id . "&sign=" . ConfigTools::mksign($cls_id, $instance_id);
    }
}