<?php
namespace app\service;
use app\model\Menu;

class MenuService
{

    public function getFather($father = []){
        if($father){
            return Menu::all([
                'status'	=>	0,
                'level'		=>	0,
                'id' => ['in', $father]
            ]);
        }

        return Menu::all([
            'status'	=>	0,
            'level'		=>	0
        ]);
    }
  
    public function getChild($child = []){
        if($child){
            return Menu::all([
                'status'	=>	0,
                'level'		=>	['>',0],
                'id' => ['in', $child]
            ]);
        }

        return Menu::all([
            'status'	=>	0,
            'level'		=>	['>',0]
        ]);
    }

}
