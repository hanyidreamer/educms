<?php

namespace app\api\model;

use think\Model;

class Sign extends Model
{
    public function make_sign($str){
        $sign = md5($str);
        return $sign;
    }
}