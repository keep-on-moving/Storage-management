<?php
namespace app\service;

use app\model\User;
use think\Request;
class UserService
{

    public function page(){
        $param = Request::instance()->param();
        $where = [];
        if(isset($param['truename']) && $param['truename']){
            $where['truename'] = $param['truename'];
        }

        if(isset($param['phone']) && $param['phone']){
            $where['phone'] = $param['phone'];
        }

        if(isset($param['department']) && $param['department']){
            $where['department'] = $param['department'];
        }

        $where['status'] = 0;

        return User::where($where)->order('id', 'desc')->paginate(10);
    }

    // 验证器
    private function _validate($data){
        // 验证
        $validate = validate('UserValidate');
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
            $user 				= new User();
            $user->truename 		= $param['truename'];
            $user->phone 		= $param['phone'];
            $user->email 		= $param['email'];
            $user->department = $param['department'];
            $user->desc = $param['desc'];

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

            $user = User::get($param['id']);
            $user->truename 		= $param['truename'];
            $user->phone 		= $param['phone'];
            $user->email 		= $param['email'];
            $user->department = $param['department'];
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
        if( User::destroy($id) ){
            return ['error'	=>	0,'msg'	=>	'删除成功'];
        }else{
            return ['error'	=>	100,'msg'	=>	'删除失败'];
        }
    }

}
