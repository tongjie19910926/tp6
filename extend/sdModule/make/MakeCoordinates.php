<?php


namespace sdModule\make;

use think\facade\App;

/**
 * 创建后台操作一套
 * Class MakeCoordinates
 * @package app\common\custom
 */
class MakeCoordinates
{
    const CONTROLLER = 1;
    const MODEL = 2;
    const VIEW = 4;
    const VALIDATE = 8;
    const ALL = 15;

    /**
     * 要创建的项目
     * @var array
     */
    private $item = [
        self::CONTROLLER => 'controller',
        self::MODEL => 'model',
        self::VIEW => 'view',
        self::VALIDATE => 'validate',
    ];

    /**
     * @var array 配置
     */
    private $config;

    /**
     * @var string 根路径
     */
    public $root;
    /**
     * @var string 
     */
    public $appPath;

    /**
     * @var string 表名
     */
    public $table;

    /**
     * @var string 页面名称
     */
    public $pageName;

    /**
     * @var array 不验证的字段
     */
    public $notVerify = ['create_time', 'update_time', 'delete_time'];

    /**
     * @var array 创建视图的 数据
     */
    public $makeViewData;

    /**
     * @var string 表主键
     */
    public $primaryKey = 'id';

    public $join = '';

    /**
     * MakeCoordinates constructor.
     * @param string $tableName    表名
     * @param string $pageName     页面名称
     * @param array  $makeViewData 创建view的数据
     * @example [
     *              'id' => [   // 键值为表单name
     *                   'label' => 'ID',       // 表单label & 列表展示的标题
     *                   "type" => "text",      // 表单类型text|select|radio|image|images|editor
     *                   "show_type" => "text"  // 列表展示的类型，text|image
     *                   "join" => ""           // 值处理，一般为下拉，单选，多选；支持数组
     *                                              和 表：值=标题
     *              ],
     *              ....
     *         ]
     */
    public function __construct($tableName, $pageName, $makeViewData = [])
    {
        $this->root = $this->getConfig('root_path', App::getRootPath());
        $this->appPath = $this->getConfig('app_path', App::getAppPath());
        $this->table = $tableName ;//parse_name($tableName, 1);
        $this->pageName = $pageName;
        $this->makeViewData = array_diff_key($makeViewData, array_flip($this->notVerify));
        $this->joinDataHandle();
    }

    /**
     * 获取配置信息
     * @param string    $key     配置的键
     * @param null      $default 默认值
     * @return mixed|null
     */
    public function getConfig($key, $default = null)
    {
        if (!$this->config) {
            $this->config = include_once __DIR__ . '/config.php';
        }
        return empty($this->config[$key]) ? $default : $this->config[$key];
    }


    /**
     * 多选择数据处理
     */
    public function joinDataHandle()
    {
        foreach ($this->makeViewData as $key => &$datum) {
            if (empty($datum['join'])) continue;
            if (is_array($datum['join'])) {
                $join = [];
                foreach ($datum['join'] as $value) {
                    $jArr = explode('=', $value);
                    $join[$jArr[0]] = $jArr[1];
                }
                $datum['join'] = $join;
            }
        }
        unset($datum);
    }


    /**
     * 生成文件
     * @param int $make 要创建的文件
     * @return array
     */
    public function make($make = self::ALL)
    {
        if (!table_field_info(parse_name($this->table))) {
            return [parse_name($this->table) . '表 不存在'];
        }
        $tip = [];
        foreach ($this->item as $key => $item) {
            if ($make & $key) {
                $class = '\\sdModule\\make\\item\\Make' . parse_name($item, 1);
                /** @var MakeInterface $class */
                $class = new $class($this);
                $tip[$item] = $class->make();
            }
        }

        return $tip;
    }


    /**
     * 返回数据字段信息
     * @param string $table
     * @return array
     */
    public static function getSchema($table = '')
    {
        return table_field_info(parse_name($table));
    }

    /**
     * 获取model的命名空间
     * @return string
     */
    public function getModelNamespace()
    {
        $app = [
            $this->getConfig('namespace', 'app'),
            $this->getConfig('app', 'admin'),
            $this->getConfig('model_dir', 'model'),
        ];

        return implode('\\', $app);
    }

    /**
     * 获取model的命名空间
     * @return string
     */
    public function getBaseModelNamespace()
    {
        list($app, $model) = explode('/', $this->getConfig('common_model_dir', 'common/model'));
        $app = [
            $this->getConfig('namespace', 'app'),
            $app, $model
        ];

        return implode('\\', $app);
    }

    /**
     * 获取controller的命名空间
     * @return string
     */
    public function getControllerNamespace()
    {
        $app = [
            $this->getConfig('namespace', 'app'),
            $this->getConfig('app', 'admin'),
            $this->getConfig('controller_dir', 'controller'),
        ];

        return implode('\\', $app);
    }

    /**
     * 获取validate的命名空间
     * @return string
     */
    public function getValidateNamespace()
    {
        $app = [
            $this->getConfig('namespace', 'app'),
            $this->getConfig('app', 'admin'),
            $this->getConfig('validate_dir', 'validate'),
        ];

        return implode('\\', $app);
    }

    /**
     * @param      $var
     * @param bool $return
     * @return mixed|string|string[]|null|void
     */
    public static function varExport($var, $return = false)
    {
        $export = var_export($var, true);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));
        if ((bool)$return) return $export; else echo $export;
    }
}

