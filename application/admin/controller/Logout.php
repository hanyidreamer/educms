<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 16:25
 */
namespace app\admin\controller;


class Logout extends AdminBase
{
    /**
     * 注销登录
     */
    public function index()
    {
        session('admin_username',null);
        session('admin_password',null);
        $this->success('正在退出', '/admin/login/index');
    }
}