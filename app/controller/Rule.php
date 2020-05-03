<?php
namespace app\controller;

use app\model\Role as RoleModel;
use app\service\MenuService;
use app\service\RoleService;
use app\service\RuleService;
use app\model\Rule as RuleModel;

class Rule extends Base
{
    protected $service;
    public function __construct(RuleService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index()
    {
        $this->assign(['list'	=>	$this->service->page()]);
        return view();
    }

    public function create(){
        $roleService = new RoleService();
        $this->assign([
            'roles'    =>  $roleService->all()
        ]);

        return view();
    }

    public function save()
    {
        return $this->service->save();
    }

    public function show($id)
    {
        $info = db('admin_user')
            ->alias('u')
            ->join('admin_role r', 'r.id = u.role_id')
            ->field('u.*, r.role_name')
            ->where('u.id', $id)
            ->find();
        $this->assign([
            'info'       =>  $info,
        ]);

        return view();
    }

    public function edit($id)
    {
        $info = db('admin_user')
            ->alias('u')
            ->join('admin_role r', 'r.id = u.role_id')
            ->field('u.*, r.role_name')
            ->where('u.id', $id)
            ->find();

        $roleService = new RoleService();
        $roles = $roleService->all();
        foreach ($roles as $key => $val){
            $roles[$key]['selected'] = 0;
            if($val['id'] == $info['role_id']){
                $roles[$key]['selected'] = 1;
            }
        }

        $this->assign([
            'roles'    =>  $roles,
            'info' =>  $info
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