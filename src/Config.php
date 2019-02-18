<?php

namespace Godbout\Time;

use Noodlehaus\Config as BaseConfig;

class Config extends BaseConfig
{
    public static function writeToFile($path, $config)
    {
        file_put_contents(
            $path,
            json_encode($config, JSON_PRETTY_PRINT)
        );
    }
}
