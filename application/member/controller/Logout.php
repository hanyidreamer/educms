<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/22
 * Time: 17:08
 */
namespace app\member\controller;

use think\Controller;
use think\facade\Session;
use think\facade\Cookie;

class Logout extends Controller
{
    /**
     * 退出登录
     */
    public function index()
    {
        // 删除登录记录
        Session::delete('username');
        Session::delete('password');
        Cookie::delete('password');
        Cookie::delete('username');
        $this->success('正在退出', '/member/login/index');
    }
}