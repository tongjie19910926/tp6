<?php


namespace sdModule\layuiSearch;


use sdModule\common\ReflexCall;
use sdModule\layuiSearch\generate\FormDefine;
use sdModule\layuiSearch\generate\Select;
use sdModule\layuiSearch\generate\Selects;
use sdModule\layuiSearch\generate\Text;
use sdModule\layuiSearch\generate\Time;
use sdModule\layuiSearch\generate\TimeRange;

/**
 * Interface FormExample
 * @package sdModule\layuiSearch
 */
interface FormExample {
    public static function Text(string $name, string $placeholder = ''):Text;
    public static function Select(string $name, string $placeholder = ''):Select;
    public static function Selects(string $name, string $placeholder = ''):Selects;
    public static function Time(string $name, string $placeholder = ''):Time;
    public static function TimeRange(string $name, string $placeholder = ''):TimeRange;
}


/**
 * 搜索表单html创建
 * Class SearchForm
 * @example
 * @mixin FormExample
 * @package sdModule\layuiSearch
 */
class SearchForm
{
    /**
     * @param $name
     * @param $arguments
     * @return FormDefine
     * @throws \ReflectionException
     */
    public static function __callStatic($name, $arguments):FormDefine
    {
        $class = ReflexCall::getInstance(strtr(FormDefine::class, ['FormDefine' => parse_name($name,1)]));
        return $class->name(...$arguments);
    }
}

