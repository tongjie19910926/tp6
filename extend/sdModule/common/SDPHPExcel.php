<?php


namespace sdModule\common;


class SDPHPExcel
{
    /** @var string PHPExcel库的路径 */
    const PHP_EXCEL_URL = '';

    /** @var string 文件格式 */
    const FILE_XLS = 'xls';
    const FILE_XLSX = 'xlsX';
    const FILE_PDF = 'pdf';

    /** @var string 文件存储路径 */
    const FILE_URL = '';

    /**
     * PHPExcel 核心库对象
     * @var \PHPExcel
     */
    private $PHPExcel;

    /**
     * 标题栏数据
     * @var
     */
    public $title;

    /**
     * @var int 行高
     */
    public $rowHeight = 15;

    /** @var int title 行高 */
    public $titleRoeHeight = 20;

    /**
     * @var bool|int 自动宽度
     */
    public $autoWidth = true;

    /**
     * @var bool 是否加锁
     */
    public $lock = true;

    /**
     * @var array
     *           range     范围，格式为 A1:B4，
     *           password  密码
     */
    public $lockRange = [];

    /**
     * @var bool|string 是否是直接输出,为字符串的时候判断为文件路径，存储到这里
     */
    public $output = true;

    /**
     * @var string 设置sheet的名称
     */
    public $sheetName = 'sheet1';


    /**
     * @param array  $data          需要导入到 Excel 的数据
     * @param string $file          输出下载或保存的文件名
     * @param array  $title         表格头部的标题
     * @param array  $setting       一些基础设置
     *                              rowHeight  默认行高（默认15）
     *                              autoWidth  自动宽度（默认，可设数字固定宽度）
     *                              lock       是否加锁（默认加锁，可设true取消）
     *                              lockRange  是否范围加锁（默认不加锁）
     *                                  range       范围，如： A4:B4
     *                                  password    密码
     *                              fileType   文件类型（默认 xls ，可设 xls , xlsx , pdf）
     *                              output     最终处理数据方式（默认输出，可设置路径保存本地不包括文件名 如： public/static/excel/ ）
     * @param array  $basics
     * @return \PHPExcel|void
     * @throws \PHPExcel_Exception
     */
    public static function InExcel(array $data, string $file = '', array $title = [], array $setting = [], array $basics = [])
    {
        $ExcelObj = new self();
        $ExcelObj::loadFile();
        $ExcelObj->setError();

        $ExcelObj->PHPExcel = new \PHPExcel();
        $ExcelObj->PHPExcel = $ExcelObj->setBasics($ExcelObj->PHPExcel, $basics);


    }

    /**
     * 表格的一些基础设置
     * @param \PHPExcel $PHPExcel
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function setBasics(\PHPExcel $PHPExcel, $basics = [])
    {
        $PHPExcel->getActiveSheet()->setTitle($this->sheetName);
        $PHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight($this->rowHeight);
        $PHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight($this->titleRoeHeight);
        return $PHPExcel;
    }

    public static function loadFile($phpExcelFile = self::PHP_EXCEL_URL)
    {
        require_once "{$phpExcelFile}";
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }


    private function setError()
    {
        error_reporting(E_ALL);
        ini_set(' display_errors ', TRUE);
        ini_set(' display_startup_errors ', TRUE);
        date_default_timezone_set('Asia/Shanghai');
        if (PHP_SAPI == 'cli') {
            die('This example should only be run from a Web Browser');
        }
    }

}

