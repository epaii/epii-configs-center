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

    public function __construct( $cls_id)
    {
        $this->__cls_id = $cls_id;
    }

    public function getAllConfig( $instance_id,  $array_enable = false)
    {
        return $this->getConfig($instance_id, null, $array_enable);
    }

    public function getConfig( $instance_id,  $key = null,  $array_enable = false)
    {
        $this_config = null;
        if (!isset($this->_config_all[$instance_id])) {

            $cache_config = $this->getConfigFromCache($instance_id);
            if ($cache_config === null) {
                //通过接口从服务器获取
                $api_config = json_decode(file_get_contents(ConfigsCenter::$server_url . "?app=getconfig@index&cls_id=" . $this->__cls_id . "&instance_id=" . $instance_id . "&sign=" . ConfigTools::mksign($this->__cls_id, $instance_id)), true);

                $this->_config_all[$instance_id] = [$json_config = $api_config["config"], ConfigTools::parse($json_config)];

                if (isset($api_config['is_cache']) && ($api_config['is_cache'] - 1 == 0)) {
                    ConfigTools::saveConfigCache($this->__cls_id, $instance_id, $this->_config_all[$instance_id]);
                }

            } else {
                $this->_config_all[$instance_id] = $cache_config;
            }
        }

        $this_config = $this->_config_all[$instance_id];
        $local_config_index = $array_enable ? 1 : 0;
        if ($key === null) {
            return $this_config[$local_config_index];
        }

        if ($local_config_index == 1) {
            if (!is_array($key)) {
                $key = explode(".", $key);
            }
        }

        if (!is_array($key)) {
            return isset($this_config[$local_config_index][$key]) ? $this_config[$local_config_index][$key] : null;
        }

        $out = $this_config[$local_config_index];

        foreach ($key as $value) {
            if (isset($out[$value])) {
                $out = $out[$value];
            } else {
                return null;
            }
        }
        return $out;
    }


    public function getConfigFromCache( $instance_id)
    {
        $path = ConfigTools::getCacheFileName($this->__cls_id, $instance_id);
        if (file_exists($path)) {
            return include $path;
        } else {
            return null;
        }
    }
}