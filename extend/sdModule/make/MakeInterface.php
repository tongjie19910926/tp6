<?php


namespace sdModule\make;

/**
 * Interface MakeInterface
 * @package sdModule\make
 */
interface MakeInterface
{
    /**
     * MakeInterface constructor.
     * @param MakeCoordinates $coordinates
     */
    public function __construct(MakeCoordinates $coordinates);

    /**
     * 创建文件的方法
     * @return mixed
     */
    public function make();
}

