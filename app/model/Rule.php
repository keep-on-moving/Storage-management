<?php
namespace app\model;
use think\Model;

class Rule extends Model
{
	protected $pk = 'id';
	protected $table = 'w_admin_user';

	public function getList($param){
	    $where = [];
	    if(isset($param['username'])){
	        $where['r.username'] = $param['username'];
        }
	    return self::alias('r')
            ->join('admin_role re', 're.id = r.role_id')
            ->where($where)
            ->field('r.*, re.role_name')
            ->order('r.id', 'desc')
            ->paginate(10000);
    }
}

