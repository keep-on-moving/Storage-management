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
        $service = new MenuService();

        $_menuList['father'] = $service->getFather();
        $_menuList['child'] = $service->getChild();


        $this->assign([
        	'my_info'	=>	User::get(['id' => Session::get('uid','think') ]),
            '_menuList' =>   $_menuList
        ]);

    }
    

}
