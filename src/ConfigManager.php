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

    public function getConfig(string $key = null, bool $array_enable = false)
    {
        return null;
    }

    public function handlePost(int $obj_id = 0)
    {
        $url = ConfigsCenter::$goto_config_url."&c_id=".$this->__cls_id."&id=".$obj_id."";
        header("location:".$url);
        exit;
    }

    public function updateConfig(int $obj_id, $config)
    {

    }
}