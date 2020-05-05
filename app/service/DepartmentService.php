<?php
namespace app\service;

use app\model\Department;
use think\Request;
class DepartmentService
{

    public function page(){
        $param = Request::instance()->param();
        $where = [];
        if(isset($param['name']) && $param['name']){
            $where['name'] = $param['name'];
        }


        return Department::where($where)->order('id', 'desc')->paginate(10);
    }

    // 验证器
    private function _validate($data){
        // 验证
        $validate = validate('DepartmentValidate');
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
            $user 				= new Department();
            $user->name 		= $param['name'];
            if( $user->save() ){
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

            $user = Department::get($param['id']);
            $user->name 		= $param['name'];
            $user->desc = $param['desc'];

            // 检测错误
            if( $user->save() ){
                return ['error'	=>	0,'msg'	=>	'修改成功'];
            }else{
                return ['error'	=>	100,'msg'	=>	'修改失败'];
            }
        }else{
            return ['error'	=>	100,'msg'	=>	$error];
        }
    }

    public function delete($id){
        $count = db('user')->where('department', $id)->count();
        if($count){
            db('department')->where('id', $id)->update(['num' => $count]);
            return ['error'	=>	100,'msg'	=>	'删除失败,当前部门仍有所属人员，请先将其转移部门'];
        }

        if( Department::destroy($id) ){
            return ['error'	=>	0,'msg'	=>	'删除成功'];
        }else{
            return ['error'	=>	100,'msg'	=>	'删除失败'];
        }
    }

}
