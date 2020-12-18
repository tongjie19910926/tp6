layui.define(['layer', 'form','table', 'element','tree'], function(exports){
    var layer = layui.layer
        ,$=  layui.$,
        tree = layui.tree;
    var table = layui.table,
        element = layui.element;
//监听提交

var data1 = [{
    label: '江西'
    ,id: 1
    ,children: [{
        label: '南昌'
        ,id: 1000
        ,children: [{
            label: '青山湖区'
            ,id: 10001
        },{
            label: '高新区'
            ,id: 10002
        }]
    },{
        label: '九江'
        ,id: 1001
    },{
        label: '赣州'
        ,id: 1002
    }]
},{
    label: '广西'
    ,id: 2
    ,children: [{
        label: '南宁'
        ,id: 2000
    },{
        label: '桂林'
        ,id: 2001
    }]
},{
    label: '陕西'
    ,id: 3
    ,children: [{
        label: '西安'
        ,id: 3000
    },{
        label: '延安'
        ,id: 3001
    }]
}];






    //操作节点
    tree.render({
        elem: '#test1'
        ,data: data1
        ,edit: ['add', 'update', 'del','dddd'] //操作节点的图标
        ,operate: function(obj){
            var type = obj.type; //得到操作类型：add、edit、del
            var data = obj.data; //得到当前节点的数据
            var elem = obj.elem; //得到当前节点元素
            if(type === 'add'){ //增加节点
                layer.open({
                    type: 2,
                    area: ['900px', '450px'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: url('add/id/'+data.id),
                    cancel: function(index, layero){
                        console.log('111');
                    }
                });
            } else if(type === 'update'){ //修改节点
               // console.log(elem.find('.layui-tree-txt').html()); //得到修改后的内容
            } else if(type === 'del'){ //删除节点

            };


            //layer.msg(JSON.stringify(obj.data));
        }
    });







    function login(url,data,index='') {
        $.ajax({
            //几个参数需要注意一下
            type: "POST",//方法类型
            dataType: "json",//预期服务器返回的数据类型
            url: url ,//url
            data: data,
            success: function (result) {
                layer.close(index);
                //location.href=result.url;
                layer.msg(result.msg, {
                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    //消失之后的回调
                    result.url? location.href=result.url : location.reload();
                });
            },
            error : function(result) {
                layer.close(index);
                if(result.responseJSON){
                    switch (typeof result.responseJSON.msg) {
                        case "string":
                            layer.msg(result.responseJSON.msg, {
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            }, function(){
                                //消失之后的回调
                                if(result.responseJSON.url){
                                    location.href=result.responseJSON.url;
                                }
                            });
                            break;
                        case "object":
                            var alert='';
                            layui.each(result.responseJSON.msg, function (item ,vales) {
                                alert= alert + vales+',';
                            });
                            alert = alert.substr(0, alert.length - 1);
                            layer.msg(alert, {
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            }, function(){
                                //消失之后的回调
                                if(result.responseJSON.url){
                                    location.href=result.responseJSON.url;
                                }
                            });
                            break;
                    }
                }else{
                    layer.msg('小伙子出错了(具体在哪我也母鸡呀)', function(){
                        //消失之后的回调
                        //location.reload();
                    });
                }
            }
        },'json');
    }










});