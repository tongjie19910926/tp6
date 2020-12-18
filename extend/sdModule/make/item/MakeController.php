<?php


namespace sdModule\make\item;

use sdModule\make\MakeCoordinates;
use sdModule\make\MakeInterface;

/**
 * 控制器创建
 * Class MakeController
 * @package sdModule\make\item
 */
class MakeController implements MakeInterface
{
    /**
     * @var MakeCoordinates
     */
    public $coordinates;

    public $join = '';

    public $field = '';

    public $assign = '';

    public $class = '';

    /**
     * MakeController constructor.
     * @param MakeCoordinates $coordinates
     */
    public function __construct(MakeCoordinates $coordinates)
    {
        $this->class = parse_name($coordinates->table, 1);
        $this->coordinates = $coordinates;
        $this->dataHandle($coordinates->makeViewData);
    }

    /**
     * 创建文件并写入内容
     * @return bool|int|string
     */
    public function make()
    {
        $file = implode(DIRECTORY_SEPARATOR, [
            $this->coordinates->appPath,
            $this->coordinates->getConfig('controller_dir', 'controller'),
            "{$this->class}.php"
        ]);
        if (file_exists($file)) return 'controller 已存在！';
        if (!is_dir(dirname($file))) mkdir(dirname($file), 777, true);

        return file_put_contents($file, $this->getContent()) ? true : '创建 controller 失败';
    }


    /**
     * @param $data
     */
    protected function dataHandle($data)
    {
        foreach ($data as $key => &$datum) {
            if (!empty($datum['join']) && is_string($datum['join'])) {
                $table = substr($datum['join'], 0, strpos($datum['join'], ':'));
                list($joinField, $selectField) = explode('=', substr($datum['join'], strpos($datum['join'], ':') + 1));
                $this->join .= <<<JON
['{$table}', '{$table}.{$joinField} = i.{$key} AND {$table}.delete_time = 0'],
            
JON;
                $this->field .= ",{$table}.{$selectField} {$key}";

                $className = parse_name($table, 1);
                $this->assign .= <<<ASS
            "{$table}" => \\{$this->coordinates->getModelNamespace()}\\{$className}::where('delete_time', 0)->field('{$joinField},{$selectField}')->select(),
  
ASS;
                if (!class_exists($this->coordinates->getBaseModelNamespace() . '\\' . $className)) {
                    $coordinates = clone $this->coordinates;
                    $coordinates->makeViewData = [];
                    $coordinates->table = $className;

                    (new MakeModel($coordinates))->make();
                }
            }
        }

        $this->join = substr($this->join, 0, strrpos($this->join, ','));
        $this->assign = substr($this->assign, 0, strrpos($this->assign, ','));
    }

    /**
     * 获取创建文件的内容
     */
    protected function getContent()
    {
        return <<<CLS
<?php

namespace {$this->coordinates->getControllerNamespace()};


class {$this->class} extends \\app\\common\\controller\\User
{
     public \$pageName = '{$this->coordinates->pageName}';
    {$this->getIndexDataContent()}
    {$this->getAddContent()}
    /**
     * @param \\{$this->coordinates->getModelNamespace()}\\{$this->class} \$model
     * @param int                     \$id
     * @return mixed|string
     * @throws \\Exception
     */
    public function edit(\\{$this->coordinates->getModelNamespace()}\\{$this->class} \$model, \$id = 0)
    {
         return \$this->fetch('', [
            'data' => \$model->detail(\$id),
{$this->assign}
         ]);
    }    
}

CLS;
    }

    /**
     * @return string
     */
    protected function getAddContent()
    {
        return $this->assign ? <<<TXT
        
    /**
     * @return mixed|string
     * @throws \\Exception
     */
    public function add()
    {
         return \$this->fetch('', [
{$this->assign}
         ]);
    }
    
TXT : '';
    }

    /**
     * @return string
     */
    protected function getIndexDataContent()
    {
        return $this->join ? <<<TXT

    /**
     * @return array|\\Closure|mixed|string|\\think\Collection|\\think\\response\\Json
     */
    public function indexData()
    {
        return \$this->setJoin([
            {$this->join}
        ])->setField('i.*{$this->field}')
        ->listsRequest();
     }

TXT : '';

    }

}

