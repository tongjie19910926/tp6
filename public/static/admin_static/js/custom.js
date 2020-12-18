/* =====================
* 自定义的一些
* ===========================*/


let custom;
custom = {
    /**
     * 设置需要的属性
     * @param need
     * @param needValue
     * @returns {custom}
     */
    setNeed: function (need, needValue) {
        this[need] = needValue;
        return this;
    },
    /**
     * 获取需要的属性
     * @param need
     * @returns {*}
     */
    getNeed: function (need) {
        return this[need];
    }
    /**
     * 自定义加载层
     * @param msg   文字提示或关闭后的回调函数
     * @param closeCallback 关闭后的回调函数
     * @returns {*}
     */
    , loading: function (msg, closeCallback) {
        if (typeof msg == 'function') {
            closeCallback = msg;
        }
        msg = typeof msg == 'string' ? msg : '操作可能需要一些时间，请稍候……';
        if (typeof closeCallback != 'function') {
            closeCallback = function () {
            }
        }

        return layer.msg(msg, {
            icon: 16
            , time: 0
            , shade: 0.1
        }, function () {
            closeCallback();
        });
    }
    /**
     * 自定义 frame 层
     * @param url   路径
     * @param title 标题
     * @param param 其他参数，所有参数以这个优先
     * @returns {Class.index|*|Layer.index}
     */
    , frame: function (url, title, param) {
        let frame = {
            type: 2,
            content: url
            , area: ['80%', '80%']
            , maxmin: true
            , title: title
        };

        if (param && typeof param == 'object') {
            for (let i in param) {
                frame[i] = param[i]
            }
        }

        return layer.open(frame);
    }
    /**
     * 百度编辑器的自定义配置
     */
    , UEditorConfig: {
        toolbars: [
            ['fullscreen', 'source', 'undo', 'redo', 'bold', 'indent', 'italic', 'underline', 'strikethrough', 'fontborder', 'horizontal', 'justifyleft', 'justifyright', 'justifycenter',
                'justifyjustify', 'forecolor', 'backcolor', 'lineheight', 'touppercase', 'tolowercase', '|', 'removeformat', 'formatmatch', '|',
                'inserttable', 'mergeright', 'mergedown', 'deletetable', 'insertrow', 'insertcol'],
            ['date', 'time', 'fontfamily', 'fontsize', 'paragraph', 'simpleupload', 'insertimage', 'link', 'background', 'spechars', 'imagenone', 'imageleft', 'imageright', 'imagecenter',]
        ]
        , initialFrameWidth: '100%'
        , initialFrameHeight: 300
    }
    /**
     * 设置百度编辑器的自定义配置
     */
    , setUEditorConfig: function (config) {
        let c = this.UEditorConfig;
        for (let i in c) {
            if (!config.hasOwnProperty(i)) {
                config[i] = c[i];
            }
        }
        return config;
    }
    /**
     * layui 图片弹出层加放大功能
     * @param layer
     * @param $
     * @param class_name
     */
    , enlarge: (layer, $, class_name) => {
        layer.photos({ photos: class_name });
        $(document).on("mousewheel DOMMouseScroll", ".layui-layer-phimg img", function (e) {
            let delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) || // chrome & ie
                (e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1)); // firefox
            let imagep = $(".layui-layer-phimg").parent().parent();
            let image = $(".layui-layer-phimg").parent();
            let h = image.height();
            let w = image.width();
            if (delta > 0) {
                h = h * 1.05;
                w = w * 1.05;
            } else if (delta < 0) {
                if (h > 100) {
                    h = h * 0.95;
                    w = w * 0.95;
                }
            }
            imagep.css("top", (window.innerHeight - h) / 2);
            imagep.css("left", (window.innerWidth - w) / 2);
            image.height(h);
            image.width(w);
            imagep.height(h);
            imagep.width(w);
        });
    },//数据表格
    tab_ren:function (table,url,config={}) {
        let conf = {
            elem: '#test'
            ,url: url
            ,toolbar: '#tableHead'
            ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            ,page:true
            ,height: 'full-113'
            ,limit:15
            ,cols: [],
            done:function (res) {
                custom.enlarge(layer, $, '.layer-photos-demo');
                window.table = table;
            }
        }
        if (config && typeof config == 'object') {
            for (let i in config) {
                conf[i] = config[i]
            }
        }
      return   table.render(conf);
    },//数据头部点击事件
    tab_bar:function (table,config={},elem='test'){
         return table.on('toolbar('+elem+')', function(obj){
             if (config[obj.event])return  config[obj.event](obj)
             console.log(obj.event+'---方法未定义');
            });
    },
    //数据列表点击事件
    tab_tool:function (table,config={},elem='test'){
        return table.on('tool('+elem+')', function(obj){
            if (config[obj.event])return  config[obj.event](obj)
            console.log(obj.event+'---方法未定义');
        });
    }
};




