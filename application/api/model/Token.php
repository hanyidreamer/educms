<?php
namespace app\api\model;


class Token
{
    // 加密算法 字母全部小写 不支持字母大写
    public function encrypt($time)
    {
        // 判断 $time 是否为空
        if($time==''){
            $out_info=array("code"=>"0","status"=>"token is null.");
            echo json_encode($out_info);
            exit;
        }
        $change=array(
            '0'=>'xtl',
            '1'=>'ypk',
            '2'=>'%wd',
            '3'=>'brn',
            '4'=>'m+s',
            '5'=>'af',
            '6'=>'zvc',
            '7'=>'geq',
            '8'=>'oih',
            '9'=>'u*j',
        );
        return strtr($time,$change);
    }

    //解密密算法 字母全部小写 不支持字母大写
    public function decrypt($time)
    {
        // 判断 $time 是否为空
        if($time==''){
            $out_info=array("code"=>"0","status"=>"token is null.");
            echo json_encode($out_info);
            exit;
        }
        $change=array(
            'xtl'=>'0',
            'ypk'=>'1',
            '%wd'=>'2',
            'brn'=>'3',
            'm+s'=>'4',
            'af'=>'5',
            'zvc'=>'6',
            'geq'=>'7',
            'oih'=>'8',
            'u*j'=>'9',
        );
        return strtr($time,$change);
    }


}
