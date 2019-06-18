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
            if (!isset($p[$tmp = substr($key, 0, $index = strpos($key, "."))]))
                $p[$tmp] = [];
            self::parseKeyValue(substr($key, $index + 1), $value, $p[$tmp]);
        } else
            $p[$key] = $value;
    }

    public static function parse($array)
    {
        $out = [];
        foreach ($array as $key => $value) {
            self::parseKeyValue($key, $value, $out);
        }
        return $out;
    }

    public static function saveConfigCache($class_id, $object_id, $config)
    {
        $file_name = ConfigTools::getCacheFileName($class_id, $object_id);

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

        var_export($config);
        echo ";" . PHP_EOL;
        $content = ob_get_clean();
        return file_put_contents($file_name, $content);
    }


    public static function success($msg, $code = null)
    {
        if (!$code) {
            $code = 2;
        }
        echo json_encode([
            "msg" => $msg,
            "code" => $code
        ]);

        exit;
    }


    public static function error($msg, $code = null)
    {
        if (!$code) {
            $code = 1;
        }
        echo json_encode([
            "msg" => $msg,
            "code" => $code
        ]);

        exit;
    }

    public static function mksign($class_id, $item_id)
    {
        return md5(implode("-", [$class_id, $item_id, ConfigsCenter::$_class_config[$class_id]]));
    }
}