<?php


namespace sdModule\layui;

/**
 * Class Tag
 * @package sdModule\layui
 */
class Tag
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * 赤
     * @param string $value
     * @return string
     */
    public function red($value = '')
    {
        $class = $value ? 'layui-badge' : 'layui-badge-dot';
        return "<span class='{$class}'>{$value}</span>";
    }

    /**
     * 橙
     * @param string $value
     * @return string
     */
    public function orange($value = '')
    {
        $class = $value ? 'layui-badge' : 'layui-badge-dot';
        return "<span class=\"{$class} layui-bg-orange\">{$value}</span>";
    }

    /**
     * 绿
     * @param string $value
     * @return string
     */
    public function green($value = '')
    {
        $class = $value ? 'layui-badge' : 'layui-badge-dot';
        return "<span class=\"{$class} layui-bg-green\">{$value}</span>";
    }

    /**
     * 青
     * @param string $value
     * @return string
     */
    public function cyan($value = '')
    {
        $class = $value ? 'layui-badge' : 'layui-badge-dot';
        return "<span class=\"{$class} layui-bg-cyan\">{$value}</span>";
    }

    /**
     * 蓝
     * @param string $value
     * @return string
     */
    public function blue($value = '')
    {
        $class = $value ? 'layui-badge' : 'layui-badge-dot';
        return "<span class=\"{$class} layui-bg-blue\">{$value}</span>";
    }

    /**
     * 黑
     * @param string $value
     * @return string
     */
    public function black($value = '')
    {
        $class = $value ? 'layui-badge' : 'layui-badge-dot';
        return "<span class=\"{$class} layui-bg-black\">{$value}</span>";
    }

    /**
     * 灰
     * @param string $value
     * @return string
     */
    public function gray($value = '')
    {
        $class = $value ? 'layui-badge' : 'layui-badge-dot';
        return "<span class=\"{$class} layui-bg-gray\">{$value}</span>";
    }

    /**
     * 边框类型
     * @param string $value
     * @return string
     */
    public function rim($value)
    {
        return "<span class=\"layui-badge-rim\">{$value}</span>";
    }

    /**
     * @return Tag
     */
    public static function init()
   {
       return  self::$instance ?:new self();
//       if (!self::$instance) {
//           self::$instance = new self();
//       }
//       return self::$instance;
   }
}
