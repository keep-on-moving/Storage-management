<?php
namespace app\service;

use app\model\Role;
use think\Request;

class RoleService
{

    public function page(){
        $data 	= Request::instance()->get();
        $where 	= [];
        if(isset($data['role_name'])){
            $where['role_name'] = $data['role_name'];
        }

        return Role::where($where)->order('id', 'desc')->paginate(10);
    }

    public function all(){
        return Role::where([])->order('id', 'desc')->select();
    }

    // 验证器
    private function _validate($data){
        // 验证
        $validate = validate('RoleValidate');
        if(!$validate->check($data)){
            return $validate->getError();
        }
    }

    // 保存数据
    public function save(){
        Request::instance()->isPost() || die('request not  post!');

        $param = Request::instance()->param();	//获取参数
        $error = $this->_validate($param); 		// 是否通过验证


        if( is_null( $error ) ){
            $role = db('admin_role')->where('role_name', $param['role_name'])->find();
            if($role){
                return ['error'	=>	100,'msg'	=>	'角色名称已经存在，请换一个'];
            }
            $role 				= new Role();
            $role->role_name 			= $param['role_name'];
            $role->role_desc 		= $param['role_desc'];
            $role->menu_list 		= implode(',',$param['menu_list']);
            if( $role->save() ){
                return ['error'	=>	0,'msg'	=>	'保存成功'];
            }else{
                return ['error'	=>	100,'msg'	=>	'保存失败'];
            }
        }else{
            return ['error'	=>	100,'msg'	=>	$error];
        }
    }

    public function update(){
        Request::instance()->isPost() || die('request not  post!');

        $param = Request::instance()->param();	//获取参数
        $error = $this->_validate($param); 		// 是否通过验证

        if( is_null( $error ) ){

            $role = Role::get($param['id']);
            $role->role_name 			= $param['role_name'];
            $role->role_desc 		= $param['role_desc'];
            $role->menu_list 		= implode(',',$param['menu_list']);

            // 检测错误
            if( $role->save() ){
                return ['error'	=>	0,'msg'	=>	'修改成功'];
            }else{
                return ['error'	=>	100,'msg'	=>	'修改失败'];
            }
        }else{
            return ['error'	=>	100,'msg'	=>	$error];
        }
    }

    public function delete($id){
        if( Role::destroy($id) ){
            return ['error'	=>	0,'msg'	=>	'删除成功'];
        }else{
            return ['error'	=>	100,'msg'	=>	'删除失败'];
        }
    }

}
