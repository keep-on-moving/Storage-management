<?php
namespace app\validate;
use think\Validate;

class DepartmentValidate extends Validate
{
    protected $rule = [
        'name'      =>  'require|max:25|unique:department',
    ];

    protected $message  =   [
        'name.require'    =>  '部门名称必填',
        'name.max'        =>  '部门名称最多不能超过25个字符',
        'name.unique'        =>  '部门名称已经存在，请重新设置'
    ];
}
