<?php
namespace app\service;
use think\Db;
use think\Request,
	app\model\Order,
	app\model\Product,
	app\validate\OrderValidate;

class OutstorageService
{

    public function page(){

    	$data 	= Request::instance()->get();
    	$where 	= [];
    	
    	//封装where查询条件
    	empty($data['type']) 	|| $where['type'] 	= 	$data['type'];
    	empty($data['author'])	|| $where['author']		= 	['like','%'.$data['author'] ];
    	empty($data['sn'])		|| $where['sn'] 		= 	$data['sn'];

    	$where['state'] 		= 	2;

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
			$order->supplier 	= $param['supplier'];
			$order->outstorage_checker = $param['outstorage_checker'];
			$order->outstorage_curator = $param['outstorage_curator'];
			$order->outstorage_consignee = $param['outstorage_consignee'];
			$order->state 		= 2;
			$order->add_time	= time();

			$temp = [];
			$productModel = new Product();
            Db::startTrans();
			foreach ( $param['product'] as $k=>$v) {
				$vv = explode('_',$v);
				$product = Product::get([ 'sn' => $vv[0] ]);
				$pNum = $product->num;
				$product->num =  $pNum - $param['num'][$k];
				if($product->num < 0){
                    Db::rollback();
					return ['error'	=>	100,'msg'	=>	'保存失败:'.$product->name.'仅有'.$pNum.$product->unit."低于要出库个数！"];
				}
				$product->save();
				$temp[] =  [
					$vv[0],
					$vv[1],
					$param['num'][$k],
					$param['time'][$k],
					$productModel->changeUnit($product->id, $param['num'][$k]),
					$vv[2],
					$vv[3],
					$vv[4],
				];
			}

			$order->res = json_encode( $temp );
			// dump( $order->res );
			// $order->res 		= $param['res'];
			// die();

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
			$order->sn 			= $param['sn'];
			$order->type 		= $param['type'];
			$order->desc 		= $param['desc'];
			$order->author 		= $param['author'];
			$order->supplier 	= $param['supplier'];
			$order->outstorage_checker = $param['outstorage_checker'];
			$order->outstorage_curator = $param['outstorage_curator'];
			$order->outstorage_consignee = $param['outstorage_consignee'];
			$order->state 		= 2;

			$temp = [];
			$productModel = new Product();
            Db::startTrans();
			//todo 先还回库存
            $res = $order->res;
            if($res){
                $res = json_decode($res, true);
                foreach ($res as $val){
                    \db('product')->where('sn', $val[0])->setInc('num', $val[2]);
                }
            }
			foreach ( $param['product'] as $k=>$v) {
				$vv = explode('_',$v);
				$product = Product::get([ 'sn' => $vv[0] ]);
				$pNum = $product->num;
				$product->num =  $pNum - $param['num'][$k];
				if($product->num < 0){
				    Db::rollback();
					return ['error'	=>	100,'msg'	=>	'保存失败:'.$product->name.'仅有'.$pNum.$product->unit."低于要出库个数！"];
				}
				$product->save();
				$temp[] =  [
					$vv[0],
					$vv[1],
					$param['num'][$k],
					$param['time'][$k],
					$productModel->changeUnit($product->id, $param['num'][$k]),
					$vv[2],
					$vv[3],
					$vv[4],
				];
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
