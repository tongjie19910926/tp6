<?php


namespace sdModule\make\item;


use sdModule\make\MakeCoordinates;
use sdModule\make\MakeInterface;

class MakeValidate implements MakeInterface
{
    /**
     * @var MakeCoordinates
     */
    public $coordinates;

    /**
     * @var
     */
    public $className;

    /**
     * MakeValidate constructor.
     * @param MakeCoordinates $coordinates
     */
    public function __construct(MakeCoordinates $coordinates)
    {
        $this->coordinates = $coordinates;
        $this->className = parse_name($coordinates->table, 1);
    }

    /**
     * @return bool|string
     */
    public function make()
    {
        $file = implode(DIRECTORY_SEPARATOR, [
            $this->coordinates->appPath,
            $this->coordinates->getConfig('validate_dir', 'validate'),
            "{$this->className}.php"
        ]);
        if (file_exists($file)) return 'validate 文件已存在。';
        is_dir(dirname($file)) or mkdir(dirname($file), 777, true);
        return file_put_contents($file, $this->getContent()) ? true : 'validate 创建失败';
    }

    /**
     * @return string
     */
    public function getContent()
    {
        list($newSchema, $allScene, $addScene) = $this->getRule();
        return <<<CLS
<?php


namespace {$this->coordinates->getValidateNamespace()};

use think\\Validate;

class {$this->className} extends Validate
{
    protected \$rule = {$newSchema};
    
    protected \$scene = [
        'add' => $addScene,
        'edit' => $allScene,
    ];
}

CLS;
    }


    /**
     * @return array
     */
    public function getRule()
    {
        $schema = MakeCoordinates::getSchema(parse_name($this->coordinates->table));
        $newSchema = [];
        foreach ($schema as $value) {
            if (!in_array($value['column_name'], $this->coordinates->notVerify)) {
                $rule = 'require';
                if (in_array($value['column_type'], ['tinyint', 'int', 'smallint', 'bigint'])) {
                    $rule .= '|number';
                }
                if (in_array($value['column_type'], ['float', 'decimal', 'double'])) {
                    $rule .= '|float';
                }
                if (preg_match('/([a-zA-Z_]*phone)$|([a-zA-Z_]*tel)$/', $value['column_name'])) {
                    $rule .= '|mobile';
                }

                if (!empty($this->coordinates->makeViewData[$value['column_name']]['join']) &&
                    is_array($this->coordinates->makeViewData[$value['column_name']]['join'])) {
                    $rule .= '|in:' . implode(',', array_keys($this->coordinates->makeViewData[$value['column_name']]['join']));
                }

                $verifyTip = $this->coordinates->makeViewData[$value['column_name']]['label'] ?? $value['column_comment'];
                $newSchema[$value['column_name'] . '|' . $verifyTip] = $rule;
            }
        }

        $allScene = array_column($schema, 'column_name');
        $allScene = array_diff($allScene, $this->coordinates->notVerify);

        return [
            MakeCoordinates::varExport($newSchema, true),
            MakeCoordinates::varExport($allScene, true),
            MakeCoordinates::varExport(array_diff($allScene, ['id']), true)
        ];
    }

}

