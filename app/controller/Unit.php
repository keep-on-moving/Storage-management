<?php
namespace app\controller;
use app\service\UnitService;

class Unit extends Base
{
    protected $service;
    public function __construct(UnitService $service)
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
        return view();
    }


    public function save()
    {
        return $this->service->save();
    }

    public function edit($id)
    {
        $this->assign(['info'  =>  $this->service->edit($id)]);   
        return view();
    }

    public function update(){
        return $this->service->update();
    }

    public function delete($id){
        return $this->service->delete($id);
    }
    
}
