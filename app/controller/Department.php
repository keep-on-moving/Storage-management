<?php
namespace app\controller;
use app\service\MenuService;
use app\service\DepartmentService;
use think\Config;

class Department extends Base
{
    protected $service;
    public function __construct(DepartmentService $service)
    {
        parent::__construct();
        $this->service = $service;
    }  

    public function index()
    {
        $this->assign([
            'list'	=>	$this->service->page()
        ]);

        return view();
    }

    public function create(){
        $department = Config::get('department');
        $this->assign([
            'departments'    =>  $department
        ]);

        return view();
    }

    public function save()
    {
        return $this->service->save();
    }

    public function edit($id)
    {
        $info = db('department')->find($id);
        $this->assign([
            'info' => $info
        ]);

        return view();
    }

    public function update()
    {
        return $this->service->update();
    }

    public function delete($id){
        return $this->service->delete($id);
    }


    public function getList(){
    	return [];
    }

    public function show($id)
    {
        $info = db('user')
            ->alias('u')
            ->join('department d', 'd.id = u.department')
            ->field('u.*, d.name dname')
            ->where('d.id', $id)
            ->select();
        $this->assign([
            'info'       =>  $info,
        ]);

        return view();
    }
}
