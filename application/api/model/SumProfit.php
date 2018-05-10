<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/22
 * Time: 11:31
 */

namespace app\api\model;

use think\Model;

class SumProfit extends Model
{
    public function index($a,$b)
    {
        // 累加 依次输出每次计算出来的值
            if($a==0){
                return $b[$a];
            }else{
                $c=$a-1;
                $sum=index($c,$b);
                return  $b[$a]+$sum;
            }
    }
}