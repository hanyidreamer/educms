<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 16:25
 */
namespace app\admin\controller;

use think\Session;
use think\Cookie;
use app\base\controller\Base;

class Logout extends Base
{
    public function index()
    {
        // 注销登录
        Session::delete('username');
        Session::delete('password');
        cookie::delete('password');
        cookie::delete('username');
        $this->success('正在退出', '/admin/login/index');
    }
}