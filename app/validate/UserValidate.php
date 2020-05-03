<?php
namespace app\validate;
use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'truename'      =>  'require|max:25',
        'phone'      =>  'require',
        'department' => 'require',
    ];

    protected $message  =   [
        'truename.require'    =>  '新员工名称必填',
        'truename.max'        =>  '新员工名称最多不能超过25个字符',
        'phone.require'          =>  '手机号为必填项',
        'department.require'          =>  '部门为必填项'
    ];
}
