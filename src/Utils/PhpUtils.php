<?php

namespace App\Utils;

/**
 * Class PhpUtils
 * @package App\Utils
 */
class PhpUtils
{
    /**
     * @param $class
     * @return string
     * @throws \ReflectionException
     */
    public static function getClassName($class): string
    {
        return (new \ReflectionClass($class))->getShortName();
    }
}