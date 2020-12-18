<?php


namespace sdModule\layuiSearch\generate;

/**
 * 普通文本类型
 * Class Text
 * @package app\common\layuiSearch
 */
class Text extends FormVar implements FormDefine
{

    /**
     * @param string $name
     * @param string $placeholder
     * @return Text
     */
    public function name(string $name, string $placeholder = ''):self
    {
        $this->name = $name;
        $this->placeholder = $placeholder;
        return $this;
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
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label ? "<label class=\"layui-form-label\">{$this->placeholder}</label>"  : '';
    }


    /**
     * @return LayuiForm
     */
    public function html():LayuiForm
    {
        if ($this->label) {
            $label = "<label class=\"layui-form-label\">{$this->placeholder}</label>";
        }
        $html = <<<HTM
                <div class="layui-inline">
                    {$this->getLabel()}
                    <div class="layui-input-inline">
                        <input type="text" name="{$this->name}"  placeholder="{$this->placeholder}" autocomplete="off" class="layui-input">
                    </div>
                </div>
HTM;
        return LayuiForm::generate($html);
    }
}

