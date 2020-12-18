<?php


namespace sdModule\layuiSearch;

use think\facade\View;

/**
 * layui数据表格搜索
 * Class Search
 * @package app\common\custom
 */
class Form
{
    /**
     * @param array $data
     * @return string
     */
    public static function CreateHTML(array $data)
    {
        $html = $js = '';
        foreach ($data as $value) {
           $html .= $value->html;
            $js .= $value->js;
        }

        $js and View::assign('searchJs', $js);

        return !$html ? '' :<<<HTML
        <blockquote class="layui-elem-quote" style="padding: 10px 15px 5px">
            <form class="layui-form layui-inline" lay-filter="sd" action="">
                <div class="layui-form-item" style="margin-bottom: 0">
                {$html}
                <div class="layui-inline">
                    <button type="button" lay-submit lay-filter="search" class="layui-btn">搜索</button>
                    <button type="reset"  class="layui-btn layui-btn-primary">重置</button>
                </div>
                 </div>
            </form>
        </blockquote>
HTML;
    }
}


