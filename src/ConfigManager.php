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

    public function __construct(int $id = 0)
    {
        $this->__cls_id = $id;
    }

    public function getConfig(string $key = null, bool $array_enable = false)
    {
        return null;
    }
}