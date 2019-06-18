<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2019/6/17
 * Time: 1:49 PM
 */

namespace epii\configs\center;


class ConfigManager
{
    private $__cls_id = 0;

    public function __construct(int $cls_id = 0)
    {
        $this->__cls_id = $cls_id;
    }

    public function getConfig(int $obj_id = 0, string $key = null, bool $array_enable = false)
    {
        $path = ConfigsCenter::$cache_dir;
        $config_files = $this->readDirs($path);
        $config = [];
        foreach ($config_files as $k => $v){
            $config[$v] = include $v;
        }

        if(empty($this->__cls_id) && empty($key) && empty($obj_id)){  //如果全不传返回该项目所有配置
            return $config;
        }

        if(empty($this->__cls_id) && !empty($obj_id)){ //如果只传obj_id 返回错误
            exit("cls_id is undefined");
        }

        if(!empty($this->__cls_id) && empty($obj_id)){ //如果只传cls_id 返回cls_id 下的所有配置
            $path = ConfigsCenter::getCachePathByClsId($this->__cls_id);
            $config_files = $this->readDirs($path);
            $config = [];
            foreach ($config_files as $k => $v){
                $config[$v] = include $v;
            }
            return $config;
        }

        if(!empty($this->__cls_id) && !empty($obj_id)){ //如果cls_id 和 obj_id 同时传递则返回相应配置文件的配置
            $config[ConfigsCenter::getCacheFileName($this->__cls_id,$obj_id)];
            if(!empty($key)){
                return $config[ConfigsCenter::getCacheFileName($this->__cls_id,$obj_id)][$key];
            }else{
                return $config[ConfigsCenter::getCacheFileName($this->__cls_id,$obj_id)];
            }
        }

        return [];
    }

    private function readDirs($path) {
        if(!file_exists($path)) {
            return [];
        }
        $files = scandir($path);
        $fileItem = [];
        foreach($files as $v) {
            $newPath = $path . "/" . $v;
            if(is_dir($newPath) && $v != '.' && $v != '..') {
                $fileItem = array_merge($fileItem, $this->readDirs($newPath));
            }else if(is_file($newPath)){
                $fileItem[] = $newPath;
            }
        }

        return $fileItem;
    }
}