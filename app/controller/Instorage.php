<?php
namespace app\controller;
use app\service\ExcelExportService;
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

    public function export(){
        $param = Request::instance()->get();
        $data = $this->service->page()->toArray();
        $name=$param['start_time'].'至'.$param['end_time'].'入库单';
        $header=[['id','出库单id'],['sn','出库单编号'],['author', '制表人'],['supplier', '供应商'], ['type', '出库类型'], ['instorage_checker_a','入库审核人A'], ['instorage_checker_b','入库审核人B'], ['add_time','创单时间'],['info','入库单详情'] ,['desc','备注']];
        $newdata = [];
        foreach ($data['data'] as $key => $value){
            $newdata[$key]['id'] = $value['id'];
            $newdata[$key]['sn'] = $value['sn'];
            $newdata[$key]['author'] = $value['author'];
            $newdata[$key]['supplier'] = $value['supplier'];
            $newdata[$key]['type'] = $value['type'];
            $newdata[$key]['instorage_checker_a'] = $value['instorage_checker_a'];
            $newdata[$key]['instorage_checker_b'] = $value['instorage_checker_b'];
            $newdata[$key]['add_time'] = date('Y年m月d日 H时i分s秒', $value['add_time']);
            $detail = '';
            $product = db('product')->find($value['product_id']);
            if($value['type'] == '采购入库'){
                $res = json_decode($value['res'], true);
                foreach ($res as $val){
                    $detail = $detail.'采购入库'.$product['name'].'的材料-材料ID：'.$val[0].'  材料名称：'.$val[1]. ' 入库数量：'.$val[2].$val[4]. ' 保质期：'.$val[3];
                }
                $detail = $detail.' 初步统计：'.$value['expected_num'];
            }else{
                $detail = '退货名称：'.$product['name'].'：退货数量：'.$value['return_num'].$product['unit']. '  退货保质期：'.$value['effective_time'].' 初步统计： '.$value['expected_num'];
            }

            $newdata[$key]['info'] = $detail;
            $newdata[$key]['desc'] = $value['desc'];
        }

        $excel = new ExcelExportService();
        $excel->exportExcel($name,$header,$newdata);

    }


}
