<?php

$fieldShow = '';

foreach ($this->coordinates->makeViewData as $field => $value) {
    if ($value['show_type'] == 'text') {
        $fieldShow .= <<<HTML
,{field:'{$field}', title: '{$value['label']}'}
                    
HTML;
    } else if ($value['show_type'] == 'image') {
        $fieldShow .= <<<HTML
                    ,{field:'{$field}', title: '照片',templet:function (obj) {
                        return obj.{$field} ? '<div class="layer-photos-demo">' +
                            '  <img layer-pid="" layer-src="__PUBLIC__/'+obj.{$field}+'" src="__PUBLIC__/'+obj.{$field}+'" alt="'+obj.{$field}+'">' +
                            '</div>' : '——';
                    }}

HTML;
    }
}

return <<<HTML

{extend name="frame"}

{block name="body"}
<!-- 表格 -->
<div class="layui-card">
    <div class="layui-card-header">{\$page_name}管理</div>
    <div class="layui-card-body">
        {:html_entity_decode(\$search ?? '')}
        <table class="layui-hide" id="test" lay-filter="test"></table>
    </div>
</div>
{/block}

{block name="js"}

<!-- 表格头部工具栏 -->
<script type="text/html" id="tableHead">
    <button type="button" lay-event="add" class="layui-btn layui-btn-sm"><i class="layui-icon layui-icon-add-1"></i>新增</button>
    <button type="button" lay-event="del" class="layui-btn layui-btn-sm layui-btn-danger"><i class="layui-icon layui-icon-delete"></i>批量删除</button>
    <div class="layui-inline layui-word-aux">双击表格行单元编辑数据</div>
</script>

<!-- 行操作需要就解开下面的注释 -->
<script type="text/html" id="handle">
    <button type="button" lay-event="del" class="layui-btn layui-btn-xs layui-btn-danger"><i class="layui-icon layui-icon-delete"></i>删除</button>
    <button type="button" lay-event="edit" class="layui-btn  layui-btn-xs"><i class="layui-icon layui-icon-edit"></i>编辑</button>
</script>


<script>
    let tableUrl = "{:url('indexData')}";
    let primary = "{\$primary ?: 'id'}";
    layui.use(['table', 'jquery', 'form', 'notice'], function() {
        let $ = layui.jquery, form = layui.form,table = layui.table;//很重要

            table.render({
                elem: '#test'
                ,url: tableUrl
                ,toolbar: '#tableHead'
                ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                ,page:true
                , title: '{\$page_name}'
                ,limits:[10,20,30,40,50,100,200,1000]
                ,cols: [[
                    {type:'checkbox'}
                    {$fieldShow}
                    ,{width:150, title: '操作',templet:'#handle'}
                ]],
                done:function (res) {
                    custom.enlarge(layer, $, '.layer-photos-demo');
                    window.table = table;
                }
            });

        table.on('toolbar(test)', function (obj) {
            if (obj.event == 'add') {
                custom.frame('{:url("add")}', '添加{\$page_name}');
            }else if(obj.event == 'del'){
                let checkStatus = table.checkStatus('test');
                if (checkStatus.data.length) {
                    let id = [];
                    for (let i in checkStatus.data) {
                        if (checkStatus.data.hasOwnProperty(i) && checkStatus.data[i].hasOwnProperty(primary)) {
                            id.push(checkStatus.data[i][primary])
                        }
                    }
                    del(id);
                }
            }
        });

        function del(id){
            layer.confirm('确认删除吗？该操作无法恢复，请确认！', {icon:3,title:'警告'}, function (index) {
                let load = custom.loading();
                $.ajax({
                    url: '{:url("del")}'
                    , type: 'post'
                    , data: {id:id}
                    , success:function (res) {
                        layer.close(load);
                        if (res.code === 200) {
                            layNotice.success('删除成功');
                            table.reload('test');
                        }else{
                            layNotice.warning(res.msg);
                        }
                    }
                    , error:function (err) {
                        console.log(err);
                    }
                });
            })
        }
        
//      行工具栏
       table.on('tool(test)', function (obj) {
           if(obj.event === 'edit'){
               custom.frame('{:url("edit")}?id=' + obj.data.id, '修改{\$page_name}');
           }else if (obj.event === 'del') {
               del(obj.data.id);
           }
       });
        
//      双击编辑
        table.on('rowDouble(test)', function(obj){
            custom.frame('{:url("edit")}?id=' + obj.data.{$this->coordinates->primaryKey}, '修改{\$page_name}');
        });
        
//      搜索
        form.on('submit(search)',function (obj) {
            table.reload('test', {
                where:{
                    search:obj.field
                },
                page:{
                    curr:1
                }
            })
        });
    });
</script>

{/block}

HTML;

