<?php
namespace app\controller;
use app\service\InstorageService,
    app\model\Product,
    app\model\Supplier,
    app\model\Order;
use think\Request;

class Instorage extends Base
{
    protected $service;
    public function __construct(InstorageService $service)
    {
        parent::__construct();
        $this->service = $service;
    }  

    public function index()
    {
        $this->assign(['list'  =>  $this->service->page()]);
        return view();
    }

    public function create()
    {
        $this->assign([
            'product'    =>  Product::all(  [ 'status' => 0 ] ),
            'supplier'   =>  Supplier::all(  [ 'status' => 0 ] ),
        ]);
        return view();
    }

    public function spec(){
        $data 	= Request::instance()->get();
        $specs = \db('product_spec')->where('product_id', $data['product_id'])->select();
        $this->assign('specs', $specs);

        return view();
    }

    public function show($id)
    {
        $this->assign([
            'product'    =>  Product::all(  [ 'status' => 0 ] ),
            'supplier'   =>  Supplier::all(  [ 'status' => 0 ] ),
            'info'       =>  Order::get(['id'=>$id]),
        ]);
        return view();
    }

    public function save()
    {
        return $this->service->save();
    }

    public function edit($id)
    {
        $this->assign([
            'product'    =>  Product::all(  [ 'status' => 0 ] ),
            'supplier'   =>  Supplier::all(  [ 'status' => 0 ] ),
            'info'  =>  $this->service->edit($id)
        ]);

        return view();
    }

    public function update(){
        return $this->service->update();
    }

    public function delete($id){
        return $this->service->delete($id);
    }
    
    public function prints($id)
    {
        $this->assign([
            'product'    =>  Product::all(  [ 'status' => 0 ] ),
            'supplier'   =>  Supplier::all(  [ 'status' => 0 ] ),
            'info'       =>  Order::get(['id'=>$id]),
        ]);
        return view();
    }

    public function barcode($sn){
        include ROOT_PATH.'/extend/Barcode.php';
        die( \Barcode::code39($sn) );  
    }


}
