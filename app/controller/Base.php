<?php
namespace app\controller;

use think\Controller,
	think\Session,
	app\model\User,
    app\service\MenuService;

class Base extends Controller
{
	public function __construct(){
		parent::__construct();
    	if( !Session::get('uid','think') ){
            return $this->redirect("Login/index"); 
        }

    	$info = \app\model\Rule::get(['id' => Session::get('uid','think') ]);
    	$role = db('admin_role')->find($info['role_id']);
    	$child = explode(',', $role['menu_list']);
        $service = new MenuService();

        $_menuList['child'] = $service->getChild($child);
        $father = [];
        foreach ($_menuList['child'] as $value){
            $father[] = $value['pid'];
        }
        $_menuList['father'] = $service->getFather($father);


        $this->assign([
        	'my_info'	=>	$info,
            '_menuList' =>   $_menuList
        ]);

    }
    

}
