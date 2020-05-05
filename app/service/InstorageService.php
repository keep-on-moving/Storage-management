<?php
namespace app\service;

use app\model\Spec;
use app\validate\SpecValidate;
use think\Db;
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
    	// $config['page'] = isset($data['page']) ? $data['page'] : 1;
		$data = Order::where($where)->order('id', 'desc')->paginate(10000);
		foreach ($data as $key => $val){
		    if(time() > ($val['add_time']+24*3600*2)){
		        $data[$key]['canDel'] = 0;
		        $data[$key]['canEdit'] = 0;
            }else{
		        $data[$key]['canDel'] = 1;
                $data[$key]['canEdit'] = 1;
                if(time() > ($val['add_time']+24*3600)){
                    $data[$key]['canEdit'] = 0;
                }
            }
        }

		return $data;
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
			$order->instorage_checker_a = $param['instorage_checker_a'];
			$order->instorage_checker_b = $param['instorage_checker_b'];
			$order->supplier 	= $param['supplier'];
			$order->state 		= 1;
			$order->add_time	= time();
			$order->product_id  = $param['product_id'];
			$order->return_num  = $param['return_num'];
			$order->effective_time = $param['effective_time'];

			$temp = [];
            Db::startTrans();
			foreach ( $param['spec_id'] as $k=>$v) {
				$temp[] =  [
				    $v,
                    $param['spec_name'][$k],
					$param['num'][$k],
					$param['time'][$k]
				 ];

				$pec = Spec::get([ 'id' => $v ]);
				if(!$param['num'][$k]){
                    $param['num'][$k] = 0;
                }
                $pec->store +=  $param['num'][$k];
                $pec->save();
			}

			if($param['return_num']){
			    $product = Product::get(['id' => $param['product_id']]);
			    $product->num += $param['return_num'];
			    $product->save();
            }

			$canMakeNum = 0;
            if($temp){
                $successNum = [];
                foreach ($temp as $val){
                    $spec_id = $val[0];
                    $num = $val[2];
                    $spec = db('product_spec')->find($spec_id);
                    $successNum[] = bcdiv($num, $spec['spec_num'], 0);
                }

                $specCount = db('product_spec')->where('product_id', $param['product_id'])->count();
                if($specCount == count($successNum)){
                    $canMakeNum = min($successNum);
                }
            }
            $productModel = new Product();
            if($param['type'] == '采购入库'){
                $order->expected_num = '部分材料不足，不能生产产品';
                if($canMakeNum){
                    $order->expected_num = $productModel->changeUnit($param['product_id'], $canMakeNum);
                }
            }else{
                $order->expected_num = $productModel->changeUnit($param['product_id'], $param['return_num']);
            }

			$order->res = json_encode( $temp );

			// 检测错误
			if( $order->save() ){
			    Db::commit();
				return ['error'	=>	0,'msg'	=>	'保存成功'];
			}else{
			    Db::rollback();
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
			if (time() > ($order['add_time'] + 24*3600)){
                return ['error'	=>	100,'msg' => '订单已经生成24小时，不支持修改！'];
            }
            $order->type 		= $param['type'];
            $order->desc 		= $param['desc'];
            $order->author 		= $param['author'];
            $order->instorage_checker_a = $param['instorage_checker_a'];
            $order->instorage_checker_b = $param['instorage_checker_b'];
            $order->supplier 	= $param['supplier'];
            $order->add_time	= time();
            $order->product_id  = $param['product_id'];
            $order->return_num  = $param['return_num'];
            $order->effective_time = $param['effective_time'];

            $temp = [];
            Db::startTrans();
            //todo 先还回库存
            $res = $order->res;
            if($res){
                $res = json_decode($res, true);
                foreach ($res as $val){
                    \db('product_spec')->where('id', $val[0])->setDec('store', $val[2]);
                }
            }
            foreach ( $param['spec_id'] as $k=>$v) {
                $temp[] =  [
                    $v,
                    $param['spec_name'][$k],
                    $param['num'][$k],
                    $param['time'][$k]
                ];

                $pec = Spec::get([ 'id' => $v ]);
                if(!$param['num'][$k]){
                    $param['num'][$k] = 0;
                }
                $pec->store +=  $param['num'][$k];
                $pec->save();
            }

            if($param['return_num']){
                $product = Product::get(['id' => $param['product_id']]);
                $product->num += $param['return_num'];
                $product->save();
            }

            $canMakeNum = 0;
            if($temp){
                $successNum = [];
                foreach ($temp as $val){
                    $spec_id = $val[0];
                    $num = $val[2];
                    $spec = db('product_spec')->find($spec_id);
                    $successNum[] = bcdiv($num, $spec['spec_num'], 0);
                }

                $specCount = db('product_spec')->where('product_id', $param['product_id'])->count();
                if($specCount == count($successNum)){
                    $canMakeNum = min($successNum);
                }
            }
            $productModel = new Product();
            if($param['type'] == '采购入库'){
                $order->expected_num = '部分材料不足，不能生产产品';
                if($canMakeNum){
                    $order->expected_num = $productModel->changeUnit($param['product_id'], $canMakeNum);
                }
            }else{
                $order->expected_num = $productModel->changeUnit($param['product_id'], $param['return_num']);
            }

            $order->res = json_encode( $temp );

			// 检测错误
			if( $order->save() ){
			    Db::commit();
				return ['error'	=>	0,'msg'	=>	'修改成功'];
			}else{
			    Db::rollback();
				return ['error'	=>	100,'msg'	=>	'修改失败'];	
			}
		}else{
			return ['error'	=>	100,'msg'	=>	$error];
		}


    }

    public function delete($id){

        $order = Order::get($id);

        if (time() > ($order['add_time'] + 24*3600*2)){
            return ['error'	=>	100,'msg' => '订单已经生成48小时，不支持删除！'];
        }

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
