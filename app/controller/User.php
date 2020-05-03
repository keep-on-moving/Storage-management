<?php
namespace app\controller;
use app\service\MenuService;
use app\service\UserService;
use think\Config;

class User extends Base
{
    protected $service;
    public function __construct(UserService $service)
    {
        parent::__construct();
        $this->service = $service;
    }  

    public function index()
    {
        $department = Config::get('department');
        $this->assign([
            'departments'    =>  $department,
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
        $info = db('user')->find($id);
        $department = Config::get('department');
        $this->assign([
            'departments'    =>  $department,
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
}
