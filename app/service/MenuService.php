<?php
namespace app\service;
use app\model\Menu;

class MenuService
{

    public function getFather(){
        return Menu::all([
        	'status'	=>	0,
        	'level'		=>	0
        ]);
    }
  
    public function getChild(){
        return Menu::all([
        	'status'	=>	0,
        	'level'		=>	['>',0]
        ]);
    }

}
