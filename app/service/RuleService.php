<?php
namespace app\service;

use app\model\Rule;
use think\Request;
class RuleService
{
    public function page(){
        $rule = new Rule();
        $param = Request::instance()->param();	//获取参数
        return $rule->getList($param);
    }

    // 验证器
    private function _validate($data){
        // 验证
        $validate = validate('RuleValidate');
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
            $rule = new Rule();
            $rule->username = $param['username'];
            $rule->password = md5($param['password']);
            $rule->truename = $param['truename'];
            $rule->role_id = $param['role_id'];
            $rule->desc = $param['desc'];
            if( $rule->save() ){
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

            $rule = Rule::get($param['id']);
            $rule->username = $param['username'];
            $rule->password = md5($param['password']);
            $rule->truename = $param['truename'];
            $rule->role_id = $param['role_id'];
            $rule->desc = $param['desc'];

            // 检测错误
            if( $rule->save() ){
                return ['error'	=>	0,'msg'	=>	'修改成功'];
            }else{
                return ['error'	=>	100,'msg'	=>	'修改失败'];
            }
        }else{
            return ['error'	=>	100,'msg'	=>	$error];
        }
    }

    public function delete($id){
        if( Rule::destroy($id) ){
            return ['error'	=>	0,'msg'	=>	'删除成功'];
        }else{
            return ['error'	=>	100,'msg'	=>	'删除失败'];
        }
    }
}
