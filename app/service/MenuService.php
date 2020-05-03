<?php
namespace app\service;
use app\model\Menu;

class MenuService
{

    public function getFather($father){

        return Menu::all([
        	'status'	=>	0,
        	'level'		=>	0,
            'id' => ['in', $father]
        ]);
    }
  
    public function getChild($child){
        return Menu::all([
        	'status'	=>	0,
        	'level'		=>	['>',0],
            'id' => ['in', $child]
        ]);
    }

}
