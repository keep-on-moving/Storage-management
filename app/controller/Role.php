<?php
namespace app\controller;
use app\model\Menu;
use app\model\Product;
use app\model\Supplier;
use app\service\MenuService;
use app\service\RoleService;
use app\model\Role as RoleModel;
use think\Request;

class Role extends Base
{
    protected $service;
    public function __construct(RoleService $service)
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
        $menuService = new MenuService();
        $this->assign([
            'menus'    =>  $menuService->getChild()
        ]);

        return view();
    }

    public function save()
    {
        return $this->service->save();
    }

    public function show($id)
    {
        $info = RoleModel::get(['id'=>$id]);
        $info['menu_list'] = explode(',', $info['menu_list']);
        $menuService = new MenuService();
        $menus = $menuService->getChild();
        foreach ($menus as $key => $val){
            $menus[$key]['selected'] = 0;
            if(in_array($val['id'], $info['menu_list'])){
                $menus[$key]['selected'] = 1;
            }
        }

        $this->assign([
            'menus'    =>  $menus,
            'info'       =>  $info,
        ]);

        return view();
    }

    public function edit($id)
    {
        $info = RoleModel::get(['id'=>$id]);
        $info['menu_list'] = explode(',', $info['menu_list']);
        $menuService = new MenuService();
        $menus = $menuService->getChild();
        foreach ($menus as $key => $val){
            $menus[$key]['selected'] = 0;
            if(in_array($val['id'], $info['menu_list'])){
                $menus[$key]['selected'] = 1;
            }
        }

        $this->assign([
            'menus'    =>  $menus,
            'info'       =>  $info,
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
