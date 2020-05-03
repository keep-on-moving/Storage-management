<?php
namespace app\validate;
use think\Validate;

class RoleValidate extends Validate
{
    protected $rule = [
        'role_name'      =>  'require|max:25',
        'role_desc'      =>  'require',
        'menu_list'      =>  'require'
    ];

    protected $message  =   [
        'role_name.require'    =>  '角色名称必填',
        'role_name.max'        =>  '角色名称最多不能超过25个字符',
        'role_desc.require'        =>  '角色描述为必填项',
        'menu_list.require'          =>  '该角色没有设置权限，请赋予权限'
    ];
}
