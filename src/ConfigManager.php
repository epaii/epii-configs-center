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

    public function __construct($cls_id)
    {
        $this->__cls_id = $cls_id;
    }

    public function getAllConfig($instance_id, $array_enable = false)
    {
        return $this->getConfig($instance_id, null, $array_enable);
    }


    public function getConfigValueWithRemoteContent($instance_id, $key)
    {
        return $this->getConfig($instance_id, $key, true);
    }

    public function apiGetConfig($instance_id, $key = null)
    {
        $ret = file_get_contents(ConfigsCenter::$server_url . "?app=getconfig@api&cls_id=" . $this->__cls_id . "&instance_id=" . $instance_id . "&sign=" . ConfigTools::mksign($this->__cls_id, $instance_id) . "&key=" . $key);
        if (!$ret) {
            return false;
        }
        $ret = json_decode($ret, true);
        if (isset($ret["data"]["value"])) {
            return $ret["data"]["value"];
        }
        return false;

    }


    public function getConfig($instance_id, $key = null, $array_enable = false)
    {
        $this_config = null;
        if (!isset($this->_config_all[$instance_id])) {

            $cache_config = $this->getConfigFromCache($instance_id);
            if ($cache_config === null) {
                //通过接口从服务器获取
                $api_config = json_decode(file_get_contents(ConfigsCenter::$server_url . "?app=getconfig@index&cls_id=" . $this->__cls_id . "&instance_id=" . $instance_id . "&sign=" . ConfigTools::mksign($this->__cls_id, $instance_id)), true);

                $this->_config_all[$instance_id] = [$json_config = $api_config["config"], ConfigTools::parse($json_config, $this->__cls_id, $instance_id)];

                if (isset($api_config['is_cache']) && ($api_config['is_cache'] - 1 == 0)) {
                    ConfigTools::saveConfigCache($this->__cls_id, $instance_id, $this->_config_all[$instance_id]);
                }

            } else {
                $this->_config_all[$instance_id] = $cache_config;
            }
        }

        $this_config = $this->_config_all[$instance_id];
        $local_config_index = $array_enable ? 1 : 0;
        return ConfigTools::getValueFromData($this_config, $key, $local_config_index);
    }


    public function getConfigFromCache($instance_id)
    {
        $path = ConfigTools::getCacheFileName($this->__cls_id, $instance_id);
        if (file_exists($path)) {
            return include $path;
        } else {
            return null;
        }
    }


    public function apiSetConfig($instance_id,$name,$value,$tip)
    {
        if(empty($instance_id) || empty($name) || empty($value)) return false;
        $config_manager = new ConfigManager($this->__cls_id);
        $has = $config_manager->getConfig($instance_id,$name);
        if($has){
            return $this->setSetting($instance_id,$name,$value,null);
        }else{
            if(empty($tip)){
                return false;
            }
            return $this->setSetting($instance_id,$name,$value,$tip);
        }
    }

    private function setSetting($instance_id,$name,$value,$tip = null)
    {
        if(empty($instance_id) || empty($name) || empty($value)) return false;

        $url = ConfigsCenter::$server_url."?app=api";
        $post_data = array(
            "class_id" => $this->__cls_id,
            "object_id" => $instance_id,
            "name" => $name,
            "value" => $value,
            "sign" => ConfigTools::mksign($this->__cls_id,$instance_id)
        );

        /*var_dump($post_data);die;*/

        if(!empty($tip)){
            $post_data['tip'] = $tip;
        }

        $request = json_decode(ConfigTools::curlRequest($url,false,'post',$post_data),true);
        /*var_dump($request);die;*/

        if($request['code'] == 1){
            return true;
        }else{
            return false;
        }
    }
}