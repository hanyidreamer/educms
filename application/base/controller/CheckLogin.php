<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/9
 * Time: 16:54
 */
namespace app\base\controller;

use think\Controller;

class CheckLogin extends Controller
{
    /**
     * 检查用户是否登录
     * @return bool
     */
    public function info()
    {
        $session_username = session('username');
        $session_password = session('password');
        $cookie_username = cookie('username');
        $cookie_password = cookie('password');
        if($session_username!=''and $session_password!='' and $session_username==$cookie_username and $session_password==$cookie_password){
            return true;
        }else{
            return false;
        }
    }
}