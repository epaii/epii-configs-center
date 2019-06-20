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

    private static function parseKeyValue($key, $value, &$p, $classid, $instance_id)
    {

        if (stripos($key, ".") > 0) {
            if (!isset($p[$tmp = substr($key, 0, $index = strpos($key, "."))]))
                $p[$tmp] = [];
            self::parseKeyValue(substr($key, $index + 1), $value, $p[$tmp], $classid, $instance_id);
            self::handleRemoteContent($p[$tmp], $classid, $instance_id);
        } else
            $p[$key] = $value;
    }

    public static function parse($array, $classid, $instance_id)
    {
        $out = [];
        foreach ($array as $key => $value) {
            self::parseKeyValue($key, $value, $out, $classid, $instance_id);
        }
        return $out;
    }


    private static function handleRemoteContent(&$data, $classid, $instance_id)
    {
        if (!is_array($data)) return;
        if (isset($data["is_remote_content"]) && ($data["is_remote_content"] - 1 == 0)) {
            $url = $data["url"];
            $version = isset($data["version"]) ? $data["version"] : "0";
            if ($version != "0") {
                $tmep_file = ConfigTools::getCacheFileName($classid, $instance_id) . "." . md5($url) . ".html";
                file_put_contents($tmep_file, file_get_contents($url));
                $data["__content__"] = $tmep_file;
            } else {
                $data["__content__"] = null;
            }
        }
    }

    public static function getRemotContent($data)
    {
        if (!is_array($data)) return $data;

        if (isset($data["is_remote_content"]) && ($data["is_remote_content"] - 1 == 0)) {

            if ($data["__content__"] === null) {
                return file_get_contents($data["url"]);
            } else {
                return file_get_contents($data["__content__"]);
            }


        }
        //var_dump($data);
        return $data;


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
            $code = 1;
        }
        echo json_encode([
            "msg" => $msg,
            "code" => $code
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    public static function error($msg, $code = null)
    {
        if (!$code) {
            $code = 0;
        }
        echo json_encode([
            "msg" => $msg,
            "code" => $code
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    public static function mksign($class_id, $item_id)
    {
        if (!isset(ConfigsCenter::$_class_config[$class_id])) {
            ConfigTools::error("no set sign");
        }
        return md5(implode("-", [$class_id, $item_id, ConfigsCenter::$_class_config[$class_id]]));
    }
}