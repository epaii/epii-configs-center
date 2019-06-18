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


    private $_config_all = [];

    public function __construct(int $cls_id)
    {
        $this->__cls_id = $cls_id;
    }


    public function getAllConfig(int $instance_id, bool $array_enable = false)
    {
        return $this->getConfig($instance_id, null, $array_enable);
    }

    public function getConfig(int $instance_id, string $key = null, bool $array_enable = false)
    {
        $this_config = null;
        if (!isset($this->_config_all[$instance_id])) {

            $cache_config = $this->getConfigFromCache($instance_id);
            if ($cache_config === null) {
                //通过接口从服务器获取
                $api_config = [];
                $this->_config_all[$instance_id] = $api_config;
            } else {
                $this->_config_all[$instance_id] = $cache_config;
            }
        }

        $this_config = $this->_config_all[$instance_id];
        $local_config_index = $array_enable ? 1 : 0;
        if ($key === null) {
            return $this_config[$local_config_index];
        }
        return isset($this_config[$local_config_index][$key]) ? $this_config[$local_config_index][$key] : null;

    }


    public function getConfigFromCache(int $instance_id)
    {
        $path = ConfigTools::getCacheFileName($this->__cls_id, $instance_id);
        if (file_exists($path)) {
            return include $path;
        } else {
            return null;
        }
    }


}