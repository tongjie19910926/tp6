<?php

$form = '';  // form 表单html
$editor = ''; // 富文本js
$moreUploadJs = ''; //多图js
$imageJs = ''; // 单图js
$dateJs = ''; // 时间选择js



// 单图上传js代码

function imageJs($field, $select_id, $show_id, $tip_id)
{
    $tips = '<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>';

    $iVar = 'i_' . $field;

    return <<<HTML
        let {$iVar} = upload.render({
            elem: '#{$select_id}'
            , url: '{:url("/admin/image")}'
            , before: function (obj) {
                //预读本地文件示例，不支持ie8
                obj.preview(function (index, file, result) {
                    $('#{$show_id}').attr('src', result); //图片链接（base64）
                });
            }
            , done: function (res) {
                //如果上传失败
                if (res.code === 202) {
                    return layNotice.warning(res.msg);
                }
                //上传成功
                $('input[name={$field}]').val(res.data);
            }
            , error: function () {
                //演示失败状态，并实现重传
                let demoText = $('#{$tip_id}');
                demoText.html('{$tips}');
                demoText.find('.demo-reload').on('click', function () {
                    $iVar.upload();
                });
            }
        });
HTML;

}


// 表单 html 代码
foreach ($this->coordinates->makeViewData as $field => $create) {
    if($field == $this->coordinates->primaryKey) continue;

    if (!empty($create['join'])) {
        if (is_array($create['join'])) {
            $joinData = $create['join'];
        }else{
            $table = substr($create['join'], 0, strpos($create['join'], ':'));
            $kv = substr($create['join'], strpos($create['join'], ':') + 1);
            $joinSelectData['table'] = substr($create['join'], 0, strpos($create['join'], ':'));

            list($joinSelectData['value'], $joinSelectData['title']) = explode('=', $kv);
        }
    }


    $form .= <<<HTML
            <div class="layui-form-item">
                <label class="layui-form-label">{$create['label']}</label>
                <div class="layui-input-block">

HTML;

    switch ($create['type']) {
        case 'text':
            $form .= <<<HTML
                    <input type="text" name="{$field}" required  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
HTML;
            break;
        case 'password':
            $form .= <<<HTML
                    <input type="password" name="{$field}" required  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
HTML;
            break;
        case 'date':
            $form .= <<<HTML
                    <input type="text" name="{$field}" required  id="{$field}_date" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
HTML;
            $dateJs .= <<<HTML
        laydate.render({
                elem: '#{$field}_date'
                ,type: 'date'
        });
HTML;

            break;
        case 'datetime':
            $form .= <<<HTML
                    <input type="text" name="{$field}" required  id="{$field}_datetime" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
HTML;
            $dateJs .= <<<HTML
        laydate.render({
                elem: '#{$field}_date'
                ,type: 'datetime'
        });
HTML;

            break;
        case 'select':
            $op = '';
            if (!empty($joinData)) {
                foreach ($joinData as $key => $value) {
                    $op .= "<option value=\"{$key}\">{$value}</option>
                        ";
                }
            }else if (!empty($joinSelectData)) {
                $op = "{foreach \${$joinSelectData['table']} as \$item}
                        <option value=\"{\$item['{$joinSelectData['value']}']}\">{\$item['{$joinSelectData['title']}']}</option>
                        {/foreach}";
            }

            $form .= <<<HTML
                    <select name="{$field}" lay-search >
                        <option value=""></option>
                        {$op}
                    </select>
HTML;
            break;
        case 'radio':
            if (!empty($joinData)) {
                foreach ($joinData as $key => $value) {
                    $form .= <<<HTML
                    <input type="radio" name="{$key}" value="1" title="{$value}" checked autocomplete="off" class="layui-input">
HTML;
                }
            }else if (!empty($joinSelectData)) {
                $form .= <<<HTML
                    {foreach \${$joinSelectData['table']} as \$item}
                    <input type="radio" name="{\$item['{$joinSelectData['value']}']}" value="1" title="{\$item['{$joinSelectData['title']}']}" checked autocomplete="off" class="layui-input">
                    {/foreach}
HTML;
            }

            break;
        case 'image':
            $select_id = 'select_' . $field;
            $show_id = 'show_' . $field;
            $tip_id = 'tip_' . $field;

            $form .= <<<HTML
                    <div class="layui-upload">
                        <input type="hidden" name="{$field}">
                        <button type="button" class="layui-btn" id="{$select_id}">选择图片</button>
                        <div class="layui-upload-list">
                            <img class="layui-upload-img" src="" style="max-width: 300px;" id="{$show_id}" alt=""/>
                            <p id="{$tip_id}"></p>
                        </div>
                    </div>
HTML;
            $imageJs .= imageJs($field, $select_id,$show_id,$tip_id);
            break;
        case 'images':
            $form .= <<<HTML
                   <div class="layui-upload">
                       <input type="hidden" name="{$field}">
                       <button type="button" class="layui-btn" id="{$field}">选择图片</button>
                       <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                           预览图：
                           <div class="layui-upload-list" id="{$field}-show" style="overflow: hidden"></div>
                       </blockquote>
                    </div>
HTML;
            $moreUploadJs .= <<<HTML
    
        custom.moreUpload($, '{$field}', upload, '{:url("/admin/image")}').init();

HTML;
            break;
        case 'editor':
            $editor_id = 'editor_' . $field;

            $form .= <<<HTML
                  <script id="{$editor_id}" name="{$field}" type="text/plain"></script>
HTML;
            $editor .= <<<HTML
    let ue_{$field} = UE.getEditor("{$editor_id}", custom.setUEditorConfig({
        zIndex: 100
    }));
HTML;

    }

    $form .= <<<HTML
                
                </div>
            </div>

HTML;
}







return <<<HTML
{extend name="frame"}

{block name="meta"}{:token_meta()}{/block}

{block name="body"}

<!-- 导航面包屑 -->

<hr>
<div class="layui-container">
    <div class="layui-row">
        <form class="layui-form" action="">

{$form}

            <div class="layui-form-item">
                <label class="layui-form-label">滑动验证</label>
                <div class="layui-input-block" style="width: 300px;">
                    <div id="slider"></div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
</div>


{/block}

{block name="js"}
<script>
        
    {$editor}

    layui.use(['form', 'jquery', 'sliderVerify', 'notice', 'upload', 'laydate'], function () {
        let form = layui.form, sliderVerify = layui.sliderVerify, $ = layui.jquery, notice = layui.notice,
            upload = layui.upload,laydate=layui.laydate;
    
        let slider = sliderVerify.render({
            elem: '#slider',
            isAutoVerify: true,
        });
        
{$dateJs}
        
{$moreUploadJs}
        
{$imageJs}

        form.on('submit(formDemo)', function (data) {
            let load = custom.loading();
            $.ajax({
                url: '{:url("addHandle")}'
                , type: 'post'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , data: data.field
                , success: function (res) {
                    layer.close(load);
                    if (res.code === 200) {
                        parent.layer.closeAll();
                        window.parent.layNotice.success('成功');
                        window.parent.table.reload('test');
                    } else {
                        notice.warning(res.msg);
                        slider.reset();
                    }
                },
                error: function (err) {
                    layer.close(load);
                    console.log(err);
                }
            });

            return false;
        })
    });

</script>

{/block}

HTML;

