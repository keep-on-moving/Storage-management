<?php
namespace app\validate;
use think\Validate;

class RuleValidate extends Validate
{
    protected $rule = [
        'username'      =>  'require|max:25|unique:admin_user',
        'password'      =>  'require',
        'phone'      =>  'require',
    ];

    protected $message  =   [
        'username.require'    =>  '管理员名称必填',
        'username.max'        =>  '管理员名称最多不能超过25个字符',
        'username.unique'        =>  '管理员名称已经存在，请重新设置',
        'password.require'        =>  '密码必须设置',
        'phone.require'          =>  '手机号为必填项'
    ];
}
