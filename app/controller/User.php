<?php
namespace app\controller;
use app\service\UserService;

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
		$this->assign(['list'	=>	$this->service->page()]);
        return view();
    }

    public function getList(){
    	return [];
    }
}
