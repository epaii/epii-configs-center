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
    private static $server_url = null;
    private static $_cls_id = 0;

    private static $_more = [];

    public static $cache_dir = null;

    // public static $goto_config_url = null;

    public static function setConfig(string $cache_dir, int $cls_id = 0, string $server_url = null)
    {
        //  self::$goto_config_url = $goto_config_url;
        if ($server_url === null) {
            $server_url = "";
        }
        self::$server_url = $server_url;
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
            if (empty($_POST['class_id'])) exit("class_id is undefined");
            if (empty($_POST['object_id'])) exit("object_id is undefined");
            if (empty($_POST['config'])) exit("config is undefined");

            $file_name = ConfigTools::getCacheFileName($_POST['class_id'], $_POST['object_id']);

            if (file_exists($file_name))
                @unlink($file_name);

            $path_dir = pathinfo($file_name, PATHINFO_DIRNAME);
            if (!is_dir($path_dir)) {
                if (!mkdir($path_dir, 0777, true)) {
                    exit(90);
                }
            }

            ob_start();
            echo "<?php \n return ";
            $cofig = [$json_config = json_decode($_POST['config'], true), ConfigTools::parse($json_config)];
            var_export($cofig);
            echo ";" . PHP_EOL;
            $content = ob_get_clean();
            file_put_contents($file_name, $content);
            echo json_encode(['code' => 1]);
            exit;
        } else {
            exit("POST data is undefined");
        }
    }

    public static function getConfigCenterUrl(int $instance_id, int $cls_id = 0)
    {
        if ($cls_id === 0) $cls_id = self::$_cls_id;

        return self::$server_url . "?c_id=" . $cls_id . "&id=" . $instance_id;

    }
}