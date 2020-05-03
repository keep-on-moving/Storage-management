<?php
namespace app\model;
use think\Model;

class Spec extends Model
{
	protected $pk = 'id';
	protected $table = 'w_product_spec';

	public function getIndex($where){

	    return self::alias('s')
            ->join('product p', 's.product_id = p.id')
            ->where($where)
            ->field('s.*, p.name')
            ->order('s.id', 'desc')
            ->paginate(10000);
	}
}

