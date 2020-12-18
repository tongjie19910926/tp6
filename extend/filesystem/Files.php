<?php
namespace filesystem;
use sdModule\common\ReflexCall;
/**
 * 搜索表单html创建
 * Class SearchForm
 * @example
 * @mixin FormExample
 * @package sdModule\layuiSearch
 */
class Files
{
    /**
     * @param $name
     * @param $arguments
     * @return FormDefine
     * @throws \ReflectionException
     */
    public static function __callStatic($name, $arguments)
    {
        $class = ReflexCall::getInstance(strtr(self::class, ['Files' => parse_name($name,1)]));
        return $class->name(...$arguments);
    }
}

