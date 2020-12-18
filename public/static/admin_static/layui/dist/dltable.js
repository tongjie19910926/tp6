layui.config({
    base: '/design/extend/'
}).extend({
    treeGrid:'treeGrid'
}).define(['laytpl', 'laypage','treeGrid', 'layer', 'form'], function(exports){
    "use strict";
    var $ = layui.jquery;
    var treeGrid = layui.treeGrid;
    var MOD_NAME='dltable';
    var dltable=$.extend({},treeGrid);
    dltable._render=dltable.render;
    dltable.render=function(param){//重写渲染方法
        param.isTree=false;//普通表格
        if(param.isPage==null||typeof(param.isPage) == "undefined"){param.isPage=true;}//默认分页
        dltable._render(param);
    };
    exports(MOD_NAME, dltable);
});