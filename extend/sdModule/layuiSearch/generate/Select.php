<?php


namespace sdModule\layuiSearch\generate;

/**
 * 下拉选择
 * Class Select
 * @package sdModule\layuiSearch\generate
 */
class Select extends FormVar implements FormDefine
{
    /**
     * @param string $name
     * @param string $placeholder
     * @return Select
     */
    public function name(string $name, string $placeholder = ''):self
    {
        $this->name = $name;
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label ? "<label class=\"layui-form-label\">{$this->placeholder}</label>"  : '';
    }

    /**
     * @param bool $status
     * @return $this|mixed
     */
    public function label(bool $status)
    {
        $this->label = $status;
        return $this;
    }

    /**
     * @param array $data
     * @return LayuiForm
     */
    public function html(array $data = []): LayuiForm
    {
        $option = "<option value=''>{$this->placeholder}</option>" . PHP_EOL;
        foreach ($data as $key => $value){
            $option .= <<<OPT
                            <option value="{$key}">{$value}</option>
OPT;
        }

        $html =  <<<HTM
                <div class="layui-inline">
                    {$this->getLabel()}
                    <div class="layui-input-inline">
                        <select name="{$this->name}">
                            {$option}
                        </select>
                    </div>
                </div>
HTM;
        return LayuiForm::generate($html);
    }

}

