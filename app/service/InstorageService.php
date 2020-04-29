<?php
namespace app\service;
use think\Request,
	app\model\Order,
	app\model\Product,
	app\validate\OrderValidate;

class InstorageService
{

    public function page(){

    	$data 	= Request::instance()->get();
    	$where 	= [];

    	//封装where查询条件
    	empty($data['type']) 	|| $where['type'] 	= 	$data['type'];
    	empty($data['author'])	|| $where['author']		= 	['like','%'.$data['author'] ];
    	empty($data['sn'])		|| $where['sn'] 		= 	$data['sn'];
    	$where['state'] 		= 	'1';
        return Order::where($where)->paginate(10);     
    }

    // 保存数据
    public function save(){
    	Request::instance()->isPost() || die('request not  post!');
    	
		$param = Request::instance()->param();	//获取参数
		$error = $this->_validate($param); 		// 是否通过验证


		if( is_null( $error ) ){

			$order 				= new Order();
			$order->sn 			= $param['sn'];
			$order->type 		= $param['type'];
			$order->desc 		= $param['desc'];
			$order->author 		= $param['author'];
			$order->supplier 	= $param['supplier'];
			$order->state 		= 1;
			$order->add_time	= time();

			$temp = [];
			foreach ( $param['product'] as $k=>$v) {
				$vv = explode('_',$v);
				$temp[] =  [
					$vv[0],
					$vv[1],
					$param['num'][$k],
					$vv[2],
					$vv[3],
					$vv[4],
				 ];

				$product = Product::get([ 'sn' => $vv[0] ]);
				$product->num +=  $param['num'][$k];
				$product->save();
			}
			$order->res = json_encode( $temp );
			// dump( $order->res );
			// $order->res 		= $param['res'];
			// die();

			// 检测错误
			if( $order->save() ){
				return ['error'	=>	0,'msg'	=>	'保存成功'];
			}else{
				return ['error'	=>	100,'msg'	=>	'保存失败'];	
			}
			
		}else{
			return ['error'	=>	100,'msg'	=>	$error];
		}

    }


    public function edit($id){
    	return Order::get($id);
    }


    public function update(){
    	Request::instance()->isPost() || die('request not  post!');
    	
		$param = Request::instance()->param();	//获取参数
		$error = $this->_validate($param); 		// 是否通过验证

		if( is_null( $error ) ){

			$order = Order::get($param['id']);
			$order->sn 			= $param['sn'];
			$order->res 		= $param['res'];
			$order->type 		= $param['type'];
			$order->desc 		= $param['desc'];
			$order->author 		= $param['author'];
			$order->supplier 	= $param['supplier'];
			$order->state 		= 1;

			// 检测错误
			if( $order->save() ){
				return ['error'	=>	0,'msg'	=>	'修改成功'];
			}else{
				return ['error'	=>	100,'msg'	=>	'修改失败'];	
			}
		}else{
			return ['error'	=>	100,'msg'	=>	$error];
		}


    }

    public function delete($id){
    	if( Order::destroy($id) ){
    		return ['error'	=>	0,'msg'	=>	'删除成功'];
    	}else{
    		return ['error'	=>	100,'msg'	=>	'删除失败'];	
    	}
    }


    // 验证器
    private function _validate($data){
		// 验证
		$validate = validate('OrderValidate');
		if(!$validate->check($data)){
			return $validate->getError();
		}
    }




}
