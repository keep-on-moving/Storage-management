<?php
namespace app\service;
use app\model\User;

class UserService
{

    public function page(){

        return User::where('status',0)->paginate(10);
    }
  


}
