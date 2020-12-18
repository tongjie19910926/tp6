<?php


namespace sdModule\make\item;

use sdModule\make\MakeCoordinates;
use sdModule\make\MakeInterface;

/**
 * 模型创建
 * Class MakeModel
 * @package sdModule\make\item
 */
class MakeModel implements MakeInterface
{
    /**
     * @var MakeCoordinates
     */
    public $coordinates;

    public $className;

    public $attr = '';

    public $tag = ['red', 'orange', 'green', 'cyan', 'blue', 'black', 'gray', 'rim'];


    /**
     * MakeModel constructor.
     * @param MakeCoordinates $coordinates
     */
    public function __construct(MakeCoordinates $coordinates)
    {
        $this->className = parse_name($coordinates->table, 1);
        $this->coordinates = $coordinates;
        shuffle($this->tag);
        $this->dataHandle($coordinates->makeViewData);
    }

    /**
     * @return bool
     */
    public function make()
    {
        $baseFile = implode(DIRECTORY_SEPARATOR, [
            dirname($this->coordinates->appPath),
            $this->coordinates->getConfig('common_model_dir', 'common/model'),
            $this->className . '.php']);
        $tip = '';
        if (!file_exists($baseFile)) {
            is_dir(dirname($baseFile)) or mkdir(dirname($baseFile), 777, true);
            file_put_contents($baseFile, $this->getBaseContent()) or $tip .= $this->coordinates->getBaseModelNamespace(). '\\' . $this->className  . '创建失败！';
        }else{
            $tip .= $this->coordinates->getBaseModelNamespace() . '\\' . $this->className . '已存在！';
        }

        $file = implode(DIRECTORY_SEPARATOR, [
            $this->coordinates->appPath,
            $this->coordinates->getConfig('model_dir', 'model'),
            $this->className . '.php']);
        if (!file_exists($file)) {
            is_dir(dirname($file)) or mkdir(dirname($file), 777, true);
            file_put_contents($file, $this->getContent()) or $tip .= $this->coordinates->getModelNamespace() . '\\' . $this->className . '创建失败！';
        }else{
            $tip .= $this->coordinates->getModelNamespace() . '\\' . $this->className . '已存在！';
        }

        return $tip ?: true;
    }

    /**
     * 数据处理
     * @param $data
     */
    protected function dataHandle($data)
    {
        foreach ($data as $key => $item) {
            if (!empty($item['join']) && is_array($item['join'])) {
                $this->attr .= $this->attrGenerate($key, $item['join']);
            }
        }
    }


    /**
     * 获取改变属性值的内容
     * @param string    $field    字段
     * @param array     $data     数据
     * @return string
     */
    protected function attrGenerate($field, $data)
    {
        $actionName = parse_name($field, 1);
        $arr = '';
        $i = 0;
        foreach ($data as $key => $datum) {
            $arr .= <<<ARR
'{$key}' => Tag::init()->{$this->tag[$i]}('{$datum}'),
            
ARR;
            $i++;
        }

        $arr = substr($arr, 0, strrpos($arr, ','));

        return <<<TXT

    /**
     * @param \$value
     * @return mixed
     */
    public function get{$actionName}Attr(\$value)
    {
        \${$actionName} = [
            {$arr}
        ];

        return \${$actionName}[\$value] ?? \$value;
    }

TXT;

    }


    /**
     * 获取文件内容
     * @return string
     */
    protected function getContent()
    {
        $use = $this->attr ? 'use sdModule\\layui\\Tag;' : '';

        return <<<TXT
<?php

namespace {$this->coordinates->getModelNamespace()};

{$use}

class {$this->className} extends \\{$this->coordinates->getBaseModelNamespace()}\\{$this->className}
{

{$this->attr}

}

TXT;

    }


    /**
     * 获取基础类的文件内容
     * @return string
     */
    protected function getBaseContent()
    {
        $schema = array_column(MakeCoordinates::getSchema(parse_name($this->coordinates->table)), 'column_type', 'column_name');
        $schema = MakeCoordinates::varExport($schema, true);
        return <<<TXT
<?php

namespace {$this->coordinates->getBaseModelNamespace()};

use think\\Model;

class {$this->className} extends Model
{

    protected \$schema = {$schema};
    
    /**
     * @param int \$id
     * @return array|mixed|Model|null
     */
    public function detail(\$id = 0)
    {
//      TODO 返回详情数据
        try {
            return \$this->where(['delete_time' => 0, 'id' => \$id])
                ->field(true)->find();
        } catch (\\Exception \$exception) {
            error_exception_handle(\$exception->getMessage());
            return [];
        }
    }
}

TXT;

    }

}

