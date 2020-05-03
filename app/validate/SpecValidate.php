<?php
namespace app\validate;
use think\Validate;

class SpecValidate extends Validate
{
    protected $rule = [
        'product_id'      =>  'require|number',
        'spec_name'      =>  'require',
        'spec_num'      => 'require|number'
    ];

    protected $message  =   [
        'product_id.require'      =>  '产品不能为空',
        'product_id.int'          =>  '产品id必须为数字',
        'spec_name.require'          =>  '规格名称不能为空',
        'spec_num.require' => '规格数量不能为空',
        'spec_num.int' => '数量必须为数字'
    ];
}
