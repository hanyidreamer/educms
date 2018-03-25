<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/9
 * Time: 16:54
 */
namespace app\base\controller;

use think\Controller;
use think\Session;
use think\Cookie;

class CheckLogin extends Controller
{
    // 检查用户是否登录
    public function info()
    {
        $session_username=Session::get('username');
        $session_password=Session::get('password');
        $cookie_username=Cookie::get('username');
        $cookie_password=Cookie::get('password');
        if($session_username!=''and $session_password!='' and $session_username==$cookie_username and $session_password==$cookie_password){
            return true;
        }else{
            return false;
        }
    }
}