<?php
namespace app\controller;

use app\service\ProductService;
use think\Request;
class Product extends Base
{
    protected $service;
    public function __construct(ProductService $service)
    {
        parent::__construct();
        $this->service = $service;
    }  

    public function index()
    {
        $this->assign(['list'  =>  $this->service->page()]);
        return view();
    }

    public function lists()
    {
        $data 	= Request::instance()->get();
        $where 	= [];
        if(isset($data['name']) && $data['name']){
            $where['p.name'] = ['like', '%'.$data['name'].'%'];
        }
        if(isset($data['sn']) && $data['sn']){
            $where['p.sn'] = $data['sn'];
        }

        if(isset($data['status']) && in_array($data['status'],[0,1]) && $data['status'] != ''){
            $where['p.status'] = $data['status'];
        }
        $sort = '';
        if(isset($data['sort']) && in_array($data['sort'], [0,1])){
            $sort = 'store';
            if($data['sort'] == 0){
                $sort = 'add_time';
            }
        }
        $list = [];
        if(!isset($data['type']) || $data['type'] == '' || $data['type'] == '1'){
            $spec = db('product_spec')
                ->alias('s')
                ->field('p.*, s.id sid, s.store, s.spec_name, s.add_time sadd_time')
                ->join('product p', 'p.id = s.product_id')
                ->where($where)
                ->select();

            foreach ($spec as $val){
                $temp = [];
                $temp = array(
                    'id' => $val['sid'],
                    'sn' => $val['sn'],
                    'type' => '材料',
                    'product_name' => $val['name'],
                    'spec_name' => $val['spec_name'],
                    'supplier' => $val['supplier'],
                    'customer' => $val['customer'],
                    'store' => $val['store'],
                    'price' => $val['price'],
                    'status' => $val['status'],
                    'storeage_location' => $val['storage'].$val['location'],
                    'add_time' => $val['sadd_time']
                );
                $list[] = $temp;
            }
        }

        if(!isset($data['type']) || $data['type'] == '' || $data['type'] == '0') {
            //封装where查询条件
            $products = db('product')->alias('p')->where($where)->select();
            foreach ($products as $val) {
                $temp = [];
                $temp = array(
                    'id' => $val['id'],
                    'sn' => $val['sn'],
                    'status' => $val['status'],
                    'type' => '成品',
                    'product_name' => $val['name'],
                    'spec_name' => '---',
                    'supplier' => $val['supplier'],
                    'customer' => $val['customer'],
                    'store' => $val['num'],
                    'price' => $val['price'],
                    'storeage_location' => $val['storage'] . $val['location'],
                    'add_time' => date('Y-m-d H:i:s', $val['add_time'])
                );
                $list[] = $temp;
            }
        }
        if($sort){
            $sortKey =  array_column($list, $sort);//取出数组中serverTime的一列，返回一维数组
            array_multisort($sortKey,SORT_DESC,$list);//排序，根据$serverTime 排序
        }

        $this->assign('list', $list);

        return view();
    }


    public function create()
    {
        $this->assign( $this->service->_init() );
        return view();
    }


    public function save()
    {
        return $this->service->save();
    }

    public function edit($id)
    {
        $this->assign( $this->service->_init() );
        $this->assign(['info'  =>  $this->service->edit($id)]);   
        return view();
    }

    public function update(){
        return $this->service->update();
    }

    public function delete($id){
        return $this->service->delete($id);
    }

    public function packing($id){
        $units = db('unit')->where('status', 0)->select();
//        $this->assign( $this->service->_init() );
        $this->assign('units', $units);
        $this->assign(['info'  =>  $this->service->edit($id)]);

        return view();
    }

    public function pack(){
        return $this->service->pack();
    }

    public function store(){
        $data = Request::instance()->param();	//获取参数
        if($data['type'] == '材料'){
            $status = db('product_spec')->where('id', $data['id'])->update(['store' => $data['store']]);
        }else{
            $status = db('product')->where('id', $data['id'])->update(['num' => $data['store']]);
        }

        if( $status ){
            return ['error'	=>	0,'msg'	=>	'库存修改成功'];
        }else{
            return ['error'	=>	100,'msg' => '库存修改失败'];
        }
    }
}
