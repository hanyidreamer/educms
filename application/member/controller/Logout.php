<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/22
 * Time: 17:08
 */
namespace app\member\controller;

use think\Controller;
use think\Session;
use think\Cookie;

class Logout extends Controller
{
    public function index()
    {
        // 删除登录记录
        Session::delete('username');
        Session::delete('password');
        cookie::delete('password');
        cookie::delete('username');
        $this->success('正在退出', '/member/login/index');
    }
}