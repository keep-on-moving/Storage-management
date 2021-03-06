<?php
namespace app\controller;
use app\service\ExcelExportService;
use app\service\OutstorageService,
    app\model\Product,
    app\model\Supplier,
    app\model\Order;
use think\Request;

class Outstorage extends Base
{
    protected $service;
    public function __construct(OutstorageService $service)
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
            'info'  =>  $this->service->edit($id),
            'product'    =>  Product::all(  [ 'status' => 0 ] ),
            'supplier'   =>  Supplier::all(  [ 'status' => 0 ] ),
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
    public function export(){
        $param = Request::instance()->get();
        $data = $this->service->page()->toArray();
        $name=$param['start_time'].'至'.$param['end_time'].'出库单';
        $header=[['id','出库单id'],['sn','出库单编号'],['author', '制表人'],['supplier', '供应商'], ['type', '出库类型'], ['outstorage_curator','保管员'], ['outstorage_consignee','提货人'], ['add_time','创单时间'],['info','出库单详情'] ,['desc','备注']];
        $newdata = [];
        foreach ($data['data'] as $key => $value){
            $newdata[$key]['id'] = $value['id'];
            $newdata[$key]['sn'] = $value['sn'];
            $newdata[$key]['author'] = $value['author'];
            $newdata[$key]['supplier'] = $value['supplier'];
            $newdata[$key]['type'] = $value['type'];
            $newdata[$key]['outstorage_curator'] = $value['outstorage_curator'];
            $newdata[$key]['outstorage_consignee'] = $value['outstorage_consignee'];
            $newdata[$key]['add_time'] = date('Y年m月d日 H时i分s秒', $value['add_time']);
            $res = json_decode($value['res'], true);
            $detail = '';
            foreach ($res as $val){
                $detail = $detail.'商品编号：'.$val[0].'  商品名称：'.$val[1]. ' 入库数量：'.$val[2].$val[5]. ' 包装：'.$val[4].' ||';
            }
            $newdata[$key]['info'] = $detail;
            $newdata[$key]['desc'] = $value['desc'];
        }

        $excel = new ExcelExportService();
        $excel->exportExcel($name,$header,$newdata);

    }
}
