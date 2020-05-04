<?php
namespace app\model;
use think\Model;

class Product extends Model
{
	protected $pk = 'id';

	public function changeUnit($productId, $num){
	    $product = $this->find($productId);
	    $unitInfo = db('unit')->where('name', $product['unit'])->find();
	    if(!$product['unit1'] && !$product['unit2'] &&  !$product['unit3']){//如果没有设置返回默认单位
	        return $num.$product['unit'];
        }
	    if ($product['unit1'] == $unitInfo['id']){//如果顶级单位为默认单位
            return $num.$product['unit'];
        }
        if ($product['unit2'] == $unitInfo['id']){//如果顶级单位为默认单位的母单位
            $firstNum = bcdiv($num, $product['unit2_num'], 0) * $product['unit1_num'];
            $firstUnit = db('unit')->where('id', $product['unit1'])->find();
            $sendNum = $num - bcdiv($firstNum, $product['unit1_num'], 0)*$product['unit2_num'];

            return $firstNum.$firstUnit['name'].$sendNum.$product['unit'];
        }

        if ($product['unit3'] == $unitInfo['id']){//如果顶级单位为默认单位的母单位
            $firstNum = bcdiv($num, $product['unit3_num'], 0) * $product['unit1_num'];
            $firstUnit = db('unit')->where('id', $product['unit1'])->find();

            $num = $num - bcdiv($firstNum, $product['unit1_num'], 0)*$product['unit3_num'];

            $secondNum = bcdiv($num, $product['unit3_num'], 0) * $product['unit2_num'];
            $secondUnit = db('unit')->where('id', $product['unit2'])->find();
            $num = $num - bcdiv($secondNum, $product['unit2_num'], 0)*$product['unit3_num'];

            return $firstNum.$firstUnit['name'].$secondNum.$secondUnit['name'].$num.$product['unit'];
        }

        return '包装设置的单位没有设置到默认单位！';
    }
}

