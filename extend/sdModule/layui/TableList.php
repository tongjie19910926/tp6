<?php


namespace sdModule\layui;


use think\facade\App;

class TableList
{
    public $css = [
        '/public/admin_static/layui/css/layui.css',
    ];

    public $jsBefore = [];

    public $jsAfter = [
        '/public/admin_static/layui/layui.all.js',
        '/public/admin_static/js/custom.js'
    ];

    private $html;
    private $root;
    private $title = '列表页';

    private $cssHtml = '';
    private $jsBeforeHtml = '';
    private $jsAfterHtml = '';

    public function htmls()
    {

    }

    public function __construct()
    {
        $this->root = App::getRootPath();
    }

    private function ResourceGenarate()
    {
        $this->cssExport();
        $this->jsBeforeExport();
        $this->jsAfterExport();
    }


    private function cssExport()
    {
        foreach ($this->css as $css) {
            $this->cssHtml .= '<link rel="stylesheet" href="' . $this->root . $css .'" media="all" />';
        }
    }

    private function jsAfterExport()
    {
        foreach ($this->jsAfter as $js) {
            $this->jsAfterHtml .= '<script type="text/javascript" src="' . $this->root . $js .'"></script>';
        }
    }

    private function jsBeforeExport()
    {
        foreach ($this->jsBefore as $js) {
            $this->jsBeforeHtml .= '<script type="text/javascript" src="' . $this->root . $js .'"></script>';
        }
    }

    public function table($data)
    {
        $data = [
            'id' => '300',
            'title' => '',
            'ddd' => ''
        ];
    }



    private function html()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>$this->title</title>
    {$this->cssHtml}
    <style>
        body{padding: 10px}
    </style>
    {$this->jsBeforeHtml}
</head>
<body>
<div class="layui-card">
    <div class="layui-card-header">{$this->title}</div>
    
    <div class="layui-card-body">
        <table class="layui-hide" id="test" lay-filter="test"></table>
    </div>
</div>

</body>
{$this->jsAfterHtml}
<script>
    layui.config({
        base: '{$this->root}/admin_static/layui/dist/'
    });

    layui.use('notice',function () {
        window.layNotice = layui.notice;
    });
</script>
</html>
HTML;
    }
}

