<?php


namespace sdModule\make\item;


use sdModule\make\MakeCoordinates;
use sdModule\make\MakeInterface;

class MakeView implements MakeInterface
{
    private $resource = [
        'index', 'add', 'edit'
    ];

    /**
     * @var MakeCoordinates
     */
    public $coordinates;

    /**
     * MakeView constructor.
     * @param MakeCoordinates $coordinates
     */
    public function __construct(MakeCoordinates $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return mixed
     */
    public function make()
    {
        $dir = implode(DIRECTORY_SEPARATOR, [
                $this->coordinates->root,
                $this->coordinates->getConfig('view_dir', 'view/admin'),
                parse_name($this->coordinates->table)
            ]) . DIRECTORY_SEPARATOR;

        is_dir($dir) or mkdir($dir, 777, true);

        $tip = '';

        foreach ($this->resource as $datum) {
            $resourceFile = dirname(__DIR__) . "/resource/{$datum}.php";
            if (file_exists($resourceFile)) {
                $resource = include_once $resourceFile;
                if (file_exists($dir . "{$datum}.html")) {
                    $tip .= "{$datum}.html 已存在！";
                }else{
                    file_put_contents($dir . "{$datum}.html", $resource) or $tip .= "{$datum}.html 创建失败！";
                }
            }
        }

        return $tip ?: true;
    }

}

