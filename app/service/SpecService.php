<?php
namespace app\service;

use app\model\Spec;
use think\Request,
	app\model\Unit,
	app\model\Product,
	app\model\Storage,
	app\model\Category,
	app\model\Customer,
	app\model\Supplier,
	app\model\Location,
	app\validate\ProductValidate;

class SpecService{

    public function page(){

    	$data 	= Request::instance()->get();
    	$where 	= [];

    	//封装where查询条件
		if(isset($data['spec_name']) && $data['spec_name']){
			$where['s.spec_name'] = ['like','%'.$data['spec_name'].'%' ];
		}
		
		if(isset($data['product_name']) && $data['product_name']){
			$where['p.name'] = ['like','%'.$data['product_name'].'%' ];
		}

		$sepc = new Spec(); 

        return $sepc->getIndex($where);
    }


    public function _init(){

    	return [
    		'unit'		=>	Unit::all(     [ 'status' => 0 ] ),
    		'category'	=>	Category::all( [ 'status' => 0 ] ),
    		'customer'	=>	Customer::all( [ 'status' => 0 ] ),
    		'supplier'	=>	Supplier::all( [ 'status' => 0 ] ),
    		'storage'	=>	Storage::all(  [ 'status' => 0 ] ),
    		'location'	=>	Location::all( [ 'status' => 0 ] ),
    	];
    }


    // 保存数据
    public function save(){
    	Request::instance()->isPost() || die('request not  post!');
    	
		$param = Request::instance()->param();	//获取参数
		$error = $this->_validate($param); 		// 是否通过验证

		if( is_null( $error ) ){

			if( Spec::get(['spec_name' => $param['spec_name'], 'product_id' => $param['product_id'] ]) ){
				return ['error'	=>	100,'msg'	=>	'名称已经存在'];
				exit();	
			}

			$spec 			= new Spec();
			$spec->product_id 		= $param['product_id'];
			$spec->spec_name 		= $param['spec_name'];
			$spec->spec_num 		= $param['spec_num'];
			$spec->add_time 	= date('Y-m-d H:i:s');

			// 检测错误
			if( $spec->save() ){
				return ['error'	=>	0,'msg'	=>	'保存成功'];
			}else{
				return ['error'	=>	100,'msg'	=>	'保存失败'];	
			}
			
		}else{
			return ['error'	=>	100,'msg'	=>	$error];
		}

    }

    public function edit($id){
    	return Spec::get($id);
    }


    public function update(){
    	Request::instance()->isPost() || die('request not  post!');
    	
		$param = Request::instance()->param();	//获取参数
		$error = $this->_validate($param); 		// 是否通过验证

		if( is_null( $error ) ){

			$spec = Spec::get($param['id']);
			$spec->product_id 		= $param['product_id'];
			$spec->spec_name 		= $param['spec_name'];
			$spec->spec_num 		= $param['spec_num'];
			$spec->add_time 	= date('Y-m-d H:i:s');

			// 检测错误
			if( $spec->save() ){
				return ['error'	=>	0,'msg'	=>	'修改成功'];
			}else{
				return ['error'	=>	100,'msg'	=>	'修改失败'];	
			}
		}else{
			return ['error'	=>	100,'msg'	=>	$error];
		}


    }

    public function delete($id){
    	if( Spec::destroy($id) ){
    		return ['error'	=>	0,'msg'	=>	'删除成功'];
    	}else{
    		return ['error'	=>	100,'msg'	=>	'删除失败'];	
    	}
    }


    // 验证器
    private function _validate($data){
		// 验证
		$validate = validate('SpecValidate');
		if(!$validate->check($data)){
			return $validate->getError();
		}
    }

}
